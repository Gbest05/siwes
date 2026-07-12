<?php

require_once __DIR__ . '/seeder.php';

function initDatabase() {
    $dbPath = __DIR__ . '/siwes.db';
    $isNew = !file_exists($dbPath);

    try {
        $db = new SQLite3($dbPath);
        $db->enableExceptions(true);
        
        // Enable Foreign Keys
        $db->exec("PRAGMA foreign_keys = ON;");

        // Read schema and execute
        $schemaSql = file_get_contents(__DIR__ . '/schema.sql');
        if ($schemaSql === false) {
            throw new Exception("Could not read schema.sql");
        }

        // Execute schema
        $db->exec($schemaSql);
        echo "Database schema initialized successfully.\n";

        // Seed if new database
        // We will check if the users table is empty to decide whether to seed
        $userCount = $db->querySingle("SELECT COUNT(*) FROM users;");
        if ($userCount == 0) {
            $seeder = new Seeder($db);
            $seeder->seed();
        } else {
            echo "Database already populated. Skipping seeding.\n";
        }

        return $db;

    } catch (Exception $e) {
        echo "Error initializing database: " . $e->getMessage() . "\n";
        exit(1);
    }
}

// Run directly if called from command line
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['PHP_SELF'])) {
    initDatabase();
}
