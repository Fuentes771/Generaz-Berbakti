<?php
/**
 * Enhanced Tsunami Warning System Configuration
 * 
 * @version 2.1.0
 * @license MIT
 */

// ========================
// Application Configuration
// ========================

// Debug Mode (true for development, false for production)
define('DEBUG_MODE', true);

// Application Version
define('APP_VERSION', '2.1.0');

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// ========================
// Security Configuration
// ========================

define('SESSION_TIMEOUT', 1800); // 30 minutes in seconds
define('CSRF_TOKEN_LIFE', 3600); // 1 hour in seconds
define('API_KEY', 'TSUNAMI_' . bin2hex(random_bytes(16)));
define('RECEIVER_API_KEY', 'arduino123'); // For Arduino/LoRa communication

// ========================
// Database Configuration
// ========================

// Use environment variables if available, fallback to defaults
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'u855675680_mntrrinovajaya');
define('DB_PASS', getenv('DB_PASS') ?: 'Generazberbaktijaya123!');
define('DB_NAME', getenv('DB_NAME') ?: 'u855675680_mntrpekon');
define('DB_CHARSET', 'utf8mb4');

// ========================
// Device Configuration
// ========================

// IoT Device Configuration
define('ESP_IP', '10.63.234.35');
define('ESP_UPDATE_INTERVAL', 60); // Update interval in seconds
define('MAX_SENSOR_VALUE', 100);

// LoRa Configuration
define('RECEIVER_IP', '10.63.234.35'); // IP address of the LoRa receiver

// ========================
// Threshold Configuration
// ========================

// Threshold values for sensors
define('VIBRATION_WARNING', 50000);  // Level getaran untuk status peringatan
define('VIBRATION_DANGER', 80000);   // Level getaran untuk status bahaya
define('ACCELERATION_WARNING', 50000); // Akselerasi untuk status peringatan (m/s²)
define('ACCELERATION_DANGER', 80000);  // Akselerasi untuk status bahaya (m/s²)

// ========================
// Path Configuration
// ========================

// Deteksi protocol dan host secara aman
$protocol = 'http://';
if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
    (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ||
    (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) {
    $protocol = 'https://';
}

$host = 'pekontelukkiluan.com'; // Default untuk CLI
if (!empty($_SERVER['HTTP_HOST'])) {
    $host = $_SERVER['HTTP_HOST'];
} elseif (!empty($_SERVER['SERVER_NAME'])) {
    $host = $_SERVER['SERVER_NAME'];
}

// Hitung base path
$scriptPath = !empty($_SERVER['SCRIPT_NAME']) ? dirname($_SERVER['SCRIPT_NAME']) : '/monitoring';
$basePath = str_replace('/includes', '', $scriptPath);

// Definisikan BASE_URL
define('BASE_URL', rtrim($protocol . $host . $basePath, '/'));define('ASSETS_PATH', '/assets');
define('API_PATH', BASE_URL . '/api');

// ========================
// Error Handling
// ========================

error_reporting(DEBUG_MODE ? E_ALL : E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', DEBUG_MODE ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../logs/error.log');

// ========================
// Security Functions
// ========================

/**
 * Sanitize input data to prevent XSS and other injections
 */
function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitize output data (alias for sanitize_input for consistency)
 */
function sanitizeOutput($data) {
    return sanitize_input($data);
}

/**
 * Validate sensor data structure and values
 */
function validate_sensor_data(array $data): bool {
    $required = ['vibration', 'mpu6050', 'pressure', 'temperature', 'humidity'];
    foreach ($required as $field) {
        if (!isset($data[$field]) || !is_numeric($data[$field])) {
            return false;
        }
    }
    return true;
}

// ========================
// Database Functions
// ========================

/**
 * Get a secure database connection (singleton pattern)
 */
function getDatabaseConnection() {
    static $db = null;
    
    if ($db === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => false // Changed from true for better resource management
            ];
            
            $db = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            header('HTTP/1.1 500 Internal Server Error');
            die('Sistem sedang tidak tersedia. Silakan coba lagi nanti.');
        }
    }
    
    return $db;
}

// ========================
// Session Configuration
// ========================

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => SESSION_TIMEOUT,
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'],
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    session_start();
}

// CSRF Protection
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ========================
// Utility Functions
// ========================

/**
 * Redirect to specified URL
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// ========================
// Autoload Classes
// ========================

spl_autoload_register(function ($class_name) {
    $file = __DIR__ . '/../classes/' . str_replace('\\', '/', $class_name) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});