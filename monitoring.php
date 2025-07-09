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
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
</head>
<body>

<?php include 'php/navbar.php'; ?>

<!-- Header Tsunami Sistem -->
<div class="container py-5 text-center header-monitor">
  <h1 class="dashboard-title">SISTEM DETEKSI DINI <br> TSUNAMI</h1>
  <p class="dashboard-subtitle">Pekon Teluk Kiluan Negri</p>
</div>

<!-- === RINGKASAN MONITORING === -->
<div class="container mb-4">
  <div class="row g-4 text-center">
    <!-- Status Umum -->
    <div class="col-md-4">
      <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, #dbeafe, #eff6ff);">
        <div class="card-body">
          <i class="fas fa-shield-alt fa-2x text-primary mb-2"></i>
          <h6 class="text-uppercase fw-bold mb-1">Status Umum</h6>
          <span id="status-umum" class="badge bg-success fs-6 px-3 py-1">AMAN</span>
        </div>
      </div>
    </div>

    <!-- Ringkasan Sensor Aktif -->
    <div class="col-md-4">
      <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, #ecfdf5, #f0fdfa);">
        <div class="card-body">
          <i class="fas fa-microchip fa-2x text-success mb-2"></i>
          <h6 class="text-uppercase fw-bold mb-1">Sensor Aktif</h6>
          <span class="fs-5 fw-bold"><span id="jumlah-sensor">3</span> Sensor</span>
        </div>
      </div>
    </div>

    <!-- Waktu Terakhir Update -->
    <div class="col-md-4">
      <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, #fef3c7, #fefce8);">
        <div class="card-body">
          <i class="fas fa-clock fa-2x text-warning mb-2"></i>
          <h6 class="text-uppercase fw-bold mb-1">Update Terakhir</h6>
          <span id="waktu-update" class="fs-6 text-dark">-</span>
        </div>
      </div>
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

<!-- === LOKASI SENSOR === -->
<div class="container mb-5">
  <h3 class="mb-4 text-center text-uppercase fw-bold" style="font-family: 'Poppins', sans-serif;">
    Lokasi Sensor
  </h3>

  <div class="row">
    <!-- Kolom Peta -->
    <div class="col-md-6">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body p-0 position-relative">
          <div id="sensor-map" style="height: 480px;"></div>
        </div>
      </div>
    </div>

    <!-- Kolom Detail Sensor -->
    <div class="col-md-6">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body">
          <h5 class="mb-3"><i class="fas fa-info-circle text-primary me-2"></i>Detail Keterangan Sensor</h5>

          <div class="mb-3 d-flex align-items-start">
            <i class="fas fa-bolt text-warning fa-lg me-3 mt-1"></i>
            <div>
              <h6 class="fw-bold mb-1">Sensor Getaran Kasar</h6>
              <p class="small text-muted mb-0">Sensor Piezoelektrik untuk deteksi getaran kuat di laut dalam.</p>
            </div>
          </div>

          <div class="mb-3 d-flex align-items-start">
            <i class="fas fa-ruler-combined text-danger fa-lg me-3 mt-1"></i>
            <div>
              <h6 class="fw-bold mb-1">Sensor Getaran Halus</h6>
              <p class="small text-muted mb-0">MPU6050 untuk memantau getaran ringan bawah laut.</p>
            </div>
          </div>

          <div class="mb-3 d-flex align-items-start">
            <i class="fas fa-leaf text-success fa-lg me-3 mt-1"></i>
            <div>
              <h6 class="fw-bold mb-1">Sensor Lingkungan</h6>
              <p class="small text-muted mb-0">BME280 membaca tekanan, suhu, dan kelembaban laut.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card shadow-sm mt-4">
  <div class="card-header">
    <strong>Simulasi Tsunami (Video Edukasi)</strong>
  </div>
  <div class="card-body text-center">
    <video id="tsunamiVideo" width="100%" height="auto" controls>
      <source src="video/simulasi-tsunami.mp4" type="video/mp4">
      Browser Anda tidak mendukung pemutar video.
    </video>
  </div>
</div>

<!-- === RIWAYAT SENSOR === -->
<div class="container my-5">
  <div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0">
        <i class="fas fa-history me-2 text-primary"></i> Riwayat Sensor
      </h5>
      <div>
        <button id="filter-logs" class="btn btn-sm btn-outline-primary me-2">
          <i class="fas fa-filter"></i> Filter
        </button>
        <button id="refresh-logs" class="btn btn-sm btn-outline-primary">
          <i class="fas fa-sync-alt"></i> Refresh
        </button>
      </div>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
          <thead class="table-light text-center">
            <tr>
              <th>Waktu</th>
              <th>Sensor</th>
              <th>Intensitas</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="event-logs" class="text-center align-middle">
            <!-- Data akan dimuat via JS -->
            <tr>
              <td colspan="5" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Memuat...</span>
                </div>
                <div class="mt-2 text-muted">Memuat histori sensor...</div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="card-footer bg-light d-flex justify-content-between align-items-center">
      <small class="text-muted">Menampilkan maksimum 10 histori terakhir</small>
      <nav>
        <ul class="pagination pagination-sm mb-0">
          <li class="page-item disabled"><a class="page-link" href="#">Sebelumnya</a></li>
          <li class="page-item active"><a class="page-link" href="#">1</a></li>
          <li class="page-item"><a class="page-link" href="#">2</a></li>
          <li class="page-item"><a class="page-link" href="#">3</a></li>
          <li class="page-item"><a class="page-link" href="#">Selanjutnya</a></li>
        </ul>
      </nav>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="js/monitoring.js"></script>

<script>
  const map = L.map('sensor-map').setView([-5.5, 105.5], 8); // Sesuaikan titik tengah

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap'
  }).addTo(map);

  // === Marker Koordinat Tetap ===
  const sensors = [
    {
      name: "Sensor Getaran Kasar",
      lat: -5.650,
      lng: 105.300,
      iconColor: "#f1c40f",
      iconClass: "fas fa-bolt"
    },
    {
      name: "Sensor Getaran Halus",
      lat: -5.675,
      lng: 105.250,
      iconColor: "#e74c3c",
      iconClass: "fas fa-ruler-combined"
    },
    {
      name: "Sensor Lingkungan",
      lat: -5.700,
      lng: 105.200,
      iconColor: "#2ecc71",
      iconClass: "fas fa-leaf"
    }
  ];

  sensors.forEach(sensor => {
    const customIcon = L.divIcon({
      html: `<i class="${sensor.iconClass}" style="color: ${sensor.iconColor}; font-size: 1.2rem;"></i>`,
      className: 'text-center'
    });

    L.marker([sensor.lat, sensor.lng], { icon: customIcon })
      .addTo(map)
      .bindPopup(`<strong>${sensor.name}</strong><br>Lat: ${sensor.lat}, Lng: ${sensor.lng}`);
  });
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
  fetch('http://1/getAllData') // ganti IP sesuai IP ESP32
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

<script>
const canvas = document.getElementById("canvas-tsunami");
const ctx = canvas.getContext("2d");
canvas.width = canvas.offsetWidth;
canvas.height = 300;

let waveOffset = 0;
let isSimulasiAktif = false;

function drawTsunami() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);

  ctx.beginPath();
  for (let x = 0; x <= canvas.width; x++) {
    const y = 50 * Math.sin((x + waveOffset) * 0.04) + 150;
    ctx.lineTo(x, y);
  }

  ctx.lineTo(canvas.width, canvas.height);
  ctx.lineTo(0, canvas.height);
  ctx.closePath();

  ctx.fillStyle = '#0d6efd88'; // transparan biru
  ctx.fill();

  waveOffset += 3;

  if (isSimulasiAktif) {
    requestAnimationFrame(drawTsunami);
  }
}

function jalankanSimulasi() {
  isSimulasiAktif = true;
  waveOffset = 0;
  drawTsunami();

  // Stop otomatis setelah 10 detik
  setTimeout(() => {
    isSimulasiAktif = false;
  }, 10000);
}
</script>

<script>
  function loadSensorHistory() {
    fetch("http://10.62.58.237/getAllData") // Ganti IP dengan IP ESP32
      .then(res => res.json())
      .then(data => {
        const tbody = document.getElementById("event-logs");
        tbody.innerHTML = ""; // Kosongkan dulu

        const logs = [
          {
            time: data.timestamp,
            sensor: "Getaran Kasar (Piezo)",
            value: data.vibration,
            status: getStatus(data.vibration, 50, 80)
          },
          {
            time: data.timestamp,
            sensor: "Getaran Halus (MPU6050)",
            value: data.mpu6050,
            status: getStatus(data.mpu6050, 40, 70)
          },
          {
            time: data.timestamp,
            sensor: "Tekanan/Lingkungan (BME)",
            value: data.bme680,
            status: getStatus(data.bme680, 70, 90)
          }
        ];

        logs.forEach(log => {
          const row = document.createElement("tr");
          row.innerHTML = `
            <td>${log.time}</td>
            <td>${log.sensor}</td>
            <td>${log.value}</td>
            <td>
              <span class="badge ${getBadge(log.status)}">${log.status}</span>
            </td>
            <td>
              <button class="btn btn-sm btn-outline-info">
                <i class="fas fa-search"></i> Detail
              </button>
            </td>
          `;
          tbody.appendChild(row);
        });
      })
      .catch(err => {
        const tbody = document.getElementById("event-logs");
        tbody.innerHTML = `
          <tr><td colspan="5" class="text-danger text-center">Gagal memuat data.</td></tr>
        `;
        console.error("Gagal fetch data:", err);
      });
  }

  function getStatus(value, warning, danger) {
    if (value >= danger) return "Bahaya";
    if (value >= warning) return "Waspada";
    return "Normal";
  }

  function getBadge(status) {
    if (status === "Bahaya") return "bg-danger";
    if (status === "Waspada") return "bg-warning text-dark";
    return "bg-success";
  }

  // Event listener tombol refresh
  document.getElementById("refresh-logs").addEventListener("click", loadSensorHistory);

  // Auto-load saat halaman dibuka
  loadSensorHistory();
</script>

</body>
</html>