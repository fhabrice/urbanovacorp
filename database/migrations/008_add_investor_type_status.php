<?php

/**
 * Migration: Add investor_type and additional status values to investors table
 */

return [
    'up' => function($db) {
        $db->execute("ALTER TABLE investors ADD COLUMN IF NOT EXISTS investor_type ENUM('business_angel', 'individual', 'family_office', 'investment_fund', 'bank', 'investment_company', 'dfi', 'corporate_venture', 'other') NOT NULL DEFAULT 'individual'");
        $db->execute("ALTER TABLE investors ADD COLUMN IF NOT EXISTS investor_status ENUM('pending', 'approved', 'rejected', 'additional_info') NOT NULL DEFAULT 'pending'");
    },
    'down' => function($db) {
        $db->execute("ALTER TABLE investors DROP COLUMN investor_type");
        $db->execute("ALTER TABLE investors DROP COLUMN investor_status");
    }
];
