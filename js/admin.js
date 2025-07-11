// Update time function
function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    document.getElementById('update-time').textContent = timeString;
}

// Initialize charts
function initCharts() {
    const ctx = document.getElementById('combinedChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: Array.from({length: 12}, (_, i) => `${i*5} menit`),
            datasets: [
                {
                    label: 'MPU6050 (m/s²)',
                    data: [1.2, 1.5, 2.0, 2.8, 3.2, 2.9, 2.5, 2.1, 1.8, 2.0, 2.3, 2.5],
                    borderColor: '#3498db',
                    backgroundColor: 'transparent',
                    tension: 0.4,
                    borderWidth: 2
                },
                {
                    label: 'BME280 (hPa)',
                    data: [12, 12.5, 13, 13.2, 13.5, 13.8, 13.2, 13.0, 12.8, 12.5, 13.0, 13.2],
                    borderColor: '#2ecc71',
                    backgroundColor: 'transparent',
                    tension: 0.4,
                    borderWidth: 2
                },
                {
                    label: 'Piezoelektrik (G)',
                    data: [0.2, 0.3, 0.5, 0.7, 0.8, 0.9, 0.7, 0.6, 0.8, 0.5, 0.4, 0.3],
                    borderColor: '#f39c12',
                    backgroundColor: 'transparent',
                    tension: 0.4,
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });
}

// Time filter functionality
function setupTimeFilters() {
    document.querySelectorAll('.time-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons in the same filter group
            this.parentElement.querySelectorAll('.time-btn').forEach(b => {
                b.classList.remove('active');
            });
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Here you would typically fetch new data based on the selected time period
            const period = this.textContent.trim();
            console.log(`Time period selected: ${period}`);
        });
    });
}

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Update time immediately and every second
    updateTime();
    setInterval(updateTime, 1000);
    
    // Initialize charts
    initCharts();
    
    // Setup time filter buttons
    setupTimeFilters();
    
    // Refresh button functionality
    document.querySelector('.refresh-btn').addEventListener('click', function() {
        console.log('Refreshing data...');
        // Here you would typically refresh the data from the server
    });
});