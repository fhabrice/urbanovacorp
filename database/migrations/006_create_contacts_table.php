<?php

/**
 * Migration: Create contacts table
 */

return [
    'up' => function($db) {
        $sql = "
            CREATE TABLE IF NOT EXISTS contacts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(50),
                company VARCHAR(255),
                subject VARCHAR(255),
                message TEXT NOT NULL,
                type ENUM('quote', 'partnership', 'general', 'investor') NOT NULL DEFAULT 'general',
                status ENUM('new', 'in_progress', 'resolved', 'closed') NOT NULL DEFAULT 'new',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_email (email),
                INDEX idx_status (status),
                INDEX idx_type (type)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $db->execute($sql);
    },
    'down' => function($db) {
        $sql = "DROP TABLE IF EXISTS contacts";
        $db->execute($sql);
    }
];
