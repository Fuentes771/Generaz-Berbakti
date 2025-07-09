// monitoring.js
const espIp = "192.168.241.203"; // Atur IP ESP Anda
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
  $.ajax({
    url: 'php/get_data.php',
    method: 'GET',
    dataType: 'json',
    success: function(data) {
      const connection = $("#connection-status");
      connection.removeClass("bg-danger").addClass("bg-success").text("Connected");

      $("#vibration-value").text(data.vibration);
      vibrationGauge.set(data.vibration);

      const status = $("#system-status").removeClass("bg-secondary bg-warning bg-danger");
      status.addClass(data.status === "Normal" ? "bg-success" : data.status === "Warning" ? "bg-warning" : "bg-danger")
            .text(data.status);

      const alertStatus = $("#alert-status");
      alertStatus.text(data.alert ? "AKTIF" : "Tidak Aktif")
                 .removeClass("bg-secondary bg-danger")
                 .addClass(data.alert ? "bg-danger" : "bg-secondary");

      if (data.alert && !isAlertActive) {
        document.getElementById("alert-sound").play();
        $("#silence-btn").removeClass("d-none").text("Matikan Alarm").prop("disabled", false);
        isAlertActive = true;
      } else if (!data.alert && isAlertActive) {
        document.getElementById("alert-sound").pause();
        $("#silence-btn").addClass("d-none");
        isAlertActive = false;
      }

      $("#last-analysis").text("Terakhir diperbarui: " + new Date().toLocaleTimeString());
      updateChart(data.vibration);
    },
    error: function() {
      $("#connection-status").removeClass("bg-success").addClass("bg-danger").text("Disconnected");
    },
    complete: function() {
      setTimeout(fetchData, 2000);
    }
  });
}

function updateChart(value) {
  dataHistory.push({
    time: new Date().toLocaleTimeString(),
    value: value
  });
  if (dataHistory.length > maxHistory) dataHistory.shift();
  vibrationChart.data.labels = dataHistory.map(d => d.time);
  vibrationChart.data.datasets[0].data = dataHistory.map(d => d.value);
  vibrationChart.update();
}

function loadLogs() {
  $.get('php/get_logs.php', function(data) {
    $('#event-logs').html(data);
  });
}

function silenceAlarm() {
  $.get(`http://${espIp}/alert?state=off`, function() {
    console.log("Alarm dimatikan");
  });
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

  setInterval(loadLogs, 30000);
});

fetch('http://192.168.137.166/getAllData')

function fetchPiezoData() {
  fetch('http://192.168.137.166/getAllData')
    .then(res => res.json())
    .then(data => {
      const now = new Date().toLocaleTimeString();
      const value = data.vibration;

      if (vibrationChart.data.labels.length > 20) {
        vibrationChart.data.labels.shift();
        vibrationChart.data.datasets[0].data.shift();
      }

      vibrationChart.data.labels.push(now);
      vibrationChart.data.datasets[0].data.push(value);
      vibrationChart.update();
    });
}

function updateMPUData() {
  fetch('http://192.168.137.166/getAllData') // Ganti IP sesuai ESP32 kamu
    .then(res => res.json())
    .then(data => {
      const value = data.mpu6050; // pastikan field JSON sesuai dengan ESP32-mu
      const time = data.timestamp_mpu || new Date().toLocaleTimeString();

      // Update Chart
      if (mpuChart.data.labels.length > 20) {
        mpuChart.data.labels.shift();
        mpuChart.data.datasets[0].data.shift();
      }

      mpuChart.data.labels.push(time);
      mpuChart.data.datasets[0].data.push(value);
      mpuChart.update();

      // Update tampilan HTML
      document.getElementById("mpu-value").textContent = value;
      document.getElementById("mpu-timestamp").textContent = time;
      document.getElementById("mpu-progress").style.width = Math.min(value, 100) + "%";

      // Update status warna dan teks
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
    })
    .catch(err => {
      console.error("Gagal mengambil data MPU:", err);
      // Opsional: tampilkan status tidak tersedia
      document.getElementById("mpu-status").textContent = "Tidak Tersedia";
      document.getElementById("mpu-status").className = "fw-bold text-muted";
    });
}

setInterval(() => {
  fetch('http://192.168.137.166/getAllData') // sesuaikan IP ESP32 kamu
    .then(res => res.json())
    .then(data => {
      const value = data.pressure;
      const status = data.pressureStatus;
      const time = data.timestamp;

      document.getElementById("bme-value").textContent = value;
      document.getElementById("bme-timestamp").textContent = time;
      document.getElementById("bme-progress").style.width = Math.min((value / 1050) * 100, 100) + "%";

      const statusElem = document.getElementById("bme-status");
      if (status === "Danger") {
        statusElem.textContent = "Bahaya";
        statusElem.className = "fw-bold text-danger";
      } else if (status === "Warning") {
        statusElem.textContent = "Waspada";
        statusElem.className = "fw-bold text-warning";
      } else {
        statusElem.textContent = "Normal";
        statusElem.className = "fw-bold text-success";
      }
    })
    .catch(err => console.error("BME fetch error:", err));
}, 3000);
