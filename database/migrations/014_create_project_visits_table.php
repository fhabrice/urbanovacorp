<?php

/**
 * Migration: Create project visits table
 * For visit requests to projects
 */

return [
    'up' => function($db) {
        $sql = "
            CREATE TABLE IF NOT EXISTS project_visits (
                id INT AUTO_INCREMENT PRIMARY KEY,
                project_id INT NOT NULL,
                user_id INT NULL,
                visitor_name VARCHAR(255) NOT NULL,
                visitor_email VARCHAR(255) NOT NULL,
                visitor_phone VARCHAR(50),
                preferred_date DATE NOT NULL,
                preferred_time TIME NOT NULL,
                message TEXT,
                status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
                INDEX idx_project_id (project_id),
                INDEX idx_user_id (user_id),
                INDEX idx_status (status),
                INDEX idx_preferred_date (preferred_date)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $db->execute($sql);
    },
    'down' => function($db) {
        $sql = "DROP TABLE IF EXISTS project_visits";
        $db->execute($sql);
    }
];
