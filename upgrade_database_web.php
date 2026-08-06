<?php
/**
 * Script de mise à jour de la base de données (Version Web)
 * Exécutez ce fichier via: http://localhost/urbanovacorp/upgrade_database_web.php
 */

echo "<h1>Mise à jour de la base de données Urbanova v2.0</h1>";
echo "<h2>Fonctionnalités: KYC, Data Room, Workflow avancé</h2>";

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'urbanova_db';

// Essayer d'abord avec les valeurs par défaut
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color: green;'>✓ Connexion à la base de données réussie (utilisateur: root)</p>";
    
} catch (PDOException $e) {
    // Si échec, essayer avec une autre configuration
    echo "<p style='color: orange;'>⚠ Connexion échouée avec root, tentative avec autre configuration...</p>";
    
    // Configuration alternative (ajustez selon votre système)
    $host = 'localhost';
    $user = 'root';
    $pass = ''; // Ajoutez votre mot de passe MySQL si nécessaire
    $dbname = 'urbanova_db';
    
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<p style='color: green;'>✓ Connexion réussie avec configuration alternative</p>";
    } catch (PDOException $e2) {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
        echo "<h3>❌ Erreur de connexion à la base de données</h3>";
        echo "<p><strong>Erreur 1:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p><strong>Erreur 2:</strong> " . htmlspecialchars($e2->getMessage()) . "</p>";
        echo "<h4>Solutions possibles:</h4>";
        echo "<ul>";
        echo "<li>Vérifiez que MySQL/Laragon est démarré</li>";
        echo "<li>Vérifiez que la base de données 'urbanova_db' existe</li>";
        echo "<li>Vérifiez vos identifiants MySQL (utilisateur: root, mot de passe: vide par défaut)</li>";
        echo "<li>Modifiez les identifiants dans ce fichier si nécessaire</li>";
        echo "</ul>";
        echo "</div>";
        exit;
    }
}
    
    // Liste des requêtes SQL à exécuter (avec vérification préalable)
    $upgradeQueries = [
        // 1. Mise à jour table users
        [
            'check' => "SHOW COLUMNS FROM users LIKE 'investor_type'",
            'alter' => "ALTER TABLE users ADD COLUMN investor_type ENUM('business_angel', 'individual', 'family_office', 'investment_fund', 'bank', 'investment_company', 'dfi', 'corporate_venture', 'other') DEFAULT 'individual'"
        ],
        [
            'check' => "SHOW COLUMNS FROM users LIKE 'company_name'",
            'alter' => "ALTER TABLE users ADD COLUMN company_name VARCHAR(255)"
        ],
        [
            'check' => "SHOW COLUMNS FROM users LIKE 'representative_name'",
            'alter' => "ALTER TABLE users ADD COLUMN representative_name VARCHAR(255)"
        ],
        [
            'check' => "SHOW COLUMNS FROM users LIKE 'position'",
            'alter' => "ALTER TABLE users ADD COLUMN position VARCHAR(100)"
        ],
        [
            'check' => "SHOW COLUMNS FROM users LIKE 'address'",
            'alter' => "ALTER TABLE users ADD COLUMN address TEXT"
        ],
        [
            'check' => "SHOW COLUMNS FROM users LIKE 'website'",
            'alter' => "ALTER TABLE users ADD COLUMN website VARCHAR(255)"
        ],
        [
            'check' => "SHOW COLUMNS FROM users LIKE 'kyc_status'",
            'alter' => "ALTER TABLE users ADD COLUMN kyc_status ENUM('pending', 'under_review', 'approved', 'rejected', 'additional_info') DEFAULT 'pending'"
        ],
        [
            'check' => "SHOW COLUMNS FROM users LIKE 'kyc_submitted_at'",
            'alter' => "ALTER TABLE users ADD COLUMN kyc_submitted_at TIMESTAMP NULL"
        ],
        [
            'check' => "SHOW COLUMNS FROM users LIKE 'kyc_approved_at'",
            'alter' => "ALTER TABLE users ADD COLUMN kyc_approved_at TIMESTAMP NULL"
        ],
        [
            'check' => "SHOW COLUMNS FROM users LIKE 'kyc_notes'",
            'alter' => "ALTER TABLE users ADD COLUMN kyc_notes TEXT"
        ],
        
        // 2. Table investor_profiles
        [
            'check' => "SHOW TABLES LIKE 'investor_profiles'",
            'create' => "CREATE TABLE IF NOT EXISTS investor_profiles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                years_experience INT,
                projects_financed INT,
                presentation TEXT,
                references_portfolio TEXT,
                investment_min DECIMAL(15,2),
                investment_max DECIMAL(15,2),
                investment_horizon VARCHAR(50),
                expected_roi DECIMAL(5,2),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        ],
        
        // 3. Table investor_preferences
        [
            'check' => "SHOW TABLES LIKE 'investor_preferences'",
            'create' => "CREATE TABLE IF NOT EXISTS investor_preferences (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                preferred_sectors JSON,
                preferred_countries JSON,
                investment_types JSON,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        ],
        
        // 4. Table kyc_documents
        [
            'check' => "SHOW TABLES LIKE 'kyc_documents'",
            'create' => "CREATE TABLE IF NOT EXISTS kyc_documents (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                document_type ENUM('id_card', 'passport', 'company_registration', 'articles_of_association', 'representative_id', 'power_of_attorney', 'proof_of_address', 'other') NOT NULL,
                document_name VARCHAR(255),
                file_path VARCHAR(500),
                file_size INT,
                file_mime VARCHAR(100),
                uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                verified BOOLEAN DEFAULT FALSE,
                verified_at TIMESTAMP NULL,
                verified_by INT,
                notes TEXT,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (verified_by) REFERENCES users(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        ],
        
        // 5. Mise à jour table projects
        [
            'check' => "SHOW COLUMNS FROM projects LIKE 'project_type'",
            'alter' => "ALTER TABLE projects ADD COLUMN project_type ENUM('residential', 'commercial', 'mixed', 'industrial', 'hotel', 'office', 'subdivision', 'other') DEFAULT 'residential'"
        ],
        [
            'check' => "SHOW COLUMNS FROM projects LIKE 'operation_type'",
            'alter' => "ALTER TABLE projects ADD COLUMN operation_type ENUM('sale', 'rental', 'fundraising', 'sale_fundraising') DEFAULT 'sale'"
        ],
        [
            'check' => "SHOW COLUMNS FROM projects LIKE 'project_status'",
            'alter' => "ALTER TABLE projects ADD COLUMN project_status ENUM('idea', 'studies_complete', 'land_acquired', 'construction', 'completed') DEFAULT 'idea'"
        ],
        [
            'check' => "SHOW COLUMNS FROM projects LIKE 'estimated_delivery'",
            'alter' => "ALTER TABLE projects ADD COLUMN estimated_delivery DATE"
        ],
        [
            'check' => "SHOW COLUMNS FROM projects LIKE 'coordinates_lat'",
            'alter' => "ALTER TABLE projects ADD COLUMN coordinates_lat DECIMAL(10,8)"
        ],
        [
            'check' => "SHOW COLUMNS FROM projects LIKE 'coordinates_lng'",
            'alter' => "ALTER TABLE projects ADD COLUMN coordinates_lng DECIMAL(11,8)"
        ],
        [
            'check' => "SHOW COLUMNS FROM projects LIKE 'land_cost'",
            'alter' => "ALTER TABLE projects ADD COLUMN land_cost DECIMAL(15,2)"
        ],
        [
            'check' => "SHOW COLUMNS FROM projects LIKE 'construction_cost'",
            'alter' => "ALTER TABLE projects ADD COLUMN construction_cost DECIMAL(15,2)"
        ],
        [
            'check' => "SHOW COLUMNS FROM projects LIKE 'technical_fees'",
            'alter' => "ALTER TABLE projects ADD COLUMN technical_fees DECIMAL(15,2)"
        ],
        [
            'check' => "SHOW COLUMNS FROM projects LIKE 'admin_fees'",
            'alter' => "ALTER TABLE projects ADD COLUMN admin_fees DECIMAL(15,2)"
        ],
        [
            'check' => "SHOW COLUMNS FROM projects LIKE 'working_capital'",
            'alter' => "ALTER TABLE projects ADD COLUMN working_capital DECIMAL(15,2)"
        ],
        [
            'check' => "SHOW COLUMNS FROM projects LIKE 'estimated_revenue'",
            'alter' => "ALTER TABLE projects ADD COLUMN estimated_revenue DECIMAL(15,2)"
        ],
        [
            'check' => "SHOW COLUMNS FROM projects LIKE 'estimated_profit'",
            'alter' => "ALTER TABLE projects ADD COLUMN estimated_profit DECIMAL(15,2)"
        ],
        [
            'check' => "SHOW COLUMNS FROM projects LIKE 'project_duration'",
            'alter' => "ALTER TABLE projects ADD COLUMN project_duration INT"
        ],
        [
            'check' => "SHOW COLUMNS FROM projects LIKE 'sale_price_estimated'",
            'alter' => "ALTER TABLE projects ADD COLUMN sale_price_estimated DECIMAL(15,2)"
        ],
        [
            'check' => "SHOW COLUMNS FROM projects LIKE 'rental_price_estimated'",
            'alter' => "ALTER TABLE projects ADD COLUMN rental_price_estimated DECIMAL(15,2)"
        ],
        [
            'check' => "SHOW COLUMNS FROM projects LIKE 'units_for_sale'",
            'alter' => "ALTER TABLE projects ADD COLUMN units_for_sale INT"
        ],
        [
            'check' => "SHOW COLUMNS FROM projects LIKE 'units_for_rent'",
            'alter' => "ALTER TABLE projects ADD COLUMN units_for_rent INT"
        ],
        [
            'check' => "SHOW COLUMNS FROM projects LIKE 'commission_rate'",
            'alter' => "ALTER TABLE projects ADD COLUMN commission_rate DECIMAL(5,2) DEFAULT 3.0"
        ],
        [
            'check' => "SHOW COLUMNS FROM projects LIKE 'validation_status'",
            'alter' => "ALTER TABLE projects ADD COLUMN validation_status ENUM('draft', 'submitted', 'under_review', 'additional_info', 'approved', 'rejected', 'published', 'suspended', 'sold', 'rented', 'archived') DEFAULT 'draft'"
        ],
        [
            'check' => "SHOW COLUMNS FROM projects LIKE 'reference'",
            'alter' => "ALTER TABLE projects ADD COLUMN reference VARCHAR(50)"
        ],
        [
            'check' => "SHOW COLUMNS FROM projects LIKE 'reviewed_by'",
            'alter' => "ALTER TABLE projects ADD COLUMN reviewed_by INT"
        ],
        [
            'check' => "SHOW COLUMNS FROM projects LIKE 'reviewed_at'",
            'alter' => "ALTER TABLE projects ADD COLUMN reviewed_at TIMESTAMP NULL"
        ],
        [
            'check' => "SHOW COLUMNS FROM projects LIKE 'review_notes'",
            'alter' => "ALTER TABLE projects ADD COLUMN review_notes TEXT"
        ],
        
        // 6. Table project_documents
        [
            'check' => "SHOW TABLES LIKE 'project_documents'",
            'create' => "CREATE TABLE IF NOT EXISTS project_documents (
                id INT AUTO_INCREMENT PRIMARY KEY,
                project_id INT NOT NULL,
                document_category ENUM('administrative', 'land', 'technical', 'administrative_auth', 'financial', 'commercial', 'other') NOT NULL,
                document_type VARCHAR(100),
                document_name VARCHAR(255),
                file_path VARCHAR(500),
                file_size INT,
                file_mime VARCHAR(100),
                uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        ],
        
        // 7. Table data_room_documents
        [
            'check' => "SHOW TABLES LIKE 'data_room_documents'",
            'create' => "CREATE TABLE IF NOT EXISTS data_room_documents (
                id INT AUTO_INCREMENT PRIMARY KEY,
                project_id INT NOT NULL,
                document_name VARCHAR(255) NOT NULL,
                document_type ENUM('business_plan', 'pitch_deck', 'financial_statements', 'market_study', 'financial_model', 'legal_documents', 'permits', 'plans', 'contracts', 'technical_reports', 'photos', 'videos', 'other') NOT NULL,
                file_path VARCHAR(500),
                file_size INT,
                file_mime VARCHAR(100),
                description TEXT,
                uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        ],
        
        // 8. Table data_room_access_requests
        [
            'check' => "SHOW TABLES LIKE 'data_room_access_requests'",
            'create' => "CREATE TABLE IF NOT EXISTS data_room_access_requests (
                id INT AUTO_INCREMENT PRIMARY KEY,
                investor_id INT NOT NULL,
                project_id INT NOT NULL,
                access_level ENUM('view_only', 'download_allowed', 'full_access') DEFAULT 'view_only',
                requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                reviewed_by INT,
                reviewed_at TIMESTAMP NULL,
                status ENUM('pending', 'approved', 'rejected', 'suspended', 'revoked') DEFAULT 'pending',
                expires_at TIMESTAMP NULL,
                review_notes TEXT,
                FOREIGN KEY (investor_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
                FOREIGN KEY (reviewed_by) REFERENCES users(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        ],
        
        // 9. Table data_room_access_logs
        [
            'check' => "SHOW TABLES LIKE 'data_room_access_logs'",
            'create' => "CREATE TABLE IF NOT EXISTS data_room_access_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                investor_id INT NOT NULL,
                project_id INT NOT NULL,
                document_id INT,
                action ENUM('request_access', 'view_document', 'download_document', 'login', 'logout') NOT NULL,
                ip_address VARCHAR(45),
                user_agent TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (investor_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
                FOREIGN KEY (document_id) REFERENCES data_room_documents(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        ],
        
        // 10. Table investment_offers
        [
            'check' => "SHOW TABLES LIKE 'investment_offers'",
            'create' => "CREATE TABLE IF NOT EXISTS investment_offers (
                id INT AUTO_INCREMENT PRIMARY KEY,
                investor_id INT NOT NULL,
                project_id INT NOT NULL,
                amount DECIMAL(15,2) NOT NULL,
                conditions TEXT,
                status ENUM('pending', 'under_review', 'accepted', 'rejected', 'withdrawn') DEFAULT 'pending',
                submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                reviewed_by INT,
                reviewed_at TIMESTAMP NULL,
                review_notes TEXT,
                FOREIGN KEY (investor_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
                FOREIGN KEY (reviewed_by) REFERENCES users(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        ],
        
        // 11. Table notifications
        [
            'check' => "SHOW TABLES LIKE 'notifications'",
            'create' => "CREATE TABLE IF NOT EXISTS notifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                type ENUM('kyc_approved', 'kyc_rejected', 'kyc_additional_info', 'project_approved', 'project_rejected', 'data_room_access_granted', 'data_room_access_denied', 'investment_offer_accepted', 'investment_offer_rejected', 'new_project', 'system') NOT NULL,
                title VARCHAR(255) NOT NULL,
                message TEXT,
                link VARCHAR(500),
                read_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        ],
        
        // 12. Table messages
        [
            'check' => "SHOW TABLES LIKE 'messages'",
            'create' => "CREATE TABLE IF NOT EXISTS messages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                sender_id INT NOT NULL,
                receiver_id INT NOT NULL,
                project_id INT,
                subject VARCHAR(255),
                message TEXT NOT NULL,
                read_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        ],
        
        // 13. Table visit_requests
        [
            'check' => "SHOW TABLES LIKE 'visit_requests'",
            'create' => "CREATE TABLE IF NOT EXISTS visit_requests (
                id INT AUTO_INCREMENT PRIMARY KEY,
                project_id INT NOT NULL,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(50) NOT NULL,
                preferred_date DATE NOT NULL,
                preferred_time VARCHAR(50),
                message TEXT,
                status ENUM('pending', 'confirmed', 'rejected', 'completed') DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        ],
        
        // 14. Table reservations
        [
            'check' => "SHOW TABLES LIKE 'reservations'",
            'create' => "CREATE TABLE IF NOT EXISTS reservations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                project_id INT NOT NULL,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(50) NOT NULL,
                property_type ENUM('purchase', 'rent') DEFAULT 'purchase',
                budget DECIMAL(15,2),
                message TEXT,
                status ENUM('pending', 'confirmed', 'rejected', 'completed') DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        ]
    ];
    
    $executed = 0;
    $failed = 0;
    $skipped = 0;
    
    echo "<h3>Exécution des requêtes SQL:</h3>";
    echo "<ul>";
    
    foreach ($upgradeQueries as $i => $queryData) {
        try {
            // Vérifier si la colonne/table existe déjà
            if (isset($queryData['check'])) {
                $stmt = $conn->query($queryData['check']);
                if ($stmt->rowCount() > 0) {
                    $skipped++;
                    echo "<li style='color: orange;'>⚠ Étape " . ($i + 1) . " déjà existante</li>";
                    continue;
                }
            }
            
            // Exécuter la requête
            if (isset($queryData['alter'])) {
                $conn->exec($queryData['alter']);
            } elseif (isset($queryData['create'])) {
                $conn->exec($queryData['create']);
            }
            
            $executed++;
            echo "<li style='color: green;'>✓ Étape " . ($i + 1) . " exécutée</li>";
        } catch (PDOException $e) {
            $failed++;
            
            // Vérifier si c'est une erreur acceptable
            if (strpos($e->getMessage(), 'Duplicate column') !== false || 
                strpos($e->getMessage(), 'already exists') !== false) {
                $skipped++;
                echo "<li style='color: orange;'>⚠ Étape " . ($i + 1) . " déjà existante</li>";
            } else {
                echo "<li style='color: red;'>❌ Étape " . ($i + 1) . " échouée: " . htmlspecialchars($e->getMessage()) . "</li>";
            }
        }
    }
    
    echo "</ul>";
    
    echo "<div style='background: #f0f0f0; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h3>RÉSUMÉ:</h3>";
    echo "<p><strong>Étapes exécutées:</strong> $executed</p>";
    echo "<p><strong>Étapes échouées:</strong> $failed</p>";
    echo "<p><strong>Étapes ignorées (déjà existantes):</strong> $skipped</p>";
    echo "</div>";
    
    // Vérifier les dossiers d'upload
    echo "<h3>Vérification des dossiers d'upload:</h3>";
    echo "<ul>";
    
    $uploadDirs = [
        'uploads',
        'uploads/kyc',
        'uploads/projects',
        'uploads/data_room'
    ];
    
    foreach ($uploadDirs as $dir) {
        if (!is_dir(__DIR__ . '/' . $dir)) {
            if (mkdir(__DIR__ . '/' . $dir, 0755, true)) {
                echo "<li style='color: green;'>✓ Dossier créé: $dir</li>";
            } else {
                echo "<li style='color: red;'>❌ Impossible de créer: $dir</li>";
            }
        } else {
            echo "<li style='color: green;'>✓ Dossier existe: $dir</li>";
        }
    }
    
    echo "</ul>";
    
    if ($failed === 0) {
        echo "<div style='background: #d4edda; color: #155724; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
        echo "<h3>✅ Mise à jour terminée avec succès!</h3>";
        echo "<p>La base de données est maintenant prête pour les fonctionnalités KYC, Data Room et Workflow avancé.</p>";
        echo "<p><a href='index.html' style='display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;'>Retour au site</a></p>";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
        echo "<h3>⚠ Mise à jour terminée avec des erreurs</h3>";
        echo "<p>Certaines requêtes ont échoué mais la mise à jour peut être fonctionnelle.</p>";
        echo "</div>";
    }
