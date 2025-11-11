<?php
// Include configuration and header
include 'includes/config.php';
include 'includes/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Peringatan Dini Tsunami berbasis IoT untuk Pekon Teluk Kiluan Negri - Pemantauan dan deteksi real-time terintegrasi dengan BMKG">
    <meta name="keywords" content="tsunami, sistem peringatan dini, deteksi gempa, BMKG, IoT, keamanan pesisir, Lampung, early warning system">
    <meta name="author" content="Rinova Generasi Berbakti">
    <meta name="theme-color" content="#1e40af">
    <title>Sistem Peringatan Dini Tsunami | Rinova Generasi Berbakti</title>
    
    <!-- Favicon -->
    <link rel="icon" href="assets/img/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/apple-touch-icon.png">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/core.css">
    <link rel="stylesheet" href="assets/css/styles.css?v=3">
</head>
<body>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 hero-content">
                <h1 class="hero-title">
                    <span class="text-gradient">EARLY TSUNAMI</span> <br>
                    DETECTION SYSTEM <br>
                    <span class="text-decorative">Pekon Teluk Kiluan Negri</span>
                </h1>
                
                <div class="d-flex gap-3 flex-wrap">
                    <a href="monitoring.php" class="hero-button">
                        <i class="fas fa-chart-line me-2"></i> Lihat Pemantauan
                    </a>
                    <a href="bmkg.php" class="hero-button" style="background: rgba(255,255,255,0.15); color: var(--color-white); border: 2px solid white;">
                        <i class="fas fa-satellite-dish me-2"></i> Data BMKG
                    </a>
                    <a href="gempa-history.php" class="hero-button" style="background: transparent; color: var(--color-white); border: 2px solid white;">
                        <i class="fas fa-history me-2"></i> History Gempa
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image-container">
                    <img src="assets/img/hero/tsunami-detection-system.png" alt="Sistem Peringatan Tsunami" class="hero-image img-fluid" style="background: transparent;">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="features-section">
    <div class="container">
        <div class="section-header">
            <h2>FITUR SISTEM</h2>
            <p>Menggunakan teknologi IoT dan pemantauan data real-time, sistem ini terhubung langsung dengan sumber data resmi dari BMKG untuk menyediakan informasi akurat guna respons cepat dan efisien terhadap ancaman tsunami potensial.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h5>Monitoring Real-time</h5>
                    <p>Grafik dan visualisasi data interaktif yang menunjukkan pola getaran dan tekanan secara real-time dengan update otomatis.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-database"></i>
                    </div>
                    <h5>Data Historis</h5>
                    <p>Arsip komprehensif data kejadian masa lalu untuk analisis, penelitian, dan peningkatan sistem dengan alat pelaporan yang dapat disesuaikan.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-satellite-dish"></i>
                    </div>
                    <h5>Integrasi BMKG</h5>
                    <p>Terhubung langsung dengan data resmi BMKG untuk validasi dan cross-check informasi gempa terkini di seluruh Indonesia.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Gallery Section -->
<section class="gallery-section">
    <div class="container">
        <div class="section-header">
            <h2>DOKUMENTASI SISTEM KAMI</h2>
            <p class="lead">Setiap perangkat yang kami kembangkan bukan hanya teknologi, tetapi perwujudan nyata dari komitmen kami untuk melindungi komunitas pesisir. Jelajahi komponen sistem dan proses instalasi kami.</p>
        </div>
        
        <div class="row justify-content-center g-4">
            <?php
            $galleryItems = [
                [
                    'img' => 'assets/img/gallery/monitoring-equipment.jpg',
                    'title' => 'Alat Pemantauan Gempa',
                    'desc' => 'Sistem pemantauan gempa dengan sensor getaran dan GPS untuk deteksi dini aktivitas seismik'
                ],
                [
                    'img' => 'assets/img/gallery/underwater-sensor.jpg',
                    'title' => 'Sensor Bawah Air',
                    'desc' => 'Peralatan deteksi getaran dan tekanan air dengan sensitivitas tinggi untuk monitoring tsunami'
                ],
                [
                    'img' => 'assets/img/gallery/solar-power-unit.jpg',
                    'title' => 'Unit Tenaga Surya',
                    'desc' => 'Sumber energi berkelanjutan untuk menjaga sistem tetap beroperasi 24/7 di lokasi terpencil'
                ]
            ];
            
            foreach ($galleryItems as $item) {
                echo '
                <div class="col-lg-4 col-md-6">
                    <div class="gallery-card h-100">
                        <div class="gallery-img-container">
                            <img src="'.$item['img'].'" alt="'.$item['title'].'" class="gallery-card-img">
                        </div>
                        <div class="gallery-card-body">
                            <h5 class="gallery-card-title">'.$item['title'].'</h5>
                            <p class="gallery-card-desc">'.$item['desc'].'</p>
                            <a href="'.$item['img'].'" class="btn btn-sm btn-outline-primary mt-2" data-fslightbox="gallery">Lihat Lebih Besar</a>
                        </div>
                    </div>
                </div>';
            }
            ?>
        </div>
    </div>
</section>

<div class="dolphin-container">
    <img src="assets/img/decorative/dolphin-left.png" alt="Lumba-Lumba Kiri" class="floating-dolphin left">
    <img src="assets/img/decorative/dolphin-right.png" alt="Lumba-Lumba Kanan" class="floating-dolphin right">
</div>


<!-- Stats Section -->
<section class="stats-section bg-gradient-primary text-white py-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="stat-box">
                    <i class="fas fa-clock fa-2x mb-3"></i>
                    <div class="display-4 fw-bold">24/7</div>
                    <p class="mb-0">Pemantauan Aktif</p>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="stat-box">
                    <i class="fas fa-water fa-2x mb-3"></i>
                    <div class="display-4 fw-bold">2</div>
                    <p class="mb-0">Sensor Bawah Laut</p>
                </div>
            </div>
             <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="stat-box">
                    <i class="fas fa-mountain fa-2x mb-3"></i>
                    <div class="display-4 fw-bold">2</div>
                    <p class="mb-0">Sensor Darat</p>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="stat-box">
                    <i class="fas fa-solar-panel fa-2x mb-3"></i>
                    <div class="display-4 fw-bold">2</div>
                    <p class="mb-0">Unit Solar Panel</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section text-white text-center py-5" style="margin-top: 80px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <i class="fas fa-shield-alt fa-4x mb-4" style="opacity: 0.9;"></i>
                <h2 class="display-5 fw-bold mb-3">Pantau Aktivitas Seismik Secara Real-time</h2>
                <p class="lead mb-4">
                    Akses dashboard pemantauan untuk melihat data sensor langsung, grafik aktivitas seismik, 
                    dan informasi gempa terkini dari BMKG
                </p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="monitoring.php" class="btn btn-light btn-lg px-4 py-3 rounded-pill">
                        <i class="fas fa-desktop me-2"></i>
                        Buka Dashboard
                    </a>
                    <a href="bmkg.php" class="btn btn-outline-light btn-lg px-4 py-3 rounded-pill">
                        <i class="fas fa-database me-2"></i>
                        Data Gempa BMKG
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fslightbox/3.3.1/index.min.js"></script>
<script>
    // Animation for elements when they come into view
    const animateOnScroll = () => {
        const elements = document.querySelectorAll('.feature-card, .gallery-card, .stat-box');
        
        elements.forEach(element => {
            const elementPosition = element.getBoundingClientRect().top;
            const screenPosition = window.innerHeight / 1.2;
            
            if(elementPosition < screenPosition) {
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }
        });
    };

    // Initialize animations
    window.addEventListener('load', () => {
        document.querySelectorAll('.feature-card, .gallery-card, .stat-box').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        });
        
        animateOnScroll();
        
        // Animate hero content
        const heroContent = document.querySelector('.hero-content');
        if (heroContent) {
            heroContent.style.animation = 'fadeInLeft 1s ease-out';
        }
        
        // Animate hero image
        const heroImage = document.querySelector('.hero-image-container');
        if (heroImage) {
            heroImage.style.animation = 'fadeInRight 1s ease-out';
        }
    });

    window.addEventListener('scroll', animateOnScroll);
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Add subtle parallax effect to hero section
    let ticking = false;
    window.addEventListener('scroll', () => {
        if (!ticking) {
            window.requestAnimationFrame(() => {
                const scrolled = window.pageYOffset;
                const heroSection = document.querySelector('.hero-section');
                if (heroSection && scrolled < window.innerHeight) {
                    heroSection.style.backgroundPositionY = scrolled * 0.5 + 'px';
                }
                ticking = false;
            });
            ticking = true;
        }
    });
    
    // Animate numbers in stats section
    const animateNumbers = () => {
        const statBoxes = document.querySelectorAll('.stat-box .display-4');
        statBoxes.forEach(stat => {
            const text = stat.textContent;
            // Only animate if it's a number
            if (!isNaN(text) && text !== '24/7') {
                const target = parseInt(text);
                let current = 0;
                const increment = target / 30;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        stat.textContent = target;
                        clearInterval(timer);
                    } else {
                        stat.textContent = Math.floor(current);
                    }
                }, 50);
            }
        });
    };
    
    // Trigger number animation when stats section is visible
    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateNumbers();
                statsObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });
    
    const statsSection = document.querySelector('.stats-section');
    if (statsSection) {
        statsObserver.observe(statsSection);
    }
    
    // Console branding
    console.log('%c🌊 Tsunami Early Warning System', 'background: #1e40af; color: white; font-size: 16px; font-weight: bold; padding: 10px;');
    console.log('%cSistem Peringatan Dini Tsunami - Rinova Generasi Berbakti', 'color: #0ea5e9; font-size: 12px;');
    console.log('%c⚡ Melindungi Pekon Teluk Kiluan Negri', 'color: #10b981; font-size: 12px; font-weight: bold;');
</script>
</body>
</html>