<?php

/**
 * Migration: Create project_documents table (Data Room)
 */

return [
    'up' => function($db) {
        $sql = "
            CREATE TABLE IF NOT EXISTS project_documents (
                id INT AUTO_INCREMENT PRIMARY KEY,
                project_id INT NOT NULL,
                document_type ENUM('business_plan', 'financial_model', 'due_diligence', 'legal', 'technical', 'other') NOT NULL,
                title VARCHAR(255) NOT NULL,
                file_path VARCHAR(255) NOT NULL,
                file_size INT,
                mime_type VARCHAR(100),
                is_confidential BOOLEAN DEFAULT TRUE,
                uploaded_by INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
                FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_project_id (project_id),
                INDEX idx_document_type (document_type)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $db->execute($sql);
    },
    'down' => function($db) {
        $sql = "DROP TABLE IF EXISTS project_documents";
        $db->execute($sql);
    }
];
