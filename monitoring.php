<?php
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Monitoring Tsunami</title>

  <!-- CSS -->
  <link rel="stylesheet" href="style/styles.css">        <!-- umum/global -->
  <link rel="stylesheet" href="style/monitoring.css">    <!-- khusus dashboard -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
</head>
<body>

<?php include 'php/navbar.php'; ?>

<!-- Header Tsunami Sistem -->
<div class="container py-5 text-center header-monitor">
  <h1 class="dashboard-title">SISTEM DETEKSI DINI <br> TSUNAMI</h1>
  <p class="dashboard-subtitle">Pekon Teluk Kiluan Negri</p>
</div>

<!-- === LOKASI SENSOR === -->
<div class="container mb-5">
  <h3 class="mb-4 text-center text-uppercase fw-bold" style="font-family: 'Poppins', sans-serif;">
    Lokasi Sensor
  </h3>

  <div class="card shadow-sm border-0">
    <div class="card-body p-0 position-relative">
      <div id="sensor-map" style="height: 480px; z-index: 0;"></div>

      <!-- Keterangan -->
      <div class="position-absolute top-0 end-0 m-3 bg-white shadow-sm rounded p-3" style="z-index: 1000;">
        <h6 class="mb-2"><i class="fas fa-map-marker-alt text-primary me-2"></i>Lokasi</h6>
        <div class="mb-1">
          <i class="fas fa-bolt text-warning me-2"></i> Sensor Getaran Kasar
        </div>
        <div class="mb-1">
          <i class="fas fa-ruler-combined text-danger me-2"></i> Sensor Getaran Halus
        </div>
        <div class="mb-1">
          <i class="fas fa-leaf text-success me-2"></i> Sensor Lingkungan
        </div>
      </div>
    </div>
    <div class="card-footer bg-light text-end">
      <button class="btn btn-sm btn-primary" onclick="tambahSensorManual()">
        <i class="fas fa-plus-circle me-1"></i> Tambah Sensor Manual
      </button>
    </div>
  </div>
</div>

<div class="row mb-4">
  <!-- Sensor Piezo -->
  <div class="col-md-6">
    <div class="card sensor-card border-0 shadow-sm p-3" style="border-radius: 18px; background: #f0faff;">
      <div class="d-flex align-items-center mb-3">
        <div class="icon-box me-3 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #4facfe, #00f2fe); width: 45px; height: 45px; border-radius: 50%;">
          <i class="fas fa-wave-square fa-lg text-white"></i>
        </div>
        <div>
          <h6 class="text-uppercase fw-bold mb-0" style="color: #017bb5; font-size: 0.95rem;">Getaran Kasar</h6>
          <small class="text-muted">Sensor Piezoelektrik</small>
        </div>
      </div>

      <div class="d-flex align-items-baseline mb-2">
        <h2 id="piezo-value" class="fw-bold text-primary me-1 mb-0" style="font-size: 1.8rem;">0</h2>
        <small class="text-muted">/100</small>
      </div>

      <div class="progress mb-2" style="height: 8px; border-radius: 10px; background-color: #d6ecff;">
        <div id="piezo-progress" class="progress-bar" style="width: 0%; background: linear-gradient(to right, #4facfe, #00f2fe);"></div>
      </div>

      <p class="mb-1 small">
        Status: <span id="piezo-status" class="fw-bold text-success">Normal</span>
      </p>

      <div class="text-end">
        <small><i class="fas fa-clock me-1 text-muted"></i><span id="piezo-timestamp">-</span></small>
        <i class="fas fa-history ms-2 text-primary" data-bs-toggle="modal" data-bs-target="#modalPiezo" style="cursor:pointer;"></i>
      </div>
    </div>

    <!-- Deskripsi di luar card -->
    <div class="mt-2 ps-1">
      <small class="text-dark d-block mb-1">
        <strong>Threshold:</strong> <span class="text-dark">Waspada ≥ 50, Bahaya ≥ 80</span>
      </small>
      <small class="text-muted">
        Sensor Piezo mendeteksi getaran kasar mendadak di dasar laut atau pelampung, sebagai indikator awal pergeseran lempeng yang dapat menyebabkan tsunami.
      </small>
    </div>
  </div>

  <!-- Grafik Piezo -->
  <div class="col-md-6">
    <div class="card shadow-sm border-0">
      <div class="card-header d-flex justify-content-between align-items-center bg-white">
        <span class="fw-bold">Grafik Getaran Kasar</span>
        <div class="btn-group btn-group-sm">
          <button class="btn btn-outline-secondary active" data-period="hour">1 Jam</button>
          <button class="btn btn-outline-secondary" data-period="day">1 Hari</button>
          <button class="btn btn-outline-secondary" data-period="week">1 Minggu</button>
        </div>
      </div>
      <div class="card-body">
        <canvas id="vibration-chart" height="230"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- === SENSOR GETARAN HALUS (MPU) === -->
<div class="row mb-5">
  <!-- Kartu Sensor -->
  <div class="col-md-6">
    <div class="card shadow-sm border-0" style="background-color: #f5fbff; border-radius: 18px;">
      <div class="card-body">
        <div class="d-flex align-items-center mb-3">
          <div class="rounded-circle d-flex justify-content-center align-items-center" style="width: 50px; height: 50px; background: linear-gradient(135deg, #e0c3fc, #8ec5fc);">
            <i class="fas fa-ruler-combined text-white fs-5"></i>
          </div>
          <div class="ms-3">
            <h6 class="mb-0 text-uppercase text-primary fw-bold" style="letter-spacing: 0.5px;">GETARAN HALUS</h6>
            <small class="text-muted">Sensor MPU6050</small>
          </div>
        </div>

        <!-- Nilai & Bar -->
        <h3 class="fw-bold text-primary"><span id="mpu-value">0</span> <small class="text-muted fs-6">/100</small></h3>
        <div class="progress mb-3" style="height: 6px;">
          <div id="mpu-progress" class="progress-bar bg-primary" style="width: 0%;"></div>
        </div>

        <!-- Status dan Time -->
        <p class="mb-1 small">Status: <span id="mpu-status" class="fw-bold text-success">Normal</span></p>
        <p class="mb-0 text-end">
          <i class="fas fa-clock text-muted me-2"></i><span id="mpu-timestamp" class="small">-</span>
        </p>
      </div>
    </div>

    <!-- Deskripsi (di luar card) -->
    <div class="mt-2 ps-2">
      <p class="mb-1 small text-muted"><strong>Threshold:</strong> <span class="text-dark">Waspada ≥ 40, Bahaya ≥ 70</span></p>
      <p class="small text-muted">
        Sensor MPU6050 mendeteksi getaran ringan atau pergerakan kecil sebagai indikasi awal aktivitas tektonik di bawah laut, berperan penting dalam mendeteksi potensi tsunami yang terjadi secara perlahan.
      </p>
    </div>
  </div>

  <!-- Grafik MPU -->
  <div class="col-md-6">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span><strong>Grafik Getaran Halus</strong></span>
        <div class="btn-group btn-group-sm">
          <button class="btn btn-outline-secondary active" data-mpu-period="hour">1 Jam</button>
          <button class="btn btn-outline-secondary" data-mpu-period="day">1 Hari</button>
          <button class="btn btn-outline-secondary" data-mpu-period="week">1 Minggu</button>
        </div>
      </div>
      <div class="card-body">
        <canvas id="mpu-chart" height="250"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- === SENSOR TEKANAN AIR (BME280) === -->
<div class="row mb-5">
  <!-- Kartu Sensor -->
  <div class="col-md-6">
    <div class="card shadow-sm border-0" style="background-color: #f5faff; border-radius: 18px;">
      <div class="card-body">
        <div class="d-flex align-items-center mb-3">
          <div class="rounded-circle d-flex justify-content-center align-items-center"
            style="width: 50px; height: 50px; background: linear-gradient(135deg, #89f7fe, #66a6ff);">
            <i class="fas fa-tint text-white fs-5"></i>
          </div>
          <div class="ms-3">
            <h6 class="mb-0 text-uppercase text-primary fw-bold" style="letter-spacing: 0.5px;">Tekanan Air</h6>
            <small class="text-muted">Sensor BME280</small>
          </div>
        </div>

        <!-- Nilai & Bar -->
        <h3 class="fw-bold text-primary"><span id="bme-value">0</span> <small class="text-muted fs-6">hPa</small></h3>
        <div class="progress mb-3" style="height: 6px;">
          <div id="bme-progress" class="progress-bar bg-info" style="width: 0%;"></div>
        </div>

        <!-- Status dan Time -->
        <p class="mb-1 small">Status: <span id="bme-status" class="fw-bold text-success">Normal</span></p>
        <p class="mb-0 text-end">
          <i class="fas fa-clock text-muted me-2"></i><span id="bme-timestamp" class="small">-</span>
        </p>
      </div>
    </div>

    <!-- Deskripsi -->
    <div class="mt-2 ps-2">
      <p class="mb-1 small text-muted"><strong>Threshold:</strong> <span class="text-dark">Waspada ≥ 1000 hPa, Bahaya ≥ 1020 hPa</span></p>
      <p class="small text-muted">
        Sensor BME280 memantau tekanan udara di atas permukaan laut. Perubahan signifikan bisa menandakan perbedaan tekanan akibat gelombang atau anomali perairan yang berpotensi tsunami.
      </p>
    </div>
  </div>

  <!-- Grafik BME280 -->
  <div class="col-md-6">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span><strong>Grafik Tekanan Air</strong></span>
        <div class="btn-group btn-group-sm">
          <button class="btn btn-outline-secondary active" data-bme-period="hour">1 Jam</button>
          <button class="btn btn-outline-secondary" data-bme-period="day">1 Hari</button>
          <button class="btn btn-outline-secondary" data-bme-period="week">1 Minggu</button>
        </div>
      </div>
      <div class="card-body">
        <canvas id="bme-chart" height="250"></canvas>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@1.2.1/dist/chartjs-plugin-zoom.min.js"></script>


<script>
  const map = L.map('sensor-map').setView([-5.5, 105.5], 6); // Sesuaikan lokasi awal

  // Tambahkan Tile Layer
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap'
  }).addTo(map);

  // Tambah Marker Sensor Getaran Kasar
  const kasarMarker = L.marker([-5.63, 105.55], {
    icon: L.divIcon({className: 'text-warning', html: '<i class="fas fa-bolt fa-lg"></i>'})
  }).addTo(map).bindPopup("Sensor Getaran Kasar");

  // Tambah Marker Sensor Getaran Halus
  const halusMarker = L.marker([-5.65, 105.52], {
    icon: L.divIcon({className: 'text-danger', html: '<i class="fas fa-ruler-combined fa-lg"></i>'})
  }).addTo(map).bindPopup("Sensor Getaran Halus");

  // Tambah Marker Sensor Lingkungan
  const lingkunganMarker = L.marker([-5.66, 105.50], {
    icon: L.divIcon({className: 'text-success', html: '<i class="fas fa-leaf fa-lg"></i>'})
  }).addTo(map).bindPopup("Sensor Lingkungan");

  // Fungsi untuk tambah sensor manual
  function tambahSensorManual() {
    map.once('click', function (e) {
      const marker = L.marker(e.latlng).addTo(map)
        .bindPopup("Sensor Baru")
        .openPopup();
    });
  }
</script>

<script>
  const ctx = document.getElementById('vibration-chart').getContext('2d');

  const vibrationChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: [],
      datasets: [{
        label: 'Intensitas Getaran',
        data: [],
        backgroundColor: 'rgba(79, 172, 254, 0.2)',
        borderColor: '#4facfe',
        borderWidth: 2,
        fill: true,
        tension: 0.4,
        pointRadius: 3,
        pointHoverRadius: 6,
        pointBackgroundColor: '#00c6ff'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      animation: {
        duration: 1000,
        easing: 'easeOutQuart'
      },
      plugins: {
        tooltip: {
          mode: 'index',
          intersect: false,
          backgroundColor: '#4facfe'
        },
        legend: {
          display: false
        },
        zoom: {
          zoom: {
            wheel: { enabled: true },
            pinch: { enabled: true },
            mode: 'x',
          },
          pan: {
            enabled: true,
            mode: 'x',
          },
        }
      },
      scales: {
        x: {
          title: {
            display: true,
            text: 'Waktu'
          }
        },
        y: {
          title: {
            display: true,
            text: 'Nilai Getaran'
          },
          min: 0,
          max: 100
        }
      }
    }
  });

  // Simulasi fetch data dari ESP32 atau backend
  function fetchPiezoData() {
    const now = new Date().toLocaleTimeString();
    const value = Math.floor(Math.random() * 100); // ganti dengan data asli nanti

    if (vibrationChart.data.labels.length > 20) {
      vibrationChart.data.labels.shift();
      vibrationChart.data.datasets[0].data.shift();
    }

    vibrationChart.data.labels.push(now);
    vibrationChart.data.datasets[0].data.push(value);
    vibrationChart.update();
  }

  setInterval(fetchPiezoData, 3000);
</script>

<script>
  const periodButtons = document.querySelectorAll('[data-period]');
  periodButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      periodButtons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      const period = btn.getAttribute('data-period');
      vibrationChart.data.labels = [];
      vibrationChart.data.datasets[0].data = [];

      // Buat data dummy sesuai waktu untuk demo
      const count = period === 'hour' ? 10 : period === 'day' ? 24 : 7;
      for (let i = 0; i < count; i++) {
        vibrationChart.data.labels.push(`${i + 1}`);
        vibrationChart.data.datasets[0].data.push(Math.floor(Math.random() * 100));
      }

      vibrationChart.update();
    });
  });
</script>

<script>
  const mpuCtx = document.getElementById('mpu-chart').getContext('2d');
  const mpuChart = new Chart(mpuCtx, {
    type: 'line',
    data: {
      labels: [],
      datasets: [{
        label: 'Getaran Halus',
        data: [],
        backgroundColor: 'rgba(142, 197, 252, 0.2)',
        borderColor: '#8ec5fc',
        borderWidth: 2,
        fill: true,
        tension: 0.4,
        pointRadius: 3,
        pointHoverRadius: 6,
        pointBackgroundColor: '#8ec5fc'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        tooltip: {
          mode: 'index',
          intersect: false,
        },
        legend: {
          display: false
        },
        zoom: {
          zoom: {
            wheel: { enabled: true },
            pinch: { enabled: true },
            mode: 'x',
          },
          pan: {
            enabled: true,
            mode: 'x',
          },
        }
      },
      scales: {
        x: {
          title: {
            display: true,
            text: 'Waktu'
          }
        },
        y: {
          title: {
            display: true,
            text: 'Intensitas'
          },
          min: 0,
          max: 100
        }
      }
    }
  });

<script>
function updateMPUData() {
  fetch('http://192.168.137.166/getAllData') // ganti IP sesuai IP ESP32
    .then(response => response.json())
    .then(data => {
      const value = data.mpu6050;
      const time = data.timestamp_mpu;

      document.getElementById("mpu-value").textContent = value;
      document.getElementById("mpu-timestamp").textContent = time;
      document.getElementById("mpu-progress").style.width = Math.min(value * 10, 100) + "%";

      const status = document.getElementById("mpu-status");
      if (value >= 70) {
        status.textContent = "Bahaya";
        status.className = "text-danger";
      } else if (value >= 40) {
        status.textContent = "Waspada";
        status.className = "text-warning";
      } else {
        status.textContent = "Normal";
        status.className = "text-success";
      }
    })
    .catch(error => console.error("Gagal fetch:", error));
}

// jalankan per 3 detik
setInterval(updateMPUData, 3000);
</script>

</body>
</html>