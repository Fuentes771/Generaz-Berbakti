<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Database configuration
$servername = "localhost";
$username = "u855675680_mntrrinovajaya";
$password = "Generazberbaktijaya123!";
$dbname = "u855675680_mntrpekon";

// Get parameters
$limit = isset($_GET['limit']) ? min((int)$_GET['limit'], 100) : 10;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$offset = ($page - 1) * $limit;

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get total count
    $countStmt = $conn->query("SELECT COUNT(*) FROM sensor_data");
    $total = $countStmt->fetchColumn();
    
    // Get logs
    $sql = "SELECT * FROM sensor_data ORDER BY timestamp DESC LIMIT :limit OFFSET :offset";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format logs with status information
    $formattedLogs = array_map(function($item) {
        $vibration = ($item['piezo_1'] + $item['piezo_2'] + $item['piezo_3']) / 3;
        $mpu6050 = sqrt(
            pow($item['accel_x'], 2) + 
            pow($item['accel_y'], 2) + 
            pow($item['accel_z'], 2)
        );
        
        $status = 'Normal';
        if ($vibration >= 5000 || $mpu6050 >= 1500) {
            $status = 'Danger';
        } elseif ($vibration >= 3000 || $mpu6050 >= 1000) {
            $status = 'Warning';
        }
        
        return [
            'id' => $item['id'],
            'event_type' => $status === 'Danger' ? 'Tsunami Alert' : ($status === 'Warning' ? 'Vibration Warning' : 'Normal Reading'),
            'timestamp' => $item['timestamp'],
            'status' => $status,
            'readings' => [
                'vibration' => $vibration,
                'mpu6050' => $mpu6050,
                'pressure' => (float)$item['pressure'],
                'temperature' => (float)$item['temperature'],
                'humidity' => (float)$item['humidity']
            ],
            'alert_level' => strtolower($status)
        ];
    }, $results);
    
    echo json_encode([
        'success' => true,
        'data' => $formattedLogs,
        'pagination' => [
            'total' => (int)$total,
            'per_page' => $limit,
            'current_page' => $page,
            'last_page' => ceil($total / $limit)
        ]
    ]);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}