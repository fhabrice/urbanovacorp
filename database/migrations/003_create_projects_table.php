<?php

/**
 * Migration: Create projects table
 */

return [
    'up' => function($db) {
        $sql = "
            CREATE TABLE IF NOT EXISTS projects (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                title VARCHAR(255) NOT NULL,
                description TEXT NOT NULL,
                type ENUM('residential', 'commercial', 'mixed_use', 'infrastructure', 'industrial') NOT NULL,
                sector VARCHAR(100),
                country VARCHAR(100) NOT NULL,
                city VARCHAR(100) NOT NULL,
                address TEXT,
                total_cost DECIMAL(15,2) NOT NULL,
                equity_contribution DECIMAL(15,2),
                funding_sought DECIMAL(15,2) NOT NULL,
                funding_mobilized DECIMAL(15,2) DEFAULT 0,
                roi DECIMAL(5,2),
                tri DECIMAL(5,2),
                payback_period INT,
                project_duration INT,
                housing_units INT DEFAULT 0,
                jobs_created INT DEFAULT 0,
                status ENUM('draft', 'submitted', 'under_review', 'approved', 'rejected', 'funded', 'completed') NOT NULL DEFAULT 'draft',
                image VARCHAR(255),
                business_plan_path VARCHAR(255),
                pitch_deck_path VARCHAR(255),
                financial_model_path VARCHAR(255),
                feasibility_study_path VARCHAR(255),
                land_title_path VARCHAR(255),
                plans_path VARCHAR(255),
                permits_path VARCHAR(255),
                submitted_at TIMESTAMP NULL,
                reviewed_at TIMESTAMP NULL,
                reviewed_by INT NULL,
                rejection_reason TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
                INDEX idx_user_id (user_id),
                INDEX idx_status (status),
                INDEX idx_type (type),
                INDEX idx_country (country),
                INDEX idx_city (city),
                INDEX idx_sector (sector)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $db->execute($sql);
    },
    'down' => function($db) {
        $sql = "DROP TABLE IF EXISTS projects";
        $db->execute($sql);
    }
];
