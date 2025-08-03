<?php
/**
 * Enhanced Navigation Bar Component
 * 
 * @version 3.0.0
 * @fix Fully responsive mobile navigation with proper styling and functionality
 */

if (!function_exists('check_system_status')) {
    function check_system_status() {
        return true; // Replace with actual system status logic
    }
}

// Ensure BASE_URL is defined
if (!defined('BASE_URL')) {
    define('BASE_URL', '/');
}

// Default navbar config if not defined
if (!defined('NAVBAR_CONFIG')) {
    $navConfig = [
        'brand' => [
            'logo' => 'fas fa-water',
            'text' => 'Tsunami Alert'
        ],
        'links' => [
            'home' => [
                'icon' => 'fas fa-home',
                'text' => 'Home',
                'page' => 'index.php'
            ],
            'monitoring' => [
                'icon' => 'fas fa-wave-square',
                'text' => 'Monitoring',
                'page' => 'monitoring.php'
            ],
            'bmkg' => [
                'icon' => 'fas fa-info-circle',
                'text' => 'Peringatan BMKG',
                'page' => 'bmkg.php'
            ]
        ]
    ];
} else {
    $navConfig = NAVBAR_CONFIG;
}

// Get current page
$current_page = basename($_SERVER['SCRIPT_NAME']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tsunami Alert System</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --danger-color: #dc3545;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --navbar-bg: #ffffff;
            --navbar-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        .navbar-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            box-shadow: var(--navbar-shadow);
        }
        
        .tsunami-navbar {
            background-color: var(--navbar-bg);
            padding: 0.5rem 0;
        }
        
        .navbar-brand {
            display: flex;
            align-items: center;
            font-weight: 700;
            color: var(--dark-color);
        }
        
        .brand-logo {
            margin-right: 0.75rem;
            font-size: 1.5rem;
            color: var(--primary-color);
        }
        
        .brand-text {
            font-size: 1.25rem;
        }
        
        .nav-link {
            padding: 0.5rem 1rem;
            font-weight: 600;
            color: var(--secondary-color);
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .nav-link i {
            margin-right: 0.5rem;
            font-size: 1rem;
        }
        
        .nav-link:hover, .nav-link.active {
            color: var(--primary-color);
        }
        
        .nav-link.active {
            position: relative;
        }
        
        .nav-link.active:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 1rem;
            right: 1rem;
            height: 2px;
            background-color: var(--primary-color);
        }
        
        .navbar-toggler {
            border: none;
            padding: 0.5rem;
            font-size: 1.5rem;
            color: var(--dark-color);
        }
        
        .navbar-toggler:focus {
            outline: none;
            box-shadow: none;
        }
        
        .system-status {
            display: flex;
            align-items: center;
            margin-left: 1rem;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.75rem;
            border-radius: 50rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: white;
        }
        
        .status-icon {
            margin-right: 0.5rem;
            font-size: 0.6rem;
        }
        
        .status-icon.pulse {
            animation: pulse 1.5s infinite;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        
        .navbar-spacer {
            height: 70px;
        }
        
        /* Mobile styles */
        @media (max-width: 991.98px) {
            .navbar-collapse {
                padding: 1rem;
                background-color: var(--navbar-bg);
                border-radius: 0.5rem;
                margin-top: 1rem;
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            }
            
            .nav-link {
                padding: 0.75rem 0;
                border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            }
            
            .nav-link:last-child {
                border-bottom: none;
            }
            
            .nav-link.active:after {
                display: none;
            }
            
            .navbar-right {
                margin-top: 1rem;
                padding-top: 1rem;
                border-top: 1px solid rgba(0, 0, 0, 0.1);
            }
            
            .system-status {
                margin-left: 0;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar Container -->
    <div class="navbar-container">
        <nav class="navbar navbar-expand-lg tsunami-navbar">
            <div class="container-fluid px-3 px-md-4">
                <!-- Brand Logo -->
                <a class="navbar-brand" href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>">
                    <span class="brand-logo">
                        <i class="<?= htmlspecialchars($navConfig['brand']['logo'] ?? 'fas fa-water') ?>"></i>
                    </span>
                    <span class="brand-text"><?= htmlspecialchars($navConfig['brand']['text'] ?? 'Tsunami Alert') ?></span>
                </a>
                
                <!-- Mobile Toggle Button -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" 
                        aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fas fa-bars"></i>
                </button>
                
                <!-- Navigation Content -->
                <div class="collapse navbar-collapse" id="mainNavbar">
                    <div class="me-auto"></div>
                    
                    <!-- Navigation Links -->
                    <ul class="navbar-nav">
                        <?php foreach ($navConfig['links'] as $link): ?>
                            <li class="nav-item">
                                <a class="nav-link <?= $current_page === $link['page'] ? 'active' : '' ?>" 
                                   href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/<?= htmlspecialchars($link['page'], ENT_QUOTES, 'UTF-8') ?>">
                                    <i class="<?= htmlspecialchars($link['icon'], ENT_QUOTES, 'UTF-8') ?> fa-fw"></i> 
                                    <?= htmlspecialchars($link['text'], ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <!-- System Status -->
                    <div class="navbar-right d-flex">
                        <div class="system-status">
                            <span class="status-badge bg-<?= check_system_status() ? 'success' : 'danger' ?>">
                                <span class="status-icon <?= check_system_status() ? 'pulse' : '' ?>">
                                    <i class="fas fa-circle"></i>
                                </span>
                                <?= check_system_status() ? 'SYSTEM ACTIVE' : 'SYSTEM OFFLINE' ?>
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
    
    <!-- Enhanced Navbar Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Close navbar when clicking on nav links (mobile view)
            document.querySelectorAll('.nav-link').forEach(function(navLink) {
                navLink.addEventListener('click', function() {
                    if (window.innerWidth < 992) {
                        var navbar = document.getElementById('mainNavbar');
                        var bsCollapse = bootstrap.Collapse.getInstance(navbar) || new bootstrap.Collapse(navbar);
                        bsCollapse.hide();
                    }
                });
            });
            
            // Add animation class when scrolling
            window.addEventListener('scroll', function() {
                var navbar = document.querySelector('.navbar-container');
                if (window.scrollY > 10) {
                    navbar.style.boxShadow = '0 2px 15px rgba(0, 0, 0, 0.15)';
                } else {
                    navbar.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
                }
            });
        });
    </script>
</body>
</html>