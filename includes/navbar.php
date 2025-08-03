<?php
/**
 * Navigation Bar Component - Complete Version
 * 
 * @version 2.3.1
 * @fix Fixed mobile toggle functionality
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
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom styles for mobile toggle */
        .navbar-toggler {
            border: none;
            padding: 0.5rem;
            font-size: 1.25rem;
            line-height: 1;
            background-color: transparent;
            transition: all 0.3s ease;
        }
        
        .navbar-toggler:focus {
            outline: none;
            box-shadow: none;
        }
        
        .navbar-toggler-icon-custom {
            display: inline-block;
            width: 1.5em;
            height: 1.5em;
            vertical-align: middle;
            background-image: none;
            pointer-events: none; /* Add this to prevent icon from capturing clicks */
        }
        
        @media (max-width: 991.98px) {
            .navbar-collapse {
                padding: 1rem;
                background-color: #f8f9fa;
                border-radius: 0.25rem;
                margin-top: 0.5rem;
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            }
            
            .navbar-nav {
                margin-bottom: 1rem;
            }
            
            .navbar-right {
                margin-top: 1rem;
                padding-top: 1rem;
                border-top: 1px solid #dee2e6;
            }
        }
    </style>
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
                
                <!-- Enhanced Mobile Toggle Button -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" 
                        aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon-custom">
                        <i class="fas fa-bars"></i>
                    </span>
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
                                SYSTEM ACTIVE
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
    
    <!-- Enhanced Navbar Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Bootstrap collapse
            var navbarCollapse = document.getElementById('mainNavbar');
            var navLinks = document.querySelectorAll('.nav-link');
            var navbarToggler = document.querySelector('.navbar-toggler');
            
            // Close navbar when clicking on nav links (mobile view)
            navLinks.forEach(function(navLink) {
                navLink.addEventListener('click', function() {
                    if (window.innerWidth < 992) { // Only for mobile view
                        var bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                            toggle: false
                        });
                        bsCollapse.hide();
                    }
                });
            });
            
            // Prevent default behavior if href is "#"
            document.querySelectorAll('a[href="#"]').forEach(function(anchor) {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                });
            });
            
            // Fix for mobile toggle button - prevent any default behavior
            if (navbarToggler) {
                navbarToggler.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
        });
    </script>
</body>
</html>