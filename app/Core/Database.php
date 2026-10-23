<?php

namespace App\Core;

use SQLite3;
use Exception;

class Database {
    private static $instance = null;
    private $db;

    private function __construct() {
        $dbPath = dirname(__DIR__) . '/Database/siwes.db';
        $isNew = !file_exists($dbPath) || filesize($dbPath) === 0;

        try {
            $this->db = new SQLite3($dbPath);
            $this->db->enableExceptions(true);
            $this->db->exec("PRAGMA foreign_keys = ON;");

            if ($isNew) {
                $this->initializeDatabase();
            }
        } catch (Exception $e) {
            die("Database Connection Error: " . $e->getMessage());
        }
    }

    private function initializeDatabase() {
        // 1. Run Schema
        $schemaPath = dirname(__DIR__) . '/Database/schema.sql';
        if (file_exists($schemaPath)) {
            $schemaSql = file_get_contents($schemaPath);
            $this->db->exec($schemaSql);
        }

        // 2. Run Seeder
        $seederPath = dirname(__DIR__) . '/Database/seeder.php';
        if (file_exists($seederPath)) {
            require_once $seederPath;
            ob_start();
            $seeder = new \Seeder($this->db);
            $seeder->seed();
            ob_end_clean();
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->db;
    }

    public function prepare($sql) {
        return $this->db->prepare($sql);
    }

    public function exec($sql) {
        return $this->db->exec($sql);
    }

    // Helper methods to simplify queries
    public function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        return $stmt->execute();
    }

    public function fetchAll($sql, $params = []) {
        $result = $this->query($sql, $params);
        $rows = [];
        if ($result) {
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    public function fetchRow($sql, $params = []) {
        $result = $this->query($sql, $params);
        if ($result) {
            return $result->fetchArray(SQLITE3_ASSOC);
        }
        return null;
    }

    public function querySingle($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $result = $stmt->execute();
        if ($result) {
            $row = $result->fetchArray(SQLITE3_NUM);
            return $row ? $row[0] : null;
        }
        return null;
    }

    public function lastInsertRowID() {
        return $this->db->lastInsertRowID();
    }

    public function escapeString($string) {
        return SQLite3::escapeString($string);
    }
}
