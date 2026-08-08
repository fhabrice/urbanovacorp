<?php

/**
 * Migration: Fix missing investor records
 * This migration creates investor records for users with investor role but no investor record
 */

return [
    'up' => function($db) {
        // Find users with investor role but no investor record
        $sql = "
            INSERT INTO investors (user_id, type, investor_type, investor_status, created_at, updated_at)
            SELECT u.id, 'individual', 'individual', 'pending', NOW(), NOW()
            FROM users u
            WHERE u.role = 'investor'
            AND NOT EXISTS (SELECT 1 FROM investors i WHERE i.user_id = u.id)
        ";
        $db->execute($sql);
        
        // Log how many records were created
        $countSql = "
            SELECT COUNT(*) as count
            FROM users u
            WHERE u.role = 'investor'
            AND NOT EXISTS (SELECT 1 FROM investors i WHERE i.user_id = u.id)
        ";
        $result = $db->fetchOne($countSql);
        error_log('Migration 009: Created ' . (0 - $result['count']) . ' investor records');
    },
    'down' => function($db) {
        // This migration cannot be safely reversed as we don't know which records were created
        error_log('Migration 009: Cannot be safely reversed');
    }
];
