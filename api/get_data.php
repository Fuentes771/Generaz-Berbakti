<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Database configuration
$servername = "localhost";
$username = "u855675680_mntrrinovajaya";
$password = "Generazberbaktijaya123!";
$dbname = "u855675680_mntrpekon";

// Get parameters
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
$nodeId = isset($_GET['node_id']) ? (int)$_GET['node_id'] : null;

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Prepare SQL query
    $sql = "SELECT * FROM sensor_data";
    
    if ($nodeId) {
        $sql .= " WHERE node_id = :node_id";
    }
    
    $sql .= " ORDER BY timestamp DESC LIMIT :limit";
    
    $stmt = $conn->prepare($sql);
    
    if ($nodeId) {
        $stmt->bindParam(':node_id', $nodeId, PDO::PARAM_INT);
    }
    
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate system status for each record
    $formattedResults = array_map(function($item) {
        // Determine status based on sensor thresholds
        $status = 'Normal';
        $vibration = ($item['piezo_1'] + $item['piezo_2'] + $item['piezo_3']) / 3;
        $mpu6050 = sqrt(
            pow($item['accel_x'], 2) + 
            pow($item['accel_y'], 2) + 
            pow($item['accel_z'], 2)
        );
        
        if ($vibration >= 500 || $mpu6050 >= 1.5) {
            $status = 'Danger';
        } elseif ($vibration >= 300 || $mpu6050 >= 1.0) {
            $status = 'Warning';
        }
        
        return [
            'node_id' => $item['node_id'],
            'temperature' => (float)$item['temperature'],
            'humidity' => (float)$item['humidity'],
            'pressure' => (float)$item['pressure'],
            'accel_x' => (float)$item['accel_x'],
            'accel_y' => (float)$item['accel_y'],
            'accel_z' => (float)$item['accel_z'],
            'gyro_x' => (float)$item['gyro_x'],
            'gyro_y' => (float)$item['gyro_y'],
            'gyro_z' => (float)$item['gyro_z'],
            'piezo_1' => (int)$item['piezo_1'],
            'piezo_2' => (int)$item['piezo_2'],
            'piezo_3' => (int)$item['piezo_3'],
            'latitude' => $item['latitude'] ? (float)$item['latitude'] : null,
            'longitude' => $item['longitude'] ? (float)$item['longitude'] : null,
            'timestamp' => $item['timestamp'],
            'status' => $status,
            'vibration_level' => $vibration,
            'mpu6050_level' => $mpu6050
        ];
    }, $results);
    
    echo json_encode([
        'success' => true,
        'data' => $formattedResults,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}