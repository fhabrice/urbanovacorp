<?php
/**
 * Load Demo Data Script for URBANOVA SOLUTIONS
 * OPTIONAL: Run this script to load demonstration data
 * ONLY use this for testing/development purposes
 */

echo "<h1>Charger les Données de Démonstration</h1>";
echo "<p style='color: orange;'><strong>Attention:</strong> Ces données sont uniquement pour le développement et les tests.</p>";

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'urbanova_db';

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p>✓ Connecté à la base de données</p>";
    
    // Read and execute demo data
    $demoFile = __DIR__ . '/database/demo_data.sql';
    if (file_exists($demoFile)) {
        $demo = file_get_contents($demoFile);
        
        // Execute demo statements
        $statements = explode(';', $demo);
        $count = 0;
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement) && !preg_match('/^--/', $statement)) {
                try {
                    $pdo->exec($statement);
                    $count++;
                } catch (PDOException $e) {
                    echo "<p style='color: orange;'>⚠ Erreur (peut être normale si données existent): " . htmlspecialchars($e->getMessage()) . "</p>";
                }
            }
        }
        echo "<p>✓ $count instructions exécutées</p>";
        echo "<p>✓ Données de démonstration chargées avec succès</p>";
    } else {
        echo "<p style='color: red;'>✗ Fichier demo_data.sql non trouvé</p>";
    }
    
    echo "<h2 style='color: green;'>✓ Données de démo chargées!</h2>";
    echo "<p>Comptes de test créés:</p>";
    echo "<ul>";
    echo "<li>Admin: admin@urbanova.cd / password</li>";
    echo "<li>Investisseur 1: jean.dupont@example.com / password</li>";
    echo "<li>Investisseur 2: marie.kouassi@example.com / password</li>";
    echo "</ul>";
    echo "<p><a href='/urbanovacorp/'>Aller au site</a></p>";
    
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>✗ Erreur de connexion à la base de données</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Solution:</strong> Exécutez d'abord setup_database.php</p>";
}
