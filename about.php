<?php
// Include konfigurasi dan header
include 'includes/config.php';
include 'includes/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Tsunami Warning System</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/about.css">
</head>
<body class="about-body">

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Project Introduction -->
            <div class="about-card p-4 mb-4">
                <h1 class="display-4 mb-4">About Our Project</h1>
                <p class="lead">
                    Tsunami Early Warning System is an IoT-based solution designed to detect potential tsunami threats and provide early warnings.
                </p>
            </div>

            <!-- Mission Card -->
            <div class="about-card p-4 mb-4">
                <h3><i class="fas fa-bullseye me-2"></i> Our Mission</h3>
                <p>
                    To develop a reliable and affordable tsunami detection system that can save lives by providing early warnings to coastal communities.
                </p>
            </div>

            <!-- Team Card -->
            <div class="about-card p-4 mb-4">
                <h3><i class="fas fa-users me-2"></i> Our Team</h3>
                <div class="row mt-3">
                    <div class="col-md-6 mb-3">
                        <div class="team-member p-3">
                            <h5>M Sulthon Alfarizky</h5>
                            <p class="text-muted">Project Lead</p>
                            <p>Electrical Engineering Student at Universitas Lampung</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="team-member p-3">
                            <h5>BEM U KBM UNILA</h5>
                            <p class="text-muted">Supporting Organization</p>
                            <p>Student organization providing resources and support</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Technology Stack -->
            <div class="about-card p-4">
                <h3><i class="fas fa-cogs me-2"></i> Technology Stack</h3>
                <div class="row mt-3">
                    <div class="col-md-4 mb-3">
                        <div class="tech-item p-3">
                            <i class="fas fa-microchip fa-3x mb-2"></i>
                            <h5>ESP32</h5>
                            <p class="small">Microcontroller for sensor data collection</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="tech-item p-3">
                            <i class="fas fa-server fa-3x mb-2"></i>
                            <h5>PHP/MySQL</h5>
                            <p class="small">Backend for data processing and storage</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="tech-item p-3">
                            <i class="fas fa-chart-line fa-3x mb-2"></i>
                            <h5>Data Visualization</h5>
                            <p class="small">Real-time monitoring dashboard</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Decorative Elements -->
<img src="assets/img/dolphin.png" class="decorative dolphin" alt="Dolphin decoration">
<img src="assets/img/coral.png" class="decorative coral" alt="Coral decoration">

<?php include 'includes/footer.php'; ?>

<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>