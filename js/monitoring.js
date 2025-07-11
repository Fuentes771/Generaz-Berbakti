// Ganti dengan IP ESP32 Anda
const espIp = "10.79.185.237"; 

function updateAllData() {
  fetch(`http://${espIp}/getAllData`)
    .then(response => response.json())
    .then(data => {
      // Update data Piezo
      updatePiezoData(data);
      
      // Update data MPU6050
      updateMPUData(data);
      
      // Update data BME280
      updateBME280Data(data);
      
      // Update lokasi di peta (jika diperlukan)
      updateMapLocation(data);
    })
    .catch(error => {
      console.error("Error fetching data:", error);
    });
}

function updatePiezoData(data) {
  const value = data.vibration;
  const time = data.timestamp_piezo || new Date().toLocaleTimeString();
  
  document.getElementById("piezo-value").textContent = value;
  document.getElementById("piezo-timestamp").textContent = time;
  document.getElementById("piezo-progress").style.width = Math.min(value, 100) + "%";
  
  const status = document.getElementById("piezo-status");
  if (value >= 80) {
    status.textContent = "Bahaya";
    status.className = "fw-bold text-danger";
  } else if (value >= 50) {
    status.textContent = "Waspada";
    status.className = "fw-bold text-warning";
  } else {
    status.textContent = "Normal";
    status.className = "fw-bold text-success";
  }
  
  // Update chart
  updateVibrationChart(value, time);
}

function updateMPUData(data) {
  const value = data.mpu6050;
  const time = data.timestamp_mpu || new Date().toLocaleTimeString();
  
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
  
  // Update chart
  updateMPUChart(value, time);
}

function updateBME280Data(data) {
  const value = data.pressure;
  const statusText = data.pressureStatus;
  const time = data.timestamp_bme || new Date().toLocaleTimeString();
  
  document.getElementById("bme-value").textContent = value;
  document.getElementById("bme-timestamp").textContent = time;
  document.getElementById("bme-progress").style.width = Math.min((value / 1050) * 100, 100) + "%";
  
  const status = document.getElementById("bme-status");
  if (statusText === "Danger") {
    status.textContent = "Bahaya";
    status.className = "fw-bold text-danger";
  } else if (statusText === "Warning") {
    status.textContent = "Waspada";
    status.className = "fw-bold text-warning";
  } else {
    status.textContent = "Normal";
    status.className = "fw-bold text-success";
  }
  
  // Update chart
  updateBMEChart(value, time);
}

// Jalankan update setiap 3 detik
setInterval(updateAllData, 3000);

// Panggil pertama kali saat halaman dimuat
document.addEventListener("DOMContentLoaded", updateAllData);