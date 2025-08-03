<?php
/**
 * Enhanced Tsunami Warning System Configuration
 * 
 * @version 3.0.0
 * @license MIT
 * @package TsunamiWarningSystem
 */

declare(strict_types=1);

// ======================================================================
// Environment Initialization
// ======================================================================

// Register shutdown function for error handling
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        error_log("Fatal Error: " . print_r($error, true));
        if (!headers_sent()) {
            header('HTTP/1.1 500 Internal Server Error');
            exit('System temporarily unavailable. Please try again later.');
        }
    }
});

// Set default timezone
date_default_timezone_set('Asia/Jakarta');

// ======================================================================
// Environment Detection
// ======================================================================

define('IS_CLI', php_sapi_name() === 'cli');
define('IS_HTTPS', 
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
    (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ||
    (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
);

// ======================================================================
// Application Configuration
// ======================================================================

// Debug Mode (true for development, false for production)
define('DEBUG_MODE', true);

// Application Version
define('APP_VERSION', '2.2.0');

// ======================================================================
// Security Configuration
// ======================================================================

define('SESSION_TIMEOUT', 1800); // 30 minutes in seconds
define('CSRF_TOKEN_LIFE', 3600); // 1 hour in seconds
define('API_KEY', 'TSUNAMI_' . bin2hex(random_bytes(16)));
define('RECEIVER_API_KEY', 'arduino123'); // For Arduino/LoRa communication

// Security Headers
if (!IS_CLI && !headers_sent()) {
    header_remove('X-Powered-By');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Feature-Policy: accelerometer \'none\'; camera \'none\'; geolocation \'none\'; microphone \'none\'; payment \'none\'; usb \'none\'');
}

// ======================================================================
// Database Configuration
// ======================================================================

// Use environment variables if available, fallback to defaults
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'u855675680_mntrrinovajaya');
define('DB_PASS', getenv('DB_PASS') ?: 'Generazberbaktijaya123!');
define('DB_NAME', getenv('DB_NAME') ?: 'u855675680_mntrpekon');
define('DB_CHARSET', 'utf8mb4');
define('DB_TIMEOUT', 5); // Connection timeout in seconds

// ======================================================================
// Device Configuration
// ======================================================================

// IoT Device Configuration
define('ESP_IP', '10.63.234.35');
define('ESP_UPDATE_INTERVAL', 60); // Update interval in seconds
define('MAX_SENSOR_VALUE', 100);

// LoRa Configuration
define('RECEIVER_IP', '10.63.234.35'); // IP address of the LoRa receiver

// ======================================================================
// Threshold Configuration
// ======================================================================

// Threshold values for sensors (in micro-g)
define('VIBRATION_WARNING', 50000);  // Warning level vibration
define('VIBRATION_DANGER', 80000);   // Danger level vibration
define('ACCELERATION_WARNING', 50000); // Warning level acceleration (m/s²)
define('ACCELERATION_DANGER', 80000);  // Danger level acceleration (m/s²)

// ======================================================================
// Path Configuration
// ======================================================================

// Base URL Configuration
$host = IS_CLI 
    ? 'pekontelukkiluan.com' 
    : ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'pekontelukkiluan.com');

$protocol = IS_HTTPS ? 'https://' : 'http://';
$scriptPath = IS_CLI ? '/monitoring' : dirname($_SERVER['SCRIPT_NAME']);
$basePath = str_replace('/includes', '', $scriptPath);

define('BASE_URL', rtrim($protocol . $host . $basePath, '/'));
define('ASSETS_PATH', '/assets');
define('API_PATH', BASE_URL . '/api');
define('LOG_PATH', __DIR__ . '/../logs');

// ======================================================================
// Error Handling Configuration
// ======================================================================

error_reporting(DEBUG_MODE ? E_ALL : E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', DEBUG_MODE ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', LOG_PATH . '/error.log');

// ======================================================================
// Session Management
// ======================================================================

if (!IS_CLI && session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => SESSION_TIMEOUT,
        'path' => '/',
        'domain' => $host,
        'secure' => IS_HTTPS,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    
    session_name('TSUNAMI_SESSID');
    session_start();
    
    // Regenerate session ID periodically
    if (!isset($_SESSION['created'])) {
        $_SESSION['created'] = time();
    } elseif (time() - $_SESSION['created'] > 300) {
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
    
    // CSRF Protection
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

// ======================================================================
// Database Connection (Singleton Pattern)
// ======================================================================

function getDatabaseConnection(): PDO {
    static $db = null;
    
    if ($db === null) {
        try {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                DB_HOST,
                DB_NAME,
                DB_CHARSET
            );
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => DB_TIMEOUT,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '+07:00'"
            ];
            
            $db = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            if (!IS_CLI && !headers_sent()) {
                header('HTTP/1.1 503 Service Unavailable');
            }
            exit('Database connection failed. Please try again later.');
        }
    }
    
    return $db;
}

// ======================================================================
// Security Functions
// ======================================================================

/**
 * Sanitize input data to prevent XSS and other injections
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    
    $data = trim($data);
    $data = stripslashes($data);
    return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Validate sensor data structure and values
 */
function validateSensorData(array $data): bool {
    $required = [
        'vibration' => ['min' => 0, 'max' => MAX_SENSOR_VALUE],
        'mpu6050' => ['min' => 0, 'max' => MAX_SENSOR_VALUE],
        'pressure' => ['min' => 900, 'max' => 1100],
        'temperature' => ['min' => -10, 'max' => 50],
        'humidity' => ['min' => 0, 'max' => 100]
    ];
    
    foreach ($required as $field => $limits) {
        if (!isset($data[$field]) || 
            !is_numeric($data[$field]) || 
            $data[$field] < $limits['min'] || 
            $data[$field] > $limits['max']) {
            return false;
        }
    }
    
    return true;
}

// ======================================================================
// Utility Functions
// ======================================================================

/**
 * Redirect to specified URL with optional status code
 */
function redirect(string $url, int $statusCode = 303): void {
    if (!headers_sent()) {
        header("Location: $url", true, $statusCode);
        exit();
    }
    
    // Fallback for when headers are already sent
    echo "<script>window.location.href='$url';</script>";
    exit();
}

/**
 * Generate CSRF token HTML input
 */
function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}

/**
 * Verify CSRF token
 */
function verifyCsrfToken(?string $token = null): bool {
    $token = $token ?? $_POST['csrf_token'] ?? null;
    return hash_equals($_SESSION['csrf_token'], $token ?? '');
}

// ======================================================================
// Autoload Classes
// ======================================================================

spl_autoload_register(function ($className) {
    $file = __DIR__ . '/../classes/' . str_replace('\\', '/', $className) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// ======================================================================
// Logging System
// ======================================================================

function logEvent(string $message, string $level = 'INFO', array $context = []): void {
    $levels = ['DEBUG', 'INFO', 'WARNING', 'ERROR', 'CRITICAL'];
    $level = strtoupper($level);
    
    if (!in_array($level, $levels)) {
        $level = 'INFO';
    }
    
    $logEntry = sprintf(
        "[%s] %s: %s %s\n",
        date('Y-m-d H:i:s'),
        $level,
        $message,
        !empty($context) ? json_encode($context) : ''
    );
    
    file_put_contents(LOG_PATH . '/application.log', $logEntry, FILE_APPEND);
    
    if ($level === 'ERROR' || $level === 'CRITICAL') {
        error_log($logEntry);
    }
}

// ======================================================================
// System Health Check
// ======================================================================

function performSystemCheck(): array {
    $checks = [
        'database' => false,
        'log_directory' => false,
        'sensor_thresholds' => false
    ];
    
    try {
        // Database check
        $db = getDatabaseConnection();
        $db->query('SELECT 1');
        $checks['database'] = true;
    } catch (PDOException $e) {
        logEvent('Database connection failed', 'ERROR', ['error' => $e->getMessage()]);
    }
    
    // Log directory check
    if (is_writable(LOG_PATH)) {
        $checks['log_directory'] = true;
    } else {
        logEvent('Log directory is not writable', 'ERROR');
    }
    
    // Sensor thresholds check
    if (VIBRATION_WARNING < VIBRATION_DANGER && ACCELERATION_WARNING < ACCELERATION_DANGER) {
        $checks['sensor_thresholds'] = true;
    }
    
    return $checks;
}

// ======================================================================
// Initialization Checks
// ======================================================================

if (DEBUG_MODE) {
    $systemStatus = performSystemCheck();
    if (in_array(false, $systemStatus, true)) {
        logEvent('System initialization check failed', 'CRITICAL', $systemStatus);
    }
}