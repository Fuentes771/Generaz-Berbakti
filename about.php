<?php
// Definisikan BASE_URL sebelum include navbar
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . '/');
define('CURRENT_PAGE', basename($_SERVER['SCRIPT_NAME']));
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>About Us | Tsunami Warning</title>

  <!-- Custom CSS -->
  <link rel="stylesheet" href="style/styles.css" />

  <!-- Bootstrap & Font Awesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>
<body>

  <!-- Navbar -->
  <?php include 'includes/navbar.php'; ?>

  <!-- Background Decoration -->
  <img src="images/dolphin.png" alt="Dolphin" class="dolphin" />
  <img src="images/coral.png" alt="Coral" class="coral" />

  <!-- MAIN SECTION -->
  <main class="container py-5">
    <div class="glass-content mx-auto px-4 px-md-5">

      <!-- Header -->
      <section class="text-center mb-5">
        <h1 class="display-5 fw-bold text-glow">About Our Project</h1>
        <p class="lead">
          Our <strong>Tsunami Early Warning System</strong> leverages IoT technology to detect tsunami threats in real-time, empowering coastal communities with early, lifesaving alerts.
        </p>
      </section>

      <!-- 1. Mission -->
      <section class="row align-items-center mb-5">
        <div class="col-md-6">
          <img src="images/mission.png" class="img-fluid rounded-3 shadow" alt="Mission Illustration" />
        </div>
        <div class="col-md-6 mt-4 mt-md-0">
          <div class="card p-4">
            <h3><i class="fas fa-bullseye me-2"></i>Our Mission</h3>
            <p>
              To build a low-cost, highly reliable tsunami detection solution that alerts people early and accurately using real-time data monitoring — so action can be taken before disaster strikes.
            </p>
          </div>
        </div>
      </section>

      <!-- 2. Our Team -->
      <section class="mb-5">
        <h3 class="mb-4"><i class="fas fa-users me-2"></i>Meet the Team</h3>
        <div class="row g-4">
          <div class="col-md-6">
            <div class="team-member p-4 h-100">
              <h5>M Sulthon Alfarizky</h5>
              <p class="text-muted mb-1">Project Lead</p>
              <p>Electrical Engineering Student – Universitas Lampung</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="team-member p-4 h-100">
              <h5>BEM U KBM UNILA</h5>
              <p class="text-muted mb-1">Supporting Organization</p>
              <p>Providing essential support, logistics, and resources for implementation</p>
            </div>
          </div>
        </div>
      </section>

      <!-- 3. Tech Stack -->
      <section>
        <h3 class="mb-4"><i class="fas fa-cogs me-2"></i>Technology Stack</h3>
        <div class="row g-4">
          <div class="col-md-4">
            <div class="tech-item p-4 text-center h-100">
              <i class="fas fa-microchip fa-3x mb-3"></i>
              <h5>ESP32</h5>
              <p class="text-muted small">Handles real-time sensor input & wireless transmission</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="tech-item p-4 text-center h-100">
              <i class="fas fa-server fa-3x mb-3"></i>
              <h5>PHP / MySQL</h5>
              <p class="text-muted small">Back-end system for data storage and processing</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="tech-item p-4 text-center h-100">
              <i class="fas fa-chart-line fa-3x mb-3"></i>
              <h5>Dashboard</h5>
              <p class="text-muted small">Visual interface for real-time warning & monitoring</p>
            </div>
          </div>
        </div>
      </section>

    </div>
  </main>

  <!-- Footer -->
  <?php include 'php/footer.php'; ?>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
