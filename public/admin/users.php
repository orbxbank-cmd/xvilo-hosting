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
$db = Database::getInstance();

$detailId = (int)($_GET['user_id'] ?? 0);

$users = $db->fetchAll("
    SELECT u.*,
        (SELECT COUNT(*) FROM xvilo_orders WHERE user_id = u.id) AS total_orders,
        (SELECT COUNT(*) FROM xvilo_orders WHERE user_id = u.id AND status = 'approved') AS active_hosts
    FROM users u
    ORDER BY u.created_at DESC
");

$totalUsers = count($users);
$usersWithHosts = count(array_filter($users, fn($u) => $u['active_hosts'] > 0));

$detailUser = null;
$detailOrders = [];
if ($detailId) {
    $detailUser = $db->fetch("SELECT * FROM users WHERE id = ?", [$detailId]);
    if ($detailUser) {
        $detailOrders = $db->fetchAll("SELECT * FROM xvilo_orders WHERE user_id = ? ORDER BY created_at DESC", [$detailId]);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Xvilo Admin — Utilisateurs</title>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=Russo+One&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/style.css" />
  <style>
    .admin-nav { display:flex; gap:8px; margin-bottom:20px; }
    .admin-nav a { padding:8px 18px; border-radius:6px; font-size:13px; font-weight:600; text-decoration:none; transition:.2s; }
    .admin-nav .active { background:var(--accent); color:#fff; }
    .admin-nav .inactive { background:var(--bg-alt); color:var(--text-muted); border:1px solid var(--border); }
    .admin-nav .inactive:hover { border-color:var(--accent); color:#fff; }
    .user-card { display:flex; align-items:center; gap:16px; background:#111; border:1px solid #222; border-radius:10px; padding:16px 20px; cursor:pointer; transition:.2s; text-decoration:none; color:inherit; }
    .user-card:hover { border-color:var(--accent); background:#151515; }
    .user-avatar { width:40px; height:40px; border-radius:50%; background:var(--accent); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:16px; color:#fff; flex-shrink:0; }
    .user-info { flex:1; min-width:0; }
    .user-info .name { font-weight:600; font-size:15px; }
    .user-info .email { font-size:12px; color:var(--text-muted); }
    .user-meta { text-align:right; font-size:12px; color:var(--text-muted); flex-shrink:0; }
    .user-meta .hosts { color:var(--success); font-weight:600; }
    .detail-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
    .detail-header h2 { font-size:22px; }
    .detail-header h2 small { font-size:14px; color:var(--text-muted); font-weight:400; }
    .detail-stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:12px; margin-bottom:24px; }
    .detail-stat { background:#111; border:1px solid #222; border-radius:8px; padding:14px 18px; }
    .detail-stat .label { font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px; }
    .detail-stat .value { font-size:18px; font-weight:700; margin-top:4px; }
    .badge { display:inline-block; padding:3px 10px; border-radius:100px; font-size:11px; font-weight:600; }
    .badge-pending { background:#f59e0b22; color:#f59e0b; border:1px solid #f59e0b44; }
    .badge-approved { background:#22c55e22; color:#22c55e; border:1px solid #22c55e44; }
    .badge-rejected { background:#ef444422; color:#ef4444; border:1px solid #ef444444; }
    .badge-expired { background:#6b728022; color:#6b7280; border:1px solid #6b728044; }
    .back-link { display:inline-flex; align-items:center; gap:6px; color:var(--text-muted); font-size:13px; text-decoration:none; margin-bottom:16px; }
    .back-link:hover { color:var(--accent); }
    .exp-countdown { font-family:monospace; font-size:12px; }
  </style>
</head>
<body>
  <div class="admin-page">
    <div class="container">
      <div class="admin-header">
        <h1>Xvilo <span>Admin</span></h1>
        <a href="/admin/logout.php" class="logout-btn">Déconnexion</a>
      </div>

      <div class="admin-nav">
        <a href="/admin/orders.php" class="inactive">Commandes</a>
        <a href="/admin/users.php" class="active">Utilisateurs</a>
      </div>

      <?php if (isset($_GET['msg'])): ?>
        <div class="admin-msg"><?= htmlspecialchars($_GET['msg']) ?></div>
      <?php endif; ?>

      <?php if ($detailUser): ?>
        <!-- User Detail View -->
        <a href="/admin/users.php" class="back-link">← Retour à la liste</a>

        <div class="detail-header">
          <div style="display:flex;align-items:center;gap:16px;">
            <div class="user-avatar" style="width:48px;height:48px;font-size:20px;"><?= strtoupper(substr($detailUser['name'], 0, 1)) ?></div>
            <div>
              <h2><?= htmlspecialchars($detailUser['name']) ?> <small>(#<?= $detailUser['id'] ?>)</small></h2>
              <span style="color:var(--text-muted);font-size:13px;"><?= htmlspecialchars($detailUser['email']) ?></span>
              <?php if ($detailUser['google_id']): ?>
                <span style="color:#888;font-size:11px;margin-left:8px;">🔗 Google</span>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <div class="detail-stats">
          <div class="detail-stat">
            <div class="label">Inscription</div>
            <div class="value" style="font-size:14px;"><?= date('d/m/Y', strtotime($detailUser['created_at'])) ?></div>
          </div>
          <div class="detail-stat">
            <div class="label">Dernière activité</div>
            <div class="value" style="font-size:14px;"><?= $detailUser['updated_at'] ? date('d/m/Y H:i', strtotime($detailUser['updated_at'])) : '-' ?></div>
          </div>
          <div class="detail-stat">
            <div class="label">Total commandes</div>
            <div class="value"><?= count($detailOrders) ?></div>
          </div>
          <div class="detail-stat">
            <div class="label">Serveurs actifs</div>
            <div class="value" style="color:var(--success);"><?= count(array_filter($detailOrders, fn($o) => $o['status'] === 'approved' && $o['server_id'])) ?></div>
          </div>
        </div>

        <?php if (empty($detailOrders)): ?>
          <div style="text-align:center;padding:40px;color:var(--text-muted);">Aucune commande pour cet utilisateur.</div>
        <?php else: ?>
          <table class="admin-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Plan</th>
                <th>Serveur</th>
                <th>Statut</th>
                <th>Prix</th>
                <th>Serveur ID</th>
                <th>Expire</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($detailOrders as $o): ?>
                <tr>
                  <td>#<?= $o['id'] ?></td>
                  <td><?= htmlspecialchars($o['plan_name']) ?></td>
                  <td><?= htmlspecialchars($o['server_name'] ?: '-') ?></td>
                  <td><span class="badge badge-<?= $o['status'] ?>"><?= $o['status'] ?></span></td>
                  <td><?= (int)$o['plan_price'] ?> DH</td>
                  <td><?= $o['server_id'] ? '#'.$o['server_id'] : '-' ?></td>
                  <td style="font-size:12px;color:var(--text-muted);">
                    <?php if ($o['expires_at']): ?>
                      <span class="exp-countdown" data-expires="<?= strtotime($o['expires_at']) ?>">
                        <?= date('d/m/Y', strtotime($o['expires_at'])) ?>
                      </span>
                    <?php else: ?>
                      -
                    <?php endif; ?>
                  </td>
                  <td style="font-size:11px;color:var(--text-muted);"><?= date('d/m H:i', strtotime($o['created_at'])) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>

      <?php else: ?>
        <!-- Users List -->
        <div class="admin-stats">
          <div class="admin-stat-card"><div class="num"><?= $totalUsers ?></div><div class="label">Utilisateurs</div></div>
          <div class="admin-stat-card"><div class="num"><?= $usersWithHosts ?></div><div class="label">Avec serveur</div></div>
          <div class="admin-stat-card"><div class="num"><?= $totalUsers - $usersWithHosts ?></div><div class="label">Sans serveur</div></div>
        </div>

        <div style="display:flex;flex-direction:column;gap:8px;">
          <?php if (empty($users)): ?>
            <div style="text-align:center;padding:40px;color:var(--text-muted);">Aucun utilisateur pour le moment.</div>
          <?php endif; ?>
          <?php foreach ($users as $u): ?>
            <a href="/admin/users.php?user_id=<?= $u['id'] ?>" class="user-card">
              <div class="user-avatar"><?= strtoupper(substr($u['name'], 0, 1)) ?></div>
              <div class="user-info">
                <div class="name"><?= htmlspecialchars($u['name']) ?></div>
                <div class="email"><?= htmlspecialchars($u['email']) ?></div>
              </div>
              <div class="user-meta">
                <div>Inscrit le <?= date('d/m/Y', strtotime($u['created_at'])) ?></div>
                <div class="hosts"><?= (int)$u['active_hosts'] ?> serveur<?= $u['active_hosts'] > 1 ? 's' : '' ?></div>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
  <script>
    (function() {
      document.querySelectorAll('.exp-countdown').forEach(function(el) {
        var expires = parseInt(el.dataset.expires) * 1000;
        if (!expires) return;
        function tick() {
          var diff = expires - Date.now();
          if (diff <= 0) { el.textContent = 'Expiré'; el.style.color = '#ef4444'; return; }
          var d = Math.floor(diff / 86400000);
          var h = Math.floor((diff % 86400000) / 3600000);
          var m = Math.floor((diff % 3600000) / 60000);
          var s = Math.floor((diff % 60000) / 1000);
          el.textContent = d + 'j ' + h + 'h ' + m + 'm ' + s + 's';
        }
        tick();
        setInterval(tick, 1000);
      });
    })();
  </script>
</body>
</html>
