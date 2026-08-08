<?php

/**
 * Migration: Create role_permissions table for RBAC
 */

return [
    'up' => function($db) {
        $sql = "
            CREATE TABLE IF NOT EXISTS role_permissions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                role ENUM('super_admin', 'admin', 'project_manager', 'promoter', 'investor', 'client') NOT NULL,
                permission_id INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
                UNIQUE KEY unique_role_permission (role, permission_id),
                INDEX idx_role (role),
                INDEX idx_permission_id (permission_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $db->execute($sql);

        // Assign default permissions to roles
        $rolePermissions = [
            // Super Admin - all permissions
            ['super_admin', 1], ['super_admin', 2], ['super_admin', 3], ['super_admin', 4], 
            ['super_admin', 5], ['super_admin', 6],
            // Admin - most admin permissions
            ['admin', 1], ['admin', 2], ['admin', 3], ['admin', 4], ['admin', 5], ['admin', 6],
            // Project Manager - project management
            ['project_manager', 2], ['project_manager', 3], ['project_manager', 6],
            // Promoter - manage own projects
            ['promoter', 7],
            // Investor - view and invest
            ['investor', 8], ['investor', 9], ['investor', 10],
            // Client - marketplace access
            ['client', 11], ['client', 12], ['client', 13],
        ];

        foreach ($rolePermissions as $rp) {
            $db->execute(
                "INSERT INTO role_permissions (role, permission_id) VALUES (?, ?)",
                $rp
            );
        }
    },
    'down' => function($db) {
        $sql = "DROP TABLE IF EXISTS role_permissions";
        $db->execute($sql);
    }
];
