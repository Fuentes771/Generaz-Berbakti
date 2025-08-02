<?php
// Include configuration and header
include 'includes/config.php';
include 'includes/navbar.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gempa Monitor | Data Real-time BMKG</title>
    <meta name="description" content="Sistem monitoring gempa bumi terkini berbasis data BMKG dengan fokus wilayah Lampung">
    
    <!-- Favicon -->
    <link rel="icon" href="https://www.bmkg.go.id/asset/img/logo/logo-bmkg.png" type="image/x-icon">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Favicon -->
    <link rel="icon" href="assets/img/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/apple-touch-icon.png">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
    
    <style>
        :root {
            --primary-color: #3498db;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --success-color: #2ecc71;
            --dark-color: #2c3e50;
            --light-color: #ecf0f1;
            --gray-color: #95a5a6;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background-color: var(--dark-color);
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo img {
            height: 40px;
        }
        
        .logo-text h1 {
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .logo-text p {
            font-size: 0.8rem;
            opacity: 0.8;
        }
        
        .last-update {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        
        .dashboard {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        @media (max-width: 768px) {
            .dashboard {
                grid-template-columns: 1fr;
            }
        }
        
        .card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-header h2 {
            font-size: 1.2rem;
            font-weight: 500;
        }
        
        .card-header .badge {
            background-color: white;
            color: var(--primary-color);
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .earthquake-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .info-item {
            margin-bottom: 10px;
        }
        
        .info-label {
            font-size: 0.8rem;
            color: var(--gray-color);
            margin-bottom: 3px;
        }
        
        .info-value {
            font-size: 1rem;
            font-weight: 500;
        }
        
        .magnitude {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--danger-color);
            text-align: center;
            margin: 15px 0;
        }
        
        .location {
            font-size: 1.2rem;
            font-weight: 500;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .shakemap-container {
            margin-top: 15px;
            text-align: center;
        }
        
        .shakemap {
            max-width: 100%;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .earthquake-list {
            width: 100%;
            border-collapse: collapse;
        }
        
        .earthquake-list th {
            background-color: var(--light-color);
            padding: 10px 15px;
            text-align: left;
            font-size: 0.9rem;
        }
        
        .earthquake-list td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            font-size: 0.9rem;
        }
        
        .earthquake-list tr:last-child td {
            border-bottom: none;
        }
        
        .earthquake-list tr:hover {
            background-color: #f9f9f9;
        }
        
        .magnitude-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.8rem;
            color: white;
        }
        
        .magnitude-5 {
            background-color: var(--warning-color);
        }
        
        .magnitude-6 {
            background-color: #e67e22;
        }
        
        .magnitude-7 {
            background-color: var(--danger-color);
        }
        
        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }
        
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            border-top-color: var(--primary-color);
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .error-message {
            background-color: #fee;
            color: var(--danger-color);
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
        }
        
        /* Filter Styles */
        .filter-container {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .filter-label {
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .filter-select {
            padding: 8px 12px;
            border-radius: 5px;
            border: 1px solid #ddd;
            background-color: white;
        }
        
        .refresh-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: background-color 0.3s;
        }
        
        .refresh-btn:hover {
            background-color: #2980b9;
        }
        
        .tab-container {
            margin-bottom: 20px;
        }
        
        .tabs {
            display: flex;
            border-bottom: 1px solid #ddd;
        }
        
        .tab {
            padding: 10px 20px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }
        
        .tab.active {
            border-bottom-color: var(--primary-color);
            color: var(--primary-color);
            font-weight: 500;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .no-data {
            text-align: center;
            padding: 30px;
            color: var(--gray-color);
            font-size: 0.9rem;
        }
        footer {
            text-align: center;
            padding: 20px;
            color: var(--gray-color);
            font-size: 0.9rem;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    
    <main class="container">
        <!-- Filter Section -->
        <div class="filter-container">
            <div class="filter-group">
                <span class="filter-label">Filter Wilayah:</span>
                <select id="regionFilter" class="filter-select">
                    <option value="all">Semua Wilayah</option>
                    <option value="lampung" selected>Lampung</option>
                </select>
            </div>
            <button class="refresh-btn" id="refreshAll">
                <i class="fas fa-sync-alt"></i> Refresh Data
            </button>
        </div>
        
        <div class="dashboard">
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-bolt"></i> Gempa Terkini</h2>
                    <div class="badge">Auto Update</div>
                </div>
                <div class="card-body" id="latestQuake">
                    <div class="loading">
                        <div class="spinner"></div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-map-marked-alt"></i> Shakemap</h2>
                    <button class="refresh-btn" id="refreshShakemap">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
                <div class="card-body">
                    <div class="shakemap-container" id="shakemapContainer">
                        <div class="loading">
                            <div class="spinner"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="tab-container">
            <div class="tabs">
                <div class="tab active" data-tab="recent">15 Gempa Terkini (M 5.0+)</div>
                <div class="tab" data-tab="felt">15 Gempa Dirasakan</div>
            </div>
            
            <div class="tab-content active" id="recentQuakes">
                <div class="card">
                    <div class="card-body">
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
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="tab-content" id="feltQuakes">
                <div class="card">
                    <div class="card-body">
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
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

        <footer>
        <p>Data diperoleh dari Badan Meteorologi, Klimatologi, dan Geofisika (BMKG)</p>
        <p>© <span id="currentYear"></span> Gempa Monitor - Sistem Pemantauan Gempa Bumi</p>
    </footer>

    <?php include 'includes/footer.php'; ?>


    <script>
      // Set current year in footer
        document.getElementById('currentYear').textContent = new Date().getFullYear();
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
            return 'magnitude-5';
        }
        
        // Check if earthquake is in Lampung region
        function isInLampung(earthquake) {
            if (!earthquake.Wilayah) return false;
            return earthquake.Wilayah.toLowerCase().includes('lampung');
        }
        
        // Filter earthquakes based on region
        function filterEarthquakes(earthquakes, regionFilter) {
            if (regionFilter === 'all') return earthquakes;
            return earthquakes.filter(quake => isInLampung(quake));
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
            
            container.innerHTML = `
                <div class="magnitude">${quake.Magnitude}</div>
                <div class="location">${quake.Wilayah}</div>
                
                <div class="earthquake-info">
                    <div class="info-item">
                        <div class="info-label">Tanggal</div>
                        <div class="info-value">${quake.Tanggal}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Jam</div>
                        <div class="info-value">${quake.Jam}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Kedalaman</div>
                        <div class="info-value">${quake.Kedalaman}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Koordinat</div>
                        <div class="info-value">${quake.Coordinates}</div>
                    </div>
                </div>
                
                <div class="shakemap-container">
                    <img src="${shakemapUrl}" alt="Shakemap Gempa ${quake.Tanggal}" class="shakemap" id="latestShakemap">
                </div>
                
                <div class="info-item" style="margin-top: 15px;">
                    <div class="info-label">Potensi</div>
                    <div class="info-value">${quake.Potensi || '-'}</div>
                </div>
            `;
            
            // Update last update time
            updateLastUpdateTime();
            
            // Return shakemap code for refreshing
            return quake.Shakemap;
        }
        
        // Load recent earthquakes (M 5.0+)
        async function loadRecentQuakes() {
            const container = document.getElementById('recentQuakesData');
            const regionFilter = document.getElementById('regionFilter').value;
            
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
            quakes = filterEarthquakes(quakes, regionFilter);
            
            if (quakes.length === 0) {
                container.innerHTML = `
                    <tr>
                        <td colspan="5">
                            <div class="no-data">
                                <i class="fas fa-info-circle"></i> Tidak ada data gempa di wilayah Lampung
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }
            
            container.innerHTML = quakes.map(quake => `
                <tr>
                    <td>${quake.Tanggal} ${quake.Jam}</td>
                    <td>${quake.Wilayah}</td>
                    <td>
                        <span class="magnitude-badge ${getMagnitudeClass(quake.Magnitude)}">
                            ${quake.Magnitude} SR
                        </span>
                    </td>
                    <td>${quake.Kedalaman}</td>
                    <td>${quake.Coordinates}</td>
                </tr>
            `).join('');
            
            updateLastUpdateTime();
        }
        
        // Load felt earthquakes
        async function loadFeltQuakes() {
            const container = document.getElementById('feltQuakesData');
            const regionFilter = document.getElementById('regionFilter').value;
            
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
            quakes = filterEarthquakes(quakes, regionFilter);
            
            if (quakes.length === 0) {
                container.innerHTML = `
                    <tr>
                        <td colspan="5">
                            <div class="no-data">
                                <i class="fas fa-info-circle"></i> Tidak ada data gempa dirasakan di wilayah Lampung
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }
            
            container.innerHTML = quakes.map(quake => `
                <tr>
                    <td>${quake.Tanggal} ${quake.Jam}</td>
                    <td>${quake.Wilayah}</td>
                    <td>
                        <span class="magnitude-badge ${getMagnitudeClass(quake.Magnitude)}">
                            ${quake.Magnitude} SR
                        </span>
                    </td>
                    <td>${quake.Kedalaman}</td>
                    <td>${quake.Dirasakan || '-'}</td>
                </tr>
            `).join('');
            
            updateLastUpdateTime();
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
                    <p style="margin-top: 10px; font-size: 0.8rem; color: var(--gray-color);">
                        Terakhir diperbarui: <span id="shakemapUpdateTime">${new Date().toLocaleTimeString()}</span>
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
            document.getElementById('lastUpdate').textContent = `Terakhir diperbarui: ${timeString}`;
        }
        
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
                document.getElementById(tabId + 'Quakes').classList.add('active');
            });
        });
        
        // Refresh all data
        document.getElementById('refreshAll').addEventListener('click', async function() {
            // Show loading state
            document.getElementById('latestQuake').innerHTML = `
                <div class="loading">
                    <div class="spinner"></div>
                </div>
            `;
            document.getElementById('recentQuakesData').innerHTML = `
                <tr>
                    <td colspan="5">
                        <div class="loading">
                            <div class="spinner"></div>
                        </div>
                    </td>
                </tr>
            `;
            document.getElementById('feltQuakesData').innerHTML = `
                <tr>
                    <td colspan="5">
                        <div class="loading">
                            <div class="spinner"></div>
                        </div>
                    </td>
                </tr>
            `;
            
            // Reload all data
            const shakemapCode = await loadLatestQuake();
            await Promise.all([
                loadRecentQuakes(),
                loadFeltQuakes(),
                updateShakemap(shakemapCode)
            ]);
        });
        
        // Refresh shakemap
        document.getElementById('refreshShakemap').addEventListener('click', async function() {
            const container = document.getElementById('shakemapContainer');
            container.innerHTML = `
                <div class="loading">
                    <div class="spinner"></div>
                </div>
            `;
            
            // Reload latest quake to get fresh shakemap code
            const shakemapCode = await loadLatestQuake();
            await updateShakemap(shakemapCode);
        });
        
        // Region filter change
        document.getElementById('regionFilter').addEventListener('change', async function() {
            await Promise.all([
                loadRecentQuakes(),
                loadFeltQuakes()
            ]);
        });
        
        // Initial load
        async function initialize() {
            const shakemapCode = await loadLatestQuake();
            await Promise.all([
                loadRecentQuakes(),
                loadFeltQuakes(),
                updateShakemap(shakemapCode)
            ]);
            
            // Auto-refresh every 5 minutes
            setInterval(async () => {
                const shakemapCode = await loadLatestQuake();
                await loadRecentQuakes();
                await loadFeltQuakes();
            }, 5 * 60 * 1000);
        }
        
        // Start the application
        initialize();
    </script>
</body>
</html>