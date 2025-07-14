<?php
// Start session dan include config jika diperlukan
require_once '../includes/config.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Tsunami Monitoring System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/admin.css?v=<?= time() ?>">
    
    <!-- Favicon -->
    <link rel="icon" href="../assets/img/favicon.ico" type="image/x-icon">
</head>
<body class="admin-dashboard">
    <!-- Header -->
    <header class="dashboard-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="dashboard-title">
                        <i class="fas fa-water me-2"></i> Monitoring Real-time Sensor Tsunami
                    </h1>
                </div>
                <div class="col-md-6 text-end">
                    <div class="header-controls">
                        <span class="update-time me-3">
                            <i class="fas fa-clock me-1"></i> 
                            <strong id="update-time">--:--:--</strong>
                        </span>
                        <button class="btn btn-sm btn-outline-light refresh-btn" id="refresh-btn">
                            <i class="fas fa-sync-alt me-1"></i> Refresh
                        </button>
                        <div class="dropdown d-inline-block ms-3">
                            <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i> <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="dashboard-content">
        <div class="container-fluid">
            <!-- Sensor Cards Row -->
            <div class="row sensor-cards">
                <!-- MPU6050 Card -->
                <div class="col-md-4 mb-4">
                    <div class="card sensor-card h-100" id="mpu-card">
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title">
                                <i class="fas fa-vibration me-2"></i> MPU6050
                                <span class="badge bg-light text-dark float-end">Getaran Halus</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="sensor-value-container">
                                <div class="sensor-value">0.0 <small>m/s²</small></div>
                                <span class="sensor-status badge bg-secondary">Loading...</span>
                            </div>
                            <div class="threshold-indicator mt-3">
                                <div class="threshold-marker" style="left:40%" data-value="40">Waspada (≥40)</div>
                                <div class="threshold-marker" style="left:70%" data-value="70">Bahaya (≥70)</div>
                                <div class="threshold-bar">
                                    <div class="current-value" style="width: 0%"></div>
                                </div>
                            </div>
                            <div class="time-filter mt-4">
                                <button class="btn btn-sm btn-time active" data-hours="1">1 Jam</button>
                                <button class="btn btn-sm btn-time" data-hours="6">6 Jam</button>
                                <button class="btn btn-sm btn-time" data-hours="24">24 Jam</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BME280 Card -->
                <div class="col-md-4 mb-4">
                    <div class="card sensor-card h-100" id="bme-card">
                        <div class="card-header bg-info text-white">
                            <h3 class="card-title">
                                <i class="fas fa-tachometer-alt me-2"></i> BME280
                                <span class="badge bg-light text-dark float-end">Tekanan Udara</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="sensor-value-container">
                                <div class="sensor-value">0.0 <small>hPa</small></div>
                                <span class="sensor-status badge bg-secondary">Loading...</span>
                            </div>
                            <div class="threshold-indicator mt-3">
                                <div class="threshold-marker" style="left:60%" data-value="60">Waspada (≥60)</div>
                                <div class="threshold-marker" style="left:85%" data-value="85">Bahaya (≥85)</div>
                                <div class="threshold-bar">
                                    <div class="current-value" style="width: 0%"></div>
                                </div>
                            </div>
                            <div class="time-filter mt-4">
                                <button class="btn btn-sm btn-time active" data-hours="1">1 Jam</button>
                                <button class="btn btn-sm btn-time" data-hours="6">6 Jam</button>
                                <button class="btn btn-sm btn-time" data-hours="24">24 Jam</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Piezoelektrik Card -->
                <div class="col-md-4 mb-4">
                    <div class="card sensor-card h-100" id="piezo-card">
                        <div class="card-header bg-warning text-dark">
                            <h3 class="card-title">
                                <i class="fas fa-wave-square me-2"></i> Piezoelektrik
                                <span class="badge bg-light text-dark float-end">Getaran Kasar</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="sensor-value-container">
                                <div class="sensor-value">0.0 <small>G</small></div>
                                <span class="sensor-status badge bg-secondary">Loading...</span>
                            </div>
                            <div class="threshold-indicator mt-3">
                                <div class="threshold-marker" style="left:50%" data-value="50">Waspada (≥50)</div>
                                <div class="threshold-marker" style="left:80%" data-value="80">Bahaya (≥80)</div>
                                <div class="threshold-bar">
                                    <div class="current-value" style="width: 0%"></div>
                                </div>
                            </div>
                            <div class="time-filter mt-4">
                                <button class="btn btn-sm btn-time active" data-hours="1">1 Jam</button>
                                <button class="btn btn-sm btn-time" data-hours="6">6 Jam</button>
                                <button class="btn btn-sm btn-time" data-hours="24">24 Jam</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Combined Chart Row -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card chart-card">
                        <div class="card-header bg-dark text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="card-title mb-0">
                                    <i class="fas fa-chart-line me-2"></i> Grafik Gabungan Sensor
                                </h3>
                                <div class="chart-controls">
                                    <button class="btn btn-sm btn-outline-light chart-range active" data-range="1">1 Jam</button>
                                    <button class="btn btn-sm btn-outline-light chart-range" data-range="6">6 Jam</button>
                                    <button class="btn btn-sm btn-outline-light chart-range" data-range="24">24 Jam</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="combinedChart"></canvas>
                            </div>
                            <div class="chart-loading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p>Memuat data grafik...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alert History Row -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-bell me-2"></i> Riwayat Alarm
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Waktu</th>
                                            <th>Sensor</th>
                                            <th>Nilai</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="alert-history">
                                        <!-- Data akan diisi oleh JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="dashboard-footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="mb-0">Sistem Monitoring Tsunami &copy; <?= date('Y') ?> | Versi <?= APP_VERSION ?></p>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/luxon@3.0.1"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-luxon@1.2.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="../assets/js/admin.js?v=<?= time() ?>"></script>
</body>
</html>