<?php
require __DIR__ . '/../templates/header.php';

$plan = $_GET['plan'] ?? '';
$price = $_GET['price'] ?? '';
$plans = ['SAMP I' => 15, 'SAMP II' => 20, 'SAMP III' => 30, 'SAMP IV MAX' => 45];

if (!isset($plans[$plan])) {
    header('Location: /');
    exit;
}
?>
<section class="section" style="padding-top:120px;">
  <div class="container" style="max-width:600px;">
    <h2 class="section-title">Commander <span class="gradient-text"><?= htmlspecialchars($plan) ?></span></h2>
    <p class="section-sub">Plan à <strong><?= $price ?> DH/mois</strong> — Remplis le formulaire ci-dessous.</p>

    <form class="order-form" action="/api/order.php" method="POST">
      <input type="hidden" name="plan" value="<?= htmlspecialchars($plan) ?>">
      <input type="hidden" name="price" value="<?= (int)$price ?>">

      <div class="form-group">
        <label>Ton nom / Pseudo</label>
        <input type="text" name="name" required placeholder="Ex: Zagtos">
      </div>
      <div class="form-group">
        <label>Contact (WhatsApp ou Discord)</label>
        <input type="text" name="contact" required placeholder="Ex: zagtos#1234 ou +2126XXXXXXXX">
      </div>
      <div class="form-group">
        <label>Nom du serveur</label>
        <input type="text" name="server_name" required placeholder="Ex: Lost Roleplay S03">
      </div>
      <div class="form-group">
        <label>Gamemode (optionnel)</label>
        <input type="text" name="gamemode" placeholder="Ex: roleplay, deathmatch, drift...">
      </div>

      <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:12px;">Continue Order</button>
    </form>
  </div>
</section>

<style>
.order-form { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 32px; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; font-size: 14px; font-weight: 600; margin-bottom: 6px; color: var(--text); }
.form-group input, .form-group select { width: 100%; padding: 12px 16px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg); color: var(--text); font-size: 14px; outline: none; transition: border 0.2s; }
.form-group input:focus { border-color: var(--primary); }
.form-group input::placeholder { color: var(--text-muted); }
</style>

<?php require __DIR__ . '/../templates/footer.php'; ?>
