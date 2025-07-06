<?php
// Configuration
$esp_ip = "192.168.241.203";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Monitoring | Tsunami Warning</title>

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

  <div class="container py-4">
    <h2 class="mb-4"><i class="fas fa-chart-line me-2"></i>System Monitoring</h2>

    <!-- Status Row -->
    <div class="row mb-4">
      <!-- Vibration Level -->
      <div class="col-md-4">
        <div class="card status-card">
          <div class="card-body text-center">
            <h5 class="card-title"><i class="fas fa-chart-line sensor-icon"></i> Kekuatan Gempa</h5>
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
            <h5 class="card-title"><i class="fas fa-info-circle sensor-icon"></i> Status Sistem</h5>
            <div class="data-value flex-grow-1 d-flex align-items-center justify-content-center">
              <div id="system-status" class="status-normal px-4 py-2 rounded-pill">Normal</div>
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
            <h5 class="card-title"><i class="fas fa-bell sensor-icon"></i> Status Peringatan</h5>
            <div class="data-value flex-grow-1 d-flex align-items-center justify-content-center">
              <div id="alert-status" class="px-4 py-2 rounded-pill bg-secondary text-white">Tidak Aktif</div>
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
            <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i> Aktivitas Gempa</h5>
          </div>
          <div class="card-body">
            <canvas id="vibration-chart" height="250"></canvas>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card h-100">
          <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-map-marked-alt me-2"></i> Lokasi Sensor</h5>
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
            <h5 class="mb-0"><i class="fas fa-history me-2"></i> Histori Sensor</h5>
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
                    <th>Intensitas</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="event-logs">
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

  <!-- Include Footer -->
  <?php include 'php/footer.php'; ?>

  <!-- Last Update Indicator -->
  <div class="last-update" id="last-update">
    <i class="fas fa-clock me-1"></i> Updating...
  </div>

  <!-- Audio Element for Alerts -->
  <audio id="alert-sound" loop>
    <source src="alert.mp3" type="audio/mpeg" />
  </audio>

  <!-- JavaScript Libraries -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- Monitoring Script -->
  <script>
    const espIp = "<?php echo $esp_ip; ?>";
    let vibrationChart, vibrationGauge, isAlertActive = false;
    let updateInterval = 2000;
    let dataHistory = [];
    const maxHistory = 20;

    function initVisualizations() {
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
        highDpiSupport: true
      });
      vibrationGauge.maxValue = 10;
      vibrationGauge.setMinValue(0);
      vibrationGauge.animationSpeed = 32;
      vibrationGauge.set(0);

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

    function fetchData() {
      $.ajax({
        url: 'php/get_data.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
          if (data.error) {
            $('#connection-status').removeClass('bg-success').addClass('bg-danger').html('<i class="fas fa-exclamation-circle"></i> Disconnected');
            return;
          }

          $('#connection-status').removeClass('bg-danger').addClass('bg-success').html('<i class="fas fa-circle"></i> Connected');
          $('#vibration-value').text(data.vibration);
          vibrationGauge.set(data.vibration);
          $('#vibration-progress').css('width', data.vibration * 10 + '%');

          const statusElement = $('#system-status').removeClass('status-normal status-warning status-danger');
          if (data.status === 'Normal') {
            statusElement.addClass('status-normal').text('Normal');
          } else if (data.status === 'Warning') {
            statusElement.addClass('status-warning').text('Warning');
          } else {
            statusElement.addClass('status-danger').text('Danger');
          }

          const alertElement = $('#alert-status');
          if (data.alert) {
            alertElement.removeClass('bg-secondary').addClass('bg-danger').text('ACTIVE');
            $('#alert-card').addClass('alert-active');
            $('#silence-btn').show();
            if (!isAlertActive) {
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

          updateHistory(data);
          $('#last-update').html(`<i class="fas fa-clock me-1"></i> Last update: ${new Date().toLocaleTimeString()}`);
          $('#last-analysis').text('Just now');

          updateInterval = data.status === 'Normal' ? 2000 : 1000;
        },
        error: function(xhr, status, error) {
          $('#connection-status').removeClass('bg-success').addClass('bg-danger')
            .html('<i class="fas fa-exclamation-circle"></i> Connection Error');
        },
        complete: function() {
          setTimeout(fetchData, updateInterval);
        }
      });
    }

    function updateHistory(data) {
      dataHistory.push({
        time: new Date().toLocaleTimeString(),
        vibration: data.vibration
      });
      if (dataHistory.length > maxHistory) dataHistory.shift();
      vibrationChart.data.labels = dataHistory.map(item => item.time);
      vibrationChart.data.datasets[0].data = dataHistory.map(item => item.vibration);
      vibrationChart.update();
    }

    function loadEventLogs() {
      $.get('php/get_logs.php', function(data) {
        $('#event-logs').html(data);
      });
    }

    function silenceAlarm() {
      $.get(`http://${espIp}/alert?state=off`, function() {
        console.log('Alarm silenced');
      }).fail(function() {
        console.error('Failed to silence alarm');
      });
    }

    $(document).ready(function() {
      initVisualizations();
      fetchData();
      loadEventLogs();

      $('#refresh-logs').click(function() {
        loadEventLogs();
        $(this).html('<i class="fas fa-sync-alt fa-spin"></i> Refreshing...');
        setTimeout(() => {
          $(this).html('<i class="fas fa-sync-alt"></i> Refresh');
        }, 1000);
      });

      $('#silence-btn').click(function() {
        silenceAlarm();
        $(this).html('<i class="fas fa-check"></i> Silenced').prop('disabled', true);
      });

      setInterval(loadEventLogs, 30000);
    });
  </script>
</body>
</html>
