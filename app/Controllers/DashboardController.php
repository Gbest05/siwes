<?php

namespace App\Controllers;

use App\Core\Response;
use App\Core\Auth;
use App\Core\Database;

class DashboardController {
    public function admin() {
        Auth::requireRole('admin');
        $db = Database::getInstance();

        // 1. Fetch metrics
        $metrics = [
            'total_students' => $db->querySingle("SELECT COUNT(*) FROM students"),
            'total_companies' => $db->querySingle("SELECT COUNT(*) FROM companies"),
            'total_supervisors' => $db->querySingle("SELECT COUNT(*) FROM supervisors WHERE type = 'company'"),
            'pending_applications' => $db->querySingle("SELECT COUNT(*) FROM companies WHERE status = 'pending'"),
            'approved_applications' => $db->querySingle("SELECT COUNT(*) FROM companies WHERE status = 'verified'"),
            'available_slots' => $db->querySingle("SELECT SUM(available_slots) FROM companies WHERE status = 'verified'"),
            'total_allocations' => $db->querySingle("SELECT COUNT(*) FROM allocations WHERE status = 'active' OR status = 'locked'"),
            'completed_siwes' => $db->querySingle("SELECT COUNT(*) FROM allocations WHERE status = 'completed'")
        ];

        // 2. Fetch Recent Activities (Audit Logs)
        $activities = $db->fetchAll("SELECT a.*, u.username FROM audit_logs a LEFT JOIN users u ON a.user_id = u.id ORDER BY a.created_at DESC LIMIT 10");

        // 3. Fetch Department Statistics (for charts)
        $deptStats = $db->fetchAll("SELECT d.code, COUNT(s.id) as count FROM departments d LEFT JOIN students s ON s.department_id = d.id GROUP BY d.id");

        // 4. Fetch Company Capacity (for charts)
        $companyCapacity = $db->fetchAll("SELECT name, available_slots FROM companies WHERE status = 'verified' LIMIT 5");

        // 5. Fetch Allocation Types
        $allocationTypes = [
            'auto' => $db->querySingle("SELECT COUNT(*) FROM allocations WHERE allocated_by = 'auto'"),
            'manual' => $db->querySingle("SELECT COUNT(*) FROM allocations WHERE allocated_by = 'manual'")
        ];

        $response = new Response();
        return $response->render('dashboards/admin', [
            'metrics' => $metrics,
            'activities' => $activities,
            'deptStats' => $deptStats,
            'companyCapacity' => $companyCapacity,
            'allocationTypes' => $allocationTypes
        ]);
    }

    public function coordinator() {
        Auth::requireRole('coordinator');
        $db = Database::getInstance();

        // Metrics similar to admin
        $metrics = [
            'total_students' => $db->querySingle("SELECT COUNT(*) FROM students"),
            'total_companies' => $db->querySingle("SELECT COUNT(*) FROM companies"),
            'unallocated_students' => $db->querySingle("SELECT COUNT(*) FROM students WHERE siwes_status = 'unallocated'"),
            'allocated_students' => $db->querySingle("SELECT COUNT(*) FROM students WHERE siwes_status = 'allocated'"),
            'pending_companies' => $db->querySingle("SELECT COUNT(*) FROM companies WHERE status = 'pending'"),
            'pending_logbooks' => $db->querySingle("SELECT COUNT(*) FROM logbooks WHERE supervisor_status = 'pending'")
        ];

        // Fetch pending students approval list
        $pendingStudents = $db->fetchAll("SELECT s.*, u.email, d.code as dept_code FROM students s JOIN users u ON s.user_id = u.id JOIN departments d ON s.department_id = d.id WHERE s.siwes_status = 'unallocated' LIMIT 5");

        // Fetch pending companies
        $pendingCompanies = $db->fetchAll("SELECT * FROM companies WHERE status = 'pending' LIMIT 5");

        $response = new Response();
        return $response->render('dashboards/coordinator', [
            'metrics' => $metrics,
            'pendingStudents' => $pendingStudents,
            'pendingCompanies' => $pendingCompanies
        ]);
    }

    public function student() {
        Auth::requireRole('student');
        $db = Database::getInstance();
        $user = Auth::user();
        $studentId = $user['profile']['id'];

        // Get student's allocation details
        $allocation = $db->fetchRow("SELECT a.*, c.name as company_name, c.email as company_email, c.phone as company_phone, c.address as company_address, s.first_name || ' ' || s.last_name as supervisor_name, s.phone as supervisor_phone FROM allocations a JOIN companies c ON a.company_id = c.id LEFT JOIN supervisors s ON a.supervisor_id = s.id WHERE a.student_id = :sid LIMIT 1", [
            ':sid' => $studentId
        ]);

        // Get logbook progression
        $totalLogsCount = $db->querySingle("SELECT COUNT(*) FROM logbooks WHERE student_id = :sid", [':sid' => $studentId]);
        $approvedLogsCount = $db->querySingle("SELECT COUNT(*) FROM logbooks WHERE student_id = :sid AND supervisor_status = 'approved'", [':sid' => $studentId]);

        // Weekly summaries
        $weeklyReports = $db->fetchAll("SELECT * FROM weekly_reports WHERE student_id = :sid ORDER BY week_number DESC", [':sid' => $studentId]);

        // Recent Notifications
        $notifications = $db->fetchAll("SELECT * FROM notifications WHERE user_id = :uid ORDER BY created_at DESC LIMIT 5", [':uid' => $user['id']]);

        // Evaluation status
        $evaluation = $db->fetchRow("SELECT * FROM evaluations WHERE student_id = :sid LIMIT 1", [':sid' => $studentId]);

        $response = new Response();
        return $response->render('dashboards/student', [
            'user' => $user,
            'allocation' => $allocation,
            'progress' => [
                'total' => $totalLogsCount,
                'approved' => $approvedLogsCount,
                'percentage' => $totalLogsCount > 0 ? round(($approvedLogsCount / 120) * 100) : 0 // 120 days is typical 6 months SIWES
            ],
            'weeklyReports' => $weeklyReports,
            'notifications' => $notifications,
            'evaluation' => $evaluation
        ]);
    }

    public function supervisor() {
        Auth::requireRole('supervisor');
        $db = Database::getInstance();
        $user = Auth::user();
        $supervisorId = $user['profile']['id'];
        $companyId = $user['profile']['company_id'];

        // Get assigned students
        $assignedStudents = $db->fetchAll("SELECT s.*, a.status as allocation_status, a.id as allocation_id, d.code as dept_code FROM allocations a JOIN students s ON a.student_id = s.id JOIN departments d ON s.department_id = d.id WHERE a.supervisor_id = :svid AND a.company_id = :cid", [
            ':svid' => $supervisorId,
            ':cid' => $companyId
        ]);

        // Get pending logbooks that require verification
        $pendingLogs = $db->fetchAll("SELECT l.*, s.first_name || ' ' || s.last_name as student_name, s.matric_no FROM logbooks l JOIN students s ON l.student_id = s.id JOIN allocations a ON a.student_id = s.id WHERE a.supervisor_id = :svid AND l.supervisor_status = 'pending'", [
            ':svid' => $supervisorId
        ]);

        $response = new Response();
        return $response->render('dashboards/supervisor', [
            'students' => $assignedStudents,
            'pendingLogs' => $pendingLogs
        ]);
    }

    public function officer() {
        Auth::requireRole('officer');
        $db = Database::getInstance();
        $user = Auth::user();

        // Match department using email or user metadata
        // For our seed officer (officer_cs), it maps to CS department. Let's find it.
        $dept = $db->fetchRow("SELECT * FROM departments WHERE code = 'CSC' LIMIT 1");
        $deptId = $dept ? $dept['id'] : 1;

        $stats = [
            'total_students' => $db->querySingle("SELECT COUNT(*) FROM students WHERE department_id = :d", [':d' => $deptId]),
            'allocated_students' => $db->querySingle("SELECT COUNT(*) FROM students WHERE department_id = :d AND siwes_status = 'allocated'", [':d' => $deptId]),
            'unallocated_students' => $db->querySingle("SELECT COUNT(*) FROM students WHERE department_id = :d AND siwes_status = 'unallocated'", [':d' => $deptId]),
        ];

        // Fetch students list
        $students = $db->fetchAll("SELECT s.*, u.email FROM students s JOIN users u ON s.user_id = u.id WHERE s.department_id = :d", [':d' => $deptId]);

        $response = new Response();
        return $response->render('dashboards/officer', [
            'department' => $dept,
            'stats' => $stats,
            'students' => $students
        ]);
    }
}
