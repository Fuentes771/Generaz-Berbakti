<?php
// sensor_script_fixed.php
require_once __DIR__ . '/../includes/config.php';

// Error reporting lebih detail
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__.'/sensor_errors.log');

// Fungsi koneksi yang lebih robust
function getDBConnection() {
    static $conn = null;
    
    if ($conn === null) {
        try {
            $conn = new PDO(
                "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_TIMEOUT => 3,
                    PDO::ATTR_PERSISTENT => false,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode='STRICT_TRANS_TABLES'"
                ]
            );
        } catch (PDOException $e) {
            error_log("DB Connection Failed: " . $e->getMessage());
            return false;
        }
    }
    
    return $conn;
}

// Fungsi utama dengan transaction handling
function processSensorData($nodeConfigs) {
    $conn = getDBConnection();
    if (!$conn) return false;

    try {
        // Mulai transaction
        $conn->beginTransaction();
        
        foreach ($nodeConfigs as $nodeId => $config) {
            $data = [
                'vibration' => round($config['vibration_base'] + (rand(-30, 30)/100), 2),
                'temperature' => round($config['temp_base'] + (rand(-15, 15)/10), 1),
                'humidity' => max(30, min($config['humidity_base'] + rand(-10, 10), 90)),
                'pressure' => rand(980, 1020),
                'battery' => round(3.6 + (rand(0, 60)/100), 2),
                'latitude' => $config['latitude'],
                'longitude' => $config['longitude'],
                'mpu6050' => round(($config['vibration_base'] * 0.35) + (rand(0, 30)/100), 2)
            ];

            $stmt = $conn->prepare("INSERT INTO sensor_data 
                (node_id, timestamp, vibration, mpu6050, temperature, humidity, pressure, battery, latitude, longitude)
                VALUES 
                (:node_id, NOW(), :vibration, :mpu6050, :temperature, :humidity, :pressure, :battery, :latitude, :longitude)");
                
            $stmt->execute([
                'node_id' => $nodeId,
                'vibration' => $data['vibration'],
                'mpu6050' => $data['mpu6050'],
                'temperature' => $data['temperature'],
                'humidity' => $data['humidity'],
                'pressure' => $data['pressure'],
                'battery' => $data['battery'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude']
            ]);
        }
        
        // Commit jika semua sukses
        $conn->commit();
        return true;
        
    } catch (PDOException $e) {
        // Rollback jika ada error
        $conn->rollBack();
        error_log("Transaction Failed: " . $e->getMessage());
        return false;
    }
}

// Eksekusi utama
try {
    $nodeConfigs = [
        1 => [
        'name' => 'Node 1 - Laut Gosong Payung 1',
        'latitude' => -5.774832,
        'longitude' => 105.105028,
        'vibration_base' => 2.5,
        'temp_base' => 28.0,
        'humidity_base' => 60,
        'behavior' => 'normal'
    ],
    2 => [
        'name' => 'Node 2 - Laut Gosong Payung 2',
        'latitude' => -5.773490,
        'longitude' => 105.100246,
        'vibration_base' => 2.0,
        'temp_base' => 27.5,
        'humidity_base' => 65,
        'behavior' => 'normal'
    ],
    3 => [
        'name' => 'Node 3 - Bandung Jaya (Pak Andi Mapa)',
        'latitude' => -5.77564,
        'longitude' => 105.10743,
        'vibration_base' => 3.2,
        'temp_base' => 30.2,
        'humidity_base' => 55,
        'behavior' => 'normal'
    ],
    4 => [
        'name' => 'Node 4 - Sinar Maju (Pak Giyok)',
        'latitude' => -5.77039,
        'longitude' => 105.10713,
        'vibration_base' => 4.0,
        'temp_base' => 32.0,
        'humidity_base' => 50,
        'behavior' => 'normal'
    ]
    ];
    
    if (processSensorData($nodeConfigs)) {
        echo json_encode(['status' => 'success', 'time' => date('Y-m-d H:i:s')]);
    } else {
        throw new Exception("Failed to process sensor data");
    }
    
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'System error occurred',
        'error_code' => 'SENSOR_INSERT_01'
    ]);
    error_log("System Error: " . $e->getMessage());
}