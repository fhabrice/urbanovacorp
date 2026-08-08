<?php

/**
 * Migration: Create funding campaigns table
 * For managing fundraising campaigns
 */

return [
    'up' => function($db) {
        $sql = "
            CREATE TABLE IF NOT EXISTS funding_campaigns (
                id INT AUTO_INCREMENT PRIMARY KEY,
                project_id INT NOT NULL,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                target_amount DECIMAL(15,2) NOT NULL,
                minimum_investment DECIMAL(15,2),
                maximum_investment DECIMAL(15,2),
                commission_rate DECIMAL(5,2) DEFAULT 3.00,
                start_date DATE NOT NULL,
                end_date DATE NOT NULL,
                status ENUM('draft', 'active', 'paused', 'completed', 'cancelled') DEFAULT 'draft',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
                INDEX idx_project_id (project_id),
                INDEX idx_status (status),
                INDEX idx_dates (start_date, end_date)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $db->execute($sql);
    },
    'down' => function($db) {
        $sql = "DROP TABLE IF EXISTS funding_campaigns";
        $db->execute($sql);
    }
];
