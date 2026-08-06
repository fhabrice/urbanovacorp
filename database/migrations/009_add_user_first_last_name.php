<?php

/**
 * Migration: Add first_name and last_name to users table and support promoter role.
 */

return [
    'up' => function($db) {
        $existingFirst = $db->fetchOne("SHOW COLUMNS FROM users LIKE 'first_name'");
        if (!$existingFirst) {
            $db->execute("ALTER TABLE users ADD COLUMN first_name VARCHAR(100) NOT NULL DEFAULT '' AFTER password");
        }

        $existingLast = $db->fetchOne("SHOW COLUMNS FROM users LIKE 'last_name'");
        if (!$existingLast) {
            $db->execute("ALTER TABLE users ADD COLUMN last_name VARCHAR(100) NOT NULL DEFAULT '' AFTER first_name");
        }

        $existingRole = $db->fetchOne("SHOW COLUMNS FROM users LIKE 'role'");
        if ($existingRole) {
            $db->execute("ALTER TABLE users MODIFY role ENUM('admin', 'investor', 'promoter', 'project_owner') NOT NULL DEFAULT 'promoter'");
        }

        $db->execute(
            "UPDATE users SET first_name = TRIM(SUBSTRING_INDEX(name, ' ', 1)), last_name = TRIM(SUBSTRING(name, LENGTH(TRIM(SUBSTRING_INDEX(name, ' ', 1))) + 2)) WHERE name IS NOT NULL AND name != ''"
        );
    },
    'down' => function($db) {
        $existingFirst = $db->fetchOne("SHOW COLUMNS FROM users LIKE 'first_name'");
        if ($existingFirst) {
            $db->execute("ALTER TABLE users DROP COLUMN first_name");
        }

        $existingLast = $db->fetchOne("SHOW COLUMNS FROM users LIKE 'last_name'");
        if ($existingLast) {
            $db->execute("ALTER TABLE users DROP COLUMN last_name");
        }

        $existingRole = $db->fetchOne("SHOW COLUMNS FROM users LIKE 'role'");
        if ($existingRole) {
            $db->execute("ALTER TABLE users MODIFY role ENUM('admin', 'investor', 'project_owner') NOT NULL DEFAULT 'investor'");
        }
    }
];
