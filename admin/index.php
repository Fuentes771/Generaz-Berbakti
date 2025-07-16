<?php
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
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/admin.css?v=<?= time() ?>">
    
    <!-- Favicon -->
    <link rel="icon" href="../assets/img/favicon.ico" type="image/x-icon">
</head>
<body class="admin-dashboard">
    <!-- Header -->
    <header class="dashboard-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center py-2">
                <div class="d-flex align-items-center">
                    <i class="fas fa-water me-3 fs-3 text-primary"></i>
                    <h1 class="dashboard-title mb-0">Monitoring Real-time Sensor Tsunami</h1>
                </div>
                <div class="header-controls">
                    <span class="update-time me-3">
                        <i class="fas fa-clock me-1"></i> 
                        <strong id="update-time">--:--:--</strong>
                    </span>
                    <button class="btn btn-sm btn-outline-primary me-2" id="refresh-btn">
                        <i class="fas fa-sync-alt me-1"></i> Refresh
                    </button>
                    <div class="dropdown d-inline-block">
                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
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
    </header>

    <!-- Main Content -->
    <main class="dashboard-content container-fluid py-4">
        <!-- Sensor Cards Row -->
        <div class="row row-cols-1 row-cols-md-3 g-4 mb-4">
            <!-- MPU6050 Card -->
            <div class="col">
                <div class="card sensor-card h-100 border-0 shadow-sm" id="mpu-card">
                    <div class="card-header bg-primary text-white rounded-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-vibration me-2"></i> MPU6050
                            </h3>
                            <span class="badge bg-light text-dark">Getaran Halus</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="sensor-value-container text-center mb-3">
                            <div class="sensor-value">0.0 <small>m/s²</small></div>
                            <span class="sensor-status badge bg-secondary">Loading...</span>
                        </div>
                        <div class="threshold-indicator">
                            <div class="d-flex justify-content-between mb-1">
                                <small>Normal</small>
                                <small>Bahaya</small>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">0</small>
                                <small class="text-muted">100</small>
                            </div>
                        </div>
                        <div class="time-filter mt-4 d-flex justify-content-center gap-2">
                            <button class="btn btn-sm btn-time active" data-hours="1">1 Jam</button>
                            <button class="btn btn-sm btn-time" data-hours="6">6 Jam</button>
                            <button class="btn btn-sm btn-time" data-hours="24">24 Jam</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BME280 Card -->
            <div class="col">
                <div class="card sensor-card h-100 border-0 shadow-sm" id="bme-card">
                    <div class="card-header bg-info text-white rounded-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-tachometer-alt me-2"></i> BME280
                            </h3>
                            <span class="badge bg-light text-dark">Tekanan Udara</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="sensor-value-container text-center mb-3">
                            <div class="sensor-value">0.0 <small>hPa</small></div>
                            <span class="sensor-status badge bg-secondary">Loading...</span>
                        </div>
                        <div class="threshold-indicator">
                            <div class="d-flex justify-content-between mb-1">
                                <small>Normal</small>
                                <small>Bahaya</small>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">0</small>
                                <small class="text-muted">100</small>
                            </div>
                        </div>
                        <div class="time-filter mt-4 d-flex justify-content-center gap-2">
                            <button class="btn btn-sm btn-time active" data-hours="1">1 Jam</button>
                            <button class="btn btn-sm btn-time" data-hours="6">6 Jam</button>
                            <button class="btn btn-sm btn-time" data-hours="24">24 Jam</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Piezoelektrik Card -->
            <div class="col">
                <div class="card sensor-card h-100 border-0 shadow-sm" id="piezo-card">
                    <div class="card-header bg-warning text-dark rounded-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-wave-square me-2"></i> Piezoelektrik
                            </h3>
                            <span class="badge bg-light text-dark">Getaran Kasar</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="sensor-value-container text-center mb-3">
                            <div class="sensor-value">0.0 <small>G</small></div>
                            <span class="sensor-status badge bg-secondary">Loading...</span>
                        </div>
                        <div class="threshold-indicator">
                            <div class="d-flex justify-content-between mb-1">
                                <small>Normal</small>
                                <small>Bahaya</small>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">0</small>
                                <small class="text-muted">100</small>
                            </div>
                        </div>
                        <div class="time-filter mt-4 d-flex justify-content-center gap-2">
                            <button class="btn btn-sm btn-time active" data-hours="1">1 Jam</button>
                            <button class="btn btn-sm btn-time" data-hours="6">6 Jam</button>
                            <button class="btn btn-sm btn-time" data-hours="24">24 Jam</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Combined Chart Row -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-chart-line text-primary me-2"></i> Grafik Gabungan Sensor
                            </h3>
                            <div class="chart-controls">
                                <button class="btn btn-sm btn-outline-primary chart-range active" data-range="1">1 Jam</button>
                                <button class="btn btn-sm btn-outline-primary chart-range" data-range="6">6 Jam</button>
                                <button class="btn btn-sm btn-outline-primary chart-range" data-range="24">24 Jam</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="chart-container" style="height: 400px;">
                            <canvas id="combinedChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert History Row -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-bell text-danger me-2"></i> Riwayat Alarm
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Waktu</th>
                                        <th>Sensor</th>
                                        <th>Nilai</th>
                                        <th>Status</th>
                                        <th class="pe-4 text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="alert-history">
                                    <tr>
                                        <td colspan="5" class="text-center py-4">Memuat data alarm...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="dashboard-footer bg-light py-3 mt-4 border-top">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="mb-0 text-muted small">Sistem Monitoring Tsunami &copy; <?= date('Y') ?> | Versi <?= APP_VERSION ?></p>
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