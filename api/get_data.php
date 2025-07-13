<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . (DEBUG_MODE ? '*' : BASE_URL));
header('Cache-Control: no-cache, must-revalidate');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

class DataFetcher {
    private string $espIp;
    private PDO $db;
    private int $requestTimeout;
    private array $statusThresholds = [
        'vibration' => [
            'danger' => 80,
            'warning' => 50
        ],
        'mpu6050' => [
            'danger' => 70,
            'warning' => 40
        ]
    ];

    public function __construct(string $espIp, int $requestTimeout = 2) {
        $this->espIp = $espIp;
        $this->requestTimeout = $requestTimeout;
        $this->db = get_db_connection();
    }

    public function fetchData(): void {
        try {
            $espData = $this->fetchESPData();
            $this->validateData($espData);
            
            $this->db->beginTransaction();
            $sensorId = $this->saveToDatabase($espData);
            $this->logEventIfNeeded($espData, $sensorId);
            $this->db->commit();

            $this->sendResponse(true, 'Data saved successfully', $espData);
        } catch (InvalidArgumentException $e) {
            $this->db->rollBack();
            $this->sendResponse(false, $e->getMessage(), [], 400);
        } catch (RuntimeException $e) {
            $this->db->rollBack();
            $this->sendResponse(false, $e->getMessage(), [], 500);
        } catch (Exception $e) {
            $this->db->rollBack();
            $this->sendResponse(false, 'An unexpected error occurred', [], 500);
        }
    }

    private function fetchESPData(): array {
        $url = "http://{$this->espIp}/getAllData";
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->requestTimeout,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
            CURLOPT_FAILONERROR => true
        ]);

        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            throw new RuntimeException("ESP request failed: " . curl_error($ch));
        }
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode !== 200) {
            throw new RuntimeException("ESP returned HTTP code {$httpCode}");
        }
        
        curl_close($ch);

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException("Invalid JSON from ESP: " . json_last_error_msg());
        }

        return $data;
    }

    private function validateData(array $data): void {
        if (!validate_sensor_data($data)) {
            throw new InvalidArgumentException("Invalid or incomplete sensor data");
        }
    }

    private function saveToDatabase(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO sensor_data 
            (vibration, mpu6050, pressure, temperature, humidity, status, timestamp) 
            VALUES (:vibration, :mpu6050, :pressure, :temp, :humidity, :status, NOW())"
        );

        $status = $this->determineSystemStatus($data);
        
        $stmt->execute([
            ':vibration' => (int)$data['vibration'],
            ':mpu6050' => (float)$data['mpu6050'],
            ':pressure' => (float)$data['pressure'],
            ':temp' => (float)$data['temperature'],
            ':humidity' => (float)$data['humidity'],
            ':status' => $status
        ]);

        return (int)$this->db->lastInsertId();
    }

    private function determineSystemStatus(array $data): string {
        if ($data['vibration'] >= $this->statusThresholds['vibration']['danger'] || 
            $data['mpu6050'] >= $this->statusThresholds['mpu6050']['danger']) {
            return 'Danger';
        }
        if ($data['vibration'] >= $this->statusThresholds['vibration']['warning'] || 
            $data['mpu6050'] >= $this->statusThresholds['mpu6050']['warning']) {
            return 'Warning';
        }
        return 'Normal';
    }

    private function logEventIfNeeded(array $data, int $sensorId): void {
        $status = $this->determineSystemStatus($data);
        if ($status !== 'Normal') {
            $stmt = $this->db->prepare(
                "INSERT INTO event_logs 
                (event_type, sensor_data_id, vibration, mpu6050, pressure, status, timestamp)
                VALUES (:eventType, :sensorId, :vibration, :mpu6050, :pressure, :status, NOW())"
            );

            $stmt->execute([
                ':eventType' => $this->getEventType($status),
                ':sensorId' => $sensorId,
                ':vibration' => (int)$data['vibration'],
                ':mpu6050' => (float)$data['mpu6050'],
                ':pressure' => (float)$data['pressure'],
                ':status' => $status
            ]);
        }
    }

    private function getEventType(string $status): string {
        return match($status) {
            'Danger' => 'Tsunami Alert',
            'Warning' => 'Vibration Warning',
            default => 'System Event'
        };
    }

    private function sendResponse(bool $success, string $message, array $data = [], int $code = 200): void {
        http_response_code($code);
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('c'),
            'version' => APP_VERSION
        ]);
        exit;
    }
}

try {
    // Verify API key if in production
    if (!DEBUG_MODE && (!isset($_GET['api_key']) || $_GET['api_key'] !== API_KEY)) {
        header('HTTP/1.1 401 Unauthorized');
        die(json_encode(['success' => false, 'message' => 'Invalid API key']));
    }

    $fetcher = new DataFetcher(ESP_IP);
    $fetcher->fetchData();
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