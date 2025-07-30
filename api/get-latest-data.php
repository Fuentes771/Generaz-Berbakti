<?php
require_once '../includes/config.php';

header('Content-Type: application/json');

try {
    $db = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    // Handle POST request (data from Arduino)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        // Validate node_id (ensure between 1-4)
        if (isset($data['node_id'])) {
            $nodeId = (int)$data['node_id'];
            if ($nodeId < 1 || $nodeId > 4) {
                // If node_id is invalid, normalize to range 1-4
                $nodeId = ($nodeId % 4) + 1;
                error_log("Normalized invalid node_id from " . $data['node_id'] . " to " . $nodeId);
            }
            
            $sensorData = [
                'node_id' => $nodeId,  // Use normalized node_id
                'timestamp' => date('Y-m-d H:i:s'),
                'temperature' => $data['environmental']['temperature_c'] ?? null,
                'humidity' => $data['environmental']['humidity_pct'] ?? null,
                'pressure' => $data['environmental']['pressure_hpa'] ?? null,
                'vibration' => max($data['vibration'] ?? [0, 0, 0]),
                'mpu6050' => sqrt(
                    pow($data['motion']['accelerometer']['x'] ?? 0, 2) + 
                    pow($data['motion']['accelerometer']['y'] ?? 0, 2) + 
                    pow($data['motion']['accelerometer']['z'] ?? 0, 2)
                ),
                'latitude' => $data['location']['latitude'] ?? null,
                'longitude' => $data['location']['longitude'] ?? null
            ];
            
            $stmt = $db->prepare("INSERT INTO sensor_data 
                (node_id, timestamp, temperature, humidity, pressure, vibration, mpu6050, latitude, longitude) 
                VALUES (:node_id, :timestamp, :temperature, :humidity, :pressure, :vibration, :mpu6050, :latitude, :longitude)");
            
            $stmt->execute($sensorData);
            
            // Log with correct node_id
            $db->prepare("INSERT INTO event_logs (node_id, event_type, message) 
                VALUES (:node_id, 'info', 'Data received from node')")
                ->execute(['node_id' => $nodeId]);
            
            echo json_encode(['status' => 'success', 'message' => 'Data saved']);
            exit;
        }
    }

    // Handle GET request for chart data
    if (isset($_GET['action']) && $_GET['action'] === 'get-chart-data') {
        $nodes = isset($_GET['nodes']) ? explode(',', $_GET['nodes']) : [];
        $sensors = isset($_GET['sensors']) ? explode(',', $_GET['sensors']) : [];
        $hours = isset($_GET['hours']) ? (int)$_GET['hours'] : 1;

        if (empty($nodes) || empty($sensors)) {
            echo json_encode(['status' => 'error', 'message' => 'No nodes or sensors selected']);
            exit;
        }

        $result = [];

        foreach ($nodes as $nodeId) {
            $nodeId = (int)$nodeId;
            $nodeData = ['node_id' => $nodeId];
            
            // Calculate time range
            $timeRange = new DateTime();
            $timeRange->sub(new DateInterval("PT{$hours}H"));
            $timeString = $timeRange->format('Y-m-d H:i:s');
            
            // Get data for selected sensors
            $stmt = $db->prepare("
                SELECT timestamp, temperature, humidity, pressure, vibration, mpu6050 
                FROM sensor_data 
                WHERE node_id = :node_id AND timestamp >= :time_range
                ORDER BY timestamp ASC
            ");
            $stmt->execute(['node_id' => $nodeId, 'time_range' => $timeString]);
            $sensorData = $stmt->fetchAll();
            
            // Format data for chart
            foreach ($sensors as $sensor) {
                if (in_array($sensor, ['temperature', 'humidity', 'pressure', 'vibration', 'mpu6050'])) {
                    $nodeData[$sensor] = array_map(function($row) use ($sensor) {
                        return [
                            'x' => $row['timestamp'],
                            'y' => $row[$sensor] ?? null
                        ];
                    }, $sensorData);
                }
            }
            
            $result[] = $nodeData;
        }

        echo json_encode($result);
        exit;
    }

    // Default GET request (for dashboard data)
    $nodesData = [];
    for ($i = 1; $i <= 4; $i++) {
        try {
            $stmt = $db->prepare("
                SELECT sd.*, nl.name as node_name 
                FROM sensor_data sd
                LEFT JOIN node_locations nl ON sd.node_id = nl.node_id
                WHERE sd.node_id = :node_id
                ORDER BY sd.timestamp DESC 
                LIMIT 1
            ");
            $stmt->execute(['node_id' => $i]);
            $nodeData = $stmt->fetch() ?? [];
            
            if ($nodeData) {
                $vibrationLevel = $nodeData['vibration'] ?? 0;
                $mpuLevel = $nodeData['mpu6050'] ?? 0;
                
                $status = 'NORMAL';
                $statusClass = 'status-normal';
                if ($vibrationLevel > VIBRATION_DANGER || $mpuLevel > ACCELERATION_DANGER) {
                    $status = 'DANGER';
                    $statusClass = 'status-danger';
                } elseif ($vibrationLevel > VIBRATION_WARNING || $mpuLevel > ACCELERATION_WARNING) {
                    $status = 'WARNING';
                    $statusClass = 'status-warning';
                }
                
                $nodesData[$i] = [
                    'node_id' => $i,
                    'timestamp' => $nodeData['timestamp'],
                    'temperature' => $nodeData['temperature'] ?? '--',
                    'humidity' => $nodeData['humidity'] ?? '--',
                    'pressure' => $nodeData['pressure'] ?? '--',
                    'vibration' => $vibrationLevel,
                    'mpu6050' => round($mpuLevel, 2),
                    'latitude' => $nodeData['latitude'] ?? 0,
                    'longitude' => $nodeData['longitude'] ?? 0,
                    'status' => $status,
                    'status_class' => $statusClass,
                    'node_name' => $nodeData['node_name'] ?? 'Node ' . $i
                ];
            }
        } catch (PDOException $e) {
            error_log("Error fetching data for node $i: " . $e->getMessage());
            $nodesData[$i] = [];
        }
    }

    // Get recent event logs
    $logs = $db->query("
        SELECT * FROM event_logs 
        ORDER BY timestamp DESC 
        LIMIT 5
    ")->fetchAll();

    echo json_encode([
        'status' => 'success',
        'timestamp' => date('Y-m-d H:i:s'),
        'nodes' => $nodesData,
        'logs' => $logs
    ]);

} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}