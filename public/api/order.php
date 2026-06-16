<?php
require __DIR__ . '/../../core/Database.php';

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

$db = Database::getInstance();
$orderId = $db->insert('xvilo_orders', [
    'plan_name'       => $plan,
    'plan_price'      => $price,
    'customer_name'   => $name,
    'customer_contact' => $contact,
    'server_name'     => $server_name,
    'gamemode'        => $gamemode ?: null,
]);

header('Location: /payment.php?id=' . $orderId);
