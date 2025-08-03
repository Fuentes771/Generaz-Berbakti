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
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4cc9f0;
            --dark-color: #1a1a2e;
            --light-color: #f8f9fa;
            --success-color: #4ade80;
            --danger-color: #f87171;
            --transition-speed: 0.4s;
            --nav-height: 70px;
        }
        
        /* Base Navbar Styles */
        .tsunami-navbar {
            height: var(--nav-height);
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: all var(--transition-speed) ease;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1050;
        }
        
        .navbar-container {
            position: relative;
            z-index: 1000;
        }
        
        .navbar-spacer {
            height: var(--nav-height);
        }
        
        /* Brand Logo */
        .navbar-brand {
            display: flex;
            align-items: center;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            color: white !important;
            transition: transform 0.3s ease;
        }
        
        .navbar-brand:hover {
            transform: scale(1.05);
        }
        
        .brand-logo {
            font-size: 1.8rem;
            margin-right: 10px;
            color: var(--accent-color);
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        .brand-text {
            font-size: 1.4rem;
            background: linear-gradient(to right, white, #e0f7fa);
            -webkit-text-fill-color: transparent;
            letter-spacing: 0.5px;
        }
        
        /* Mobile Toggle Button - Premium Design */
        .navbar-toggler {
            position: relative;
            width: 40px;
            height: 40px;
            border: none;
            background: transparent;
            padding: 0;
            cursor: pointer;
            outline: none;
            z-index: 1051;
        }
        
        .navbar-toggler-icon-custom {
            position: relative;
            display: block;
            width: 24px;
            height: 2px;
            background: white;
            transition: all 0.3s ease;
            margin: 0 auto;
        }
        
        .navbar-toggler-icon-custom::before,
        .navbar-toggler-icon-custom::after {
            content: '';
            position: absolute;
            left: 0;
            width: 24px;
            height: 2px;
            background: white;
            transition: all 0.3s ease;
        }
        
        .navbar-toggler-icon-custom::before {
            top: -8px;
        }
        
        .navbar-toggler-icon-custom::after {
            top: 8px;
        }
        
        .navbar-toggler[aria-expanded="true"] .navbar-toggler-icon-custom {
            background: transparent;
        }
        
        .navbar-toggler[aria-expanded="true"] .navbar-toggler-icon-custom::before {
            transform: rotate(45deg);
            top: 0;
        }
        
        .navbar-toggler[aria-expanded="true"] .navbar-toggler-icon-custom::after {
            transform: rotate(-45deg);
            top: 0;
        }
        
        /* Mobile Menu - Premium Design */
        @media (max-width: 991.98px) {
            .navbar-collapse {
                position: fixed;
                top: var(--nav-height);
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(26, 26, 46, 0.98);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                padding: 2rem;
                overflow-y: auto;
                transform: translateX(100%);
                transition: transform 0.5s cubic-bezier(0.77, 0.2, 0.05, 1);
                z-index: 1050;
                margin-top: 0;
                border-radius: 0;
                box-shadow: none;
            }
            
            .navbar-collapse.show {
                transform: translateX(0);
            }
            
            .navbar-nav {
                flex-direction: column;
                margin-bottom: 2rem;
            }
            
            .nav-item {
                opacity: 0;
                transform: translateX(20px);
                transition: all 0.5s ease;
                margin-bottom: 1.5rem;
            }
            
            .navbar-collapse.show .nav-item {
                opacity: 1;
                transform: translateX(0);
            }
            
            /* Staggered animation for nav items */
            .navbar-collapse.show .nav-item:nth-child(1) { transition-delay: 0.1s; }
            .navbar-collapse.show .nav-item:nth-child(2) { transition-delay: 0.2s; }
            .navbar-collapse.show .nav-item:nth-child(3) { transition-delay: 0.3s; }
            .navbar-collapse.show .nav-item:nth-child(4) { transition-delay: 0.4s; }
            
            .nav-link {
                color: white !important;
                font-size: 1.2rem;
                padding: 0.8rem 1rem;
                border-radius: 8px;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                font-family: 'Poppins', sans-serif;
                font-weight: 500;
            }
            
            .nav-link:hover, .nav-link.active {
                background: rgba(255, 255, 255, 0.1);
                transform: translateX(10px);
            }
            
            .nav-link i {
                margin-right: 15px;
                font-size: 1.3rem;
                width: 24px;
                text-align: center;
            }
            
            .navbar-right {
                margin-top: 2rem;
                padding-top: 2rem;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
                opacity: 0;
                transform: translateY(20px);
                transition: all 0.5s ease 0.5s;
            }
            
            .navbar-collapse.show .navbar-right {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Desktop Styles */
        @media (min-width: 992px) {
            .nav-link {
                color: rgba(255, 255, 255, 0.9) !important;
                font-family: 'Poppins', sans-serif;
                font-weight: 500;
                padding: 0.5rem 1rem;
                margin: 0 0.3rem;
                border-radius: 6px;
                transition: all 0.3s ease;
                position: relative;
            }
            
            .nav-link:hover, .nav-link.active {
                color: white !important;
                background: rgba(255, 255, 255, 0.15);
            }
            
            .nav-link::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 50%;
                width: 0;
                height: 2px;
                background: var(--accent-color);
                transition: all 0.3s ease;
                transform: translateX(-50%);
            }
            
            .nav-link:hover::after, .nav-link.active::after {
                width: 60%;
            }
            
            .nav-link i {
                margin-right: 8px;
                font-size: 0.9rem;
            }
        }
        
        /* System Status Badge */
        .system-status {
            display: inline-flex;
            align-items: center;
            font-family: 'Poppins', sans-serif;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.8rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .status-icon {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 8px;
            position: relative;
        }
        
        .status-icon i {
            font-size: 0.5rem;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(74, 222, 128, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(74, 222, 128, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(74, 222, 128, 0);
            }
        }
        
        /* Overlay for mobile menu */
        .nav-overlay {
            position: fixed;
            top: var(--nav-height);
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1049;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .nav-overlay.show {
            opacity: 1;
            visibility: visible;
        }
    </style>
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