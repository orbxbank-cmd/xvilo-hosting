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
    'pterodactyl' => [
        'base_url' => getenv('PTERO_URL') ?: 'http://62.84.180.151',
        'api_key'  => getenv('PTERO_API_KEY') ?: 'xvloBiII4uJr0yFNffd20e80c4c042a4ccdaa73581bf6e0938d1eb678a14d5d8b6af3ea204969472',
        'node_id'  => (int)(getenv('PTERO_NODE') ?: 2),
        'nest_id'  => (int)(getenv('PTERO_NEST') ?: 2),
        'egg_id'   => (int)(getenv('PTERO_EGG') ?: 1),
    ],
    'admin_code'  => getenv('ADMIN_CODE') ?: 'admin123',
    'app_url'     => getenv('APP_URL') ?: 'https://xvilo-hosting.onrender.com',
    'upload_max'  => 10 * 1024 * 1024,
    'google_oauth' => [
        'client_id'     => getenv('GOOGLE_CLIENT_ID') ?: '',
        'client_secret' => getenv('GOOGLE_CLIENT_SECRET') ?: '',
        'redirect_uri'  => getenv('APP_URL') ?: 'https://xvilo-hosting.onrender.com',
    ],
];
