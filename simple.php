<?php
/**
 * Simple test version to check if the site structure is working
 */

echo "<h1>URBANOVA SOLUTIONS - Test</h1>";
echo "<p>Si vous voyez ceci, PHP fonctionne correctement.</p>";

// Check if directories exist
$dirs = [
    'app',
    'config', 
    'public',
    'database'
];

echo "<h2>Structure des dossiers:</h2>";
echo "<ul>";
foreach ($dirs as $dir) {
    $exists = is_dir(__DIR__ . '/' . $dir);
    echo "<li>$dir: " . ($exists ? '✓ Existe' : '✗ Manquant') . "</li>";
}
echo "</ul>";

// Check if key files exist
$files = [
    'index.php',
    'config/config.php',
    'app/Core/Application.php',
    'app/Controllers/HomeController.php'
];

echo "<h2>Fichiers clés:</h2>";
echo "<ul>";
foreach ($files as $file) {
    $exists = file_exists(__DIR__ . '/' . $file);
    echo "<li>$file: " . ($exists ? '✓ Existe' : '✗ Manquant') . "</li>";
}
echo "</ul>";

// Check PHP extensions
echo "<h2>Extensions PHP requises:</h2>";
echo "<ul>";
$required_extensions = ['pdo', 'pdo_mysql', 'mbstring', 'json', 'openssl'];
foreach ($required_extensions as $ext) {
    $loaded = extension_loaded($ext);
    echo "<li>$ext: " . ($loaded ? '✓ Chargé' : '✗ Manquant') . "</li>";
}
echo "</ul>";

echo "<p><a href='test.php'>Tester PHP phpinfo()</a></p>";
