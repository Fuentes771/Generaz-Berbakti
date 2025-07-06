<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="fas fa-chart-line me-2"></i>Real-time Monitoring</h2>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-wave-square me-2"></i>Seismograf Real-time</h5>
                </div>
                <div class="card-body">
                    <canvas id="seismograph-chart" height="300"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Sensor Status</h5>
                </div>
                <div class="card-body">
                    <div class="sensor-status-list">
                        <div class="d-flex justify-content-between align-items-center mb-3 p-2 border-bottom">
                            <div>
                                <i class="fas fa-vibration text-primary me-2"></i>
                                <span>Vibration Sensor</span>
                            </div>
                            <span class="badge bg-success">Active</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3 p-2 border-bottom">
                            <div>
                                <i class="fas fa-tachometer-alt text-primary me-2"></i>
                                <span>Accelerometer</span>
                            </div>
                            <span class="badge bg-success">Active</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3 p-2 border-bottom">
                            <div>
                                <i class="fas fa-water text-primary me-2"></i>
                                <span>Water Level</span>
                            </div>
                            <span class="badge bg-success">Active</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3 p-2 border-bottom">
                            <div>
                                <i class="fas fa-satellite-dish text-primary me-2"></i>
                                <span>GPS Module</span>
                            </div>
                            <span class="badge bg-success">Active</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Raw Data Stream</h5>
                    <div>
                        <button class="btn btn-sm btn-outline-primary me-2" id="pause-stream">
                            <i class="fas fa-pause"></i> Pause
                        </button>
                        <button class="btn btn-sm btn-outline-danger" id="clear-stream">
                            <i class="fas fa-trash"></i> Clear
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Timestamp</th>
                                    <th>X-axis</th>
                                    <th>Y-axis</th>
                                    <th>Z-axis</th>
                                    <th>Magnitude</th>
                                </tr>
                            </thead>
                            <tbody id="data-stream">
                                <!-- Data will be streamed here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Audio Element for Alerts (hidden) -->
<audio id="alert-sound" loop>
    <source src="alert.mp3" type="audio/mpeg">
</audio>

<script src="../js/monitoring.js"></script>
<?php include 'includes/footer.php'; ?>