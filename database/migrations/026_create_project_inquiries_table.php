<?php

/**
 * Migration: Create project_inquiries table
 * Module Marketplace - demandes d'informations envoyées par les visiteurs
 * sur un projet précis, gérées par Urbanova
 */

return [
    'up' => function($db) {
        $sql = "
            CREATE TABLE IF NOT EXISTS project_inquiries (
                id INT AUTO_INCREMENT PRIMARY KEY,
                project_id INT NOT NULL,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(50),
                message TEXT,
                status ENUM('new', 'in_progress', 'resolved', 'closed') DEFAULT 'new',
                admin_note TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
                INDEX idx_project_id (project_id),
                INDEX idx_status (status),
                INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $db->execute($sql);
    },
    'down' => function($db) {
        $sql = "DROP TABLE IF EXISTS project_inquiries";
        $db->execute($sql);
    }
];
