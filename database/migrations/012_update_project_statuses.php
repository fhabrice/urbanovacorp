<?php

/**
 * Migration: Update project statuses to match marketplace requirements
 * Add: published, suspended, sold, rented, archived
 */

return [
    'up' => function($db) {
        // Modify the status ENUM to include all required statuses
        $sql = "
            ALTER TABLE projects 
            MODIFY COLUMN status ENUM('draft', 'submitted', 'under_review', 'approved', 'rejected', 'funded', 'completed', 'published', 'suspended', 'sold', 'rented', 'archived') 
            NOT NULL DEFAULT 'draft'
        ";
        $db->execute($sql);
    },
    'down' => function($db) {
        // Revert to original statuses
        $sql = "
            ALTER TABLE projects 
            MODIFY COLUMN status ENUM('draft', 'submitted', 'under_review', 'approved', 'rejected', 'funded', 'completed') 
            NOT NULL DEFAULT 'draft'
        ";
        $db->execute($sql);
    }
];
