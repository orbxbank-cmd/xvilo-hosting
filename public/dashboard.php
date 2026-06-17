<?php
require __DIR__ . '/../core/Database.php';
require __DIR__ . '/../core/Auth.php';
Auth::require();

$user = Auth::user();
$db = Database::getInstance();
$orders = $db->fetchAll(
    "SELECT * FROM xvilo_orders WHERE user_id = ? ORDER BY created_at DESC",
    [$user['id']]
);

require __DIR__ . '/../templates/header.php';
?>
<section class="section" style="padding-top:120px;">
  <div class="container" style="max-width:1100px;">
    <h2 class="section-title">Tableau de <span class="gradient-text">bord</span></h2>
    <p class="section-sub">Bienvenue, <strong><?= htmlspecialchars($user['name']) ?></strong> !</p>

    <?php if (empty($orders)): ?>
      <div style="text-align:center;padding:60px 20px;color:var(--text-muted);">
        <p style="font-size:18px;margin-bottom:12px;">Tu n'as pas encore de serveur.</p>
        <a href="/#pricing" class="btn btn-primary btn-lg">Commander un serveur</a>
      </div>
    <?php else: ?>
      <div style="overflow-x:auto;">
        <table class="admin-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Plan</th>
              <th>Serveur</th>
              <th>Statut</th>
              <th>Connexion</th>
              <th>Base de données</th>
              <th>Expire</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $o): ?>
              <tr>
                <td>#<?= $o['id'] ?></td>
                <td><?= htmlspecialchars($o['plan_name']) ?><br><small style="color:var(--text-muted);"><?= $o['plan_price'] ?> DH</small></td>
                <td><?= htmlspecialchars($o['server_name'] ?: '-') ?></td>
                <td>
                  <span class="badge badge-<?= $o['status'] === 'pending' ? 'pending' : ($o['status'] === 'approved' ? 'approved' : 'rejected') ?>">
                    <?= $o['status'] ?>
                  </span>
                </td>
                <td style="font-size:12px;">
                  <?php if ($o['server_id']): ?>
                    <strong>Host:</strong> 62.84.180.151:<?= $o['server_port'] ?: '?' ?><br>
                    <strong>User:</strong> <?= htmlspecialchars($o['server_username'] ?: '-') ?><br>
                    <strong>Pass:</strong> <?= htmlspecialchars($o['server_password'] ?: '-') ?>
                  <?php else: ?>
                    <span style="color:var(--text-muted);">-</span>
                  <?php endif; ?>
                </td>
                <td style="font-size:12px;">
                  <?php if ($o['server_db_name']): ?>
                    <strong>DB:</strong> <?= htmlspecialchars($o['server_db_name']) ?><br>
                    <strong>User:</strong> <?= htmlspecialchars($o['server_db_user'] ?: '-') ?><br>
                    <strong>Pass:</strong> <?= htmlspecialchars($o['server_db_pass'] ?: '-') ?>
                  <?php elseif ($o['status'] === 'approved'): ?>
                    <span style="color:var(--text-muted);">En cours...</span>
                  <?php else: ?>
                    <span style="color:var(--text-muted);">-</span>
                  <?php endif; ?>
                </td>
                <td style="font-size:12px;color:var(--text-muted);">
                  <?php if ($o['expires_at']): ?>
                    <span class="exp-countdown" data-expires="<?= strtotime($o['expires_at']) ?>"></span>
                  <?php else: ?>
                    -
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($o['server_id'] && $o['status'] === 'approved'): ?>
                    <a href="/server.php?id=<?= $o['id'] ?>" class="btn btn-primary btn-small">Panel</a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</section>
<script>
document.querySelectorAll('.exp-countdown').forEach(function(el) {
  function tick() {
    var expires = parseInt(el.dataset.expires) * 1000;
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
</script>
<?php require __DIR__ . '/../templates/footer.php'; ?>
