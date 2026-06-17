<?php

class PterodactylAPI
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct()
    {
        $config = require __DIR__ . '/../config/app.php';
        $ptero = $config['pterodactyl'];
        $this->baseUrl = rtrim($ptero['base_url'], '/');
        $this->apiKey = $ptero['api_key'];
    }

    public function request(string $method, string $endpoint, array $data = []): ?array
    {
        $url = $this->baseUrl . $endpoint;
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->apiKey,
                'Accept: application/json',
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT => 15,
            CURLOPT_CONNECTTIMEOUT => 5,
        ]);

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        }

        return null;
    }

    public function getNextAllocation(int $nodeId): ?array
    {
        $res = $this->request('GET', "/api/application/nodes/{$nodeId}/allocations?per_page=100");
        if (!$res || empty($res['data'])) return null;

        foreach ($res['data'] as $alloc) {
            if (!$alloc['attributes']['assigned']) {
                return [
                    'id' => $alloc['attributes']['id'],
                    'port' => $alloc['attributes']['port'],
                    'ip' => $alloc['attributes']['ip'],
                ];
            }
        }
        return null;
    }

    public function findUserByUsername(string $username): ?int
    {
        $res = $this->request('GET', "/api/application/users?filter[username]={$username}");
        if ($res && !empty($res['data'])) {
            return $res['data'][0]['attributes']['id'];
        }
        return null;
    }

    public function createServer(array $params): ?array
    {
        return $this->request('POST', '/api/application/servers', $params);
    }

    public function buildServerPayload(array $order, int $allocationId, int $userId): array
    {
        $resources = self::getPlanResources($order['plan_name']);
        $maxPlayers = self::getPlanSlots($order['plan_name']);

        $allocInfo = $this->request('GET', "/api/application/nodes/2/allocations/{$allocationId}");
        $allocPort = $allocInfo['attributes']['port'] ?? '7777';

        return [
            'name' => $order['server_name'] ?: ('Serveur-' . $order['id']),
            'user' => $userId,
            'egg' => 1,
            'docker_image' => 'temasm/samp',
            'startup' => './samp03svr {{SERVER_PORT}} {{MAX_PLAYERS}}',
            'environment' => [
                'SAMP_VERSION' => '0.3.7',
                'SERVER_PORT' => (string)$allocPort,
                'MAX_PLAYERS' => $maxPlayers,
            ],
            'limits' => [
                'memory' => $resources['memory'],
                'swap' => 0,
                'disk' => $resources['disk'],
                'io' => 500,
                'cpu' => $resources['cpu'],
            ],
            'feature_limits' => [
                'databases' => 1,
                'allocations' => 1,
                'backups' => 0,
            ],
            'allocation' => [
                'default' => $allocationId,
            ],
        ];
    }

    public function getServer(int $serverId): ?array
    {
        return $this->request('GET', "/api/application/servers/{$serverId}");
    }

    public function createDatabase(int $serverId, int $databaseHostId): ?array
    {
        $dbName = 'srv_' . $serverId;
        $password = bin2hex(random_bytes(8));
        $result = $this->request('POST', "/api/application/servers/{$serverId}/databases", [
            'database' => $dbName,
            'host' => $databaseHostId,
            'password' => $password,
        ]);

        if ($result && isset($result['attributes'])) {
            $result['_plain_password'] = $password;
        }
        return $result;
    }

    public function suspendServer(int $serverId): ?array
    {
        return $this->request('POST', "/api/application/servers/{$serverId}/suspend");
    }

    public function unsuspendServer(int $serverId): ?array
    {
        return $this->request('POST', "/api/application/servers/{$serverId}/unsuspend");
    }

    public function deleteServer(int $serverId): bool
    {
        $result = $this->request('DELETE', "/api/application/servers/{$serverId}");
        return $result === [] || $result === null;
    }

    public static function getPlanResources(string $plan): array
    {
        $plans = [
            'SAMP I'      => ['memory' => 1024, 'disk' => 51200, 'cpu' => 100],
            'SAMP II'      => ['memory' => 2048, 'disk' => 102400, 'cpu' => 100],
            'SAMP III'     => ['memory' => 3072, 'disk' => 153600, 'cpu' => 100],
            'SAMP IV MAX' => ['memory' => 4096, 'disk' => 204800, 'cpu' => 200],
        ];
        return $plans[$plan] ?? ['memory' => 1024, 'disk' => 51200, 'cpu' => 100];
    }

    public static function getPlanSlots(string $plan): int
    {
        $plans = [
            'SAMP I'      => 250,
            'SAMP II'      => 500,
            'SAMP III'     => 750,
            'SAMP IV MAX' => 1000,
        ];
        return $plans[$plan] ?? 250;
    }
}
