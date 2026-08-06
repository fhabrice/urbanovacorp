<?php
if (!defined('PUBLIC_PATH')) {
    define('PUBLIC_PATH', __DIR__ . '/public');
}
$cfg = include __DIR__ . '/config/config.php';
$dsn = 'mysql:host=' . $cfg['database']['host'] . ';port=' . $cfg['database']['port'] . ';dbname=' . $cfg['database']['database'] . ';charset=' . $cfg['database']['charset'];
$pdo = new PDO($dsn, $cfg['database']['username'], $cfg['database']['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$stmt = $pdo->query('SHOW CREATE TABLE investors');
$row = $stmt->fetch(PDO::FETCH_ASSOC);
print_r($row);

$stmt2 = $pdo->query('SHOW CREATE TABLE users');
$row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
print_r($row2);
