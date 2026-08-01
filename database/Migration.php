<?php

/**
 * Migration Runner
 */

class Migration
{
    private $db;
    private $migrationsPath;

    public function __construct($db, $migrationsPath)
    {
        $this->db = $db;
        $this->migrationsPath = $migrationsPath;
    }

    public function run()
    {
        // Create migrations table if not exists
        $this->createMigrationsTable();

        // Get all migration files
        $files = glob($this->migrationsPath . '/*.php');
        sort($files);

        foreach ($files as $file) {
            $migrationName = basename($file, '.php');
            
            // Check if migration already ran
            if (!$this->hasRun($migrationName)) {
                echo "Running migration: $migrationName\n";
                
                $migration = require $file;
                
                try {
                    $migration['up']($this->db);
                    $this->markAsRun($migrationName);
                    echo "Migration completed: $migrationName\n";
                } catch (\Exception $e) {
                    echo "Migration failed: $migrationName - " . $e->getMessage() . "\n";
                    throw $e;
                }
            }
        }
    }

    public function rollback($steps = 1)
    {
        // Get last ran migrations
        $ranMigrations = $this->getRanMigrations($steps);

        foreach (array_reverse($ranMigrations) as $migrationName) {
            $file = $this->migrationsPath . '/' . $migrationName . '.php';
            
            if (file_exists($file)) {
                echo "Rolling back migration: $migrationName\n";
                
                $migration = require $file;
                
                try {
                    $migration['down']($this->db);
                    $this->removeFromRan($migrationName);
                    echo "Rollback completed: $migrationName\n";
                } catch (\Exception $e) {
                    echo "Rollback failed: $migrationName - " . $e->getMessage() . "\n";
                    throw $e;
                }
            }
        }
    }

    private function createMigrationsTable()
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                ran_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_migration (migration)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";
        $this->db->execute($sql);
    }

    private function hasRun($migrationName)
    {
        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM migrations WHERE migration = ?",
            [$migrationName]
        );
        return $result['count'] > 0;
    }

    private function markAsRun($migrationName)
    {
        $this->db->execute(
            "INSERT INTO migrations (migration) VALUES (?)",
            [$migrationName]
        );
    }

    private function removeFromRan($migrationName)
    {
        $this->db->execute(
            "DELETE FROM migrations WHERE migration = ?",
            [$migrationName]
        );
    }

    private function getRanMigrations($limit)
    {
        return $this->db->fetchAll(
            "SELECT migration FROM migrations ORDER BY ran_at DESC LIMIT ?",
            [$limit]
        );
    }
}
