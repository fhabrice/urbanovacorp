<?php
/**
 * Script de pré-déploiement
 * Vérifie que tout est prêt pour le déploiement en production
 */

echo "=== Vérification de pré-déploiement URBANOVA SOLUTIONS ===\n\n";

$errors = [];
$warnings = [];

// 1. Vérifier PHP version
$phpVersion = PHP_VERSION;
echo "1. Version PHP: $phpVersion\n";
if (version_compare($phpVersion, '7.4.0', '<')) {
    $errors[] = "PHP 7.4 ou supérieur requis (actuel: $phpVersion)";
} else {
    echo "   ✓ Version PHP OK\n";
}

// 2. Vérifier les extensions PHP
$requiredExtensions = ['pdo', 'pdo_mysql', 'mysqli', 'json', 'mbstring'];
echo "\n2. Extensions PHP requises:\n";
foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        echo "   ✓ $ext\n";
    } else {
        $errors[] = "Extension PHP manquante: $ext";
        echo "   ✗ $ext\n";
    }
}

// 3. Vérifier les fichiers essentiels
$requiredFiles = [
    'index.html',
    'api.php',
    'index.php',
    'config/config.php',
    'app/Controllers/ApiController.php',
    'app/Core/Database.php'
];

echo "\n3. Fichiers essentiels:\n";
foreach ($requiredFiles as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "   ✓ $file\n";
    } else {
        $errors[] = "Fichier manquant: $file";
        echo "   ✗ $file\n";
    }
}

// 4. Vérifier le fichier .env
echo "\n4. Configuration:\n";
if (file_exists(__DIR__ . '/.env')) {
    echo "   ✓ Fichier .env présent\n";
} else {
    $warnings[] = "Fichier .env manquant - utilisez .env.production comme modèle";
    echo "   ⚠ Fichier .env manquant\n";
}

// 5. Vérifier les permissions
echo "\n5. Permissions des dossiers:\n";
$writableDirs = ['app', 'config'];
foreach ($writableDirs as $dir) {
    if (is_writable(__DIR__ . '/' . $dir)) {
        echo "   ✓ $dir est accessible en écriture\n";
    } else {
        $warnings[] = "Dossier $dir n'est pas accessible en écriture";
        echo "   ⚠ $dir n'est pas accessible en écriture\n";
    }
}

// 6. Vérifier le code admin
echo "\n6. Sécurité:\n";
$indexHtml = file_get_contents(__DIR__ . '/index.html');
if (strpos($indexHtml, "const ADMIN_CODE = '1234567'") !== false) {
    $warnings[] = "Code admin par défaut (1234567) - changez-le avant le déploiement!";
    echo "   ⚠ Code admin par défaut détecté - CHANGEZ-LE!\n";
} else {
    echo "   ✓ Code admin personnalisé\n";
}

// 7. Vérifier le mode debug
if (strpos($indexHtml, 'console.log') !== false) {
    $warnings[] = "console.log détecté dans index.html - retirez-les en production";
    echo "   ⚠ console.log détecté - retirez-les en production\n";
} else {
    echo "   ✓ Pas de console.log détecté\n";
}

// Résumé
echo "\n" . str_repeat("=", 50) . "\n";
echo "RÉSUMÉ:\n";
echo str_repeat("=", 50) . "\n";

if (empty($errors) && empty($warnings)) {
    echo "✓ Tout est prêt pour le déploiement!\n";
    exit(0);
} elseif (!empty($errors)) {
    echo "ERREURS (" . count($errors) . "):\n";
    foreach ($errors as $error) {
        echo "  ✗ $error\n";
    }
}

if (!empty($warnings)) {
    echo "\nAVERTISSEMENTS (" . count($warnings) . "):\n";
    foreach ($warnings as $warning) {
        echo "  ⚠ $warning\n";
    }
}

if (!empty($errors)) {
    echo "\n✗ Corrigez les erreurs avant le déploiement\n";
    exit(1);
} else {
    echo "\n⚠ Vérifiez les avertissements avant le déploiement\n";
    exit(0);
}
