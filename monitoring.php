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

<div class="row mb-5">
  <!-- CARD SENSOR PIEZO -->
  <div class="col-md-6">
    <div class="card shadow border-0" style="border-radius: 18px; background: linear-gradient(135deg, #e3f2fd, #ffffff);">
      <div class="card-body">
        <div class="d-flex align-items-center mb-3">
          <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
            <i class="fas fa-wave-square fs-4"></i>
          </div>
          <div class="ms-3">
            <h6 class="text-uppercase fw-bold mb-0">Sensor Getaran Kasar</h6>
            <small class="text-muted">Piezoelektrik</small>
          </div>
        </div>

        <div class="d-flex align-items-center mb-2">
          <h2 class="fw-bold text-primary mb-0"><span id="piezo-value">0</span></h2>
          <span class="text-muted ms-2">/100</span>
        </div>

        <div class="progress mb-2" style="height: 8px;">
          <div class="progress-bar bg-info" id="piezo-progress" style="width: 0%;"></div>
        </div>

        <p class="mb-0 small">Status: <span class="fw-bold" id="piezo-status" style="color: green;">Normal</span></p>
        <p class="mb-0 small text-end"><i class="fas fa-clock me-1"></i><span id="piezo-timestamp">-</span></p>
      </div>
    </div>

    <!-- Deskripsi -->
    <div class="mt-2 ps-2">
      <p class="mb-1 small text-muted"><strong>Threshold:</strong> Waspada ≥ 50, Bahaya ≥ 80</p>
      <p class="small text-muted">Sensor Piezo mendeteksi getaran kasar mendadak di dasar laut atau pelampung, sebagai indikator awal pergeseran lempeng yang dapat menyebabkan tsunami.</p>
    </div>
  </div>

  <!-- GRAFIK -->
  <div class="col-md-6">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Grafik Getaran Kasar</strong>
        <div class="btn-group btn-group-sm">
          <button class="btn btn-outline-secondary active" data-piezo-period="hour">1 Jam</button>
          <button class="btn btn-outline-secondary" data-piezo-period="day">1 Hari</button>
          <button class="btn btn-outline-secondary" data-piezo-period="week">1 Minggu</button>
        </div>
      </div>
      <div class="card-body">
        <canvas id="piezo-chart" height="250"></canvas>
      </div>
    </div>
  </div>
</div>

<div class="row mb-5">
  <!-- Kartu Sensor MPU -->
  <div class="col-md-6">
    <div class="card shadow border-0" style="border-radius: 18px; background: linear-gradient(135deg, #f3e5f5, #ffffff);">
      <div class="card-body">
        <div class="d-flex align-items-center mb-3">
          <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
            <i class="fas fa-ruler-combined fs-4"></i>
          </div>
          <div class="ms-3">
            <h6 class="text-uppercase fw-bold mb-0">Sensor Getaran Halus</h6>
            <small class="text-muted">MPU6050</small>
          </div>
        </div>

        <div class="d-flex align-items-center mb-2">
          <h2 class="fw-bold text-danger mb-0"><span id="mpu-value">0</span></h2>
          <span class="text-muted ms-2">/100</span>
        </div>

        <div class="progress mb-2" style="height: 8px;">
          <div class="progress-bar bg-danger" id="mpu-progress" style="width: 0%;"></div>
        </div>

        <p class="mb-0 small">Status: <span class="fw-bold" id="mpu-status" style="color: green;">Normal</span></p>
        <p class="mb-0 small text-end"><i class="fas fa-clock me-1"></i><span id="mpu-timestamp">-</span></p>
      </div>
    </div>
    <div class="mt-2 ps-2">
      <p class="mb-1 small text-muted"><strong>Threshold:</strong> Waspada ≥ 40, Bahaya ≥ 70</p>
      <p class="small text-muted">Sensor MPU6050 mendeteksi getaran kecil akibat aktivitas tektonik. Jika meningkat signifikan, bisa menandakan potensi tsunami perlahan.</p>
    </div>
  </div>

  <!-- Grafik MPU -->
  <div class="col-md-6">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Grafik Getaran Halus</strong>
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

<div class="row mb-5">
  <!-- Kartu Sensor BME -->
  <div class="col-md-6">
    <div class="card shadow border-0" style="border-radius: 18px; background: linear-gradient(135deg, #e0f7fa, #ffffff);">
      <div class="card-body">
        <div class="d-flex align-items-center mb-3">
          <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
            <i class="fas fa-leaf fs-4"></i>
          </div>
          <div class="ms-3">
            <h6 class="text-uppercase fw-bold mb-0">Sensor Tekanan Udara</h6>
            <small class="text-muted">BME280</small>
          </div>
        </div>

        <div class="row text-center mb-2">
          <div class="col-4">
            <h6 class="fw-bold text-success"><span id="bme-temp">--</span>°C</h6>
            <small class="text-muted">Suhu</small>
          </div>
          <div class="col-4">
            <h6 class="fw-bold text-success"><span id="bme-hum">--</span>%</h6>
            <small class="text-muted">Kelembapan</small>
          </div>
          <div class="col-4">
            <h6 class="fw-bold text-success"><span id="bme-pres">--</span> hPa</h6>
            <small class="text-muted">Tekanan</small>
          </div>
        </div>
        <p class="mb-0 small text-end"><i class="fas fa-clock me-1"></i><span id="bme-timestamp">-</span></p>
      </div>
    </div>
    <div class="mt-2 ps-2">
      <p class="small text-muted">BME280 digunakan untuk membaca suhu, kelembapan, dan tekanan atmosfer. Perubahan signifikan dapat menjadi indikator gelombang laut atau perubahan cuaca ekstrem.</p>
    </div>
  </div>

  <!-- Grafik BME -->
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <strong>Grafik Tekanan Udara</strong>
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

<script>
setInterval(() => {
  fetch("http://192.168.4.1/getAllData")  // ganti dengan IP ESP32 S3 kamu
    .then(res => res.json())
    .then(data => {
      const value = data.vibration;
      document.getElementById("piezo-value").innerText = value;
      document.getElementById("piezo-progress").style.width = value + "%";

      const status = document.getElementById("piezo-status");
      if (value >= 80) {
        status.innerText = "Bahaya";
        status.style.color = "red";
      } else if (value >= 50) {
        status.innerText = "Waspada";
        status.style.color = "orange";
      } else {
        status.innerText = "Normal";
        status.style.color = "green";
      }

      document.getElementById("piezo-timestamp").innerText = new Date().toLocaleTimeString();
    })
    .catch(err => console.error("Gagal ambil data piezo:", err));
}, 2000);
</script>

<script>
setInterval(() => {
  fetch("http://192.168.4.1/getAllData") // Ganti dengan IP ESP32 kamu
    .then(res => res.json())
    .then(data => {
      // MPU6050
      const mpu = data.mpu6050;
      document.getElementById("mpu-value").innerText = mpu;
      document.getElementById("mpu-progress").style.width = mpu + "%";

      const mpuStatus = document.getElementById("mpu-status");
      if (mpu >= 70) {
        mpuStatus.innerText = "Bahaya";
        mpuStatus.style.color = "red";
      } else if (mpu >= 40) {
        mpuStatus.innerText = "Waspada";
        mpuStatus.style.color = "orange";
      } else {
        mpuStatus.innerText = "Normal";
        mpuStatus.style.color = "green";
      }
      document.getElementById("mpu-timestamp").innerText = new Date().toLocaleTimeString();

      // BME280
      document.getElementById("bme-temp").innerText = data.temperature;
      document.getElementById("bme-hum").innerText = data.humidity;
      document.getElementById("bme-pres").innerText = data.pressure;
      document.getElementById("bme-timestamp").innerText = new Date().toLocaleTimeString();
    })
    .catch(err => console.error("Gagal ambil data:", err));
}, 2000);
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