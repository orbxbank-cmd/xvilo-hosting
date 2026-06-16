<?php
require __DIR__ . '/../../core/Database.php';
require __DIR__ . '/../../core/Auth.php';

$config = require __DIR__ . '/../../config/app.php';
$google = $config['google_oauth'];

if (!$google['client_id'] || !$google['client_secret']) {
    header('Location: /auth/login.php?error=google_not_configured');
    exit;
}

if (!isset($_GET['code'])) {
    $params = http_build_query([
        'client_id' => $google['client_id'],
        'redirect_uri' => $google['redirect_uri'] . '/auth/google.php',
        'response_type' => 'code',
        'scope' => 'email profile',
        'prompt' => 'select_account',
    ]);
    header('Location: https://accounts.google.com/o/oauth2/auth?' . $params);
    exit;
}

$code = $_GET['code'];

$tokenData = @file_get_contents('https://oauth2.googleapis.com/token', false, stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/x-www-form-urlencoded',
        'content' => http_build_query([
            'code' => $code,
            'client_id' => $google['client_id'],
            'client_secret' => $google['client_secret'],
            'redirect_uri' => $google['redirect_uri'] . '/auth/google.php',
            'grant_type' => 'authorization_code',
        ]),
    ],
]));

if (!$tokenData) {
    header('Location: /auth/login.php?error=token_failed');
    exit;
}

$token = json_decode($tokenData, true);
if (!isset($token['access_token'])) {
    header('Location: /auth/login.php?error=invalid_token');
    exit;
}

$userInfo = @file_get_contents('https://www.googleapis.com/oauth2/v2/userinfo', false, stream_context_create([
    'http' => [
        'header' => 'Authorization: Bearer ' . $token['access_token'],
    ],
]));

if (!$userInfo) {
    header('Location: /auth/login.php?error=userinfo_failed');
    exit;
}

$user = json_decode($userInfo, true);
if (!isset($user['id'])) {
    header('Location: /auth/login.php?error=invalid_user');
    exit;
}

$userId = Auth::findOrCreateByGoogle($user['id'], $user['email'], $user['name']);
Auth::login($userId);

header('Location: /dashboard.php');
