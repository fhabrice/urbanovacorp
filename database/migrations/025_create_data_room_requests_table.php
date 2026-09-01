<?php

/**
 * Migration: Create data_room_requests table
 * Module Espace Investisseur - demandes d'accès à la Data Room par projet,
 * validées / refusées / révoquées par Urbanova
 */

return [
    'up' => function($db) {
        $sql = "
            CREATE TABLE IF NOT EXISTS data_room_requests (
                id INT AUTO_INCREMENT PRIMARY KEY,
                project_id INT NOT NULL,
                investor_id INT NOT NULL,
                requested_level ENUM('view_only', 'download_allowed', 'full_access', 'temporary') DEFAULT 'view_only',
                justification TEXT,
                status ENUM('pending', 'approved', 'refused', 'revoked', 'expired') DEFAULT 'pending',
                granted_permission_id INT NULL,
                decided_by INT NULL,
                decided_at TIMESTAMP NULL,
                refusal_reason TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
                FOREIGN KEY (investor_id) REFERENCES investors(id) ON DELETE CASCADE,
                FOREIGN KEY (granted_permission_id) REFERENCES data_room_permissions(id) ON DELETE SET NULL,
                FOREIGN KEY (decided_by) REFERENCES users(id) ON DELETE SET NULL,
                INDEX idx_project_id (project_id),
                INDEX idx_investor_id (investor_id),
                INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $db->execute($sql);
    },
    'down' => function($db) {
        $sql = "DROP TABLE IF EXISTS data_room_requests";
        $db->execute($sql);
    }
];
