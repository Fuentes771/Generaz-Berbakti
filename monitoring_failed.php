<?php
require_once 'includes/config.php';

// Fungsi untuk cek status sistem (apakah ada sensor yang aktif)
function getSystemStatus(): array {
    try {
        $conn = getDatabaseConnection();
        
        // Cek data sensor dalam 5 menit terakhir
        $stmt = $conn->prepare("
            SELECT COUNT(*) as active_sensors,
                   MAX(timestamp) as last_update
            FROM sensor_data 
            WHERE timestamp >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
        ");
        $stmt->execute();
        $result = $stmt->fetch();
        
        $isActive = ($result['active_sensors'] > 0);
        $lastUpdate = $result['last_update'];
        
        return [
            'is_active' => $isActive,
            'active_sensors' => $result['active_sensors'],
            'last_update' => $lastUpdate,
            'status_text' => $isActive ? 'Sistem Aktif' : 'Sistem Tidak Aktif',
            'status_class' => $isActive ? 'status-active' : 'status-inactive'
        ];
        
    } catch (PDOException $e) {
        error_log("Error checking system status: " . $e->getMessage());
        return [
            'is_active' => false,
            'active_sensors' => 0,
            'last_update' => null,
            'status_text' => 'Sistem Offline',
            'status_class' => 'status-offline'
        ];
    }
}

// Ambil status sistem
$systemStatus = getSystemStatus();

// Koordinat Pekon Teluk Kiluan Negri
$kiluanCenter = [
    'lat' => -5.6833,
    'lng' => 105.0333,
    'name' => 'Pekon Teluk Kiluan Negri',
    'description' => 'Lokasi Sistem Peringatan Dini Tsunami'
];

$pageTitle = "Monitoring Real-time - Sistem Peringatan Dini Tsunami";
$activePage = "monitoring";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Monitoring Real-time Sistem Peringatan Dini Tsunami">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/img/favicon-32x32.png">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <link rel="stylesheet" href="assets/css/core.css">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        /* Hero Section */
        .monitoring-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0 40px;
            position: relative;
            overflow: hidden;
        }
        
        .monitoring-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') bottom center no-repeat;
            background-size: cover;
            opacity: 0.3;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .system-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 25px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 50px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            font-weight: 600;
            margin-top: 20px;
        }
        
        .status-active .status-dot {
            width: 12px;
            height: 12px;
            background: #2ecc71;
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }
        
        .status-inactive .status-dot {
            width: 12px;
            height: 12px;
            background: #e74c3c;
            border-radius: 50%;
        }
        
        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(46, 204, 113, 0.7);
            }
            50% {
                box-shadow: 0 0 0 10px rgba(46, 204, 113, 0);
            }
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: white;
        }
        
        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            font-family: 'Orbitron', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .stat-label {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-top: 5px;
        }
        
        /* Map Section */
        .map-section {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            margin-top: 30px;
        }
        
        .map-container {
            height: 500px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .map-info {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 20px;
            border-radius: 15px;
            margin-top: 20px;
        }
        
        .map-info h6 {
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-icon {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #667eea;
        }
        
        /* Sensor Status Section */
        .sensor-section {
            margin-top: 30px;
        }
        
        .sensor-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .sensor-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        
        .sensor-card.sensor-offline {
            opacity: 0.6;
            border-left-color: #e74c3c;
        }
        
        .sensor-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        }
        
        .sensor-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .sensor-title {
            font-weight: 700;
            color: #2c3e50;
        }
        
        .sensor-status {
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .sensor-status.online {
            background: #d4edda;
            color: #155724;
        }
        
        .sensor-status.offline {
            background: #f8d7da;
            color: #721c24;
        }
        
        .sensor-reading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .sensor-reading:last-child {
            border-bottom: none;
        }
        
        .reading-label {
            color: #7f8c8d;
            font-size: 0.9rem;
        }
        
        .reading-value {
            font-size: 1.5rem;
            font-weight: 700;
            font-family: 'Orbitron', sans-serif;
            color: #667eea;
        }
        
        /* Alert Box */
        .alert-box {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            padding: 20px;
            border-radius: 15px;
            margin-top: 30px;
            display: none;
        }
        
        .alert-box.show {
            display: block;
            animation: slideInDown 0.5s ease;
        }
        
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert-box h5 {
            margin: 0;
            font-weight: 700;
        }
        
        .alert-box p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .monitoring-hero {
                padding: 40px 0 30px;
            }
            
            .stat-value {
                font-size: 2rem;
            }
            
            .map-container {
                height: 350px;
            }
        }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<!-- Hero Section -->
<section class="monitoring-hero">
    <div class="container">
        <div class="hero-content text-center">
            <h1 class="display-4 fw-bold mb-3">Monitoring Real-time</h1>
            <p class="lead">Pemantauan Sistem Peringatan Dini Tsunami 24/7</p>
            
            <div class="system-status-badge <?= $systemStatus['status_class'] ?>">
                <div class="status-dot"></div>
                <span><?= $systemStatus['status_text'] ?></span>
                <?php if ($systemStatus['last_update']): ?>
                <small style="opacity: 0.8;">| Update: <?= date('H:i:s', strtotime($systemStatus['last_update'])) ?></small>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<div class="container pb-5">
    
    <!-- Alert Box (Hidden by default) -->
    <div id="systemAlert" class="alert-box">
        <div class="d-flex align-items-center gap-3">
            <div>
                <i class="fas fa-exclamation-triangle fa-3x"></i>
            </div>
            <div>
                <h5>PERINGATAN SISTEM</h5>
                <p id="alertMessage">Sensor tidak mengirim data. Sistem dalam mode offline.</p>
            </div>
        </div>
    </div>
    
    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-value" id="uptime">24/7</div>
            <div class="stat-label">Waktu Operasional</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-satellite-dish"></i>
            </div>
            <div class="stat-value" id="sensorCount"><?= $systemStatus['active_sensors'] ?></div>
            <div class="stat-label">Sensor Aktif</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div class="stat-value status-indicator">
                <?= $systemStatus['is_active'] ? 'ON' : 'OFF' ?>
            </div>
            <div class="stat-label">Status Sistem</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-sync-alt"></i>
            </div>
            <div class="stat-value" id="updateTime">--:--:--</div>
            <div class="stat-label">Update Terakhir</div>
        </div>
    </div>
    
    <!-- Map Section -->
    <div class="map-section">
        <h3 class="mb-4">
            <i class="fas fa-map-marked-alt me-2" style="color: #667eea;"></i>
            Peta Lokasi Sistem
        </h3>
        
        <div class="map-container" id="kiluanMap"></div>
        
        <div class="map-info">
            <h6><i class="fas fa-info-circle me-2"></i>Informasi Lokasi</h6>
            
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div>
                    <strong><?= $kiluanCenter['name'] ?></strong><br>
                    <small class="text-muted"><?= $kiluanCenter['description'] ?></small>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-compass"></i>
                </div>
                <div>
                    <strong>Koordinat</strong><br>
                    <small class="text-muted">
                        Lat: <?= $kiluanCenter['lat'] ?>, Lng: <?= $kiluanCenter['lng'] ?>
                    </small>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-water"></i>
                </div>
                <div>
                    <strong>Zona Risiko</strong><br>
                    <small class="text-muted">Wilayah Pesisir - Pantai Kiluan</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sensor Status Section -->
    <div class="sensor-section">
        <h3 class="mb-4">
            <i class="fas fa-sensor me-2" style="color: #667eea;"></i>
            Status Sensor
        </h3>
        
        <div class="sensor-grid" id="sensorGrid">
            <!-- Sensors will be loaded via JavaScript -->
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Memuat data sensor...</p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
let map;
let systemActive = <?= $systemStatus['is_active'] ? 'true' : 'false' ?>;

// Initialize map
function initMap() {
    try {
        // Center to Kiluan
        const kiluanLat = <?= $kiluanCenter['lat'] ?>;
        const kiluanLng = <?= $kiluanCenter['lng'] ?>;
        
        map = L.map('kiluanMap').setView([kiluanLat, kiluanLng], 13);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 18
        }).addTo(map);
        
        // Add custom marker for Kiluan
        const customIcon = L.divIcon({
            html: `<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                          width: 40px; height: 40px; border-radius: 50%; 
                          display: flex; align-items: center; justify-content: center;
                          box-shadow: 0 4px 15px rgba(102, 126, 234, 0.5);
                          border: 3px solid white;">
                      <i class="fas fa-broadcast-tower" style="color: white; font-size: 18px;"></i>
                   </div>`,
            className: '',
            iconSize: [40, 40],
            iconAnchor: [20, 20]
        });
        
        L.marker([kiluanLat, kiluanLng], { icon: customIcon })
            .addTo(map)
            .bindPopup(`
                <div style="text-align: center; padding: 10px;">
                    <h6 style="margin: 0 0 10px 0; color: #667eea;">
                        <i class="fas fa-map-marker-alt me-1"></i>
                        <?= $kiluanCenter['name'] ?>
                    </h6>
                    <p style="margin: 0; font-size: 0.85rem; color: #7f8c8d;">
                        <?= $kiluanCenter['description'] ?>
                    </p>
                    <hr style="margin: 10px 0;">
                    <small style="color: #95a5a6;">
                        Status: <strong style="color: ${systemActive ? '#2ecc71' : '#e74c3c'}">
                            ${systemActive ? 'Aktif' : 'Tidak Aktif'}
                        </strong>
                    </small>
                </div>
            `, {
                maxWidth: 300
            })
            .openPopup();
        
        // Add circle radius
        L.circle([kiluanLat, kiluanLng], {
            color: '#667eea',
            fillColor: '#667eea',
            fillOpacity: 0.1,
            radius: 2000 // 2km radius
        }).addTo(map);
        
    } catch (error) {
        console.error('Error initializing map:', error);
        $('#kiluanMap').html(`
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Gagal memuat peta. Silakan refresh halaman.
            </div>
        `);
    }
}

// Load sensor data
function loadSensorData() {
    $.ajax({
        url: 'api/get-latest-data.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                updateSensorCards(response.nodes);
                updateSystemStats(response);
            } else {
                showOfflineMessage();
            }
        },
        error: function() {
            showOfflineMessage();
        }
    });
}

// Update sensor cards
function updateSensorCards(nodes) {
    let html = '';
    
    for (let i = 1; i <= 4; i++) {
        const node = nodes[i] || {};
        const isOnline = node.timestamp && isDataRecent(node.timestamp);
        const statusClass = isOnline ? 'online' : 'offline';
        const statusText = isOnline ? 'Online' : 'Offline';
        
        html += `
            <div class="sensor-card ${!isOnline ? 'sensor-offline' : ''}">
                <div class="sensor-header">
                    <h5 class="sensor-title mb-0">
                        <i class="fas fa-satellite-dish me-2"></i>
                        Sensor Node ${i}
                    </h5>
                    <span class="sensor-status ${statusClass}">${statusText}</span>
                </div>
                
                <div class="sensor-reading">
                    <span class="reading-label">
                        <i class="fas fa-bolt me-1"></i> Getaran
                    </span>
                    <span class="reading-value">${node.vibration || '--'}</span>
                </div>
                
                <div class="sensor-reading">
                    <span class="reading-label">
                        <i class="fas fa-wave-square me-1"></i> Akselerasi
                    </span>
                    <span class="reading-value">${node.mpu6050 ? node.mpu6050.toFixed(2) : '--'} m/s²</span>
                </div>
                
                <div class="sensor-reading">
                    <span class="reading-label">
                        <i class="fas fa-thermometer-half me-1"></i> Suhu
                    </span>
                    <span class="reading-value">${node.temperature || '--'}°C</span>
                </div>
                
                <div class="sensor-reading">
                    <span class="reading-label">
                        <i class="fas fa-tint me-1"></i> Kelembaban
                    </span>
                    <span class="reading-value">${node.humidity || '--'}%</span>
                </div>
                
                ${isOnline ? `
                <div class="text-muted small mt-3">
                    <i class="fas fa-clock me-1"></i>
                    Update: ${formatTime(node.timestamp)}
                </div>
                ` : `
                <div class="text-danger small mt-3">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    Tidak ada data
                </div>
                `}
            </div>
        `;
    }
    
    $('#sensorGrid').html(html);
    
    // Check if all sensors offline
    const activeSensors = Object.values(nodes).filter(n => 
        n.timestamp && isDataRecent(n.timestamp)
    ).length;
    
    if (activeSensors === 0) {
        $('#systemAlert').addClass('show');
        systemActive = false;
    } else {
        $('#systemAlert').removeClass('show');
        systemActive = true;
    }
}

// Update system stats
function updateSystemStats(response) {
    const activeSensors = Object.values(response.nodes || {}).filter(n => 
        n.timestamp && isDataRecent(n.timestamp)
    ).length;
    
    $('#sensorCount').text(activeSensors);
    $('.status-indicator').text(systemActive ? 'ON' : 'OFF');
    $('#updateTime').text(new Date().toLocaleTimeString());
    
    // Update status badge in hero
    $('.system-status-badge')
        .removeClass('status-active status-inactive')
        .addClass(systemActive ? 'status-active' : 'status-inactive');
    
    $('.system-status-badge span').first().text(
        systemActive ? 'Sistem Aktif' : 'Sistem Tidak Aktif'
    );
}

// Show offline message
function showOfflineMessage() {
    $('#sensorGrid').html(`
        <div class="col-12">
            <div class="alert alert-warning">
                <h5><i class="fas fa-exclamation-triangle me-2"></i>Sensor Offline</h5>
                <p class="mb-0">Tidak dapat terhubung ke sensor. Sistem sedang dalam mode offline.</p>
            </div>
        </div>
    `);
    
    $('#systemAlert').addClass('show');
    systemActive = false;
}

// Check if data is recent (within 5 minutes)
function isDataRecent(timestamp) {
    const now = new Date();
    const dataTime = new Date(timestamp);
    const diffMinutes = (now - dataTime) / 1000 / 60;
    return diffMinutes < 5;
}

// Format time
function formatTime(timestamp) {
    return new Date(timestamp).toLocaleTimeString('id-ID');
}

// Initialize on page load
$(document).ready(function() {
    initMap();
    loadSensorData();
    
    // Auto refresh every 5 seconds
    setInterval(loadSensorData, 5000);
    
    // Update clock
    setInterval(function() {
        $('#updateTime').text(new Date().toLocaleTimeString());
    }, 1000);
});

// Console branding
console.log('%c🌊 Monitoring System Active', 'background: #667eea; color: white; font-size: 16px; font-weight: bold; padding: 10px;');
console.log('%cSistem Peringatan Dini Tsunami - Real-time Monitoring', 'color: #667eea; font-size: 12px;');
</script>

</body>
</html>
