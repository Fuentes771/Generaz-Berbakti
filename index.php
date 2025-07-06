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
    
    <style>
        :root {
            --color-normal: #28a745;
            --color-warning: #ffc107;
            --color-danger: #dc3545;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 25px 0;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .status-card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: none;
            margin-bottom: 24px;
            overflow: hidden;
        }
        
        .status-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        }
        
        .card-title {
            font-weight: 600;
            color: #495057;
        }
        
        .gauge-container {
            height: 200px;
            margin: 20px auto;
            position: relative;
        }
        
        .status-normal {
            background-color: var(--color-normal);
            color: white;
        }
        
        .status-warning {
            background-color: var(--color-warning);
            color: #212529;
        }
        
        .status-danger {
            background-color: var(--color-danger);
            color: white;
        }
        
        .alert-active {
            animation: pulse 1.5s infinite;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
            70% { box-shadow: 0 0 0 12px rgba(220, 53, 69, 0); }
            100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
        }
        
        .map-container {
            height: 300px;
            background-color: #e9ecef;
            border-radius: 10px;
            margin-bottom: 20px;
            background-image: url('map-placeholder.jpg');
            background-size: cover;
            background-position: center;
        }
        
        .data-value {
            font-size: 2.2rem;
            font-weight: 700;
            margin: 10px 0;
        }
        
        .last-update {
            position: fixed;
            bottom: 15px;
            right: 15px;
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            z-index: 1000;
        }
        
        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .sensor-icon {
            font-size: 1.5rem;
            margin-right: 10px;
        }
        /* Footer Styles */
        .footer {
            background-color: #343a40;
            color: white;
            padding: 30px 0 20px;
            margin-top: 50px;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .footer-link {
            color: rgba(255, 255, 255, 0.7);
            margin: 0 15px;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-link:hover {
            color: white;
            text-decoration: none;
        }

        .copyright {
            text-align: center;
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.9rem;
            margin-top: 15px;
        }

        .footer-brand {
            font-weight: 600;
            font-size: 1.2rem;
            margin-bottom: 15px;
            display: block;
            text-align: center;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .social-icon {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 8px;
            transition: all 0.3s;
        }

        .social-icon:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
        }
    </style>
</head>
<body>
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="fas fa-water me-2"></i> Sistem Pedeteksi Dini Tsunami</h1>
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
    
    <script>
        // Global variables
        const espIp = "<?php echo $esp_ip; ?>";
        let vibrationChart;
        let vibrationGauge;
        let isAlertActive = false;
        let updateInterval = 2000; // 2 seconds
        let dataHistory = [];
        const maxHistory = 20;
        
        // Initialize gauges and charts
        function initVisualizations() {
            // Vibration Gauge
            const gaugeTarget = document.getElementById('vibration-gauge');
            vibrationGauge = new Gauge(gaugeTarget).setOptions({
                angle: 0,
                lineWidth: 0.3,
                radiusScale: 1,
                pointer: {
                    length: 0.6,
                    strokeWidth: 0.05,
                    color: '#000000'
                },
                staticZones: [
                    {strokeStyle: "#28a745", min: 0, max: 4},
                    {strokeStyle: "#ffc107", min: 5, max: 7},
                    {strokeStyle: "#dc3545", min: 8, max: 10}
                ],
                limitMax: false,
                limitMin: false,
                highDpiSupport: true
            });
            vibrationGauge.maxValue = 10;
            vibrationGauge.setMinValue(0);
            vibrationGauge.animationSpeed = 32;
            vibrationGauge.set(0);
            
            // Vibration Chart
            const ctx = document.getElementById('vibration-chart').getContext('2d');
            vibrationChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Vibration Level',
                        data: [],
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 10
                        }
                    },
                    animation: {
                        duration: 0
                    }
                }
            });
        }
        
        // Fetch data from server
        function fetchData() {
            $.ajax({
                url: 'get_data.php',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.error) {
                        console.error('Error:', data.error);
                        $('#connection-status').removeClass('bg-success').addClass('bg-danger')
                            .html('<i class="fas fa-exclamation-circle"></i> Disconnected');
                        return;
                    }
                    
                    // Update connection status
                    $('#connection-status').removeClass('bg-danger').addClass('bg-success')
                        .html('<i class="fas fa-circle"></i> Connected');
                    
                    // Update vibration display
                    $('#vibration-value').text(data.vibration);
                    vibrationGauge.set(data.vibration);
                    $('#vibration-progress').css('width', data.vibration * 10 + '%');
                    
                    // Update status display
                    const statusElement = $('#system-status');
                    statusElement.removeClass('status-normal status-warning status-danger');
                    
                    if (data.status === 'Normal') {
                        statusElement.addClass('status-normal').text('Normal');
                    } else if (data.status === 'Warning') {
                        statusElement.addClass('status-warning').text('Warning');
                    } else {
                        statusElement.addClass('status-danger').text('Danger');
                    }
                    
                    // Update alert display
                    const alertElement = $('#alert-status');
                    if (data.alert) {
                        alertElement.removeClass('bg-secondary').addClass('bg-danger').text('ACTIVE');
                        $('#alert-card').addClass('alert-active');
                        $('#silence-btn').show();
                        
                        if (!isAlertActive) {
                            // Trigger alert sound
                            document.getElementById('alert-sound').play().catch(e => console.log('Audio error:', e));
                            isAlertActive = true;
                        }
                    } else {
                        alertElement.removeClass('bg-danger').addClass('bg-secondary').text('Inactive');
                        $('#alert-card').removeClass('alert-active');
                        $('#silence-btn').hide();
                        
                        if (isAlertActive) {
                            document.getElementById('alert-sound').pause();
                            isAlertActive = false;
                        }
                    }
                    
                    // Update history data
                    updateHistory(data);
                    
                    // Update timestamp
                    const now = new Date();
                    $('#last-update').html(`<i class="fas fa-clock me-1"></i> Last update: ${now.toLocaleTimeString()}`);
                    $('#last-analysis').text('Just now');
                    
                    // Adjust update interval based on status
                    updateInterval = data.status === 'Normal' ? 2000 : 1000;
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    $('#connection-status').removeClass('bg-success').addClass('bg-danger')
                        .html('<i class="fas fa-exclamation-circle"></i> Connection Error');
                },
                complete: function() {
                    // Schedule next update
                    setTimeout(fetchData, updateInterval);
                }
            });
        }
        
        // Update history data for chart
        function updateHistory(data) {
            // Add to history array
            dataHistory.push({
                time: new Date().toLocaleTimeString(),
                vibration: data.vibration
            });
            
            // Keep only the last maxHistory items
            if (dataHistory.length > maxHistory) {
                dataHistory.shift();
            }
            
            // Update chart
            vibrationChart.data.labels = dataHistory.map(item => item.time);
            vibrationChart.data.datasets[0].data = dataHistory.map(item => item.vibration);
            vibrationChart.update();
        }
        
        // Load event logs
        function loadEventLogs() {
            $.get('get_logs.php', function(data) {
                $('#event-logs').html(data);
            });
        }
        
        // Silence alarm
        function silenceAlarm() {
            $.get(`http://${espIp}/alert?state=off`, function() {
                console.log('Alarm silenced');
            }).fail(function() {
                console.error('Failed to silence alarm');
            });
        }
        
        // Initialize when page loads
        $(document).ready(function() {
            initVisualizations();
            
            // Start data updates
            fetchData();
            
            // Load initial logs
            loadEventLogs();
            
            // Set up log refresh
            $('#refresh-logs').click(function() {
                loadEventLogs();
                $(this).html('<i class="fas fa-sync-alt fa-spin"></i> Refreshing...');
                setTimeout(() => {
                    $(this).html('<i class="fas fa-sync-alt"></i> Refresh');
                }, 1000);
            });
            
            // Set up silence button
            $('#silence-btn').click(function() {
                silenceAlarm();
                $(this).html('<i class="fas fa-check"></i> Silenced').prop('disabled', true);
            });
            
            // Set up periodic log refresh (every 30 seconds)
            setInterval(loadEventLogs, 30000);
        });

        // Update copyright year automatically
        document.getElementById('current-year').textContent = new Date().getFullYear();
    </script>
</body>
</html>