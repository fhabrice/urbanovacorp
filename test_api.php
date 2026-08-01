<?php
/**
 * Test API endpoints
 */

// Define base path
define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');

// Simple autoloader
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

// Start session
session_start();

// Load configuration
$config = require CONFIG_PATH . '/config.php';

echo "<h1>Test API URBANOVA</h1>";
echo "<p>Test des endpoints API</p>";

// Test 1: Get Projects
echo "<h2>Test 1: GET /api/projects</h2>";
try {
    $_GET['route'] = 'api/projects';
    require_once APP_PATH . '/Controllers/MainController.php';
    $controller = new App\Controllers\MainController();
    // This will output JSON directly
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur: " . $e->getMessage() . "</p>";
}

echo "<hr>";

// Test 2: Check Auth
echo "<h2>Test 2: GET /api/check-auth</h2>";
try {
    $_GET['route'] = 'api/check-auth';
    $controller = new App\Controllers\MainController();
    $controller->apiCheckAuth();
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='/urbanovacorp/'>Retour au site</a></p>";
