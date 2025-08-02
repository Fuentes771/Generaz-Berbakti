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
    <link rel="stylesheet" href="assets/css/styles.css">
    
    <style>
        :root {
            --primary-color: #3498db;
            --primary-dark: #2980b9;
            --danger-color: #e74c3c;
            --danger-dark: #c0392b;
            --warning-color: #f39c12;
            --warning-dark: #e67e22;
            --success-color: #2ecc71;
            --success-dark: #27ae60;
            --dark-color: #2c3e50;
            --dark-dark: #1a252f;
            --light-color: #ecf0f1;
            --gray-color: #95a5a6;
            --lampung-color: #16a085;
            --lampung-dark: #1abc9c;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
            color: #333;
            line-height: 1.6;
            overflow-x: hidden;
        }
        
        .container-fluid {
            max-width: 1400px;
            padding: 0 20px;
        }
        
        /* Header Styles */
        .main-header {
            background: linear-gradient(135deg, var(--dark-color), var(--dark-dark));
            color: white;
            padding: 15px 0;
            box-shadow: var(--shadow);
            position: relative;
            z-index: 100;
            border-bottom: 5px solid var(--lampung-color);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
            color: white;
        }
        
        .logo-icon {
            font-size: 2rem;
            color: var(--lampung-color);
        }
        
        .logo-text h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0;
            line-height: 1.2;
            background: linear-gradient(to right, var(--lampung-color), var(--primary-color));
            -webkit-text-fill-color: transparent;
        }
        
        .logo-text p {
            font-size: 0.85rem;
            opacity: 0.9;
            margin-bottom: 0;
        }
        
        .last-update {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 8px 15px;
            border-radius: 30px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: var(--transition);
        }
        
        .last-update:hover {
            background-color: rgba(255, 255, 255, 0.15);
        }
        
        /* Main Content */
        main {
            padding: 30px 0;
            position: relative;
        }
        
        /* Alert Banner */
        .alert-banner {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            border-left: 5px solid;
            box-shadow: var(--shadow);
            transition: var(--transition);
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .alert-banner i {
            font-size: 1.5rem;
            flex-shrink: 0;
        }
        
        .alert-banner .alert-content {
            flex-grow: 1;
        }
        
        .alert-banner .alert-title {
            font-weight: 600;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .alert-banner.warning {
            background-color: rgba(255, 193, 7, 0.15);
            border-left-color: var(--warning-color);
            color: #856404;
        }
        
        .alert-banner.danger {
            background-color: rgba(220, 53, 69, 0.15);
            border-left-color: var(--danger-color);
            color: #721c24;
        }
        
        .alert-banner.info {
            background-color: rgba(23, 162, 184, 0.15);
            border-left-color: var(--primary-color);
            color: #0c5460;
        }
        
        .alert-banner.success {
            background-color: rgba(40, 167, 69, 0.15);
            border-left-color: var(--success-color);
            color: #155724;
        }
        
        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: var(--shadow);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            border-top: 4px solid;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0));
            pointer-events: none;
        }
        
        .stat-card.primary {
            border-top-color: var(--primary-color);
        }
        
        .stat-card.lampung {
            border-top-color: var(--lampung-color);
        }
        
        .stat-card.danger {
            border-top-color: var(--danger-color);
        }
        
        .stat-card.warning {
            border-top-color: var(--warning-color);
        }
        
        .stat-icon {
            font-size: 2rem;
            margin-bottom: 15px;
            color: inherit;
            opacity: 0.8;
        }
        
        .stat-value {
            font-size: 2.2rem;
            font-weight: 700;
            margin: 5px 0;
            font-family: 'Montserrat', sans-serif;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: var(--gray-color);
            margin-bottom: 5px;
        }
        
        .stat-subtext {
            font-size: 0.8rem;
            opacity: 0.8;
        }
        
        /* Dashboard Grid */
        .dashboard {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 30px;
        }
        
        @media (max-width: 992px) {
            .dashboard {
                grid-template-columns: 1fr;
            }
        }
        
        /* Card Styles */
        .card {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: var(--transition);
            border: none;
            position: relative;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--dark-color), var(--dark-dark));
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: none;
        }
        
        .card-header h2 {
            font-size: 1.3rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
        }
        
        .card-header .badge {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .card-body {
            padding: 25px;
        }
        
        /* Earthquake Info */
        .earthquake-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        @media (max-width: 576px) {
            .earthquake-info {
                grid-template-columns: 1fr;
            }
        }
        
        .info-item {
            margin-bottom: 15px;
        }
        
        .info-label {
            font-size: 0.85rem;
            color: var(--gray-color);
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }
        
        .info-value {
            font-size: 1.05rem;
            font-weight: 500;
            word-break: break-word;
        }
        
        /* Magnitude Display */
        .magnitude-container {
            text-align: center;
            margin: 20px 0;
            position: relative;
        }
        
        .magnitude {
            font-size: 3.5rem;
            font-weight: 800;
            font-family: 'Montserrat', sans-serif;
            color: var(--danger-color);
            line-height: 1;
            position: relative;
            display: inline-block;
            text-shadow: 0 2px 5px rgba(231, 76, 60, 0.2);
        }
        
        .magnitude::after {
            content: "SR";
            font-size: 1rem;
            position: absolute;
            top: 10px;
            right: -25px;
            color: var(--gray-color);
            font-weight: normal;
        }
        
        .magnitude-scale {
            font-size: 0.8rem;
            color: var(--gray-color);
            margin-top: 5px;
        }
        
        /* Location Display */
        .location {
            font-size: 1.3rem;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
            position: relative;
            padding-bottom: 15px;
        }
        
        .location::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, var(--lampung-color), var(--primary-color));
            border-radius: 3px;
        }
        
        /* Shakemap Styles */
        .shakemap-container {
            margin-top: 20px;
            text-align: center;
            position: relative;
        }
        
        .shakemap {
            max-width: 100%;
            border-radius: 8px;
            box-shadow: var(--shadow);
            border: 1px solid #eee;
            transition: var(--transition);
        }
        
        .shakemap:hover {
            transform: scale(1.02);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }
        
        .shakemap-update {
            font-size: 0.8rem;
            color: var(--gray-color);
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        
        /* Earthquake List Styles */
        .earthquake-list {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .earthquake-list thead th {
            background-color: var(--dark-color);
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-size: 0.9rem;
            font-weight: 500;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .earthquake-list th:first-child {
            border-top-left-radius: 8px;
        }
        
        .earthquake-list th:last-child {
            border-top-right-radius: 8px;
        }
        
        .earthquake-list td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            font-size: 0.9rem;
            vertical-align: middle;
        }
        
        .earthquake-list tr:last-child td {
            border-bottom: none;
        }
        
        .earthquake-list tr:hover {
            background-color: #f9f9f9;
        }
        
        /* Magnitude Badges */
        .magnitude-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            color: white;
            min-width: 70px;
            text-align: center;
            box-shadow: 0 2px 3px rgba(0,0,0,0.1);
        }
        
        .magnitude-2 {
            background-color: #2ecc71;
        }
        
        .magnitude-3 {
            background-color: #3498db;
        }
        
        .magnitude-4 {
            background-color: #f1c40f;
        }
        
        .magnitude-5 {
            background-color: #e67e22;
        }
        
        .magnitude-6 {
            background-color: #e74c3c;
        }
        
        .magnitude-7 {
            background-color: #c0392b;
        }
        
        /* Distance Badge */
        .distance-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 600;
            background-color: var(--lampung-color);
            color: white;
            margin-left: 5px;
            vertical-align: middle;
        }
        
        /* Lampung Highlight */
        .lampung-highlight {
            background-color: rgba(22, 160, 133, 0.05);
            position: relative;
        }
        
        .lampung-highlight::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background-color: var(--lampung-color);
        }
        
        /* Loading State */
        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
            flex-direction: column;
            gap: 15px;
            text-align: center;
            color: var(--gray-color);
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
        
        /* Error Message */
        .error-message {
            background-color: #fee;
            color: var(--danger-color);
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border-left: 4px solid var(--danger-color);
        }
        
        /* No Data State */
        .no-data {
            text-align: center;
            padding: 30px;
            color: var(--gray-color);
            font-size: 0.9rem;
        }
        
        /* Tab Styles */
        .tab-container {
            margin-bottom: 30px;
        }
        
        .tabs {
            display: flex;
            border-bottom: 2px solid #eee;
            flex-wrap: wrap;
            gap: 5px;
        }
        
        .tab {
            padding: 10px 20px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: var(--transition);
            font-size: 0.9rem;
            position: relative;
            font-weight: 500;
            color: var(--gray-color);
            border-radius: 5px 5px 0 0;
        }
        
        .tab:hover {
            color: var(--dark-color);
            background-color: #f5f5f5;
        }
        
        .tab.active {
            border-bottom-color: var(--primary-color);
            color: var(--primary-color);
            font-weight: 600;
            background-color: rgba(52, 152, 219, 0.05);
        }
        
        .tab-content {
            display: none;
            animation: fadeIn 0.3s ease;
        }
        
        .tab-content.active {
            display: block;
        }
        
        /* Filter Controls */
        .filter-container {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            padding: 15px 0;
        }
        
        .filter-btn {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            background-color: #f0f0f0;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }
        
        .filter-btn:hover {
            background-color: #e0e0e0;
        }
        
        .filter-btn.active {
            background-color: var(--lampung-color);
            color: white;
        }
        
        .filter-btn i {
            font-size: 0.8rem;
        }
        
        /* Button Styles */
        .btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 30px;
            cursor: pointer;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            font-weight: 500;
            box-shadow: var(--shadow);
        }
        
        .btn:hover {
            background-color: var(--primary-dark);
            color: white;
            transform: translateY(-2px);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .btn-lampung {
            background-color: var(--lampung-color);
        }
        
        .btn-lampung:hover {
            background-color: var(--lampung-dark);
        }
        
        .btn-warning {
            background-color: var(--warning-color);
        }
        
        .btn-warning:hover {
            background-color: var(--warning-dark);
        }
        
        .btn-danger {
            background-color: var(--danger-color);
        }
        
        .btn-danger:hover {
            background-color: var(--danger-dark);
        }
        
        /* Footer Styles */
        .main-footer {
            background-color: var(--dark-color);
            color: white;
            padding: 30px 0;
            margin-top: 50px;
        }
        
        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .footer-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            text-decoration: none;
        }
        
        .footer-logo i {
            font-size: 1.5rem;
            color: var(--lampung-color);
        }
        
        .footer-logo-text {
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
        }
        
        .footer-links {
            display: flex;
            gap: 15px;
        }
        
        .footer-link {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: var(--transition);
        }
        
        .footer-link:hover {
            color: white;
        }
        
        .footer-bottom {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.6);
        }
        
        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
            }
            
            .logo {
                justify-content: center;
            }
            
            .stats-container {
                grid-template-columns: 1fr 1fr;
            }
        }
        
        @media (max-width: 576px) {
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            .tab {
                padding: 8px 12px;
                font-size: 0.8rem;
            }
            
            .earthquake-list td, 
            .earthquake-list th {
                padding: 8px 10px;
                font-size: 0.8rem;
            }
            
            .magnitude {
                font-size: 2.5rem;
            }
        }
        
        /* Animation Classes */
        .animate-bounce {
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
        
        .animate-pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }
    </style>
</head>
<body>

                <div class="last-update">
                    <i class="fas fa-sync-alt"></i> <span id="lastUpdate">Memuat data...</span>
                </div>

    
    <main class="container-fluid">
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
        <div class="dashboard">
            <!-- Latest Earthquake Card -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-bolt"></i> Gempa Terkini</h2>
                    <div class="badge">Auto Update</div>
                </div>
                <div class="card-body" id="latestQuake">
                    <div class="loading">
                        <div class="spinner"></div>
                        <div>Memuat data gempa terkini...</div>
                    </div>
                </div>
            </div>
            
            <!-- Shakemap Card -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-map-marked-alt"></i> Peta Guncangan</h2>
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
            <div class="filter-container" id="filterContainer" style="display: none;">
                <button class="filter-btn active" data-filter="all">
                    <i class="fas fa-globe-asia"></i> Semua
                </button>
                <button class="filter-btn" data-filter="lampung">
                    <i class="fas fa-map-marker-alt"></i> Lampung
                </button>
                <button class="filter-btn" data-filter="nearby">
                    <i class="fas fa-location-arrow"></i> Sekitar Lampung
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
        
        <!-- Map Section -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-map"></i> Peta Sebaran Gempa Lampung</h2>
                <div class="btn-group">
                    <button class="btn btn-sm btn-primary" id="loadMap">
                        <i class="fas fa-layer-group"></i> Muat Peta
                    </button>
                    <button class="btn btn-sm btn-lampung" id="refreshMap">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="map-container" id="mapContainer">
                    <div class="map-placeholder">
                        <i class="fas fa-map-marked-alt" style="font-size: 3rem; color: var(--lampung-color);"></i>
                        <h4 style="margin: 15px 0 10px; color: var(--dark-color);">Peta Sebaran Gempa Lampung</h4>
                        <p>Klik "Muat Peta" untuk menampilkan visualisasi sebaran gempa di wilayah Lampung dan sekitarnya.</p>
                        <button class="btn btn-lampung mt-3" id="loadMapBtn">
                            <i class="fas fa-map"></i> Muat Peta
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <footer class="main-footer">
            <div class="footer-bottom">
                <p>© <span id="currentYear"></span> Rinova Generasi Berbakti | Sistem Pemantauan Gempa Bumi</p>
                <p class="mt-2">Data diperoleh dari Badan Meteorologi, Klimatologi, dan Geofisika (BMKG)</p>
            </div>
    </footer>

    <?php include 'includes/footer.php'; ?>

    <script>
        // Set current year in footer
        document.getElementById('currentYear').textContent = new Date().getFullYear();
        
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
                
                // Show/hide filter container
                const filterContainer = document.getElementById('filterContainer');
                if (tabId === 'recent' || tabId === 'felt') {
                    filterContainer.style.display = 'flex';
                } else {
                    filterContainer.style.display = 'none';
                }
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
            }
            
            container.innerHTML = `
                <div class="magnitude-container">
                    <div class="magnitude">${quake.Magnitude}</div>
                    <div class="magnitude-scale">Skala Richter</div>
                </div>
                
                <div class="location">
                    ${quake.Wilayah}
                    ${isLampung ? '<span class="distance-badge">LAMPUNG</span>' : ''}
                    ${(!isLampung && isNearby) ? '<span class="distance-badge">DEKAT LAMPUNG</span>' : ''}
                </div>
                
                <div class="earthquake-info">
                    <div class="info-item">
                        <div class="info-label"><i class="far fa-calendar-alt"></i> Tanggal</div>
                        <div class="info-value">${quake.Tanggal}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="far fa-clock"></i> Jam</div>
                        <div class="info-value">${quake.Jam}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-ruler-vertical"></i> Kedalaman</div>
                        <div class="info-value">${quake.Kedalaman}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-map-marker-alt"></i> Koordinat</div>
                        <div class="info-value">${quake.Coordinates}</div>
                    </div>
                </div>
                
                <div class="info-item" style="margin-top: 15px;">
                    <div class="info-label"><i class="fas fa-exclamation-circle"></i> Potensi</div>
                    <div class="info-value">${quake.Potensi || '-'}</div>
                </div>
                
                <div class="shakemap-container">
                    <img src="${shakemapUrl}" alt="Shakemap Gempa ${quake.Tanggal}" class="shakemap" id="latestShakemap">
                    <p class="shakemap-update">
                        <i class="fas fa-clock"></i> Terakhir diperbarui: <span id="shakemapUpdateTime">${new Date().toLocaleTimeString()}</span>
                    </p>
                </div>
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
                    filteredQuakes = quakes.filter(q => q.isLampung);
                    break;
                case 'nearby':
                    filteredQuakes = quakes.filter(q => !q.isLampung && q.isNearby);
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
                        ${(!quake.isLampung && quake.isNearby) ? '<span class="distance-badge">DEKAT</span>' : ''}
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
                    filteredQuakes = quakes.filter(q => q.isLampung);
                    break;
                case 'nearby':
                    filteredQuakes = quakes.filter(q => !q.isLampung && q.isNearby);
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
                        ${(!quake.isLampung && quake.isNearby) ? '<span class="distance-badge">DEKAT</span>' : ''}
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
        
        // Load map (placeholder for actual map implementation)
        function loadMap() {
            const mapContainer = document.getElementById('mapContainer');
            mapContainer.innerHTML = `
                <div class="map-placeholder">
                    <i class="fas fa-map-marked-alt" style="font-size: 3rem; color: var(--lampung-color);"></i>
                    <h4 style="margin: 15px 0 10px; color: var(--dark-color);">Peta Interaktif Gempa Lampung</h4>
                    <p>Fitur peta interaktif sedang dalam pengembangan</p>
                    <p style="font-size: 0.9rem; margin-top: 10px;">
                        <i class="fas fa-info-circle"></i> Peta ini akan menampilkan visualisasi sebaran gempa di wilayah Lampung dan sekitarnya.
                    </p>
                </div>
            `;
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
        
        document.getElementById('loadMapBtn').addEventListener('click', loadMap);
        document.getElementById('loadMap').addEventListener('click', loadMap);
        document.getElementById('refreshMap').addEventListener('click', loadMap);
        
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