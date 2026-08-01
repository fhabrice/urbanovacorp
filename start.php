<?php
/**
 * Version simplifiée pour démarrer sans composer
 * Charge manuellement les classes nécessaires
 */

// Define base path
define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('CONFIG_PATH', BASE_PATH . '/config');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Load language helper
require_once APP_PATH . '/Helpers/language.php';

// Load configuration
$config = require CONFIG_PATH . '/config.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_name($config['security']['session_name']);
    session_start();
}

// Set default language
if (!isset($_SESSION['language'])) {
    $_SESSION['language'] = $config['languages']['default'];
}

// Simple routing
$request_uri = $_SERVER['REQUEST_URI'];
$request_uri = strtok($request_uri, '?');

// Remove leading slash
$path = ltrim($request_uri, '/');

// Default route
if (empty($path) || $path === 'index.php') {
    $path = '/';
}

// Simple router
switch ($path) {
    case '/':
        // Home page
        require_once APP_PATH . '/Controllers/HomeController.php';
        $controller = new App\Controllers\HomeController();
        $controller->index();
        break;
        
    case 'about':
        require_once APP_PATH . '/Controllers/AboutController.php';
        $controller = new App\Controllers\AboutController();
        $controller->index();
        break;
        
    case 'contact':
        require_once APP_PATH . '/Controllers/ContactController.php';
        $controller = new App\Controllers\ContactController();
        $controller->index();
        break;
        
    case 'simple':
        require __DIR__ . '/simple.php';
        break;
        
    case 'test':
        require __DIR__ . '/test.php';
        break;
        
    default:
        // 404 error
        http_response_code(404);
        require APP_PATH . '/Views/errors/404.php';
        break;
}
