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
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tentang Kami | Sistem Peringatan Tsunami</title>

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
<body class="about-page">

  <!-- Navbar -->
  <?php include 'includes/navbar.php'; ?>

  <!-- Hero Section -->
  <section class="about-hero">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6">
          <h1 class="display-3 fw-normal mb-4 animate__animated animate__fadeInDown">Tentang Proyek Kami</span></h1>
          <p class="lead mb-4 animate__animated animate__fadeIn animate__delay-1s">
            Merevolusi keselamatan pesisir dengan teknologi IoT mutakhir untuk deteksi tsunami dan peringatan dini secara real-time.
          </p>
          <div class="d-flex gap-3 animate__animated animate__fadeIn animate__delay-2s">
            <a href="#mission" class="btn btn-primary btn-lg px-4">Misi Kami</a>
            <a href="#team" class="btn btn-outline-dark btn-lg px-4">Tim Kami</a>
          </div>
        </div>
        <div class="col-lg-6 animate__animated animate__fadeInRight">
        </div>
      </div>
    </div>
  </section>

  <!-- Mission Section -->
  <section id="mission" class="mission-section py-5">
    <div class="container">
      <div class="section-header text-center mb-5">
        <h2 class="display-5 fw-normal">Misi Kami</span></h2>
        <div class="divider mx-auto"></div>
      </div>
      
      <div class="row g-4 align-items-center">
        <div class="col-lg-6">
          <div class="mission-card p-4 p-lg-5 shadow-lg rounded-4">
            <div class="icon-box mb-4">
              <i class="fas fa-bullseye"></i>
            </div>
            <h3 class="mb-3">Melindungi Masyarakat Pesisir</h3>
            <p class="mb-4">
              Kami berkomitmen untuk mengembangkan sistem deteksi tsunami yang terjangkau dan sangat andal yang memberikan peringatan dini akurat ke daerah pesisir yang rentan.
            </p>
            <ul class="mission-list">
              <li><i class="fas fa-check-circle"></i> Pemantauan real-time</li>
              <li><i class="fas fa-check-circle"></i> Deteksi ancaman 24/7</li>
              <li><i class="fas fa-check-circle"></i> Sistem peringatan instan</li>
            </ul>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="row g-4">
            <div class="col-md-6">
              <div class="feature-card p-4 h-100 rounded-4">
                <i class="fas fa-clock feature-icon mb-3"></i>
                <h4>Deteksi Dini</h4>
                <p>Mendeteksi ancaman potensial beberapa menit sebelum sistem tradisional</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="feature-card p-4 h-100 rounded-4">
                <i class="fas fa-bolt feature-icon mb-3"></i>
                <h4>Peringatan Cepat</h4>
                <p>Notifikasi instan ke pihak berwenang dan masyarakat</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="feature-card p-4 h-100 rounded-4">
                <i class="fas fa-money-bill-wave feature-icon mb-3"></i>
                <h4>Harga Terjangkau</h4>
                <p>Solusi ekonomis untuk daerah berkembang</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="feature-card p-4 h-100 rounded-4">
                <i class="fas fa-shield-alt feature-icon mb-3"></i>
                <h4>Andal</h4>
                <p>Jaminan waktu aktif sistem 99.9%</p>
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
      <h2 class="display-5 fw-normal">Tim Kami</span></h2>
      <div class="divider mx-auto"></div>
      <p class="lead mx-auto" style="max-width: 700px;">
        Organisasi berdedikasi yang bekerja sama untuk membuat komunitas pesisir lebih aman
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
          <p class="text-primary mb-2">Organisasi Pendukung</p>
          <p class="mb-3">Menyediakan dukungan dan sumber daya penting untuk implementasi</p>
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
          <h3>Tim Rinova</h3>
          <p class="text-primary mb-2">Tim Pengembangan Teknis</p>
          <p class="mb-3">Menyediakan dukungan dan sumber daya penting untuk implementasi</p>
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
        <h2 class="display-5 fw-normal">Teknologi Kami</span></h2>
        <div class="divider mx-auto"></div>
        <p class="lead mx-auto" style="max-width: 700px;">
          Teknologi mutakhir yang mendukung sistem peringatan dini kami
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
            <p class="mb-3">Mikrokontroler hemat daya dengan kemampuan Wi-Fi/Bluetooth</p>
            <div class="tech-progress">
              <div class="progress">
                <div class="progress-bar" role="progressbar" style="width: 95%"></div>
              </div>
              <span>95% Keandalan</span>
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
            <p class="mb-3">Backend kuat untuk pemrosesan dan penyimpanan data</p>
            <div class="tech-progress">
              <div class="progress">
                <div class="progress-bar" role="progressbar" style="width: 90%"></div>
              </div>
              <span>90% Efisiensi</span>
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
            <p class="mb-3">Pemantauan real-time dan visualisasi peringatan</p>
            <div class="tech-progress">
              <div class="progress">
                <div class="progress-bar" role="progressbar" style="width: 92%"></div>
              </div>
              <span>92% Akurasi</span>
            </div>
          </div>
        </div>
        
        <!-- Tech 4 -->
        <div class="col-lg-3 col-md-6">
          <div class="tech-card text-center p-4 rounded-4 h-100">
            <div class="tech-icon mb-4">
              <i class="fas fa-satellite-dish"></i>
            </div>
            <h3 class="mb-3">Sensor IoT</h3>
            <p class="mb-3">Sensor tekanan air dan seismik canggih</p>
            <div class="tech-progress">
              <div class="progress">
                <div class="progress-bar" role="progressbar" style="width: 97%"></div>
              </div>
              <span>97% Presisi</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Call to Action -->
  <section class="cta-section py-5 bg-primary text-white">
    <div class="container text-center">
      <h2 class="display-5 fw-bold mb-4">Siap Menerapkan Sistem Kami?</h2>
      <p class="lead mb-5 mx-auto" style="max-width: 700px;">
        Hubungi kami hari ini untuk membawa perlindungan peringatan dini tsunami ke komunitas pesisir Anda
      </p>
      <div class="d-flex justify-content-center gap-3">
        <a href="#" class="btn btn-light btn-lg px-4">Minta Demo</a>
        <a href="#footer-heading" class="btn btn-outline-light btn-lg px-4">Hubungi Kami</a>
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