// monitoring.js

const espIp = "10.62.58.237"; // IP ESP32 kamu
const espUrl = `http://${espIp}/getAllData`;
let vibrationGauge, vibrationChart;
let dataHistory = [], maxHistory = 20;
let isAlertActive = false;

function initGauge() {
  const gaugeTarget = document.getElementById("vibration-gauge");
  vibrationGauge = new Gauge(gaugeTarget).setOptions({
    angle: 0,
    lineWidth: 0.3,
    radiusScale: 1,
    pointer: {
      length: 0.6,
      strokeWidth: 0.05,
      color: "#000000"
    },
    staticZones: [
      { strokeStyle: "#28a745", min: 0, max: 4 },
      { strokeStyle: "#ffc107", min: 4, max: 7 },
      { strokeStyle: "#dc3545", min: 7, max: 10 }
    ],
    highDpiSupport: true
  });
  vibrationGauge.maxValue = 10;
  vibrationGauge.setMinValue(0);
  vibrationGauge.animationSpeed = 32;
  vibrationGauge.set(0);
}

function initChart() {
  const ctx = document.getElementById("vibration-chart").getContext("2d");
  vibrationChart = new Chart(ctx, {
    type: "line",
    data: {
      labels: [],
      datasets: [{
        label: "Vibration Level",
        data: [],
        borderColor: "#007bff",
        backgroundColor: "rgba(0,123,255,0.1)",
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
  fetch(espUrl)
    .then(res => res.json())
    .then(data => {
      $("#connection-status").removeClass("bg-danger").addClass("bg-success").text("Connected");

      // Piezo (vibration)
      $("#vibration-value").text(data.vibration);
      vibrationGauge.set(data.vibration);
      updateChart(data.vibration);

      const systemStatus = data.vibration >= 7 ? "Bahaya" : data.vibration >= 4 ? "Waspada" : "Normal";
      const statusColor = data.vibration >= 7 ? "bg-danger" : data.vibration >= 4 ? "bg-warning" : "bg-success";
      $("#system-status").removeClass("bg-secondary bg-warning bg-danger bg-success").addClass(statusColor).text(systemStatus);

      // Alarm
      const isAlert = data.vibration >= 7 || data.mpu6050 >= 70;
      $("#alert-status").text(isAlert ? "AKTIF" : "Tidak Aktif")
        .removeClass("bg-secondary bg-danger")
        .addClass(isAlert ? "bg-danger" : "bg-secondary");

      if (isAlert && !isAlertActive) {
        document.getElementById("alert-sound").play();
        $("#silence-btn").removeClass("d-none").text("Matikan Alarm").prop("disabled", false);
        isAlertActive = true;
      } else if (!isAlert && isAlertActive) {
        document.getElementById("alert-sound").pause();
        $("#silence-btn").addClass("d-none");
        isAlertActive = false;
      }

      $("#last-analysis").text("Terakhir diperbarui: " + new Date().toLocaleTimeString());

      // Ringkasan
      updateRingkasan({
        vibration_status: systemStatus === "Bahaya" ? "Danger" : systemStatus === "Waspada" ? "Warning" : "Normal",
        mpu_status: data.mpu6050 >= 70 ? "Danger" : data.mpu6050 >= 40 ? "Warning" : "Normal",
        bme_status: data.pressureStatus === "Bahaya" ? "Danger" : data.pressureStatus === "Waspada" ? "Warning" : "Normal",
        timestamp: data.timestamp || "-"
      });

      // MPU update
      updateMPU(data);

      // BME update
      updateBME(data);
    })
    .catch(() => {
      $("#connection-status").removeClass("bg-success").addClass("bg-danger").text("Disconnected");
    })
    .finally(() => {
      setTimeout(fetchData, 2000);
    });
}

function updateChart(value) {
  const time = new Date().toLocaleTimeString();
  dataHistory.push({ time, value });
  if (dataHistory.length > maxHistory) dataHistory.shift();
  vibrationChart.data.labels = dataHistory.map(d => d.time);
  vibrationChart.data.datasets[0].data = dataHistory.map(d => d.value);
  vibrationChart.update();
}

function updateMPU(data) {
  const value = data.mpu6050;
  const time = data.timestamp_mpu || new Date().toLocaleTimeString();

  if (typeof mpuChart !== 'undefined') {
    if (mpuChart.data.labels.length > 20) {
      mpuChart.data.labels.shift();
      mpuChart.data.datasets[0].data.shift();
    }
    mpuChart.data.labels.push(time);
    mpuChart.data.datasets[0].data.push(value);
    mpuChart.update();
  }

  document.getElementById("mpu-value").textContent = value;
  document.getElementById("mpu-timestamp").textContent = time;
  document.getElementById("mpu-progress").style.width = Math.min(value, 100) + "%";

  const status = document.getElementById("mpu-status");
  if (value >= 70) {
    status.textContent = "Bahaya";
    status.className = "fw-bold text-danger";
  } else if (value >= 40) {
    status.textContent = "Waspada";
    status.className = "fw-bold text-warning";
  } else {
    status.textContent = "Normal";
    status.className = "fw-bold text-success";
  }
}

function updateBME(data) {
  const value = data.pressure;
  const time = data.timestamp || new Date().toLocaleTimeString();
  const status = data.pressureStatus;

  document.getElementById("bme-value").textContent = value;
  document.getElementById("bme-timestamp").textContent = time;
  document.getElementById("bme-progress").style.width = Math.min((value / 1050) * 100, 100) + "%";

  const statusElem = document.getElementById("bme-status");
  if (status === "Bahaya") {
    statusElem.textContent = "Bahaya";
    statusElem.className = "fw-bold text-danger";
  } else if (status === "Waspada") {
    statusElem.textContent = "Waspada";
    statusElem.className = "fw-bold text-warning";
  } else {
    statusElem.textContent = "Normal";
    statusElem.className = "fw-bold text-success";
  }
}

function updateRingkasan(data) {
  const statusEl = document.getElementById("status-umum");
  const waktuEl = document.getElementById("waktu-update");
  const totalSensor = 3;

  let status = "Aman";
  if (data.vibration_status === "Danger" || data.mpu_status === "Danger" || data.bme_status === "Danger") {
    status = "Bahaya";
  } else if (data.vibration_status === "Warning" || data.mpu_status === "Warning" || data.bme_status === "Warning") {
    status = "Waspada";
  }

  statusEl.textContent = status.toUpperCase();
  statusEl.className = "badge fs-6 px-3 py-1 " +
    (status === "Bahaya" ? "bg-danger" : status === "Waspada" ? "bg-warning text-dark" : "bg-success");

  waktuEl.textContent = data.timestamp || "-";
  document.getElementById("jumlah-sensor").textContent = totalSensor;
}

function silenceAlarm() {
  fetch(`http://${espIp}/alert?state=off`)
    .then(() => {
      console.log("Alarm dimatikan");
    });
}

function loadLogs() {
  // Opsional: Jika ingin log lokal dari ESP
  $('#event-logs').html("Log hanya tersedia dari sisi ESP atau akan dimatikan jika PHP tidak digunakan.");
}

$(document).ready(function() {
  initGauge();
  initChart();
  fetchData();
  loadLogs();

  $('#refresh-logs').click(function() {
    loadLogs();
    $(this).html('<i class="fas fa-sync-alt fa-spin"></i> Memuat ulang...');
    setTimeout(() => {
      $(this).html('<i class="fas fa-sync-alt"></i> Refresh');
    }, 1000);
  });

  $('#silence-btn').click(function() {
    silenceAlarm();
    $(this).text('Dimatikan').prop('disabled', true);
  });

  setInterval(loadLogs, 30000); // Masih bisa digunakan jika ESP mendukung log lokal
});

if (value >= 80) {
  document.getElementById("tsunamiVideo").play();
}