<?php
/**
 * Script de mise à jour de la base de données vers la version 2.0
 * Ajout des fonctionnalités KYC, Data Room et Workflow avancé
 */

echo "=== Mise à jour de la base de données Urbanova v2.0 ===\n\n";

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'urbanova_db';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Connexion à la base de données réussie\n\n";
    
    // Lire le fichier SQL
    $sqlFile = __DIR__ . '/database_upgrade_v2.sql';
    if (!file_exists($sqlFile)) {
        die("❌ Erreur: Le fichier database_upgrade_v2.sql n'existe pas\n");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Diviser en requêtes individuelles
    $queries = explode(';', $sql);
    
    $executed = 0;
    $failed = 0;
    
    foreach ($queries as $query) {
        $query = trim($query);
        if (empty($query) || strpos($query, '--') === 0) {
            continue;
        }
        
        try {
            $conn->exec($query);
            $executed++;
            echo "✓ Requête exécutée: " . substr($query, 0, 50) . "...\n";
        } catch (PDOException $e) {
            // Certaines erreurs sont acceptées (ex: colonne déjà existe)
            if (strpos($e->getMessage(), 'Duplicate column') !== false || 
                strpos($e->getMessage(), 'already exists') !== false) {
                echo "⚠ Déjà existant: " . substr($query, 0, 50) . "...\n";
            } else {
                $failed++;
                echo "❌ Erreur: " . $e->getMessage() . "\n";
                echo "   Requête: " . substr($query, 0, 100) . "...\n";
            }
        }
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "RÉSUMÉ:\n";
    echo str_repeat("=", 50) . "\n";
    echo "Requêtes exécutées: $executed\n";
    echo "Requêtes échouées: $failed\n";
    
    if ($failed === 0) {
        echo "\n✅ Mise à jour terminée avec succès!\n";
    } else {
        echo "\n⚠ Mise à jour terminée avec des erreurs\n";
    }
    
    // Vérifier les dossiers d'upload
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "VÉRIFICATION DES DOSSIERS D'UPLOAD:\n";
    echo str_repeat("=", 50) . "\n";
    
    $uploadDirs = [
        'uploads',
        'uploads/kyc',
        'uploads/projects',
        'uploads/data_room'
    ];
    
    foreach ($uploadDirs as $dir) {
        if (!is_dir(__DIR__ . '/' . $dir)) {
            if (mkdir(__DIR__ . '/' . $dir, 0755, true)) {
                echo "✓ Dossier créé: $dir\n";
            } else {
                echo "❌ Impossible de créer: $dir\n";
            }
        } else {
            echo "✓ Dossier existe: $dir\n";
        }
    }
    
} catch (PDOException $e) {
    die("❌ Erreur de connexion: " . $e->getMessage() . "\n");
}
