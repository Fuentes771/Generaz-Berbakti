<?php
require __DIR__ . '/../includes/config.php';

header('Content-Type: text/plain');

// Test 1: Cek apakah file config terbaca
echo "=== CONFIG TEST ===\n";
var_dump([
    'DB_HOST' => defined('DB_HOST') ? DB_HOST : 'NOT DEFINED',
    'DB_NAME' => defined('DB_NAME') ? DB_NAME : 'NOT DEFINED',
    'DB_USER' => defined('DB_USER') ? '*****' : 'NOT DEFINED', // Mask password
    'DB_PASS' => defined('DB_PASS') ? '*****' : 'NOT DEFINED'
]);

// Test 2: Cek koneksi database
echo "\n=== DATABASE CONNECTION TEST ===\n";
try {
    $conn = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME, 
        DB_USER, 
        DB_PASS
    );
    echo "SUCCESS: Connected to database\n";
    
    // Test 3: Cek tabel
    $stmt = $conn->query("SHOW TABLES LIKE 'sensor_data'");
    echo "TABLE CHECK: " . ($stmt->rowCount() ? "EXISTS" : "MISSING");
    
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}