<?php
/**
 * API Endpoint Direct - Version simplifiée pour contourner le problème de routage
 */

// Définir les constantes de base
define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('CONFIG_PATH', BASE_PATH . '/config');

// Démarrer la session
session_start();

// Charger les dépendances
require_once CONFIG_PATH . '/config.php';

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

// Déterminer l'action basée sur le paramètre action
$action = $_GET['action'] ?? '';

header('Content-Type: application/json');

try {
    switch ($action) {
        case 'login':
            require_once APP_PATH . '/Controllers/ApiController.php';
            $api = new App\Controllers\ApiController();
            $api->login();
            break;
            
        case 'register':
            require_once APP_PATH . '/Controllers/ApiController.php';
            $api = new App\Controllers\ApiController();
            $api->register();
            break;
            
        case 'logout':
            require_once APP_PATH . '/Controllers/ApiController.php';
            $api = new App\Controllers\ApiController();
            $api->logout();
            break;
            
        case 'check-auth':
            require_once APP_PATH . '/Controllers/ApiController.php';
            $api = new App\Controllers\ApiController();
            $api->checkAuth();
            break;
            
        case 'projects':
            require_once APP_PATH . '/Controllers/ApiController.php';
            $api = new App\Controllers\ApiController();
            $api->getProjects();
            break;
            
        case 'get-projects':
            require_once APP_PATH . '/Controllers/ApiController.php';
            $api = new App\Controllers\ApiController();
            $api->getProjects();
            break;
            
        case 'get-promoter-projects':
            require_once APP_PATH . '/Controllers/ApiController.php';
            $api = new App\Controllers\ApiController();
            $api->getPromoterProjects();
            break;
            
        case 'submit-project':
            require_once APP_PATH . '/Controllers/ApiController.php';
            $api = new App\Controllers\ApiController();
            $api->submitProject();
            break;
            
        case 'investor-data':
            require_once APP_PATH . '/Controllers/ApiController.php';
            $api = new App\Controllers\ApiController();
            $api->getInvestorData();
            break;
            
        case 'approve-project':
            require_once APP_PATH . '/Controllers/ApiController.php';
            $api = new App\Controllers\ApiController();
            $api->approveProject();
            break;
            
        case 'invest-project':
            require_once APP_PATH . '/Controllers/ApiController.php';
            $api = new App\Controllers\ApiController();
            $api->investInProject();
            break;
            
        case 'upload-project-image':
            require_once APP_PATH . '/Controllers/ApiController.php';
            $api = new App\Controllers\ApiController();
            $api->uploadProjectImage();
            break;
            
        case 'request-visit':
            require_once APP_PATH . '/Controllers/ApiController.php';
            $api = new App\Controllers\ApiController();
            $api->requestVisit();
            break;
            
        case 'make-reservation':
            require_once APP_PATH . '/Controllers/ApiController.php';
            $api = new App\Controllers\ApiController();
            $api->makeReservation();
            break;
            
        case 'update-project-coordinates':
            require_once APP_PATH . '/Controllers/ApiController.php';
            $api = new App\Controllers\ApiController();
            $api->updateProjectCoordinates();
            break;
            
        case 'create-project':
            require_once APP_PATH . '/Controllers/ApiController.php';
            $api = new App\Controllers\ApiController();
            $api->createProject();
            break;
            
        case 'update-project':
            require_once APP_PATH . '/Controllers/ApiController.php';
            $api = new App\Controllers\ApiController();
            $api->updateProject();
            break;
            
        case 'delete-project':
            require_once APP_PATH . '/Controllers/ApiController.php';
            $api = new App\Controllers\ApiController();
            $api->deleteProject();
            break;
            
        case 'get-project-details':
            require_once APP_PATH . '/Controllers/ApiController.php';
            $api = new App\Controllers\ApiController();
            $api->getProjectDetails();
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}
