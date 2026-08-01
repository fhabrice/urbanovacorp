<?php

/**
 * Migration: Create investor_interests table
 */

return [
    'up' => function($db) {
        $sql = "
            CREATE TABLE IF NOT EXISTS investor_interests (
                id INT AUTO_INCREMENT PRIMARY KEY,
                project_id INT NOT NULL,
                investor_id INT NOT NULL,
                interest_type ENUM('view', 'inquiry', 'nda_signed', 'negotiation') NOT NULL DEFAULT 'view',
                investment_amount DECIMAL(15,2),
                message TEXT,
                nda_signed BOOLEAN DEFAULT FALSE,
                nda_signed_at TIMESTAMP NULL,
                status ENUM('pending', 'accepted', 'rejected', 'withdrawn') NOT NULL DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
                FOREIGN KEY (investor_id) REFERENCES investors(id) ON DELETE CASCADE,
                INDEX idx_project_id (project_id),
                INDEX idx_investor_id (investor_id),
                INDEX idx_status (status),
                UNIQUE KEY unique_project_investor (project_id, investor_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $db->execute($sql);
    },
    'down' => function($db) {
        $sql = "DROP TABLE IF EXISTS investor_interests";
        $db->execute($sql);
    }
];
