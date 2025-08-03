<?php
/**
 * Premium Navigation Bar Component 
 * 
 * @version 3.0.0
 * @feature Enhanced mobile experience with animations and premium styling
 */

if (!function_exists('check_system_status')) {
    function check_system_status() {
        return true; // Replace with actual system status logic
    }
}
$navConfig = NAVBAR_CONFIG;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar Container -->
    <div class="navbar-container">
        <!-- Overlay for mobile menu -->
        <div class="nav-overlay" id="navOverlay"></div>
        
        <nav class="navbar navbar-expand-lg tsunami-navbar">
            <div class="container-fluid px-4">
                <!-- Brand Logo - Left Side -->
                <a class="navbar-brand" href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>">
                    <span class="brand-logo">
                        <i class="fas fa-water"></i>
                    </span>
                    <span class="brand-text">Tsunami Alert</span>
                </a>
                
                <!-- Premium Mobile Toggle Button -->
                <button class="navbar-toggler" type="button" aria-controls="mainNavbar" 
                        aria-expanded="false" aria-label="Toggle navigation" id="navbarToggler">
                    <span class="navbar-toggler-icon-custom"></span>
                </button>
                
                <!-- Navigation Content -->
                <div class="collapse navbar-collapse" id="mainNavbar">
                    <!-- Empty space to push items to right -->
                    <div class="me-auto"></div>
                    
                    <!-- Right-aligned Navigation Links -->
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) === 'index.php' ? 'active' : '' ?>" 
                               href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>">
                                <i class="fas fa-home fa-fw"></i> Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) === 'monitoring.php' ? 'active' : '' ?>" 
                               href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/monitoring.php">
                                <i class="fas fa-wave-square fa-fw"></i> Monitoring
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) === 'bmkg.php' ? 'active' : '' ?>" 
                               href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/bmkg.php">
                                <i class="fas fa-info-circle fa-fw"></i> Peringatan BMKG
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Right-aligned System Status -->
                    <div class="navbar-right">
                        <div class="system-status">
                            <span class="status-badge bg-<?= check_system_status() ? 'success' : 'danger' ?>">
                                <span class="status-icon <?= check_system_status() ? 'pulse' : '' ?>">
                                    <i class="fas fa-circle"></i>
                                </span>
                                SYSTEM <?= check_system_status() ? 'ACTIVE' : 'OFFLINE' ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <!-- Content Spacer -->
    <div class="navbar-spacer"></div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Premium Navbar Script with Enhanced Interactions -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navbarToggler = document.getElementById('navbarToggler');
            const navbarCollapse = document.getElementById('mainNavbar');
            const navOverlay = document.getElementById('navOverlay');
            const navLinks = document.querySelectorAll('.nav-link');
            
            // Initialize Bootstrap collapse
            const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                toggle: false
            });
            
            // Custom toggle functionality
            navbarToggler.addEventListener('click', function() {
                const isExpanded = this.getAttribute('aria-expanded') === 'true';
                this.setAttribute('aria-expanded', !isExpanded);
                
                if (!isExpanded) {
                    bsCollapse.show();
                    navOverlay.classList.add('show');
                    document.body.style.overflow = 'hidden';
                } else {
                    bsCollapse.hide();
                    navOverlay.classList.remove('show');
                    document.body.style.overflow = '';
                }
            });
            
            // Close navbar when clicking on overlay
            navOverlay.addEventListener('click', function() {
                navbarToggler.setAttribute('aria-expanded', 'false');
                bsCollapse.hide();
                this.classList.remove('show');
                document.body.style.overflow = '';
            });
            
            // Close navbar when clicking on nav links (mobile view)
            navLinks.forEach(function(navLink) {
                navLink.addEventListener('click', function() {
                    if (window.innerWidth < 992) {
                        navbarToggler.setAttribute('aria-expanded', 'false');
                        bsCollapse.hide();
                        navOverlay.classList.remove('show');
                        document.body.style.overflow = '';
                    }
                });
            });
            
            // Add ripple effect to nav links
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (window.innerWidth >= 992) {
                        const rect = this.getBoundingClientRect();
                        const x = e.clientX - rect.left;
                        const y = e.clientY - rect.top;
                        
                        const ripple = document.createElement('span');
                        ripple.className = 'ripple-effect';
                        ripple.style.left = `${x}px`;
                        ripple.style.top = `${y}px`;
                        
                        this.appendChild(ripple);
                        
                        setTimeout(() => {
                            ripple.remove();
                        }, 1000);
                    }
                });
            });
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 992) {
                    // Ensure navbar is always visible on desktop
                    if (navbarCollapse.classList.contains('show')) {
                        bsCollapse.hide();
                        navOverlay.classList.remove('show');
                        document.body.style.overflow = '';
                    }
                }
            });
        });
    </script>
</body>
</html>