<?php
/**
 * Database Setup Script for URBANOVA SOLUTIONS
 * Run this script to create the database and seed initial data
 */

echo "<h1>Setup Database URBANOVA SOLUTIONS</h1>";

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'urbanova_db';

try {
    // Connect to MySQL (without database)
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p>✓ Connecté à MySQL</p>";
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p>✓ Base de données '$database' créée ou existe déjà</p>";
    
    // Select database
    $pdo->exec("USE `$database`");
    echo "<p>✓ Base de données sélectionnée</p>";
    
    // Read and execute schema
    $schemaFile = __DIR__ . '/database/investment_schema.sql';
    if (file_exists($schemaFile)) {
        $schema = file_get_contents($schemaFile);
        
        // Remove the CREATE DATABASE and USE statements (already done)
        $schema = preg_replace('/CREATE DATABASE.*?;/s', '', $schema);
        $schema = preg_replace('/USE .*?;/s', '', $schema);
        
        // Split by semicolon and execute each statement
        $statements = explode(';', $schema);
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                try {
                    $pdo->exec($statement);
                } catch (PDOException $e) {
                    echo "<p style='color: orange;'>⚠ Erreur (peut être normale): " . htmlspecialchars($e->getMessage()) . "</p>";
                }
            }
        }
        echo "<p>✓ Schéma de base de données créé</p>";
    } else {
        echo "<p style='color: red;'>✗ Fichier schema non trouvé</p>";
    }
    
    // Seed data
    $seedFile = __DIR__ . '/database/seed_data.sql';
    if (file_exists($seedFile)) {
        $seed = file_get_contents($seedFile);
        
        // Execute seed statements
        $statements = explode(';', $seed);
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement) && !preg_match('/^--/', $statement)) {
                try {
                    $pdo->exec($statement);
                } catch (PDOException $e) {
                    echo "<p style='color: orange;'>⚠ Erreur seed: " . htmlspecialchars($e->getMessage()) . "</p>";
                }
            }
        }
        echo "<p>✓ Données de test insérées</p>";
    } else {
        echo "<p style='color: red;'>✗ Fichier seed_data non trouvé</p>";
    }
    
    echo "<h2 style='color: green;'>✓ Setup terminé avec succès!</h2>";
    echo "<p><a href='/urbanovacorp/'>Aller au site</a></p>";
    
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>✗ Erreur de connexion à la base de données</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Solution:</strong> Vérifiez que MySQL est démarré et que les identifiants sont corrects dans config/config.php</p>";
}
