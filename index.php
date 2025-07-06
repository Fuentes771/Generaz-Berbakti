<?php
    // Configuration
    $esp_ip = "192.168.241.203";
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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pendeteksi Dini Tsunami Rinova</title>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="style/styles.css" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />

    <!-- Gauge JS -->
    <script src="https://cdn.jsdelivr.net/npm/gaugeJS/dist/gauge.min.js"></script>
    </head>
    <body>

    <!-- Include Navbar -->
    <?php include 'php/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section bg-primary text-white py-5">
        <div class="container py-4">
        <div class="row align-items-center">
            <div class="col-md-6">
            <h1 class="display-4 fw-bold">Tsunami Early Warning System</h1>
            <p class="lead">Real-time monitoring and detection system for tsunami warnings</p>
            <a href="monitoring.php" class="btn btn-light btn-lg mt-3">View Monitoring</a>
            </div>
            <div class="col-md-6">
            <img src="images/tsunami-warning.png" alt="Tsunami Warning System" class="img-fluid rounded" />
            </div>
        </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
        <div class="text-center mb-5">
            <h2>System Features</h2>
            <p class="lead text-muted">Advanced technology for early detection</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                <div class="feature-icon bg-primary bg-opacity-10 text-primary rounded-circle p-3 mb-3 mx-auto">
                    <i class="fas fa-bell fa-2x"></i>
                </div>
                <h5>Real-time Alerts</h5>
                <p class="text-muted">Instant notifications when potential tsunami vibrations are detected</p>
                </div>
            </div>
            </div>
            <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                <div class="feature-icon bg-success bg-opacity-10 text-success rounded-circle p-3 mb-3 mx-auto">
                    <i class="fas fa-chart-line fa-2x"></i>
                </div>
                <h5>Data Visualization</h5>
                <p class="text-muted">Interactive charts and graphs showing vibration patterns over time</p>
                </div>
            </div>
            </div>
            <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                <div class="feature-icon bg-info bg-opacity-10 text-info rounded-circle p-3 mb-3 mx-auto">
                    <i class="fas fa-database fa-2x"></i>
                </div>
                <h5>Historical Data</h5>
                <p class="text-muted">Access to past events and vibration records for analysis</p>
                </div>
            </div>
            </div>
        </div>
        </div>
    </section>

    <!-- Include Footer -->
    <?php include 'php/footer.php'; ?>

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
                url: 'php/get_data.php',
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
            $.get('php/get_logs.php', function(data) {
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