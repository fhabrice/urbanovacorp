<?php
/**
 * URBANOVA SOLUTIONS - Main Entry Point (Simplified)
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

// Simple routing based on query parameter or path
$route = $_GET['route'] ?? '/';
$route = ltrim($route, '/');

// If no route specified, use the path from REQUEST_URI
if ($route === '/') {
    $request_uri = $_SERVER['REQUEST_URI'];
    $request_uri = strtok($request_uri, '?');
    $path = ltrim($request_uri, '/');
    
    // Remove directory name if present
    $path = preg_replace('#^[^/]+/#', '', $path);
    $path = preg_replace('#^[^/]+$#', '', $path);
    
    if (!empty($path)) {
        $route = $path;
    }
}

// Default to home if empty
if (empty($route)) {
    $route = '/';
}

// Simple router
try {
    switch ($route) {
        case '/':
        case '':
        case 'home':
            // Main page with new interface
            require_once APP_PATH . '/Controllers/MainController.php';
            $controller = new App\Controllers\MainController();
            $controller->index();
            break;
            
        // API Endpoints
        case 'api/projects':
            require_once APP_PATH . '/Controllers/MainController.php';
            $controller = new App\Controllers\MainController();
            $controller->apiProjects();
            break;
            
        case 'api/submit-project':
            require_once APP_PATH . '/Controllers/MainController.php';
            $controller = new App\Controllers\MainController();
            $controller->apiSubmitProject();
            break;
            
        case 'api/submit-contact':
            require_once APP_PATH . '/Controllers/MainController.php';
            $controller = new App\Controllers\MainController();
            $controller->apiSubmitContact();
            break;
            
        case 'api/check-auth':
            require_once APP_PATH . '/Controllers/MainController.php';
            $controller = new App\Controllers\MainController();
            $controller->apiCheckAuth();
            break;
            
        case 'api/investor-data':
            require_once APP_PATH . '/Controllers/MainController.php';
            $controller = new App\Controllers\MainController();
            $controller->apiInvestorData();
            break;
            
        // Legacy routes (keep for compatibility)
        case 'about':
            require_once APP_PATH . '/Controllers/AboutController.php';
            $controller = new App\Controllers\AboutController();
            $controller->index();
            break;
            
        case 'governance':
            require_once APP_PATH . '/Controllers/AboutController.php';
            $controller = new App\Controllers\AboutController();
            $controller->governance();
            break;
            
        case 'services':
            require_once APP_PATH . '/Controllers/AboutController.php';
            $controller = new App\Controllers\AboutController();
            $controller->services();
            break;
            
        case 'contact':
            require_once APP_PATH . '/Controllers/ContactController.php';
            $controller = new App\Controllers\ContactController();
            $controller->index();
            break;
            
        case 'lang':
            // Language switch
            $lang = $_GET['lang'] ?? 'fr';
            if (in_array($lang, ['fr', 'en'])) {
                $_SESSION['language'] = $lang;
            }
            header('Location: /urbanovacorp/?route=/');
            exit;
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
} catch (Exception $e) {
    // 500 error
    http_response_code(500);
    echo "<h1>Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
