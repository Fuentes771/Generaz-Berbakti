/**
 * Admin Dashboard Controller
 * 
 * @version 2.0.0
 */

class AdminDashboard {
    constructor() {
        this.chart = null;
        this.init();
    }

    init() {
        this.setupTimeUpdater();
        this.initCharts();
        this.setupEventListeners();
    }

    setupTimeUpdater() {
        this.updateTime();
        setInterval(() => this.updateTime(), 1000);
    }

    updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        document.getElementById('update-time').textContent = timeString;
    }

    initCharts() {
        const ctx = document.getElementById('combinedChart').getContext('2d');
        this.chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: Array.from({length: 12}, (_, i) => `${i*5} menit`),
                datasets: [
                    this.createDataset('MPU6050 (m/s²)', [1.2, 1.5, 2.0, 2.8, 3.2, 2.9, 2.5, 2.1, 1.8, 2.0, 2.3, 2.5], '#3498db'),
                    this.createDataset('BME280 (hPa)', [12, 12.5, 13, 13.2, 13.5, 13.8, 13.2, 13.0, 12.8, 12.5, 13.0, 13.2], '#2ecc71'),
                    this.createDataset('Piezoelektrik (G)', [0.2, 0.3, 0.5, 0.7, 0.8, 0.9, 0.7, 0.6, 0.8, 0.5, 0.4, 0.3], '#f39c12')
                ]
            },
            options: this.getChartOptions()
        });
    }

    createDataset(label, data, borderColor) {
        return {
            label,
            data,
            borderColor,
            backgroundColor: 'transparent',
            tension: 0.4,
            borderWidth: 2
        };
    }

    getChartOptions() {
        return {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { mode: 'index', intersect: false }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    grid: { display: false }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        };
    }

    setupEventListeners() {
        this.setupTimeFilters();
        document.querySelector('.refresh-btn').addEventListener('click', () => this.refreshData());
    }

    setupTimeFilters() {
        document.querySelectorAll('.time-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                this.handleTimeFilterClick(btn);
            });
        });
    }

    handleTimeFilterClick(btn) {
        // Remove active class from all buttons in the same filter group
        btn.parentElement.querySelectorAll('.time-btn').forEach(b => {
            b.classList.remove('active');
        });
        
        // Add active class to clicked button
        btn.classList.add('active');
        
        // Handle time period selection
        const period = btn.textContent.trim();
        this.fetchDataByPeriod(period);
    }

    fetchDataByPeriod(period) {
        console.log(`Fetching data for period: ${period}`);
        // Implement actual data fetching logic here
    }

    refreshData() {
        console.log('Refreshing data...');
        // Implement data refresh logic here
    }
}

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new AdminDashboard();
});