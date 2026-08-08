<?php

/**
 * Migration: Create permissions table for RBAC
 */

return [
    'up' => function($db) {
        $sql = "
            CREATE TABLE IF NOT EXISTS permissions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL UNIQUE,
                description TEXT,
                module VARCHAR(50) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $db->execute($sql);

        // Insert default permissions
        $permissions = [
            ['manage_users', 'Gérer les utilisateurs', 'admin'],
            ['manage_projects', 'Gérer tous les projets', 'admin'],
            ['validate_projects', 'Valider les projets', 'admin'],
            ['manage_investors', 'Gérer les investisseurs', 'admin'],
            ['manage_campaigns', 'Gérer les campagnes de financement', 'admin'],
            ['view_analytics', 'Voir les statistiques', 'admin'],
            ['manage_own_projects', 'Gérer ses propres projets', 'promoter'],
            ['view_projects', 'Voir les projets', 'investor'],
            ['submit_offers', 'Soumettre des offres d\'investissement', 'investor'],
            ['access_dataroom', 'Accéder à la Data Room', 'investor'],
            ['view_marketplace', 'Voir la marketplace', 'client'],
            ['request_visits', 'Demander des visites', 'client'],
            ['make_reservations', 'Faire des réservations', 'client'],
        ];

        foreach ($permissions as $perm) {
            $db->execute(
                "INSERT INTO permissions (name, description, module) VALUES (?, ?, ?)",
                $perm
            );
        }
    },
    'down' => function($db) {
        $sql = "DROP TABLE IF EXISTS permissions";
        $db->execute($sql);
    }
];
