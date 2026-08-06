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
    // Simplified session configuration
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
    error_log('Session started: ' . session_id());
}

// Set default language
if (!isset($_SESSION['language'])) {
    $_SESSION['language'] = $config['languages']['default'];
}

// Simple routing based on query parameter or path
$route = $_GET['route'] ?? '/';

// Debug logging
error_log('Requested route: ' . $route);
error_log('REQUEST_URI: ' . ($_SERVER['REQUEST_URI'] ?? 'not set'));
error_log('GET params: ' . json_encode($_GET));

// Remove leading/trailing slashes
$route = trim($route, '/');

// Default to home if empty
if (empty($route)) {
    $route = 'home';
}

// Simple router
try {
    error_log('Processing route: ' . $route);
    
    switch ($route) {
        case 'home':
        case '/':
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
            
        case 'api/register':
            require_once APP_PATH . '/Controllers/MainController.php';
            $controller = new App\Controllers\MainController();
            $controller->apiRegister();
            break;
            
        case 'api/login':
            require_once APP_PATH . '/Controllers/MainController.php';
            $controller = new App\Controllers\MainController();
            $controller->apiLogin();
            break;
            
        case 'api/logout':
            require_once APP_PATH . '/Controllers/MainController.php';
            $controller = new App\Controllers\MainController();
            $controller->apiLogout();
            break;
            
        case 'api/approve-project':
            require_once APP_PATH . '/Controllers/MainController.php';
            $controller = new App\Controllers\MainController();
            $controller->apiApproveProject();
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
        
        case 'news':
            require_once APP_PATH . '/Controllers/NewsController.php';
            $controller = new App\Controllers\NewsController();
            $controller->index();
            break;

        case 'lang':
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
            if (str_starts_with($route, 'news/show-')) {
                require_once APP_PATH . '/Controllers/NewsController.php';
                $controller = new App\Controllers\NewsController();
                $slug = substr($route, strlen('news/show-'));
                $controller->show($slug);
                break;
            }

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
