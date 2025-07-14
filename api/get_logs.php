<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . (DEBUG_MODE ? '*' : BASE_URL));
header('Cache-Control: no-cache, must-revalidate');
header('X-Content-Type-Options: nosniff');

class LogFetcher {
    private PDO $db;
    private int $defaultLimit = 10;
    private int $maxLimit = 100;

    public function __construct() {
        $this->db = get_db_connection();
    }

    public function fetchLogs(): void {
        try {
            $limit = $this->getSafeLimit($_GET['limit'] ?? null);
            $page = $this->getSafePage($_GET['page'] ?? null);
            $offset = ($page - 1) * $limit;

            $logs = $this->queryLogs($limit, $offset);
            $totalLogs = $this->getTotalLogs();
            
            $this->sendResponse([
                'success' => true,
                'data' => $logs,
                'pagination' => [
                    'total' => $totalLogs,
                    'per_page' => $limit,
                    'current_page' => $page,
                    'last_page' => ceil($totalLogs / $limit)
                ],
                'version' => APP_VERSION
            ]);
        } catch (Exception $e) {
            $this->sendResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function queryLogs(int $limit, int $offset): array {
        $stmt = $this->db->prepare("
            SELECT 
                l.id,
                l.event_type,
                l.vibration,
                l.mpu6050,
                l.pressure,
                l.status,
                l.timestamp,
                s.temperature,
                s.humidity
            FROM event_logs l
            JOIN sensor_data s ON l.sensor_data_id = s.id
            ORDER BY l.timestamp DESC
            LIMIT :limit OFFSET :offset
        ");

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return array_map([$this, 'formatLog'], $stmt->fetchAll());
    }

    private function getTotalLogs(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM event_logs")->fetchColumn();
    }

    private function formatLog(array $log): array {
        return [
            'id' => (int)$log['id'],
            'type' => $log['event_type'],
            'timestamp' => $log['timestamp'],
            'status' => $log['status'],
            'readings' => [
                'vibration' => (int)$log['vibration'],
                'mpu6050' => (float)$log['mpu6050'],
                'pressure' => (float)$log['pressure'],
                'temperature' => (float)($log['temperature'] ?? 0),
                'humidity' => (float)($log['humidity'] ?? 0)
            ],
            'alert_level' => $this->getAlertLevel($log['status'])
        ];
    }

    private function getAlertLevel(string $status): string {
        return match(strtolower($status)) {
            'danger' => 'critical',
            'warning' => 'high',
            default => 'normal'
        };
    }

    private function getSafeLimit(?string $input): int {
        $limit = filter_var($input, FILTER_VALIDATE_INT, [
            'options' => [
                'default' => $this->defaultLimit,
                'min_range' => 1,
                'max_range' => $this->maxLimit
            ]
        ]);
        return $limit;
    }

    private function getSafePage(?string $input): int {
        return filter_var($input, FILTER_VALIDATE_INT, [
            'options' => [
                'default' => 1,
                'min_range' => 1
            ]
        ]);
    }

    private function sendResponse(array $data, int $code = 200): void {
        http_response_code($code);
        echo json_encode($data);
        exit;
    }
}

try {
    // Verify API key if in production
    if (!DEBUG_MODE && (!isset($_GET['api_key']) || $_GET['api_key'] !== API_KEY)) {
        header('HTTP/1.1 401 Unauthorized');
        die(json_encode(['success' => false, 'message' => 'Invalid API key']));
    }

    $fetcher = new LogFetcher();
    $fetcher->fetchLogs();
} catch (Exception $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Initialization failed',
        'error' => $e->getMessage(),
        'timestamp' => date('c')
    ]);
}