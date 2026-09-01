<?php

/**
 * Migration: Create funding_requests table
 * Module Levée de fonds - demande de mandat soumise par un porteur de projet
 * et étudiée par Urbanova (étude préalable -> acceptation / compléments / refus)
 */

return [
    'up' => function($db) {
        $sql = "
            CREATE TABLE IF NOT EXISTS funding_requests (
                id INT AUTO_INCREMENT PRIMARY KEY,
                project_id INT NOT NULL,
                user_id INT NOT NULL,
                summary TEXT,
                amount_requested DECIMAL(15,2),
                status ENUM('pending_study', 'under_review', 'accepted', 'requested_info', 'rejected', 'closed', 'cancelled') DEFAULT 'pending_study',
                commission_rate DECIMAL(5,2) NULL,
                mandate_reference VARCHAR(100) NULL,
                mandate_date DATE NULL,
                admin_notes TEXT,
                decided_by INT NULL,
                decided_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (decided_by) REFERENCES users(id) ON DELETE SET NULL,
                INDEX idx_project_id (project_id),
                INDEX idx_user_id (user_id),
                INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $db->execute($sql);
    },
    'down' => function($db) {
        $sql = "DROP TABLE IF EXISTS funding_requests";
        $db->execute($sql);
    }
];
