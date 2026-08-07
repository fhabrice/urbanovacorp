<?php
// Simple migration runner for SQL files in database/migrations
// Usage: php database/run_migrations.php

if (!defined('PUBLIC_PATH')) {
    define('PUBLIC_PATH', realpath(__DIR__ . '/../public'));
}
if (!defined('BASE_PATH')) {
    define('BASE_PATH', realpath(__DIR__ . '/..'));
}

$config = require __DIR__ . '/../config/config.php';
$dbConf = $config['database'];

$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $dbConf['host'], $dbConf['port'], $dbConf['database'], $dbConf['charset']);
try {
    $pdo = new PDO($dsn, $dbConf['username'] ?? $dbConf['user'], $dbConf['password'] ?? null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Exception $e) {
    echo "DB connection failed: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

// ensure migrations table
$pdo->exec("CREATE TABLE IF NOT EXISTS migrations (id INT AUTO_INCREMENT PRIMARY KEY, migration VARCHAR(255) NOT NULL, ran_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, UNIQUE KEY unique_migration (migration)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$files = glob(__DIR__ . '/migrations/*.sql');
sort($files);

foreach ($files as $file) {
    $filename = basename($file);
    $stmt = $pdo->prepare('SELECT COUNT(*) as c FROM migrations WHERE migration = ?');
    $stmt->execute([$filename]);
    $row = $stmt->fetch();
    if ($row && $row['c'] > 0) {
        echo "Skipping already applied: $filename" . PHP_EOL;
        continue;
    }

    echo "Applying: $filename" . PHP_EOL;
    $sql = file_get_contents($file);
    try {
        $pdo->beginTransaction();
        $pdo->exec($sql);
        $stmt = $pdo->prepare('INSERT INTO migrations (migration) VALUES (?)');
        $stmt->execute([$filename]);
        $pdo->commit();
        echo "Applied: $filename" . PHP_EOL;
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Failed to apply $filename: " . $e->getMessage() . PHP_EOL;
        exit(1);
    }
}

echo "Migrations complete." . PHP_EOL;
