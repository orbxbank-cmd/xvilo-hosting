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
    echo '<section class="section" style="padding-top:120px;"><div class="container"><h2 class="section-title">Commande introuvable</h2><a href="/" class="btn btn-primary">Retour</a></div></section>';
    require __DIR__ . '/../templates/footer.php';
    exit;
}

$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
$methodLabel = $order['payment_method'] === 'inwi' ? 'Inwi Carta' : ($order['payment_method'] === 'orange' ? 'Orange Carta' : '...');
?>
<section class="section" style="padding-top:120px;">
  <div class="container" style="max-width:600px;">
    <h2 class="section-title">Paiement <span class="gradient-text"><?= $methodLabel ?></span></h2>
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
              <div class="payment-option-content" style="flex-direction:row;align-items:center;gap:12px;">
                <img src="https://i.postimg.cc/RCpT0FZX/images-(4).jpg" alt="Inwi Carta" style="height:32px;width:auto;border-radius:4px;">
                <div>
                  <strong>Inwi Carta</strong>
                  <span>Paiement via Inwi Carta</span>
                </div>
              </div>
            </label>
            <label class="payment-option">
              <input type="radio" name="method" value="orange" required>
              <div class="payment-option-content" style="flex-direction:row;align-items:center;gap:12px;">
                <img src="https://i.postimg.cc/DfpczZwx/images-(3).jpg" alt="Orange Carta" style="height:32px;width:auto;border-radius:4px;">
                <div>
                  <strong>Orange Carta</strong>
                  <span>Paiement via Orange Carta</span>
                </div>
              </div>
            </label>
          </div>
          <button type="submit" class="btn btn-primary btn-block btn-lg">Continuer</button>
        </form>
      </div>
    <?php elseif (empty($order['screenshot'])): ?>
      <!-- Step 2: Show payment code + Arabic guide + upload -->
      <div class="payment-box">
        <h3>Code de paiement</h3>
        <div class="payment-code"><?= htmlspecialchars($order['payment_code']) ?></div>
        <p style="color:var(--text-muted);font-size:14px;margin-bottom:20px;">
          Envoie <strong><?= (int)$order['plan_price'] ?> DH</strong> via <?= $order['payment_method'] === 'inwi' ? 'Inwi Carta' : 'Orange Carta' ?> au code ci-dessus.
        </p>

        <!-- Arabic Guide -->
        <div class="guide-arabic">
          <h4>📸 إرشادات رفع إثبات الدفع</h4>
          <p>
            من فضلك، بعد إجراء الدفع، ارفع صورة واضحة وكبيرة لعملية الدفع<br>
            حتى نتمكن من التحقق منها بسرعة وتفعيل استضافتك.
          </p>
          <div class="guide-tips">
            <div class="guide-tip"><span>📱</span> صورة التطبيق كاملة مع الرمز</div>
            <div class="guide-tip"><span>🔍</span> تأكد من وضوح المبلغ والتاريخ</div>
            <div class="guide-tip"><span>📷</span> استخدم تصوير الشاشة (Screenshot)</div>
          </div>
        </div>

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

<?php require __DIR__ . '/../templates/footer.php'; ?>
