<?php

/**
 * Migration: Create investors table (KYC/KYB data)
 */

return [
    'up' => function($db) {
        $sql = "
            CREATE TABLE IF NOT EXISTS investors (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                type ENUM('individual', 'corporate') NOT NULL,
                investor_status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
                nationality VARCHAR(100),
                phone VARCHAR(50),
                address TEXT,
                city VARCHAR(100),
                country VARCHAR(100),
                id_document_type VARCHAR(50),
                id_document_number VARCHAR(100),
                id_document_expiry DATE,
                company_name VARCHAR(255),
                company_registration_number VARCHAR(100),
                company_tax_id VARCHAR(100),
                investment_capacity DECIMAL(15,2),
                investment_sectors TEXT,
                risk_profile ENUM('conservative', 'moderate', 'aggressive'),
                kyc_documents JSON,
                kyc_submitted_at TIMESTAMP NULL,
                kyc_reviewed_at TIMESTAMP NULL,
                kyc_reviewed_by INT NULL,
                rejection_reason TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (kyc_reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
                INDEX idx_user_id (user_id),
                INDEX idx_investor_status (investor_status),
                INDEX idx_type (type)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $db->execute($sql);
    },
    'down' => function($db) {
        $sql = "DROP TABLE IF EXISTS investors";
        $db->execute($sql);
    }
];
