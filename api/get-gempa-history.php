<?php
/**
 * API untuk mengambil history gempa Lampung dari database
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    $db = getDatabaseConnection();
    if (!$db) {
        throw new Exception('Database connection failed');
    }

    // Parameter filter
    $limit = isset($_GET['limit']) ? min(intval($_GET['limit']), 1000) : 100;
    $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
    $minMagnitude = isset($_GET['min_magnitude']) ? floatval($_GET['min_magnitude']) : 0;
    $lampungOnly = isset($_GET['lampung_only']) && $_GET['lampung_only'] === 'true';
    $startDate = $_GET['start_date'] ?? null;
    $endDate = $_GET['end_date'] ?? null;

    // Build query
    $where = ['1=1'];
    $params = [];

    if ($minMagnitude > 0) {
        $where[] = 'magnitude >= :min_magnitude';
        $params[':min_magnitude'] = $minMagnitude;
    }

    if ($lampungOnly) {
        $where[] = '(is_lampung = 1 OR is_nearby = 1)';
    }

    if ($startDate) {
        $where[] = 'datetime >= :start_date';
        $params[':start_date'] = $startDate . ' 00:00:00';
    }

    if ($endDate) {
        $where[] = 'datetime <= :end_date';
        $params[':end_date'] = $endDate . ' 23:59:59';
    }

    $whereClause = implode(' AND ', $where);

    // Count total
    $countSql = "SELECT COUNT(*) as total FROM gempa_lampung_history WHERE $whereClause";
    $countStmt = $db->prepare($countSql);
    $countStmt->execute($params);
    $total = $countStmt->fetch()['total'];

    // Get data
    $sql = "SELECT 
                id, tanggal, jam, datetime, coordinates, lintang, bujur,
                magnitude, kedalaman, wilayah, potensi, shakemap,
                is_lampung, is_nearby, distance_from_lampung,
                created_at
            FROM gempa_lampung_history 
            WHERE $whereClause
            ORDER BY datetime DESC 
            LIMIT :limit OFFSET :offset";

    $stmt = $db->prepare($sql);
    
    // Bind parameters
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    $stmt->execute();
    $data = $stmt->fetchAll();

    // Get statistics
    $statsSql = "SELECT 
                    COUNT(*) as total_count,
                    MAX(magnitude) as max_magnitude,
                    AVG(magnitude) as avg_magnitude,
                    MIN(datetime) as earliest,
                    MAX(datetime) as latest,
                    SUM(is_lampung) as lampung_count,
                    SUM(is_nearby) as nearby_count
                 FROM gempa_lampung_history 
                 WHERE $whereClause";
    
    $statsStmt = $db->prepare($statsSql);
    $statsStmt->execute($params);
    $stats = $statsStmt->fetch();

    echo json_encode([
        'success' => true,
        'data' => $data,
        'pagination' => [
            'total' => intval($total),
            'limit' => $limit,
            'offset' => $offset,
            'pages' => ceil($total / $limit)
        ],
        'statistics' => [
            'total_earthquakes' => intval($stats['total_count']),
            'max_magnitude' => floatval($stats['max_magnitude']),
            'avg_magnitude' => round(floatval($stats['avg_magnitude']), 2),
            'lampung_earthquakes' => intval($stats['lampung_count']),
            'nearby_earthquakes' => intval($stats['nearby_count']),
            'earliest_date' => $stats['earliest'],
            'latest_date' => $stats['latest']
        ],
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
