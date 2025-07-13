<?php
require_once 'includes/config.php';

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
    echo "Koneksi database BERHASIL!";
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}

// Get latest data from database
$db = get_db_connection();
$latestData = $db->query("
    SELECT * FROM sensor_data 
    ORDER BY timestamp DESC 
    LIMIT 1
")->fetch() ?? [];

$pageTitle = "Tsunami Monitoring Dashboard";
$activePage = "monitoring";

include 'includes/navbar.php';
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    
    <!-- Favicon -->
    <link rel="icon" href="<?= ASSETS_PATH ?>/img/favicon.ico">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/monitoring.css">
    

</head>
<body>
    <!-- Alert Banner (Fixed at Top) -->
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

    <!-- Navbar is included from navbar.php -->
    
    <!-- Main Content -->
    <main class="container py-4">
        <!-- Dashboard Header -->
        <div class="text-center mb-5">
            <h1 class="dashboard-title display-4 mb-2">TSUNAMI EARLY DETECTION SYSTEM</h1>
            <p class="lead text-muted">Real-time Coastal Monitoring Platform</p>
            <div class="d-flex justify-content-center align-items-center gap-3 mt-3">
                <span class="status-badge bg-secondary" id="connection-status">
                    <i class="fas fa-circle-notch fa-spin me-1"></i> Connecting...
                </span>
                <span class="status-badge bg-secondary" id="system-status">
                    <i class="fas fa-circle-notch fa-spin me-1"></i> Initializing...
                </span>
                <span class="status-badge bg-light text-dark" id="last-update">
                    <i class="fas fa-clock me-1"></i> <?= date('Y-m-d H:i:s') ?>
                </span>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card sensor-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">System Status</h5>
                        <div id="status-umum" class="status-badge bg-success mb-2">NORMAL</div>
                        <p class="card-text small text-muted">Overall system condition based on all sensors</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card sensor-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-satellite-dish fa-3x text-success mb-3"></i>
                        <h5 class="card-title">Active Sensors</h5>
                        <h2 class="mb-2"><span id="jumlah-sensor">3</span>/3</h2>
                        <p class="card-text small text-muted">Sensors currently online and reporting data</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card sensor-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                        <h5 class="card-title">Last Data Update</h5>
                        <h4 class="mb-2"><span id="waktu-update"><?= $latestData['timestamp'] ?? '-' ?></span></h4>
                        <p class="card-text small text-muted" id="last-analysis">Analyzing sensor data...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vibration and Environmental Data -->
        <div class="row g-4 mb-4">
            <!-- Vibration Monitoring -->
            <div class="col-lg-6">
                <div class="card sensor-card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-wave-square me-2"></i>Vibration Monitoring</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="text-center position-relative">
                                    <div id="vibration-gauge" class="gauge-container"></div>
                                    <div class="gauge-value">
                                        <span id="vibration-value">0</span>
                                        <small class="text-muted">/ 10</small>
                                    </div>
                                    <p class="mb-0 text-muted">Current vibration level</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="chart-container">
                                    <canvas id="vibration-chart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Environmental Data -->
            <div class="col-lg-6">
                <div class="card sensor-card h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-leaf me-2"></i>Environmental Data</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center g-2 mb-3">
                            <div class="col-md-4">
                                <div class="p-3 rounded bg-light h-100">
                                    <i class="fas fa-temperature-high fa-2x text-danger mb-2"></i>
                                    <h4><span id="bme-temp"><?= $latestData['temperature'] ?? '--' ?></span>°C</h4>
                                    <p class="mb-0 small text-muted">Temperature</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded bg-light h-100">
                                    <i class="fas fa-tint fa-2x text-info mb-2"></i>
                                    <h4><span id="bme-hum"><?= $latestData['humidity'] ?? '--' ?></span>%</h4>
                                    <p class="mb-0 small text-muted">Humidity</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded bg-light h-100">
                                    <i class="fas fa-tachometer-alt fa-2x text-warning mb-2"></i>
                                    <h4><span id="bme-pres"><?= $latestData['pressure'] ?? '--' ?></span> hPa</h4>
                                    <p class="mb-0 small text-muted">Pressure</p>
                                </div>
                            </div>
                        </div>
                        <div class="chart-container mt-2">
                            <canvas id="bme-chart" height="150"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sensor Details -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card sensor-card h-100">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-ruler-combined me-2"></i>Fine Vibration Sensor (MPU6050)</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="mb-0"><span id="mpu-value"><?= $latestData['mpu6050'] ?? '0' ?></span></h2>
                            <span class="status-badge bg-success" id="mpu-status">Normal</span>
                        </div>
                        <div class="progress progress-thin mb-3">
                            <div id="mpu-progress" class="progress-bar bg-danger" style="width: 0%"></div>
                        </div>
                        <p class="small mb-1">Status: <span id="mpu-status-text">Normal operation</span></p>
                        <p class="small mb-0 text-end text-muted"><i class="fas fa-clock me-1"></i> <span id="mpu-timestamp"><?= $latestData['timestamp'] ?? '-' ?></span></p>
                        <div class="chart-container mt-3">
                            <canvas id="mpu-chart" height="150"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card sensor-card h-100">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Coarse Vibration Sensor (Piezoelectric)</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="mb-0"><span id="piezo-value"><?= $latestData['vibration'] ?? '0' ?></span></h2>
                            <span class="status-badge bg-success" id="piezo-status">Normal</span>
                        </div>
                        <div class="progress progress-thin mb-3">
                            <div id="piezo-progress" class="progress-bar bg-warning" style="width: 0%"></div>
                        </div>
                        <p class="small mb-1">Status: <span id="piezo-status-text">Normal operation</span></p>
                        <p class="small mb-0 text-end text-muted"><i class="fas fa-clock me-1"></i> <span id="piezo-timestamp"><?= $latestData['timestamp'] ?? '-' ?></span></p>
                        <div class="chart-container mt-3">
                            <canvas id="piezo-chart" height="150"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sensor Map and Details -->
        <div class="card sensor-card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-map-marked-alt me-2"></i>Sensor Network</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-8 mb-4 mb-lg-0">
                        <div id="sensor-map"></div>
                    </div>
                    <div class="col-lg-4">
                        <h5><i class="fas fa-info-circle text-info me-2"></i>Sensor Details</h5>
                        <div class="list-group mb-4">
                            <div class="list-group-item border-0 p-3">
                                <div class="d-flex align-items-center">
                                    <div class="position-relative me-3" style="width: 40px; height: 40px;">
                                        <div class="marker-pin orange"></div>
                                        <div class="marker-pin orange"></div>
                                        <i class="fas fa-bolt warning-icon"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Piezoelectric Sensor</h6>
                                        <p class="small mb-0 text-muted">Detects strong vibrations in the deep sea</p>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item border-0 p-3">
                                <div class="d-flex align-items-center">
                                    <div class="position-relative me-3" style="width: 40px; height: 40px;">
                                        <div class="marker-pin red"></div>
                                        <div class="marker-pin red"></div>
                                        <i class="fas fa-ruler-combined danger-icon"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">MPU6050 Sensor</h6>
                                        <p class="small mb-0 text-muted">Monitors subtle underwater vibrations</p>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item border-0 p-3">
                                <div class="d-flex align-items-center">
                                    <div class="position-relative me-3" style="width: 40px; height: 40px;">
                                        <div class="marker-pin green"></div>
                                        <div class="marker-pin green"></div>
                                        <i class="fas fa-leaf success-icon"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">BME280 Sensor</h6>
                                        <p class="small mb-0 text-muted">Measures atmospheric conditions</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h5><i class="fas fa-database text-info me-2"></i>Data Management</h5>
                            <div class="list-group">
                                <a href="history.php" class="list-group-item list-group-item-action border-0">
                                    <i class="fas fa-chart-line me-2"></i>View Historical Charts
                                </a>
                                <a href="export.php" class="list-group-item list-group-item-action border-0">
                                    <i class="fas fa-file-export me-2"></i>Export Data
                                </a>
                            </div>
                        </div>
                        
                        <div>
                            <h5><i class="fas fa-cog text-info me-2"></i>System Controls</h5>
                            <button id="refresh-data" class="btn btn-sm btn-outline-primary me-2">
                                <i class="fas fa-sync-alt me-1"></i> Refresh Data
                            </button>
                            <button id="test-alarm" class="btn btn-sm btn-outline-warning">
                                <i class="fas fa-bell me-1"></i> Test Alarm
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Logs -->
        <div class="card sensor-card mb-4">
            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>System Event Log</h5>
                <div>
                    <button id="refresh-logs" class="btn btn-sm btn-light">
                        <i class="fas fa-sync-alt me-1"></i> Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="event-logs" class="small">
                    <div class="text-center py-3">
                        <div class="spinner-border text-secondary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading event logs...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- External Data -->
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card sensor-card h-100">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="fas fa-globe-asia me-2"></i>Earthquake Data</h5>
                    </div>
                    <div class="card-body">
                        <div id="earthquake-data">
                            <div class="text-center py-3">
                                <div class="spinner-border text-secondary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Loading earthquake data...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card sensor-card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-cloud-sun me-2"></i>Weather Data</h5>
                    </div>
                    <div class="card-body">
                        <div id="weather-data">
                            <div class="text-center py-3">
                                <div class="spinner-border text-secondary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Loading weather data...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer is included from footer.php -->
    <?php include 'includes/footer.php'; ?>

    <!-- Audio Alert (Hidden) -->
    <audio id="alert-sound" loop>
        <source src="<?= ASSETS_PATH ?>/audio/alert.mp3" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gauge-chart@1.2.2/dist/gauge.min.js"></script>
    <script src="<?= ASSETS_PATH ?>/js/monitoring.js"></script>
</body>
</html>