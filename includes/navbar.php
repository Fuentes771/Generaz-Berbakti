<?php
/**
 * Premium Navigation Bar Component 
 * 
 * @version 3.5.0
 * @feature Ultra-enhanced mobile experience with 3D animations and premium styling
 */

if (!function_exists('check_system_status')) {
    function check_system_status() {
        return true; // Replace with actual system status logic
    }
}

$navConfig = defined('NAVBAR_CONFIG') ? NAVBAR_CONFIG : [
    'brand_name' => 'Tsunami Alert',
    'brand_icon' => 'fa-water',
    'menu_items' => [
        ['title' => 'Home', 'url' => 'index.php', 'icon' => 'fa-home'],
        ['title' => 'Monitoring', 'url' => 'monitoring.php', 'icon' => 'fa-wave-square'],
        ['title' => 'Peringatan BMKG', 'url' => 'bmkg.php', 'icon' => 'fa-info-circle']
    ]
];

$current_page = basename($_SERVER['SCRIPT_NAME']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar Container with 3D Perspective -->
    <div class="navbar-container" id="navbarContainer">
        <!-- Animated Background Layer -->
        <div class="nav-background-layer"></div>
        
        <!-- Overlay for mobile menu with gradient effect -->
        <div class="nav-overlay" id="navOverlay"></div>
        
        <!-- Main Navbar -->
        <nav class="navbar navbar-expand-lg tsunami-navbar">
            <div class="container-fluid px-4">
                <!-- Brand Logo with Animation -->
                <a class="navbar-brand" href="<?= htmlspecialchars(BASE_URL ?? '/', ENT_QUOTES, 'UTF-8') ?>">
                    <span class="brand-logo">
                        <i class="fas <?= $navConfig['brand_icon'] ?>"></i>
                    </span>
                    <span class="brand-text"><?= $navConfig['brand_name'] ?></span>
                    <span class="brand-underline"></span>
                </a>
                
                <!-- Premium Animated Toggle Button -->
                <button class="navbar-toggler hamburger hamburger--spin" type="button" 
                        id="navbarToggler" aria-controls="mainNavbar" 
                        aria-expanded="false" aria-label="Toggle navigation">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </button>
                
                <!-- Navigation Content -->
                <div class="collapse navbar-collapse" id="mainNavbar">
                    <!-- Empty space to push items to right -->
                    <div class="me-auto"></div>
                    
                    <!-- Right-aligned Navigation Links with Ripple Effect -->
                    <ul class="navbar-nav">
                        <?php foreach ($navConfig['menu_items'] as $item): ?>
                        <li class="nav-item" data-title="<?= $item['title'] ?>">
                            <a class="nav-link <?= $current_page === $item['url'] ? 'active' : '' ?>" 
                               href="<?= htmlspecialchars(($BASE_URL ?? '/') . $item['url'], ENT_QUOTES, 'UTF-8') ?>">
                                <i class="fas <?= $item['icon'] ?> fa-fw"></i>
                                <span class="nav-link-text"><?= $item['title'] ?></span>
                                <span class="nav-link-hover"></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <!-- Right-aligned System Status with Animation -->
                    <div class="navbar-right">
                        <div class="system-status" id="systemStatus">
                            <span class="status-badge bg-<?= check_system_status() ? 'success' : 'danger' ?>">
                                <span class="status-icon <?= check_system_status() ? 'pulse' : '' ?>">
                                    <i class="fas fa-circle"></i>
                                </span>
                                <span class="status-text">SYSTEM <?= check_system_status() ? 'ACTIVE' : 'OFFLINE' ?></span>
                                <span class="status-glow"></span>
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
    
    <!-- Premium Navbar Script with 3D Effects -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navbarToggler = document.getElementById('navbarToggler');
            const navbarCollapse = document.getElementById('mainNavbar');
            const navOverlay = document.getElementById('navOverlay');
            const navLinks = document.querySelectorAll('.nav-link');
            const navbarContainer = document.getElementById('navbarContainer');
            const systemStatus = document.getElementById('systemStatus');
            
            // Initialize Bootstrap collapse with custom settings
            const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                toggle: false
            });
            
            // Enhanced toggle functionality with animations
            navbarToggler.addEventListener('click', function() {
                this.classList.toggle('is-active');
                const isExpanded = this.getAttribute('aria-expanded') === 'true';
                this.setAttribute('aria-expanded', !isExpanded);
                
                if (!isExpanded) {
                    // Show menu with 3D effect
                    navbarContainer.classList.add('menu-open');
                    bsCollapse.show();
                    navOverlay.classList.add('show');
                    document.body.style.overflow = 'hidden';
                    
                    // Animate background layer
                    gsap.fromTo('.nav-background-layer', 
                        { scaleY: 0, opacity: 0 },
                        { scaleY: 1, opacity: 1, duration: 0.8, ease: 'power3.out' }
                    );
                } else {
                    // Hide menu with reverse animation
                    navbarContainer.classList.remove('menu-open');
                    bsCollapse.hide();
                    navOverlay.classList.remove('show');
                    document.body.style.overflow = '';
                    
                    // Animate background layer out
                    gsap.to('.nav-background-layer', 
                        { scaleY: 0, opacity: 0, duration: 0.6, ease: 'power3.in' }
                    );
                }
            });
            
            // Close navbar when clicking on overlay
            navOverlay.addEventListener('click', function() {
                navbarToggler.classList.remove('is-active');
                navbarToggler.setAttribute('aria-expanded', 'false');
                navbarContainer.classList.remove('menu-open');
                bsCollapse.hide();
                this.classList.remove('show');
                document.body.style.overflow = '';
                
                // Animate background layer out
                gsap.to('.nav-background-layer', 
                    { scaleY: 0, opacity: 0, duration: 0.6, ease: 'power3.in' }
                );
            });
            
            // Enhanced nav link interactions
            navLinks.forEach(function(navLink) {
                // Click handler
                navLink.addEventListener('click', function(e) {
                    if (window.innerWidth < 992) {
                        navbarToggler.classList.remove('is-active');
                        navbarToggler.setAttribute('aria-expanded', 'false');
                        navbarContainer.classList.remove('menu-open');
                        bsCollapse.hide();
                        navOverlay.classList.remove('show');
                        document.body.style.overflow = '';
                        
                        // Animate background layer out
                        gsap.to('.nav-background-layer', 
                            { scaleY: 0, opacity: 0, duration: 0.6, ease: 'power3.in' }
                        );
                    }
                    
                    // Ripple effect
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
                
                // Hover effect for desktop
                if (window.innerWidth >= 992) {
                    navLink.addEventListener('mouseenter', function() {
                        const parentItem = this.parentElement;
                        const title = parentItem.getAttribute('data-title');
                        
                        // Create floating title effect
                        const floatTitle = document.createElement('div');
                        floatTitle.className = 'float-title';
                        floatTitle.textContent = title;
                        
                        const rect = this.getBoundingClientRect();
                        floatTitle.style.left = `${rect.left + rect.width/2}px`;
                        floatTitle.style.top = `${rect.top - 20}px`;
                        
                        document.body.appendChild(floatTitle);
                        
                        gsap.fromTo(floatTitle, 
                            { opacity: 0, y: 10 },
                            { opacity: 1, y: 0, duration: 0.3 }
                        );
                        
                        parentItem.floatTitle = floatTitle;
                    });
                    
                    navLink.addEventListener('mouseleave', function() {
                        const parentItem = this.parentElement;
                        if (parentItem.floatTitle) {
                            gsap.to(parentItem.floatTitle, 
                                { opacity: 0, y: -10, duration: 0.2, 
                                  onComplete: () => parentItem.floatTitle.remove() }
                            );
                        }
                    });
                }
            });
            
            // System status animation
            if (systemStatus) {
                setInterval(() => {
                    if (systemStatus.querySelector('.pulse')) {
                        gsap.to(systemStatus.querySelector('.status-glow'), 
                            { scale: 1.5, opacity: 0.6, duration: 1.5, 
                              repeat: -1, yoyo: true, ease: 'sine.inOut' }
                        );
                    }
                }, 100);
            }
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 992) {
                    // Ensure navbar is always visible on desktop
                    if (navbarCollapse.classList.contains('show')) {
                        navbarToggler.classList.remove('is-active');
                        navbarToggler.setAttribute('aria-expanded', 'false');
                        navbarContainer.classList.remove('menu-open');
                        bsCollapse.hide();
                        navOverlay.classList.remove('show');
                        document.body.style.overflow = '';
                        
                        // Animate background layer out
                        gsap.to('.nav-background-layer', 
                            { scaleY: 0, opacity: 0, duration: 0.3 }
                        );
                    }
                }
            });
            
            // Parallax effect on scroll
            window.addEventListener('scroll', function() {
                if (window.innerWidth >= 992) {
                    const scrollY = window.scrollY;
                    navbarContainer.style.transform = `translateZ(${scrollY * 0.1}px)`;
                }
            });
        });
    </script>
</body>
</html>