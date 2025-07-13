<?php
/**
 * Enhanced Tsunami Warning System Configuration
 * 
 * @version 2.0.0
 * @license MIT
 */

// Debug Mode (true for development, false for production)
define('DEBUG_MODE', true);

// Application Version
define('APP_VERSION', '2.0.0');

// Security Configuration
define('SESSION_TIMEOUT', 1800); // 30 minutes in seconds
define('CSRF_TOKEN_LIFE', 3600); // 1 hour in seconds
define('API_KEY', 'TSUNAMI_' . bin2hex(random_bytes(16)));

// Database Configuration - Use environment variables if available
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'tsunami_monitoring');
define('DB_CHARSET', 'utf8mb4');

// IoT Device Configuration
define('ESP_IP', '192.168.241.203');
define('ESP_UPDATE_INTERVAL', 60); // Update interval in seconds
define('MAX_SENSOR_VALUE', 100);

// Path Constants
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
define('BASE_URL', $protocol . $_SERVER['HTTP_HOST'] . str_replace('/includes', '', dirname($_SERVER['SCRIPT_NAME'])));
define('ASSETS_PATH', BASE_URL . '/assets');
define('API_PATH', BASE_URL . '/api');

// Error Reporting
error_reporting(DEBUG_MODE ? E_ALL : E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', DEBUG_MODE ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Security Functions
function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function validate_sensor_data(array $data): bool {
    $required = ['vibration', 'mpu6050', 'pressure', 'temperature', 'humidity'];
    foreach ($required as $field) {
        if (!isset($data[$field])) {
            return false;
        }
        if (!is_numeric($data[$field])) {
            return false;
        }
    }
    return true;
}

// Custom Error Handler
function handle_error($error_level, $error_message, $error_file, $error_line) {
    $error_types = [
        E_ERROR => 'Error',
        E_WARNING => 'Warning',
        E_PARSE => 'Parse Error',
        E_NOTICE => 'Notice',
        E_CORE_ERROR => 'Core Error',
        E_CORE_WARNING => 'Core Warning',
        E_COMPILE_ERROR => 'Compile Error',
        E_COMPILE_WARNING => 'Compile Warning',
        E_USER_ERROR => 'User Error',
        E_USER_WARNING => 'User Warning',
        E_USER_NOTICE => 'User Notice',
        E_STRICT => 'Strict Standards',
        E_RECOVERABLE_ERROR => 'Recoverable Error',
        E_DEPRECATED => 'Deprecated',
        E_USER_DEPRECATED => 'User Deprecated'
    ];

    $level_name = $error_types[$error_level] ?? 'Unknown Error';

    $log_message = sprintf(
        "[%s] %s in %s on line %d",
        $level_name,
        $error_message,
        $error_file,
        $error_line
    );
    
    error_log(date('[Y-m-d H:i:s] ') . $log_message);

    if (DEBUG_MODE) {
        echo "<div class='alert alert-danger'>";
        echo "<strong>{$level_name}:</strong> {$error_message}<br>";
        echo "<small>in {$error_file} on line {$error_line}</small>";
        echo "</div>";
    }

    if ($error_level & (E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR)) {
        if (!DEBUG_MODE) {
            header('HTTP/1.1 500 Internal Server Error');
            die('A system error occurred. Please try again later.');
        }
        return false;
    }

    return true;
}

set_error_handler('handle_error');

// Database Connection Function
function get_db_connection() {
    static $db = null;
    
    if ($db === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => true
            ];
            
            $db = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            header('HTTP/1.1 500 Internal Server Error');
            die('Database connection error');
        }
    }
    
    return $db;
}

// Session Configuration
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

// Auto-load Classes
spl_autoload_register(function ($class_name) {
    $file = __DIR__ . '/../classes/' . str_replace('\\', '/', $class_name) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});