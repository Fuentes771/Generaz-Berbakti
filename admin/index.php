<?php
// Start session dan include config jika diperlukan
require_once '../includes/config.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Real-time Sensor Tsunami</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    
    <!-- Favicon -->
    <link rel="icon" href="assets/img/favicon.ico" type="image/x-icon">
    
    <!-- Metadata -->
    <meta name="description" content="Sistem monitoring real-time sensor tsunami">
    <meta name="keywords" content="tsunami, monitoring, sensor, early warning">
</head>
<body>
    <!-- Header Section -->
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <h1 class="site-title">
                    <i class="fas fa-water"></i> Monitoring Real-time Sensor Tsunami
                </h1>
                <div class="header-controls">
                    <span class="update-time">
                        <i class="fas fa-clock"></i> <strong id="update-time">--:--:--</strong>
                    </span>
                    <button class="refresh-btn" id="refresh-btn">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="container main-content">
        <div class="sensor-grid">
            <?php
            // Data sensor dalam array untuk memudahkan pengelolaan
            $sensors = [
                [
                    'id' => 'mpu',
                    'icon' => 'vibration',
                    'name' => 'MPU6050',
                    'description' => 'Sensor Getaran Halus',
                    'unit' => 'm/s²',
                    'value' => '3.2',
                    'status' => 'normal',
                    'thresholds' => [
                        ['position' => '40%', 'label' => 'Waspada (≥40)'],
                        ['position' => '70%', 'label' => 'Bahaya (≥70)']
                    ]
                ],
                [
                    'id' => 'bme',
                    'icon' => 'tachometer-alt',
                    'name' => 'BME280',
                    'description' => 'Sensor Tekanan Udara',
                    'unit' => 'hPa',
                    'value' => '1013.2',
                    'status' => 'normal',
                    'thresholds' => [
                        ['position' => '60%', 'label' => 'Waspada (≥60)'],
                        ['position' => '85%', 'label' => 'Bahaya (≥85)']
                    ]
                ],
                [
                    'id' => 'piezo',
                    'icon' => 'wave-square',
                    'name' => 'Piezoelektrik',
                    'description' => 'Sensor Getaran Kasar',
                    'unit' => 'G',
                    'value' => '0.8',
                    'status' => 'normal',
                    'thresholds' => [
                        ['position' => '50%', 'label' => 'Waspada (≥50)'],
                        ['position' => '80%', 'label' => 'Bahaya (≥80)']
                    ]
                ]
            ];
            
            // Generate sensor cards dari array
            foreach ($sensors as $sensor) {
                echo '<div class="sensor-card ' . $sensor['id'] . '-card">';
                echo '  <div class="sensor-header">';
                echo '    <h3><i class="fas fa-' . $sensor['icon'] . '"></i> ' . $sensor['name'] . '</h3>';
                echo '    <p class="sensor-description">' . $sensor['description'] . '</p>';
                echo '  </div>';
                echo '  <div class="sensor-body">';
                echo '    <div class="sensor-value">' . $sensor['value'] . ' <small>' . $sensor['unit'] . '</small></div>';
                echo '    <span class="sensor-status ' . $sensor['status'] . '">' . ucfirst($sensor['status']) . '</span>';
                echo '    <div class="threshold-indicator">';
                
                foreach ($sensor['thresholds'] as $threshold) {
                    echo '      <div class="threshold-marker" style="left:' . $threshold['position'] . '">' . $threshold['label'] . '</div>';
                }
                
                echo '    </div>';
                echo '    <div class="time-filter">';
                echo '      <button class="time-btn active">1 Jam</button>';
                echo '      <button class="time-btn">6 Jam</button>';
                echo '      <button class="time-btn">24 Jam</button>';
                echo '    </div>';
                echo '  </div>';
                echo '</div>';
            }
            ?>
        </div>
        
        <!-- Combined Chart Section -->
        <div class="combined-chart">
            <h3 class="chart-title"><i class="fas fa-chart-line"></i> Grafik Gabungan Sensor</h3>
            <canvas id="combinedChart"></canvas>
            <div class="chart-legend">
                <?php
                $legendItems = [
                    ['class' => 'mpu', 'label' => 'MPU6050'],
                    ['class' => 'bme', 'label' => 'BME280'],
                    ['class' => 'piezo', 'label' => 'Piezoelektrik']
                ];
                
                foreach ($legendItems as $item) {
                    echo '<div class="legend-item">';
                    echo '  <span class="legend-color ' . $item['class'] . '"></span>';
                    echo '  <span class="legend-label">' . $item['label'] . '</span>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </main>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/admin.js"></script>
</body>
</html>