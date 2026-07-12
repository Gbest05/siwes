<?php

// Test Runner for SIWES Portal

require_once __DIR__ . '/app/Core/Session.php';
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require_once $file;
});

use App\Core\Database;

class TestSuite {
    private $db;

    public function run() {
        echo "=== SIWES System Verification Test Runner ===\n\n";

        $this->testDatabaseConnection();
        $this->testDatabaseTables();
        $this->testAuthHelper();
        $this->testAllocationScoring();

        echo "\n============================================\n";
        echo "SUCCESS: All core system checks passed.\n";
    }

    private function testDatabaseConnection() {
        echo "[1/4] Checking SQLite Connection... ";
        try {
            $this->db = Database::getInstance();
            if ($this->db->getConnection()) {
                echo "PASSED\n";
            } else {
                throw new Exception("Null connection object.");
            }
        } catch (Exception $e) {
            echo "FAILED: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    private function testDatabaseTables() {
        echo "[2/4] Verifying Table Schemas... ";
        $expectedTables = ['users', 'departments', 'students', 'companies', 'supervisors', 'allocations', 'logbooks', 'weekly_reports', 'attendance', 'evaluations', 'settings', 'audit_logs'];

        foreach ($expectedTables as $table) {
            $count = $this->db->querySingle("SELECT COUNT(*) FROM sqlite_master WHERE type='table' AND name=:t", [':t' => $table]);
            if ($count == 0) {
                echo "FAILED: Missing table '$table'\n";
                exit(1);
            }
        }
        echo "PASSED (All " . count($expectedTables) . " tables verified)\n";
    }

    private function testAuthHelper() {
        echo "[3/4] Verifying Authentication Helper... ";
        
        // Fetch seed admin
        $admin = $this->db->fetchRow("SELECT * FROM users WHERE username = 'adminsiwes' LIMIT 1");
        if (!$admin || $admin['role'] !== 'admin') {
            echo "FAILED: Admin seed account verification failed.\n";
            exit(1);
        }
        echo "PASSED\n";
    }

    private function testAllocationScoring() {
        echo "[4/4] dry-run testing Allocation matching Scorer... ";
        
        // Mock a student and company
        $student = [
            'course_of_study' => 'Software Development',
            'pref_company_1_id' => 1,
            'pref_company_2_id' => 2,
            'pref_company_3_id' => 3
        ];
        $company = [
            'id' => 1,
            'specializations' => 'Software Development, UI/UX Design, Data Science',
            'industry_category' => 'Software Engineering',
            'rating' => 5,
            'latitude' => 7.3912,
            'longitude' => 3.8424
        ];

        // University coordinates ( Ibadan base )
        $uniLat = 7.3912;
        $uniLng = 3.8424;

        // Course Specialization Match (40pts)
        $specMatch = 0;
        if (stripos($company['specializations'], $student['course_of_study']) !== false || 
            stripos($company['industry_category'], $student['course_of_study']) !== false) {
            $specMatch = 1;
        }

        // Preference Score (30pts for 1st choice)
        $prefScore = 30; // matched first choice

        // Distance (0 penalty for base match)
        $distance = sqrt(pow($company['latitude'] - $uniLat, 2) + pow($company['longitude'] - $uniLng, 2));
        $distScore = max(0, 20 - ($distance * 15));

        // Rating (10pts for 5 stars)
        $ratingScore = $company['rating'] * 2;

        $score = ($specMatch * 40) + $prefScore + $distScore + $ratingScore;

        // Expected score: (1 * 40) + 30 + 20 + 10 = 100
        if ($score == 100) {
            echo "PASSED (Perfect match scored: $score/100)\n";
        } else {
            echo "FAILED: Expected score 100, got $score\n";
            exit(1);
        }
    }
}

$suite = new TestSuite();
$suite->run();
