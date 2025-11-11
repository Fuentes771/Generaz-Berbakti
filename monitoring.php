<?php
require_once 'includes/config.php';

// Ambil semua data node dari database
function getAllNodeData(): array {
    $nodesData = [];
    
    try {
        // Ambil data terbaru dari setiap node
            $conn = getDatabaseConnection();

        $stmt = $conn->prepare("
            SELECT sd.* FROM sensor_data sd
            INNER JOIN (
                SELECT node_id, MAX(timestamp) as latest_timestamp 
                FROM sensor_data 
                GROUP BY node_id
            ) latest ON sd.node_id = latest.node_id AND sd.timestamp = latest.latest_timestamp
            ORDER BY sd.node_id
        ");
        $stmt->execute();
        
        $rawData = $stmt->fetchAll();
        
        // Proses data untuk setiap node
        foreach ($rawData as $row) {
            $nodeId = $row['node_id'];
            $vibrationLevel = $row['vibration'] ?? 0;
            $mpuLevel = $row['mpu6050'] ?? 0;
            
            // Tentukan status node
            $status = 'NORMAL';
            $statusClass = 'status-normal';
            if ($vibrationLevel > VIBRATION_DANGER || $mpuLevel > ACCELERATION_DANGER) {
                $status = 'BAHAYA';
                $statusClass = 'status-danger';
            } elseif ($vibrationLevel > VIBRATION_WARNING || $mpuLevel > ACCELERATION_WARNING) {
                $status = 'PERINGATAN';
                $statusClass = 'status-warning';
            }
            
            $nodesData[$nodeId] = [
                'node_id' => $nodeId,
                'timestamp' => $row['timestamp'],
                'temperature' => $row['temperature'] ?? '--',
                'humidity' => $row['humidity'] ?? '--',
                'pressure' => $row['pressure'] ?? '--',
                'vibration' => $vibrationLevel,
                'mpu6050' => round($mpuLevel, 2),
                'latitude' => $row['latitude'] ?? 0,
                'longitude' => $row['longitude'] ?? 0,
                'status' => $status,
                'status_class' => $statusClass,
                'battery' => $row['battery'] ?? '--'
            ];
        }
        
        // Pastikan ada data untuk semua node (1-4)
        for ($i = 1; $i <= 4; $i++) {
            if (!isset($nodesData[$i])) {
                $nodesData[$i] = [
                    'node_id' => $i,
                    'timestamp' => null,
                    'temperature' => '--',
                    'humidity' => '--',
                    'pressure' => '--',
                    'vibration' => 0,
                    'mpu6050' => 0,
                    'latitude' => 0,
                    'longitude' => 0,
                    'status' => 'NORMAL',
                    'status_class' => 'status-normal',
                    'battery' => '--'
                ];
            }
        }
        
    } catch (PDOException $e) {
        error_log("Error fetching node data: " . $e->getMessage());
        // Return data kosong jika terjadi error
        for ($i = 1; $i <= 4; $i++) {
            $nodesData[$i] = [
                'node_id' => $i,
                'timestamp' => null,
                'temperature' => '--',
                'humidity' => '--',
                'pressure' => '--',
                'vibration' => 0,
                'mpu6050' => 0,
                'latitude' => 0,
                'longitude' => 0,
                'status' => 'NORMAL',
                'status_class' => 'status-normal',
                'battery' => '--'
            ];
        }
    }
    
    return $nodesData;
}

// Ambil data semua node
$nodesData = getAllNodeData();

// Cek apakah ada alert bahaya
$criticalAlert = false;
foreach ($nodesData as $node) {
    if ($node['status'] === 'BAHAYA') {
        $criticalAlert = true;
        break;
    }
}

// Siapkan data lokasi untuk peta
$nodeLocations = [];
for ($i = 1; $i <= 4; $i++) {
    $nodeData = $nodesData[$i] ?? [];
    $nodeLocations[$i] = [
        'latitude' => $nodeData['latitude'] ?? 0,
        'longitude' => $nodeData['longitude'] ?? 0,
        'status' => $nodeData['status'] ?? 'NORMAL',
        'status_class' => $nodeData['status_class'] ?? 'status-normal',
        'timestamp' => !empty($nodeData['timestamp']) ? date('H:i:s', strtotime($nodeData['timestamp'])) : 'N/A'
    ];
}

$pageTitle = "Sistem Peringatan Dini Tsunami";
$activePage = "monitoring";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="description" content="Sistem Monitoring Tsunami Real-time">
    <meta name="author" content="Pusat Peringatan Tsunami">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?= ASSETS_PATH ?>/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= ASSETS_PATH ?>/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= ASSETS_PATH ?>/favicon/favicon-16x16.png">
    <link rel="manifest" href="<?= ASSETS_PATH ?>/favicon/site.webmanifest">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Poppins:wght@300;400;500;600;700&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <link rel="stylesheet" href="assets/css/core.css">
    <link rel="stylesheet" href="assets/css/monitoring.css">
    </head>
<body class="monitoring-body" data-bs-theme="<?= $criticalAlert ? 'dark' : 'light' ?>">
<?php include 'includes/navbar.php'; ?>
    <!-- Panel Peringatan Tsunami -->
    <div id="tsunami-alert-panel" class="tsunami-alert-panel" style="<?= $criticalAlert ? 'display: block;' : 'display: none;' ?>">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4><i class="fas fa-exclamation-triangle me-2"></i> PERINGATAN TSUNAMI</h4>
                    <p class="mb-1">Sensor pantai mendeteksi potensi tsunami</p>
                    <p class="alert-time mb-0"><i class="fas fa-clock me-1"></i> <?= date('Y-m-d H:i:s') ?></p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <button id="alert-details-btn" class="btn btn-light me-2">
                        <i class="fas fa-info-circle me-1"></i> Detail
                    </button>
                    <button id="alert-siren-btn" class="btn btn-danger">
                        <i class="fas fa-bullhorn me-1"></i> Sirene Darurat
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Banner Alert -->
    <div id="alert-banner" class="alert alert-danger alert-banner d-none mb-0 rounded-0">
        <div class="container d-flex justify-content-between align-items-center py-2">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <div>
                    <strong id="alert-message" class="me-2">PERINGATAN: Pembacaan sensor tidak normal!</strong>
                    <span id="alert-node" class="badge bg-dark me-2 d-none">Node 1</span>
                    <small id="alert-timestamp" class="text-white-50"></small>
                </div>
            </div>
            <div>
                <button id="silence-btn" class="btn btn-sm btn-outline-light me-2 d-none">
                    <i class="fas fa-bell-slash me-1"></i> Matikan Alarm
                </button>
                <button id="more-info-btn" class="btn btn-sm btn-light">
                    <i class="fas fa-info-circle me-1"></i> Detail
                </button>
            </div>
        </div>
    </div>

    <!-- Konten Utama -->
    <main class="container py-4">
        <!-- Header Dashboard -->
        <div class="text-center mb-4">
            <h1 class="dashboard-title mb-2" style="font-size: 2rem;">
                <i class="fas fa-chart-line me-2" style="color: var(--primary-color);"></i>
                Monitoring Real-time
            </h1>
            <p class="dashboard-subtitle mb-3">Sistem Peringatan Dini Tsunami - Pekon Teluk Kiluan Negri</p>
            <div class="d-flex flex-wrap justify-content-center align-items-center gap-3 mt-3">
                <span class="status-badge status-normal" style="background: var(--bg-light); color: var(--success-color); border: 1px solid var(--success-color);">
                    <i class="fas fa-check-circle me-1"></i> Terhubung
                </span>
                <span class="status-badge status-normal" style="background: var(--bg-light); color: var(--primary-color); border: 1px solid var(--primary-color);">
                    <i class="fas fa-satellite-dish me-1"></i> 4/4 Node Aktif
                </span>
                <span class="status-badge real-time-blink" id="last-update" style="background: white; color: var(--dark-color); border: 1px solid #dee2e6;">
                    <i class="fas fa-clock me-1"></i> <?= date('d/m/Y H:i:s') ?>
                </span>
            </div>
        </div>

        <!-- Kontrol Filter Node -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between">
                    <div class="d-flex align-items-center me-3 mb-2 mb-md-0">
                        <span class="me-2 fw-medium">Filter Node:</span>
                        <div class="btn-group btn-group-sm" role="group">
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                            <button type="button" class="btn btn-outline-secondary node-filter-btn active" data-node="<?= $i ?>">
                                <span class="node-badge node<?= $i ?> me-1"></span>
                                Node <?= $i ?>
                                <?php if (($nodesData[$i]['status'] ?? '') === 'BAHAYA'): ?>
                                <span class="notification-badge bg-danger rounded-circle text-white ms-1">!</span>
                                <?php endif; ?>
                            </button>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <button id="toggle-all-nodes" class="btn btn-sm btn-outline-primary me-2">
                            <i class="fas fa-eye-slash me-1"></i> Tampilkan/Sembunyikan Semua
                        </button>
                        <div class="input-group input-group-sm" style="width: 220px;">
                            <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                            <input type="text" id="node-search" class="form-control" placeholder="Cari node...">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid Node Sensor -->
        <div class="data-grid mb-4">
            <?php for ($i = 1; $i <= 4; $i++): 
                $nodeData = $nodesData[$i] ?? [];
                $vibrationLevel = $nodeData['vibration'] ?? 0;
                $mpuLevel = $nodeData['mpu6050'] ?? 0;
                
                // Tentukan status
                $status = $nodeData['status'] ?? 'NORMAL';
                $statusClass = $nodeData['status_class'] ?? 'status-normal';
                $statusIcon = ($status === 'BAHAYA') ? 'exclamation-triangle' : 
                             (($status === 'PERINGATAN') ? 'exclamation-circle' : 'check-circle');
            ?>
            <div class="card node-card node<?= $i ?> h-100">
                <div class="card-body">
                    <div class="node-header mb-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="bg-<?= $i === 1 ? 'primary' : ($i === 2 ? 'success' : ($i === 3 ? 'warning' : 'danger')) ?> bg-opacity-10 p-2 rounded me-3">
                                <i class="fas fa-satellite-dish fa-lg text-<?= $i === 1 ? 'primary' : ($i === 2 ? 'success' : ($i === 3 ? 'warning' : 'danger')) ?>"></i>
                            </div>
                            <div>
                                <h5 class="node-id mb-0">Node <?= $i ?></h5>
                                <small class="text-muted">ID: <?= sprintf('%011d', 80000000000 + $i) ?></small>
                            </div>
                        </div>
                        <i class="fas fa-<?= $statusIcon ?> fa-lg <?= $statusClass ?>"></i>
                    </div>
                    
                    <div class="node-status mb-3 d-flex align-items-center">
                        <span class="status-badge <?= $statusClass ?>"><?= $status ?></span>
                        <span class="text-muted ms-2">
                            <i class="fas fa-clock me-1"></i>
                            <?= !empty($nodeData['timestamp']) ? date('H:i:s', strtotime($nodeData['timestamp'])) : 'N/A' ?>
                        </span>
                    </div>
                    
                    <div class="sensor-readings">
                        <div class="row g-3 text-center mb-3">
                            <div class="col-6">
                                <div class="sensor-card p-3 rounded bg-light bg-opacity-50">
                                    <i class="fas fa-bolt text-warning mb-2 fa-lg"></i>
                                    <div class="sensor-value"><?= $vibrationLevel ?></div>
                                    <small class="text-muted sensor-unit">Level Getaran</small>
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
                                    <small class="text-muted sensor-unit">Akselerasi</small>
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
                            <i class="fas fa-chart-line me-1"></i> Lihat Detail
                        </button>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
        </div>

        <!-- Bagian Grafik Interaktif -->
        <div class="card mb-4 border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
            <div class="card-header text-white py-3" style="background: linear-gradient(135deg, var(--primary-color, #1e40af) 0%, #1e3a8a 100%);">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Visualisasi Data Sensor Real-time</h5>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-light time-filter-btn active" data-hours="1">1 Jam</button>
                        <button type="button" class="btn btn-outline-light time-filter-btn" data-hours="6">6 Jam</button>
                        <button type="button" class="btn btn-outline-light time-filter-btn" data-hours="24">24 Jam</button>
                        <button type="button" class="btn btn-outline-light sensor-filter-btn active" data-sensor="vibration">Getaran</button>
                        <button type="button" class="btn btn-outline-light sensor-filter-btn active" data-sensor="acceleration">Akselerasi</button>
                        <button type="button" class="btn btn-outline-light sensor-filter-btn" data-sensor="temperature">Suhu</button>
                        <button type="button" class="btn btn-outline-light sensor-filter-btn" data-sensor="humidity">Kelembaban</button>
                    </div>
                </div>
            </div>
            <div class="card-body" style="padding: 25px;">
                <div class="chart-container">
                    <canvas id="sensorChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Bagian Peta Jaringan Sensor -->
        <div class="card mb-4 border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
            <div class="card-header text-white py-3" style="background: linear-gradient(135deg, var(--success-color, #16a085) 0%, #0f7a65 100%);">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-map-marked-alt me-2"></i>Peta Lokasi Sistem</h5>
                    <div>
                        <button id="refresh-map" class="btn btn-sm btn-outline-light">
                            <i class="fas fa-sync-alt me-1"></i> Segarkan Peta
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="sensor-map" class="map-container map-full-height"></div>
                <!-- Panel Kontrol Peta -->
                <div class="map-control-panel">
                    <div class="btn-group-vertical btn-group-sm" role="group">
                        <button id="zoom-in-btn" class="btn btn-light" title="Zoom In">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button id="zoom-out-btn" class="btn btn-light" title="Zoom Out">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button id="center-map-btn" class="btn btn-light" title="Pusatkan Peta">
                            <i class="fas fa-crosshairs"></i>
                        </button>
                    </div>
                </div>
                <!-- Info Lokasi -->
                <div class="map-legend" style="background: white; padding: 15px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <h6 class="mb-2" style="color: var(--dark-color); font-weight: 700;">
                        <i class="fas fa-info-circle me-2"></i>Informasi Lokasi
                    </h6>
                    <div class="legend-item" style="display: flex; align-items: center; gap: 10px; padding: 8px 0;">
                        <div style="width: 30px; height: 30px; background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                            <i class="fas fa-broadcast-tower"></i>
                        </div>
                        <div>
                            <strong style="display: block; font-size: 0.9rem;">Pekon Teluk Kiluan Negri</strong>
                            <small style="color: var(--gray-color);">Sistem Peringatan Dini Tsunami</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <!-- Modal Detail Node -->
    <div class="modal fade" id="nodeDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="nodeModalTitle">Detail Node</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
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
                        <h6>Pembacaan Terakhir</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Waktu</th>
                                        <th>Getaran</th>
                                        <th>Akselerasi</th>
                                        <th>Suhu (°C)</th>
                                        <th>Kelembaban</th>
                                    </tr>
                                </thead>
                                <tbody id="nodeReadingsTable">
                                    <!-- Diisi oleh JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary">
                        <i class="fas fa-download me-1"></i> Ekspor Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Audio Alert (Optional - gracefully handled if missing) -->
    <audio id="alert-sound">
        <source src="assets/audio/alert.mp3" type="audio/mpeg">
    </audio>

    <audio id="siren-sound">
        <source src="assets/audio/siren.mp3" type="audio/mpeg">
    </audio>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@1.0.1/dist/chartjs-adapter-moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@3.0.1/dist/chartjs-plugin-annotation.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1/dist/chartjs-plugin-zoom.min.js"></script>

    <script>
    // Variabel global
    const nodeMarkers = {};
    const nodeLocations = <?= json_encode($nodeLocations) ?>;
    let sensorChart, nodeVibrationChart, nodeAccelChart;
    let currentHours = 1;
    let activeSensors = ['vibration', 'acceleration'];
    let lastAlertNode = null;
    let alertSoundPlaying = false;
    let sirenPlaying = false;
    let map;
    
    // Fungsi untuk memastikan plugin Chart terload
    function ensureChartPlugins() {
        return new Promise((resolve, reject) => {
            if (typeof Chart === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
                script.onload = () => {
                    loadChartAnnotation().then(resolve).catch(reject);
                };
                script.onerror = reject;
                document.head.appendChild(script);
            } else {
                loadChartAnnotation().then(resolve).catch(reject);
            }
        });
    }

    function loadChartAnnotation() {
        return new Promise((resolve, reject) => {
            if (typeof Chart.Annotation !== 'undefined') {
                resolve();
            } else {
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@3.0.1/dist/chartjs-plugin-annotation.min.js';
                script.onload = resolve;
                script.onerror = reject;
                document.head.appendChild(script);
            }
        });
    }

    // Fungsi utama setelah DOM ready
    $(document).ready(function() {
        // Inisialisasi komponen
        initMap();
        initAudioSystem();
        
        // Inisialisasi chart dengan fallback
        ensureChartPlugins()
            .then(() => {
                initMainChart();
                loadChartData(currentHours);
            })
            .catch(error => {
                console.error('Gagal memuat plugin chart:', error);
                initBasicChart();
            });
        
        // Muat data awal
        fetchLatestData();
        
        // Setup event handlers
        setupEventHandlers();
        
        // Mulai auto-refresh
        startAutoRefresh();
    });
    
    function initMap() {
    try {
        // Koordinat Pekon Teluk Kiluan Negri (lokasi tetap)
        const kiluanLat = -5.774832;
        const kiluanLng = 105.105028;

        // Inisialisasi peta dengan center ke Kiluan
        map = L.map('sensor-map').setView([kiluanLat, kiluanLng], 13);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 18
        }).addTo(map);

        // Tambahkan marker utama di Kiluan
        const kiluanIcon = L.divIcon({
            html: `<div style="text-align: center;">
                      <div style="background-color: var(--primary-color, #1e40af); 
                                  color: white; 
                                  width: 40px; 
                                  height: 40px; 
                                  border-radius: 50%; 
                                  display: flex; 
                                  align-items: center; 
                                  justify-content: center;
                                  box-shadow: 0 4px 10px rgba(30, 64, 175, 0.4);
                                  border: 3px solid white;">
                          <i class="fas fa-broadcast-tower" style="font-size: 18px;"></i>
                      </div>
                   </div>`,
            className: '',
            iconSize: [40, 40],
            iconAnchor: [20, 40]
        });
        
        L.marker([kiluanLat, kiluanLng], { icon: kiluanIcon })
            .addTo(map)
            .bindPopup(`
                <div style="padding: 10px; min-width: 200px;">
                    <h6 style="margin: 0 0 10px 0; color: var(--primary-color, #1e40af);">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        Pekon Teluk Kiluan Negri
                    </h6>
                    <p style="margin: 5px 0; font-size: 0.9rem;">
                        <strong>Lokasi Sistem:</strong><br>
                        Lat: ${kiluanLat}<br>
                        Lng: ${kiluanLng}
                    </p>
                    <hr style="margin: 10px 0;">
                    <p style="margin: 5px 0; font-size: 0.85rem; color: #6c757d;">
                        <i class="fas fa-broadcast-tower me-1"></i>
                        Sistem Peringatan Dini Tsunami
                    </p>
                </div>
            `, {
                maxWidth: 300
            })
            .openPopup();
        
        // Tambahkan circle untuk coverage area
        L.circle([kiluanLat, kiluanLng], {
            color: 'var(--primary-color, #1e40af)',
            fillColor: 'var(--primary-color, #1e40af)',
            fillOpacity: 0.1,
            radius: 2000, // 2km radius
            weight: 2
        }).addTo(map);

    } catch (error) {
        console.error('Gagal inisialisasi peta:', error);
        $('#sensor-map').html(`
            <div class="alert alert-danger p-3">
                <h5>Gagal Memuat Peta</h5>
                <p>${error.message}</p>
                <p>Silakan refresh halaman atau hubungi administrator.</p>
            </div>
        `);
    }
}

    function initAudioSystem() {
        // Nonaktifkan error audio yang mengganggu
        window.addEventListener('error', function(e) {
            if (e.target.tagName === 'AUDIO' || e.target.tagName === 'SOURCE') {
                e.preventDefault();
                console.log('Audio error ditangani:', e.target.src);
            }
        }, true);
        
        // Fallback jika audio tidak ada
        if (!$('#alert-sound source')[0] || !$('#siren-sound source')[0]) {
            console.log('File audio tidak ditemukan, menonaktifkan fitur suara');
            $('#alert-sound, #siren-sound').remove();
            $('#alert-siren-btn').prop('disabled', true)
                .html('<i class="fas fa-bell-slash me-1"></i> Sirene Tidak Tersedia');
        }
    }
    
    function initMainChart() {
        try {
            const ctx = document.getElementById('sensorChart');
            if (!ctx) {
                throw new Error('Canvas element tidak ditemukan');
            }
            
            const options = {
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
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y.toFixed(2);
                                    if (context.dataset.label.includes('Suhu')) {
                                        label += '°C';
                                    } else if (context.dataset.label.includes('Kelembaban')) {
                                        label += '%';
                                    } else if (context.dataset.label.includes('Tekanan')) {
                                        label += ' hPa';
                                    } else if (context.dataset.label.includes('Akselerasi')) {
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
                            text: 'Waktu',
                            font: { family: 'Poppins', weight: 'bold' }
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Nilai Sensor',
                            font: { family: 'Poppins', weight: 'bold' }
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
            };

            // Tambahkan annotation jika plugin tersedia
            if (typeof Chart.Annotation !== 'undefined') {
                options.plugins.annotation = {
                    annotations: {
                        dangerLine: {
                            type: 'line',
                            yMin: <?= ACCELERATION_DANGER ?>,
                            yMax: <?= ACCELERATION_DANGER ?>,
                            borderColor: '#e74a3b',
                            borderWidth: 1,
                            borderDash: [6, 6],
                            label: {
                                content: 'Batas Bahaya',
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
                                content: 'Batas Peringatan',
                                enabled: true,
                                position: 'left',
                                backgroundColor: 'rgba(246, 194, 62, 0.8)',
                                color: '#212529',
                                font: { size: 10 }
                            }
                        }
                    }
                };
            }

            sensorChart = new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: { datasets: [] },
                options: options,
                plugins: [
                    ...(typeof Chart.Annotation !== 'undefined' ? [Chart.Annotation] : []),
                    ...(typeof Chart.Zoom !== 'undefined' ? [Chart.Zoom] : [])
                ]
            });
            
        } catch (error) {
            console.error('Gagal inisialisasi chart utama:', error);
            initBasicChart();
        }
    }

    function initBasicChart() {
        try {
            const ctx = document.getElementById('sensorChart');
            if (!ctx) return;
            
            sensorChart = new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: { datasets: [] },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { type: 'time' },
                        y: { beginAtZero: true }
                    }
                }
            });
            
            console.log('Chart dasar berhasil diinisialisasi');
        } catch (error) {
            console.error('Gagal inisialisasi chart dasar:', error);
            $('#sensorChart').replaceWith('<div class="alert alert-warning">Grafik tidak dapat dimuat</div>');
        }
    }
    
    function setupEventHandlers() {
        // Node filter buttons
        $('.node-filter-btn').click(function() {
            $(this).toggleClass('active');
            const nodeId = $(this).data('node');
            $(`.node-card.node${nodeId}`).toggleClass('d-none');
            
            // Update chart visibility
            if (sensorChart) {
                const datasets = sensorChart.data.datasets;
                for (let i = 0; i < datasets.length; i++) {
                    if (datasets[i].label.includes(`Node ${nodeId}`)) {
                        const meta = sensorChart.getDatasetMeta(i);
                        meta.hidden = !$(this).hasClass('active');
                    }
                }
                sensorChart.update();
            }
        });
        
        // Toggle all nodes button
        $('#toggle-all-nodes').click(function() {
            const allActive = $('.node-filter-btn.active').length === 4;
            $('.node-filter-btn').toggleClass('active', !allActive);
            $('.node-card').toggleClass('d-none', allActive);
            $(this).html(`<i class="fas fa-eye${allActive ? '' : '-slash'} me-1"></i> ${allActive ? 'Tampilkan' : 'Sembunyikan'} Semua`);
            
            // Update chart visibility
            if (sensorChart) {
                const datasets = sensorChart.data.datasets;
                for (let i = 0; i < datasets.length; i++) {
                    const nodeId = Math.ceil((i+1)/2); // Karena ada 2 dataset per node
                    const meta = sensorChart.getDatasetMeta(i);
                    meta.hidden = !$(`.node-filter-btn[data-node="${nodeId}"]`).hasClass('active');
                }
                sensorChart.update();
            }
        });
        
        // Time filter buttons
        $('.time-filter-btn').click(function() {
            $('.time-filter-btn').removeClass('active');
            $(this).addClass('active');
            currentHours = parseInt($(this).data('hours'));
            loadChartData(currentHours);
        });
        
        // Sensor filter buttons
        $('.sensor-filter-btn').click(function() {
            $(this).toggleClass('active');
            const sensorType = $(this).data('sensor');
            
            // Update active sensors
            if ($(this).hasClass('active')) {
                if (!activeSensors.includes(sensorType)) {
                    activeSensors.push(sensorType);
                }
            } else {
                activeSensors = activeSensors.filter(s => s !== sensorType);
            }
            
            loadChartData(currentHours);
        });
        
        // Refresh map
        $('#refresh-map').click(function() {
            if (map) {
                const currentCenter = map.getCenter();
                const currentZoom = map.getZoom();
                map.setView(currentCenter, currentZoom, { animate: true });
            }
        });
        
        // Zoom controls
        $('#zoom-in-btn').click(function() {
            if (map) map.zoomIn();
        });
        
        $('#zoom-out-btn').click(function() {
            if (map) map.zoomOut();
        });
        
        $('#center-map-btn').click(function() {
            if (map && Object.keys(nodeMarkers).length > 0) {
                const group = new L.featureGroup(Object.values(nodeMarkers));
                map.fitBounds(group.getBounds().pad(0.2));
            }
        });
        
        // Alert panel buttons
        $('#alert-details-btn').click(function() {
            if (lastAlertNode) {
                showNodeDetails(lastAlertNode);
            }
        });
        
        $('#alert-siren-btn').click(function() {
            const sirenSound = document.getElementById('siren-sound');
            if (!sirenSound) {
                alert('Sirene tidak tersedia');
                return;
            }
            
            if (sirenPlaying) {
                sirenSound.pause();
                sirenSound.currentTime = 0;
                sirenPlaying = false;
                $(this).removeClass('btn-danger').addClass('btn-light');
                $(this).html('<i class="fas fa-bullhorn me-1"></i> Sirene Darurat');
            } else {
                sirenSound.play().catch(e => {
                    console.error('Gagal memutar sirene:', e);
                    alert('Gagal memutar sirene. Pastikan browser mengizinkan audio.');
                });
                sirenPlaying = true;
                $(this).removeClass('btn-light').addClass('btn-danger');
                $(this).html('<i class="fas fa-stop me-1"></i> Matikan Sirene');
            }
        });
        
        // Silence button
        $('#silence-btn').click(function() {
            const alertSound = document.getElementById('alert-sound');
            if (alertSound) {
                alertSound.pause();
                alertSound.currentTime = 0;
            }
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
    
    function loadChartData(hours) {
        $.get('api/get-node-data.php', { 
            hours: hours,
            sensors: activeSensors.join(',')
        })
        .done(function(data) {
            if (data.status === 'success') {
                updateMainChart(data);
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            console.error('Gagal memuat data grafik:', textStatus, errorThrown);
            // Tampilkan data dummy untuk development
            if (window.location.hostname === 'localhost') {
                console.log('Menggunakan data dummy untuk development');
                updateMainChart(getDummyData());
            }
        });
    }
    
    function getDummyData() {
        // Generate dummy data untuk development
        const now = new Date();
        const data = {
            status: 'success',
            node1: {
                vibration: Array(24).fill().map((_, i) => ({
                    x: new Date(now.getTime() - (23 - i) * 3600000),
                    y: Math.floor(Math.random() * 500) + 100
                })),
                acceleration: Array(24).fill().map((_, i) => ({
                    x: new Date(now.getTime() - (23 - i) * 3600000),
                    y: Math.random() * 10 + 2
                })),
                temperature: Array(24).fill().map((_, i) => ({
                    x: new Date(now.getTime() - (23 - i) * 3600000),
                    y: Math.random() * 10 + 25
                })),
                humidity: Array(24).fill().map((_, i) => ({
                    x: new Date(now.getTime() - (23 - i) * 3600000),
                    y: Math.random() * 30 + 60
                }))
            },
            node2: {
                vibration: Array(24).fill().map((_, i) => ({
                    x: new Date(now.getTime() - (23 - i) * 3600000),
                    y: Math.floor(Math.random() * 600) + 50
                })),
                acceleration: Array(24).fill().map((_, i) => ({
                    x: new Date(now.getTime() - (23 - i) * 3600000),
                    y: Math.random() * 12 + 1
                })),
                temperature: Array(24).fill().map((_, i) => ({
                    x: new Date(now.getTime() - (23 - i) * 3600000),
                    y: Math.random() * 10 + 24
                })),
                humidity: Array(24).fill().map((_, i) => ({
                    x: new Date(now.getTime() - (23 - i) * 3600000),
                    y: Math.random() * 30 + 65
                }))
            },
            node3: {
                vibration: Array(24).fill().map((_, i) => ({
                    x: new Date(now.getTime() - (23 - i) * 3600000),
                    y: Math.floor(Math.random() * 800) + 200
                })),
                acceleration: Array(24).fill().map((_, i) => ({
                    x: new Date(now.getTime() - (23 - i) * 3600000),
                    y: Math.random() * 15 + 3
                })),
                temperature: Array(24).fill().map((_, i) => ({
                    x: new Date(now.getTime() - (23 - i) * 3600000),
                    y: Math.random() * 10 + 26
                })),
                humidity: Array(24).fill().map((_, i) => ({
                    x: new Date(now.getTime() - (23 - i) * 3600000),
                    y: Math.random() * 30 + 55
                }))
            },
            node4: {
                vibration: Array(24).fill().map((_, i) => ({
                    x: new Date(now.getTime() - (23 - i) * 3600000),
                    y: Math.floor(Math.random() * 400) + 80
                })),
                acceleration: Array(24).fill().map((_, i) => ({
                    x: new Date(now.getTime() - (23 - i) * 3600000),
                    y: Math.random() * 8 + 1.5
                })),
                temperature: Array(24).fill().map((_, i) => ({
                    x: new Date(now.getTime() - (23 - i) * 3600000),
                    y: Math.random() * 10 + 27
                })),
                humidity: Array(24).fill().map((_, i) => ({
                    x: new Date(now.getTime() - (23 - i) * 3600000),
                    y: Math.random() * 30 + 50
                }))
            }
        };
        return data;
    }
    
    function updateMainChart(data) {
        if (!sensorChart) return;
        
        // Warna untuk setiap node
        const nodeColors = {
            1: '#4e73df',
            2: '#1cc88a',
            3: '#f6c23e',
            4: '#e74a3b'
        };
        
        // Label untuk setiap sensor
        const sensorLabels = {
            vibration: 'Getaran',
            acceleration: 'Akselerasi (m/s²)',
            temperature: 'Suhu (°C)',
            humidity: 'Kelembaban (%)',
            pressure: 'Tekanan (hPa)',
            battery: 'Baterai (%)'
        };
        
        // Update datasets
        sensorChart.data.datasets = [];
        
        for (let nodeId = 1; nodeId <= 4; nodeId++) {
            const nodeData = data[`node${nodeId}`] || {};
            const isActive = $(`.node-filter-btn[data-node="${nodeId}"]`).hasClass('active');
            
            // Tambahkan dataset untuk setiap sensor yang aktif
            activeSensors.forEach(sensor => {
                if (nodeData[sensor] && nodeData[sensor].length > 0) {
                    sensorChart.data.datasets.push({
                        label: `Node ${nodeId} - ${sensorLabels[sensor] || sensor}`,
                        data: nodeData[sensor],
                        borderColor: nodeColors[nodeId],
                        backgroundColor: hexToRgba(nodeColors[nodeId], 0.1),
                        borderWidth: 2,
                        pointRadius: 0,
                        pointHoverRadius: 5,
                        yAxisID: 'y',
                        fill: sensor === 'vibration' || sensor === 'temperature',
                        hidden: !isActive
                    });
                }
            });
        }
        
        sensorChart.update();
    }
    
    function startAutoRefresh() {
        // Initial fetch
        fetchLatestData();
        
        // Set up interval for auto-refresh (every 5 seconds)
        setInterval(fetchLatestData, 5000);
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
                    
                    nodeElement.find('.fa-lg')
                        .removeClass('fa-exclamation-triangle fa-exclamation-circle fa-check-circle')
                        .addClass(`fa-${statusIcon}`)
                        .removeClass('status-normal status-warning status-danger')
                        .addClass(statusClass);
                    
                    // Update sensor values
                    nodeElement.find('.sensor-value').eq(0).text(nodeData.vibration || '--');
                    nodeElement.find('.sensor-value').eq(1).text(
                        nodeData.mpu6050 ? `${roundToTwo(nodeData.mpu6050)}`  : '--'
                    );
                    nodeElement.find('.sensor-value').eq(2).text(
                        nodeData.temperature ? `${nodeData.temperature}`  : '--'
                    );
                    nodeElement.find('.sensor-value').eq(3).text(
                        nodeData.humidity ? `${nodeData.humidity}`  : '--'
                    );
                    nodeElement.find('.sensor-value').eq(4).text(
                        nodeData.pressure ? `${nodeData.pressure}`  : '--'
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
                    
                    // Update map marker if position changed
                    if (nodeData.latitude && nodeData.longitude && nodeMarkers[nodeId]) {
                        const newLatLng = L.latLng(nodeData.latitude, nodeData.longitude);
                        nodeMarkers[nodeId].setLatLng(newLatLng);
                        
                        // Update popup content
                        nodeMarkers[nodeId].setPopupContent(`
                            <b>Node ${nodeId}</b><br>
                            Lokasi: ${roundToFour(nodeData.latitude)}, ${roundToFour(nodeData.longitude)}<br>
                            Status: <span class="${statusClass}">${nodeData.status}</span><br>
                            Update terakhir: ${timeStr}
                        `);
                    }
                    
                    // Trigger alert if status changed to warning/danger
                    if (nodeData.status === 'BAHAYA' || nodeData.status === 'PERINGATAN') {
                        if (!lastAlertNode || lastAlertNode !== nodeId) {
                            triggerAlert(
                                `Peringatan Node ${nodeId}`, 
                                `Pembacaan sensor tidak normal (${nodeData.status})`,
                                nodeData.status.toLowerCase(),
                                nodeId
                            );
                            lastAlertNode = nodeId;
                        }
                    }
                }
                
                // Show tsunami alert panel if any node is in danger
                if (data.nodes && Object.values(data.nodes).some(node => node.status === 'BAHAYA')) {
                    $('#tsunami-alert-panel').slideDown();
                    $('html').attr('data-bs-theme', 'dark');
                } else {
                    $('#tsunami-alert-panel').slideUp();
                    $('html').attr('data-bs-theme', 'light');
                }
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            console.error('Gagal mengambil data terbaru:', textStatus, errorThrown);
        });
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
        if ((type === 'danger' || type === 'warning') && alertSound) {
            if (!alertSoundPlaying) {
                alertSound.play().catch(e => {
                    console.error('Gagal memutar alert sound:', e);
                });
                alertSoundPlaying = true;
                $('#silence-btn').removeClass('d-none');
            }
        }
    }
    
    function showNodeDetails(nodeElement) {
        const nodeId = typeof nodeElement === 'object' ? $(nodeElement).data('node') : nodeElement;
        
        // Set modal title
        $('#nodeModalTitle').html(`<i class="fas fa-satellite-dish me-2" style="color: ${getNodeColor(nodeId)}"></i> Analisis Detail Node ${nodeId}`);
        
        // Fetch detailed data for this node
        $.get('api/get-node-data.php', { node_id: nodeId })
        .done(function(data) {
            if (data.status === 'success') {
                // Update vibration chart
                initNodeChart('nodeVibrationChart', 
                    `Level Getaran Node ${nodeId}`, 
                    data.vibration, 
                    'Getaran',
                    '#f6c23e',
                    <?= VIBRATION_WARNING ?>,
                    <?= VIBRATION_DANGER ?>
                );
                
                // Update acceleration chart
                initNodeChart('nodeAccelChart', 
                    `Akselerasi Node ${nodeId}`, 
                    data.acceleration, 
                    'Akselerasi (m/s²)',
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
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            console.error('Gagal memuat detail node:', textStatus, errorThrown);
            alert('Gagal memuat detail node. Silakan coba lagi.');
        });
    }
    
    function initNodeChart(canvasId, title, data, label, color, warningThreshold, dangerThreshold) {
        try {
            const ctx = document.getElementById(canvasId);
            if (!ctx) throw new Error('Canvas element tidak ditemukan');
            
            if (window[canvasId.replace('Chart', '')]) {
                window[canvasId.replace('Chart', '')].destroy();
            }
            
            const options = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: title,
                        font: { family: 'Poppins', size: 14 }
                    },
                    legend: { display: false }
                },
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'minute',
                            displayFormats: { minute: 'HH:mm' }
                        }
                    },
                    y: {
                        title: { display: true, text: label }
                    }
                },
                elements: { line: { tension: 0.1 } }
            };
            
            // Tambahkan annotation jika plugin tersedia
            if (typeof Chart.Annotation !== 'undefined') {
                options.plugins.annotation = {
                    annotations: {
                        dangerLine: {
                            type: 'line',
                            yMin: dangerThreshold,
                            yMax: dangerThreshold,
                            borderColor: '#e74a3b',
                            borderWidth: 1,
                            borderDash: [6, 6],
                            label: {
                                content: 'Batas Bahaya',
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
                                content: 'Batas Peringatan',
                                enabled: true,
                                position: 'left',
                                backgroundColor: 'rgba(246, 194, 62, 0.8)',
                                color: '#212529',
                                font: { size: 10 }
                            }
                        }
                    }
                };
            }
            
            window[canvasId.replace('Chart', '')] = new Chart(ctx.getContext('2d'), {
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
                options: options,
                plugins: typeof Chart.Annotation !== 'undefined' ? [Chart.Annotation] : []
            });
            
        } catch (error) {
            console.error(`Gagal inisialisasi chart ${canvasId}:`, error);
            document.getElementById(canvasId).parentElement.innerHTML = 
                '<div class="alert alert-warning">Grafik tidak dapat dimuat</div>';
        }
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
    
    function roundToTwo(num) {
        return +(Math.round(num + "e+2")  + "e-2");
    }
    
    function roundToFour(num) {
        return +(Math.round(num + "e+4")  + "e-4");
    }
    </script>
</body>
</html>