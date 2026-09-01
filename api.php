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

    // ---- Module 1 : Marketplace - demandes d'information ----
    'submit-project-inquiry'         => 'submitProjectInquiry',
    'get-project-inquiries'          => 'getProjectInquiries',
    'update-project-inquiry-status'  => 'updateProjectInquiryStatus',

    // ---- Module 2 : Levée de fonds - mandats & campagnes ----
    'submit-funding-request'   => 'submitFundingRequest',
    'get-my-funding-requests'  => 'getMyFundingRequests',
    'get-funding-requests'     => 'getFundingRequests',
    'review-funding-request'   => 'reviewFundingRequest',
    'get-campaigns'            => 'getCampaigns',
    'create-campaign'          => 'createCampaign',
    'update-campaign'          => 'updateCampaign',
    'get-funding-dashboard'    => 'getFundingDashboard',

    // ---- Module 2 : offres d'investissement ----
    'submit-investment-offer'   => 'submitInvestmentOffer',
    'get-my-investment-offers'  => 'getMyInvestmentOffers',
    'get-investment-offers'     => 'getInvestmentOffers',
    'review-investment-offer'   => 'reviewInvestmentOffer',
    'express-funding-interest'  => 'expressFundingInterest',

    // ---- Module 3 : Data Room & audit ----
    'request-data-room-access'  => 'requestDataRoomAccess',
    'get-my-data-room-requests' => 'getMyDataRoomRequests',
    'get-data-room-requests'    => 'getDataRoomRequests',
    'review-data-room-access'   => 'reviewDataRoomAccess',
    'get-project-documents'     => 'getProjectDocuments',
    'log-document-access'       => 'logDocumentAccess',
    'get-data-room-audit'       => 'getDataRoomAudit',

    // ---- Admin : statistiques, réservations, visites ----
    'get-admin-stats'            => 'getAdminStats',
    'get-reservations'           => 'getReservations',
    'update-reservation-status'  => 'updateReservationStatus',
    'get-visits'                 => 'getVisits',
    'update-visit-status'        => 'updateVisitStatus',

    // ---- Notifications & messagerie ----
    'get-my-notifications'    => 'getMyNotifications',
    'mark-notifications-read' => 'markNotificationsRead',
    'get-investor-messages'   => 'getInvestorMessages',
    'send-investor-message'   => 'sendInvestorMessage',
    'reply-investor-message'  => 'replyInvestorMessage',
];

// ---------------------------------------------------------------
// Actions réservées à l'administration (protégées par mot de passe)
// ---------------------------------------------------------------
$appConfig = require CONFIG_PATH . '/config.php';
$adminPassword = $appConfig['security']['admin_password'] ?? 'urbanova';

$adminActions = [
    // Utilisateurs / investisseurs
    'get-users',
    'update-user-status',
    'delete-user',
    'approve-investor',
    'reject-investor',
    // Investissements & projets
    'get-investments',
    'update-investment-status',
    'delete-investment',
    'approve-project',
    'delete-project',
    'create-project',
    'update-project',
    // Module 1 - demandes d'information
    'get-project-inquiries',
    'update-project-inquiry-status',
    // Module 2 - mandats, campagnes, offres
    'get-funding-requests',
    'review-funding-request',
    'create-campaign',
    'update-campaign',
    'get-funding-dashboard',
    'get-investment-offers',
    'review-investment-offer',
    // Module 3 - Data Room
    'get-data-room-requests',
    'review-data-room-access',
    'get-data-room-audit',
    // Admin - statistiques, réservations, visites
    'get-admin-stats',
    'get-reservations',
    'update-reservation-status',
    'get-visits',
    'update-visit-status',
    // Messagerie admin (réponses)
    'reply-investor-message',
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
