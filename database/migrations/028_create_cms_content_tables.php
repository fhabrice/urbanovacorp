<?php

/**
 * Migration: Gestion des contenus par l'admin (CMS)
 *
 * 1. Table news (actualités) — création si absente
 * 2. Table site_content (contenus du site : héro, stats, sections, contact, footer)
 * 3. Colonne projects.is_featured (projets phares de la page d'accueil)
 * 4. Valeurs par défaut du contenu du site
 */

return [
    'up' => function($db) {
        // ---- 1. Table news ----
        try {
            $db->execute("
                CREATE TABLE IF NOT EXISTS news (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    slug VARCHAR(255) NOT NULL UNIQUE,
                    excerpt VARCHAR(500),
                    content TEXT,
                    category ENUM('entreprise', 'projets', 'marché', 'partenariats') NOT NULL DEFAULT 'entreprise',
                    image VARCHAR(512),
                    author_id INT NULL,
                    status ENUM('draft', 'published') DEFAULT 'draft',
                    deleted_at DATETIME NULL,
                    published_at DATETIME NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL,
                    INDEX idx_status (status),
                    INDEX idx_category (category)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        } catch (\Exception $e) {
            // Table déjà présente -> ignorer
        }

        // ---- 2. Table site_content ----
        try {
            $db->execute("
                CREATE TABLE IF NOT EXISTS site_content (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    content_key VARCHAR(100) NOT NULL UNIQUE,
                    content_value MEDIUMTEXT,
                    category VARCHAR(50) DEFAULT 'general',
                    updated_by INT NULL,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX idx_category (category)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        } catch (\Exception $e) {
            // Table déjà présente -> ignorer
        }

        // ---- 3. Colonne is_featured sur projects ----
        try {
            $db->execute("ALTER TABLE projects ADD COLUMN is_featured TINYINT(1) NOT NULL DEFAULT 0");
        } catch (\Exception $e) {
            // Colonne déjà présente -> ignorer
        }
        try {
            $db->execute("ALTER TABLE projects ADD INDEX idx_featured (is_featured)");
        } catch (\Exception $e) {
            // Index déjà présent -> ignorer
        }

        // ---- 4. Valeurs par défaut du contenu du site ----
        $defaults = [
            // Accueil - Héro
            ['hero_pretitle', 'URBANOVA SOLUTIONS', 'home'],
            ['hero_title', 'Structurer les villes', 'home'],
            ['hero_title_highlight', 'africaines de demain', 'home'],
            ['hero_subtitle', 'Construction, Immobilier, Infrastructures et Investissements pour un développement durable en République Démocratique du Congo et Afrique Centrale.', 'home'],
            // Accueil - Statistiques
            ['stats_projects_value', '120+', 'home'],
            ['stats_projects_label', 'Projets réalisés', 'home'],
            ['stats_investments_value', '45 M$', 'home'],
            ['stats_investments_label', 'Investissements mobilisés', 'home'],
            ['stats_housing_value', '2 500', 'home'],
            ['stats_housing_label', 'Logements développés', 'home'],
            ['stats_jobs_value', '5 000', 'home'],
            ['stats_jobs_label', 'Emplois créés', 'home'],
            ['stats_countries_value', '4', 'home'],
            ['stats_countries_label', "Pays d'intervention", 'home'],
            ['stats_countries_detail', 'RDC • Rwanda • Ouganda • Burundi', 'home'],
            // Accueil - Sections
            ['departments_title', 'Nos Départements', 'home'],
            ['departments_items', '[{"icon": "fa-solid fa-helmet-safety", "title": "Construction & Maintenance", "text": "Édification et entretien d\'ouvrages durables, respectueux de l\'environnement et des standards de sécurité modernes."}, {"icon": "fa-solid fa-trash-can", "title": "Assainissement & Déchets", "text": "Gestion environnementale intégrée, tri, recyclage et propreté urbaine pour préserver la santé publique."}, {"icon": "fa-solid fa-building-circle-check", "title": "Facility Management", "text": "Exploitation, sécurisation, et maintenance d\'infrastructures d\'envergure pour prolonger la durée de vie de vos actifs."}, {"icon": "fa-solid fa-house-chimney-window", "title": "Immobilier & Aménagement", "text": "Promotion immobilière et aménagement de zones urbaines intégrées favorisant la mixité sociale et économique."}, {"icon": "fa-solid fa-compass-drafting", "title": "Ingénierie", "text": "Assistance technique de haut niveau, études de sol, architecture moderne et conception modulaire de projets."}, {"icon": "fa-solid fa-leaf", "title": "Développement durable", "text": "Conception d\'infrastructures à fort impact environnemental positif (énergies renouvelables, captage d\'eau)."}]', 'home'],
            ['departments_text', "Une expertise multisectorielle au service du développement urbain durable en République Démocratique du Congo.", 'home'],
            ['cta_promoter_title', 'Vous avez un projet immobilier ?', 'home'],
            ['cta_promoter_text', "Soumettez votre dossier technique, profitez d'une validation d'experts et accédez à notre réseau international d'investisseurs certifiés.", 'home'],
            ['cta_investor_title', 'Vous êtes investisseur ?', 'home'],
            ['cta_investor_text', "Rejoignez notre écosystème d'investisseurs, explorez des opportunités qualifiées et suivez vos rendements via notre tableau de bord de pointe.", 'home'],
            ['featured_title', 'Projets phares', 'home'],
            ['featured_text', "Découvrez nos réalisations majeures en cours d'exécution ou livrées avec succès sur le continent.", 'home'],
            // À propos
            ['about_mission', "Concevoir, développer et opérer des solutions urbaines durables à fort impact pour restructurer durablement l'environnement urbain et mobiliser des capitaux qualifiés.", 'about'],
            ['about_vision', "Devenir la référence africaine incontournable en matière de développement urbain intégré et de facilitation financière de projets durables en Afrique Centrale.", 'about'],
            // Contact
            ['contact_address', "Avenue de l'Unité, Quartier Himbi, Goma, Nord-Kivu, RD Congo", 'contact'],
            ['contact_phone1', '+243 900 000 000', 'contact'],
            ['contact_phone2', '+243 800 000 000', 'contact'],
            ['contact_email', 'contact@urbanova.cd', 'contact'],
            ['contact_hours1', 'Lun - Ven : 8h00 - 17h00', 'contact'],
            ['contact_hours2', 'Sam : 9h00 - 13h00', 'contact'],
            // Pied de page
            ['footer_city', 'Goma, RD Congo', 'footer'],
            ['footer_phone', '+243 900 000 000', 'footer'],
            ['footer_email', 'contact@urbanova.cd', 'footer'],
            ['footer_about', 'URBANOVA SOLUTIONS — Développement urbain durable, immobilier et investissements en Afrique Centrale.', 'footer'],
        ];

        foreach ($defaults as $row) {
            try {
                $stmt = $db->query("SELECT COUNT(*) FROM site_content WHERE content_key = '" . $row[0] . "'");
                if ((int)$stmt->fetchColumn() === 0) {
                    $ins = $db->prepare("INSERT INTO site_content (content_key, content_value, category) VALUES (?, ?, ?)");
                    $ins->execute($row);
                }
            } catch (\Exception $e) {
                // Ignorer (table absente)
            }
        }
    },
    'down' => function($db) {
        // Pas de rollback pour éviter toute perte de contenu
    }
];
