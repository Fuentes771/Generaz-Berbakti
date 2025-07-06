<?php
// Configuration
$esp_ip = "192.168.241.203"; // Replace with your ESP8266 IP address
$db_config = [
    'host' => 'localhost',
    'user' => 'username',
    'pass' => 'password',
    'name' => 'tsunami_warning'
];
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendeteksi Dini Tsuami Rinova</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Gauge JS -->
    <script src="https://cdn.jsdelivr.net/npm/gaugeJS/dist/gauge.min.js"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="fas fa-water me-2"></i> Sistm Pedeteksi Dini Tsunami</h1>
                    <p class="lead mb-0">Dashboard real-time monitoring Tsunami</p>
                </div>
                <div class="col-md-4 text-end">
                    <div id="connection-status" class="badge bg-success p-2">
                        <i class="fas fa-circle"></i> Connected
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Status Row -->
        <div class="row mb-4">
            <!-- Vibration Level -->
            <div class="col-md-4">
                <div class="card status-card">
                    <div class="card-body text-center">
                        <h5 class="card-title"><i class="fas fa-chart-line sensor-icon"></i>Kekuatan Gempa</h5>
                        <div class="data-value" id="vibration-value">0</div>
                        <div class="gauge-container">
                            <canvas id="vibration-gauge"></canvas>
                        </div>
                        <div class="progress mt-2" style="height: 8px;">
                            <div id="vibration-progress" class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- System Status -->
            <div class="col-md-4">
                <div class="card status-card h-100">
                    <div class="card-body text-center d-flex flex-column">
                        <h5 class="card-title"><i class="fas fa-info-circle sensor-icon"></i>Status Sistem</h5>
                        <div class="data-value flex-grow-1 d-flex align-items-center justify-content-center">
                            <div id="system-status" class="status-normal px-4 py-2 rounded-pill">
                                Normal
                            </div>
                        </div>
                        <div class="mt-auto">
                            <small class="text-muted">Analisis pola getaran terakhir</small>
                            <div id="last-analysis" class="small">Baru Saja</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Alert Status -->
            <div class="col-md-4">
                <div class="card status-card h-100" id="alert-card">
                    <div class="card-body text-center d-flex flex-column">
                        <h5 class="card-title"><i class="fas fa-bell sensor-icon"></i>Status Peringatan</h5>
                        <div class="data-value flex-grow-1 d-flex align-items-center justify-content-center">
                            <div id="alert-status" class="px-4 py-2 rounded-pill bg-secondary text-white">
                                Tidak Aktif
                            </div>
                        </div>
                        <button id="silence-btn" class="btn btn-light btn-sm mt-2 align-self-center" style="display: none;">
                            <i class="fas fa-volume-mute me-1"></i> Matikan Alarm
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visualization Row -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Aktivitas Gempa</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="vibration-chart" height="250"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-map-marked-alt me-2"></i>Lokasi Sensor</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="map-container d-flex align-items-center justify-content-center">
                            <div class="text-center text-muted" id="map-overlay">
                                <i class="fas fa-map fa-3x mb-2"></i>
                                <p>Map visualization</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event History -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Histori Sensor</h5>
                        <button class="btn btn-sm btn-outline-secondary" id="refresh-logs">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Waktu</th>
                                        <th>Kejadian</th>
                                        <th>Itensitas</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="event-logs">
                                    <!-- Logs will be loaded here -->
                                    <tr>
                                        <td colspan="5" class="text-center py-4">Loading histori...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Section -->
    <footer class="footer">
        <div class="container">
            <span class="footer-brand">Sistem Pendeteksi Dini Tsunami Rinova BEM U KBM UNILA</span>
            
            <div class="footer-links">
                <a href="#" class="footer-link">Tentang Kami</a>
                <a href="#" class="footer-link">Kebijakan Privasi</a>
                <a href="#" class="footer-link">Syarat & Ketentuan</a>
                <a href="#" class="footer-link">Kontak</a>
                <a href="#" class="footer-link">Dokumentasi</a>
            </div>
            
            <div class="social-icons">
                <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
            </div>
            
            <div class="copyright">
                &copy; <span id="current-year"><?php echo date('Y'); ?></span> M Sulthon Alfarizky. All rights reserved.
                <div class="mt-2">Versi 1.0.0</div>
            </div>
        </div>
    </footer>

    <!-- Last Update Indicator -->
    <div class="last-update" id="last-update">
        <i class="fas fa-clock me-1"></i> Updating...
    </div>

    <!-- Audio Element for Alerts (hidden) -->
    <audio id="alert-sound" loop>
        <source src="alert.mp3" type="audio/mpeg">
    </audio>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="js/script.js"></script>
</body>
</html>