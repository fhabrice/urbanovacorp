<?php

/**
 * Migration: Add marketplace project fields
 * Add: video_url, virtual_tour_url, google_maps_embed, brochure_path, availability, price
 */

return [
    'up' => function($db) {
        $sql = "
            ALTER TABLE projects 
            ADD COLUMN video_url VARCHAR(500) NULL AFTER image,
            ADD COLUMN virtual_tour_url VARCHAR(500) NULL AFTER video_url,
            ADD COLUMN google_maps_embed TEXT NULL AFTER virtual_tour_url,
            ADD COLUMN brochure_path VARCHAR(255) NULL AFTER google_maps_embed,
            ADD COLUMN availability VARCHAR(100) DEFAULT 'available' AFTER brochure_path,
            ADD COLUMN price DECIMAL(15,2) NULL AFTER availability,
            ADD COLUMN latitude DECIMAL(10,8) NULL AFTER price,
            ADD COLUMN longitude DECIMAL(11,8) NULL AFTER latitude
        ";
        $db->execute($sql);
    },
    'down' => function($db) {
        $sql = "
            ALTER TABLE projects 
            DROP COLUMN video_url,
            DROP COLUMN virtual_tour_url,
            DROP COLUMN google_maps_embed,
            DROP COLUMN brochure_path,
            DROP COLUMN availability,
            DROP COLUMN price,
            DROP COLUMN latitude,
            DROP COLUMN longitude
        ";
        $db->execute($sql);
    }
];
