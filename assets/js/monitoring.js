/**
 * Enhanced Tsunami Monitoring Dashboard Controller
 * 
 * @version 4.0.0
 */

class MonitoringDashboard {
    constructor() {
        this.config = {
            espIp: "192.168.248.237",
            apiBaseUrl: "<?= API_PATH ?>",
            maxHistory: 30,
            refreshInterval: 2000,
            vibrationThresholds: {
                normal: 4,
                warning: 7,
                danger: 10
            },
            mpuThresholds: {
                normal: 40,
                warning: 70,
                danger: 100
            },
            piezoThresholds: {
                normal: 50,
                warning: 80,
                danger: 100
            }
        };

        this.state = {
            isAlertActive: false,
            lastData: {},
            charts: {},
            map: null,
            markers: {},
            dataHistory: []
        };

        this.init();
    }

    init() {
        this.initGauge();
        this.initCharts();
        this.initMap();
        this.setupEventListeners();
        this.fetchData();
        this.loadLogs();
        this.fetchExternalData();
        
        // Set up data refresh interval
        this.dataInterval = setInterval(() => this.fetchData(), this.config.refreshInterval);
    }

    initGauge() {
        const gaugeTarget = document.getElementById("vibration-gauge");
        
        this.state.gauge = Gauge(gaugeTarget, {
            angle: 0,
            lineWidth: 0.3,
            radiusScale: 1,
            pointer: {
                length: 0.6,
                strokeWidth: 0.05,
                color: "#000000"
            },
            staticLabels: {
                font: "10px sans-serif",
                labels: [0, 2, 4, 6, 8, 10],
                fractionDigits: 0
            },
            staticZones: [
                { strokeStyle: "#28a745", min: 0, max: this.config.vibrationThresholds.normal },
                { strokeStyle: "#ffc107", min: this.config.vibrationThresholds.normal, max: this.config.vibrationThresholds.warning },
                { strokeStyle: "#dc3545", min: this.config.vibrationThresholds.warning, max: this.config.vibrationThresholds.danger }
            ],
            limitMax: true,
            limitMin: true,
            highDpiSupport: true
        });
        
        this.state.gauge.setMinValue(0);
        this.state.gauge.maxValue = this.config.vibrationThresholds.danger;
        this.state.gauge.set(0);
    }

    initCharts() {
        const chartConfigs = {
            'vibration-chart': {
                label: "Vibration Level",
                color: "#0d6efd",
                yAxisLabel: "Level (0-10)"
            },
            'mpu-chart': {
                label: "MPU6050 Value",
                color: "#dc3545",
                yAxisLabel: "Value"
            },
            'piezo-chart': {
                label: "Piezoelectric Value",
                color: "#ffc107",
                yAxisLabel: "Value"
            },
            'bme-chart': {
                label: "Pressure (hPa)",
                color: "#28a745",
                yAxisLabel: "hPa"
            }
        };

        Object.keys(chartConfigs).forEach(chartId => {
            const ctx = document.getElementById(chartId).getContext("2d");
            const config = chartConfigs[chartId];
            
            this.state.charts[chartId] = new Chart(ctx, {
                type: "line",
                data: {
                    labels: [],
                    datasets: [{
                        label: config.label,
                        data: [],
                        borderColor: config.color,
                        backgroundColor: `${config.color}20`,
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        pointRadius: 0
                    }]
                },
                options: this.getChartOptions(config.yAxisLabel)
            });
        });
    }

    getChartOptions(yAxisLabel) {
        return {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { 
                        maxRotation: 0, 
                        autoSkip: true, 
                        maxTicksLimit: 8,
                        callback: value => {
                            const date = new Date(value);
                            return date.toLocaleTimeString();
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: "rgba(0,0,0,0.05)" },
                    title: {
                        display: true,
                        text: yAxisLabel
                    }
                }
            },
            animation: { duration: 0 },
            elements: { line: { borderJoinStyle: 'round' } }
        };
    }

    initMap() {
        this.state.map = L.map('sensor-map').setView([-5.7002, 105.2644], 12);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 18
        }).addTo(this.state.map);
        
        // Add sensor markers
        this.addMarker('piezo', [-5.7002, 105.2644], 'Piezoelectric Sensor', 'warning');
        this.addMarker('mpu', [-5.7010, 105.2650], 'MPU6050 Sensor', 'danger');
        this.addMarker('bme', [-5.6995, 105.2638], 'BME280 Sensor', 'success');
    }

    addMarker(id, latlng, title, type) {
        const colors = {
            'warning': 'orange',
            'danger': 'red',
            'success': 'green',
            'primary': 'blue'
        };
        
        const icon = L.divIcon({
            className: `custom-icon`,
            html: `
                <div class="marker-pin ${colors[type]}"></div>
                <i class="fas fa-${type === 'success' ? 'leaf' : type === 'danger' ? 'ruler-combined' : 'bolt'} ${type}-icon"></i>
            `,
            iconSize: [30, 42],
            iconAnchor: [15, 42],
            popupAnchor: [0, -36]
        });
        
        if (this.state.markers[id]) {
            this.state.markers[id].setLatLng(latlng);
        } else {
            this.state.markers[id] = L.marker(latlng, { icon })
                .addTo(this.state.map)
                .bindPopup(title);
        }
    }

    setupEventListeners() {
        // Refresh buttons
        $('#refresh-logs').click(() => this.handleRefreshLogs());
        $('#refresh-data').click(() => this.handleRefreshData());
        
        // Alarm controls
        $('#silence-btn').click(() => this.silenceAlarm());
        $('#test-alarm').click(() => this.testAlarm());
        $('#more-info-btn').click(() => this.showAlertDetails());
        
        // Video controls
        $('.close-video').click(() => this.hideTsunamiVideo());
        
        // Keyboard shortcuts
        $(document).keydown(e => {
            if (e.key === 'Escape') {
                this.hideTsunamiVideo();
            }
        });
    }

    async fetchData() {
        try {
            const response = await fetch(`${this.config.apiBaseUrl}/get_data.php`);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            
            const data = await response.json();
            if (!data.success) throw new Error(data.message || 'Failed to fetch data');
            
            this.processData(data.data);
            this.updateConnectionStatus(true);
        } catch (error) {
            console.error("Fetch error:", error);
            this.updateConnectionStatus(false);
        }
    }

    processData(data) {
        this.state.lastData = data;
        
        // Update vibration data
        this.updateVibrationData(data);
        
        // Update sensor data
        this.updateMPUData(data);
        this.updatePiezoData(data);
        this.updateBMEData(data);
        
        // Update system status
        this.updateSystemStatus(data);
        this.updateAlertStatus(data);
        this.updateLastUpdate();
        
        // Update map if GPS data is available
        if (data.gps_lat && data.gps_lng) {
            this.updateMap(data.gps_lat, data.gps_lng);
        }
    }

    updateConnectionStatus(isConnected) {
        const status = $("#connection-status");
        status.removeClass("bg-success bg-danger")
              .addClass(isConnected ? "bg-success" : "bg-danger")
              .html(`<i class="fas fa-${isConnected ? 'check-circle' : 'times-circle'} me-1"></i> ${isConnected ? 'Connected' : 'Disconnected'}`);
    }

    updateVibrationData(data) {
        const value = data.vibration / 10; // Scale to 0-10 for gauge
        $("#vibration-value").text(value.toFixed(1));
        this.state.gauge.set(value);
        
        this.updateChart('vibration-chart', new Date(), value);
    }

    updateMPUData(data) {
        const value = data.mpu6050;
        $("#mpu-value").text(value.toFixed(1));
        $("#mpu-progress").css("width", `${Math.min(value, 100)}%`);
        $("#mpu-timestamp").text(data.timestamp || new Date().toLocaleTimeString());
        
        // Determine status
        let status = "Normal";
        let statusClass = "bg-success";
        let statusText = "Normal operation";
        
        if (value >= this.config.mpuThresholds.danger) {
            status = "DANGER";
            statusClass = "bg-danger";
            statusText = "High vibration detected";
        } else if (value >= this.config.mpuThresholds.warning) {
            status = "WARNING";
            statusClass = "bg-warning";
            statusText = "Moderate vibration";
        }
        
        $("#mpu-status")
            .removeClass("bg-success bg-warning bg-danger")
            .addClass(statusClass)
            .text(status);
            
        $("#mpu-status-text")
            .removeClass("text-danger text-warning")
            .addClass(status === "DANGER" ? "text-danger" : status === "WARNING" ? "text-warning" : "")
            .text(statusText);
        
        this.updateChart('mpu-chart', new Date(), value);
    }

    updatePiezoData(data) {
        const value = data.vibration;
        $("#piezo-value").text(value);
        $("#piezo-progress").css("width", `${Math.min(value, 100)}%`);
        $("#piezo-timestamp").text(data.timestamp || new Date().toLocaleTimeString());
        
        // Determine status
        let status = "Normal";
        let statusClass = "bg-success";
        let statusText = "Normal operation";
        
        if (value >= this.config.piezoThresholds.danger) {
            status = "DANGER";
            statusClass = "bg-danger";
            statusText = "High vibration detected";
        } else if (value >= this.config.piezoThresholds.warning) {
            status = "WARNING";
            statusClass = "bg-warning";
            statusText = "Moderate vibration";
        }
        
        $("#piezo-status")
            .removeClass("bg-success bg-warning bg-danger")
            .addClass(statusClass)
            .text(status);
            
        $("#piezo-status-text")
            .removeClass("text-danger text-warning")
            .addClass(status === "DANGER" ? "text-danger" : status === "WARNING" ? "text-warning" : "")
            .text(statusText);
        
        this.updateChart('piezo-chart', new Date(), value);
    }

    updateBMEData(data) {
        $("#bme-temp").text(data.temperature ? data.temperature.toFixed(1) : "--");
        $("#bme-hum").text(data.humidity ? data.humidity.toFixed(1) : "--");
        $("#bme-pres").text(data.pressure ? data.pressure.toFixed(1) : "--");
        
        if (data.pressure) {
            this.updateChart('bme-chart', new Date(), data.pressure);
        }
    }

    updateChart(chartId, time, value) {
        const chart = this.state.charts[chartId];
        
        if (chart.data.labels.length >= this.config.maxHistory) {
            chart.data.labels.shift();
            chart.data.datasets[0].data.shift();
        }
        
        chart.data.labels.push(time);
        chart.data.datasets[0].data.push(value);
        chart.update();
    }

    updateSystemStatus(data) {
        let systemStatus = "NORMAL";
        let statusClass = "bg-success";
        
        if (data.vibration_status === "Danger" || data.mpu_status === "Danger") {
            systemStatus = "DANGER";
            statusClass = "bg-danger";
        } else if (data.vibration_status === "Warning" || data.mpu_status === "Warning") {
            systemStatus = "WARNING";
            statusClass = "bg-warning";
        }
        
        $("#system-status")
            .removeClass("bg-success bg-warning bg-danger")
            .addClass(statusClass)
            .html(`<i class="fas fa-${systemStatus === "DANGER" ? 'exclamation-triangle' : 'check-circle'} me-1"></i> ${systemStatus}`);
            
        $("#status-umum")
            .removeClass("bg-success bg-warning bg-danger text-dark")
            .addClass(statusClass + (statusClass === "bg-warning" ? " text-dark" : ""))
            .text(systemStatus);
    }

    updateAlertStatus(data) {
        const isAlert = data.alert_active || 
                       (data.vibration_status === "Danger" || 
                        data.mpu_status === "Danger");
        
        const alertBanner = $("#alert-banner");
        const alertMessage = $("#alert-message");
        
        if (isAlert) {
            // Determine alert message
            let message = "WARNING: ";
            if (data.vibration_status === "Danger") message += "High vibration detected! ";
            if (data.mpu_status === "Danger") message += "MPU6050 danger level! ";
            
            alertMessage.text(message.trim());
            
            if (!this.state.isAlertActive) {
                // First time alert
                alertBanner.removeClass("d-none");
                document.getElementById("alert-sound").play();
                $("#silence-btn").removeClass("d-none");
                this.state.isAlertActive = true;
                
                // Show tsunami video if vibration is critical
                if (data.vibration >= 80) {
                    this.showTsunamiVideo();
                }
            }
        } else if (this.state.isAlertActive) {
            // Alert cleared
            alertBanner.addClass("d-none");
            document.getElementById("alert-sound").pause();
            $("#silence-btn").addClass("d-none");
            this.state.isAlertActive = false;
            
            // Hide tsunami video if shown
            this.hideTsunamiVideo();
        }
    }

    updateLastUpdate() {
        const now = new Date();
        $("#last-update").html(`<i class="fas fa-clock me-1"></i> ${now.toLocaleTimeString()}`);
        $("#last-analysis").text(`Last analyzed: ${now.toLocaleTimeString()}`);
    }

    updateMap(lat, lng) {
        this.addMarker('current', [lat, lng], 'Current Location', 'primary');
        this.state.map.setView([lat, lng], 12);
    }

    async loadLogs() {
        try {
            const response = await fetch(`${this.config.apiBaseUrl}/get_logs.php`);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            
            const data = await response.json();
            if (!data.success) throw new Error(data.message || 'Failed to load logs');
            
            this.displayLogs(data.data);
        } catch (error) {
            console.error("Logs error:", error);
            $("#event-logs").html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Failed to load event logs. Please try again.
                </div>
            `);
        }
    }

    displayLogs(logs) {
        if (!logs || logs.length === 0) {
            $("#event-logs").html(`
                <div class="text-center py-3 text-muted">
                    <i class="fas fa-info-circle fa-2x mb-2"></i>
                    <p>No event logs found</p>
                </div>
            `);
            return;
        }
        
        let html = '';
        logs.forEach(log => {
            const logClass = log.alert_level === 'critical' ? 'danger' : 
                           log.alert_level === 'high' ? 'warning' : '';
            
            html += `
                <div class="log-entry ${logClass}">
                    <div class="d-flex justify-content-between">
                        <span class="log-message">
                            <strong>${log.type}</strong>: ${log.status}
                        </span>
                        <span class="badge bg-${log.alert_level === 'critical' ? 'danger' : 
                                             log.alert_level === 'high' ? 'warning' : 'success'}">
                            ${log.alert_level.toUpperCase()}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span class="log-time">
                            <i class="fas fa-clock me-1"></i>
                            ${new Date(log.timestamp).toLocaleString()}
                        </span>
                        <span>
                            Vib: ${log.readings.vibration} | 
                            MPU: ${log.readings.mpu6050.toFixed(1)} | 
                            Pres: ${log.readings.pressure.toFixed(1)}hPa
                        </span>
                    </div>
                </div>
            `;
        });
        
        $("#event-logs").html(html);
    }

    async fetchExternalData() {
        await this.fetchEarthquakeData();
        await this.fetchWeatherData();
    }

    async fetchEarthquakeData() {
        try {
            const response = await fetch("https://data.bmkg.go.id/gempabumi/autogempa.json");
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            
            const data = await response.json();
            const quake = data.Infogempa.gempa;
            
            let html = `
                <div class="text-center mb-3">
                    <i class="fas fa-earth-asia fa-3x text-warning mb-2"></i>
                    <h5>Latest Earthquake</h5>
                </div>
                <table class="table table-sm">
                    <tr><th>Time</th><td>${quake.Tanggal} ${quake.Jam}</td></tr>
                    <tr><th>Magnitude</th><td>${quake.Magnitude} SR</td></tr>
                    <tr><th>Location</th><td>${quake.Wilayah}</td></tr>
                    <tr><th>Depth</th><td>${quake.Kedalaman}</td></tr>
                    <tr><th>Potential</th><td class="${quake.Potensi === "Tsunami" ? "text-danger fw-bold" : ""}">
                        ${quake.Potensi}
                    </td></tr>
                </table>
            `;
            
            $("#earthquake-data").html(html);
            
            if (quake.Potensi === "Tsunami") {
                this.triggerTsunamiWarning("BMKG Tsunami Alert: " + quake.Wilayah);
            }
        } catch (error) {
            console.error("Earthquake data error:", error);
            $("#earthquake-data").html(`
                <div class="text-center py-3 text-muted">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Failed to load earthquake data
                </div>
            `);
        }
    }

    async fetchWeatherData() {
        try {
            const response = await fetch("https://bmkg-cuaca-api.vercel.app/cuaca?provinceId=13&districtId=1371");
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            
            const data = await response.json();
            
            let html = `
                <div class="text-center mb-3">
                    <i class="fas fa-cloud-sun fa-3x text-primary mb-2"></i>
                    <h5>Current Weather</h5>
                </div>
                <div class="text-center">
                    <h2 class="display-4">${data.temperature}°C</h2>
                    <p class="lead">${data.weather_desc}</p>
                    <p>Humidity: ${data.humidity}% | Wind: ${data.wind_speed} km/h</p>
                </div>
            `;
            
            $("#weather-data").html(html);
        } catch (error) {
            console.error("Weather data error:", error);
            $("#weather-data").html(`
                <div class="text-center py-3 text-muted">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Failed to load weather data
                </div>
            `);
        }
    }

    handleRefreshLogs() {
        this.loadLogs();
        $('#refresh-logs').html('<i class="fas fa-sync-alt fa-spin me-1"></i> Refreshing...');
        setTimeout(() => {
            $('#refresh-logs').html('<i class="fas fa-sync-alt me-1"></i> Refresh');
        }, 1000);
    }

    handleRefreshData() {
        clearInterval(this.dataInterval);
        this.fetchData();
        this.dataInterval = setInterval(() => this.fetchData(), this.config.refreshInterval);
        
        $('#refresh-data').html('<i class="fas fa-sync-alt fa-spin me-1"></i> Refreshing...');
        setTimeout(() => {
            $('#refresh-data').html('<i class="fas fa-sync-alt me-1"></i> Refresh');
        }, 1000);
    }

    silenceAlarm() {
        fetch(`http://${this.config.espIp}/alert?state=off`)
            .then(() => {
                console.log("Alarm silenced");
                $('#silence-btn').html('<i class="fas fa-check me-1"></i> Silenced').prop('disabled', true);
            })
            .catch(err => console.error("Silence error:", err));
    }

    testAlarm() {
        this.triggerTsunamiWarning("SYSTEM TEST: Tsunami Alert Simulation");
    }

    showAlertDetails() {
        // In a real implementation, this would show more details about the alert
        alert("Alert details would be displayed here with more information.");
    }

    showTsunamiVideo() {
        const video = document.getElementById("tsunamiVideo");
        video.currentTime = 0;
        video.play();
        $("#tsunami-video").fadeIn();
    }

    hideTsunamiVideo() {
        const video = document.getElementById("tsunamiVideo");
        video.pause();
        $("#tsunami-video").fadeOut();
    }

    triggerTsunamiWarning(message) {
        if (!this.state.isAlertActive) {
            const alertBanner = $("#alert-banner");
            const alertMessage = $("#alert-message");
            
            alertMessage.text(message);
            alertBanner.removeClass("d-none");
            document.getElementById("alert-sound").play();
            $("#silence-btn").removeClass("d-none");
            this.state.isAlertActive = true;
            
            this.showTsunamiVideo();
        }
    }
}

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    const dashboard = new MonitoringDashboard();
    
    // Make dashboard available globally for debugging if needed
    if (DEBUG_MODE) {
        window.dashboard = dashboard;
    }
});