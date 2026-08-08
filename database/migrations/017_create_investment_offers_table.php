<?php

/**
 * Migration: Create investment offers table
 * For formal investment offers from investors
 */

return [
    'up' => function($db) {
        $sql = "
            CREATE TABLE IF NOT EXISTS investment_offers (
                id INT AUTO_INCREMENT PRIMARY KEY,
                campaign_id INT NOT NULL,
                investor_id INT NOT NULL,
                amount DECIMAL(15,2) NOT NULL,
                message TEXT,
                status ENUM('pending', 'under_review', 'accepted', 'rejected', 'withdrawn') DEFAULT 'pending',
                reviewed_at TIMESTAMP NULL,
                reviewed_by INT NULL,
                rejection_reason TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (campaign_id) REFERENCES funding_campaigns(id) ON DELETE CASCADE,
                FOREIGN KEY (investor_id) REFERENCES investors(id) ON DELETE CASCADE,
                FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
                INDEX idx_campaign_id (campaign_id),
                INDEX idx_investor_id (investor_id),
                INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $db->execute($sql);
    },
    'down' => function($db) {
        $sql = "DROP TABLE IF EXISTS investment_offers";
        $db->execute($sql);
    }
];
