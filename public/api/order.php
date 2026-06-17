<?php
require __DIR__ . '/../../core/Database.php';
require __DIR__ . '/../../core/Auth.php';

$plan = $_POST['plan'] ?? '';
$price = (int)($_POST['price'] ?? 0);
$name = trim($_POST['name'] ?? '');
$contact = trim($_POST['contact'] ?? '');
$server_name = trim($_POST['server_name'] ?? '');
$gamemode = trim($_POST['gamemode'] ?? '');

$plans = ['SAMP I' => 15, 'SAMP II' => 20, 'SAMP III' => 30, 'SAMP IV MAX' => 45];

if (!isset($plans[$plan]) || $plans[$plan] !== $price || !$name || !$contact || !$server_name) {
    header('Location: /order.php?plan=' . urlencode($plan) . '&price=' . $price);
    exit;
}

Auth::init();
$userId = Auth::userId();

if (!$userId) {
    header('Location: /auth/login.php');
    exit;
}

try {
    $db = Database::getInstance();
    $orderId = $db->insert('xvilo_orders', [
        'user_id'         => $userId,
        'plan_name'       => $plan,
        'plan_price'      => $price,
        'customer_name'   => $name,
        'customer_contact' => $contact,
        'server_name'     => $server_name,
        'gamemode'        => $gamemode ?: null,
    ]);
    header('Location: /payment.php?id=' . $orderId);
    exit;
} catch (Exception $e) {
    header('Location: /order.php?plan=' . urlencode($plan) . '&price=' . $price . '&error=' . urlencode('Erreur: ' . $e->getMessage()));
    exit;
}
