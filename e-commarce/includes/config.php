<?php
// Start session before any output - improved session handling
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Site configuration
define('SITE_NAME', 'E-Commerce Store');
define('SITE_URL', 'http://0.0.0.0:8000');

// Database configuration
define('DB_TYPE', 'sqlite');
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ecommerce_db');

// Directories
define('ROOT_DIR', dirname(__DIR__));
define('INCLUDES_DIR', ROOT_DIR . '/includes');
define('UPLOADS_DIR', ROOT_DIR . '/public/assets/uploads');
define('UPLOADS_URL', SITE_URL . '/assets/uploads');

// Maximum size for file uploads (in bytes)
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Allowed file types for uploads
define('ALLOWED_FILE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

// Session timeout (in seconds)
define('SESSION_TIMEOUT', 1800); // 30 minutes

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>