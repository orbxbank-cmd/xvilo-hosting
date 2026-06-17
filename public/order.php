<?php
require __DIR__ . '/../core/Auth.php';
Auth::init();
$user = Auth::user();

$plan = $_GET['plan'] ?? '';
$price = $_GET['price'] ?? '';
$plans = ['SAMP I' => 15, 'SAMP II' => 20, 'SAMP III' => 30, 'SAMP IV MAX' => 45];

if (!isset($plans[$plan])) {
    header('Location: /');
    exit;
}

if (!$user) {
    header('Location: /auth/login.php?redirect=/order.php?plan=' . urlencode($plan) . '&price=' . urlencode($price));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postPlan = $_POST['plan'] ?? '';
    $postPrice = (int)($_POST['price'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $server_name = trim($_POST['server_name'] ?? '');
    $gamemode = trim($_POST['gamemode'] ?? '');

    if (!isset($plans[$postPlan]) || $plans[$postPlan] !== $postPrice || !$name || !$contact || !$server_name) {
        $error = 'Tous les champs sont requis.';
    } else {
        try {
            $db = Database::getInstance();
            $orderId = $db->insert('xvilo_orders', [
                'user_id'          => $user['id'],
                'plan_name'        => $postPlan,
                'plan_price'       => $postPrice,
                'customer_name'    => $name,
                'customer_contact' => $contact,
                'server_name'      => $server_name,
                'gamemode'         => $gamemode ?: null,
            ]);
            header('Location: /payment.php?id=' . $orderId);
            exit;
        } catch (Exception $e) {
            $error = 'Erreur: ' . $e->getMessage();
        }
    }
}

require __DIR__ . '/../templates/header.php';
?>
<section class="section" style="padding-top:120px;">
  <div class="container" style="max-width:600px;">
    <?php if ($error): ?>
      <div class="admin-msg" style="background:var(--accent);margin-bottom:20px;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <h2 class="section-title">Commander <span class="gradient-text"><?= htmlspecialchars($plan) ?></span></h2>
    <p class="section-sub">Plan à <strong><?= $price ?> DH/mois</strong></p>

    <form class="order-form" method="POST">
      <input type="hidden" name="plan" value="<?= htmlspecialchars($plan) ?>">
      <input type="hidden" name="price" value="<?= (int)$price ?>">

      <div class="form-group">
        <label>Ton nom / Pseudo</label>
        <input type="text" name="name" required placeholder="Ex: Zagtos" value="<?= htmlspecialchars($user['name']) ?>">
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

<?php require __DIR__ . '/../templates/footer.php'; ?>
