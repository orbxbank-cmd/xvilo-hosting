<?php

return [
    'db' => [
        'host'     => getenv('DB_HOST') ?: '45.8.187.109',
        'port'     => getenv('DB_PORT') ?: '3306',
        'dbname'   => getenv('DB_NAME') ?: 's82939_Lost100',
        'username' => getenv('DB_USER') ?: 'pterodactyl',
        'password' => getenv('DB_PASS') ?: 'P@ssw0rd2024!',
        'charset'  => 'utf8mb4',
    ],
    'admin_code'  => getenv('ADMIN_CODE') ?: 'admin123',
    'app_url'     => getenv('APP_URL') ?: 'https://xvilo-hosting.onrender.com',
    'upload_max'  => 10 * 1024 * 1024,
];
