<?php
/**
 * Cron: auto-remove expired servers
 * Run daily via cron: 0 0 * * * php /path/to/cron_expire.php
 */

require __DIR__ . '/core/Database.php';
require __DIR__ . '/core/PterodactylAPI.php';

$db = Database::getInstance();
$ptero = new PterodactylAPI();

$expired = $db->fetchAll(
    "SELECT * FROM xvilo_orders WHERE status = 'approved' AND server_id IS NOT NULL AND expires_at IS NOT NULL AND expires_at <= NOW()"
);

foreach ($expired as $order) {
    $serverId = (int)$order['server_id'];

    // Suspend first
    $ptero->suspendServer($serverId);
    echo "Suspended server #{$serverId} (order #{$order['id']})\n";

    // Delete after 3 days grace period? Or just suspend for now
    // $ptero->deleteServer($serverId); // Would need a deleteServer method

    $db->update('xvilo_orders', [
        'status' => 'expired',
        'server_username' => null,
        'server_password' => null,
    ], 'id = :id', ['id' => $order['id']]);

    echo "Expired order #{$order['id']}\n";
}

echo "Done. Processed " . count($expired) . " expired servers.\n";
