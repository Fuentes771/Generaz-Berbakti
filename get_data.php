<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');
header('Access-Control-Allow-Origin: *');

// Timeout pendek untuk request ke ESP
set_time_limit(3);

$esp_ip = "192.168.241.203"; // Ganti dengan IP ESP

function fetchESPData() {
    global $esp_ip;
    $url = "http://$esp_ip/getAllData";
    $options = [
        'http' => [
            'timeout' => 2, // Timeout 2 detik
            'method' => 'GET'
        ]
    ];
    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    return $response ? json_decode($response, true) : false;
}

$espData = fetchESPData();

if ($espData) {
    // Simpan ke database
    try {
        $db = new PDO('mysql:host=localhost;dbname=tsunami_warning', 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Simpan data sensor
        $stmt = $db->prepare("INSERT INTO sensor_data (vibration, status, alert) VALUES (?, ?, ?)");
        $stmt->execute([$espData['vibration'], $espData['status'], $espData['alert']]);
        
        // Jika status tidak normal, buat log event
        if ($espData['status'] != 'Normal') {
            $eventType = ($espData['status'] == 'Danger') ? 'Tsunami Alert' : 'Vibration Warning';
            $stmt = $db->prepare("INSERT INTO event_logs (event_type, vibration, status) VALUES (?, ?, ?)");
            $stmt->execute([$eventType, $espData['vibration'], $espData['status']]);
        }
        
        echo json_encode($espData);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Could not fetch data from ESP']);
}
?>