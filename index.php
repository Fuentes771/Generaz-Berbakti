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
    <meta name="description" content="Sistem Deteksi Dini Tsunami untuk Pekon Teluk Kiluan Negri - Pemantauan dan deteksi real-time untuk peringatan tsunami">
    <meta name="keywords" content="tsunami, sistem peringatan dini, deteksi gempa, BMKG, keamanan pesisir">
    <title>Sistem Deteksi Dini Tsunami | RINOVA</title>
    
    <!-- Favicon -->
    <link rel="icon" href="assets/img/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/apple-touch-icon.png">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css?v=2">
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
                
                <div class="d-flex gap-3">
                    <a href="monitoring.php" class="hero-button">
                        <i class="fas fa-chart-line me-2"></i> Lihat Pemantauan
                    </a>
                    <a href="#features" class="hero-button" style="background: transparent; color: var(--color-white);">
                        <i class="fas fa-info-circle me-2"></i> Pelajari Lebih Lanjut
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image-container">
                    <img src="assets/img/desta.png" alt="Sistem Peringatan Tsunami" class="hero-image img-fluid" style="background: transparent;">
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
                    <h5>Visualisasi Data</h5>
                    <p>Grafik dan peta interaktif yang menunjukkan pola getaran, dan tekanan.</p>
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
                    <h5>Jaringan Multi-Sensor</h5>
                    <p>Penyebaran berbagai jenis sensor termasuk sensor getaran, tekanan, dan GPS untuk pengumpulan data yang komprehensif.</p>
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
                    'img' => 'assets/img/teluk1.png',
                    'title' => 'Alat Pemantauan Gempa',
                    'desc' => 'Alat pemantauan gempa dengan sensor getaran dan GPS untuk deteksi dini'
                ],
                [
                    'img' => 'assets/img/teluk2.png',
                    'title' => 'Alat Dibawah Air',
                    'desc' => 'Peralatan deteksi getaran tanah dibawah air dengan sensitivitas tinggi'
                ],
                [
                    'img' => 'assets/img/teluk1.png',
                    'title' => 'Unit Tenaga Surya',
                    'desc' => 'Sumber energi berkelanjutan untuk instalasi terpencil'
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
    <!-- Contoh path absolut untuk testing -->
    <img src="assets/img/cindy2.png" alt="Lumba-Lumba Kiri" class="floating-dolphin left">
    <img src="assets/img/cindy3.png" alt="Lumba-Lumba Kanan" class="floating-dolphin right">
</div>


<!-- Stats Section -->
<section class="stats-section bg-gradient-primary text-white py-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="display-4 fw-bold">24/7</div>
                <p class="mb-0">Pemantauan</p>
            </div>
            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="display-4 fw-bold">2</div>
                <p class="mb-0">Titik Deteksi Laut</p>
            </div>
             <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="display-4 fw-bold">2</div>
                <p class="mb-0">Titik Deteksi Darat</p>
            </div>
            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="display-4 fw-bold">2</div>
                <p class="mb-0">Solar Panel</p>
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
        const elements = document.querySelectorAll('.feature-card, .gallery-card');
        
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
        document.querySelectorAll('.feature-card, .gallery-card').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        });
        
        animateOnScroll();
    });

    window.addEventListener('scroll', animateOnScroll);
</script>
</body>
</html>