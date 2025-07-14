<?php
// Include configuration and header
include 'includes/config.php';
include 'includes/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Early Tsunami Detection System for Pekon Teluk Kiluan Negri - Real-time monitoring and detection for tsunami warnings">
    <meta name="keywords" content="tsunami, early warning system, earthquake detection, BMKG, coastal safety">
    <title>Early Tsunami Detection System | RINOVA</title>
    
    <!-- Favicon -->
    <link rel="icon" href="assets/img/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/apple-touch-icon.png">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 hero-content">
                <h1 class="hero-title">
                    <span class="text-gradient">EARLY TSUNAMI</span> <br>
                    DETECTION SYSTEM <br>
                    <span class="text-decorative">Pekon Teluk Kiluan Negri</span>
                </h1>
                <p class="hero-subtitle">Advanced real-time monitoring and detection system for early tsunami warnings...</p>
                <div class="d-flex gap-3">
                    <a href="monitoring.php" class="hero-button">
                        <i class="fas fa-chart-line me-2"></i> View Monitoring
                    </a>
                    <a href="#features" class="hero-button" style="background: transparent; color: var(--color-white);">
                        <i class="fas fa-info-circle me-2"></i> Learn More
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image-container">
                    <img src="assets/img/desta.png" alt="Tsunami Warning System" class="hero-image img-fluid" style="background: transparent;">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="features-section">
    <div class="container">
        <div class="section-header">
            <h2>SYSTEM FEATURES</h2>
            <p>Utilizing IoT technology and real-time data monitoring, this system connects directly with official data sources from BMKG to provide accurate information for quick and efficient response to potential tsunami threats.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h5>Real-time Alerts</h5>
                    <p>Instant notifications when potential tsunami vibrations are detected, with multi-channel warning systems including SMS, sirens, and mobile app push notifications.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h5>Data Visualization</h5>
                    <p>Interactive charts and maps showing vibration patterns, wave heights, and potential impact areas with real-time updates every 30 seconds.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-database"></i>
                    </div>
                    <h5>Historical Data</h5>
                    <p>Comprehensive archive of past event data for analysis, research, and system improvement with customizable reporting tools.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-satellite-dish"></i>
                    </div>
                    <h5>Multi-Sensor Network</h5>
                    <p>Deployment of multiple sensor types including seismic, pressure, and GPS sensors for comprehensive data collection.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <h5>Impact Prediction</h5>
                    <p>Advanced algorithms predict potential impact zones and estimated wave arrival times based on current data.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h5>Redundant Systems</h5>
                    <p>Multiple backup systems ensure continuous operation even during power outages or communication failures.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Gallery Section -->
<section class="gallery-section">
    <div class="container">
        <div class="section-header">
            <h2>OUR SYSTEM DOCUMENTATION</h2>
            <p class="lead">Each device we develop is not just technology, but a tangible manifestation of our commitment to protecting coastal communities. Explore our system components and installation process.</p>
        </div>
        
        <div class="row justify-content-center g-4">
            <?php
            $galleryItems = [
                [
                    'img' => 'assets/img/teluk1.png',
                    'title' => 'Detection Buoy',
                    'desc' => 'Advanced offshore buoy with pressure sensors and GPS positioning'
                ],
                [
                    'img' => 'assets/img/teluk2.png',
                    'title' => 'Seismic Sensor',
                    'desc' => 'High-sensitivity ground vibration detection equipment'
                ],
                [
                    'img' => 'assets/img/teluk3.png',
                    'title' => 'Control Center',
                    'desc' => 'Central monitoring station with data processing capabilities'
                ],
                [
                    'img' => 'assets/img/teluk4.png',
                    'title' => 'Warning Siren',
                    'desc' => 'High-decibel alert system for community notification'
                ],
                [
                    'img' => 'assets/img/teluk1.png',
                    'title' => 'Solar Power Unit',
                    'desc' => 'Sustainable energy source for remote installations'
                ],
                [
                    'img' => 'assets/img/teluk2.png',
                    'title' => 'Communication Hub',
                    'desc' => 'Data transmission center with satellite backup'
                ]
            ];
            
            foreach ($galleryItems as $item) {
                echo '
                <div class="col-lg-4 col-md-6">
                    <div class="gallery-card h-100">
                        <div class="gallery-img-container">
                            <img src="'.$item['img'].'" alt="'.$item['title'].'" class="gallery-card-img">
                        </div>
                        <div class="gallery-card-body">
                            <h5 class="gallery-card-title">'.$item['title'].'</h5>
                            <p class="gallery-card-desc">'.$item['desc'].'</p>
                            <a href="'.$item['img'].'" class="btn btn-sm btn-outline-primary mt-2" data-fslightbox="gallery">View Larger</a>
                        </div>
                    </div>
                </div>';
            }
            ?>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section bg-gradient-primary text-white py-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="display-4 fw-bold">24/7</div>
                <p class="mb-0">Monitoring</p>
            </div>
            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="display-4 fw-bold">5</div>
                <p class="mb-0">Detection Points</p>
            </div>
            <div class="col-md-3 col-6">
                <div class="display-4 fw-bold">3.5</div>
                <p class="mb-0">Minute Response</p>
            </div>
            <div class="col-md-3 col-6">
                <div class="display-4 fw-bold">99.9%</div>
                <p class="mb-0">Uptime</p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fslightbox/3.3.1/index.min.js"></script>
<script>
    // Animation for elements when they come into view
    const animateOnScroll = () => {
        const elements = document.querySelectorAll('.feature-card, .gallery-card');
        
        elements.forEach(element => {
            const elementPosition = element.getBoundingClientRect().top;
            const screenPosition = window.innerHeight / 1.2;
            
            if(elementPosition < screenPosition) {
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }
        });
    };

    // Initialize animations
    window.addEventListener('load', () => {
        document.querySelectorAll('.feature-card, .gallery-card').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        });
        
        animateOnScroll();
    });

    window.addEventListener('scroll', animateOnScroll);
</script>
</body>
</html>