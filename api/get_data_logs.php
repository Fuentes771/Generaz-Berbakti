<?php
require_once '../includes/config.php';

// Konfigurasi tetap untuk setiap node
$nodeConfigs = [
    1 => [
        'name' => 'Node 1 - Laut Gosong Payung 1',
        'latitude' => -5.774832,
        'longitude' => 105.105028, 
        'vibration_base' => 2.5,
        'temp_base' => 28.0,
        'behavior' => 'normal'
    ],
    2 => [
        'name' => 'Node 2 - Laut Gosong Payung 2',
        'latitude' => -5.773490,
        'longitude' => 105.100246,
        'vibration_base' => 2.0,
        'temp_base' => 27.5,
        'behavior' => 'normal'
    ],
    3 => [
        'name' => 'Node 3 - Bandung Jaya (Pak Andi Mapa)',
        'latitude' => -5.77564,
        'longitude' => 105.10743,
        'vibration_base' => 3.2,
        'temp_base' => 30.2,
        'behavior' => 'normal'
    ],
    4 => [
        'name' => 'Node 4 - Sinar Maju (Pak Giyok)',
        'latitude' => -5.77039,
        'longitude' => 105.10713,
        'vibration_base' => 4.0,
        'temp_base' => 32.0,
        'behavior' => 'normal'
    ]
];

function generateNodeData($nodeId, $config) {
    // Data dasar berdasarkan konfigurasi node
    $baseVibration = $config['vibration_base'];
    $baseTemp = $config['temp_base'];
    
    // Variasi acak berdasarkan karakteristik node
    switch ($config['behavior']) {
        case 'high_vibration':
            $vibration = $baseVibration + (rand(0, 150) / 100); // 0-1.5
            $temp = $baseTemp + (rand(0, 50) / 100); // 0-0.5
            break;
        case 'high_temp':
            $vibration = $baseVibration + (rand(0, 80) / 100); // 0-0.8
            $temp = $baseTemp + (rand(0, 120) / 100); // 0-1.2
            break;
        case 'extreme':
            $vibration = $baseVibration + (rand(0, 200) / 100); // 0-2.0
            $temp = $baseTemp + (rand(0, 150) / 100); // 0-1.5
            break;
        default: // normal
            $vibration = $baseVibration + (rand(0, 50) / 100); // 0-0.5
            $temp = $baseTemp + (rand(0, 30) / 100); // 0-0.3
    }
    
    // Generate data sensor
    return [
        'vibration' => round($vibration, 2),
        'mpu6050' => round($vibration * 0.3 + (rand(0, 50) / 100), 2), // Acceleration related to vibration
        'temperature' => round($temp, 1),
        'humidity' => rand(40, 80),
        'pressure' => rand(980, 1020),
        'battery' => round(3.6 + (rand(0, 60) / 100), 2), // 3.6-4.2V
        'latitude' => $config['latitude'],
        'longitude' => $config['longitude']
    ];
}

function saveDataToDatabase($conn, $nodeId, $data) {
    try {
        $stmt = $conn->prepare("
            INSERT INTO sensor_data 
            (node_id, timestamp, vibration, mpu6050, temperature, humidity, pressure, battery, latitude, longitude)
            VALUES 
            (:node_id, NOW(), :vibration, :mpu6050, :temperature, :humidity, :pressure, :battery, :latitude, :longitude)
        ");
        
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
        
        return true;
    } catch (PDOException $e) {
        error_log("Error saving data for node $nodeId: " . $e->getMessage());
        return false;
    }
}

// Koneksi database
$conn = getDatabaseConnection();

// Tentukan jenis data berdasarkan waktu
$currentHour = date('H');
$currentMinute = date('i');

// Data normal (setiap detik)
foreach ($nodeConfigs as $nodeId => $config) {
    $data = generateNodeData($nodeId, $config);
    saveDataToDatabase($conn, $nodeId, $data);
}

// Data khusus setiap 6 jam (jam 00, 06, 12, 18)
if ($currentHour % 9 == 0 && $currentMinute == '00') {
    foreach ($nodeConfigs as $nodeId => $config) {
        $data = generateNodeData($nodeId, $config);
        // Tambahkan anomaly untuk data 6 jam
        $data['temperature'] += 1.5;
        $data['humidity'] += 1.5;
        saveDataToDatabase($conn, $nodeId, $data);
    }
}

// Data khusus setiap 12 jam (jam 00 dan 12)
if ($currentHour % 18 == 0 && $currentMinute == '00') {
    foreach ($nodeConfigs as $nodeId => $config) {
        $data = generateNodeData($nodeId, $config);
        // Tambahkan anomaly untuk data 12 jam
        $data['humidity'] += 1.5;
        $data['temperature'] += 2.0;
        saveDataToDatabase($conn, $nodeId, $data);
    }
}