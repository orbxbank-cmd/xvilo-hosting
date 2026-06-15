<?php
require __DIR__ . '/../core/Database.php';

$order_id = (int)($_POST['order_id'] ?? 0);
$method = $_POST['method'] ?? '';

if (!$order_id || !in_array($method, ['inwi', 'orange'])) {
    header('Location: /payment.php?id=' . $order_id . '&error=Méthode invalide');
    exit;
}

$db = Database::getInstance();
$order = $db->fetch("SELECT * FROM xvilo_orders WHERE id = ? AND status = 'pending'", [$order_id]);

if (!$order) {
    header('Location: /');
    exit;
}

// Generate a random payment code
$paymentCode = strtoupper(substr(md5(uniqid()), 0, 8)) . '-' . $order_id;

$db->update('xvilo_orders', [
    'payment_method' => $method,
    'payment_code'   => $paymentCode,
], 'id = :id', ['id' => $order_id]);

header('Location: /payment.php?id=' . $order_id);
