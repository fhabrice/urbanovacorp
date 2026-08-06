<?php

return [
    'up' => function($db) {
        $existing = $db->fetchOne("SHOW COLUMNS FROM projects LIKE 'user_id'");
        if (!$existing) {
            $db->execute("ALTER TABLE projects ADD COLUMN user_id INT NULL AFTER id");
            $db->execute("UPDATE projects SET user_id = promoter_id WHERE user_id IS NULL");
            $db->execute("ALTER TABLE projects MODIFY COLUMN user_id INT NOT NULL");
            $db->execute("ALTER TABLE projects ADD INDEX idx_user_id (user_id)");
            $db->execute("ALTER TABLE projects ADD CONSTRAINT projects_ibfk_2 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE");
        }
    },
    'down' => function($db) {
        $existing = $db->fetchOne("SHOW COLUMNS FROM projects LIKE 'user_id'");
        if ($existing) {
            $db->execute("ALTER TABLE projects DROP FOREIGN KEY projects_ibfk_2");
            $db->execute("ALTER TABLE projects DROP INDEX idx_user_id");
            $db->execute("ALTER TABLE projects DROP COLUMN user_id");
        }
    }
];
