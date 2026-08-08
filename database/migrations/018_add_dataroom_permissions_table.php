<?php

/**
 * Migration: Add data room permissions table
 * For managing access levels to data rooms
 */

return [
    'up' => function($db) {
        $sql = "
            CREATE TABLE IF NOT EXISTS data_room_permissions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                project_id INT NOT NULL,
                investor_id INT NOT NULL,
                permission_level ENUM('view_only', 'download_allowed', 'full_access', 'temporary') DEFAULT 'view_only',
                expires_at TIMESTAMP NULL,
                granted_by INT NOT NULL,
                granted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
                FOREIGN KEY (investor_id) REFERENCES investors(id) ON DELETE CASCADE,
                FOREIGN KEY (granted_by) REFERENCES users(id) ON DELETE SET NULL,
                UNIQUE KEY unique_project_investor (project_id, investor_id),
                INDEX idx_project_id (project_id),
                INDEX idx_investor_id (investor_id),
                INDEX idx_permission_level (permission_level)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $db->execute($sql);
    },
    'down' => function($db) {
        $sql = "DROP TABLE IF EXISTS data_room_permissions";
        $db->execute($sql);
    }
];
