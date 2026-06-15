<?php
session_start();

$config = require __DIR__ . '/../config/app.php';

if (empty($_SESSION['admin_logged_in'])) {
    $code = $_POST['code'] ?? '';
    if ($code !== $config['admin_code']) {
        header('Location: /?error=Code PIN incorrect');
        exit;
    }
    $_SESSION['admin_logged_in'] = true;
}

require __DIR__ . '/../core/Database.php';
$db = Database::getInstance();

$action = $_GET['action'] ?? '';

if ($action === 'approve' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $db->update('xvilo_orders', ['status' => 'approved'], 'id = :id', ['id' => $id]);
    header('Location: /admin/orders.php?msg=Commande approuvée');
    exit;
}

if ($action === 'reject' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $note = $_GET['note'] ?? '';
    $db->update('xvilo_orders', ['status' => 'rejected', 'admin_note' => $note], 'id = :id', ['id' => $id]);
    header('Location: /admin/orders.php?msg=Commande refusée');
    exit;
}

// stats
$pendingCount = $db->fetch("SELECT COUNT(*) as c FROM xvilo_orders WHERE status = 'pending'")['c'];
$approvedCount = $db->fetch("SELECT COUNT(*) as c FROM xvilo_orders WHERE status = 'approved'")['c'];
$totalRevenue = $db->fetch("SELECT COALESCE(SUM(plan_price),0) as total FROM xvilo_orders WHERE status = 'approved'")['total'];
$orders = $db->fetchAll("SELECT * FROM xvilo_orders ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Xvilo Admin — Panel</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    :root { --bg:#0a0a0f; --bg-card:#12121a; --text:#e8e8f0; --text-muted:#8888a0; --primary:#6366f1; --border:#1e1e30; --success:#22c55e; --danger:#ef4444; }
    body { font-family:'Inter',sans-serif; background:var(--bg); color:var(--text); padding:24px; }
    .container { max-width:1200px; margin:0 auto; }
    .header { display:flex; justify-content:space-between; align-items:center; margin-bottom:32px; }
    .header h1 { font-size:24px; }
    .header h1 span { background:linear-gradient(135deg,#6366f1,#a855f7); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
    .stats { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:32px; }
    .stat-card { background:var(--bg-card); border:1px solid var(--border); border-radius:12px; padding:20px; }
    .stat-card .num { font-size:32px; font-weight:700; }
    .stat-card .label { font-size:13px; color:var(--text-muted); margin-top:4px; }
    .msg { background:rgba(34,197,94,0.15); border:1px solid rgba(34,197,94,0.3); color:#22c55e; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:14px; }
    table { width:100%; border-collapse:collapse; background:var(--bg-card); border:1px solid var(--border); border-radius:12px; overflow:hidden; }
    th, td { padding:14px 16px; text-align:left; font-size:13px; border-bottom:1px solid var(--border); }
    th { background:rgba(99,102,241,0.1); font-weight:600; color:var(--text-muted); text-transform:uppercase; font-size:11px; letter-spacing:0.5px; }
    td { color:var(--text); }
    .badge { display:inline-block; padding:4px 10px; border-radius:6px; font-size:11px; font-weight:600; }
    .badge-pending { background:rgba(234,179,8,0.15); color:#eab308; }
    .badge-approved { background:rgba(34,197,94,0.15); color:#22c55e; }
    .badge-rejected { background:rgba(239,68,68,0.15); color:#ef4444; }
    .btn { display:inline-flex; align-items:center; padding:6px 14px; border-radius:6px; font-size:12px; font-weight:600; text-decoration:none; border:none; cursor:pointer; }
    .btn-approve { background:#22c55e; color:#fff; }
    .btn-reject { background:#ef4444; color:#fff; }
    .btn-small { padding:4px 10px; font-size:11px; }
    .screenshot-link { color:var(--primary); text-decoration:underline; font-size:12px; }
    .logout { background:var(--bg-card); border:1px solid var(--border); padding:8px 16px; border-radius:8px; color:var(--text); text-decoration:none; font-size:13px; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>Xvilo <span>Admin</span></h1>
      <a href="/admin/logout.php" class="logout">Déconnexion</a>
    </div>

    <?php if (isset($_GET['msg'])): ?>
      <div class="msg"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>

    <div class="stats">
      <div class="stat-card"><div class="num"><?= $pendingCount ?></div><div class="label">En attente</div></div>
      <div class="stat-card"><div class="num"><?= $approvedCount ?></div><div class="label">Approuvées</div></div>
      <div class="stat-card"><div class="num"><?= $totalRevenue ?> DH</div><div class="label">Revenu total</div></div>
    </div>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Client</th>
          <th>Contact</th>
          <th>Plan</th>
          <th>Prix</th>
          <th>Méthode</th>
          <th>Code</th>
          <th>Preuve</th>
          <th>Statut</th>
          <th>Date</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($orders)): ?>
          <tr><td colspan="11" style="text-align:center;color:var(--text-muted);padding:40px;">Aucune commande pour le moment.</td></tr>
        <?php endif; ?>
        <?php foreach ($orders as $o): ?>
          <tr>
            <td>#<?= $o['id'] ?></td>
            <td><?= htmlspecialchars($o['customer_name']) ?></td>
            <td><?= htmlspecialchars($o['customer_contact']) ?></td>
            <td><?= htmlspecialchars($o['plan_name']) ?></td>
            <td><?= (int)$o['plan_price'] ?> DH</td>
            <td><?= $o['payment_method'] ? strtoupper($o['payment_method']) : '-' ?></td>
            <td style="font-family:monospace;font-size:12px;"><?= htmlspecialchars($o['payment_code'] ?? '-') ?></td>
            <td>
              <?php if ($o['screenshot']): ?>
                <a href="<?= htmlspecialchars($o['screenshot']) ?>" target="_blank" class="screenshot-link">Voir</a>
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
                <a href="?action=approve&id=<?= $o['id'] ?>" class="btn btn-approve btn-small" onclick="return confirm('Approuver cette commande ?')">✓ Approuver</a>
                <a href="?action=reject&id=<?= $o['id'] ?>" class="btn btn-reject btn-small" onclick="return confirm('Refuser cette commande ?')">✕ Refuser</a>
              <?php else: ?>
                <span style="color:var(--text-muted);font-size:11px;"><?= $o['status'] === 'approved' ? '✓' : '✕' ?></span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
