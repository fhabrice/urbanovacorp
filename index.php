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

// Load configuration
require_once CONFIG_PATH . '/config.php';

// Load autoloader
require_once BASE_PATH . '/vendor/autoload.php';

// Initialize application
require_once APP_PATH . '/Core/Application.php';

// Run application
$app = new App\Core\Application();
$app->run();
