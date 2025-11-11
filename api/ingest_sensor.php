<?php
// Real-time sensor ingestion endpoint
// Accepts JSON (preferred) or form-urlencoded POST from sensor gateway

declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');
header('Cache-Control: no-store');

// Only allow POST
if (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    exit;
}

// API key check (Header: X-API-Key or query param api_key)
$providedKey = $_SERVER['HTTP_X_API_KEY'] ?? ($_GET['api_key'] ?? '');
if (!defined('RECEIVER_API_KEY') || !hash_equals((string)RECEIVER_API_KEY, (string)$providedKey)) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// Read raw body and try JSON first
$raw = file_get_contents('php://input') ?: '';
$payload = [];
if ($raw !== '') {
    $decoded = json_decode($raw, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $payload = $decoded;
    }
}

// Fallback to form data if not JSON
if (empty($payload)) {
    $payload = $_POST;
}

// Normalize expected fields (Arduino sample structure supported)
$nodeId = (int)($payload['nodeID'] ?? $payload['node_id'] ?? 0);
if ($nodeId <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'nodeID is required']);
    exit;
}

$temperature = isset($payload['temperature']) ? (float)$payload['temperature'] : null;
$humidity    = isset($payload['humidity']) ? (float)$payload['humidity'] : null;
$pressure    = isset($payload['pressure']) ? (float)$payload['pressure'] : null;

// Acceleration vector (magnitude) from nested object or flat fields
$accelX = $payload['accelX'] ?? ($payload['acceleration']['x'] ?? null);
$accelY = $payload['accelY'] ?? ($payload['acceleration']['y'] ?? null);
$accelZ = $payload['accelZ'] ?? ($payload['acceleration']['z'] ?? null);
$accelX = isset($accelX) ? (float)$accelX : null;
$accelY = isset($accelY) ? (float)$accelY : null;
$accelZ = isset($accelZ) ? (float)$accelZ : null;

$mpu6050 = null;
if ($accelX !== null && $accelY !== null && $accelZ !== null) {
    $mpu6050 = sqrt($accelX*$accelX + $accelY*$accelY + $accelZ*$accelZ);
}

// Gyro (optional)
$gyroX = $payload['gyroX'] ?? ($payload['gyro']['x'] ?? null); $gyroX = isset($gyroX) ? (float)$gyroX : null;
$gyroY = $payload['gyroY'] ?? ($payload['gyro']['y'] ?? null); $gyroY = isset($gyroY) ? (float)$gyroY : null;
$gyroZ = $payload['gyroZ'] ?? ($payload['gyro']['z'] ?? null); $gyroZ = isset($gyroZ) ? (float)$gyroZ : null;

// Piezo -> compute vibration as average
$p1 = $payload['piezo1'] ?? ($payload['piezo']['p1'] ?? null); $p1 = isset($p1) ? (int)$p1 : null;
$p2 = $payload['piezo2'] ?? ($payload['piezo']['p2'] ?? null); $p2 = isset($p2) ? (int)$p2 : null;
$p3 = $payload['piezo3'] ?? ($payload['piezo']['p3'] ?? null); $p3 = isset($p3) ? (int)$p3 : null;

$vibration = null;
$piezoVals = array_values(array_filter([$p1, $p2, $p3], fn($v) => $v !== null));
if (!empty($piezoVals)) {
    $vibration = (int)round(array_sum($piezoVals) / count($piezoVals));
}

$latitude  = isset($payload['latitude']) ? (float)$payload['latitude'] : null;
$longitude = isset($payload['longitude']) ? (float)$payload['longitude'] : null;

// Basic sanity clamping (lenient, avoid rejecting real data)
if ($temperature !== null) { $temperature = max(-40.0, min(85.0, $temperature)); }
if ($humidity !== null)    { $humidity    = max(0.0,   min(100.0, $humidity)); }
if ($pressure !== null)    { $pressure    = max(300.0, min(1100.0, $pressure)); }

try {
    $db = getDatabaseConnection();

    $sql = "INSERT INTO sensor_data (
                node_id, timestamp, vibration, mpu6050, temperature, humidity, pressure, latitude, longitude,
                accel_x, accel_y, accel_z, gyro_x, gyro_y, gyro_z, piezo_1, piezo_2, piezo_3
            ) VALUES (
                :node_id, NOW(), :vibration, :mpu6050, :temperature, :humidity, :pressure, :latitude, :longitude,
                :accel_x, :accel_y, :accel_z, :gyro_x, :gyro_y, :gyro_z, :piezo_1, :piezo_2, :piezo_3
            )";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        'node_id'    => $nodeId,
        'vibration'  => $vibration,
        'mpu6050'    => $mpu6050,
        'temperature'=> $temperature,
        'humidity'   => $humidity,
        'pressure'   => $pressure,
        'latitude'   => $latitude,
        'longitude'  => $longitude,
        'accel_x'    => $accelX,
        'accel_y'    => $accelY,
        'accel_z'    => $accelZ,
        'gyro_x'     => $gyroX,
        'gyro_y'     => $gyroY,
        'gyro_z'     => $gyroZ,
        'piezo_1'    => $p1,
        'piezo_2'    => $p2,
        'piezo_3'    => $p3,
    ]);

    // Provide computed status snapshot for immediate feedback
    [$status, $statusClass] = computeStatus((float)($vibration ?? 0), (float)($mpu6050 ?? 0));
    echo json_encode([
        'status' => 'success',
        'node_id' => $nodeId,
        'inserted_at' => date('Y-m-d H:i:s'),
        'reading_status' => $status,
        'reading_status_class' => $statusClass
    ]);
} catch (Throwable $e) {
    error_log('Ingest error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}
