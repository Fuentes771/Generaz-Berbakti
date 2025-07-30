<?php
require_once 'includes/config.php';

// Konfigurasi untuk Arduino/LoRa Receiver
define('RECEIVER_API_KEY', 'arduino123');

try {
    $conn = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    error_log("Database connection error: " . $e->getMessage());
}

// Get sensor data for nodes
$nodesData = [];
for ($i = 1; $i <= 4; $i++) {
    try {
        $stmt = $conn->prepare("
            SELECT * FROM sensor_data 
            WHERE node_id = :node_id
            ORDER BY timestamp DESC 
            LIMIT 1
        ");
        $stmt->execute(['node_id' => $i]);
        $nodeData = $stmt->fetch() ?? [];
        
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
            
            $nodesData[$i] = [
                'node_id' => $i,
                'timestamp' => $nodeData['timestamp'],
                'temperature' => $nodeData['temperature'] ?? '--',
                'humidity' => $nodeData['humidity'] ?? '--',
                'pressure' => $nodeData['pressure'] ?? '--',
                'vibration' => $vibrationLevel,
                'mpu6050' => round($mpuLevel, 2),
                'latitude' => $nodeData['latitude'] ?? 0,
                'longitude' => $nodeData['longitude'] ?? 0,
                'status' => $status,
                'status_class' => $statusClass
            ];
        }
    } catch (PDOException $e) {
        error_log("Error fetching data for node $i: " . $e->getMessage());
        $nodesData[$i] = [];
    }
}

$pageTitle = "Tsunami Monitoring Dashboard";
$activePage = "monitoring";

include 'includes/navbar.php';
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Poppins:wght@300;400;500;600&family=Rajdhani:wght@500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <link href="<?= ASSETS_PATH ?>/css/monitoring.css" rel="stylesheet">
    <style>
        :root {
            --node1-color: #4e73df;
            --node2-color: #1cc88a;
            --node3-color: #f6c23e;
            --node4-color: #e74a3b;
        }
        
        .node-card.node1 { border-left: 4px solid var(--node1-color); }
        .node-card.node2 { border-left: 4px solid var(--node2-color); }
        .node-card.node3 { border-left: 4px solid var(--node3-color); }
        .node-card.node4 { border-left: 4px solid var(--node4-color); }
        
        .node-badge.node1 { background-color: var(--node1-color); }
        .node-badge.node2 { background-color: var(--node2-color); }
        .node-badge.node3 { background-color: var(--node3-color); }
        .node-badge.node4 { background-color: var(--node4-color); }
        
        .chart-container {
            position: relative;
            height: 400px;
            width: 100%;
        }
        
        .sensor-card {
            transition: all 0.3s ease;
        }
        
        .sensor-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .node-filter-btn.active {
            box-shadow: 0 0 0 3px rgba(0,0,0,0.1);
        }
        
        .sensor-selector .form-check-input:checked + .form-check-label {
            font-weight: 600;
        }
        
        .status-badge {
            letter-spacing: 0.5px;
        }
        
        .progress-thin {
            height: 5px;
        }
        
        .real-time-blink {
            animation: blinker 2s linear infinite;
        }
        
        @keyframes blinker {
            50% { opacity: 0.7; }
        }
    </style>
</head>
<body class="monitoring-body">
    <!-- Alert Banner -->
    <div id="alert-banner" class="alert alert-danger alert-banner d-none mb-0">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong id="alert-message">WARNING: System alert!</strong>
            </div>
            <div>
                <button id="silence-btn" class="btn btn-sm btn-outline-light me-2 d-none">
                    <i class="fas fa-bell-slash me-1"></i> Silence
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
            <h1 class="dashboard-title display-4 mb-2">TSUNAMI EARLY DETECTION SYSTEM</h1>
            <p class="dashboard-subtitle">Real-time Coastal Monitoring Platform</p>
            <div class="d-flex justify-content-center align-items-center gap-3 mt-3">
                <span class="status-badge status-normal">
                    <i class="fas fa-check-circle me-1"></i> Connected
                </span>
                <span class="status-badge status-normal">
                    <i class="fas fa-check-circle me-1"></i> Operational
                </span>
                <span class="status-badge bg-light text-dark real-time-blink" id="last-update">
                    <i class="fas fa-clock me-1"></i> <?= date('Y-m-d H:i:s') ?>
                </span>
            </div>
        </div>

        <!-- Node Filter Controls -->
        <div class="card mb-4">
            <div class="card-body py-2">
                <div class="d-flex flex-wrap align-items-center justify-content-between">
                    <div class="d-flex align-items-center me-3">
                        <span class="me-2"><strong>Nodes:</strong></span>
                        <div class="btn-group btn-group-sm" role="group">
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                            <button type="button" class="btn btn-outline-secondary node-filter-btn active" data-node="<?= $i ?>">
                                <span class="node-badge node<?= $i ?> badge rounded-circle me-1">&nbsp;</span>
                                Node <?= $i ?>
                            </button>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mt-2 mt-md-0">
                        <button id="toggle-all-nodes" class="btn btn-sm btn-outline-primary me-2">
                            <i class="fas fa-eye-slash me-1"></i> Toggle All
                        </button>
                        <div class="input-group input-group-sm" style="width: 200px;">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" id="node-search" class="form-control" placeholder="Search nodes...">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sensor Nodes Grid -->
        <div class="nodes-grid mb-4">
            <?php for ($i = 1; $i <= 4; $i++): 
                $nodeData = $nodesData[$i] ?? [];
                $vibrationLevel = $nodeData['vibration'] ?? 0;
                $mpuLevel = $nodeData['mpu6050'] ?? 0;
                
                // Determine status
                $status = 'NORMAL';
                $statusClass = 'status-normal';
                if ($vibrationLevel > VIBRATION_DANGER || $mpuLevel > ACCELERATION_DANGER) {
                    $status = 'DANGER';
                    $statusClass = 'status-danger';
                } elseif ($vibrationLevel > VIBRATION_WARNING || $mpuLevel > ACCELERATION_WARNING) {
                    $status = 'WARNING';
                    $statusClass = 'status-warning';
                }
            ?>
            <div class="card node-card node<?= $i ?> sensor-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-satellite-dish fa-2x me-3" style="color: var(--node<?= $i ?>-color)"></i>
                            <div>
                                <h5 class="node-id mb-0">Node <?= $i ?> - <?= sprintf('%011d', 80000000000 + $i) ?></h5>
                                <small class="text-muted">Coastal Monitoring Station</small>
                            </div>
                        </div>
                        <span class="node-badge node<?= $i ?> badge rounded-circle">&nbsp;</span>
                    </div>
                    
                    <div class="node-status mb-3">
                        <span class="status-badge <?= $statusClass ?>"><?= $status ?></span>
                        <span class="text-muted ms-2">Last update: <?= !empty($nodeData['timestamp']) ? 
                            date('H:i:s', strtotime($nodeData['timestamp'])) : 'N/A' ?></span>
                    </div>
                    
                    <div class="sensor-readings">
                        <div class="row g-2 text-center mb-3">
                            <div class="col-6">
                                <div class="p-2 rounded bg-light">
                                    <i class="fas fa-bolt text-warning mb-1"></i>
                                    <div class="fw-bold"><?= $vibrationLevel ?></div>
                                    <small class="text-muted">Vibration</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 rounded bg-light">
                                    <i class="fas fa-ruler-combined text-danger mb-1"></i>
                                    <div class="fw-bold"><?= round($mpuLevel, 2) ?> m/s²</div>
                                    <small class="text-muted">Acceleration</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-2 text-center">
                            <div class="col-4">
                                <div class="p-2 rounded bg-light">
                                    <i class="fas fa-temperature-high text-danger mb-1"></i>
                                    <div class="fw-bold"><?= $nodeData['temperature'] ?? '--' ?>°C</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 rounded bg-light">
                                    <i class="fas fa-tint text-info mb-1"></i>
                                    <div class="fw-bold"><?= $nodeData['humidity'] ?? '--' ?>%</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 rounded bg-light">
                                    <i class="fas fa-tachometer-alt text-warning mb-1"></i>
                                    <div class="fw-bold"><?= $nodeData['pressure'] ?? '--' ?> hPa</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <div class="progress progress-thin mb-2">
                            <div class="progress-bar bg-<?= $statusClass === 'status-danger' ? 'danger' : ($statusClass === 'status-warning' ? 'warning' : 'success') ?>" 
                                 style="width: <?= min(($vibrationLevel / 1000 * 100), 100) ?>%"></div>
                        </div>
                        <small class="text-muted">Vibration Level: <?= $vibrationLevel ?>/1000</small>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
        </div>

        <!-- Interactive Chart Section -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Real-time Sensor Data Visualization</h5>
                    <div class="dropdown">
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
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-9">
                        <div class="chart-container">
                            <canvas id="sensorChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-filter me-1"></i>Chart Filters</h6>
                            </div>
                            <div class="card-body p-3 sensor-selector">
                                <h6 class="mb-2">Nodes:</h6>
                                <div class="mb-3">
                                    <?php for ($i = 1; $i <= 4; $i++): ?>
                                    <div class="form-check">
                                        <input class="form-check-input node-selector" type="checkbox" value="<?= $i ?>" id="node-<?= $i ?>" checked>
                                        <label class="form-check-label" for="node-<?= $i ?>">
                                            <span class="node-badge node<?= $i ?> badge rounded-circle me-1">&nbsp;</span>
                                            Node <?= $i ?>
                                        </label>
                                    </div>
                                    <?php endfor; ?>
                                </div>
                                
                                <h6 class="mb-2">Sensors:</h6>
                                <div class="form-check">
                                    <input class="form-check-input sensor-type" type="checkbox" value="vibration" id="vibration-sensor" checked>
                                    <label class="form-check-label" for="vibration-sensor">
                                        <i class="fas fa-bolt text-warning me-1"></i> Vibration
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input sensor-type" type="checkbox" value="mpu6050" id="accel-sensor" checked>
                                    <label class="form-check-label" for="accel-sensor">
                                        <i class="fas fa-ruler-combined text-danger me-1"></i> Acceleration
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input sensor-type" type="checkbox" value="temperature" id="temp-sensor">
                                    <label class="form-check-label" for="temp-sensor">
                                        <i class="fas fa-temperature-high text-danger me-1"></i> Temperature
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input sensor-type" type="checkbox" value="humidity" id="humidity-sensor">
                                    <label class="form-check-label" for="humidity-sensor">
                                        <i class="fas fa-tint text-info me-1"></i> Humidity
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input sensor-type" type="checkbox" value="pressure" id="pressure-sensor">
                                    <label class="form-check-label" for="pressure-sensor">
                                        <i class="fas fa-tachometer-alt text-warning me-1"></i> Pressure
                                    </label>
                                </div>
                                
                                <div class="d-grid mt-3">
                                    <button id="update-chart" class="btn btn-sm btn-primary">
                                        <i class="fas fa-sync-alt me-1"></i> Update Chart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map and System Controls -->
        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-map-marked-alt me-2"></i>Sensor Network Map</h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="sensor-map" style="height: 400px;"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-cog me-2"></i>System Controls</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2 mb-4">
                            <button id="refresh-data" class="btn btn-primary">
                                <i class="fas fa-sync-alt me-2"></i> Refresh Data
                            </button>
                            <button id="test-alarm" class="btn btn-warning">
                                <i class="fas fa-bell me-2"></i> Test Alarm
                            </button>
                            <a href="history.php" class="btn btn-outline-primary">
                                <i class="fas fa-chart-line me-2"></i> View Historical Data
                            </a>
                            <a href="export.php" class="btn btn-outline-success">
                                <i class="fas fa-file-export me-2"></i> Export Data
                            </a>
                        </div>
                        
                        <h5 class="mb-3"><i class="fas fa-clipboard-list me-2"></i>Recent Events</h5>
                        <div id="event-logs" class="small" style="max-height: 200px; overflow-y: auto;">
                            <?php
                            try {
                                $logs = $conn->query("
                                    SELECT * FROM event_logs 
                                    ORDER BY timestamp DESC 
                                    LIMIT 5
                                ")->fetchAll();
                                
                                if (empty($logs)) {
                                    echo '<div class="alert alert-info">No recent events</div>';
                                } else {
                                    echo '<div class="list-group">';
                                    foreach ($logs as $log) {
                                        $icon = 'info-circle';
                                        $color = 'text-primary';
                                        if (strpos($log['message'], 'error') !== false) {
                                            $icon = 'exclamation-triangle';
                                            $color = 'text-danger';
                                        } elseif (strpos($log['message'], 'warning') !== false) {
                                            $icon = 'exclamation-circle';
                                            $color = 'text-warning';
                                        }
                                        
                                        echo '<div class="list-group-item border-0 py-2 px-0">';
                                        echo '<div class="d-flex justify-content-between">';
                                        echo '<div><i class="fas fa-'.$icon.' me-2 '.$color.'"></i> '.htmlspecialchars($log['message']).'</div>';
                                        echo '<small class="text-muted">'.date('H:i', strtotime($log['timestamp'])).'</small>';
                                        echo '</div></div>';
                                    }
                                    echo '</div>';
                                }
                            } catch (PDOException $e) {
                                echo '<div class="alert alert-danger">Failed to load event logs</div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <!-- Audio Alert -->
    <audio id="alert-sound" loop>
        <source src="<?= ASSETS_PATH ?>/audio/alert.mp3" type="audio/mpeg">
    </audio>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@1.0.1/dist/chartjs-adapter-moment.min.js"></script>
    <script>
    $(document).ready(function() {
        // Initialize map
        const map = L.map('sensor-map').setView([-6.2088, 106.8456], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Add markers for each node
        const nodeMarkers = {};
        <?php for ($i = 1; $i <= 4; $i++): 
            $nodeData = $nodesData[$i] ?? [];
            if (!empty($nodeData['latitude']) && !empty($nodeData['longitude'])):
        ?>
            nodeMarkers[<?= $i ?>] = L.marker([<?= $nodeData['latitude'] ?>, <?= $nodeData['longitude'] ?>])
                .addTo(map)
                .bindPopup('Node <?= $i ?>');
        <?php endif; endfor; ?>

        // Initialize chart
        const ctx = document.getElementById('sensorChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                datasets: []
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'minute'
                        },
                        title: {
                            display: true,
                            text: 'Time'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Sensor Values'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    tooltip: {
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
                                        label += 'hPa';
                                    } else if (context.dataset.label.includes('Accel')) {
                                        label += ' m/s²';
                                    }
                                }
                                return label;
                            }
                        }
                    },
                    legend: {
                        onClick: function(e, legendItem, legend) {
                            const index = legendItem.datasetIndex;
                            const ci = legend.chart;
                            const meta = ci.getDatasetMeta(index);

                            // See controller.isDatasetVisible comment
                            meta.hidden = meta.hidden === null ? !ci.data.datasets[index].hidden : null;

                            // We hid a dataset ... rerender the chart
                            ci.update();
                        }
                    }
                }
            }
        });

        // Function to load chart data
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
                alert('Please select at least one node and one sensor type');
                return;
            }

            $.ajax({
                url: 'api/get-latest-data.php',
                method: 'POST',
                data: {
                    nodes: selectedNodes,
                    sensors: selectedSensors,
                    hours: hours
                },
                dataType: 'json',
                success: function(data) {
                    updateChart(data);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading chart data:', error);
                }
            });
        }

        // Function to update chart with new data
        function updateChart(data) {
            // Clear existing datasets
            chart.data.datasets = [];
            
            // Add new datasets
            data.forEach(nodeData => {
                const nodeColor = getNodeColor(nodeData.node_id);
                
                if (nodeData.vibration && nodeData.vibration.length > 0) {
                    chart.data.datasets.push({
                        label: `Node ${nodeData.node_id} Vibration`,
                        data: nodeData.vibration,
                        borderColor: nodeColor,
                        backgroundColor: nodeColor,
                        borderWidth: 2,
                        tension: 0.1,
                        pointRadius: 0,
                        hidden: !$('#vibration-sensor').is(':checked')
                    });
                }
                
                if (nodeData.mpu6050 && nodeData.mpu6050.length > 0) {
                    chart.data.datasets.push({
                        label: `Node ${nodeData.node_id} Acceleration`,
                        data: nodeData.mpu6050,
                        borderColor: nodeColor,
                        backgroundColor: nodeColor,
                        borderWidth: 2,
                        borderDash: [5, 5],
                        tension: 0.1,
                        pointRadius: 0,
                        hidden: !$('#accel-sensor').is(':checked')
                    });
                }
                
                if (nodeData.temperature && nodeData.temperature.length > 0) {
                    chart.data.datasets.push({
                        label: `Node ${nodeData.node_id} Temperature`,
                        data: nodeData.temperature,
                        borderColor: nodeColor,
                        backgroundColor: nodeColor,
                        borderWidth: 2,
                        tension: 0.1,
                        pointRadius: 0,
                        hidden: !$('#temp-sensor').is(':checked')
                    });
                }
                
                if (nodeData.humidity && nodeData.humidity.length > 0) {
                    chart.data.datasets.push({
                        label: `Node ${nodeData.node_id} Humidity`,
                        data: nodeData.humidity,
                        borderColor: nodeColor,
                        backgroundColor: nodeColor,
                        borderWidth: 2,
                        tension: 0.1,
                        pointRadius: 0,
                        hidden: !$('#humidity-sensor').is(':checked')
                    });
                }
                
                if (nodeData.pressure && nodeData.pressure.length > 0) {
                    chart.data.datasets.push({
                        label: `Node ${nodeData.node_id} Pressure`,
                        data: nodeData.pressure,
                        borderColor: nodeColor,
                        backgroundColor: nodeColor,
                        borderWidth: 2,
                        tension: 0.1,
                        pointRadius: 0,
                        hidden: !$('#pressure-sensor').is(':checked')
                    });
                }
            });
            
            chart.update();
        }

        // Get node color
        function getNodeColor(nodeId) {
            const colors = {
                1: '#4e73df',
                2: '#1cc88a',
                3: '#f6c23e',
                4: '#e74a3b'
            };
            return colors[nodeId] || '#666';
        }

        // Time range selector
        let currentHours = 1;
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
        });

        // Node search
        $('#node-search').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            $('.node-card').each(function() {
                const nodeText = $(this).text().toLowerCase();
                $(this).toggle(nodeText.includes(searchTerm));
            });
        });

        // Initial chart load
        loadChartData();

        // Auto-refresh data every 5 seconds
        setInterval(fetchLatestData, 5000);

        // Manual refresh button
        $('#refresh-data').click(fetchLatestData);

        // Test alarm button
        $('#test-alarm').click(function() {
            triggerAlert('Test Alarm', 'This is a test of the tsunami warning system', 'warning');
        });

        // Function to fetch latest data
        function fetchLatestData() {
            $.get('api/get-latest-data.php', function(data) {
                if (data.status === 'success') {
                    // Update timestamp
                    $('#last-update').html('<i class="fas fa-clock me-1"></i> ' + new Date().toLocaleString());
                    
                    // Update each node's data
                    for (let nodeId = 1; nodeId <= 4; nodeId++) {
                        const nodeData = data.nodes[nodeId] || {};
                        
                        // Update sensor readings
                        $(`.node${nodeId} .node-status .status-badge`)
                            .text(nodeData.status || 'N/A')
                            .removeClass('status-normal status-warning status-danger')
                            .addClass(nodeData.status_class || 'status-normal');
                        
                        $(`.node${nodeId} .sensor-readings .fw-bold`).eq(0).text(nodeData.vibration || '--');
                        $(`.node${nodeId} .sensor-readings .fw-bold`).eq(1).text((nodeData.mpu6050 || '--') + (nodeData.mpu6050 ? ' m/s²' : ''));
                        $(`.node${nodeId} .sensor-readings .fw-bold`).eq(2).text(nodeData.temperature || '--');
                        $(`.node${nodeId} .sensor-readings .fw-bold`).eq(3).text(nodeData.humidity || '--');
                        $(`.node${nodeId} .sensor-readings .fw-bold`).eq(4).text(nodeData.pressure || '--');
                        
                        // Update progress bar
                        const statusClass = nodeData.status_class || 'status-normal';
                        $(`.node${nodeId} .progress-bar`)
                            .css('width', Math.min(((nodeData.vibration || 0) / 1000 * 100), 100) + '%')
                            .removeClass('bg-danger bg-warning bg-success')
                            .addClass(statusClass === 'status-danger' ? 'bg-danger' : 
                                     (statusClass === 'status-warning' ? 'bg-warning' : 'bg-success'));
                        
                        // Update timestamp
                        const timeStr = nodeData.timestamp ? 
                            new Date(nodeData.timestamp).toLocaleTimeString() : 'N/A';
                        $(`.node${nodeId} .text-muted`).last().text('Last update: ' + timeStr);
                        
                        // Update map marker if position changed
                        if (nodeData.latitude && nodeData.longitude && nodeMarkers[nodeId]) {
                            const newLatLng = L.latLng(nodeData.latitude, nodeData.longitude);
                            nodeMarkers[nodeId].setLatLng(newLatLng);
                        }
                        
                        // Trigger alert if status changed to warning/danger
                        if (nodeData.status === 'DANGER' || nodeData.status === 'WARNING') {
                            triggerAlert(`Node ${nodeId} Alert`, 
                                `Node ${nodeId} detected ${nodeData.status === 'DANGER' ? 'dangerous' : 'warning'} conditions`, 
                                nodeData.status.toLowerCase());
                        }
                    }
                    
                    // Update event logs
                    if (data.logs && data.logs.length > 0) {
                        let logsHtml = '<div class="list-group">';
                        data.logs.forEach(log => {
                            let icon = 'info-circle';
                            let color = 'text-primary';
                            if (log.event_type === 'danger') {
                                icon = 'exclamation-triangle';
                                color = 'text-danger';
                            } else if (log.event_type === 'warning') {
                                icon = 'exclamation-circle';
                                color = 'text-warning';
                            }
                            
                            logsHtml += `
                                <div class="list-group-item border-0 py-2 px-0">
                                    <div class="d-flex justify-content-between">
                                        <div><i class="fas fa-${icon} me-2 ${color}"></i> ${escapeHtml(log.message)}</div>
                                        <small class="text-muted">${new Date(log.timestamp).toLocaleTimeString()}</small>
                                    </div>
                                </div>`;
                        });
                        logsHtml += '</div>';
                        $('#event-logs').html(logsHtml);
                    }
                }
            }).fail(function() {
                console.error('Failed to fetch latest data');
            });
        }

        // Function to trigger alert
        function triggerAlert(title, message, type) {
            const alertBanner = $('#alert-banner');
            const alertSound = document.getElementById('alert-sound');
            
            alertBanner.removeClass('alert-danger alert-warning alert-success d-none').addClass('alert-' + type);
            $('#alert-message').text(title + ': ' + message);
            
            if (type === 'danger' || type === 'warning') {
                alertSound.play();
                $('#silence-btn').removeClass('d-none');
            } else {
                alertSound.pause();
                alertSound.currentTime = 0;
            }
        }

        // Silence button
        $('#silence-btn').click(function() {
            const alertSound = document.getElementById('alert-sound');
            alertSound.pause();
            alertSound.currentTime = 0;
            $(this).addClass('d-none');
        });

        // More info button
        $('#more-info-btn').click(function() {
            alert($('#alert-message').text());
        });

        // Helper function to escape HTML
        function escapeHtml(unsafe) {
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    });
    </script>
</body>
</html>