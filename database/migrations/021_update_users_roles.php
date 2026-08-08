<?php

/**
 * Migration: Update users table to support additional roles
 * Add: super_admin, project_manager, client
 */

return [
    'up' => function($db) {
        $sql = "
            ALTER TABLE users 
            MODIFY COLUMN role ENUM('admin', 'super_admin', 'project_manager', 'promoter', 'investor', 'client') 
            NOT NULL DEFAULT 'investor'
        ";
        $db->execute($sql);
    },
    'down' => function($db) {
        $sql = "
            ALTER TABLE users 
            MODIFY COLUMN role ENUM('admin', 'promoter', 'investor') 
            NOT NULL DEFAULT 'investor'
        ";
        $db->execute($sql);
    }
];
