<?php
/**
 * Tsunami Warning System - Configuration
 * Clean, minimal, professional
 * 
 * @version 3.1.0
 */

// === CORE SETTINGS ===
date_default_timezone_set('Asia/Jakarta');
define('APP_VERSION', '3.1.0');
define('DEBUG_MODE', filter_var(getenv('DEBUG_MODE') ?: 'true', FILTER_VALIDATE_BOOLEAN)); // Default TRUE for development

error_reporting(DEBUG_MODE ? E_ALL : E_ERROR | E_WARNING);
ini_set('display_errors', DEBUG_MODE ? '1' : '0');
ini_set('log_errors', '1');

// === DATABASE ===
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'u855675680_mntrpekon');
define('DB_CHARSET', 'utf8mb4');

// === UTILITIES (defined early for use in other functions) ===
function logEvent(string $msg, string $level = 'INFO'): void {
    $logFile = __DIR__ . '/../logs/app.log';
    $entry = sprintf("[%s] %s: %s\n", date('Y-m-d H:i:s'), strtoupper($level), $msg);
    @file_put_contents($logFile, $entry, FILE_APPEND);
    if (in_array($level, ['ERROR', 'CRITICAL'])) error_log($entry);
}

function getDatabaseConnection(): ?PDO {
    static $db = null;
    static $failed = false;
    
    if ($failed) {
        return null; // Don't retry if already failed
    }
    
    if ($db === null) {
        try {
            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
            $db = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '+07:00'"
            ]);
            logEvent('Database connected successfully');
        } catch (PDOException $e) {
            $failed = true;
            $errorMsg = "Database connection failed: " . $e->getMessage();
            logEvent($errorMsg, 'ERROR');
            
            // In DEBUG mode, show detailed error
            if (DEBUG_MODE) {
                echo "<div style='background:#fee;border:1px solid #c00;padding:20px;margin:20px;border-radius:8px;font-family:sans-serif;'>";
                echo "<h3 style='color:#c00;margin:0 0 10px 0;'>❌ Database Connection Failed</h3>";
                echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
                echo "<p><strong>Database:</strong> " . DB_NAME . "</p>";
                echo "<p><strong>Host:</strong> " . DB_HOST . "</p>";
                echo "<p><strong>User:</strong> " . DB_USER . "</p>";
                echo "<hr style='margin:15px 0;'>";
                echo "<p><strong>Troubleshooting:</strong></p>";
                echo "<ol style='margin:10px 0;padding-left:20px;'>";
                echo "<li>Make sure XAMPP/Laragon MySQL service is <strong>running</strong></li>";
                echo "<li>Check if database '<code>" . DB_NAME . "</code>' exists in phpMyAdmin</li>";
                echo "<li>Verify database credentials in config.php</li>";
                echo "<li>Visit <a href='" . ($_SERVER['REQUEST_URI'] ?? '') . "/../check-db.php' style='color:#007bff;'>check-db.php</a> for detailed diagnostics</li>";
                echo "</ol>";
                echo "</div>";
            }
            
            return null;
        }
    }
    return $db;
}

// === SECURITY ===
define('API_KEY', getenv('API_KEY') ?: 'default-key');
define('RECEIVER_API_KEY', getenv('RECEIVER_API_KEY') ?: 'changeme');

if (php_sapi_name() !== 'cli' && !headers_sent()) {
    header_remove('X-Powered-By');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

// === THRESHOLDS ===
define('VIBRATION_WARNING', 50000);
define('VIBRATION_DANGER', 80000);
define('ACCELERATION_WARNING', 50000);
define('ACCELERATION_DANGER', 80000);

function computeStatus(float $vib, float $mpu): array {
    if ($vib >= VIBRATION_DANGER || $mpu >= ACCELERATION_DANGER) return ['BAHAYA', 'status-danger'];
    if ($vib >= VIBRATION_WARNING || $mpu >= ACCELERATION_WARNING) return ['PERINGATAN', 'status-warning'];
    return ['NORMAL', 'status-normal'];
}

// === PATHS ===
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptPath = dirname($_SERVER['SCRIPT_NAME'] ?? '');
$basePath = str_replace('/includes', '', $scriptPath);

define('BASE_URL', rtrim($protocol . $host . $basePath, '/'));
define('ASSETS_PATH', BASE_URL . '/assets');
define('API_PATH', BASE_URL . '/api');

// === UTILITIES ===
function sanitizeInput($data) {
    if (is_array($data)) return array_map('sanitizeInput', $data);
    return htmlspecialchars(trim($data), ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function redirect(string $url, int $code = 303): void {
    if (!headers_sent()) {
        header("Location: $url", true, $code);
        exit;
    }
    echo "<script>location.href='$url';</script>";
    exit;
}

// === SESSION ===
if (php_sapi_name() !== 'cli' && session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 1800,
        'path' => '/',
        'secure' => $protocol === 'https://',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
    if (!isset($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function verifyCsrf(?string $token = null): bool {
    $token = $token ?? $_POST['csrf_token'] ?? '';
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// === INIT CHECK ===
if (DEBUG_MODE) {
    try {
        getDatabaseConnection();
        error_log(" Config loaded");
    } catch (Throwable $e) {
        error_log(" Config failed: " . $e->getMessage());
    }
}
