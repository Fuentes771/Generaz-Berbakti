<?php
if (!function_exists('check_system_status')) {
    function check_system_status() {
        return true;
    }
}
?>
    <div class="navbar-container">
        <nav class="navbar navbar-expand-lg tsunami-navbar">
            <div class="container-fluid px-4">
                <a class="navbar-brand" href="<?= BASE_URL ?>">
                    <span class="brand-logo"><i class="fas fa-water"></i></span>
                    <span class="brand-text">Peringatan Tsunami</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false">
                    <span class="navbar-toggler-icon-custom"></span>
                </button>
                <div class="collapse navbar-collapse" id="mainNavbar">
                    <div class="me-auto"></div>
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) === 'index.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>">
                                <i class="fas fa-home fa-fw"></i> Beranda
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) === 'monitoring.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/monitoring.php">
                                <i class="fas fa-wave-square fa-fw"></i> Pemantauan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) === 'bmkg.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/bmkg.php">
                                <i class="fas fa-info-circle fa-fw"></i> Data BMKG
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) === 'gempa-history.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/gempa-history.php">
                                <i class="fas fa-history fa-fw"></i> History Gempa
                            </a>
                        </li>
                    </ul>
                    <div class="navbar-right">
                        <div class="system-status">
                            <span class="status-badge bg-<?= check_system_status() ? 'success' : 'danger' ?>">
                                <span class="status-icon <?= check_system_status() ? 'pulse' : '' ?>">
                                    <i class="fas fa-circle"></i>
                                </span>
                                SISTEM <?= check_system_status() ? 'AKTIF' : 'OFFLINE' ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </div>
    <div class="navbar-spacer"></div>
