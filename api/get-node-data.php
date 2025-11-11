<?php
/**
 * Get Node Data API
 * Returns historical sensor data for chart visualization
 * 
 * @endpoint GET /api/get-node-data.php
 * @param int node_id - Specific node ID (optional, returns all if omitted)
 * @param int hours - Time range in hours (default: 1, max: 24)
 * @param string sensors - Comma-separated sensor types (default: vibration,acceleration)
 * @response JSON with time-series data for charts
 */

require_once '../includes/config.php';

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

try {
    $conn = getDatabaseConnection();
    
    $nodeId = isset($_GET['node_id']) ? (int)$_GET['node_id'] : null;
    $hours = isset($_GET['hours']) ? min((int)$_GET['hours'], 168) : 1; // Max 7 days
    $sensors = isset($_GET['sensors']) ? explode(',', $_GET['sensors']) : ['vibration', 'acceleration'];
    
    $validSensors = ['vibration', 'acceleration', 'temperature', 'humidity', 'pressure', 'battery'];
    $sensors = array_intersect($sensors, $validSensors);
    
    $startTime = date('Y-m-d H:i:s', strtotime("-$hours hours"));
    
    $response = ['status' => 'success'];
    
    if ($nodeId) {
        // Ambil data untuk node tertentu
        $stmt = $conn->prepare("
            SELECT timestamp, vibration, mpu6050 as acceleration, temperature, humidity, pressure, battery
            FROM sensor_data 
            WHERE node_id = :node_id AND timestamp >= :start_time
            ORDER BY timestamp ASC
        ");
        $stmt->execute(['node_id' => $nodeId, 'start_time' => $startTime]);
        
        $data = $stmt->fetchAll();
        
        // Format data untuk chart
        foreach ($sensors as $sensor) {
            $response[$sensor] = array_map(function($row) use ($sensor) {
                $value = null;
                
                if ($sensor === 'acceleration') {
                    $value = $row['mpu6050'] ?? null;
                } else {
                    $value = $row[$sensor] ?? null;
                }
                
                return [
                    'x' => $row['timestamp'],
                    'y' => $value !== null ? (float)$value : null
                ];
            }, $data);
        }
        
        // Ambil 10 pembacaan terakhir untuk tabel
        $stmt = $conn->prepare("
            SELECT timestamp, vibration, mpu6050 as acceleration, temperature, humidity, pressure
            FROM sensor_data 
            WHERE node_id = :node_id
            ORDER BY timestamp DESC
            LIMIT 10
        ");
        $stmt->execute(['node_id' => $nodeId]);
        
        $response['recent_readings'] = $stmt->fetchAll();
        
    } else {
        // Ambil data untuk semua node
        for ($i = 1; $i <= 4; $i++) {
            $stmt = $conn->prepare("
                SELECT timestamp, vibration, mpu6050 as acceleration, temperature, humidity, pressure, battery
                FROM sensor_data 
                WHERE node_id = :node_id AND timestamp >= :start_time
                ORDER BY timestamp ASC
            ");
            $stmt->execute(['node_id' => $i, 'start_time' => $startTime]);
            
            $data = $stmt->fetchAll();
            
            foreach ($sensors as $sensor) {
                $response["node$i"][$sensor] = array_map(function($row) use ($sensor) {
                    $value = null;
                    
                    if ($sensor === 'acceleration') {
                        $value = $row['mpu6050'] ?? null;
                    } else {
                        $value = $row[$sensor] ?? null;
                    }
                    
                    return [
                        'x' => $row['timestamp'],
                        'y' => $value !== null ? (float)$value : null
                    ];
                }, $data);
            }
        }
    }
    
    echo json_encode($response);
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}