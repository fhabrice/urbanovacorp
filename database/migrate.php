<?php
/**
 * Database Migration Runner
 */

// Load configuration
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/Migration.php';

$config = require __DIR__ . '/../config/config.php';

// Create database connection
try {
    $db = new Database($config['database']);
    echo "Database connection established.\n";
} catch (Exception $e) {
    echo "Error connecting to database: " . $e->getMessage() . "\n";
    exit(1);
}

// Create migration instance
$migration = new Migration($db, __DIR__ . '/migrations');

// Check command line arguments
if (isset($argv[1])) {
    switch ($argv[1]) {
        case 'rollback':
            $steps = isset($argv[2]) ? (int)$argv[2] : 1;
            echo "Rolling back $steps migration(s)...\n";
            $migration->rollback($steps);
            echo "Rollback completed.\n";
            break;
        case 'reset':
            echo "Resetting all migrations...\n";
            // Rollback all then run all
            $migration->rollback(100);
            $migration->run();
            echo "Reset completed.\n";
            break;
        default:
            echo "Unknown command: " . $argv[1] . "\n";
            echo "Available commands: run, rollback [steps], reset\n";
            break;
    }
} else {
    // Run migrations by default
    echo "Running migrations...\n";
    try {
        $migration->run();
        echo "Migrations completed successfully.\n";
    } catch (Exception $e) {
        echo "Migration failed: " . $e->getMessage() . "\n";
        exit(1);
    }
}
