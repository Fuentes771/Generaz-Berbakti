// monitoring.js

const espIp = "10.79.185.237"; // Atur IP ESP Anda
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
      { strokeStyle: "#ffc107", min: 5, max: 7 },
      { strokeStyle: "#dc3545", min: 8, max: 10 }
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
      $("#connection-status").removeClass("bg-danger").addClass("bg-success").text("Connected");
      $("#vibration-value").text(data.vibration);
      vibrationGauge.set(data.vibration);

      let status = $("#system-status").removeClass("bg-secondary bg-warning bg-danger");
      status.addClass(data.status === "Normal" ? "bg-success" : data.status === "Warning" ? "bg-warning" : "bg-danger")
            .text(data.status);

      $("#alert-status").text(data.alert ? "AKTIF" : "Tidak Aktif")
                         .removeClass("bg-secondary bg-danger")
                         .addClass(data.alert ? "bg-danger" : "bg-secondary");

      if (data.alert && !isAlertActive) {
        document.getElementById("alert-sound").play();
        $("#silence-btn").removeClass("d-none");
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
  $.get(`http://${espIp}/alert?state=off`);
}

$(document).ready(function() {
  initGauge();
  initChart();
  fetchData();
  loadLogs();

  $('#refresh-logs').click(function() {
    loadLogs();
  });

  $('#silence-btn').click(function() {
    silenceAlarm();
    $(this).text('Dimatikan').prop('disabled', true);
  });

  setInterval(loadLogs, 30000);
});
