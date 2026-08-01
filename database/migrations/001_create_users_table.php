<?php

/**
 * Migration: Create users table
 */

return [
    'up' => function($db) {
        $sql = "
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                first_name VARCHAR(100) NOT NULL,
                last_name VARCHAR(100) NOT NULL,
                role ENUM('admin', 'investor', 'promoter') NOT NULL DEFAULT 'promoter',
                status ENUM('active', 'pending', 'suspended') NOT NULL DEFAULT 'pending',
                email_verified BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_email (email),
                INDEX idx_role (role),
                INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $db->execute($sql);
    },
    'down' => function($db) {
        $sql = "DROP TABLE IF EXISTS users";
        $db->execute($sql);
    }
];
