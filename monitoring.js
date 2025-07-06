// Initialize monitoring page elements
$(document).ready(function() {
    // Initialize seismograph chart
    const ctx = document.getElementById('seismograph-chart').getContext('2d');
    const seismographChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'X-axis',
                    data: [],
                    borderColor: '#dc3545',
                    borderWidth: 1,
                    tension: 0.1
                },
                {
                    label: 'Y-axis',
                    data: [],
                    borderColor: '#28a745',
                    borderWidth: 1,
                    tension: 0.1
                },
                {
                    label: 'Z-axis',
                    data: [],
                    borderColor: '#007bff',
                    borderWidth: 1,
                    tension: 0.1
                }
            ]
        },
        options: {
            responsive: true,
            animation: {
                duration: 0
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 10,
                    min: -10
                }
            }
        }
    });

    // Simulate real-time data (replace with actual data fetching)
    let isPaused = false;
    let dataCount = 0;
    
    function simulateData() {
        if (isPaused) return;
        
        dataCount++;
        const timestamp = new Date().toLocaleTimeString();
        const xVal = (Math.random() * 6 - 3).toFixed(2);
        const yVal = (Math.random() * 6 - 3).toFixed(2);
        const zVal = (Math.random() * 6 - 3).toFixed(2);
        const magnitude = (Math.sqrt(xVal*xVal + yVal*yVal + zVal*zVal)).toFixed(2);
        
        // Update chart
        if (seismographChart.data.labels.length > 30) {
            seismographChart.data.labels.shift();
            seismographChart.data.datasets[0].data.shift();
            seismographChart.data.datasets[1].data.shift();
            seismographChart.data.datasets[2].data.shift();
        }
        
        seismographChart.data.labels.push(timestamp);
        seismographChart.data.datasets[0].data.push(xVal);
        seismographChart.data.datasets[1].data.push(yVal);
        seismographChart.data.datasets[2].data.push(zVal);
        seismographChart.update();
        
        // Update data stream table
        const newRow = `
            <tr>
                <td>${timestamp}</td>
                <td>${xVal}</td>
                <td>${yVal}</td>
                <td>${zVal}</td>
                <td>${magnitude}</td>
            </tr>
        `;
        
        $('#data-stream').prepend(newRow);
        
        // Keep only the last 50 rows
        if ($('#data-stream tr').length > 50) {
            $('#data-stream tr:last').remove();
        }
        
        setTimeout(simulateData, 500);
    }
    
    // Start simulation
    simulateData();
    
    // Pause/Resume button
    $('#pause-stream').click(function() {
        isPaused = !isPaused;
        if (isPaused) {
            $(this).html('<i class="fas fa-play"></i> Resume');
            $(this).removeClass('btn-outline-primary').addClass('btn-outline-success');
        } else {
            $(this).html('<i class="fas fa-pause"></i> Pause');
            $(this).removeClass('btn-outline-success').addClass('btn-outline-primary');
            simulateData();
        }
    });
    
    // Clear button
    $('#clear-stream').click(function() {
        $('#data-stream').empty();
    });
});