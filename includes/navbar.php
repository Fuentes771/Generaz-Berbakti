<?php
/**
 * Navigation Bar Component
 * 
 * @version 1.2.0
 */

if (!function_exists('check_system_status')) {
    function check_system_status() {
        return true; // Ganti dengan logika sebenarnya
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>../assets/css/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar Container with Padding -->
    <div class="navbar-container">
        <nav class="navbar navbar-expand-lg navbar-dark tsunami-navbar">
            <div class="container">
                <!-- Brand Logo with Gradient Text -->
                <a class="navbar-brand" href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>">
                    <i class="fas fa-water me-2"></i>
                    <span class="brand-text">TsnamiAlert</span>
                </a>
                
                <!-- Mobile Toggle Button -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-expanded="false">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <!-- Navigation Links -->
                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) === 'index.php' ? 'active' : '' ?>" 
                               href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>">
                                <i class="fas fa-home me-1"></i> Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) === 'monitoring.php' ? 'active' : '' ?>" 
                               href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/monitoring.php">
                                <i class="fas fa-wave-square me-1"></i> Monitoring
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) === 'about.php' ? 'active' : '' ?>" 
                               href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/about.php">
                                <i class="fas fa-info-circle me-1"></i> About
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Right Side Elements -->
                    <div class="navbar-right">
                        <!-- System Status Badge with Pulse Animation -->
                        <div class="system-status me-3">
                            <span class="status-badge badge bg-<?= check_system_status() ? 'success' : 'danger' ?>">
                                <span class="status-icon <?= check_system_status() ? 'pulse' : '' ?>">
                                    <i class="fas fa-circle"></i>
                                </span>
                                <span class="status-text"><?= check_system_status() ? 'SYSTEM ACTIVE' : 'SYSTEM DOWN' ?></span>
                            </span>
                        </div>
                        
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <!-- User Dropdown with Avatar -->
                            <div class="dropdown user-dropdown">
                                <button class="btn dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                                    <div class="user-avatar">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                    <span class="username"><?= htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/admin"><i class="fas fa-cog me-2"></i>Dashboard</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <!-- Content Padding -->
    <div class="navbar-spacer"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>