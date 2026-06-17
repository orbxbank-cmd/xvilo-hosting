<?php
require __DIR__ . '/../../core/Database.php';
require __DIR__ . '/../../core/Auth.php';

$error = '';
$redirect = trim($_POST['redirect'] ?? $_GET['redirect'] ?? '/dashboard.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = 'Email et mot de passe requis.';
    } else {
        $userId = Auth::loginByEmail($email, $password);
        if ($userId) {
            Auth::login($userId);
            header('Location: ' . $redirect);
            exit;
        }
        $error = 'Email ou mot de passe incorrect.';
    }
}

$config = require __DIR__ . '/../../config/app.php';
$googleClientId = $config['google_oauth']['client_id'];

require __DIR__ . '/../../templates/header.php';
?>
<section class="section" style="padding-top:120px;">
  <div class="container" style="max-width:450px;">
    <h2 class="section-title">Connexion</h2>
    <p class="section-sub">Connecte-toi pour gérer tes serveurs.</p>

    <?php if ($error): ?>
      <div class="admin-msg" style="background:var(--accent);"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form class="order-form" method="POST">
      <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required placeholder="ton@email.com">
      </div>
      <div class="form-group">
        <label>Mot de passe</label>
        <input type="password" name="password" required placeholder="Ton mot de passe">
      </div>
      <button type="submit" class="btn btn-primary btn-block btn-lg">Se connecter</button>
    </form>

    <?php if ($googleClientId): ?>
      <div style="text-align:center;margin-top:20px;position:relative;">
        <div style="border-top:1px solid var(--border);margin-bottom:20px;"></div>
        <a href="/auth/google.php" style="display:inline-flex;align-items:center;gap:10px;background:#fff;color:#333;padding:10px 24px;border-radius:6px;text-decoration:none;font-weight:600;font-size:14px;border:1px solid #ddd;">
          <svg width="18" height="18" viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.54 28.59A14.5 14.5 0 0 1 9.5 24c0-1.59.28-3.14.76-4.59l-7.98-6.19A23.99 23.99 0 0 0 0 24c0 3.77.87 7.35 2.56 10.56l7.98-5.97z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 5.97C6.51 42.62 14.62 48 24 48z"/></svg>
          Se connecter avec Google
        </a>
      </div>
    <?php endif; ?>

    <p style="text-align:center;margin-top:20px;color:var(--text-muted);font-size:14px;">
      Pas de compte ? <a href="/auth/register.php" style="color:var(--accent);">Inscris-toi</a>
    </p>
  </div>
</section>
<?php require __DIR__ . '/../../templates/footer.php'; ?>
