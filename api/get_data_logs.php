<?php
require __DIR__ . '/../includes/config.php';

// Konfigurasi node dengan koordinat tetap
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

// Fungsi optimasi untuk generate data
function generateNodeData($nodeId, $config, $intervalType = 'normal') {
    // Fluktuasi normal
    $fluctuation = [
        'vibration' => rand(-30, 30) / 100,
        'temp' => rand(-15, 15) / 10,
        'humidity' => rand(-10, 10)
    ];
    
    // Data dasar
    $data = [
        'vibration' => $config['vibration_base'] + $fluctuation['vibration'],
        'temperature' => $config['temp_base'] + $fluctuation['temp'],
        'humidity' => $config['humidity_base'] + $fluctuation['humidity'],
        'pressure' => rand(980, 1020),
        'battery' => round(3.6 + (rand(0, 60)) / 100, 2),
        'latitude' => $config['latitude'],
        'longitude' => $config['longitude']
    ];
    
    // Hitung accelerometer berdasarkan vibration
    $data['mpu6050'] = round($data['vibration'] * 0.35 + (rand(0, 30) / 100), 2);
    
    // Sesuaikan dengan interval khusus
    if ($intervalType === '9jam') {
        $data['temperature'] += 1.5;
        $data['humidity'] += 5;
    } elseif ($intervalType === '18jam') {
        $data['temperature'] += 2.0;
        $data['humidity'] += 8;
        $data['vibration'] *= 1.2;
    }
    
    // Batasi nilai-nilai agar tetap realistis
    $data['vibration'] = max(0, round($data['vibration'], 2));
    $data['temperature'] = round(max(15, min($data['temperature'], 45)), 1);
    $data['humidity'] = max(30, min($data['humidity'], 90));
    
    return $data;
}

// Fungsi optimasi untuk menyimpan data
function saveDataToDatabase($conn, $nodeId, $data) {
    static $stmt = null;
    
    if ($stmt === null) {
        $stmt = $conn->prepare("
            INSERT INTO sensor_data 
            (node_id, timestamp, vibration, mpu6050, temperature, humidity, pressure, battery, latitude, longitude)
            VALUES 
            (:node_id, NOW(), :vibration, :mpu6050, :temperature, :humidity, :pressure, :battery, :latitude, :longitude)
        ");
    }
    
    try {
        return $stmt->execute([
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
    } catch (PDOException $e) {
        error_log("Database error for node $nodeId: " . $e->getMessage());
        return false;
    }
}

// Koneksi database dengan error handling
try {
    $conn = getDatabaseConnection();
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Sistem timestamp presisi
    $currentTime = time();
    $currentHour = date('H', $currentTime);
    $currentMinute = date('i', $currentTime);
    $currentSecond = date('s', $currentTime);
    
    // Data setiap detik (simulasi)
    // Catatan: Di Hostinger, eksekusi tiap detik harus melalui cron job terpisah
    foreach ($nodeConfigs as $nodeId => $config) {
        $data = generateNodeData($nodeId, $config);
        saveDataToDatabase($conn, $nodeId, $data);
        
        // Log untuk debugging
        file_put_contents('sensor_log.txt', 
            date('Y-m-d H:i:s') . " - Node $nodeId: " . 
            "Vib: {$data['vibration']}, Temp: {$data['temperature']}\n", 
            FILE_APPEND);
    }
    
    // Data khusus setiap 9 jam (jam 00:00, 09:00, 18:00)
    if ($currentHour % 9 == 0 && $currentMinute == '00' && $currentSecond == '00') {
        foreach ($nodeConfigs as $nodeId => $config) {
            $data = generateNodeData($nodeId, $config, '9jam');
            saveDataToDatabase($conn, $nodeId, $data);
        }
    }
    
    // Data khusus setiap 18 jam (jam 00:00, 18:00)
    if ($currentHour % 18 == 0 && $currentMinute == '00' && $currentSecond == '00') {
        foreach ($nodeConfigs as $nodeId => $config) {
            $data = generateNodeData($nodeId, $config, '18jam');
            saveDataToDatabase($conn, $nodeId, $data);
        }
    }
    
    echo json_encode(['status' => 'success', 'time' => date('Y-m-d H:i:s')]);
    
} catch (PDOException $e) {
    file_put_contents('db_errors.log', date('Y-m-d H:i:s') . " - " . $e->getMessage() . "\n", FILE_APPEND);
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}