<?php
// Mulai session (jika menggunakan fitur login)
session_start();

// Definisikan BASE_URL sebelum include navbar
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . '/');
define('CURRENT_PAGE', basename($_SERVER['SCRIPT_NAME']));

// Pastikan file navbar ada
if (!file_exists('includes/navbar.php')) {
    die("Error: File navbar.php tidak ditemukan!");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>About Us | Tsunami Warning</title>

  <!-- Favicon -->
  <link rel="icon" href="<?= BASE_URL ?>images/wave-icon.png" type="image/png">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?= BASE_URL ?>style/styles.css" />

  <!-- Bootstrap & Font Awesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <!-- Animate.css -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

  <!-- Navbar CSS -->
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/navbar.css" />
</head>
<body> class="about-page">

  <!-- Navbar -->
  <?php include 'includes/navbar.php'; ?>

  <!-- Hero Section -->
  <section class="about-hero">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6">
          <h1 class="display-3 fw-bold mb-4 animate__animated animate__fadeInDown">About Our <span class="text-primary">Project</span></h1>
          <p class="lead mb-4 animate__animated animate__fadeIn animate__delay-1s">
            Revolutionizing coastal safety with cutting-edge IoT technology for real-time tsunami detection and early warning.
          </p>
          <div class="d-flex gap-3 animate__animated animate__fadeIn animate__delay-2s">
            <a href="#mission" class="btn btn-primary btn-lg px-4">Our Mission</a>
            <a href="#team" class="btn btn-outline-dark btn-lg px-4">Meet the Team</a>
          </div>
        </div>
        <div class="col-lg-6 animate__animated animate__fadeInRight">
          <img src="<?= BASE_URL ?>images/tsunami-illustration.png" alt="Tsunami Early Warning System" class="img-fluid">
        </div>
      </div>
    </div>
  </section>

  <!-- Mission Section -->
  <section id="mission" class="mission-section py-5">
    <div class="container">
      <div class="section-header text-center mb-5">
        <h2 class="display-5 fw-bold">Our <span class="text-primary">Mission</span></h2>
        <div class="divider mx-auto"></div>
      </div>
      
      <div class="row g-4 align-items-center">
        <div class="col-lg-6">
          <div class="mission-card p-4 p-lg-5 shadow-lg rounded-4">
            <div class="icon-box mb-4">
              <i class="fas fa-bullseye"></i>
            </div>
            <h3 class="mb-3">Protecting Coastal Communities</h3>
            <p class="mb-4">
              We're committed to developing a low-cost, highly reliable tsunami detection system that provides accurate early warnings to vulnerable coastal areas.
            </p>
            <ul class="mission-list">
              <li><i class="fas fa-check-circle"></i> Real-time monitoring</li>
              <li><i class="fas fa-check-circle"></i> 24/7 threat detection</li>
              <li><i class="fas fa-check-circle"></i> Instant alert system</li>
            </ul>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="row g-4">
            <div class="col-md-6">
              <div class="feature-card p-4 h-100 rounded-4">
                <i class="fas fa-clock feature-icon mb-3"></i>
                <h4>Early Detection</h4>
                <p>Detects potential threats minutes before traditional systems</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="feature-card p-4 h-100 rounded-4">
                <i class="fas fa-bolt feature-icon mb-3"></i>
                <h4>Rapid Alerts</h4>
                <p>Instant notifications to authorities and communities</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="feature-card p-4 h-100 rounded-4">
                <i class="fas fa-money-bill-wave feature-icon mb-3"></i>
                <h4>Cost Effective</h4>
                <p>Affordable solution for developing regions</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="feature-card p-4 h-100 rounded-4">
                <i class="fas fa-shield-alt feature-icon mb-3"></i>
                <h4>Reliable</h4>
                <p>99.9% system uptime guarantee</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

<!-- Team Section -->
<section id="team" class="team-section py-5 bg-light">
  <div class="container">
    <div class="section-header text-center mb-5">
      <h2 class="display-5 fw-bold">Meet Our <span class="text-primary">Team</span></h2>
      <div class="divider mx-auto"></div>
      <p class="lead mx-auto" style="max-width: 700px;">
        Dedicated organizations working together to make coastal communities safer
      </p>
    </div>
    
    <div class="row g-4 justify-content-center">
      <!-- BEM U KBM UNILA -->
      <div class="col-lg-4 col-md-6">
        <div class="team-card text-center p-4 rounded-4 h-100">
          <div class="team-img mx-auto mb-4">
            <img src="<?= BASE_URL ?>images/team/bem-unila.jpg" alt="BEM U KBM UNILA" class="rounded-circle">
          </div>
          <h3>BEM U KBM UNILA</h3>
          <p class="text-primary mb-2">Supporting Organization</p>
          <p class="mb-3">Providing essential support and resources for implementation</p>
          <div class="social-links">
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fas fa-globe"></i></a>
          </div>
        </div>
      </div>
      
      <!-- Team Rinova -->
      <div class="col-lg-4 col-md-6">
        <div class="team-card text-center p-4 rounded-4 h-100">
          <div class="team-img mx-auto mb-4">
            <img src="<?= BASE_URL ?>images/team/rinova.jpg" alt="Team Rinova" class="rounded-circle">
          </div>
          <h3>Team Rinova</h3>
          <p class="text-primary mb-2">Technical Development Team</p>
          <p class="mb-3">Providing essential support and resources for implementation</p>
          <div class="social-links">
              <a href="#"><i class="fab fa-instagram"></i></a>
              <a href="#"><i class="fas fa-globe"></i></a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

  <!-- Technology Stack -->
  <section class="tech-section py-5">
    <div class="container">
      <div class="section-header text-center mb-5">
        <h2 class="display-5 fw-bold">Our <span class="text-primary">Technology</span></h2>
        <div class="divider mx-auto"></div>
        <p class="lead mx-auto" style="max-width: 700px;">
          Cutting-edge technologies powering our early warning system
        </p>
      </div>
      
      <div class="row g-4">
        <!-- Tech 1 -->
        <div class="col-lg-3 col-md-6">
          <div class="tech-card text-center p-4 rounded-4 h-100">
            <div class="tech-icon mb-4">
              <i class="fas fa-microchip"></i>
            </div>
            <h3 class="mb-3">ESP32</h3>
            <p class="mb-3">Low-power microcontroller with Wi-Fi/Bluetooth capabilities</p>
            <div class="tech-progress">
              <div class="progress">
                <div class="progress-bar" role="progressbar" style="width: 95%"></div>
              </div>
              <span>95% Reliability</span>
            </div>
          </div>
        </div>
        
        <!-- Tech 2 -->
        <div class="col-lg-3 col-md-6">
          <div class="tech-card text-center p-4 rounded-4 h-100">
            <div class="tech-icon mb-4">
              <i class="fas fa-server"></i>
            </div>
            <h3 class="mb-3">PHP/MySQL</h3>
            <p class="mb-3">Robust backend for data processing and storage</p>
            <div class="tech-progress">
              <div class="progress">
                <div class="progress-bar" role="progressbar" style="width: 90%"></div>
              </div>
              <span>90% Efficiency</span>
            </div>
          </div>
        </div>
        
        <!-- Tech 3 -->
        <div class="col-lg-3 col-md-6">
          <div class="tech-card text-center p-4 rounded-4 h-100">
            <div class="tech-icon mb-4">
              <i class="fas fa-chart-line"></i>
            </div>
            <h3 class="mb-3">Dashboard</h3>
            <p class="mb-3">Real-time monitoring and alert visualization</p>
            <div class="tech-progress">
              <div class="progress">
                <div class="progress-bar" role="progressbar" style="width: 92%"></div>
              </div>
              <span>92% Accuracy</span>
            </div>
          </div>
        </div>
        
        <!-- Tech 4 -->
        <div class="col-lg-3 col-md-6">
          <div class="tech-card text-center p-4 rounded-4 h-100">
            <div class="tech-icon mb-4">
              <i class="fas fa-satellite-dish"></i>
            </div>
            <h3 class="mb-3">IoT Sensors</h3>
            <p class="mb-3">Advanced water pressure and seismic sensors</p>
            <div class="tech-progress">
              <div class="progress">
                <div class="progress-bar" role="progressbar" style="width: 97%"></div>
              </div>
              <span>97% Precision</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Call to Action -->
  <section class="cta-section py-5 bg-primary text-white">
    <div class="container text-center">
      <h2 class="display-5 fw-bold mb-4">Ready to Implement Our System?</h2>
      <p class="lead mb-5 mx-auto" style="max-width: 700px;">
        Contact us today to bring tsunami early warning protection to your coastal community
      </p>
      <div class="d-flex justify-content-center gap-3">
        <a href="#" class="btn btn-light btn-lg px-4">Request Demo</a>
        <a href="#" class="btn btn-outline-light btn-lg px-4">Contact Us</a>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <?php include 'includes/footer.php'; ?>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- Custom JS -->
  <script src="<?= BASE_URL ?>js/scripts.js"></script>
  
  <!-- Animation on Scroll -->
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init({
      duration: 1000,
      once: true
    });
  </script>
</body>
</html>