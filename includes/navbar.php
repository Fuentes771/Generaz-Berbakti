<?php
/**
 * Navigation Bar Component - Complete Version
 * 
 * @version 2.2.0
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
                
                <!-- Mobile Toggle Button -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                    <span class="navbar-toggler-icon"></span>
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
                        <!-- System Status - Far Right -->
                        <div class="system-status">
                            <span class="status-badge bg-<?= check_system_status() ? 'success' : 'danger' ?>">
                                <span class="status-icon <?= check_system_status() ? 'pulse' : '' ?>">
                                    <i class="fas fa-circle"></i>
                                </span>
                                SYSTEM ACTIVE
                            </span>
                        </div>
                        
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- User Dropdown -->
                        <div class="dropdown user-dropdown">
                            <button class="btn user-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <span class="user-avatar">
                                    <i class="fas fa-user-circle"></i>
                                </span>
                                <span class="username"><?= htmlspecialchars($_SESSION['username'] ?? 'User', ENT_QUOTES, 'UTF-8') ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>admin"><i class="fas fa-cog fa-fw me-2"></i>Dashboard</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user fa-fw me-2"></i>Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>logout.php"><i class="fas fa-sign-out-alt fa-fw me-2"></i>Logout</a></li>
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
</body>
</html>