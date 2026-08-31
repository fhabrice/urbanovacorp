<?php
/**
 * URBANOVA SOLUTIONS - Point d'entrée de l'API AJAX
 *
 * Toutes les requêtes AJAX du site (marketplace, espace admin, authentification,
 * soumission de projets, investissements...) sont traitées ici :
 *   api.php?action=<nom-de-l-action>
 */

// ---------------------------------------------------------------
// Chemins requis par la configuration et les contrôleurs
// ---------------------------------------------------------------
if (!defined('BASE_PATH')) {
    define('BASE_PATH', realpath(__DIR__));
}
if (!defined('APP_PATH')) {
    define('APP_PATH', BASE_PATH . '/app');
}
if (!defined('PUBLIC_PATH')) {
    define('PUBLIC_PATH', BASE_PATH . '/public');
}
if (!defined('CONFIG_PATH')) {
    define('CONFIG_PATH', BASE_PATH . '/config');
}

// ---------------------------------------------------------------
// Autoloader (Composer si disponible, sinon autoloader PSR-4 simple)
// ---------------------------------------------------------------
$composerAutoload = BASE_PATH . '/vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
} else {
    spl_autoload_register(function ($class) {
        $prefix = 'App\\';
        if (strpos($class, $prefix) !== 0) {
            return;
        }
        $relative = substr($class, strlen($prefix));
        $file = APP_PATH . '/' . str_replace('\\', '/', $relative) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    });
}

// ---------------------------------------------------------------
// Session (utilisée par l'authentification et les espaces privés)
// ---------------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    $appConfig = require CONFIG_PATH . '/config.php';
    $sessionName = $appConfig['security']['session_name'] ?? 'urbanova_session';
    session_name($sessionName);
    session_start();
}

// ---------------------------------------------------------------
// Instance du contrôleur API
// ---------------------------------------------------------------
$api = new \App\Controllers\ApiController();

// ---------------------------------------------------------------
// Table de correspondance action -> méthode
// ---------------------------------------------------------------
$actionMap = [
    // Authentification
    'check-auth'      => 'checkAuth',
    'login'           => 'login',
    'register'        => 'register',
    'logout'          => 'logout',

    // Projets / Marketplace
    'get-projects'          => 'getProjects',
    'projects'              => 'getProjects',
    'get-project-details'   => 'getProjectDetails',
    'get-promoter-projects' => 'getPromoterProjects',
    'submit-project'        => 'submitProject',
    'create-project'        => 'createProject',
    'update-project'        => 'updateProject',
    'delete-project'        => 'deleteProject',
    'approve-project'       => 'approveProject',
    'update-coordinates'    => 'updateProjectCoordinates',
    'upload-project-image'  => 'uploadProjectImage',

    // Investisseurs
    'investor-data'     => 'getInvestorData',
    'invest-project'    => 'investInProject',
    'approve-investor'  => 'approveInvestorUser',
    'reject-investor'   => 'rejectInvestorUser',

    // Administration - utilisateurs
    'get-users'                => 'getUsers',
    'update-user-status'       => 'updateUserStatus',
    'delete-user'              => 'deleteUser',

    // Administration - investissements
    'get-investments'              => 'getAllInvestments',
    'update-investment-status'     => 'updateInvestmentStatus',
    'delete-investment'            => 'deleteInvestment',

    // Divers
    'submit-contact'   => 'submitContact',
    'make-reservation' => 'makeReservation',
    'request-visit'    => 'requestVisit',
];

// ---------------------------------------------------------------
// Actions réservées à l'administration (protégées par mot de passe)
// ---------------------------------------------------------------
$appConfig = require CONFIG_PATH . '/config.php';
$adminPassword = $appConfig['security']['admin_password'] ?? 'urbanova';

$adminActions = [
    'get-users',
    'update-user-status',
    'delete-user',
    'approve-investor',
    'reject-investor',
    'get-investments',
    'update-investment-status',
    'delete-investment',
    'approve-project',
    'delete-project',
];

// ---------------------------------------------------------------
// Dispatch
// ---------------------------------------------------------------
$action = $_GET['action'] ?? '';

if ($action === '' || !isset($actionMap[$action])) {
    http_response_code(404);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'message' => 'Action API inconnue',
        'action'  => $action,
    ]);
    exit;
}

// Vérification du mot de passe administrateur pour les actions sensibles
if (in_array($action, $adminActions, true)) {
    $submittedPassword = $_SERVER['HTTP_X_ADMIN_PASSWORD']
        ?? $_GET['admin_password']
        ?? $_POST['admin_password']
        ?? null;

    if ($submittedPassword === null || !hash_equals((string)$adminPassword, (string)$submittedPassword)) {
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'message' => 'Mot de passe administrateur invalide',
            'action'  => $action,
        ]);
        exit;
    }
}

$method = $actionMap[$action];

if (!method_exists($api, $method)) {
    http_response_code(404);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'message' => 'Méthode API introuvable',
        'action'  => $action,
    ]);
    exit;
}

$api->$method();
