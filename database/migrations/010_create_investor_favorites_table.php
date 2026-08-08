<?php

/**
 * Migration: Create investor favorites table
 */

return [
    'up' => function($db) {
        $sql = "
            CREATE TABLE IF NOT EXISTS investor_favorites (
                id INT AUTO_INCREMENT PRIMARY KEY,
                investor_id INT NOT NULL,
                project_id INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (investor_id) REFERENCES investors(id) ON DELETE CASCADE,
                FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
                UNIQUE KEY unique_favorite (investor_id, project_id),
                INDEX idx_investor_id (investor_id),
                INDEX idx_project_id (project_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $db->execute($sql);
    },
    'down' => function($db) {
        $sql = "DROP TABLE IF EXISTS investor_favorites";
        $db->execute($sql);
    }
];
