<?php

namespace App\Controllers;

use App\Core\Response;
use App\Core\Request;
use App\Core\Session;
use App\Core\Auth;
use App\Core\Database;

class AllocationController {
    public function index() {
        Auth::requireRole(['admin', 'coordinator']);
        $db = Database::getInstance();

        // Fetch all allocations
        $allocations = $db->fetchAll("SELECT a.*, s.first_name || ' ' || s.last_name as student_name, s.matric_no, d.code as dept_code, c.name as company_name, sup.first_name || ' ' || sup.last_name as supervisor_name FROM allocations a JOIN students s ON a.student_id = s.id JOIN departments d ON s.department_id = d.id JOIN companies c ON a.company_id = c.id LEFT JOIN supervisors sup ON a.supervisor_id = sup.id");

        // Fetch unallocated students (to assign manually)
        $unallocatedStudents = $db->fetchAll("SELECT s.*, d.code as dept_code FROM students s JOIN departments d ON s.department_id = d.id WHERE s.siwes_status = 'unallocated'");

        // Fetch verified companies with slots
        $companies = $db->fetchAll("SELECT * FROM companies WHERE status = 'verified' AND available_slots > 0");

        $response = new Response();
        return $response->render('allocations/index', [
            'allocations' => $allocations,
            'unallocated' => $unallocatedStudents,
            'companies' => $companies
        ]);
    }

    public function runAutoAllocation() {
        Auth::requireRole(['admin', 'coordinator']);
        $request = new Request();
        $response = new Response();

        if (!$request->isPost()) {
            return $response->redirect('/coordinator/allocations');
        }

        $db = Database::getInstance();

        // 1. Fetch unallocated students
        $students = $db->fetchAll("SELECT s.*, d.code as dept_code FROM students s JOIN departments d ON s.department_id = d.id WHERE s.siwes_status = 'unallocated'");
        
        // 2. Fetch companies with slots
        $companies = $db->fetchAll("SELECT * FROM companies WHERE status = 'verified' AND available_slots > 0");

        if (empty($students)) {
            Session::setFlash('warning', 'All students are already allocated.');
            return $response->redirect('/coordinator/allocations');
        }

        if (empty($companies)) {
            Session::setFlash('error', 'No company slots available for allocation.');
            return $response->redirect('/coordinator/allocations');
        }

        // University coordinates (Base location) - Idi-Ishin, Ibadan
        $uniLat = 7.3912;
        $uniLng = 3.8424;

        $allocationPairs = [];

        // 3. Compute score matrix
        foreach ($students as $student) {
            foreach ($companies as $company) {
                // Course Specialization Match
                $specMatch = 0;
                if (!empty($company['specializations'])) {
                    if (stripos($company['specializations'], $student['course_of_study']) !== false || 
                        stripos($company['industry_category'], $student['course_of_study']) !== false) {
                        $specMatch = 1;
                    }
                }

                // Preference Score (1st = 30pts, 2nd = 20pts, 3rd = 10pts)
                $prefScore = 0;
                if ($company['id'] == $student['pref_company_1_id']) {
                    $prefScore = 30;
                } elseif ($company['id'] == $student['pref_company_2_id']) {
                    $prefScore = 20;
                } elseif ($company['id'] == $student['pref_company_3_id']) {
                    $prefScore = 10;
                }

                // Distance penalty (clamped to max 20pts)
                $distance = sqrt(pow($company['latitude'] - $uniLat, 2) + pow($company['longitude'] - $uniLng, 2));
                // Scale distance so 1 degree (approx 111km) is penalized, standard distance is minor
                $distScore = max(0, 20 - ($distance * 15));

                // Company rating component (max 10pts)
                $ratingScore = $company['rating'] * 2;

                // Total score
                $totalScore = ($specMatch * 40) + $prefScore + $distScore + $ratingScore;

                $allocationPairs[] = [
                    'student_id' => $student['id'],
                    'company_id' => $company['id'],
                    'score' => $totalScore
                ];
            }
        }

        // 4. Sort pairs descending by score
        usort($allocationPairs, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        // Track allocations in this pass
        $allocatedStudents = [];
        $companySlots = [];
        foreach ($companies as $c) {
            $companySlots[$c['id']] = $c['available_slots'];
        }

        $allocatedCount = 0;
        $db->getConnection()->exec("BEGIN TRANSACTION;");

        try {
            // Get default academic supervisor
            $acadSupervisorId = $db->querySingle("SELECT id FROM supervisors WHERE type = 'institution' LIMIT 1") ?? 1;

            foreach ($allocationPairs as $pair) {
                $sid = $pair['student_id'];
                $cid = $pair['company_id'];

                if (in_array($sid, $allocatedStudents)) {
                    continue; // Student already allocated in this pass
                }

                if ($companySlots[$cid] <= 0) {
                    continue; // Company slots filled
                }

                // Find company supervisor
                $compSupervisorId = $db->querySingle("SELECT id FROM supervisors WHERE company_id = :cid LIMIT 1", [':cid' => $cid]);

                // Create Allocation
                $stmt = $db->getConnection()->prepare("INSERT INTO allocations (student_id, company_id, supervisor_id, institution_supervisor_id, status, allocated_by) VALUES (:sid, :cid, :svid, :asvid, 'active', 'auto')");
                $stmt->bindValue(':sid', $sid);
                $stmt->bindValue(':cid', $cid);
                $stmt->bindValue(':svid', $compSupervisorId);
                $stmt->bindValue(':asvid', $acadSupervisorId);
                $stmt->execute();

                // Update Student status
                $db->query("UPDATE students SET siwes_status = 'allocated' WHERE id = :sid", [':sid' => $sid]);

                // Decrement company slot in memory and database
                $companySlots[$cid]--;
                $db->query("UPDATE companies SET available_slots = available_slots - 1 WHERE id = :cid", [':cid' => $cid]);

                // Add notification
                $user = $db->fetchRow("SELECT user_id FROM students WHERE id = :sid LIMIT 1", [':sid' => $sid]);
                if ($user) {
                    $stmtNotif = $db->getConnection()->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (:uid, 'SIWES Allocated', 'You have been automatically allocated to your placement. Check your dashboard for details.', 'success')");
                    $stmtNotif->bindValue(':uid', $user['user_id']);
                    $stmtNotif->execute();
                }

                $allocatedStudents[] = $sid;
                $allocatedCount++;
            }

            $db->getConnection()->exec("COMMIT;");
            Session::setFlash('success', "Auto-allocation run finished! Successfully allocated $allocatedCount students.");
            
        } catch (\Exception $e) {
            $db->getConnection()->exec("ROLLBACK;");
            Session::setFlash('error', 'Auto-allocation failed: ' . $e->getMessage());
        }

        return $response->redirect('/coordinator/allocations');
    }

    public function manualAssign() {
        Auth::requireRole(['admin', 'coordinator']);
        $request = new Request();
        $response = new Response();

        if (!$request->isPost()) {
            return $response->redirect('/coordinator/allocations');
        }

        $sid = $request->post('student_id');
        $cid = $request->post('company_id');

        if (empty($sid) || empty($cid)) {
            Session::setFlash('error', 'Student and Company are required.');
            return $response->redirect('/coordinator/allocations');
        }

        $db = Database::getInstance();

        // Verify company availability
        $slots = $db->querySingle("SELECT available_slots FROM companies WHERE id = :cid", [':cid' => $cid]);
        if ($slots <= 0) {
            Session::setFlash('error', 'The selected company has no available slots.');
            return $response->redirect('/coordinator/allocations');
        }

        // Get supervisors
        $compSupervisorId = $db->querySingle("SELECT id FROM supervisors WHERE company_id = :cid LIMIT 1", [':cid' => $cid]);
        $acadSupervisorId = $db->querySingle("SELECT id FROM supervisors WHERE type = 'institution' LIMIT 1") ?? 1;

        $db->getConnection()->exec("BEGIN TRANSACTION;");
        try {
            // Delete any existing allocation first if overriding
            $db->query("DELETE FROM allocations WHERE student_id = :sid", [':sid' => $sid]);

            // Insert new allocation
            $stmt = $db->getConnection()->prepare("INSERT INTO allocations (student_id, company_id, supervisor_id, institution_supervisor_id, status, allocated_by) VALUES (:sid, :cid, :svid, :asvid, 'active', 'manual')");
            $stmt->bindValue(':sid', $sid);
            $stmt->bindValue(':cid', $cid);
            $stmt->bindValue(':svid', $compSupervisorId);
            $stmt->bindValue(':asvid', $acadSupervisorId);
            $stmt->execute();

            // Update student status
            $db->query("UPDATE students SET siwes_status = 'allocated' WHERE id = :sid", [':sid' => $sid]);

            // Decrement slots
            $db->query("UPDATE companies SET available_slots = available_slots - 1 WHERE id = :cid", [':cid' => $cid]);

            $db->getConnection()->exec("COMMIT;");
            Session::setFlash('success', 'Student manually allocated successfully.');
        } catch (\Exception $e) {
            $db->getConnection()->exec("ROLLBACK;");
            Session::setFlash('error', 'Allocation failed: ' . $e->getMessage());
        }

        return $response->redirect('/coordinator/allocations');
    }

    public function lockAllocation() {
        Auth::requireRole(['admin', 'coordinator']);
        $request = new Request();
        $response = new Response();

        if (!$request->isPost()) {
            return $response->redirect('/coordinator/allocations');
        }

        $aid = $request->post('allocation_id');
        $lock = $request->post('lock'); // 1 to lock, 0 to unlock

        if (empty($aid)) {
            Session::setFlash('error', 'Allocation ID is required.');
            return $response->redirect('/coordinator/allocations');
        }

        $status = ($lock == '1') ? 'locked' : 'active';
        $db = Database::getInstance();
        $db->query("UPDATE allocations SET status = :s WHERE id = :aid", [
            ':s' => $status,
            ':aid' => $aid
        ]);

        $msg = ($lock == '1') ? 'Allocation locked. It cannot be overridden by auto-allocation.' : 'Allocation unlocked.';
        Session::setFlash('success', $msg);
        return $response->redirect('/coordinator/allocations');
    }
}
