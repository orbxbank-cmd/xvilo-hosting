<?php
require __DIR__ . '/../../core/Database.php';
require __DIR__ . '/../../core/Auth.php';
Auth::require();
$user = Auth::user();

$id = (int)($_GET['id'] ?? 0);
$db = Database::getInstance();
$order = $db->fetch("SELECT * FROM xvilo_orders WHERE id = ? AND user_id = ?", [$id, $user['id']]);
if (!$order || !$order['server_db_user'] || !$order['server_db_pass']) {
    header('Location: /server.php?id=' . $id);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Connexion phpMyAdmin</title>
  <style>
    body { background:#0a0a0a; color:#fff; font-family:sans-serif; display:flex; align-items:center; justify-content:center; min-height:100vh; margin:0; }
    .msg { text-align:center; }
    .msg h2 { color:#22c55e; }
    .msg p { color:#888; }
  </style>
</head>
<body>
  <div class="msg">
    <h2>Connexion automatique...</h2>
    <p>Redirection vers phpMyAdmin</p>
  </div>
  <form id="loginForm" method="post" action="http://62.84.180.151/phpmyadmin/index.php" target="_blank">
    <input type="hidden" name="pma_username" value="<?= htmlspecialchars($order['server_db_user']) ?>">
    <input type="hidden" name="pma_password" value="<?= htmlspecialchars($order['server_db_pass']) ?>">
    <input type="hidden" name="server" value="1">
  </form>
  <script>
    document.getElementById('loginForm').submit();
  </script>
</body>
</html>
