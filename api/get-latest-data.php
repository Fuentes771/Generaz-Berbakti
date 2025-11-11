<?php
/**
 * Get Latest Data API
 * Returns the most recent sensor reading for each node (1-4)
 * 
 * @endpoint GET /api/get-latest-data.php
 * @response JSON with node data and computed status using computeStatus()
 */

require_once '../includes/config.php';

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

try {
    $conn = getDatabaseConnection();
    
    // Ambil data terbaru dari setiap node
    $stmt = $conn->prepare("
        SELECT sd.* FROM sensor_data sd
        INNER JOIN (
            SELECT node_id, MAX(timestamp) as latest_timestamp 
            FROM sensor_data 
            GROUP BY node_id
        ) latest ON sd.node_id = latest.node_id AND sd.timestamp = latest.latest_timestamp
        ORDER BY sd.node_id
    ");
    $stmt->execute();
    
    $rawData = $stmt->fetchAll();
    
    $response = ['status' => 'success', 'nodes' => []];
    
    // Proses data untuk setiap node
    foreach ($rawData as $row) {
        $nodeId = $row['node_id'];
        $vibrationLevel = $row['vibration'] ?? 0;
        $mpuLevel = $row['mpu6050'] ?? 0;
        [$status, $statusClass] = computeStatus((float)$vibrationLevel, (float)$mpuLevel);
        
        $response['nodes'][$nodeId] = [
            'node_id' => $nodeId,
            'timestamp' => $row['timestamp'],
            'temperature' => $row['temperature'] ?? null,
            'humidity' => $row['humidity'] ?? null,
            'pressure' => $row['pressure'] ?? null,
            'vibration' => (float)$vibrationLevel,
            'mpu6050' => round($mpuLevel, 2),
            'latitude' => $row['latitude'] ?? null,
            'longitude' => $row['longitude'] ?? null,
            'status' => $status,
            'status_class' => $statusClass,
            'battery' => $row['battery'] ?? null
        ];
    }
    
    // Pastikan ada data untuk semua node (1-4)
    for ($i = 1; $i <= 4; $i++) {
        if (!isset($response['nodes'][$i])) {
            $response['nodes'][$i] = [
                'node_id' => $i,
                'timestamp' => null,
                'temperature' => null,
                'humidity' => null,
                'pressure' => null,
                'vibration' => 0,
                'mpu6050' => 0,
                'latitude' => null,
                'longitude' => null,
                'status' => 'NORMAL',
                'status_class' => 'status-normal',
                'battery' => null
            ];
        }
    }
    
    echo json_encode($response);
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}