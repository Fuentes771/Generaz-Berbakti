<?php
include 'includes/config.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Gempa Lampung | Sistem Pemantauan Gempa</title>
    <meta name="description" content="Riwayat lengkap gempa bumi yang terjadi di Lampung dan sekitarnya">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/core.css">
    <link rel="stylesheet" href="assets/css/bmkg.css">
    
    <style>
        .history-header {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .stat-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid #3498db;
            text-align: center;
        }
        
        .stat-box.lampung {
            border-left-color: #16a085;
        }
        
        .stat-box.danger {
            border-left-color: #e74c3c;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            font-family: 'Montserrat', sans-serif;
        }
        
        .stat-label {
            font-size: 0.85rem;
            color: #7f8c8d;
            margin-top: 5px;
        }
        
        .filter-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        
        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        
        .form-group label {
            font-weight: 600;
            font-size: 0.9rem;
            color: #2c3e50;
            margin-bottom: 8px;
            display: block;
        }
        
        .form-control {
            border-radius: 8px;
            border: 2px solid #ecf0f1;
            padding: 10px 15px;
        }
        
        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52,152,219,0.25);
        }
        
        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 25px;
        }
        
        .pagination-btn {
            padding: 8px 16px;
            border-radius: 8px;
            border: 2px solid #3498db;
            background: white;
            color: #3498db;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .pagination-btn:hover:not(:disabled) {
            background: #3498db;
            color: white;
        }
        
        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .pagination-info {
            padding: 8px 16px;
            background: #f8f9fa;
            border-radius: 8px;
            font-weight: 600;
            color: #2c3e50;
        }
    </style>
</head>
<body class="bmkg-body">
    <?php include 'includes/navbar.php'; ?>
    
    <main class="container-fluid bmkg-main">
        <!-- Header -->
        <div class="history-header">
            <h1 style="color: #2c3e50; margin-bottom: 10px;">
                <i class="fas fa-history"></i> History Gempa Lampung
            </h1>
            <p style="color: #7f8c8d; margin-bottom: 20px;">
                Riwayat lengkap data gempa bumi yang terjadi di wilayah Lampung dan sekitarnya
            </p>
            
            <div class="stats-grid" id="statsGrid">
                <div class="stat-box">
                    <div class="stat-number" id="totalCount">-</div>
                    <div class="stat-label">Total Gempa Tercatat</div>
                </div>
                <div class="stat-box lampung">
                    <div class="stat-number" id="lampungCount">-</div>
                    <div class="stat-label">Gempa di Lampung</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number" id="nearbyCount">-</div>
                    <div class="stat-label">Gempa Sekitar (300km)</div>
                </div>
                <div class="stat-box danger">
                    <div class="stat-number" id="maxMagnitude">-</div>
                    <div class="stat-label">Magnitudo Terbesar</div>
                </div>
            </div>
        </div>
        
        <!-- Filter -->
        <div class="filter-section">
            <h4 style="margin-bottom: 20px; color: #2c3e50;">
                <i class="fas fa-filter"></i> Filter Data
            </h4>
            <div class="filter-row">
                <div class="form-group">
                    <label for="startDate">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="startDate">
                </div>
                <div class="form-group">
                    <label for="endDate">Tanggal Akhir</label>
                    <input type="date" class="form-control" id="endDate">
                </div>
                <div class="form-group">
                    <label for="minMagnitude">Magnitudo Minimal</label>
                    <select class="form-control" id="minMagnitude">
                        <option value="0">Semua</option>
                        <option value="3">≥ 3.0 SR</option>
                        <option value="4">≥ 4.0 SR</option>
                        <option value="5" selected>≥ 5.0 SR</option>
                        <option value="6">≥ 6.0 SR</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button class="btn btn-lampung w-100" onclick="loadHistory()">
                        <i class="fas fa-search"></i> Terapkan Filter
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Data Table -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-table"></i> Data Gempa</h2>
                <button class="btn btn-sm btn-lampung" onclick="exportToCSV()">
                    <i class="fas fa-download"></i> Export CSV
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="earthquake-list">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal & Jam</th>
                                <th>Lokasi</th>
                                <th>Magnitudo</th>
                                <th>Kedalaman</th>
                                <th>Koordinat</th>
                                <th>Jarak (km)</th>
                                <th>Kategori</th>
                            </tr>
                        </thead>
                        <tbody id="historyData">
                            <tr>
                                <td colspan="8">
                                    <div class="loading">
                                        <div class="spinner"></div>
                                        <div>Memuat data history...</div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="pagination-container" id="paginationContainer" style="display: none;">
                    <button class="pagination-btn" id="prevBtn" onclick="changePage(-1)">
                        <i class="fas fa-chevron-left"></i> Sebelumnya
                    </button>
                    <div class="pagination-info" id="paginationInfo">Halaman 1 / 1</div>
                    <button class="pagination-btn" id="nextBtn" onclick="changePage(1)">
                        Selanjutnya <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    
    <script>
        let currentPage = 1;
        let totalPages = 1;
        let limit = 50;
        let historyData = [];
        
        // Load history on page load
        document.addEventListener('DOMContentLoaded', () => {
            loadHistory();
        });
        
        async function loadHistory() {
            const container = document.getElementById('historyData');
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const minMagnitude = document.getElementById('minMagnitude').value;
            
            container.innerHTML = `
                <tr>
                    <td colspan="8">
                        <div class="loading">
                            <div class="spinner"></div>
                            <div>Memuat data history...</div>
                        </div>
                    </td>
                </tr>
            `;
            
            const offset = (currentPage - 1) * limit;
            let url = `<?= BASE_URL ?>/api/get-gempa-history.php?limit=${limit}&offset=${offset}&lampung_only=true`;
            
            if (startDate) url += `&start_date=${startDate}`;
            if (endDate) url += `&end_date=${endDate}`;
            if (minMagnitude) url += `&min_magnitude=${minMagnitude}`;
            
            try {
                const response = await fetch(url);
                const result = await response.json();
                
                if (!result.success) {
                    throw new Error(result.message);
                }
                
                historyData = result.data;
                totalPages = result.pagination.pages;
                
                // Update statistics
                updateStatistics(result.statistics);
                
                // Display data
                displayHistory(result.data, offset);
                
                // Update pagination
                updatePagination();
                
            } catch (error) {
                console.error('Error loading history:', error);
                container.innerHTML = `
                    <tr>
                        <td colspan="8">
                            <div class="error-message">
                                <i class="fas fa-exclamation-triangle"></i> 
                                Gagal memuat data: ${error.message}
                            </div>
                        </td>
                    </tr>
                `;
            }
        }
        
        function displayHistory(data, offset) {
            const container = document.getElementById('historyData');
            
            if (data.length === 0) {
                container.innerHTML = `
                    <tr>
                        <td colspan="8">
                            <div class="no-data">
                                <i class="fas fa-info-circle"></i>
                                Tidak ada data gempa yang sesuai dengan filter
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }
            
            container.innerHTML = data.map((quake, index) => `
                <tr class="${quake.is_lampung == 1 ? 'lampung-highlight' : ''}">
                    <td>${offset + index + 1}</td>
                    <td>
                        ${quake.tanggal}<br>
                        <small style="color: #7f8c8d;">${quake.jam}</small>
                    </td>
                    <td>${quake.wilayah}</td>
                    <td>
                        <span class="magnitude-badge ${getMagnitudeClass(quake.magnitude)}">
                            ${quake.magnitude} SR
                        </span>
                    </td>
                    <td>${quake.kedalaman}</td>
                    <td><small>${quake.coordinates}</small></td>
                    <td>${quake.distance_from_lampung ? Math.round(quake.distance_from_lampung) : '-'}</td>
                    <td>
                        ${quake.is_lampung == 1 
                            ? '<span class="distance-badge">LAMPUNG</span>' 
                            : '<span class="distance-badge">SEKITAR</span>'}
                    </td>
                </tr>
            `).join('');
        }
        
        function updateStatistics(stats) {
            document.getElementById('totalCount').textContent = stats.total_earthquakes.toLocaleString('id-ID');
            document.getElementById('lampungCount').textContent = stats.lampung_earthquakes.toLocaleString('id-ID');
            document.getElementById('nearbyCount').textContent = stats.nearby_earthquakes.toLocaleString('id-ID');
            document.getElementById('maxMagnitude').textContent = stats.max_magnitude + ' SR';
        }
        
        function updatePagination() {
            const container = document.getElementById('paginationContainer');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const info = document.getElementById('paginationInfo');
            
            if (totalPages > 1) {
                container.style.display = 'flex';
                prevBtn.disabled = currentPage === 1;
                nextBtn.disabled = currentPage === totalPages;
                info.textContent = `Halaman ${currentPage} / ${totalPages}`;
            } else {
                container.style.display = 'none';
            }
        }
        
        function changePage(direction) {
            currentPage += direction;
            if (currentPage < 1) currentPage = 1;
            if (currentPage > totalPages) currentPage = totalPages;
            loadHistory();
        }
        
        function getMagnitudeClass(magnitude) {
            const mag = parseFloat(magnitude);
            if (mag >= 7.0) return 'magnitude-7';
            if (mag >= 6.0) return 'magnitude-6';
            if (mag >= 5.0) return 'magnitude-5';
            if (mag >= 4.0) return 'magnitude-4';
            if (mag >= 3.0) return 'magnitude-3';
            return 'magnitude-2';
        }
        
        function exportToCSV() {
            if (historyData.length === 0) {
                alert('Tidak ada data untuk di-export');
                return;
            }
            
            const headers = ['No', 'Tanggal', 'Jam', 'Lokasi', 'Magnitudo', 'Kedalaman', 'Koordinat', 'Jarak (km)', 'Kategori'];
            const rows = historyData.map((quake, index) => [
                index + 1,
                quake.tanggal,
                quake.jam,
                quake.wilayah,
                quake.magnitude,
                quake.kedalaman,
                quake.coordinates,
                quake.distance_from_lampung ? Math.round(quake.distance_from_lampung) : '-',
                quake.is_lampung == 1 ? 'LAMPUNG' : 'SEKITAR'
            ]);
            
            let csv = headers.join(',') + '\n';
            csv += rows.map(row => row.map(cell => `"${cell}"`).join(',')).join('\n');
            
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `gempa-lampung-history-${new Date().toISOString().split('T')[0]}.csv`;
            link.click();
        }
    </script>
</body>
</html>
