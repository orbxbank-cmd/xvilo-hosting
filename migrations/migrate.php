<?php
require __DIR__ . '/../config/app.php';

$config = require __DIR__ . '/../config/app.php';
$db = $config['db'];

try {
    $pdo = new PDO("mysql:host={$db['host']};port={$db['port']};dbname={$db['dbname']};charset={$db['charset']}", $db['username'], $db['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $pdo->exec("CREATE TABLE IF NOT EXISTS xvilo_orders (
        id int(11) NOT NULL AUTO_INCREMENT,
        plan_name varchar(50) NOT NULL,
        plan_price int(11) NOT NULL,
        customer_name varchar(100) NOT NULL,
        customer_contact varchar(100) NOT NULL,
        server_name varchar(100) NOT NULL,
        gamemode varchar(100) DEFAULT NULL,
        payment_method enum('inwi','orange') DEFAULT NULL,
        payment_code varchar(50) DEFAULT NULL,
        screenshot varchar(255) DEFAULT NULL,
        status enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
        admin_note text DEFAULT NULL,
        created_at timestamp NOT NULL DEFAULT current_timestamp(),
        updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

    echo "Migration OK\n";
} catch (Exception $e) {
    echo "Migration error: " . $e->getMessage() . "\n";
}
