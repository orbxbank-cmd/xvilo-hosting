<?php

return [
    'db' => [
        'host'     => getenv('DB_HOST') ?: '62.84.180.151',
        'port'     => getenv('DB_PORT') ?: '3306',
        'dbname'   => getenv('DB_NAME') ?: 'xvilo_hosting',
        'username' => getenv('DB_USER') ?: 'xvilo',
        'password' => getenv('DB_PASS') ?: 'Xvilo2024!',
        'charset'  => 'utf8mb4',
    ],
    'admin_code'  => getenv('ADMIN_CODE') ?: 'admin123',
    'app_url'     => getenv('APP_URL') ?: 'https://xvilo-hosting.onrender.com',
    'upload_max'  => 10 * 1024 * 1024,
];
