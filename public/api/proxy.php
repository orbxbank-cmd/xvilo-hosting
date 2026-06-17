<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$action = $_GET['action'] ?? '';
$serverId = (int)($_GET['server_id'] ?? 0);
$cmd = $_GET['cmd'] ?? '';

if (!$serverId || !$action) {
    echo json_encode(['error' => 'Missing params']);
    exit;
}

$vpsUrl = 'http://62.84.180.151/panel-api.php';
$secret = 'xvil0pr0xy2024!';

$url = $vpsUrl . '?action=' . urlencode($action) . '&key=' . $secret . '&server_id=' . $serverId;
if ($cmd) {
    $url .= '&cmd=' . urlencode($cmd);
}

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_CONNECTTIMEOUT => 5,
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode >= 200 && $httpCode < 300 && $response) {
    echo $response;
} else {
    echo json_encode(['error' => 'VPS unreachable', 'code' => $httpCode]);
}
