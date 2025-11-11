<?php
include 'includes/config.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gempa Monitor | Sistem Pemantauan Gempa Real-time BMKG (Lampung)</title>
    <meta name="description" content="Sistem monitoring gempa bumi terkini berbasis data BMKG dengan fokus wilayah Lampung dan sekitarnya">
    <meta name="author" content="Rinova Generasi Berbakti">
    
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/img/favicon-16x16.png">
    <link rel="manifest" href="assets/img/site.webmanifest">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/core.css">
    <link rel="stylesheet" href="assets/css/bmkg.css">
</head>
<body class="bmkg-body">
    <?php include 'includes/navbar.php'; ?>

                <div class="last-update">
                    <i class="fas fa-sync-alt"></i> <span id="lastUpdate">Memuat data...</span>
                </div>

    
    <main class="container-fluid bmkg-main">
        <!-- Hero Section -->
        <div class="bmkg-hero">
            <div class="hero-content">
                <h1 class="hero-title">
                    <i class="fas fa-broadcast-tower"></i>
                    Monitor Gempa Bumi Real-time
                </h1>
                <p class="hero-subtitle">Data resmi dari Badan Meteorologi, Klimatologi, dan Geofisika (BMKG)</p>
                <div class="last-update-badge">
                    <i class="fas fa-sync-alt"></i> <span id="lastUpdate">Memuat data...</span>
                </div>
            </div>
        </div>

        <!-- Alert Banner for Lampung Earthquakes -->
        <div id="lampungAlert" class="alert-banner warning" style="display: none;">
            <i class="fas fa-exclamation-triangle"></i>
            <div class="alert-content">
                <div class="alert-title">
                    <span>Peringatan Gempa Lampung!</span>
                </div>
                <div id="lampungAlertDetails">Mendeteksi aktivitas gempa di wilayah Lampung</div>
            </div>
            <button class="btn btn-sm btn-lampung" id="dismissAlert">
                <i class="fas fa-times"></i> Tutup
            </button>
        </div>
        
        <!-- Stats Section -->
        <div class="stats-container" id="statsContainer">
            <div class="stat-card primary">
                <i class="fas fa-bolt stat-icon"></i>
                <div class="stat-value" id="totalQuakes">-</div>
                <div class="stat-label">Total Gempa Terkini</div>
                <div class="stat-subtext">(Magnitudo ≥ 5.0)</div>
            </div>
            
            <div class="stat-card lampung">
                <i class="fas fa-map-marker-alt stat-icon"></i>
                <div class="stat-value" id="lampungQuakes">-</div>
                <div class="stat-label">Gempa di Lampung</div>
                <div class="stat-subtext">30 Hari Terakhir</div>
            </div>
            
            <div class="stat-card danger">
                <i class="fas fa-mountain stat-icon"></i>
                <div class="stat-value" id="largestQuake">-</div>
                <div class="stat-label">Gempa Terbesar</div>
                <div class="stat-subtext" id="largestQuakeLocation">-</div>
            </div>
            
            <div class="stat-card warning">
                <i class="fas fa-ruler-vertical stat-icon"></i>
                <div class="stat-value" id="avgDepth">-</div>
                <div class="stat-label">Kedalaman Rata-rata</div>
                <div class="stat-subtext">Dalam Kilometer</div>
            </div>
        </div>
        
        <!-- Dashboard Grid -->
        <div class="dashboard-grid">
            <!-- Latest Earthquake Card -->
            <div class="card latest-quake-card">
                <div class="card-header">
                    <h2><i class="fas fa-bolt"></i> Gempa Terkini</h2>
                    <div class="badge badge-live">
                        <span class="pulse-dot"></span>
                        Live
                    </div>
                </div>
                <div class="card-body" id="latestQuake">
                    <div class="loading">
                        <div class="spinner"></div>
                        <div>Memuat data gempa terkini...</div>
                    </div>
                </div>
            </div>
            
            <!-- Shakemap Card -->
            <div class="card shakemap-card">
                <div class="card-header">
                    <h2><i class="fas fa-map-marked-alt"></i> Peta Guncangan (Shakemap)</h2>
                    <button class="btn btn-sm btn-lampung" id="refreshShakemap">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
                <div class="card-body">
                    <div class="shakemap-container" id="shakemapContainer">
                        <div class="loading">
                            <div class="spinner"></div>
                            <div>Memuat peta guncangan...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Earthquake List Section -->
        <div class="tab-container">
            <div class="tabs">
                <div class="tab active" data-tab="recent">15 Gempa Terkini (M ≥ 5.0)</div>
                <div class="tab" data-tab="felt">15 Gempa Dirasakan</div>
                <div class="tab" data-tab="lampung">Gempa di Lampung</div>
            </div>
            
            <!-- Filter Controls -->
            <div class="filter-container" id="filterContainer">
                <button class="filter-btn active" data-filter="all">
                    <i class="fas fa-globe-asia"></i> Semua Wilayah
                </button>
                <button class="filter-btn" data-filter="lampung">
                    <i class="fas fa-map-marker-alt"></i> Lampung & Sekitarnya
                </button>
                <button class="filter-btn" data-filter="significant">
                    <i class="fas fa-exclamation-triangle"></i> Signifikan (M ≥ 6.0)
                </button>
            </div>
            
            <!-- Recent Quakes Tab -->
            <div class="tab-content active" id="recentQuakes">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="earthquake-list">
                                <thead>
                                    <tr>
                                        <th>Waktu</th>
                                        <th>Lokasi</th>
                                        <th>Magnitudo</th>
                                        <th>Kedalaman</th>
                                        <th>Koordinat</th>
                                    </tr>
                                </thead>
                                <tbody id="recentQuakesData">
                                    <tr>
                                        <td colspan="5">
                                            <div class="loading">
                                                <div class="spinner"></div>
                                                <div>Memuat data gempa terkini...</div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Felt Quakes Tab -->
            <div class="tab-content" id="feltQuakes">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="earthquake-list">
                                <thead>
                                    <tr>
                                        <th>Waktu</th>
                                        <th>Lokasi</th>
                                        <th>Magnitudo</th>
                                        <th>Kedalaman</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody id="feltQuakesData">
                                    <tr>
                                        <td colspan="5">
                                            <div class="loading">
                                                <div class="spinner"></div>
                                                <div>Memuat data gempa dirasakan...</div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Lampung Quakes Tab -->
            <div class="tab-content" id="lampungQuakesTab">
                <div class="card">
                    <div class="card-body">
                        <div id="lampungStats" style="margin-bottom: 20px;"></div>
                        <div class="table-responsive">
                            <table class="earthquake-list">
                                <thead>
                                    <tr>
                                        <th>Waktu</th>
                                        <th>Lokasi</th>
                                        <th>Magnitudo</th>
                                        <th>Kedalaman</th>
                                        <th>Jarak dari Lampung</th>
                                    </tr>
                                </thead>
                                <tbody id="lampungQuakesData">
                                    <tr>
                                        <td colspan="5">
                                            <div class="loading">
                                                <div class="spinner"></div>
                                                <div>Memuat data gempa wilayah Lampung...</div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </main>
    
    <?php include 'includes/footer.php'; ?>

    <script>
        // Lampung coordinates (approximate center point)
        const LAMPUNG_COORDS = {
            lat: -5.1099,
            lon: 105.2253
        };
        
        // Radius for "near Lampung" filter (in km)
        const NEARBY_RADIUS = 300;
        
        // Tab functionality
        const tabs = document.querySelectorAll('.tab');
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active class from all tabs and contents
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                
                // Add active class to clicked tab and corresponding content
                tab.classList.add('active');
                const tabId = tab.getAttribute('data-tab');
                document.getElementById(tabId + 'Quakes' + (tabId === 'lampung' ? 'Tab' : '')).classList.add('active');
            });
        });
        
        // Filter functionality
        const filterButtons = document.querySelectorAll('.filter-btn');
        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                filterButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
                
                const filter = button.getAttribute('data-filter');
                const activeTab = document.querySelector('.tab.active').getAttribute('data-tab');
                
                if (activeTab === 'recent') {
                    filterQuakes(filter);
                } else if (activeTab === 'felt') {
                    filterFeltQuakes(filter);
                }
            });
        });
        
        // Format date to Indonesian format
        function formatDate(dateString) {
            if (!dateString) return '-';
            
            const months = [
                'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 
                'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
            ];
            
            const date = new Date(dateString);
            if (isNaN(date.getTime())) {
                // If date is invalid, try to parse custom format
                const parts = dateString.split(' ');
                if (parts.length >= 2) {
                    return dateString; // Return as is if already formatted
                }
                return '-';
            }
            
            const day = date.getDate();
            const month = months[date.getMonth()];
            const year = date.getFullYear();
            const hours = date.getHours().toString().padStart(2, '0');
            const minutes = date.getMinutes().toString().padStart(2, '0');
            
            return `${day} ${month} ${year}, ${hours}:${minutes} WIB`;
        }
        
        // Get magnitude badge class
        function getMagnitudeClass(magnitude) {
            const mag = parseFloat(magnitude);
            if (mag >= 7.0) return 'magnitude-7';
            if (mag >= 6.0) return 'magnitude-6';
            if (mag >= 5.0) return 'magnitude-5';
            if (mag >= 4.0) return 'magnitude-4';
            if (mag >= 3.0) return 'magnitude-3';
            return 'magnitude-2';
        }
        
        // Get magnitude description
        function getMagnitudeDescription(magnitude) {
            const mag = parseFloat(magnitude);
            if (mag >= 8.0) return '<span class="severity-extreme">Sangat Besar - Kerusakan Masif</span>';
            if (mag >= 7.0) return '<span class="severity-major">Besar - Kerusakan Parah</span>';
            if (mag >= 6.0) return '<span class="severity-strong">Kuat - Merusak</span>';
            if (mag >= 5.0) return '<span class="severity-moderate">Sedang - Dapat Dirasakan</span>';
            if (mag >= 4.0) return '<span class="severity-light">Ringan - Getaran Terasa</span>';
            return '<span class="severity-minor">Sangat Ringan</span>';
        }
        
        // Calculate distance between two coordinates (Haversine formula)
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371; // Earth radius in km
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = 
                Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
                Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }
        
        // Parse coordinates from BMKG format
        function parseCoordinates(coordsStr) {
            if (!coordsStr) return null;
            
            // Example format: "1.48 LS - 126.42 BT"
            const parts = coordsStr.split('-').map(part => part.trim());
            if (parts.length !== 2) return null;
            
            try {
                const latPart = parts[0].split(' ');
                const lonPart = parts[1].split(' ');
                
                let lat = parseFloat(latPart[0]);
                let lon = parseFloat(lonPart[0]);
                
                // Adjust for direction
                if (latPart[1].toUpperCase() === 'LS') lat *= -1;
                if (lonPart[1].toUpperCase() === 'BB') lon *= -1;
                
                return { lat, lon };
            } catch (e) {
                console.error('Error parsing coordinates:', e);
                return null;
            }
        }
        
        // Check if location is in Lampung or nearby
        function isLampungArea(wilayah) {
            if (!wilayah) return false;
            return wilayah.toLowerCase().includes('lampung');
        }
        
        // Check if coordinates are near Lampung
        function isNearLampung(coords) {
            if (!coords) return false;
            const distance = calculateDistance(
                coords.lat, coords.lon, 
                LAMPUNG_COORDS.lat, LAMPUNG_COORDS.lon
            );
            return distance <= NEARBY_RADIUS;
        }
        
        // Fetch data with error handling
        async function fetchData(url) {
            try {
                const response = await fetch(url);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return await response.json();
            } catch (error) {
                console.error('Error fetching data:', error);
                return null;
            }
        }
        
        // Save gempa Lampung to database
        async function saveGempaToDatabase(quake) {
            try {
                const response = await fetch('<?= BASE_URL ?>/api/save-gempa-lampung.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(quake)
                });
                
                const result = await response.json();
                
                if (result.success && !result.data?.skipped) {
                    console.log('✅ Gempa saved:', quake.Magnitude, 'SR -', quake.Wilayah);
                } else if (result.data?.skipped) {
                    console.log('⏭️ Gempa already exists:', quake.Wilayah);
                }
            } catch (error) {
                console.error('❌ Error saving gempa:', error);
            }
        }
        
        // Load latest earthquake data
        async function loadLatestQuake() {
            const container = document.getElementById('latestQuake');
            
            const data = await fetchData('https://data.bmkg.go.id/DataMKG/TEWS/autogempa.json');
            if (!data) {
                container.innerHTML = `
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i> Gagal memuat data gempa terkini. Silakan coba lagi.
                    </div>
                `;
                return null;
            }
            
            const quake = data.Infogempa.gempa;
            const shakemapUrl = `https://data.bmkg.go.id/DataMKG/TEWS/${quake.Shakemap}`;
            const coords = parseCoordinates(quake.Coordinates);
            const isLampung = isLampungArea(quake.Wilayah);
            const isNearby = coords ? isNearLampung(coords) : false;
            
            // Check if this quake should trigger Lampung alert
            if (isLampung || isNearby) {
                showLampungAlert(quake, isLampung);
                // Auto-save latest quake jika di Lampung atau sekitarnya
                saveGempaToDatabase(quake);
            }
            
            container.innerHTML = `
                <div class="quake-main-info">
                    <div class="magnitude-display">
                        <div class="magnitude-circle ${getMagnitudeClass(quake.Magnitude)}">
                            <div class="magnitude-value">${quake.Magnitude}</div>
                            <div class="magnitude-unit">SR</div>
                        </div>
                        <div class="magnitude-description">
                            ${getMagnitudeDescription(quake.Magnitude)}
                        </div>
                    </div>
                    
                    <div class="quake-location-info">
                        <div class="location-primary">
                            <i class="fas fa-map-marker-alt"></i>
                            ${quake.Wilayah}
                        </div>
                        ${isLampung || isNearby ? `
                            <div class="location-badges">
                                ${isLampung ? '<span class="location-badge lampung-badge"><i class="fas fa-exclamation-circle"></i> WILAYAH LAMPUNG</span>' : ''}
                                ${(!isLampung && isNearby) ? '<span class="location-badge nearby-badge"><i class="fas fa-location-arrow"></i> DEKAT LAMPUNG</span>' : ''}
                            </div>
                        ` : ''}
                    </div>
                </div>
                
                <div class="quake-details-grid">
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="far fa-calendar-alt"></i>
                        </div>
                        <div class="detail-content">
                            <div class="detail-label">Tanggal</div>
                            <div class="detail-value">${quake.Tanggal}</div>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="far fa-clock"></i>
                        </div>
                        <div class="detail-content">
                            <div class="detail-label">Jam</div>
                            <div class="detail-value">${quake.Jam}</div>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-arrows-alt-v"></i>
                        </div>
                        <div class="detail-content">
                            <div class="detail-label">Kedalaman</div>
                            <div class="detail-value">${quake.Kedalaman}</div>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-crosshairs"></i>
                        </div>
                        <div class="detail-content">
                            <div class="detail-label">Koordinat</div>
                            <div class="detail-value">${quake.Coordinates}</div>
                        </div>
                    </div>
                </div>
                
                ${quake.Potensi ? `
                    <div class="potential-warning">
                        <div class="warning-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="warning-content">
                            <div class="warning-title">Potensi Dampak</div>
                            <div class="warning-text">${quake.Potensi}</div>
                        </div>
                    </div>
                ` : ''}
            `;
            
            // Update last update time
            updateLastUpdateTime();
            
            // Return shakemap code and quake data for refreshing
            return {
                shakemapCode: quake.Shakemap,
                quakeData: quake
            };
        }
        
        // Show alert for Lampung earthquakes
        function showLampungAlert(quake, isLampung) {
            const alertBanner = document.getElementById('lampungAlert');
            const alertDetails = document.getElementById('lampungAlertDetails');
            
            if (parseFloat(quake.Magnitude) >= 6.0) {
                alertBanner.className = 'alert-banner danger';
            } else {
                alertBanner.className = 'alert-banner warning';
            }
            
            alertDetails.innerHTML = `
                Gempa ${quake.Magnitude} SR terjadi di ${isLampung ? 'Lampung' : 'sekitar Lampung'} 
                pada ${quake.Tanggal} ${quake.Jam} dengan kedalaman ${quake.Kedalaman}. 
                ${quake.Potensi ? 'Potensi: ' + quake.Potensi : ''}
            `;
            
            alertBanner.style.display = 'flex';
            
            // Add animation
            alertBanner.classList.add('animate__animated', 'animate__pulse');
            
            // Auto hide after 1 hour
            setTimeout(() => {
                alertBanner.style.display = 'none';
            }, 60 * 60 * 1000);
        }
        
        // Dismiss alert manually
        document.getElementById('dismissAlert').addEventListener('click', function() {
            document.getElementById('lampungAlert').style.display = 'none';
        });
        
        // Load recent earthquakes (M 5.0+)
        async function loadRecentQuakes() {
            const container = document.getElementById('recentQuakesData');
            
            const data = await fetchData('https://data.bmkg.go.id/DataMKG/TEWS/gempaterkini.json');
            if (!data || !data.Infogempa || !data.Infogempa.gempa) {
                container.innerHTML = `
                    <tr>
                        <td colspan="5">
                            <div class="error-message">
                                <i class="fas fa-exclamation-triangle"></i> Gagal memuat data gempa terkini. Silakan coba lagi.
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }
            
            let quakes = Array.isArray(data.Infogempa.gempa) ? data.Infogempa.gempa : [data.Infogempa.gempa];
            
            // Add distance information for each quake
            quakes = quakes.map(quake => {
                const coords = parseCoordinates(quake.Coordinates);
                let distance = null;
                if (coords) {
                    distance = calculateDistance(
                        coords.lat, coords.lon,
                        LAMPUNG_COORDS.lat, LAMPUNG_COORDS.lon
                    );
                }
                return {
                    ...quake,
                    distance,
                    isLampung: isLampungArea(quake.Wilayah),
                    isNearby: distance ? distance <= NEARBY_RADIUS : false
                };
            });
            
            // Auto-save gempa Lampung dan sekitarnya ke database
            quakes.forEach(quake => {
                if (quake.isLampung || quake.isNearby) {
                    saveGempaToDatabase(quake);
                }
            });
            
            // Update stats
            updateStats(quakes);
            
            // Store for filtering
            window.recentQuakesData = quakes;
            
            // Initial render
            filterQuakes('all');
            
            updateLastUpdateTime();
        }
        
        // Load felt earthquakes
        async function loadFeltQuakes() {
            const container = document.getElementById('feltQuakesData');
            
            const data = await fetchData('https://data.bmkg.go.id/DataMKG/TEWS/gempadirasakan.json');
            if (!data || !data.Infogempa || !data.Infogempa.gempa) {
                container.innerHTML = `
                    <tr>
                        <td colspan="5">
                            <div class="error-message">
                                <i class="fas fa-exclamation-triangle"></i> Gagal memuat data gempa dirasakan. Silakan coba lagi.
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }
            
            let quakes = Array.isArray(data.Infogempa.gempa) ? data.Infogempa.gempa : [data.Infogempa.gempa];
            
            // Add distance information for each quake
            quakes = quakes.map(quake => {
                const coords = parseCoordinates(quake.Coordinates);
                let distance = null;
                if (coords) {
                    distance = calculateDistance(
                        coords.lat, coords.lon,
                        LAMPUNG_COORDS.lat, LAMPUNG_COORDS.lon
                    );
                }
                return {
                    ...quake,
                    distance,
                    isLampung: isLampungArea(quake.Wilayah),
                    isNearby: distance ? distance <= NEARBY_RADIUS : false
                };
            });
            
            // Auto-save gempa Lampung dan sekitarnya ke database
            quakes.forEach(quake => {
                if (quake.isLampung || quake.isNearby) {
                    saveGempaToDatabase(quake);
                }
            });
            
            // Store for filtering
            window.feltQuakesData = quakes;
            
            // Initial render
            filterFeltQuakes('all');
            
            updateLastUpdateTime();
        }
        
        // Load Lampung earthquakes
        async function loadLampungQuakes() {
            const container = document.getElementById('lampungQuakesData');
            const statsContainer = document.getElementById('lampungStats');
            
            // We'll use the felt quakes data as it covers a longer period
            const data = await fetchData('https://data.bmkg.go.id/DataMKG/TEWS/gempadirasakan.json');
            if (!data || !data.Infogempa || !data.Infogempa.gempa) {
                container.innerHTML = `
                    <tr>
                        <td colspan="5">
                            <div class="error-message">
                                <i class="fas fa-exclamation-triangle"></i> Gagal memuat data gempa wilayah Lampung. Silakan coba lagi.
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }
            
            let quakes = Array.isArray(data.Infogempa.gempa) ? data.Infogempa.gempa : [data.Infogempa.gempa];
            
            // Filter for Lampung and nearby quakes and add distance
            quakes = quakes
                .map(quake => {
                    const coords = parseCoordinates(quake.Coordinates);
                    let distance = null;
                    if (coords) {
                        distance = calculateDistance(
                            coords.lat, coords.lon,
                            LAMPUNG_COORDS.lat, LAMPUNG_COORDS.lon
                        );
                    }
                    return {
                        ...quake,
                        distance,
                        isLampung: isLampungArea(quake.Wilayah),
                        isNearby: distance ? distance <= NEARBY_RADIUS : false
                    };
                })
                .filter(quake => quake.isLampung || quake.isNearby)
                .sort((a, b) => {
                    // Sort by date descending
                    const dateA = new Date(`${a.Tanggal} ${a.Jam}`);
                    const dateB = new Date(`${b.Tanggal} ${b.Jam}`);
                    return dateB - dateA;
                });
            
            // Update stats
            const lampungCount = quakes.filter(q => q.isLampung).length;
            const nearbyCount = quakes.filter(q => !q.isLampung && q.isNearby).length;
            const largestInLampung = quakes.reduce((max, quake) => 
                parseFloat(quake.Magnitude) > parseFloat(max.Magnitude) ? quake : max, 
                quakes[0] || { Magnitude: '-', Wilayah: '-' }
            );
            
            statsContainer.innerHTML = `
                <div class="alert-banner info">
                    <i class="fas fa-info-circle"></i>
                    <div class="alert-content">
                        <div class="alert-title">
                            <span>Statistik Gempa Lampung</span>
                        </div>
                        <div>
                            ${lampungCount} gempa di Lampung, 
                            ${nearbyCount} gempa di sekitar Lampung (${NEARBY_RADIUS} km).
                            Gempa terbesar: ${largestInLampung.Magnitude} SR di ${largestInLampung.Wilayah}.
                        </div>
                    </div>
                </div>
            `;
            
            if (quakes.length === 0) {
                container.innerHTML = `
                    <tr>
                        <td colspan="5">
                            <div class="no-data">
                                <i class="fas fa-info-circle"></i> Tidak ada data gempa di wilayah Lampung dalam periode ini.
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }
            
            container.innerHTML = quakes.map(quake => `
                <tr class="${quake.isLampung ? 'lampung-highlight' : ''}">
                    <td>${quake.Tanggal} ${quake.Jam}</td>
                    <td>
                        ${quake.Wilayah}
                        ${!quake.isLampung ? '<span class="distance-badge">DEKAT</span>' : ''}
                    </td>
                    <td>
                        <span class="magnitude-badge ${getMagnitudeClass(quake.Magnitude)}">
                            ${quake.Magnitude} SR
                        </span>
                    </td>
                    <td>${quake.Kedalaman}</td>
                    <td>
                        ${quake.distance ? `${Math.round(quake.distance)} km` : '-'}
                    </td>
                </tr>
            `).join('');
            
            updateLastUpdateTime();
        }
        
        // Update statistics
        function updateStats(quakes) {
            if (!quakes || quakes.length === 0) return;
            
            // Total quakes today
            document.getElementById('totalQuakes').textContent = quakes.length;
            
            // Count quakes in Lampung (last 30 days - approximate)
            const lampungQuakes = quakes.filter(q => q.isLampung).length;
            document.getElementById('lampungQuakes').textContent = lampungQuakes;
            
            // Largest quake
            const largestQuake = quakes.reduce((max, quake) => 
                parseFloat(quake.Magnitude) > parseFloat(max.Magnitude) ? quake : max, 
                quakes[0]
            );
            document.getElementById('largestQuake').textContent = largestQuake.Magnitude;
            document.getElementById('largestQuakeLocation').textContent = largestQuake.Wilayah;
            
            // Average depth
            const avgDepth = quakes.reduce((sum, quake) => {
                const depthStr = quake.Kedalaman.split(' ')[0]; // Extract numeric part
                return sum + parseFloat(depthStr);
            }, 0) / quakes.length;
            document.getElementById('avgDepth').textContent = avgDepth.toFixed(1);
        }
        
        // Filter quakes based on selected filter
        function filterQuakes(filter) {
            const quakes = window.recentQuakesData || [];
            const container = document.getElementById('recentQuakesData');
            
            let filteredQuakes = quakes;
            
            switch(filter) {
                case 'lampung':
                    // Gabungkan Lampung dan sekitarnya (nearby)
                    filteredQuakes = quakes.filter(q => q.isLampung || q.isNearby);
                    break;
                case 'significant':
                    filteredQuakes = quakes.filter(q => parseFloat(q.Magnitude) >= 6.0);
                    break;
                // 'all' is default
            }
            
            if (filteredQuakes.length === 0) {
                container.innerHTML = `
                    <tr>
                        <td colspan="5">
                            <div class="no-data">
                                <i class="fas fa-info-circle"></i> Tidak ada data gempa yang sesuai dengan filter.
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }
            
            container.innerHTML = filteredQuakes.map(quake => `
                <tr class="${quake.isLampung ? 'lampung-highlight' : ''}">
                    <td>${quake.Tanggal} ${quake.Jam}</td>
                    <td>
                        ${quake.Wilayah}
                        ${quake.isLampung ? '<span class="distance-badge">LAMPUNG</span>' : ''}
                        ${(!quake.isLampung && quake.isNearby) ? '<span class="distance-badge">SEKITAR</span>' : ''}
                    </td>
                    <td>
                        <span class="magnitude-badge ${getMagnitudeClass(quake.Magnitude)}">
                            ${quake.Magnitude} SR
                        </span>
                    </td>
                    <td>${quake.Kedalaman}</td>
                    <td>${quake.Coordinates}</td>
                </tr>
            `).join('');
        }
        
        // Filter felt quakes based on selected filter
        function filterFeltQuakes(filter) {
            const quakes = window.feltQuakesData || [];
            const container = document.getElementById('feltQuakesData');
            
            let filteredQuakes = quakes;
            
            switch(filter) {
                case 'lampung':
                    // Gabungkan Lampung dan sekitarnya (nearby)
                    filteredQuakes = quakes.filter(q => q.isLampung || q.isNearby);
                    break;
                case 'significant':
                    filteredQuakes = quakes.filter(q => parseFloat(q.Magnitude) >= 6.0);
                    break;
                // 'all' is default
            }
            
            if (filteredQuakes.length === 0) {
                container.innerHTML = `
                    <tr>
                        <td colspan="5">
                            <div class="no-data">
                                <i class="fas fa-info-circle"></i> Tidak ada data gempa yang sesuai dengan filter.
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }
            
            container.innerHTML = filteredQuakes.map(quake => `
                <tr class="${quake.isLampung ? 'lampung-highlight' : ''}">
                    <td>${quake.Tanggal} ${quake.Jam}</td>
                    <td>
                        ${quake.Wilayah}
                        ${quake.isLampung ? '<span class="distance-badge">LAMPUNG</span>' : ''}
                        ${(!quake.isLampung && quake.isNearby) ? '<span class="distance-badge">SEKITAR</span>' : ''}
                    </td>
                    <td>
                        <span class="magnitude-badge ${getMagnitudeClass(quake.Magnitude)}">
                            ${quake.Magnitude} SR
                        </span>
                    </td>
                    <td>${quake.Kedalaman}</td>
                    <td>${quake.Dirasakan || '-'}</td>
                </tr>
            `).join('');
        }
        
        // Update shakemap
        async function updateShakemap(shakemapCode) {
            const container = document.getElementById('shakemapContainer');
            if (!shakemapCode) {
                container.innerHTML = `
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i> Tidak dapat memuat shakemap. Data tidak tersedia.
                    </div>
                `;
                return;
            }
            
            const shakemapUrl = `https://data.bmkg.go.id/DataMKG/TEWS/${shakemapCode}`;
            
            // Check if image exists
            const img = new Image();
            img.onload = function() {
                container.innerHTML = `
                    <img src="${shakemapUrl}" alt="Shakemap Terkini" class="shakemap">
                    <p class="shakemap-update">
                        <i class="fas fa-clock"></i> Terakhir diperbarui: <span id="shakemapUpdateTime">${new Date().toLocaleTimeString()}</span>
                    </p>
                `;
            };
            img.onerror = function() {
                container.innerHTML = `
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i> Shakemap tidak tersedia untuk gempa ini.
                    </div>
                `;
            };
            img.src = shakemapUrl;
        }
        
        // Update last update time
        function updateLastUpdateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            const dateString = now.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
            document.getElementById('lastUpdate').textContent = `Terakhir diperbarui: ${dateString} ${timeString}`;
        }
        
        // Event listeners
        document.getElementById('refreshShakemap').addEventListener('click', async function() {
            const container = document.getElementById('shakemapContainer');
            container.innerHTML = `
                <div class="loading">
                    <div class="spinner"></div>
                    <div>Memperbarui peta guncangan...</div>
                </div>
            `;
            
            // Reload latest quake to get fresh shakemap code
            const result = await loadLatestQuake();
            if (result) {
                await updateShakemap(result.shakemapCode);
            }
        });
        
        // Initial load
        async function initialize() {
            const result = await loadLatestQuake();
            await Promise.all([
                loadRecentQuakes(),
                loadFeltQuakes(),
                loadLampungQuakes(),
                result ? updateShakemap(result.shakemapCode) : Promise.resolve()
            ]);
            
            // Auto-refresh every 5 minutes
            setInterval(async () => {
                const result = await loadLatestQuake();
                await Promise.all([
                    loadRecentQuakes(),
                    loadFeltQuakes(),
                    loadLampungQuakes(),
                    result ? updateShakemap(result.shakemapCode) : Promise.resolve()
                ]);
            }, 5 * 60 * 1000);
        }
        
        // Start the application
        document.addEventListener('DOMContentLoaded', initialize);
    </script>
</body>
</html>