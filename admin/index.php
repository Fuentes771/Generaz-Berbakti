<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Real-time Sensor Tsunami</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/cards.css">
    <link rel="stylesheet" href="css/charts.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <h1 class="site-title">
                    <i class="fas fa-water"></i> Monitoring Real-time Sensor Tsunami
                </h1>
                <div class="header-controls">
                    <span class="update-time">
                        <i class="fas fa-clock"></i> <strong id="update-time">05:30:45</strong>
                    </span>
                    <button class="refresh-btn">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>
        </div>
    </header>
    
    <main class="container main-content">
        <div class="sensor-grid">
            <!-- Sensor MPU6050 -->
            <div class="sensor-card mpu-card">
                <div class="sensor-header">
                    <h3><i class="fas fa-vibration"></i> MPU6050</h3>
                    <p class="sensor-description">Sensor Getaran Halus</p>
                </div>
                <div class="sensor-body">
                    <div class="sensor-value">3.2 <small>m/s²</small></div>
                    <span class="sensor-status normal">Normal</span>
                    
                    <div class="threshold-indicator">
                        <div class="threshold-marker" style="left:40%">Waspada (≥40)</div>
                        <div class="threshold-marker" style="left:70%">Bahaya (≥70)</div>
                    </div>
                    
                    <div class="time-filter">
                        <button class="time-btn active">1 Jam</button>
                        <button class="time-btn">6 Jam</button>
                        <button class="time-btn">24 Jam</button>
                    </div>
                </div>
            </div>
            
            <!-- Sensor BME280 -->
            <div class="sensor-card bme-card">
                <div class="sensor-header">
                    <h3><i class="fas fa-tachometer-alt"></i> BME280</h3>
                    <p class="sensor-description">Sensor Tekanan Udara</p>
                </div>
                <div class="sensor-body">
                    <div class="sensor-value">1013.2 <small>hPa</small></div>
                    <span class="sensor-status normal">Normal</span>
                    
                    <div class="threshold-indicator">
                        <div class="threshold-marker" style="left:60%">Waspada (≥60)</div>
                        <div class="threshold-marker" style="left:85%">Bahaya (≥85)</div>
                    </div>
                    
                    <div class="time-filter">
                        <button class="time-btn active">1 Jam</button>
                        <button class="time-btn">6 Jam</button>
                        <button class="time-btn">24 Jam</button>
                    </div>
                </div>
            </div>
            
            <!-- Sensor Piezoelektrik -->
            <div class="sensor-card piezo-card">
                <div class="sensor-header">
                    <h3><i class="fas fa-wave-square"></i> Piezoelektrik</h3>
                    <p class="sensor-description">Sensor Getaran Kasar</p>
                </div>
                <div class="sensor-body">
                    <div class="sensor-value">0.8 <small>G</small></div>
                    <span class="sensor-status normal">Normal</span>
                    
                    <div class="threshold-indicator">
                        <div class="threshold-marker" style="left:50%">Waspada (≥50)</div>
                        <div class="threshold-marker" style="left:80%">Bahaya (≥80)</div>
                    </div>
                    
                    <div class="time-filter">
                        <button class="time-btn active">1 Jam</button>
                        <button class="time-btn">6 Jam</button>
                        <button class="time-btn">24 Jam</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Combined Chart -->
        <div class="combined-chart">
            <h3 class="chart-title"><i class="fas fa-chart-line"></i> Grafik Gabungan Sensor</h3>
            <div class="chart-container" id="combinedChart"></div>
            <div class="chart-legend">
                <div class="legend-item">
                    <span class="legend-color mpu"></span>
                    <span class="legend-label">MPU6050</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color bme"></span>
                    <span class="legend-label">BME280</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color piezo"></span>
                    <span class="legend-label">Piezoelektrik</span>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="js/admin.js"></script>
</body>
</html>