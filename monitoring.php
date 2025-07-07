<?php
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Monitoring Tsunami</title>

  <!-- CSS -->
  <link rel="stylesheet" href="style/styles.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>
<body>

<?php include 'php/navbar.php'; ?>

<div class="container py-4">
  <h2 class="mb-4 d-flex align-items-center">
    <i class="fas fa-wave-square me-2"></i> Monitoring Tsunami
    <span id="connection-status" class="badge bg-danger ms-auto animate__animated animate__pulse">Disconnected</span>
  </h2>

  <div class="row mb-4">
    <!-- Piezo Vibration Sensor -->
    <div class="col-md-4">
      <div class="card status-card">
        <div class="card-body text-center">
          <h5 class="card-title"><i class="fas fa-wave-square sensor-icon"></i> Getaran Kasar (Piezo)</h5>
          <div class="data-value" id="piezo-value">0</div>
          <div class="progress mt-2" style="height: 10px;">
            <div id="piezo-progress" class="progress-bar bg-gradient-success" role="progressbar" style="width: 0%"></div>
          </div>
          <small class="text-muted">Intensitas getaran</small>
        </div>
      </div>
    </div>

    <!-- MPU6050 Sensor -->
    <div class="col-md-4">
      <div class="card status-card">
        <div class="card-body text-center">
          <h5 class="card-title"><i class="fas fa-arrows-alt sensor-icon"></i> Getaran Halus (MPU6050)</h5>
          <div class="data-value" id="mpu-value">0</div>
          <div class="progress mt-2" style="height: 10px;">
            <div id="mpu-progress" class="progress-bar bg-gradient-warning" role="progressbar" style="width: 0%"></div>
          </div>
          <small class="text-muted">Akurasi tinggi</small>
        </div>
      </div>
    </div>

    <!-- Pressure, Temp, Humidity -->
    <div class="col-md-4">
      <div class="card status-card">
        <div class="card-body text-center">
          <h5 class="card-title"><i class="fas fa-thermometer-half sensor-icon"></i> Tekanan, Suhu & Kelembaban</h5>
          <div class="data-value small">
            <div class="d-flex justify-content-between mb-1">
              <span>Tekanan:</span>
              <span id="pressure-value" class="fw-bold">0</span> kPa
            </div>
            <div class="d-flex justify-content-between mb-1">
              <span>Suhu:</span>
              <span id="temperature-value" class="fw-bold">0</span> °C
            </div>
            <div class="d-flex justify-content-between">
              <span>Kelembaban:</span>
              <span id="humidity-value" class="fw-bold">0</span> %
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Grafik dan Peta -->
  <div class="row mb-4">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span>Aktivitas Gempa</span>
          <div class="btn-group btn-group-sm">
            <button class="btn btn-outline-secondary active" data-period="hour">1 Jam</button>
            <button class="btn btn-outline-secondary" data-period="day">1 Hari</button>
            <button class="btn btn-outline-secondary" data-period="week">1 Minggu</button>
          </div>
        </div>
        <div class="card-body">
          <canvas id="vibration-chart" height="250"></canvas>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span>Lokasi Sensor</span>
          <button id="map-legend-btn" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-layer-group"></i> Legenda
          </button>
        </div>
        <div class="card-body p-0">
          <div id="sensor-map" style="height: 300px;"></div>
          <div id="map-legend" class="p-3" style="display: none;">
            <h6 class="mb-2">Legenda Sensor:</h6>
            <div class="d-flex align-items-center mb-1">
              <div class="legend-color" style="background-color: #3498db; width: 15px; height: 15px; border-radius: 50%; margin-right: 8px;"></div>
              <span>Sensor Getaran Kasar</span>
            </div>
            <div class="d-flex align-items-center mb-1">
              <div class="legend-color" style="background-color: #e74c3c; width: 15px; height: 15px; border-radius: 50%; margin-right: 8px;"></div>
              <span>Sensor Getaran Halus</span>
            </div>
            <div class="d-flex align-items-center">
              <div class="legend-color" style="background-color: #2ecc71; width: 15px; height: 15px; border-radius: 50%; margin-right: 8px;"></div>
              <span>Sensor Lingkungan</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Riwayat Sensor -->
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span><i class="fas fa-history me-2"></i> Histori Sensor</span>
      <div>
        <button id="filter-logs" class="btn btn-sm btn-outline-secondary me-2">
          <i class="fas fa-filter"></i> Filter
        </button>
        <button id="refresh-logs" class="btn btn-sm btn-outline-secondary">
          <i class="fas fa-sync-alt"></i> Refresh
        </button>
      </div>
    </div>
    <div class="card-body p-0">
      <table class="table table-hover mb-0">
        <thead class="table-light">
          <tr>
            <th>Waktu</th>
            <th>Kejadian</th>
            <th>Intensitas</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody id="event-logs">
          <tr>
            <td colspan="5" class="text-center py-4">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Memuat...</span>
              </div>
              <div class="mt-2">Memuat histori sensor...</div>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="p-3 border-top">
        <nav aria-label="Log navigation">
          <ul class="pagination justify-content-center mb-0">
            <li class="page-item disabled">
              <a class="page-link" href="#" tabindex="-1">Previous</a>
            </li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item">
              <a class="page-link" href="#">Next</a>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </div>
</div>

<?php include 'php/footer.php'; ?>

<!-- Audio -->
<audio id="alert-sound" loop>
  <source src="assets/alert.mp3" type="audio/mpeg">
</audio>

<!-- JS Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/gaugeJS/dist/gauge.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="js/map.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  // Inisialisasi peta
  var map = L.map('sensor-map').setView([-5.45, 105.26], 9); // Koordinat awal di sekitar Lampung

  // Tambahkan peta dasar (OpenStreetMap)
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  // Data sensor (bisa diganti dari database nanti)
  const sensorData = [
    {
      name: "Sensor Piezo",
      type: "Getaran Kasar (Piezo)",
      lat: -5.626,
      lng: 105.563,
      color: '#3498db'
    },
    {
      name: "Sensor MPU6050",
      type: "Getaran Halus (MPU6050)",
      lat: -5.807,
      lng: 105.508,
      color: '#e74c3c'
    },
    {
      name: "Sensor DHT + Pressure",
      type: "Suhu, Tekanan, Kelembaban",
      lat: -5.446,
      lng: 105.309,
      color: '#2ecc71'
    }
  ];

  // Tampilkan marker di peta dengan custom icon
  sensorData.forEach(sensor => {
    const marker = L.circleMarker([sensor.lat, sensor.lng], {
      radius: 8,
      fillColor: sensor.color,
      color: "#fff",
      weight: 1,
      opacity: 1,
      fillOpacity: 0.8
    }).addTo(map);
    
    marker.bindPopup(`<b>${sensor.name}</b><br>${sensor.type}<br><small>Lat: ${sensor.lat.toFixed(4)}, Lng: ${sensor.lng.toFixed(4)}</small>`);
  });

  // Toggle legend
  document.getElementById('map-legend-btn').addEventListener('click', function() {
    const legend = document.getElementById('map-legend');
    if (legend.style.display === 'none') {
      legend.style.display = 'block';
      this.innerHTML = '<i class="fas fa-times"></i> Tutup';
    } else {
      legend.style.display = 'none';
      this.innerHTML = '<i class="fas fa-layer-group"></i> Legenda';
    }
  });

  // Simulate connection status change
  setTimeout(() => {
    document.getElementById('connection-status').classList.remove('bg-danger', 'animate__pulse');
    document.getElementById('connection-status').classList.add('bg-success');
    document.getElementById('connection-status').textContent = 'Connected';
    
    // Update some values for demo
    document.getElementById('piezo-value').textContent = '24';
    document.getElementById('piezo-progress').style.width = '24%';
    document.getElementById('mpu-value').textContent = '156';
    document.getElementById('mpu-progress').style.width = '52%';
    document.getElementById('pressure-value').textContent = '101.3';
    document.getElementById('temperature-value').textContent = '28.5';
    document.getElementById('humidity-value').textContent = '78';
    
    // Update logs table
    document.getElementById('event-logs').innerHTML = `
      <tr>
        <td>${new Date().toLocaleString()}</td>
        <td>Getaran terdeteksi</td>
        <td>Sedang (24)</td>
        <td><span class="badge bg-warning">Warning</span></td>
        <td><button class="btn btn-sm btn-outline-primary">Detail</button></td>
      </tr>
      <tr>
        <td>${new Date(Date.now() - 3600000).toLocaleString()}</td>
        <td>Pemantauan rutin</td>
        <td>Rendah (5)</td>
        <td><span class="badge bg-success">Normal</span></td>
        <td><button class="btn btn-sm btn-outline-primary">Detail</button></td>
      </tr>
      <tr>
        <td>${new Date(Date.now() - 86400000).toLocaleString()}</td>
        <td>Kalibrasi sensor</td>
        <td>-</td>
        <td><span class="badge bg-info">Maintenance</span></td>
        <td><button class="btn btn-sm btn-outline-primary">Detail</button></td>
      </tr>
    `;
  }, 2000);

  // Initialize chart
  const ctx = document.getElementById('vibration-chart').getContext('2d');
  const chart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: Array.from({length: 24}, (_, i) => `${i}:00`),
      datasets: [
        {
          label: 'Getaran Kasar (Piezo)',
          data: Array.from({length: 24}, () => Math.floor(Math.random() * 30)),
          borderColor: '#3498db',
          backgroundColor: 'rgba(52, 152, 219, 0.1)',
          tension: 0.4,
          fill: true
        },
        {
          label: 'Getaran Halus (MPU6050)',
          data: Array.from({length: 24}, () => Math.floor(Math.random() * 200)),
          borderColor: '#e74c3c',
          backgroundColor: 'rgba(231, 76, 60, 0.1)',
          tension: 0.4,
          fill: true
        }
      ]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'top',
        },
        tooltip: {
          mode: 'index',
          intersect: false,
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Intensitas'
          }
        },
        x: {
          title: {
            display: true,
            text: 'Waktu'
          }
        }
      }
    }
  });

  // Time period buttons
  document.querySelectorAll('[data-period]').forEach(btn => {
    btn.addEventListener('click', function() {
      document.querySelectorAll('[data-period]').forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      // Here you would update the chart data based on the selected period
    });
  });
</script>

</body>
</html>