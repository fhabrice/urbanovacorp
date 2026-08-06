<?php

/**
 * Migration: Add investor_type and additional status values to investors table
 */

return [
    'up' => function($db) {
        $existing = $db->fetchOne("SHOW COLUMNS FROM investors LIKE 'investor_type'");
        if (!$existing) {
            $db->execute("ALTER TABLE investors ADD COLUMN investor_type ENUM('business_angel', 'individual', 'family_office', 'investment_fund', 'bank', 'investment_company', 'dfi', 'corporate_venture', 'other') NOT NULL DEFAULT 'individual'");
        }
        $existing = $db->fetchOne("SHOW COLUMNS FROM investors LIKE 'investor_status'");
        if (!$existing) {
            $db->execute("ALTER TABLE investors ADD COLUMN investor_status ENUM('pending', 'approved', 'rejected', 'additional_info') NOT NULL DEFAULT 'pending'");
        }
    },
    'down' => function($db) {
        $db->execute("ALTER TABLE investors DROP COLUMN investor_type");
        $db->execute("ALTER TABLE investors DROP COLUMN investor_status");
    }
];
