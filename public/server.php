<?php
require __DIR__ . '/../core/Database.php';
require __DIR__ . '/../core/PterodactylAPI.php';
require __DIR__ . '/../core/Auth.php';
Auth::require();
$user = Auth::user();
$config = require __DIR__ . '/../config/app.php';

$id = (int)($_GET['id'] ?? 0);
$db = Database::getInstance();
$order = $db->fetch("SELECT * FROM xvilo_orders WHERE id = ? AND user_id = ?", [$id, $user['id']]);
if (!$order || !$order['server_id']) {
    header('Location: /dashboard.php');
    exit;
}

$ptero = new PterodactylAPI();
$srv = $ptero->getServer((int)$order['server_id']);
$srvAttr = $srv['attributes'] ?? null;
$allocId = $srvAttr['allocation'] ?? 0;
$limits = $srvAttr['limits'] ?? [];
$container = $srvAttr['container'] ?? [];
$env = $container['environment'] ?? [];
$sampVer = $env['SAMP_VERSION'] ?? '0.3.7';

require __DIR__ . '/../templates/header.php';
?>
<style>
.panel-wrap { max-width:600px; margin:120px auto 60px; padding:0 20px; }
.panel-box { background:#111; border:1px solid #222; border-radius:12px; padding:32px; text-align:center; }
.panel-box h2 { font-size:20px; color:#fff; margin-bottom:8px; }
.panel-box p { color:var(--text-muted); font-size:14px; margin-bottom:24px; }
.cred-box { background:#0a0a0a; border:1px solid #333; border-radius:8px; padding:20px; margin-bottom:20px; }
.cred-box .row { display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid #222; }
.cred-box .row:last-child { border-bottom:none; }
.cred-box .label { color:var(--text-muted); font-size:13px; }
.cred-box .value { color:#fff; font-family:monospace; font-size:14px; }
.panel-link { display:inline-block; background:var(--accent); color:#fff; padding:14px 32px; border-radius:8px; text-decoration:none; font-weight:600; font-size:15px; transition:.2s; }
.panel-link:hover { opacity:.8; }
.expire-note { margin-top:20px; font-size:13px; color:var(--text-muted); }
.expire-note .countdown { font-family:monospace; font-size:18px; color:var(--accent); font-weight:700; }
</style>

<div class="panel-wrap">
  <div class="panel-box">
    <h2><?= htmlspecialchars($order['server_name']) ?></h2>
    <p>Votre hébergement SA-MP est actif. Utilisez le panneau ci-dessous pour gérer votre serveur.</p>

    <div class="cred-box">
      <?php if ($order['server_username'] && $order['server_password']): ?>
      <div class="row">
        <span class="label">Lien</span>
        <a href="http://62.84.180.151/" target="_blank" style="color:#3b82f6;font-family:monospace;font-size:14px;">http://62.84.180.151/</a>
      </div>
      <div class="row">
        <span class="label">Email</span>
        <span class="value"><?= htmlspecialchars($order['server_username']) ?></span>
      </div>
      <div class="row">
        <span class="label">Mot de passe</span>
        <span class="value"><?= htmlspecialchars($order['server_password']) ?></span>
      </div>
      <?php else: ?>
      <div class="row">
        <span class="label">Lien</span>
        <a href="http://62.84.180.151/" target="_blank" style="color:#3b82f6;font-family:monospace;font-size:14px;">http://62.84.180.151/</a>
      </div>
      <div class="row">
        <span class="label">Adresse</span>
        <span class="value">62.84.180.151:<?= (int)$order['server_port'] ?></span>
      </div>
      <?php endif; ?>
    </div>

    <?php if ($order['server_username'] && $order['server_password']): ?>
    <a href="http://62.84.180.151/" target="_blank" class="panel-link">Ouvrir le panneau Pterodactyl</a>
    <?php endif; ?>

    <?php if ($order['expires_at']): ?>
    <div class="expire-note">
      Expire dans <span class="countdown" id="countdownTimer" data-expires="<?= strtotime($order['expires_at']) ?>"></span>
    </div>
    <?php endif; ?>
  </div>
</div>

<script>
function updateCountdown() {
  const el = document.getElementById('countdownTimer');
  if (!el) return;
  const expires = parseInt(el.dataset.expires) * 1000;
  const diff = expires - Date.now();
  if (diff <= 0) {
    el.textContent = 'Expiré';
    el.style.color = '#ef4444';
    return;
  }
  const d = Math.floor(diff / 86400000);
  const h = Math.floor((diff % 86400000) / 3600000);
  const m = Math.floor((diff % 3600000) / 60000);
  const s = Math.floor((diff % 60000) / 1000);
  el.textContent = d + 'd ' + h + 'h ' + m + 'm ' + s + 's';
}
updateCountdown();
setInterval(updateCountdown, 1000);
</script>

<?php require __DIR__ . '/../templates/footer.php'; ?>
