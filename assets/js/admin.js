/**
 * Admin Dashboard Controller
 * 
 * @version 3.0.0
 * @license MIT
 */

class AdminDashboard {
    constructor() {
        this.chart = null;
        this.currentRange = '1';
        this.sensorData = {
            mpu: [],
            bme: [],
            piezo: []
        };
        this.lastUpdate = null;
        this.dataUpdateInterval = null;
        this.isRefreshing = false;
        
        // Sensor configurations
        this.sensorConfigs = {
            mpu: {
                name: 'MPU6050',
                unit: 'm/s²',
                warningThreshold: 40,
                dangerThreshold: 70,
                maxValue: 100
            },
            bme: {
                name: 'BME280',
                unit: 'hPa',
                warningThreshold: 60,
                dangerThreshold: 85,
                maxValue: 120
            },
            piezo: {
                name: 'Piezoelektrik',
                unit: 'G',
                warningThreshold: 50,
                dangerThreshold: 80,
                maxValue: 100
            }
        };
        
        this.init();
    }

    init() {
        this.setupTimeUpdater();
        this.initCharts();
        this.setupEventListeners();
        this.loadInitialData();
        this.setupDataPolling();
    }

    setupTimeUpdater() {
        this.updateTime();
        setInterval(() => this.updateTime(), 1000);
    }

    updateTime() {
        const now = new Date();
        this.lastUpdate = now;
        const timeString = now.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        document.getElementById('update-time').textContent = timeString;
    }

    initCharts() {
        const ctx = document.getElementById('combinedChart').getContext('2d');
        
        // Register plugins for better performance
        Chart.register({
            id: 'backgroundPlugin',
            beforeDraw: (chart) => {
                const ctx = chart.ctx;
                ctx.save();
                ctx.fillStyle = 'white';
                ctx.fillRect(0, 0, chart.width, chart.height);
                ctx.restore();
            }
        });
        
        this.chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    this.createDataset('MPU6050 (m/s²)', [], '#3498db'),
                    this.createDataset('BME280 (hPa)', [], '#2ecc71'),
                    this.createDataset('Piezoelektrik (G)', [], '#f39c12')
                ]
            },
            options: this.getChartOptions(),
            plugins: [{
                id: 'chartLoading',
                afterRender: (chart) => {
                    const loading = document.querySelector('.chart-loading');
                    if (chart.data.datasets[0].data.length === 0 && loading) {
                        loading.style.display = 'flex';
                    } else if (loading) {
                        loading.style.display = 'none';
                    }
                }
            }]
        });
    }

    createDataset(label, data, borderColor) {
        return {
            label,
            data,
            borderColor,
            backgroundColor: 'transparent',
            borderWidth: 2,
            pointRadius: 0,
            pointHoverRadius: 5,
            pointHitRadius: 10,
            pointBackgroundColor: borderColor,
            cubicInterpolationMode: 'monotone',
            tension: 0.4
        };
    }

    getChartOptions() {
        return {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1000,
                easing: 'easeOutQuart',
                x: {
                    type: 'number',
                    easing: 'linear',
                    duration: 0
                },
                y: {
                    type: 'number',
                    easing: 'easeOutQuart',
                    duration: 1000
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        boxWidth: 12,
                        padding: 20,
                        font: {
                            size: 12
                        },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    enabled: true,
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(0, 0, 0, 0.85)',
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 12
                    },
                    padding: 12,
                    cornerRadius: 6,
                    displayColors: true,
                    callbacks: {
                        label: (context) => {
                            const label = context.dataset.label || '';
                            const value = context.parsed.y;
                            const unit = label.includes('MPU') ? 'm/s²' : 
                                         label.includes('BME') ? 'hPa' : 'G';
                            return `${label}: ${value.toFixed(2)} ${unit}`;
                        }
                    }
                },
                decimation: {
                    enabled: true,
                    algorithm: 'lttb',
                    samples: 50,
                    threshold: 100
                }
            },
            scales: {
                x: {
                    type: 'time',
                    time: {
                        unit: 'minute',
                        displayFormats: {
                            minute: 'HH:mm'
                        },
                        tooltipFormat: 'HH:mm:ss'
                    },
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxRotation: 0,
                        autoSkip: true,
                        maxTicksLimit: 12
                    }
                },
                y: {
                    beginAtZero: false,
                    grace: '5%',
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        callback: (value) => {
                            if (value >= 1000) return value/1000 + 'k';
                            return value;
                        }
                    }
                }
            },
            elements: {
                line: {
                    tension: 0.4
                },
                point: {
                    radius: 0,
                    hoverRadius: 5
                }
            }
        };
    }

    setupEventListeners() {
        // Time filter buttons for sensors
        document.querySelectorAll('.btn-time').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.handleTimeFilterClick(e.target);
            });
        });

        // Chart range buttons
        document.querySelectorAll('.chart-range').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.handleChartRangeClick(e.target);
            });
        });

        // Refresh button
        document.getElementById('refresh-btn').addEventListener('click', () => {
            this.refreshData();
        });

        // Threshold marker hover
        document.querySelectorAll('.threshold-marker').forEach(marker => {
            marker.addEventListener('mouseenter', (e) => {
                this.showThresholdTooltip(e.target);
            });
            marker.addEventListener('mouseleave', () => {
                this.hideThresholdTooltip();
            });
        });
    }

    setupDataPolling() {
        // Clear existing interval if any
        if (this.dataUpdateInterval) {
            clearInterval(this.dataUpdateInterval);
        }
        
        // Set new interval (every 5 seconds)
        this.dataUpdateInterval = setInterval(() => {
            if (!this.isRefreshing) {
                this.fetchLatestData();
            }
        }, 5000);
    }

    loadInitialData() {
        this.isRefreshing = true;
        this.showLoadingState();
        
        // Simulate loading all sensor data
        Promise.all([
            this.fetchSensorData('mpu', this.currentRange),
            this.fetchSensorData('bme', this.currentRange),
            this.fetchSensorData('piezo', this.currentRange)
        ]).then(() => {
            this.updateAllSensorDisplays();
            this.updateChart();
            this.loadAlertHistory();
            this.isRefreshing = false;
            this.hideLoadingState();
        }).catch(error => {
            console.error('Error loading initial data:', error);
            this.isRefreshing = false;
            this.hideLoadingState();
            this.showNotification('Gagal memuat data awal', 'error');
        });
    }

    fetchLatestData() {
        Object.keys(this.sensorConfigs).forEach(sensorId => {
            this.fetchSensorData(sensorId, this.currentRange, true)
                .then(() => {
                    this.updateSensorDisplay(sensorId);
                    this.updateChart();
                })
                .catch(error => {
                    console.error(`Error fetching ${sensorId} data:`, error);
                });
        });
    }

    fetchSensorData(sensorId, hours, isUpdate = false) {
        return new Promise((resolve) => {
            // Simulate API delay
            setTimeout(() => {
                // Generate realistic time-series data
                const dataPoints = hours === '1' ? 12 : (hours === '6' ? 24 : 48);
                const config = this.sensorConfigs[sensorId];
                
                // Generate base values with realistic variations
                let baseValue;
                if (isUpdate && this.sensorData[sensorId].length > 0) {
                    // Continue from last value for updates
                    const lastValue = this.sensorData[sensorId][this.sensorData[sensorId].length - 1];
                    baseValue = lastValue + (Math.random() * 2 - 1); // Small variation
                } else {
                    // Initial random value within normal range
                    baseValue = config.warningThreshold * 0.3 + Math.random() * config.warningThreshold * 0.4;
                }
                
                // Ensure value stays within reasonable bounds
                baseValue = Math.max(0, Math.min(baseValue, config.maxValue * 1.2));
                
                // Generate time-series data with realistic patterns
                const newData = Array.from({length: dataPoints}, (_, i) => {
                    const position = i / dataPoints;
                    const noise = Math.random() * config.warningThreshold * 0.1;
                    const trend = Math.sin(position * Math.PI * 2) * config.warningThreshold * 0.2;
                    
                    // Occasionally simulate spikes
                    const spike = Math.random() > 0.95 ? 
                        config.dangerThreshold * (0.5 + Math.random()) : 0;
                    
                    return baseValue + trend + noise + spike;
                });
                
                // Apply smoothing
                const smoothedData = this.applyDataSmoothing(newData);
                
                // Update sensor data
                if (isUpdate) {
                    // For updates, keep most recent data and add new point
                    this.sensorData[sensorId] = [...this.sensorData[sensorId].slice(1), smoothedData[smoothedData.length - 1]];
                } else {
                    // For initial load, replace all data
                    this.sensorData[sensorId] = smoothedData;
                }
                
                resolve();
            }, 300);
        });
    }

    applyDataSmoothing(data) {
        // Apply a low-pass filter to smooth the data
        const smoothed = [];
        const alpha = 0.3; // Smoothing factor
        
        if (data.length === 0) return data;
        
        smoothed[0] = data[0];
        for (let i = 1; i < data.length; i++) {
            smoothed[i] = alpha * data[i] + (1 - alpha) * smoothed[i - 1];
        }
        
        return smoothed;
    }

    updateAllSensorDisplays() {
        Object.keys(this.sensorConfigs).forEach(sensorId => {
            this.updateSensorDisplay(sensorId);
        });
    }

    updateSensorDisplay(sensorId) {
        const data = this.sensorData[sensorId];
        if (!data || data.length === 0) return;
        
        const currentValue = data[data.length - 1];
        const config = this.sensorConfigs[sensorId];
        const card = document.getElementById(`${sensorId}-card`);
        
        if (!card) return;
        
        // Update value display
        const valueElement = card.querySelector('.sensor-value');
        if (valueElement) {
            valueElement.textContent = currentValue.toFixed(1);
            valueElement.querySelector('small').textContent = config.unit;
        }
        
        // Update status
        const statusElement = card.querySelector('.sensor-status');
        if (statusElement) {
            if (currentValue >= config.dangerThreshold) {
                statusElement.textContent = 'Bahaya';
                statusElement.className = 'sensor-status badge bg-danger';
            } else if (currentValue >= config.warningThreshold) {
                statusElement.textContent = 'Waspada';
                statusElement.className = 'sensor-status badge bg-warning';
            } else {
                statusElement.textContent = 'Normal';
                statusElement.className = 'sensor-status badge bg-success';
            }
        }
        
        // Update threshold bar
        const percentage = Math.min(100, (currentValue / config.maxValue) * 100);
        const thresholdBar = card.querySelector('.current-value');
        if (thresholdBar) {
            // Change color based on status
            if (currentValue >= config.dangerThreshold) {
                thresholdBar.style.background = 'linear-gradient(90deg, #e74c3c, #c0392b)';
            } else if (currentValue >= config.warningThreshold) {
                thresholdBar.style.background = 'linear-gradient(90deg, #f39c12, #e67e22)';
            } else {
                thresholdBar.style.background = 'linear-gradient(90deg, #2ecc71, #27ae60)';
            }
            
            thresholdBar.style.width = `${percentage}%`;
        }
        
        // Trigger alarm if needed
        if (currentValue >= config.dangerThreshold) {
            this.triggerAlarm(sensorId, currentValue);
        }
    }

    updateChart() {
        if (!this.chart) return;
        
        // Generate time-based labels
        const now = luxon.DateTime.now();
        const labelCount = this.sensorData.mpu.length;
        const intervalMinutes = this.currentRange === '1' ? 5 : 
                              (this.currentRange === '6' ? 15 : 30);
        
        const labels = Array.from({length: labelCount}, (_, i) => {
            return now.minus({minutes: (labelCount - i - 1) * intervalMinutes}).toJSDate();
        });
        
        // Update chart data
        this.chart.data.labels = labels;
        this.chart.data.datasets[0].data = this.sensorData.mpu;
        this.chart.data.datasets[1].data = this.sensorData.bme;
        this.chart.data.datasets[2].data = this.sensorData.piezo;
        
        // Adjust y-axis scale based on data range
        const allValues = [...this.sensorData.mpu, ...this.sensorData.bme, ...this.sensorData.piezo];
        const maxValue = Math.max(...allValues);
        const minValue = Math.min(...allValues);
        
        this.chart.options.scales.y.min = Math.max(0, minValue * 0.9);
        this.chart.options.scales.y.max = maxValue * 1.1;
        
        // Update the chart
        this.chart.update();
    }

    handleTimeFilterClick(btn) {
        // Remove active class from all buttons in the same filter group
        const parent = btn.closest('.time-filter');
        parent.querySelectorAll('.btn-time').forEach(b => {
            b.classList.remove('active');
        });
        
        // Add active class to clicked button
        btn.classList.add('active');
        
        // Get sensor ID from card
        const sensorId = btn.closest('.sensor-card').id.replace('-card', '');
        const hours = btn.dataset.hours;
        
        // Show loading state
        const card = document.getElementById(`${sensorId}-card`);
        if (card) {
            const statusElement = card.querySelector('.sensor-status');
            if (statusElement) {
                statusElement.textContent = 'Memuat...';
                statusElement.className = 'sensor-status badge bg-secondary';
            }
        }
        
        // Fetch new data
        this.fetchSensorData(sensorId, hours)
            .then(() => {
                this.updateSensorDisplay(sensorId);
                this.updateChart();
            })
            .catch(error => {
                console.error(`Error loading ${sensorId} data:`, error);
                this.showNotification(`Gagal memuat data ${this.sensorConfigs[sensorId].name}`, 'error');
            });
    }

    handleChartRangeClick(btn) {
        // Remove active class from all range buttons
        document.querySelectorAll('.chart-range').forEach(b => {
            b.classList.remove('active');
        });
        
        // Add active class to clicked button
        btn.classList.add('active');
        
        // Update chart range
        this.currentRange = btn.dataset.range;
        
        // Show loading state
        document.querySelector('.chart-loading').style.display = 'flex';
        
        // Reload all data with new range
        this.loadInitialData();
    }

    loadAlertHistory() {
        // Simulate loading alert history
        const alerts = [
            {
                time: this.formatTime(new Date()),
                sensor: 'MPU6050',
                value: '72.3 m/s²',
                status: 'danger',
                statusText: 'Bahaya'
            },
            {
                time: this.formatTime(new Date(Date.now() - 3600000)),
                sensor: 'BME280',
                value: '87.5 hPa',
                status: 'danger',
                statusText: 'Bahaya'
            },
            {
                time: this.formatTime(new Date(Date.now() - 7200000)),
                sensor: 'Piezoelektrik',
                value: '52.1 G',
                status: 'warning',
                statusText: 'Waspada'
            }
        ];
        
        const tbody = document.getElementById('alert-history');
        if (tbody) {
            tbody.innerHTML = alerts.map(alert => `
                <tr>
                    <td>${alert.time}</td>
                    <td>${alert.sensor}</td>
                    <td>${alert.value}</td>
                    <td><span class="badge bg-${alert.status}">${alert.statusText}</span></td>
                    <td><button class="btn btn-sm btn-outline-primary">Detail</button></td>
                </tr>
            `).join('');
        }
    }

    triggerAlarm(sensorId, value) {
        const config = this.sensorConfigs[sensorId];
        console.warn(`ALARM: ${config.name} mencapai ${value.toFixed(1)} ${config.unit}`);
        
        // Show visual alarm
        const card = document.getElementById(`${sensorId}-card`);
        if (card) {
            card.classList.add('alarm-active');
            setTimeout(() => {
                card.classList.remove('alarm-active');
            }, 1000);
        }
        
        // Play sound alarm (optional)
        // this.playAlarmSound();
        
        // Add to alert history
        this.addNewAlert({
            sensor: config.name,
            value: `${value.toFixed(1)} ${config.unit}`,
            status: 'danger',
            statusText: 'Bahaya'
        });
    }

    addNewAlert(alert) {
        const tbody = document.getElementById('alert-history');
        if (tbody) {
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>${this.formatTime(new Date())}</td>
                <td>${alert.sensor}</td>
                <td>${alert.value}</td>
                <td><span class="badge bg-${alert.status}">${alert.statusText}</span></td>
                <td><button class="btn btn-sm btn-outline-primary">Detail</button></td>
            `;
            tbody.insertBefore(newRow, tbody.firstChild);
            
            // Limit to 50 alerts
            if (tbody.children.length > 50) {
                tbody.removeChild(tbody.lastChild);
            }
        }
    }

    refreshData() {
        if (this.isRefreshing) return;
        
        this.isRefreshing = true;
        this.showLoadingState();
        this.showNotification('Memperbarui data...', 'info');
        
        // Show refresh button loading state
        const refreshBtn = document.getElementById('refresh-btn');
        refreshBtn.disabled = true;
        refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Memperbarui...';
        
        // Simulate data refresh
        setTimeout(() => {
            this.loadInitialData();
            refreshBtn.disabled = false;
            refreshBtn.innerHTML = '<i class="fas fa-sync-alt me-1"></i> Refresh';
            this.showNotification('Data berhasil diperbarui', 'success');
        }, 1500);
    }

    showLoadingState() {
        // Show loading state for all sensor cards
        Object.keys(this.sensorConfigs).forEach(sensorId => {
            const card = document.getElementById(`${sensorId}-card`);
            if (card) {
                const statusElement = card.querySelector('.sensor-status');
                if (statusElement) {
                    statusElement.textContent = 'Memuat...';
                    statusElement.className = 'sensor-status badge bg-secondary';
                }
                
                const valueElement = card.querySelector('.sensor-value');
                if (valueElement) {
                    valueElement.textContent = '0.0';
                }
                
                const thresholdBar = card.querySelector('.current-value');
                if (thresholdBar) {
                    thresholdBar.style.width = '0%';
                }
            }
        });
    }

    hideLoadingState() {
        // Hide loading states
        document.querySelector('.chart-loading').style.display = 'none';
    }

    showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 
                          type === 'error' ? 'fa-exclamation-circle' : 
                          'fa-info-circle'} me-2"></i>
            ${message}
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }

    formatTime(date) {
        return date.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    }
}

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Register required Chart.js components
    Chart.register(
        Chart.LineController,
        Chart.LineElement,
        Chart.PointElement,
        Chart.LinearScale,
        Chart.TimeScale,
        Chart.Tooltip,
        Chart.Filler,
        Chart.Decimation
    );
    
    // Initialize dashboard
    new AdminDashboard();
});