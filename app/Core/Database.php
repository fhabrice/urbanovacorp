<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private $config;
    private $connection;
    private $connected = false;

    public function __construct($config)
    {
        $this->config = $config;
        $this->connect();
    }

    private function connect()
    {
        $candidates = [];
        // Primary from config
        $candidates[] = $this->config;
        // Fallbacks for local dev
        $candidates[] = ['host'=>'localhost','port'=>'3306','database'=>'urbanova_db','username'=>'root','password'=>'','charset'=>'utf8mb4'];
        $candidates[] = ['host'=>'127.0.0.1','port'=>'3306','database'=>'urbanova_db','username'=>'root','password'=>'','charset'=>'utf8mb4'];
        $candidates[] = ['host'=>'localhost','port'=>'3306','database'=>'wqmetrvw_urbanova','username'=>'wqmetrvw_urbanova','password'=>'Goma@2019','charset'=>'utf8mb4'];

        $lastError = null;
        foreach ($candidates as $cfg) {
            try {
                $host = $cfg['host'] ?? $cfg['host'] ?? 'localhost';
                $port = $cfg['port'] ?? '3306';
                $dbname = $cfg['database'] ?? $cfg['name'] ?? 'urbanova_db';
                $charset = $cfg['charset'] ?? 'utf8mb4';
                $user = $cfg['username'] ?? $cfg['user'] ?? $cfg['username'] ?? null;
                $pass = $cfg['password'] ?? $cfg['pass'] ?? $cfg['password'] ?? null;

                $dsn = sprintf("mysql:host=%s;port=%s;dbname=%s;charset=%s", $host, $port, $dbname, $charset);

                $this->connection = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
                $this->connected = true;
                // Try simple query to confirm
                $this->connection->query("SELECT 1");
                return;
            } catch (PDOException $e) {
                $lastError = $e->getMessage();
                continue;
            }
        }
        // If all fail, set connection to null but don't throw hard — allow mock mode
        error_log("Database connection failed (all candidates): " . $lastError);
        $this->connection = null;
        $this->connected = false;
    }

    public function isConnected()
    {
        return $this->connected && $this->connection !== null;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function query($sql, $params = [])
    {
        if (!$this->isConnected()) {
            throw new \Exception("Database not connected");
        }
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new \Exception("Query failed: " . $e->getMessage() . " | SQL: $sql");
        }
    }

    public function fetchAll($sql, $params = [])
    {
        try {
            $stmt = $this->query($sql, $params);
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            // Mock fallback: return empty
            error_log($e->getMessage());
            return [];
        }
    }

    public function fetchOne($sql, $params = [])
    {
        try {
            $stmt = $this->query($sql, $params);
            $res = $stmt->fetch();
            return $res ?: [];
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function execute($sql, $params = [])
    {
        try {
            $stmt = $this->query($sql, $params);
            return $stmt->rowCount();
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return 0;
        }
    }

    public function lastInsertId()
    {
        if (!$this->isConnected()) return 0;
        return $this->connection->lastInsertId();
    }

    public function beginTransaction()
    {
        if (!$this->isConnected()) return false;
        return $this->connection->beginTransaction();
    }

    public function commit()
    {
        if (!$this->isConnected()) return false;
        return $this->connection->commit();
    }

    public function rollback()
    {
        if (!$this->isConnected()) return false;
        return $this->connection->rollBack();
    }
}
