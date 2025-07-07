<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <!-- Brand -->
    <a class="navbar-brand" href="index.php">
      <i class="fas fa-water me-2"></i> Tsunami Warning
    </a>

    <!-- Toggler for mobile -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar links -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link active" href="index.php">
            <i class="fas fa-home me-1"></i> Home
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="monitoring.php">
            <i class="fas fa-chart-line me-1"></i> Monitoring
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="about.php">
            <i class="fas fa-info-circle me-1"></i> About Us
          </a>
        </li>
      </ul>

      <!-- Right side: connection status & user info -->
      <div class="d-flex">
        <div id="connection-status" class="badge bg-success p-2 me-3">
          <i class="fas fa-circle"></i> Connected
        </div>
        <div class="navbar-text">
          <i class="fas fa-user me-1"></i> Admin
        </div>
      </div>
    </div>
  </div>
</nav>