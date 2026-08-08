<?php

/**
 * Migration: Create investor messages table
 * For communication between investors and Urbanova team
 */

return [
    'up' => function($db) {
        $sql = "
            CREATE TABLE IF NOT EXISTS investor_messages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                investor_id INT NOT NULL,
                subject VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                status ENUM('unread', 'read', 'replied') DEFAULT 'unread',
                admin_reply TEXT NULL,
                admin_replied_at TIMESTAMP NULL,
                admin_replied_by INT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (investor_id) REFERENCES investors(id) ON DELETE CASCADE,
                FOREIGN KEY (admin_replied_by) REFERENCES users(id) ON DELETE SET NULL,
                INDEX idx_investor_id (investor_id),
                INDEX idx_status (status),
                INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $db->execute($sql);
    },
    'down' => function($db) {
        $sql = "DROP TABLE IF EXISTS investor_messages";
        $db->execute($sql);
    }
];
