<?php
/**
 * Footer Fragment (no HTML/HEAD/BODY wrappers)
 */

$appVersion = defined('APP_VERSION') ? APP_VERSION : '1.0.0';
?>
    <footer class="tsunami-footer">
        <div class="container">
            <div class="row g-4">
                <!-- About Section -->
                <div class="col-lg-4 col-md-6">
                    <h5 class="footer-heading">Sistem Pemantauan Gempa Bumi</h5>
                    <p class="footer-about-text">Sistem pendeteksi dini tsunami berbasis IoT untuk memberikan peringatan lebih awal kepada masyarakat.</p>
                </div>
                
                <!-- Quick Links -->
                <div class="col-lg-3 col-md-6">
                    <h5 class="footer-heading">Tautan Cepat</h5>
                    <ul class="footer-links">
                        <li><a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>" class="footer-link">Beranda</a></li>
                        <li><a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/monitoring.php" class="footer-link">Pemantauan</a></li>
                        <li><a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/bmkg.php" class="footer-link">Data BMKG</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div class="col-lg-3 col-md-6">
                    <h5 class="footer-heading">Kontak</h5>
                    <ul class="footer-contact-info">
                        <li>
                            <i class="fas fa-map-marker-alt contact-icon"></i>
                            <span>Universitas Lampung, Bandar Lampung</span>
                        </li>
                        <li>
                            <i class="fas fa-phone contact-icon"></i>
                            <span>+62 812 3456 7890</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-divider"></div>
            
            <div class="footer-bottom">
                <div class="row">
                    <div class="col-12 text-center">
                        <p class="copyright-text mb-2">&copy; <?= date('Y') ?> Rinova Generasi Berbakti | Sistem Pemantauan Gempa Bumi</p>
                        <?php if (basename($_SERVER['SCRIPT_NAME']) === 'bmkg.php'): ?>
                        <p class="version-text">Data diperoleh dari Badan Meteorologi, Klimatologi, dan Geofisika (BMKG)</p>
                        <?php else: ?>
                        <p class="version-text">Versi: <?= $appVersion ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Page should already include Bootstrap JS if needed -->