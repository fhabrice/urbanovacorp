<?php
/**
 * URBANOVA SOLUTIONS - Main Entry Point
 * Plateforme Web Corporate & Investissement Immobilier
 */

// Define base path
define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('CONFIG_PATH', BASE_PATH . '/config');

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load configuration
if (file_exists(CONFIG_PATH . '/config.php')) {
    require_once CONFIG_PATH . '/config.php';
} else {
    die('Configuration file not found. Please create config/config.php');
}

// Load autoloader
if (file_exists(BASE_PATH . '/vendor/autoload.php')) {
    require_once BASE_PATH . '/vendor/autoload.php';
} else {
    // Simple autoloader if composer not installed
    spl_autoload_register(function ($class) {
        $prefix = 'App\\';
        $base_dir = APP_PATH . '/';
        
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }
        
        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        
        if (file_exists($file)) {
            require $file;
        }
    });
}

// Initialize application
try {
    require_once APP_PATH . '/Core/Application.php';
    $app = new App\Core\Application();
    $app->run();
} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
