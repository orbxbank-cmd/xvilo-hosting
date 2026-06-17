<?php
require __DIR__ . '/../../core/Database.php';

$order_id = (int)($_POST['order_id'] ?? 0);

if (!$order_id || !isset($_FILES['screenshot']) || $_FILES['screenshot']['error'] !== UPLOAD_ERR_OK) {
    header('Location: /payment.php?id=' . $order_id . '&error=Erreur de téléchargement');
    exit;
}

$db = Database::getInstance();
$order = $db->fetch("SELECT * FROM xvilo_orders WHERE id = ? AND status = 'pending'", [$order_id]);

if (!$order) {
    header('Location: /');
    exit;
}

$file = $_FILES['screenshot'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

if (!in_array($ext, $allowed)) {
    header('Location: /payment.php?id=' . $order_id . '&error=Format non accepté');
    exit;
}

if ($file['size'] > 10 * 1024 * 1024) {
    header('Location: /payment.php?id=' . $order_id . '&error=Fichier trop volumineux (max 10MB)');
    exit;
}

$uploadDir = __DIR__ . '/../uploads/proofs/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
$filename = 'order_' . $order_id . '_' . time() . '.' . $ext;
move_uploaded_file($file['tmp_name'], $uploadDir . $filename);

$db->update('xvilo_orders', [
    'screenshot' => '/uploads/proofs/' . $filename,
], 'id = :id', ['id' => $order_id]);

header('Location: /payment.php?id=' . $order_id . '&success=Merci ! Commande soumise avec succès.');
