<?php
/**
 * API untuk menyimpan data gempa Lampung ke database
 * Auto-save gempa yang terjadi di Lampung dan sekitarnya
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

function sendResponse($success, $message, $data = null, $code = 200) {
    http_response_code($code);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
    exit;
}

// Hanya terima POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Method not allowed. Use POST.', null, 405);
}

// Ambil data dari request
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    sendResponse(false, 'Invalid JSON data', null, 400);
}

// Validasi data yang diperlukan
$required = ['Tanggal', 'Jam', 'Coordinates', 'Magnitude', 'Kedalaman', 'Wilayah'];
foreach ($required as $field) {
    if (!isset($data[$field]) || empty($data[$field])) {
        sendResponse(false, "Missing required field: $field", null, 400);
    }
}

try {
    $db = getDatabaseConnection();
    if (!$db) {
        sendResponse(false, 'Database connection failed', null, 500);
    }

    // Parse koordinat
    $coords = parseCoordinates($data['Coordinates']);
    $lintang = $coords['lat'] ?? null;
    $bujur = $coords['lon'] ?? null;

    // Hitung jarak dari Lampung
    $lampungLat = -5.1099;
    $lampungLon = 105.2253;
    $distance = null;
    
    if ($lintang && $bujur) {
        $distance = calculateDistance($lampungLat, $lampungLon, $lintang, $bujur);
    }

    // Cek apakah di Lampung atau sekitarnya
    $isLampung = stripos($data['Wilayah'], 'lampung') !== false;
    $isNearby = $distance && $distance <= 300; // radius 300 km

    // Parse datetime
    $datetime = parseDateTime($data['Tanggal'], $data['Jam']);

    // Cek duplikat berdasarkan tanggal, jam, dan koordinat
    $checkSql = "SELECT id FROM gempa_lampung_history 
                 WHERE tanggal = :tanggal 
                 AND jam = :jam 
                 AND coordinates = :coordinates 
                 LIMIT 1";
    
    $checkStmt = $db->prepare($checkSql);
    $checkStmt->execute([
        ':tanggal' => $data['Tanggal'],
        ':jam' => $data['Jam'],
        ':coordinates' => $data['Coordinates']
    ]);

    if ($checkStmt->fetch()) {
        sendResponse(true, 'Data already exists in database', ['skipped' => true]);
    }

    // Insert data
    $sql = "INSERT INTO gempa_lampung_history 
            (tanggal, jam, datetime, coordinates, lintang, bujur, magnitude, 
             kedalaman, wilayah, potensi, shakemap, is_lampung, is_nearby, 
             distance_from_lampung) 
            VALUES 
            (:tanggal, :jam, :datetime, :coordinates, :lintang, :bujur, :magnitude, 
             :kedalaman, :wilayah, :potensi, :shakemap, :is_lampung, :is_nearby, 
             :distance)";

    $stmt = $db->prepare($sql);
    $result = $stmt->execute([
        ':tanggal' => $data['Tanggal'],
        ':jam' => $data['Jam'],
        ':datetime' => $datetime,
        ':coordinates' => $data['Coordinates'],
        ':lintang' => $lintang,
        ':bujur' => $bujur,
        ':magnitude' => floatval($data['Magnitude']),
        ':kedalaman' => $data['Kedalaman'],
        ':wilayah' => $data['Wilayah'],
        ':potensi' => $data['Potensi'] ?? null,
        ':shakemap' => $data['Shakemap'] ?? null,
        ':is_lampung' => $isLampung ? 1 : 0,
        ':is_nearby' => $isNearby ? 1 : 0,
        ':distance' => $distance
    ]);

    if ($result) {
        logEvent("Gempa saved: {$data['Magnitude']} SR - {$data['Wilayah']}", 'INFO');
        sendResponse(true, 'Earthquake data saved successfully', [
            'id' => $db->lastInsertId(),
            'magnitude' => $data['Magnitude'],
            'wilayah' => $data['Wilayah'],
            'is_lampung' => $isLampung,
            'distance' => $distance
        ]);
    } else {
        sendResponse(false, 'Failed to save data', null, 500);
    }

} catch (PDOException $e) {
    logEvent("Error saving gempa: " . $e->getMessage(), 'ERROR');
    sendResponse(false, 'Database error: ' . $e->getMessage(), null, 500);
} catch (Exception $e) {
    logEvent("Error: " . $e->getMessage(), 'ERROR');
    sendResponse(false, 'Error: ' . $e->getMessage(), null, 500);
}

// Helper functions
function parseCoordinates($coordsStr) {
    if (!$coordsStr) return ['lat' => null, 'lon' => null];
    
    $parts = explode('-', $coordsStr);
    if (count($parts) !== 2) return ['lat' => null, 'lon' => null];
    
    try {
        $latPart = explode(' ', trim($parts[0]));
        $lonPart = explode(' ', trim($parts[1]));
        
        $lat = floatval($latPart[0]);
        $lon = floatval($lonPart[0]);
        
        if (isset($latPart[1]) && strtoupper($latPart[1]) === 'LS') $lat *= -1;
        if (isset($lonPart[1]) && strtoupper($lonPart[1]) === 'BB') $lon *= -1;
        
        return ['lat' => $lat, 'lon' => $lon];
    } catch (Exception $e) {
        return ['lat' => null, 'lon' => null];
    }
}

function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $R = 6371; // Radius bumi dalam km
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    
    $a = sin($dLat/2) * sin($dLat/2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * 
         sin($dLon/2) * sin($dLon/2);
    
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return $R * $c;
}

function parseDateTime($tanggal, $jam) {
    try {
        // Format: "7 Nov 2025" "15:02:26 WIB"
        $jamClean = str_replace(' WIB', '', $jam);
        $dateStr = "$tanggal $jamClean";
        
        $months = [
            'Jan' => '01', 'Feb' => '02', 'Mar' => '03', 'Apr' => '04',
            'Mei' => '05', 'Jun' => '06', 'Jul' => '07', 'Agu' => '08',
            'Sep' => '09', 'Okt' => '10', 'Nov' => '11', 'Des' => '12'
        ];
        
        foreach ($months as $indo => $num) {
            $dateStr = str_replace($indo, $num, $dateStr);
        }
        
        $dt = new DateTime($dateStr);
        return $dt->format('Y-m-d H:i:s');
    } catch (Exception $e) {
        return date('Y-m-d H:i:s');
    }
}
