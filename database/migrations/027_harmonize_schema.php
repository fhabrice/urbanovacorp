<?php

/**
 * Migration: Harmonisation du schéma projects avec l'API + RBAC + Data Room
 *
 * 1. Ajoute les colonnes utilisées par ApiController (validation_status,
 *    funding_raised, expected_roi, project_type, operation_type, slug,
 *    promoter, promoter_id, coordinates_lat, coordinates_lng) si absentes.
 * 2. Étend le type ENUM de notifications.
 * 3. Ajoute un statut aux permissions Data Room (active / révoquée / expirée).
 *
 * Chaque ALTER est protégé par try/catch pour rester compatible MySQL et
 * MariaDB (pas de ADD COLUMN IF NOT EXISTS côté MySQL).
 */

return [
    'up' => function($db) {
        $columns = [
            'slug VARCHAR(255) NULL',
            'promoter VARCHAR(255) NULL',
            'promoter_id INT NULL',
            'validation_status VARCHAR(50) NULL',
            'funding_raised DECIMAL(15,2) DEFAULT 0',
            'expected_roi DECIMAL(5,2) NULL',
            'project_type VARCHAR(50) NULL',
            'operation_type VARCHAR(50) NULL',
            'coordinates_lat DECIMAL(10,8) NULL',
            'coordinates_lng DECIMAL(11,8) NULL',
        ];

        foreach ($columns as $column) {
            try {
                $db->execute("ALTER TABLE projects ADD COLUMN $column");
            } catch (\Exception $e) {
                // Colonne déjà présente -> on ignore
            }
        }

        // Index utiles (ignorés si déjà présents)
        $indexes = [
            'idx_validation_status (validation_status)',
            'idx_project_type (project_type)',
        ];
        foreach ($indexes as $index) {
            try {
                $db->execute("ALTER TABLE projects ADD INDEX $index");
            } catch (\Exception $e) {
                // Ignorer si déjà présent
            }
        }

        // Notifications : étendre les types disponibles
        try {
            $db->execute("
                ALTER TABLE notifications
                MODIFY COLUMN type ENUM('project_status', 'investment_update', 'visit_request',
                    'reservation', 'kyc_status', 'message', 'system', 'funding', 'mandate',
                    'offer', 'data_room', 'inquiry') NOT NULL DEFAULT 'system'
            ");
        } catch (\Exception $e) {
            // Table absente ou type déjà étendu -> ignorer
        }

        // Data Room permissions : statut (active / révoquée / expirée)
        try {
            $db->execute("
                ALTER TABLE data_room_permissions
                ADD COLUMN status ENUM('active', 'revoked', 'expired') DEFAULT 'active'
            ");
        } catch (\Exception $e) {
            // Colonne déjà présente -> ignorer
        }

        // Messagerie : lien optionnel avec un projet (questions sur un projet)
        try {
            $db->execute("ALTER TABLE investor_messages ADD COLUMN project_id INT NULL AFTER investor_id");
        } catch (\Exception $e) {
            // Colonne déjà présente -> ignorer
        }
    },
    'down' => function($db) {
        // Pas de rollback automatique des colonnes pour éviter toute perte de données
    }
];
