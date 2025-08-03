<?php
/**
 * Enhanced Navigation Bar Component - Responsive Version
 * 
 * @version 3.0.0
 * Features:
 * - Fully responsive design
 * - Mobile-friendly dropdown menu
 * - Animated hamburger icon
 * - Better accessibility
 * - Improved visual hierarchy
 */

if (!function_exists('check_system_status')) {
    function check_system_status() {
        return true; // Replace with actual system status logic
    }
}

$navConfig = NAVBAR_CONFIG ?? [];
$current_page = basename($_SERVER['SCRIPT_NAME']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navbar Container -->
    <div class="navbar-container">
        <nav class="navbar navbar-expand-lg tsunami-navbar">
            <div class="container-fluid px-4">
                <!-- Brand Logo - Left Side -->
                <a class="navbar-brand" href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>">
                    <span class="brand-logo">
                        <i class="fas fa-water"></i>
                    </span>
                    <span class="brand-text">Tsunami Alert</span>
                </a>
                
                <!-- Mobile Toggle Button - Enhanced with Animation -->
                <button class="custom-toggler navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <!-- Navigation Content -->
                <div class="collapse navbar-collapse" id="mainNavbar">
                    <!-- Empty space to push items to right -->
                    <div class="me-auto"></div>
                    
                    <!-- Right-aligned Navigation Links -->
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link <?= $current_page === 'index.php' ? 'active' : '' ?>" 
                               href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>">
                                <i class="fas fa-home fa-fw"></i> <span class="nav-text">Home</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?= in_array($current_page, ['monitoring.php', 'sensor-data.php', 'map-view.php']) ? 'active' : '' ?>" 
                               href="#" id="monitoringDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-wave-square fa-fw"></i> <span class="nav-text">Monitoring</span>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="monitoringDropdown">
                                <li>
                                    <a class="dropdown-item <?= $current_page === 'monitoring.php' ? 'active' : '' ?>" 
                                       href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/monitoring.php">
                                        <i class="fas fa-chart-line fa-fw me-2"></i> Real-time Data
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item <?= $current_page === 'sensor-data.php' ? 'active' : '' ?>" 
                                       href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/sensor-data.php">
                                        <i class="fas fa-microchip fa-fw me-2"></i> Sensor Status
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item <?= $current_page === 'map-view.php' ? 'active' : '' ?>" 
                                       href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/map-view.php">
                                        <i class="fas fa-map-marked-alt fa-fw me-2"></i> Map View
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?= in_array($current_page, ['bmkg.php', 'history.php', 'alerts.php']) ? 'active' : '' ?>" 
                               href="#" id="bmkgDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-info-circle fa-fw"></i> <span class="nav-text">Peringatan BMKG</span>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="bmkgDropdown">
                                <li>
                                    <a class="dropdown-item <?= $current_page === 'bmkg.php' ? 'active' : '' ?>" 
                                       href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/bmkg.php">
                                        <i class="fas fa-bell fa-fw me-2"></i> Current Alerts
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item <?= $current_page === 'history.php' ? 'active' : '' ?>" 
                                       href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/history.php">
                                        <i class="fas fa-history fa-fw me-2"></i> Alert History
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item <?= $current_page === 'alerts.php' ? 'active' : '' ?>" 
                                       href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/alerts.php">
                                        <i class="fas fa-exclamation-triangle fa-fw me-2"></i> All Warnings
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    
                    <!-- Right-aligned System Status -->
                    <div class="navbar-right d-flex align-items-center">
                        <!-- System Status - Far Right -->
                        <div class="system-status me-3">
                            <span class="status-badge bg-<?= check_system_status() ? 'success' : 'danger' ?>">
                                <span class="status-icon <?= check_system_status() ? 'pulse' : '' ?>">
                                    <i class="fas fa-circle"></i>
                                </span>
                                <span class="status-text">SYSTEM <?= check_system_status() ? 'ACTIVE' : 'DOWN' ?></span>
                            </span>
                        </div>
                        
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- User Dropdown -->
                        <div class="dropdown user-dropdown">
                            <button class="btn user-btn dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="user-avatar me-2">
                                    <i class="fas fa-user-circle"></i>
                                </span>
                                <span class="username"><?= htmlspecialchars($_SESSION['username'] ?? 'User', ENT_QUOTES, 'UTF-8') ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item" href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>admin">
                                        <i class="fas fa-cog fa-fw me-2"></i>Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <i class="fas fa-user fa-fw me-2"></i>Profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <i class="fas fa-bell fa-fw me-2"></i>Notifications
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>logout.php">
                                        <i class="fas fa-sign-out-alt fa-fw me-2"></i>Logout
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <!-- Content Spacer -->
    <div class="navbar-spacer"></div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enhanced mobile menu functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Close dropdowns when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.dropdown') && !e.target.closest('.navbar-toggler')) {
                    const openDropdowns = document.querySelectorAll('.dropdown-menu.show');
                    openDropdowns.forEach(function(dropdown) {
                        dropdown.classList.remove('show');
                    });
                }
            });
            
            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                });
            });
            
            // Add active class to parent dropdown when child is active
            document.querySelectorAll('.dropdown-item.active').forEach(item => {
                item.closest('.dropdown').querySelector('.nav-link').classList.add('active');
            });
        });
    </script>
</body>
</html>