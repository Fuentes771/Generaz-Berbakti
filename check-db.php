<?php
/**
 * Database Connection Diagnostic Tool
 * Run this to troubleshoot database connection issues
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Database Connection Check</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f5f5f5; padding: 40px 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .card { background: white; border-radius: 12px; padding: 30px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h1 { color: #333; margin-bottom: 20px; font-size: 28px; }
        h2 { color: #555; margin: 20px 0 15px 0; font-size: 20px; border-bottom: 2px solid #e0e0e0; padding-bottom: 8px; }
        .status { padding: 12px 16px; border-radius: 6px; margin: 10px 0; display: flex; align-items: center; gap: 10px; }
        .status-ok { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .status-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .status-warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .icon { font-size: 20px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e0e0e0; }
        th { background: #f8f9fa; font-weight: 600; color: #555; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 4px; font-family: 'Courier New', monospace; color: #c7254e; }
        .info { background: #e7f3ff; padding: 15px; border-left: 4px solid #2196F3; margin: 15px 0; border-radius: 4px; }
        ol { margin: 15px 0; padding-left: 30px; }
        li { margin: 8px 0; line-height: 1.6; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 6px; margin-top: 15px; }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='card'>
            <h1>🔍 Database Connection Diagnostic</h1>
            <p style='color: #666; margin-bottom: 20px;'>Tsunami Warning System - Connection Test</p>";

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'u855675680_mntrpekon');

echo "<h2>📋 Configuration</h2>
            <table>
                <tr><th>Setting</th><th>Value</th></tr>
                <tr><td>Host</td><td><code>" . DB_HOST . "</code></td></tr>
                <tr><td>User</td><td><code>" . DB_USER . "</code></td></tr>
                <tr><td>Password</td><td><code>" . (DB_PASS === '' ? '(empty)' : '******') . "</code></td></tr>
                <tr><td>Database</td><td><code>" . DB_NAME . "</code></td></tr>
            </table>";

// Check if MySQL extension loaded
echo "<h2>🔧 PHP MySQL Extensions</h2>";
if (extension_loaded('pdo_mysql')) {
    echo "<div class='status status-ok'><span class='icon'>✓</span> PDO MySQL extension is loaded</div>";
} else {
    echo "<div class='status status-error'><span class='icon'>✗</span> PDO MySQL extension is NOT loaded</div>";
    echo "<div class='info'>⚠️ You need to enable <code>extension=pdo_mysql</code> in php.ini</div>";
}

if (extension_loaded('mysqli')) {
    echo "<div class='status status-ok'><span class='icon'>✓</span> MySQLi extension is loaded</div>";
} else {
    echo "<div class='status status-warning'><span class='icon'>!</span> MySQLi extension is NOT loaded</div>";
}

// Test connection
echo "<h2>🔌 Connection Test</h2>";

try {
    $dsn = "mysql:host=" . DB_HOST . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    echo "<div class='status status-ok'><span class='icon'>✓</span> Successfully connected to MySQL server</div>";
    
    // Check if database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE '" . DB_NAME . "'");
    $dbExists = $stmt->fetch();
    
    if ($dbExists) {
        echo "<div class='status status-ok'><span class='icon'>✓</span> Database '<code>" . DB_NAME . "</code>' exists</div>";
        
        // Connect to specific database
        $pdo->exec("USE " . DB_NAME);
        
        // Check tables
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "<h2>📊 Database Tables</h2>";
        if (count($tables) > 0) {
            echo "<table>";
            echo "<tr><th>#</th><th>Table Name</th><th>Row Count</th></tr>";
            foreach ($tables as $index => $table) {
                $countStmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
                $count = $countStmt->fetch()['count'];
                echo "<tr><td>" . ($index + 1) . "</td><td><code>$table</code></td><td>$count rows</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<div class='status status-warning'><span class='icon'>!</span> No tables found in database</div>";
        }
        
        // Test query
        echo "<h2>🧪 Test Query</h2>";
        try {
            $testStmt = $pdo->query("SELECT 1 as test");
            $result = $testStmt->fetch();
            echo "<div class='status status-ok'><span class='icon'>✓</span> Test query executed successfully</div>";
        } catch (PDOException $e) {
            echo "<div class='status status-error'><span class='icon'>✗</span> Test query failed: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
        
        echo "<div class='status status-ok' style='margin-top: 30px; font-size: 16px; font-weight: bold;'>
                <span class='icon'>🎉</span> ALL CHECKS PASSED - Database connection is working!
              </div>";
        
    } else {
        echo "<div class='status status-error'><span class='icon'>✗</span> Database '<code>" . DB_NAME . "</code>' does NOT exist</div>";
        echo "<div class='info'>
                <strong>How to fix:</strong>
                <ol>
                    <li>Open phpMyAdmin (usually at <code>http://localhost/phpmyadmin</code>)</li>
                    <li>Create a new database named: <code>" . DB_NAME . "</code></li>
                    <li>Import your SQL file if you have one</li>
                    <li>Refresh this page</li>
                </ol>
              </div>";
    }
    
} catch (PDOException $e) {
    echo "<div class='status status-error'><span class='icon'>✗</span> Connection FAILED</div>";
    echo "<div class='status status-error'><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</div>";
    
    echo "<h2>🔧 Troubleshooting Steps</h2>
          <div class='info'>
            <ol>
                <li><strong>Check if MySQL is running:</strong>
                    <ul style='margin-top: 8px;'>
                        <li>XAMPP: Open XAMPP Control Panel and start MySQL</li>
                        <li>Laragon: Open Laragon and start MySQL</li>
                    </ul>
                </li>
                <li><strong>Verify credentials:</strong>
                    <ul style='margin-top: 8px;'>
                        <li>Default XAMPP/Laragon username is usually <code>root</code></li>
                        <li>Default password is usually empty or <code>root</code></li>
                    </ul>
                </li>
                <li><strong>Check port:</strong> MySQL should be running on port 3306</li>
                <li><strong>Check php.ini:</strong> Make sure <code>extension=pdo_mysql</code> is enabled</li>
                <li><strong>Restart services:</strong> Try restarting Apache and MySQL</li>
            </ol>
          </div>";
}

echo "      <a href='monitoring.php' class='btn'>← Back to Monitoring</a>
        </div>
    </div>
</body>
</html>";
?>
