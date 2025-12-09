<?php
/**
 * Application Configuration
 * General Wingate Polytechnic College - Employee Management System
 */

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
ini_set('session.cookie_samesite', 'Strict');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Application constants
define('APP_NAME', 'GWPC Employee Management System');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost/hello-world');
define('SITE_URL', BASE_URL);

// Directory constants
define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');
define('MODEL_PATH', ROOT_PATH . '/models');
define('CONTROLLER_PATH', ROOT_PATH . '/controllers');
define('VIEW_PATH', ROOT_PATH . '/views');
define('ASSET_PATH', ROOT_PATH . '/assets');

// Timezone
date_default_timezone_set('UTC');

// Auto-loader for classes
spl_autoload_register(function ($class) {
    $paths = [
        MODEL_PATH . '/' . $class . '.php',
        CONTROLLER_PATH . '/' . $class . '.php'
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Include helper functions
require_once CONFIG_PATH . '/helpers.php';

// Security headers
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
?>
