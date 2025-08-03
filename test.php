<?php
require_once 'includes/config.php';

try {
    $conn = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    echo "Koneksi database BERHASIL!";
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}