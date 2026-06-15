<?php
session_start();

$config = require __DIR__ . '/../config/app.php';

$code = $_POST['code'] ?? '';
if ($code && $code === $config['admin_code']) {
    $_SESSION['admin_logged_in'] = true;
    header('Location: /admin/orders.php');
    exit;
}

// If already logged in, go to orders
if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: /admin/orders.php');
    exit;
}

// Invalid code
header('Location: /?error=Code PIN incorrect');
