<?php
session_start();
require __DIR__ . '/../templates/header.php';

$order_id = $_GET['id'] ?? '';
if (!$order_id) {
    header('Location: /');
    exit;
}

require __DIR__ . '/../core/Database.php';
$db = Database::getInstance();
$order = $db->fetch("SELECT * FROM xvilo_orders WHERE id = ? AND status = 'pending'", [$order_id]);
if (!$order) {
    echo '<section class="section" style="padding-top:120px;"><div class="container"><h2>Commande introuvable</h2><a href="/" class="btn btn-primary">Retour</a></div></section>';
    require __DIR__ . '/../templates/footer.php';
    exit;
}

$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
?>
<section class="section" style="padding-top:120px;">
  <div class="container" style="max-width:600px;">
    <h2 class="section-title">Paiement <span class="gradient-text"><?= htmlspecialchars($order['payment_method'] ?? '...') ?></span></h2>
    <p class="section-sub">Plan <strong><?= htmlspecialchars($order['plan_name']) ?></strong> — <strong><?= (int)$order['plan_price'] ?> DH</strong></p>

    <?php if ($success): ?>
      <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (empty($order['payment_method'])): ?>
      <!-- Step 1: Choose payment method -->
      <div class="payment-box">
        <h3>Choisis ta méthode de paiement</h3>
        <form action="/api/payment.php" method="POST">
          <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
          <div class="payment-options">
            <label class="payment-option">
              <input type="radio" name="method" value="inwi" required>
              <div class="payment-option-content">
                <strong>Inwi Money</strong>
                <span>Paiement via Inwi</span>
              </div>
            </label>
            <label class="payment-option">
              <input type="radio" name="method" value="orange" required>
              <div class="payment-option-content">
                <strong>Orange Money</strong>
                <span>Paiement via Orange</span>
              </div>
            </label>
          </div>
          <button type="submit" class="btn btn-primary btn-block btn-lg">Continuer</button>
        </form>
      </div>
    <?php elseif (empty($order['screenshot'])): ?>
      <!-- Step 2: Show payment code + upload screenshot -->
      <div class="payment-box">
        <h3>Code de paiement</h3>
        <div class="payment-code"><?= htmlspecialchars($order['payment_code']) ?></div>
        <p style="color:var(--text-muted);font-size:14px;margin-bottom:20px;">
          Envoie <strong><?= (int)$order['plan_price'] ?> DH</strong> via <?= $order['payment_method'] === 'inwi' ? 'Inwi Money' : 'Orange Money' ?> au code ci-dessus.<br>
          Prends une capture d'écran et uploads-la ci-dessous.
        </p>
        <form action="/api/upload.php" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
          <div class="form-group">
            <label>Capture d'écran du paiement</label>
            <input type="file" name="screenshot" accept="image/*" required>
          </div>
          <button type="submit" class="btn btn-primary btn-block btn-lg">Envoyer &amp; Done</button>
        </form>
      </div>
    <?php else: ?>
      <!-- Step 3: Done -->
      <div class="payment-box" style="text-align:center;">
        <div style="font-size:64px;margin-bottom:16px;">✅</div>
        <h3>Commande soumise !</h3>
        <p style="color:var(--text-muted);font-size:15px;">
          Merci ! Ton paiement est en cours de vérification.<br>
          Tu recevras ton hébergement sous <strong>1h à 4h</strong>.<br>
          Reste connecté sur WhatsApp/Discord.
        </p>
        <a href="/" class="btn btn-primary" style="margin-top:16px;">Retour à l'accueil</a>
      </div>
    <?php endif; ?>
  </div>
</section>

<style>
.payment-box { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 32px; margin-top:16px; }
.payment-box h3 { font-size:18px; margin-bottom:16px; }
.payment-options { display:flex; flex-direction:column; gap:12px; margin-bottom:24px; }
.payment-option { display:block; cursor:pointer; }
.payment-option input { display:none; }
.payment-option-content { display:flex; flex-direction:column; gap:4px; padding:16px; border:2px solid var(--border); border-radius:12px; transition:all 0.2s; }
.payment-option input:checked + .payment-option-content { border-color:var(--primary); background:rgba(99,102,241,0.08); }
.payment-option-content strong { font-size:15px; }
.payment-option-content span { font-size:13px; color:var(--text-muted); }
.payment-code { font-size:28px; font-weight:800; text-align:center; padding:20px; background:var(--bg); border-radius:12px; margin-bottom:16px; letter-spacing:4px; border:1px dashed var(--primary); color:var(--primary); }
.alert { padding:14px 20px; border-radius:8px; margin-bottom:16px; font-size:14px; font-weight:500; }
.alert-success { background:rgba(34,197,94,0.15); border:1px solid rgba(34,197,94,0.3); color:#22c55e; }
.alert-error { background:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.3); color:#ef4444; }
</style>

<?php require __DIR__ . '/../templates/footer.php'; ?>
