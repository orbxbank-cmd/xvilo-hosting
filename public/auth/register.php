<?php
require __DIR__ . '/../../core/Database.php';
require __DIR__ . '/../../core/Auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $name = trim($_POST['name'] ?? '');

    if (!$email || !$password || !$name) {
        $error = 'Tous les champs sont requis.';
    } elseif (strlen($password) < 6) {
        $error = 'Le mot de passe doit faire au moins 6 caractères.';
    } else {
        $user = Auth::register($email, $password, $name);
        if ($user) {
            Auth::login($user['id']);
            header('Location: /dashboard.php');
            exit;
        }
        $error = 'Cet email est déjà utilisé.';
    }
}

require __DIR__ . '/../../templates/header.php';
?>
<section class="section" style="padding-top:120px;">
  <div class="container" style="max-width:450px;">
    <h2 class="section-title">Créer un <span class="gradient-text">compte</span></h2>
    <p class="section-sub">Rejoins Xvilo Hosting et gère tes serveurs.</p>

    <?php if ($error): ?>
      <div class="admin-msg" style="background:var(--accent);"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form class="order-form" method="POST">
      <div class="form-group">
        <label>Nom / Pseudo</label>
        <input type="text" name="name" required placeholder="Ex: Zagtos">
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required placeholder="ton@email.com">
      </div>
      <div class="form-group">
        <label>Mot de passe</label>
        <input type="password" name="password" required minlength="6" placeholder="Au moins 6 caractères">
      </div>
      <button type="submit" class="btn btn-primary btn-block btn-lg">Créer mon compte</button>
    </form>

    <p style="text-align:center;margin-top:20px;color:var(--text-muted);font-size:14px;">
      Déjà un compte ? <a href="/auth/login.php" style="color:var(--accent);">Connecte-toi</a>
    </p>
  </div>
</section>
<?php require __DIR__ . '/../../templates/footer.php'; ?>
