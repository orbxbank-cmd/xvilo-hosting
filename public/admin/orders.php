<?php
session_start();

$config = require __DIR__ . '/../../config/app.php';

if (empty($_SESSION['admin_logged_in'])) {
    $code = $_POST['code'] ?? '';
    if ($code !== $config['admin_code']) {
        header('Location: /?error=Code PIN incorrect');
        exit;
    }
    $_SESSION['admin_logged_in'] = true;
}

require __DIR__ . '/../../core/Database.php';
require __DIR__ . '/../../core/PterodactylAPI.php';
$db = Database::getInstance();

$action = $_GET['action'] ?? '';

if ($action === 'approve' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $order = $db->fetch("SELECT * FROM xvilo_orders WHERE id = ? AND status = 'pending'", [$id]);

    if ($order) {
        $ptero = new PterodactylAPI();
        $pteroConfig = $config['pterodactyl'];

        $userId = $ptero->findUserByUsername('client1') ?: 1;
        $allocationId = $ptero->getNextAllocation($pteroConfig['node_id']);

        if ($allocationId) {
            $serverParams = $ptero->buildServerPayload($order, $allocationId, $userId);
            $result = $ptero->createServer($serverParams);

            if ($result && isset($result['attributes'])) {
                $serverId = $result['attributes']['id'];
                $uuid = $result['attributes']['uuid'];

                $username = 'u' . $serverId;
                $password = bin2hex(random_bytes(6));

                $updateData = [
                    'status' => 'approved',
                    'server_id' => $serverId,
                    'server_port' => 0,
                    'server_username' => $username,
                    'server_password' => $password,
                ];

                $dbResult = $ptero->createDatabase($serverId, $pteroConfig['dbhost_id']);
                if ($dbResult && isset($dbResult['attributes'])) {
                    $updateData['server_db_name'] = $dbResult['attributes']['database'];
                    $updateData['server_db_user'] = $dbResult['attributes']['username'];
                    $updateData['server_db_pass'] = $dbResult['_plain_password'] ?? '';
                }

                $db->update('xvilo_orders', $updateData, 'id = :id', ['id' => $id]);

                $msg = 'Serveur créé #' . $serverId . ' (' . substr($uuid, 0, 8) . '...)';
            } else {
                $db->update('xvilo_orders', [
                    'status' => 'approved',
                ], 'id = :id', ['id' => $id]);
                $msg = 'Commande approuvée (API a échoué)';
            }
        } else {
            $db->update('xvilo_orders', [
                'status' => 'approved',
            ], 'id = :id', ['id' => $id]);
            $msg = 'Commande approuvée (aucune allocation libre)';
        }
    } else {
        $msg = 'Commande introuvable';
    }

    header('Location: /admin/orders.php?msg=' . urlencode($msg));
    exit;
}

if ($action === 'reject' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $note = $_GET['note'] ?? '';
    $db->update('xvilo_orders', ['status' => 'rejected', 'admin_note' => $note], 'id = :id', ['id' => $id]);
    header('Location: /admin/orders.php?msg=Commande refusée');
    exit;
}

$pendingCount = $db->fetch("SELECT COUNT(*) as c FROM xvilo_orders WHERE status = 'pending'")['c'];
$approvedCount = $db->fetch("SELECT COUNT(*) as c FROM xvilo_orders WHERE status = 'approved'")['c'];
$totalRevenue = $db->fetch("SELECT COALESCE(SUM(plan_price),0) as total FROM xvilo_orders WHERE status = 'approved'")['total'];
$orders = $db->fetchAll("SELECT o.*, u.email AS user_email FROM xvilo_orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Xvilo Admin — Panel</title>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=Russo+One&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/style.css" />
</head>
<body>
  <div class="admin-page">
    <div class="container">
      <div class="admin-header">
        <h1>Xvilo <span>Admin</span></h1>
        <a href="/admin/logout.php" class="logout-btn">Déconnexion</a>
      </div>

      <?php if (isset($_GET['msg'])): ?>
        <div class="admin-msg"><?= htmlspecialchars($_GET['msg']) ?></div>
      <?php endif; ?>

      <div class="admin-stats">
        <div class="admin-stat-card"><div class="num"><?= $pendingCount ?></div><div class="label">En attente</div></div>
        <div class="admin-stat-card"><div class="num"><?= $approvedCount ?></div><div class="label">Approuvées</div></div>
        <div class="admin-stat-card"><div class="num"><?= $totalRevenue ?> DH</div><div class="label">Revenu total</div></div>
      </div>

      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Client</th>
            <th>Compte</th>
            <th>Contact</th>
            <th>Plan</th>
            <th>Prix</th>
            <th>Méthode</th>
            <th>Serveur</th>
            <th>Accès</th>
            <th>Statut</th>
            <th>Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($orders)): ?>
            <tr><td colspan="12" style="text-align:center;color:var(--text-muted);padding:40px;">Aucune commande pour le moment.</td></tr>
          <?php endif; ?>
          <?php foreach ($orders as $o): ?>
            <tr>
              <td>#<?= $o['id'] ?></td>
              <td><?= htmlspecialchars($o['customer_name']) ?></td>
              <td style="font-size:11px;"><?= htmlspecialchars($o['user_email'] ?? '') ?: '-' ?></td>
              <td><?= htmlspecialchars($o['customer_contact']) ?></td>
              <td><?= htmlspecialchars($o['plan_name']) ?></td>
              <td><?= (int)$o['plan_price'] ?> DH</td>
              <td><?php
                $ml = $o['payment_method'];
                if ($ml === 'inwi') echo 'Inwi Carta';
                elseif ($ml === 'orange') echo 'Orange Carta';
                else echo '-';
              ?></td>
              <td>
                <?php if ($o['server_id']): ?>
                  #<?= $o['server_id'] ?><br>
                  <small style="font-size:11px;color:var(--text-muted);">Port <?= $o['server_port'] ?></small>
                <?php else: ?>
                  -
                <?php endif; ?>
              </td>
              <td style="font-size:11px;">
                <?php if ($o['server_username']): ?>
                  <strong>User:</strong> <?= htmlspecialchars($o['server_username']) ?><br>
                  <strong>Pass:</strong> <?= htmlspecialchars($o['server_password']) ?>
                <?php else: ?>
                  -
                <?php endif; ?>
              </td>
              <td>
                <span class="badge badge-<?= $o['status'] === 'pending' ? 'pending' : ($o['status'] === 'approved' ? 'approved' : 'rejected') ?>">
                  <?= $o['status'] ?>
                </span>
              </td>
              <td style="font-size:11px;color:var(--text-muted);"><?= date('d/m H:i', strtotime($o['created_at'])) ?></td>
              <td>
                <?php if ($o['status'] === 'pending'): ?>
                  <a href="/admin/orders.php?action=approve&id=<?= $o['id'] ?>" class="btn btn-approve btn-small" onclick="return confirm('Approuver ? Le serveur sera créé automatiquement.')">✓ Approuver</a>
                  <a href="/admin/orders.php?action=reject&id=<?= $o['id'] ?>" class="btn btn-reject btn-small" onclick="return confirm('Refuser ?')">✕ Refuser</a>
                <?php elseif ($o['status'] === 'approved' && $o['server_id']): ?>
                  <span style="color:var(--success);font-size:11px;">✓ Serveur #<?= $o['server_id'] ?></span>
                <?php else: ?>
                  <span style="color:var(--text-muted);font-size:11px;"><?= $o['status'] === 'approved' ? '✓' : '✕' ?></span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
