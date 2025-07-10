<?php
    // Configuration
    $esp_ip = "192.168.241.203";
    $db_config = [
        'host' => 'localhost',
        'user' => 'username',
        'pass' => 'password',
        'name' => 'tsunami_warning'
    ];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>PENDETEKSI DINI TSUNMI RINOVA</title>

<!-- CSS External -->
<link rel="stylesheet" href="style/styles.css" />

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" />

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />

<!-- Tambahan Font Unik -->
<link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Dancing+Script&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Satisfy&family=Clicker+Script&display=swap" rel="stylesheet">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">

<!-- Gauge JS -->
<script src="https://cdn.jsdelivr.net/npm/gaugeJS@1.3.7/dist/gauge.min.js"></script>
</head>
<body>

<!-- Include Navbar -->
<?php include 'php/navbar.php'; ?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container py-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="hero-title">
                    SISTEM DETEKSI DINI TSUNAMI <br>
                    <span class="hero-subfont">Pekon Teluk Kiluan Negri</span>
                </h1>
                <p class="hero-subtitle">Monitoring dan deteksi real-time untuk peringatan tsunami</p>
                <a href="monitoring.php" class="hero-button">Lihat Monitoring</a>
            </div>
            <div class="col-md-6 position-relative">
                <img src="img/desta.png" alt="Sistem Peringatan Tsunami" class="hero-image" />
            </div>
        </div>
    </div>
    <div class="wave"></div>
</section>

<img src="img/cindy3.png" class="coffee-cup right" alt="Deskripsi gambar">
<img src="img/cindy2.png" class="coffee-cup left" alt="Deskripsi gambar">

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <div class="section-header">
            <h2>FITUR          SISTEM</h2>
            <p>"Dengan memanfaatkan teknologi Internet of Things (IoT) dan pemantauan berbasis data real-time, 
                sistem ini terhubung langsung dengan sumber data resmi dari BMKG (Badan Meteorologi, Klimatologi, dan Geofisika),
                 sehingga mampu menyajikan informasi akurat secara langsung ke pusat pemantauan 
                 untuk merespons potensi ancaman tsunami dengan cepat, tepat, dan efisien."</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h5>Peringatan Real-time</h5>
                    <p>Notifikasi instan saat terdeteksi getaran berpotensi tsunami</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h5>Visualisasi Data</h5>
                    <p>Grafik interaktif menunjukkan pola getaran</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-database"></i>
                    </div>
                    <h5>Data Historis</h5>
                    <p>Akses ke data kejadian masa lalu untuk analisis</p>
                </div>
            </div>
        </div>
    </div>
</section>





<!-- Foto Alat Kami Section -->
<section class="gallery-section py-5" style="background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="mb-3 text-white">DOKUMENTASI ALAT KAMI</h2>
            <p class="lead text-white">“Setiap alat yang kami kembangkan bukan sekadar teknologi, 
                melainkan wujud nyata komitmen kami dalam melindungi masyarakat pesisir. 
                Dirancang dengan kecanggihan dan diuji langsung di lapangan, sistem ini terintegrasi dengan data BMKG dan mampu memberikan peringatan dini sebelum bahaya datang.”

</p>
        </div>
        
        <div class="row justify-content-center">
            <!-- Card 1 -->
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="gallery-card h-100">
                    <div class="gallery-img-container">
                        <img src="img/teluk1.png" alt="Alat Modern" class="gallery-card-img">
                    </div>
                    <div class="gallery-card-body">
                        <h5 class="gallery-card-title">Alat Modern</h5>
                        <p class="gallery-card-desc">Peralatan canggih untuk hasil terbaik</p>
                    </div>
                </div>
            </div>
            
            <!-- Card 2 -->
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="gallery-card h-100">
                    <div class="gallery-img-container">
                        <img src="img/teluk2.png" alt="Bahan Premium" class="gallery-card-img">
                    </div>
                    <div class="gallery-card-body">
                        <h5 class="gallery-card-title">Bahan Premium</h5>
                        <p class="gallery-card-desc">Hanya bahan terbaik yang kami gunakan</p>
                    </div>
                </div>
            </div>
            
            <!-- Card 3 -->
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="gallery-card h-100">
                    <div class="gallery-img-container">
                        <img src="img/teluk3.png" alt="Proses Higienis" class="gallery-card-img">
                    </div>
                    <div class="gallery-card-body">
                        <h5 class="gallery-card-title">Proses Higienis</h5>
                        <p class="gallery-card-desc">Standar kebersihan tinggi</p>
                    </div>
                </div>
            </div>
            
            <!-- Card 4 -->
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="gallery-card h-100">
                    <div class="gallery-img-container">
                        <img src="img/teluk4.png" alt="Ramah Lingkungan" class="gallery-card-img">
                    </div>
                    <div class="gallery-card-body">
                        <h5 class="gallery-card-title">Ramah Lingkungan</h5>
                        <p class="gallery-card-desc">Proses produksi berkelanjutan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Include Footer -->
<?php include 'php/footer.php'; ?>

<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


</body>
</html>