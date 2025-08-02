<?php
require_once 'includes/config.php';

// Konfigurasi untuk Arduino/LoRa Receiver
define('RECEIVER_API_KEY', 'arduino123');

// Database connection with error handling
try {
    $conn = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    error_log("Database connection error: " . $e->getMessage());
    die("System temporarily unavailable. Please try again later.");
}

// Function to get node data with caching
function getNodeData($conn, $nodeId) {
    static $cache = [];
    
    if (!isset($cache[$nodeId])) {
        try {
            $stmt = $conn->prepare("
                SELECT * FROM sensor_data 
                WHERE node_id = :node_id
                ORDER BY timestamp DESC 
                LIMIT 1
            ");
            $stmt->execute(['node_id' => $nodeId]);
            $nodeData = $stmt->fetch() ?: [];
            
            if ($nodeData) {
                $vibrationLevel = $nodeData['vibration'] ?? 0;
                $mpuLevel = $nodeData['mpu6050'] ?? 0;
                
                $status = 'NORMAL';
                $statusClass = 'status-normal';
                if ($vibrationLevel > VIBRATION_DANGER || $mpuLevel > ACCELERATION_DANGER) {
                    $status = 'DANGER';
                    $statusClass = 'status-danger';
                } elseif ($vibrationLevel > VIBRATION_WARNING || $mpuLevel > ACCELERATION_WARNING) {
                    $status = 'WARNING';
                    $statusClass = 'status-warning';
                }
                
                $cache[$nodeId] = [
                    'node_id' => $nodeId,
                    'timestamp' => $nodeData['timestamp'],
                    'temperature' => $nodeData['temperature'] ?? '--',
                    'humidity' => $nodeData['humidity'] ?? '--',
                    'pressure' => $nodeData['pressure'] ?? '--',
                    'vibration' => $vibrationLevel,
                    'mpu6050' => round($mpuLevel, 2),
                    'latitude' => $nodeData['latitude'] ?? 0,
                    'longitude' => $nodeData['longitude'] ?? 0,
                    'status' => $status,
                    'status_class' => $statusClass,
                    'battery' => $nodeData['battery'] ?? '--'
                ];
            } else {
                $cache[$nodeId] = [];
            }
        } catch (PDOException $e) {
            error_log("Error fetching data for node $nodeId: " . $e->getMessage());
            $cache[$nodeId] = [];
        }
    }
    
    return $cache[$nodeId];
}

// Get all nodes data
$nodesData = [];
for ($i = 1; $i <= 4; $i++) {
    $nodesData[$i] = getNodeData($conn, $i);
}

// Check for any critical alerts
$criticalAlert = false;
foreach ($nodesData as $node) {
    if (isset($node['status']) && $node['status'] === 'DANGER') {
        $criticalAlert = true;
        break;
    }
}

$pageTitle = "Tsunami Early Warning System Dashboard";
$activePage = "monitoring";

include 'includes/navbar.php';
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="<?= $criticalAlert ? 'dark' : 'light' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="description" content="Real-time Tsunami Monitoring System Dashboard">
    <meta name="author" content="Tsunami Warning Center">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?= ASSETS_PATH ?>/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= ASSETS_PATH ?>/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= ASSETS_PATH ?>/favicon/favicon-16x16.png">
    <link rel="manifest" href="<?= ASSETS_PATH ?>/favicon/site.webmanifest">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Poppins:wght@300;400;500;600;700&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@3.0.1/dist/chartjs-plugin-annotation.min.css">
    <link href="<?= ASSETS_PATH ?>/css/monitoring.min.css" rel="stylesheet">
    
    <style>
        :root {
            --node1-color: #4e73df;
            --node2-color: #1cc88a;
            --node3-color: #f6c23e;
            --node4-color: #e74a3b;
            --danger-color: #e74a3b;
            --warning-color: #f6c23e;
            --normal-color: #1cc88a;
            --primary-bg: #f8f9fa;
            --dark-bg: #212529;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--primary-bg);
            color: #212529;
            overflow-x: hidden;
        }
        
        body[data-bs-theme="dark"] {
            background-color: var(--dark-bg);
            color: #f8f9fa;
        }
        
        .dashboard-title {
            font-family: 'Orbitron', sans-serif;
            font-weight: 700;
            letter-spacing: 1px;
            background: linear-gradient(90deg, #4e73df, #224abe);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        body[data-bs-theme="dark"] .dashboard-title {
            background: linear-gradient(90deg, #4e73df, #8ab4ff);
            -webkit-background-clip: text;
            background-clip: text;
        }
        
        .dashboard-subtitle {
            font-family: 'Rajdhani', sans-serif;
            font-weight: 600;
            letter-spacing: 1px;
            color: #6c757d;
        }
        
        body[data-bs-theme="dark"] .dashboard-subtitle {
            color: #adb5bd;
        }
        
        .node-card {
            border-radius: 10px;
            border: none;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
        }
        
        .node-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .node-card.node1 { border-left: 5px solid var(--node1-color); }
        .node-card.node2 { border-left: 5px solid var(--node2-color); }
        .node-card.node3 { border-left: 5px solid var(--node3-color); }
        .node-card.node4 { border-left: 5px solid var(--node4-color); }
        
        .node-badge {
            width: 12px;
            height: 12px;
            display: inline-block;
        }
        
        .node-badge.node1 { background-color: var(--node1-color); }
        .node-badge.node2 { background-color: var(--node2-color); }
        .node-badge.node3 { background-color: var(--node3-color); }
        .node-badge.node4 { background-color: var(--node4-color); }
        
        .status-badge {
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            padding: 0.35rem 0.65rem;
            border-radius: 50px;
            text-transform: uppercase;
        }
        
        .status-normal {
            background-color: var(--normal-color);
            color: white;
        }
        
        .status-warning {
            background-color: var(--warning-color);
            color: #212529;
        }
        
        .status-danger {
            background-color: var(--danger-color);
            color: white;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(231, 74, 59, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(231, 74, 59, 0); }
            100% { box-shadow: 0 0 0 0 rgba(231, 74, 59, 0); }
        }
        
        .chart-container {
            position: relative;
            height: 400px;
            width: 100%;
            background-color: white;
            border-radius: 8px;
            padding: 15px;
        }
        
        body[data-bs-theme="dark"] .chart-container {
            background-color: #2c3034;
        }
        
        .sensor-value {
            font-size: 1.25rem;
            font-weight: 600;
        }
        
        .sensor-unit {
            font-size: 0.75rem;
            opacity: 0.8;
        }
        
        .progress-thin {
            height: 6px;
            border-radius: 3px;
        }
        
        .real-time-blink {
            animation: blinker 2s linear infinite;
        }
        
        @keyframes blinker {
            50% { opacity: 0.7; }
        }
        
        .alert-banner {
            border-radius: 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            animation: slideDown 0.5s ease-out;
        }
        
        @keyframes slideDown {
            from { transform: translateY(-100%); }
            to { transform: translateY(0); }
        }
        
        .map-container {
            border-radius: 8px;
            overflow: hidden;
            height: 400px;
            box-shadow: var(--card-shadow);
        }
        
        .system-health-card {
            border-left: 4px solid #4e73df;
        }
        
        .event-log {
            max-height: 200px;
            overflow-y: auto;
            scrollbar-width: thin;
        }
        
        .event-log::-webkit-scrollbar {
            width: 6px;
        }
        
        .event-log::-webkit-scrollbar-thumb {
            background-color: rgba(0,0,0,0.2);
            border-radius: 3px;
        }
        
        body[data-bs-theme="dark"] .event-log::-webkit-scrollbar-thumb {
            background-color: rgba(255,255,255,0.2);
        }
        
        .node-connection {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }
        
        .connection-active {
            background-color: #1cc88a;
            box-shadow: 0 0 10px #1cc88a;
        }
        
        .connection-inactive {
            background-color: #e74a3b;
        }
        
        .battery-indicator {
            position: relative;
            width: 20px;
            height: 10px;
            border: 1px solid #6c757d;
            border-radius: 2px;
            display: inline-block;
            vertical-align: middle;
            margin-left: 5px;
        }
        
        .battery-level {
            position: absolute;
            top: 1px;
            left: 1px;
            bottom: 1px;
            border-radius: 1px;
        }
        
        .battery-level-high {
            background-color: #1cc88a;
            width: 80%;
        }
        
        .battery-level-medium {
            background-color: #f6c23e;
            width: 50%;
        }
        
        .battery-level-low {
            background-color: #e74a3b;
            width: 20%;
        }
        
        .battery-tip {
            position: absolute;
            right: -3px;
            top: 3px;
            width: 2px;
            height: 4px;
            background-color: #6c757d;
            border-radius: 0 1px 1px 0;
        }
        
        .gauge-container {
            position: relative;
            width: 100%;
            height: 120px;
            margin: 0 auto;
        }
        
        .gauge {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        
        .sensor-card {
            transition: all 0.3s ease;
            border-radius: 8px;
        }
        
        .sensor-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .node-filter-btn.active {
            box-shadow: 0 0 0 2px rgba(0,0,0,0.1);
        }
        
        .sensor-selector .form-check-input:checked + .form-check-label {
            font-weight: 600;
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            font-size: 0.6rem;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .tsunami-alert-panel {
            background: linear-gradient(135deg, #e74a3b, #f6c23e);
            color: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            display: none;
        }
        
        .tsunami-alert-panel h4 {
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .tsunami-alert-panel p {
            margin-bottom: 5px;
        }
        
        .tsunami-alert-panel .alert-time {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .node-header {
            position: relative;
        }
        
        .node-status-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 1.5rem;
        }
        
        .status-normal .node-status-icon {
            color: var(--normal-color);
        }
        
        .status-warning .node-status-icon {
            color: var(--warning-color);
        }
        
        .status-danger .node-status-icon {
            color: var(--danger-color);
            animation: pulse 2s infinite;
        }
        
        .data-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        
        @media (max-width: 768px) {
            .data-grid {
                grid-template-columns: 1fr;
            }
            
            .dashboard-title {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body class="monitoring-body">
    <!-- Tsunami Alert Panel (shown only when danger detected) -->
    <div id="tsunami-alert-panel" class="tsunami-alert-panel" style="<?= $criticalAlert ? 'display: block;' : 'display: none;' ?>">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4><i class="fas fa-exclamation-triangle me-2"></i> TSUNAMI WARNING</h4>
                    <p class="mb-1">Potential tsunami detected by coastal monitoring sensors</p>
                    <p class="alert-time mb-0"><i class="fas fa-clock me-1"></i> <?= date('Y-m-d H:i:s') ?></p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <button id="alert-details-btn" class="btn btn-light me-2">
                        <i class="fas fa-info-circle me-1"></i> Details
                    </button>
                    <button id="alert-siren-btn" class="btn btn-danger">
                        <i class="fas fa-bullhorn me-1"></i> Emergency Siren
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Banner -->
    <div id="alert-banner" class="alert alert-danger alert-banner d-none mb-0 rounded-0">
        <div class="container d-flex justify-content-between align-items-center py-2">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <div>
                    <strong id="alert-message" class="me-2">WARNING: Abnormal sensor readings detected!</strong>
                    <span id="alert-node" class="badge bg-dark me-2 d-none">Node 1</span>
                    <small id="alert-timestamp" class="text-white-50"></small>
                </div>
            </div>
            <div>
                <button id="silence-btn" class="btn btn-sm btn-outline-light me-2 d-none">
                    <i class="fas fa-bell-slash me-1"></i> Silence Alarm
                </button>
                <button id="more-info-btn" class="btn btn-sm btn-light">
                    <i class="fas fa-info-circle me-1"></i> Details
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="container py-4">
        <!-- Dashboard Header -->
        <div class="text-center mb-5">
            <h1 class="dashboard-title display-4 mb-2">TSUNAMI EARLY WARNING SYSTEM</h1>
            <p class="dashboard-subtitle">Real-time Coastal Monitoring Network</p>
            <div class="d-flex flex-wrap justify-content-center align-items-center gap-3 mt-3">
                <span class="status-badge status-normal">
                    <span class="node-connection connection-active"></span>
                    <span>Network Connected</span>
                </span>
                <span class="status-badge status-normal">
                    <i class="fas fa-satellite-dish me-1"></i> 4/4 Nodes Active
                </span>
                <span class="status-badge bg-light text-dark real-time-blink" id="last-update">
                    <i class="fas fa-clock me-1"></i> <?= date('Y-m-d H:i:s T') ?>
                </span>
            </div>
        </div>

        <!-- Node Filter Controls -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between">
                    <div class="d-flex align-items-center me-3 mb-2 mb-md-0">
                        <span class="me-2 fw-medium">Filter Nodes:</span>
                        <div class="btn-group btn-group-sm" role="group">
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                            <button type="button" class="btn btn-outline-secondary node-filter-btn active" data-node="<?= $i ?>">
                                <span class="node-badge node<?= $i ?> badge rounded-circle me-1"></span>
                                Node <?= $i ?>
                                <?php if (($nodesData[$i]['status'] ?? '') === 'DANGER'): ?>
                                <span class="notification-badge bg-danger rounded-circle text-white ms-1">!</span>
                                <?php endif; ?>
                            </button>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <button id="toggle-all-nodes" class="btn btn-sm btn-outline-primary me-2">
                            <i class="fas fa-eye-slash me-1"></i> Toggle All
                        </button>
                        <div class="input-group input-group-sm" style="width: 220px;">
                            <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                            <input type="text" id="node-search" class="form-control" placeholder="Search nodes...">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sensor Nodes Grid -->
        <div class="data-grid mb-4">
            <?php for ($i = 1; $i <= 4; $i++): 
                $nodeData = $nodesData[$i] ?? [];
                $vibrationLevel = $nodeData['vibration'] ?? 0;
                $mpuLevel = $nodeData['mpu6050'] ?? 0;
                $batteryLevel = $nodeData['battery'] ?? '--';
                
                // Determine status
                $status = 'NORMAL';
                $statusClass = 'status-normal';
                $statusIcon = 'check-circle';
                if ($vibrationLevel > VIBRATION_DANGER || $mpuLevel > ACCELERATION_DANGER) {
                    $status = 'DANGER';
                    $statusClass = 'status-danger';
                    $statusIcon = 'exclamation-triangle';
                } elseif ($vibrationLevel > VIBRATION_WARNING || $mpuLevel > ACCELERATION_WARNING) {
                    $status = 'WARNING';
                    $statusClass = 'status-warning';
                    $statusIcon = 'exclamation-circle';
                }
                
                // Determine battery level class
                $batteryClass = '';
                if ($batteryLevel !== '--') {
                    if ($batteryLevel >= 70) {
                        $batteryClass = 'battery-level-high';
                    } elseif ($batteryLevel >= 30) {
                        $batteryClass = 'battery-level-medium';
                    } else {
                        $batteryClass = 'battery-level-low';
                    }
                }
            ?>
            <div class="card node-card node<?= $i ?> h-100">
                <div class="card-body">
                    <div class="node-header mb-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-<?= $i === 1 ? 'primary' : ($i === 2 ? 'success' : ($i === 3 ? 'warning' : 'danger')) ?> bg-opacity-10 p-2 rounded me-3">
                                <i class="fas fa-satellite-dish fa-lg text-<?= $i === 1 ? 'primary' : ($i === 2 ? 'success' : ($i === 3 ? 'warning' : 'danger')) ?>"></i>
                            </div>
                            <div>
                                <h5 class="node-id mb-0">Node <?= $i ?></h5>
                                <small class="text-muted">ID: <?= sprintf('%011d', 80000000000 + $i) ?></small>
                            </div>
                        </div>
                        <i class="node-status-icon fas fa-<?= $statusIcon ?>"></i>
                    </div>
                    
                    <div class="node-status mb-3">
                        <span class="status-badge <?= $statusClass ?>"><?= $status ?></span>
                        <span class="text-muted ms-2">
                            <i class="fas fa-clock me-1"></i>
                            <?= !empty($nodeData['timestamp']) ? date('H:i:s', strtotime($nodeData['timestamp'])) : 'N/A' ?>
                        </span>
                        <?php if ($batteryLevel !== '--'): ?>
                        <span class="float-end" data-bs-toggle="tooltip" title="Battery Level: <?= $batteryLevel ?>%">
                            <span class="battery-indicator">
                                <span class="battery-level <?= $batteryClass ?>" style="width: <?= $batteryLevel ?>%"></span>
                                <span class="battery-tip"></span>
                            </span>
                            <small><?= $batteryLevel ?>%</small>
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="sensor-readings">
                        <div class="row g-3 text-center mb-3">
                            <div class="col-6">
                                <div class="sensor-card p-3 rounded bg-light bg-opacity-50">
                                    <i class="fas fa-bolt text-warning mb-2 fa-lg"></i>
                                    <div class="sensor-value"><?= $vibrationLevel ?></div>
                                    <small class="text-muted sensor-unit">Vibration Level</small>
                                    <div class="progress progress-thin mt-2">
                                        <div class="progress-bar bg-<?= $statusClass === 'status-danger' ? 'danger' : ($statusClass === 'status-warning' ? 'warning' : 'success') ?>" 
                                             style="width: <?= min(($vibrationLevel / 1000 * 100), 100) ?>%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="sensor-card p-3 rounded bg-light bg-opacity-50">
                                    <i class="fas fa-ruler-combined text-danger mb-2 fa-lg"></i>
                                    <div class="sensor-value"><?= round($mpuLevel, 2) ?> <span class="sensor-unit">m/s²</span></div>
                                    <small class="text-muted sensor-unit">Acceleration</small>
                                    <div class="progress progress-thin mt-2">
                                        <div class="progress-bar bg-<?= $statusClass === 'status-danger' ? 'danger' : ($statusClass === 'status-warning' ? 'warning' : 'success') ?>" 
                                             style="width: <?= min(($mpuLevel / 20 * 100), 100) ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-2 text-center">
                            <div class="col-4">
                                <div class="sensor-card p-2 rounded bg-light bg-opacity-50">
                                    <i class="fas fa-temperature-high text-danger mb-1"></i>
                                    <div class="sensor-value"><?= $nodeData['temperature'] ?? '--' ?> <span class="sensor-unit">°C</span></div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="sensor-card p-2 rounded bg-light bg-opacity-50">
                                    <i class="fas fa-tint text-info mb-1"></i>
                                    <div class="sensor-value"><?= $nodeData['humidity'] ?? '--' ?> <span class="sensor-unit">%</span></div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="sensor-card p-2 rounded bg-light bg-opacity-50">
                                    <i class="fas fa-tachometer-alt text-warning mb-1"></i>
                                    <div class="sensor-value"><?= $nodeData['pressure'] ?? '--' ?> <span class="sensor-unit">hPa</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3 pt-2 border-top">
                        <button class="btn btn-sm btn-outline-secondary w-100" data-node="<?= $i ?>" onclick="showNodeDetails(this)">
                            <i class="fas fa-chart-line me-1"></i> View Details
                        </button>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
        </div>

        <!-- Interactive Chart Section -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-primary text-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Real-time Sensor Data Visualization</h5>
                    <div>
                        <div class="dropdown d-inline-block me-2">
                            <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button" id="timeRangeDropdown" data-bs-toggle="dropdown">
                                <i class="fas fa-clock me-1"></i> Last 1 Hour
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item time-range" href="#" data-range="1">Last 1 Hour</a></li>
                                <li><a class="dropdown-item time-range" href="#" data-range="6">Last 6 Hours</a></li>
                                <li><a class="dropdown-item time-range" href="#" data-range="24">Last 24 Hours</a></li>
                                <li><a class="dropdown-item time-range" href="#" data-range="168">Last 7 Days</a></li>
                            </ul>
                        </div>
                        <button id="export-chart" class="btn btn-sm btn-light">
                            <i class="fas fa-download me-1"></i> Export
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-9">
                        <div class="chart-container shadow-sm">
                            <canvas id="sensorChart"></canvas>
                        </div>
                    </div>
                    <div class="col-lg-3 mt-3 mt-lg-0">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-light py-3">
                                <h6 class="mb-0"><i class="fas fa-filter me-1"></i>Chart Controls</h6>
                            </div>
                            <div class="card-body p-3 sensor-selector">
                                <h6 class="mb-3 fw-medium">Nodes:</h6>
                                <div class="mb-4">
                                    <?php for ($i = 1; $i <= 4; $i++): ?>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input node-selector" type="checkbox" role="switch" value="<?= $i ?>" id="node-<?= $i ?>" checked>
                                        <label class="form-check-label" for="node-<?= $i ?>">
                                            <span class="node-badge node<?= $i ?> badge rounded-circle me-1"></span>
                                            Node <?= $i ?>
                                        </label>
                                    </div>
                                    <?php endfor; ?>
                                </div>
                                
                                <h6 class="mb-3 fw-medium">Sensors:</h6>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input sensor-type" type="checkbox" role="switch" value="vibration" id="vibration-sensor" checked>
                                    <label class="form-check-label" for="vibration-sensor">
                                        <i class="fas fa-bolt text-warning me-1"></i> Vibration
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input sensor-type" type="checkbox" role="switch" value="mpu6050" id="accel-sensor" checked>
                                    <label class="form-check-label" for="accel-sensor">
                                        <i class="fas fa-ruler-combined text-danger me-1"></i> Acceleration
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input sensor-type" type="checkbox" role="switch" value="temperature" id="temp-sensor">
                                    <label class="form-check-label" for="temp-sensor">
                                        <i class="fas fa-temperature-high text-danger me-1"></i> Temperature
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input sensor-type" type="checkbox" role="switch" value="humidity" id="humidity-sensor">
                                    <label class="form-check-label" for="humidity-sensor">
                                        <i class="fas fa-tint text-info me-1"></i> Humidity
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input sensor-type" type="checkbox" role="switch" value="pressure" id="pressure-sensor">
                                    <label class="form-check-label" for="pressure-sensor">
                                        <i class="fas fa-tachometer-alt text-warning me-1"></i> Pressure
                                    </label>
                                </div>
                                
                                <div class="d-grid gap-2 mt-4">
                                    <button id="update-chart" class="btn btn-primary btn-sm">
                                        <i class="fas fa-sync-alt me-1"></i> Update Chart
                                    </button>
                                    <button id="reset-chart" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-undo me-1"></i> Reset View
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map and System Status Section -->
        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-info text-white py-3">
                        <h5 class="mb-0"><i class="fas fa-map-marked-alt me-2"></i>Sensor Network Map</h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="sensor-map" class="map-container"></div>
                    </div>
                    <div class="card-footer bg-light py-2 small">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-info-circle me-1"></i> Click on markers for node details</span>
                            <button id="refresh-map" class="btn btn-sm btn-outline-info">
                                <i class="fas fa-sync-alt me-1"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-secondary text-white py-3">
                        <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>System Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="system-health-card p-3 mb-4 rounded bg-light">
                            <h6 class="mb-3"><i class="fas fa-heartbeat me-2"></i>System Health</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="p-2 rounded bg-white">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success bg-opacity-10 p-2 rounded me-2">
                                                <i class="fas fa-server text-success"></i>
                                            </div>
                                            <div>
                                                <div class="fw-medium">Backend</div>
                                                <small class="text-success">Operational</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 rounded bg-white">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success bg-opacity-10 p-2 rounded me-2">
                                                <i class="fas fa-database text-success"></i>
                                            </div>
                                            <div>
                                                <div class="fw-medium">Database</div>
                                                <small class="text-success">Connected</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 rounded bg-white">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success bg-opacity-10 p-2 rounded me-2">
                                                <i class="fas fa-network-wired text-success"></i>
                                            </div>
                                            <div>
                                                <div class="fw-medium">Network</div>
                                                <small class="text-success">Stable</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 rounded bg-white">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success bg-opacity-10 p-2 rounded me-2">
                                                <i class="fas fa-cloud text-success"></i>
                                            </div>
                                            <div>
                                                <div class="fw-medium">API</div>
                                                <small class="text-success">Online</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <h6 class="mb-3"><i class="fas fa-history me-2"></i>Recent Events</h6>
                        <div id="event-logs" class="event-log small">
                            <?php
                            try {
                                $logs = $conn->query("
                                    SELECT * FROM event_logs 
                                    ORDER BY timestamp DESC 
                                    LIMIT 6
                                ")->fetchAll();
                                
                                if (empty($logs)) {
                                    echo '<div class="alert alert-info mb-0">No recent events found</div>';
                                } else {
                                    echo '<div class="list-group list-group-flush">';
                                    foreach ($logs as $log) {
                                        $icon = 'info-circle';
                                        $color = 'text-primary';
                                        $badge = '';
                                        if (strpos(strtolower($log['message']), 'error') !== false) {
                                            $icon = 'exclamation-triangle';
                                            $color = 'text-danger';
                                            $badge = '<span class="badge bg-danger float-end">Error</span>';
                                        } elseif (strpos(strtolower($log['message']), 'warning') !== false) {
                                            $icon = 'exclamation-circle';
                                            $color = 'text-warning';
                                            $badge = '<span class="badge bg-warning text-dark float-end">Warning</span>';
                                        } elseif (strpos(strtolower($log['message']), 'alert') !== false) {
                                            $icon = 'bell';
                                            $color = 'text-danger';
                                            $badge = '<span class="badge bg-danger float-end">Alert</span>';
                                        }
                                        
                                        echo '<div class="list-group-item border-0 px-0 py-2">';
                                        echo '<div class="d-flex justify-content-between mb-1">';
                                        echo '<div><i class="fas fa-'.$icon.' me-2 '.$color.'"></i> '.date('H:i', strtotime($log['timestamp'])).'</div>';
                                        echo $badge;
                                        echo '</div>';
                                        echo '<div class="text-muted small">'.htmlspecialchars($log['message']).'</div>';
                                        echo '</div>';
                                    }
                                    echo '</div>';
                                }
                            } catch (PDOException $e) {
                                echo '<div class="alert alert-danger mb-0">Failed to load event logs</div>';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="card-footer bg-light py-2 small">
                        <button id="view-all-events" class="btn btn-sm btn-outline-secondary w-100">
                            <i class="fas fa-list me-1"></i> View All Events
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <!-- Node Details Modal -->
    <div class="modal fade" id="nodeDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="nodeModalTitle">Node Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="chart-container" style="height: 250px;">
                                <canvas id="nodeVibrationChart"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="chart-container" style="height: 250px;">
                                <canvas id="nodeAccelChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <h6>Recent Readings</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Vibration</th>
                                        <th>Acceleration</th>
                                        <th>Temp (°C)</th>
                                        <th>Humidity</th>
                                    </tr>
                                </thead>
                                <tbody id="nodeReadingsTable">
                                    <!-- Filled by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">
                        <i class="fas fa-download me-1"></i> Export Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Audio Alerts -->
    <audio id="alert-sound" loop>
        <source src="<?= ASSETS_PATH ?>/audio/alert.mp3" type="audio/mpeg">
    </audio>
    <audio id="siren-sound" loop>
        <source src="<?= ASSETS_PATH ?>/audio/siren.mp3" type="audio/mpeg">
    </audio>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@1.0.1/dist/chartjs-adapter-moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@3.0.1/dist/chartjs-plugin-annotation.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1/dist/chartjs-plugin-zoom.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    
    <script>
    // Global variables
    const nodeMarkers = {};
    let sensorChart, nodeVibrationChart, nodeAccelChart;
    let currentHours = 1;
    let lastAlertNode = null;
    let alertSoundPlaying = false;
    let sirenPlaying = false;
    
    $(document).ready(function() {
        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();
        
        // Initialize map
        const map = initMap();
        
        // Initialize main chart
        sensorChart = initMainChart();
        
        // Load initial data
        loadChartData(currentHours);
        fetchLatestData();
        
        // Set up event handlers
        setupEventHandlers();
        
        // Start auto-refresh
        startAutoRefresh();
    });
    
    function initMap() {
        const map = L.map('sensor-map').setView([-6.2088, 106.8456], 11);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        // Add markers for each node
        <?php for ($i = 1; $i <= 4; $i++): 
            $nodeData = $nodesData[$i] ?? [];
            if (!empty($nodeData['latitude']) && !empty($nodeData['longitude'])):
        ?>
            nodeMarkers[<?= $i ?>] = L.marker([<?= $nodeData['latitude'] ?>, <?= $nodeData['longitude'] ?>], {
                icon: L.divIcon({
                    html: `<div class="node-marker marker-<?= $i ?>" data-node="<?= $i ?>">
                              <span class="marker-pin"></span>
                              <span class="marker-label"><?= $i ?></span>
                           </div>`,
                    className: '',
                    iconSize: [30, 42],
                    iconAnchor: [15, 42]
                })
            }).addTo(map)
            .bindPopup(`<b>Node <?= $i ?></b><br>
                        Location: <?= round($nodeData['latitude'], 4) ?>, <?= round($nodeData['longitude'], 4) ?><br>
                        Last update: <?= !empty($nodeData['timestamp']) ? date('H:i:s', strtotime($nodeData['timestamp'])) : 'N/A' ?>`);
        <?php endif; endfor; ?>
        
        return map;
    }
    
    function initMainChart() {
        const ctx = document.getElementById('sensorChart').getContext('2d');
        
        return new Chart(ctx, {
            type: 'line',
            data: { datasets: [] },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                family: 'Poppins',
                                size: 12
                            },
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        },
                        onClick: function(e, legendItem, legend) {
                            const index = legendItem.datasetIndex;
                            const ci = legend.chart;
                            const meta = ci.getDatasetMeta(index);

                            meta.hidden = meta.hidden === null ? !ci.data.datasets[index].hidden : null;
                            ci.update();
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleFont: { family: 'Poppins', size: 12 },
                        bodyFont: { family: 'Poppins', size: 12 },
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y.toFixed(2);
                                    if (context.dataset.label.includes('Temp')) {
                                        label += '°C';
                                    } else if (context.dataset.label.includes('Humidity')) {
                                        label += '%';
                                    } else if (context.dataset.label.includes('Pressure')) {
                                        label += ' hPa';
                                    } else if (context.dataset.label.includes('Accel')) {
                                        label += ' m/s²';
                                    }
                                }
                                return label;
                            }
                        }
                    },
                    zoom: {
                        zoom: {
                            wheel: { enabled: true },
                            pinch: { enabled: true },
                            mode: 'xy'
                        },
                        pan: {
                            enabled: true,
                            mode: 'xy'
                        }
                    },
                    annotation: {
                        annotations: {
                            dangerLine: {
                                type: 'line',
                                yMin: <?= ACCELERATION_DANGER ?>,
                                yMax: <?= ACCELERATION_DANGER ?>,
                                borderColor: '#e74a3b',
                                borderWidth: 1,
                                borderDash: [6, 6],
                                label: {
                                    content: 'Danger Threshold',
                                    enabled: true,
                                    position: 'left',
                                    backgroundColor: 'rgba(231, 74, 59, 0.8)',
                                    color: 'white',
                                    font: { size: 10 }
                                }
                            },
                            warningLine: {
                                type: 'line',
                                yMin: <?= ACCELERATION_WARNING ?>,
                                yMax: <?= ACCELERATION_WARNING ?>,
                                borderColor: '#f6c23e',
                                borderWidth: 1,
                                borderDash: [6, 6],
                                label: {
                                    content: 'Warning Threshold',
                                    enabled: true,
                                    position: 'left',
                                    backgroundColor: 'rgba(246, 194, 62, 0.8)',
                                    color: '#212529',
                                    font: { size: 10 }
                                }
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'minute',
                            displayFormats: {
                                minute: 'HH:mm',
                                hour: 'HH:mm',
                                day: 'MMM D'
                            }
                        },
                        title: {
                            display: true,
                            text: 'Time',
                            font: { family: 'Poppins', weight: 'bold' }
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Sensor Values',
                            font: { family: 'Poppins', weight: 'bold' }
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                elements: {
                    line: {
                        tension: 0.1
                    },
                    point: {
                        radius: 0,
                        hoverRadius: 6
                    }
                }
            },
            plugins: [ChartAnnotation, ChartZoom]
        });
    }
    
    function setupEventHandlers() {
        // Time range selector
        $('.time-range').click(function(e) {
            e.preventDefault();
            currentHours = $(this).data('range');
            $('#timeRangeDropdown').html(`<i class="fas fa-clock me-1"></i> ${$(this).text()}`);
            loadChartData(currentHours);
        });
        
        // Update chart button
        $('#update-chart').click(function() {
            loadChartData(currentHours);
        });
        
        // Reset chart view
        $('#reset-chart').click(function() {
            if (sensorChart) {
                sensorChart.resetZoom();
            }
        });
        
        // Export chart as image
        $('#export-chart').click(function() {
            if (sensorChart) {
                const canvas = document.getElementById('sensorChart');
                const link = document.createElement('a');
                link.download = `tsunami-sensor-data-${new Date().toISOString().slice(0,10)}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();
            }
        });
        
        // Node filter buttons
        $('.node-filter-btn').click(function() {
            $(this).toggleClass('active');
            const nodeId = $(this).data('node');
            $(`.node-card.node${nodeId}`).toggleClass('d-none');
        });
        
        // Toggle all nodes button
        $('#toggle-all-nodes').click(function() {
            const allActive = $('.node-filter-btn.active').length === 4;
            $('.node-filter-btn').toggleClass('active', !allActive);
            $('.node-card').toggleClass('d-none', allActive);
            $(this).html(`<i class="fas fa-eye${allActive ? '' : '-slash'} me-1"></i> ${allActive ? 'Show' : 'Hide'} All`);
        });
        
        // Node search
        $('#node-search').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            $('.node-card').each(function() {
                const nodeText = $(this).text().toLowerCase();
                $(this).toggle(nodeText.includes(searchTerm));
            });
        });
        
        // Refresh map
        $('#refresh-map').click(function() {
            const currentCenter = map.getCenter();
            const currentZoom = map.getZoom();
            map.setView(currentCenter, currentZoom, { animate: true });
        });
        
        // View all events
        $('#view-all-events').click(function() {
            // In a real app, this would navigate to a full events page
            alert('This would open a full event log page in a complete application.');
        });
        
        // Alert panel buttons
        $('#alert-details-btn').click(function() {
            if (lastAlertNode) {
                showNodeDetails(lastAlertNode);
            } else {
                alert('No specific node alert to show details for.');
            }
        });
        
        $('#alert-siren-btn').click(function() {
            const sirenSound = document.getElementById('siren-sound');
            if (sirenPlaying) {
                sirenSound.pause();
                sirenSound.currentTime = 0;
                sirenPlaying = false;
                $(this).removeClass('btn-danger').addClass('btn-light');
                $(this).html('<i class="fas fa-bullhorn me-1"></i> Emergency Siren');
            } else {
                sirenSound.play();
                sirenPlaying = true;
                $(this).removeClass('btn-light').addClass('btn-danger');
                $(this).html('<i class="fas fa-stop me-1"></i> Stop Siren');
            }
        });
        
        // Silence button
        $('#silence-btn').click(function() {
            const alertSound = document.getElementById('alert-sound');
            alertSound.pause();
            alertSound.currentTime = 0;
            alertSoundPlaying = false;
            $(this).addClass('d-none');
        });
        
        // More info button
        $('#more-info-btn').click(function() {
            if (lastAlertNode) {
                showNodeDetails(lastAlertNode);
            } else {
                alert($('#alert-message').text());
            }
        });
    }
    
    function startAutoRefresh() {
        // Initial fetch
        fetchLatestData();
        
        // Set up interval for auto-refresh (every 5 seconds)
        setInterval(fetchLatestData, 5000);
    }
    
    function loadChartData(hours = 1) {
        const selectedNodes = [];
        $('.node-selector:checked').each(function() {
            selectedNodes.push($(this).val());
        });

        const selectedSensors = [];
        $('.sensor-type:checked').each(function() {
            selectedSensors.push($(this).val());
        });

        if (selectedNodes.length === 0 || selectedSensors.length === 0) {
            showAlert('Chart Error', 'Please select at least one node and one sensor type', 'warning');
            return;
        }

        showLoading(true);
        
        $.ajax({
            url: 'api/get-latest-data.php',
            method: 'POST',
            data: {
                nodes: selectedNodes,
                sensors: selectedSensors,
                hours: hours
            },
            dataType: 'json'
        })
        .done(function(data) {
            updateChart(data);
        })
        .fail(function(xhr, status, error) {
            console.error('Error loading chart data:', error);
            showAlert('Data Error', 'Failed to load chart data. Please try again.', 'danger');
        })
        .always(function() {
            showLoading(false);
        });
    }
    
    function updateChart(data) {
        if (!sensorChart || !data) return;
        
        // Clear existing datasets
        sensorChart.data.datasets = [];
        
        // Add new datasets
        data.forEach(nodeData => {
            const nodeColor = getNodeColor(nodeData.node_id);
            
            if (nodeData.vibration && nodeData.vibration.length > 0 && $('#vibration-sensor').is(':checked')) {
                sensorChart.data.datasets.push({
                    label: `Node ${nodeData.node_id} Vibration`,
                    data: nodeData.vibration,
                    borderColor: nodeColor,
                    backgroundColor: hexToRgba(nodeColor, 0.1),
                    borderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    hidden: false
                });
            }
            
            if (nodeData.mpu6050 && nodeData.mpu6050.length > 0 && $('#accel-sensor').is(':checked')) {
                sensorChart.data.datasets.push({
                    label: `Node ${nodeData.node_id} Acceleration`,
                    data: nodeData.mpu6050,
                    borderColor: nodeColor,
                    backgroundColor: hexToRgba(nodeColor, 0.1),
                    borderWidth: 2,
                    borderDash: [5, 5],
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    hidden: false
                });
            }
            
            if (nodeData.temperature && nodeData.temperature.length > 0 && $('#temp-sensor').is(':checked')) {
                sensorChart.data.datasets.push({
                    label: `Node ${nodeData.node_id} Temperature`,
                    data: nodeData.temperature,
                    borderColor: nodeColor,
                    backgroundColor: hexToRgba(nodeColor, 0.1),
                    borderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    hidden: false
                });
            }
            
            if (nodeData.humidity && nodeData.humidity.length > 0 && $('#humidity-sensor').is(':checked')) {
                sensorChart.data.datasets.push({
                    label: `Node ${nodeData.node_id} Humidity`,
                    data: nodeData.humidity,
                    borderColor: nodeColor,
                    backgroundColor: hexToRgba(nodeColor, 0.1),
                    borderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    hidden: false
                });
            }
            
            if (nodeData.pressure && nodeData.pressure.length > 0 && $('#pressure-sensor').is(':checked')) {
                sensorChart.data.datasets.push({
                    label: `Node ${nodeData.node_id} Pressure`,
                    data: nodeData.pressure,
                    borderColor: nodeColor,
                    backgroundColor: hexToRgba(nodeColor, 0.1),
                    borderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    hidden: false
                });
            }
        });
        
        sensorChart.update();
    }
    
    function fetchLatestData() {
        $.get('api/get-latest-data.php', { latest: true })
        .done(function(data) {
            if (data.status === 'success') {
                // Update timestamp
                const now = new Date();
                $('#last-update').html(`<i class="fas fa-clock me-1"></i> ${now.toLocaleTimeString()}`);
                
                // Update each node's data
                for (let nodeId = 1; nodeId <= 4; nodeId++) {
                    const nodeData = data.nodes[nodeId] || {};
                    const nodeElement = $(`.node-card.node${nodeId}`);
                    
                    if (Object.keys(nodeData).length === 0) continue;
                    
                    // Update status
                    const statusClass = nodeData.status_class || 'status-normal';
                    const statusIcon = statusClass === 'status-danger' ? 'exclamation-triangle' : 
                                     (statusClass === 'status-warning' ? 'exclamation-circle' : 'check-circle');
                    
                    nodeElement.find('.node-status .status-badge')
                        .text(nodeData.status || 'N/A')
                        .removeClass('status-normal status-warning status-danger')
                        .addClass(statusClass);
                    
                    nodeElement.find('.node-status-icon')
                        .removeClass('fa-exclamation-triangle fa-exclamation-circle fa-check-circle')
                        .addClass(`fa-${statusIcon}`);
                    
                    // Update sensor values
                    nodeElement.find('.sensor-value').eq(0).text(nodeData.vibration || '--');
                    nodeElement.find('.sensor-value').eq(1).text(
                        nodeData.mpu6050 ? `${roundToTwo(nodeData.mpu6050)} <span class="sensor-unit">m/s²</span>` : '--'
                    );
                    nodeElement.find('.sensor-value').eq(2).text(
                        nodeData.temperature ? `${nodeData.temperature} <span class="sensor-unit">°C</span>` : '--'
                    );
                    nodeElement.find('.sensor-value').eq(3).text(
                        nodeData.humidity ? `${nodeData.humidity} <span class="sensor-unit">%</span>` : '--'
                    );
                    nodeElement.find('.sensor-value').eq(4).text(
                        nodeData.pressure ? `${nodeData.pressure} <span class="sensor-unit">hPa</span>` : '--'
                    );
                    
                    // Update progress bars
                    nodeElement.find('.progress-bar').eq(0)
                        .css('width', `${Math.min(((nodeData.vibration || 0) / 1000 * 100), 100)}%`)
                        .removeClass('bg-danger bg-warning bg-success')
                        .addClass(statusClass === 'status-danger' ? 'bg-danger' : 
                                 (statusClass === 'status-warning' ? 'bg-warning' : 'bg-success'));
                    
                    nodeElement.find('.progress-bar').eq(1)
                        .css('width', `${Math.min(((nodeData.mpu6050 || 0) / 20 * 100), 100)}%`)
                        .removeClass('bg-danger bg-warning bg-success')
                        .addClass(statusClass === 'status-danger' ? 'bg-danger' : 
                                 (statusClass === 'status-warning' ? 'bg-warning' : 'bg-success'));
                    
                    // Update timestamp
                    const timeStr = nodeData.timestamp ? 
                        new Date(nodeData.timestamp).toLocaleTimeString() : 'N/A';
                    nodeElement.find('.node-status .text-muted').html(
                        `<i class="fas fa-clock me-1"></i> ${timeStr}`
                    );
                    
                    // Update battery indicator
                    if (nodeData.battery) {
                        let batteryClass = '';
                        if (nodeData.battery >= 70) {
                            batteryClass = 'battery-level-high';
                        } else if (nodeData.battery >= 30) {
                            batteryClass = 'battery-level-medium';
                        } else {
                            batteryClass = 'battery-level-low';
                        }
                        
                        nodeElement.find('.battery-level')
                            .removeClass('battery-level-high battery-level-medium battery-level-low')
                            .addClass(batteryClass)
                            .css('width', `${nodeData.battery}%`);
                        
                        nodeElement.find('.battery-indicator + small').text(`${nodeData.battery}%`);
                    }
                    
                    // Update map marker if position changed
                    if (nodeData.latitude && nodeData.longitude && nodeMarkers[nodeId]) {
                        const newLatLng = L.latLng(nodeData.latitude, nodeData.longitude);
                        nodeMarkers[nodeId].setLatLng(newLatLng);
                        
                        // Update popup content
                        nodeMarkers[nodeId].setPopupContent(`
                            <b>Node ${nodeId}</b><br>
                            Location: ${roundToFour(nodeData.latitude)}, ${roundToFour(nodeData.longitude)}<br>
                            Status: <span class="${statusClass}">${nodeData.status}</span><br>
                            Last update: ${timeStr}
                        `);
                    }
                    
                    // Trigger alert if status changed to warning/danger
                    if (nodeData.status === 'DANGER' || nodeData.status === 'WARNING') {
                        // Only trigger if this is a new alert or the node hasn't been alerted recently
                        if (!lastAlertNode || lastAlertNode !== nodeId) {
                            triggerAlert(
                                `Node ${nodeId} Alert`, 
                                `Abnormal sensor readings detected (${nodeData.status})`,
                                nodeData.status.toLowerCase(),
                                nodeId
                            );
                            lastAlertNode = nodeId;
                        }
                    }
                }
                
                // Update event logs
                if (data.logs && data.logs.length > 0) {
                    updateEventLogs(data.logs);
                }
                
                // Show tsunami alert panel if any node is in danger
                if (data.nodes && Object.values(data.nodes).some(node => node.status === 'DANGER')) {
                    $('#tsunami-alert-panel').slideDown();
                    $('html').attr('data-bs-theme', 'dark');
                } else {
                    $('#tsunami-alert-panel').slideUp();
                    $('html').attr('data-bs-theme', 'light');
                }
            }
        })
        .fail(function() {
            console.error('Failed to fetch latest data');
            showAlert('Connection Error', 'Unable to connect to server. Trying to reconnect...', 'danger');
        });
    }
    
    function updateEventLogs(logs) {
        let logsHtml = '<div class="list-group list-group-flush">';
        
        logs.slice(0, 6).forEach(log => {
            let icon = 'info-circle';
            let color = 'text-primary';
            let badge = '';
            
            if (log.event_type === 'danger' || log.message.toLowerCase().includes('error')) {
                icon = 'exclamation-triangle';
                color = 'text-danger';
                badge = '<span class="badge bg-danger float-end">Error</span>';
            } else if (log.event_type === 'warning') {
                icon = 'exclamation-circle';
                color = 'text-warning';
                badge = '<span class="badge bg-warning text-dark float-end">Warning</span>';
            } else if (log.message.toLowerCase().includes('alert')) {
                icon = 'bell';
                color = 'text-danger';
                badge = '<span class="badge bg-danger float-end">Alert</span>';
            }
            
            const timeStr = new Date(log.timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            
            logsHtml += `
                <div class="list-group-item border-0 px-0 py-2">
                    <div class="d-flex justify-content-between mb-1">
                        <div><i class="fas fa-${icon} me-2 ${color}"></i> ${timeStr}</div>
                        ${badge}
                    </div>
                    <div class="text-muted small">${escapeHtml(log.message)}</div>
                </div>`;
        });
        
        logsHtml += '</div>';
        $('#event-logs').html(logsHtml);
    }
    
    function triggerAlert(title, message, type, nodeId = null) {
        const alertBanner = $('#alert-banner');
        const alertSound = document.getElementById('alert-sound');
        
        // Update alert banner
        alertBanner.removeClass('d-none alert-danger alert-warning alert-success')
                   .addClass(`alert-${type}`);
        
        $('#alert-message').text(title);
        $('#alert-timestamp').text(new Date().toLocaleTimeString());
        
        if (nodeId) {
            $(`#alert-node`).removeClass('d-none').text(`Node ${nodeId}`);
        } else {
            $(`#alert-node`).addClass('d-none');
        }
        
        // Play sound for danger/warning alerts
        if (type === 'danger' || type === 'warning') {
            if (!alertSoundPlaying) {
                alertSound.play();
                alertSoundPlaying = true;
                $('#silence-btn').removeClass('d-none');
            }
        } else {
            alertSound.pause();
            alertSound.currentTime = 0;
            alertSoundPlaying = false;
        }
        
        // Auto-hide normal alerts after 5 seconds
        if (type === 'success') {
            setTimeout(() => {
                alertBanner.slideUp();
            }, 5000);
        }
    }
    
    function showNodeDetails(nodeElement) {
        const nodeId = typeof nodeElement === 'object' ? $(nodeElement).data('node') : nodeElement;
        
        // Set modal title
        $('#nodeModalTitle').html(`<i class="fas fa-satellite-dish me-2" style="color: ${getNodeColor(nodeId)}"></i> Node ${nodeId} Detailed Analysis`);
        
        // Fetch detailed data for this node
        $.get('api/get-node-data.php', { node_id: nodeId })
        .done(function(data) {
            if (data.status === 'success') {
                // Update vibration chart
                initNodeChart('nodeVibrationChart', 
                    `Node ${nodeId} Vibration Levels`, 
                    data.vibration, 
                    'Vibration',
                    '#f6c23e',
                    <?= VIBRATION_WARNING ?>,
                    <?= VIBRATION_DANGER ?>
                );
                
                // Update acceleration chart
                initNodeChart('nodeAccelChart', 
                    `Node ${nodeId} Acceleration`, 
                    data.acceleration, 
                    'Acceleration (m/s²)',
                    '#e74a3b',
                    <?= ACCELERATION_WARNING ?>,
                    <?= ACCELERATION_DANGER ?>
                );
                
                // Update readings table
                let tableHtml = '';
                data.recent_readings.forEach(reading => {
                    tableHtml += `
                        <tr>
                            <td>${new Date(reading.timestamp).toLocaleTimeString()}</td>
                            <td>${reading.vibration || '--'}</td>
                            <td>${reading.mpu6050 ? roundToTwo(reading.mpu6050) : '--'}</td>
                            <td>${reading.temperature || '--'}</td>
                            <td>${reading.humidity || '--'}</td>
                        </tr>`;
                });
                $('#nodeReadingsTable').html(tableHtml);
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('nodeDetailsModal'));
                modal.show();
            } else {
                showAlert('Data Error', 'Failed to load node details', 'danger');
            }
        })
        .fail(function() {
            showAlert('Connection Error', 'Failed to connect to server', 'danger');
        });
    }
    
    function initNodeChart(canvasId, title, data, label, color, warningThreshold, dangerThreshold) {
        const ctx = document.getElementById(canvasId).getContext('2d');
        
        if (window[canvasId.replace('Chart', '')]) {
            window[canvasId.replace('Chart', '')].destroy();
        }
        
        window[canvasId.replace('Chart', '')] = new Chart(ctx, {
            type: 'line',
            data: {
                datasets: [{
                    label: label,
                    data: data,
                    borderColor: color,
                    backgroundColor: hexToRgba(color, 0.1),
                    borderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: title,
                        font: { family: 'Poppins', size: 14 }
                    },
                    legend: { display: false },
                    annotation: {
                        annotations: {
                            dangerLine: {
                                type: 'line',
                                yMin: dangerThreshold,
                                yMax: dangerThreshold,
                                borderColor: '#e74a3b',
                                borderWidth: 1,
                                borderDash: [6, 6],
                                label: {
                                    content: 'Danger Threshold',
                                    enabled: true,
                                    position: 'left',
                                    backgroundColor: 'rgba(231, 74, 59, 0.8)',
                                    color: 'white',
                                    font: { size: 10 }
                                }
                            },
                            warningLine: {
                                type: 'line',
                                yMin: warningThreshold,
                                yMax: warningThreshold,
                                borderColor: '#f6c23e',
                                borderWidth: 1,
                                borderDash: [6, 6],
                                label: {
                                    content: 'Warning Threshold',
                                    enabled: true,
                                    position: 'left',
                                    backgroundColor: 'rgba(246, 194, 62, 0.8)',
                                    color: '#212529',
                                    font: { size: 10 }
                                }
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'minute',
                            displayFormats: { minute: 'HH:mm' }
                        },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    y: {
                        title: { display: true, text: label },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    }
                },
                elements: { line: { tension: 0.1 } }
            },
            plugins: [ChartAnnotation]
        });
    }
    
    function showLoading(show) {
        if (show) {
            $('#sensorChart').after('<div class="chart-loading-overlay"><div class="spinner-border text-primary"></div></div>');
        } else {
            $('.chart-loading-overlay').remove();
        }
    }
    
    function showAlert(title, message, type) {
        // Create alert element
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <strong>${title}</strong> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        // Prepend to body (or another suitable container)
        $('main').prepend(alertHtml);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            $('.alert').alert('close');
        }, 5000);
    }
    
    // Helper functions
    function getNodeColor(nodeId) {
        const colors = {
            1: '#4e73df',
            2: '#1cc88a',
            3: '#f6c23e',
            4: '#e74a3b'
        };
        return colors[nodeId] || '#666';
    }
    
    function hexToRgba(hex, alpha) {
        const r = parseInt(hex.slice(1, 3), 16);
        const g = parseInt(hex.slice(3, 5), 16);
        const b = parseInt(hex.slice(5, 7), 16);
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }
    
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
    
    function roundToTwo(num) {
        return +(Math.round(num + "e+2")  + "e-2");
    }
    
    function roundToFour(num) {
        return +(Math.round(num + "e+4")  + "e-4");
    }
    </script>
</body>
</html>