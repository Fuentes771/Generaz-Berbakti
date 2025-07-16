<?php
/**
 * Footer Component
 * 
 * @version 1.1.0
 */
?>

<?php
// At the top of your footer.php or before the footer is included
$appVersion = defined('APP_VERSION') ? APP_VERSION : '1.0.0';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <footer class="tsunami-footer">
        <div class="container">
            <div class="row g-4">
                <!-- About Section -->
                <div class="col-lg-4 col-md-6">
                    <h5 class="footer-heading">Tsunami Warning System</h5>
                    <p class="footer-about-text">Sistem pendeteksi dini tsunami berbasis IoT untuk memberikan peringatan lebih awal dan menyelamatkan lebih banyak nyawa.</p>
                    <div class="social-links">
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6">
                    <h5 class="footer-heading">Quick Links</h5>
                    <ul class="footer-links">
                        <li><a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>" class="footer-link">Home</a></li>
                        <li><a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/monitoring.php" class="footer-link">Monitoring</a></li>
                        <li><a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/about.php" class="footer-link">About Us</a></li>
                        <li><a href="#" class="footer-link">Documentation</a></li>
                        <li><a href="#" class="footer-link">API Docs</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div class="col-lg-3 col-md-6">
                    <h5 class="footer-heading">Contact Us</h5>
                    <ul class="footer-contact-info">
                        <li>
                            <i class="fas fa-map-marker-alt contact-icon"></i>
                            <span>Universitas Lampung, Bandar Lampung</span>
                        </li>
                        <li>
                            <i class="fas fa-phone contact-icon"></i>
                            <span>+62 812 3456 7890</span>
                        </li>
                        <li>
                            <i class="fas fa-envelope contact-icon"></i>
                            <span>info@tsunami-warning.com</span>
                        </li>
                    </ul>
                </div>
                
                <!-- Newsletter -->
                <div class="col-lg-3 col-md-6">
                    <h5 class="footer-heading">Newsletter</h5>
                    <p class="footer-newsletter-text">Subscribe untuk mendapatkan update terbaru</p>
                    <form class="newsletter-form">
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Your Email" required>
                            <button class="btn btn-primary" type="submit">Subscribe</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="footer-divider"></div>
            
            <div class="footer-bottom">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start">
                        <p class="copyright-text">&copy; <?= date('Y') ?> Tsunami Warning System. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <p class="version-text">Version: <?= $appVersion ?></div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>