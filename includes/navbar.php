<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultimate Mobile Navbar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ========== VARIABLES & BASE ========== */
        :root {
            /* Color Scheme */
            --nav-primary: #0f172a;
            --nav-primary-dark: #0a1120;
            --nav-accent: #3b82f6;
            --nav-accent-light: #60a5fa;
            --nav-text: rgba(255, 255, 255, 0.98);
            --nav-text-secondary: rgba(255, 255, 255, 0.7);
            --nav-hover: rgba(255, 255, 255, 0.12);
            --nav-active: rgba(59, 130, 246, 0.2);
            --nav-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
            --nav-success: #10b981;
            --nav-danger: #ef4444;
            
            /* Sizing */
            --nav-height: 80px;
            --mobile-nav-height: 70px;
            --nav-brand-size: 2rem;
            --nav-icon-size: 1.2rem;
            
            /* Animations */
            --transition-speed: 0.4s;
            --transition-curve: cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }
        
        /* ========== NAVBAR CONTAINER ========== */
        .navbar-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--mobile-nav-height);
            background: var(--nav-primary);
            box-shadow: var(--nav-shadow);
            z-index: 1000;
            display: flex;
            justify-content: center;
        }
        
        .navbar-spacer {
            height: var(--mobile-nav-height);
            width: 100%;
        }
        
        .tsunami-navbar {
            width: 100%;
            max-width: 1400px;
            height: 100%;
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
        }
        
        /* ========== BRANDING ========== */
        .navbar-brand {
            display: flex;
            align-items: center;
            text-decoration: none;
            z-index: 1100;
            transition: transform 0.3s ease;
        }
        
        .navbar-brand:active {
            transform: scale(0.95);
        }
        
        .brand-logo {
            font-size: var(--nav-brand-size);
            margin-right: 0.8rem;
            color: var(--nav-accent);
            filter: drop-shadow(0 2px 5px rgba(59, 130, 246, 0.3));
        }
        
        .brand-text {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--nav-text);
            background: linear-gradient(to right, #ffffff, #e0f2fe);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: 0.5px;
        }
        
        /* ========== MOBILE MENU TOGGLE ========== */
        .menu-toggle {
            width: 40px;
            height: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
            z-index: 1100;
            position: relative;
        }
        
        .menu-toggle span {
            display: block;
            height: 2px;
            width: 24px;
            background-color: white;
            border-radius: 2px;
            transition: all var(--transition-speed) var(--transition-curve);
            position: absolute;
        }
        
        .menu-toggle span:nth-child(1) {
            transform: translateY(-8px);
        }
        
        .menu-toggle span:nth-child(3) {
            transform: translateY(8px);
        }
        
        .menu-toggle.active span:nth-child(1) {
            transform: translateY(0) rotate(45deg);
        }
        
        .menu-toggle.active span:nth-child(2) {
            opacity: 0;
            transform: scale(0);
        }
        
        .menu-toggle.active span:nth-child(3) {
            transform: translateY(0) rotate(-45deg);
        }
        
        /* ========== MOBILE MENU OVERLAY ========== */
        .mobile-menu-overlay {
            position: fixed;
            top: var(--mobile-nav-height);
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 900;
            opacity: 0;
            visibility: hidden;
            transition: all var(--transition-speed) var(--transition-curve);
        }
        
        .mobile-menu-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        /* ========== NAVIGATION CONTENT ========== */
        .navbar-content {
            position: fixed;
            top: var(--mobile-nav-height);
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--nav-primary-dark);
            z-index: 1000;
            transform: translateY(-100%);
            opacity: 0;
            transition: all var(--transition-speed) var(--transition-curve);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .navbar-content.active {
            transform: translateY(0);
            opacity: 1;
        }
        
        .navbar-nav {
            flex: 1;
            list-style: none;
            padding: 2rem 1.5rem;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--nav-accent) transparent;
        }
        
        .navbar-nav::-webkit-scrollbar {
            width: 4px;
        }
        
        .navbar-nav::-webkit-scrollbar-thumb {
            background-color: var(--nav-accent);
            border-radius: 2px;
        }
        
        .nav-item {
            margin-bottom: 1rem;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.5s ease;
        }
        
        .navbar-content.active .nav-item {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Staggered animations */
        .navbar-content.active .nav-item:nth-child(1) { transition-delay: 0.1s; }
        .navbar-content.active .nav-item:nth-child(2) { transition-delay: 0.15s; }
        .navbar-content.active .nav-item:nth-child(3) { transition-delay: 0.2s; }
        .navbar-content.active .nav-item:nth-child(4) { transition-delay: 0.25s; }
        .navbar-content.active .nav-item:nth-child(5) { transition-delay: 0.3s; }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 1.25rem 1.5rem;
            color: var(--nav-text);
            text-decoration: none;
            font-weight: 500;
            font-size: 1.1rem;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--nav-accent);
            transform: scaleY(0);
            transform-origin: bottom;
            transition: transform 0.3s ease;
        }
        
        .nav-link:hover::before,
        .nav-link.active::before {
            transform: scaleY(1);
        }
        
        .nav-link i {
            font-size: var(--nav-icon-size);
            margin-right: 1.25rem;
            width: 1.5rem;
            text-align: center;
            color: var(--nav-accent);
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            background: var(--nav-hover);
            transform: translateX(8px);
        }
        
        .nav-link:hover i {
            transform: scale(1.2);
        }
        
        .nav-link.active {
            background: var(--nav-active);
            font-weight: 600;
        }
        
        /* ========== SYSTEM STATUS ========== */
        .navbar-footer {
            padding: 1.5rem;
            background: rgba(0, 0, 0, 0.2);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .system-status {
            margin-bottom: 1.5rem;
        }
        
        .status-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: white;
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            transition: all 0.3s ease;
        }
        
        .status-badge.offline {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        
        .status-icon {
            position: relative;
            margin-right: 0.75rem;
            width: 10px;
            height: 10px;
            background: var(--nav-success);
            border-radius: 50%;
        }
        
        .status-icon.offline {
            background: var(--nav-danger);
        }
        
        .status-icon.pulse::after {
            content: '';
            position: absolute;
            top: -5px;
            left: -5px;
            right: -5px;
            bottom: -5px;
            background: var(--nav-success);
            border-radius: 50%;
            animation: pulse 2s infinite;
            opacity: 0;
        }
        
        @keyframes pulse {
            0% { transform: scale(0.8); opacity: 0.7; }
            70% { transform: scale(1.3); opacity: 0; }
            100% { transform: scale(0.8); opacity: 0; }
        }
        
        /* ========== USER PROFILE ========== */
        .user-profile {
            display: flex;
            align-items: center;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 0.75rem;
            transition: all 0.3s ease;
        }
        
        .user-profile:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--nav-accent);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: white;
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .user-info {
            flex: 1;
        }
        
        .user-name {
            font-weight: 600;
            color: white;
            margin-bottom: 0.2rem;
        }
        
        .user-email {
            font-size: 0.8rem;
            color: var(--nav-text-secondary);
        }
        
        /* ========== MEDIA QUERIES ========== */
        @media (min-width: 992px) {
            .navbar-container {
                height: var(--nav-height);
            }
            
            .navbar-spacer {
                height: var(--nav-height);
            }
            
            .menu-toggle {
                display: none;
            }
            
            .mobile-menu-overlay {
                display: none;
            }
            
            .navbar-content {
                position: static;
                background: transparent;
                transform: none;
                opacity: 1;
                flex-direction: row;
                align-items: center;
                justify-content: flex-end;
                overflow: visible;
            }
            
            .navbar-nav {
                display: flex;
                padding: 0;
                overflow: visible;
                flex: 0;
            }
            
            .nav-item {
                margin: 0 0.5rem;
                opacity: 1;
                transform: none;
            }
            
            .nav-link {
                padding: 0.75rem 1.25rem;
                font-size: 0.95rem;
            }
            
            .nav-link:hover {
                transform: translateY(-3px);
            }
            
            .nav-link::before {
                width: 100%;
                height: 3px;
                top: auto;
                bottom: 0;
                left: 0;
                transform: scaleX(0);
                transform-origin: right;
            }
            
            .nav-link:hover::before,
            .nav-link.active::before {
                transform: scaleX(1);
                transform-origin: left;
            }
            
            .navbar-footer {
                display: flex;
                align-items: center;
                padding: 0;
                background: transparent;
                border: none;
                margin-left: 1rem;
            }
            
            .system-status {
                margin: 0 1rem 0 0;
            }
            
            .status-badge {
                padding: 0.5rem 1rem;
            }
            
            .user-profile {
                padding: 0.5rem 0.75rem;
            }
            
            .user-avatar {
                width: 36px;
                height: 36px;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar Container -->
    <div class="navbar-container">
        <!-- Mobile Menu Overlay -->
        <div class="mobile-menu-overlay"></div>
        
        <nav class="tsunami-navbar">
            <!-- Brand Logo -->
            <a class="navbar-brand" href="#">
                <span class="brand-logo">
                    <i class="fas fa-water"></i>
                </span>
                <span class="brand-text">TsunamiAlert</span>
            </a>
            
            <!-- Mobile Menu Toggle -->
            <button class="menu-toggle" aria-label="Toggle navigation">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <!-- Navigation Content -->
            <div class="navbar-content">
                <!-- Navigation Links -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">
                            <i class="fas fa-home"></i>
                            <span>Home</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-wave-square"></i>
                            <span>Monitoring</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-info-circle"></i>
                            <span>BMKG Alerts</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-map-marked-alt"></i>
                            <span>Risk Areas</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-history"></i>
                            <span>History</span>
                        </a>
                    </li>
                </ul>
                
                <!-- Footer Section -->
                <div class="navbar-footer">
                    <div class="system-status">
                        <div class="status-badge">
                            <span class="status-icon pulse"></span>
                            <span>SYSTEM ACTIVE</span>
                        </div>
                    </div>
                    
                    <div class="user-profile">
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="user-info">
                            <div class="user-name">Admin User</div>
                            <div class="user-email">admin@tsunamialert.id</div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <!-- Content Spacer -->
    <div class="navbar-spacer"></div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.querySelector('.menu-toggle');
            const mobileOverlay = document.querySelector('.mobile-menu-overlay');
            const navbarContent = document.querySelector('.navbar-content');
            const navLinks = document.querySelectorAll('.nav-link');
            
            // Toggle mobile menu
            menuToggle.addEventListener('click', function() {
                this.classList.toggle('active');
                mobileOverlay.classList.toggle('active');
                navbarContent.classList.toggle('active');
                document.body.style.overflow = navbarContent.classList.contains('active') ? 'hidden' : '';
            });
            
            // Close menu when clicking on overlay
            mobileOverlay.addEventListener('click', function() {
                menuToggle.classList.remove('active');
                this.classList.remove('active');
                navbarContent.classList.remove('active');
                document.body.style.overflow = '';
            });
            
            // Close menu when clicking on nav links (for anchor links)
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (this.getAttribute('href') === '#') {
                        e.preventDefault();
                    }
                    
                    if (window.innerWidth < 992) {
                        menuToggle.classList.remove('active');
                        mobileOverlay.classList.remove('active');
                        navbarContent.classList.remove('active');
                        document.body.style.overflow = '';
                    }
                });
            });
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 992) {
                    menuToggle.classList.remove('active');
                    mobileOverlay.classList.remove('active');
                    navbarContent.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
            
            // Add ripple effect to nav links
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (window.innerWidth < 992) {
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
        });
    </script>
</body>
</html>