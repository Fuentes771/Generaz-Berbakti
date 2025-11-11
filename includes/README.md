# Includes - System Configuration

Clean, professional configuration and template files.

## Files

### config.php
**Main system configuration** - Database, security, thresholds, utilities

**Features:**
- ✅ Database connection (singleton pattern)
- ✅ Security headers (CSP, XSS protection)
- ✅ Sensor status computation
- ✅ Session management with CSRF protection
- ✅ Environment variable support
- ✅ Clean logging system

**Key Functions:**
```php
getDatabaseConnection()  // Get PDO database instance
computeStatus($vib, $mpu)  // Calculate sensor status [status, class]
sanitizeInput($data)  // Clean user input
redirect($url, $code)  // Redirect with status code
logEvent($msg, $level)  // Log to file
verifyCsrf($token)  // Verify CSRF token
```

**Configuration via Environment:**
- Set `DEBUG_MODE=true` for development
- Configure database: `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`
- Set API keys: `API_KEY`, `RECEIVER_API_KEY`
- Override thresholds: `THRESHOLD_VIB_WARN`, etc.

### navbar.php
Navigation bar template (included in all pages)

### footer.php
Footer template (included in all pages)

## Usage

Include config in your PHP files:
```php
<?php
require_once __DIR__ . '/includes/config.php';

// Use database
$db = getDatabaseConnection();
$stmt = $db->query("SELECT * FROM sensor_data LIMIT 10");

// Check sensor status
list($status, $class) = computeStatus(60000, 45000);
echo "<span class='$class'>$status</span>";
```

## Environment Setup

1. Copy `.env.example` to `.env`
2. Update database credentials
3. Change API keys for production
4. Set `DEBUG_MODE=false` for production

## Security Notes

⚠️ **IMPORTANT:**
- Change `RECEIVER_API_KEY` before deploying to production
- Use strong, random API keys
- Enable HTTPS in production (updates `secure` cookie flag automatically)
- Review CSP headers in `config.php` to match your CDN sources

## Logging

Logs are stored in `logs/app.log`. Use `logEvent()`:
```php
logEvent('User login successful', 'INFO');
logEvent('Database query failed', 'ERROR');
```

## Maintenance

- File is ~130 lines (reduced from 400+)
- Clear section organization
- Minimal comments, maximum clarity
- All functions documented inline
