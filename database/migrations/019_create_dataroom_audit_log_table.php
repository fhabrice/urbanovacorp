<?php

/**
 * Migration: Create data room audit log table
 * For tracking all data room activities
 */

return [
    'up' => function($db) {
        $sql = "
            CREATE TABLE IF NOT EXISTS data_room_audit_log (
                id INT AUTO_INCREMENT PRIMARY KEY,
                project_id INT NOT NULL,
                investor_id INT NOT NULL,
                action ENUM('access_granted', 'access_denied', 'document_viewed', 'document_downloaded', 'access_revoked', 'permission_changed') NOT NULL,
                document_id INT NULL,
                details TEXT,
                ip_address VARCHAR(45),
                user_agent TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
                FOREIGN KEY (investor_id) REFERENCES investors(id) ON DELETE CASCADE,
                INDEX idx_project_id (project_id),
                INDEX idx_investor_id (investor_id),
                INDEX idx_action (action),
                INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $db->execute($sql);
    },
    'down' => function($db) {
        $sql = "DROP TABLE IF EXISTS data_room_audit_log";
        $db->execute($sql);
    }
];
