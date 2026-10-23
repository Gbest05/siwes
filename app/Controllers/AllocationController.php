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

        // Fetch all allocations with both company supervisor and departmental supervisor
        $allocations = $db->fetchAll("SELECT a.*, s.first_name || ' ' || s.last_name as student_name, s.matric_no, s.year_of_study, d.code as dept_code, d.name as department_name, c.name as company_name, sup.first_name || ' ' || sup.last_name as supervisor_name, dsup.first_name || ' ' || dsup.last_name as dept_supervisor_name 
            FROM allocations a 
            JOIN students s ON a.student_id = s.id 
            JOIN departments d ON s.department_id = d.id 
            LEFT JOIN companies c ON a.company_id = c.id 
            LEFT JOIN supervisors sup ON a.supervisor_id = sup.id 
            LEFT JOIN supervisors dsup ON a.institution_supervisor_id = dsup.id 
            ORDER BY s.matric_no ASC");

        // Fetch unallocated students (to assign manually)
        $unallocatedStudents = $db->fetchAll("SELECT s.*, d.code as dept_code FROM students s JOIN departments d ON s.department_id = d.id WHERE s.siwes_status = 'unallocated'");

        // Fetch verified companies with slots
        $companies = $db->fetchAll("SELECT * FROM companies WHERE status = 'verified' AND available_slots > 0");

        // Fetch all verified companies for assignment
        $allCompanies = $db->fetchAll("SELECT * FROM companies WHERE status = 'verified' ORDER BY name ASC");

        // Fetch all supervisors separated by type
        $supervisors = $db->fetchAll("SELECT s.*, c.name as company_name FROM supervisors s LEFT JOIN companies c ON s.company_id = c.id ORDER BY s.first_name ASC");
        $companySupervisors = $db->fetchAll("SELECT s.*, c.name as company_name FROM supervisors s LEFT JOIN companies c ON s.company_id = c.id WHERE s.type = 'company' ORDER BY s.first_name ASC");
        $deptSupervisors = $db->fetchAll("SELECT s.*, d.name as department_name FROM supervisors s JOIN departments d ON s.department_id = d.id WHERE s.type = 'institution' ORDER BY s.first_name ASC");

        // Fetch all students
        $allStudents = $db->fetchAll("SELECT s.*, d.code as dept_code FROM students s JOIN departments d ON s.department_id = d.id ORDER BY s.matric_no ASC");

        $response = new Response();
        return $response->render('allocations/index', [
            'allocations' => $allocations,
            'unallocated' => $unallocatedStudents,
            'companies' => $companies,
            'allCompanies' => $allCompanies,
            'supervisors' => $supervisors,
            'companySupervisors' => $companySupervisors,
            'deptSupervisors' => $deptSupervisors,
            'allStudents' => $allStudents
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
            // Fetch available departmental supervisors for distribution
            $deptSupervisorsList = $db->fetchAll("SELECT id FROM supervisors WHERE type = 'institution' ORDER BY id ASC");
            $deptSupCount = !empty($deptSupervisorsList) ? count($deptSupervisorsList) : 1;

            foreach ($allocationPairs as $pair) {
                $sid = $pair['student_id'];
                $cid = $pair['company_id'];

                if (in_array($sid, $allocatedStudents)) {
                    continue; // Student already allocated in this pass
                }

                if ($companySlots[$cid] <= 0) {
                    continue; // Company slots filled
                }

                // Pick departmental supervisor in round-robin fashion
                $acadSupervisorId = !empty($deptSupervisorsList) ? $deptSupervisorsList[$allocatedCount % $deptSupCount]['id'] : 1;

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
        $customSupervisorId = $request->post('supervisor_id');

        if (empty($sid) || empty($cid)) {
            Session::setFlash('error', 'Student and Company are required.');
            return $response->redirect('/coordinator/allocations');
        }

        $db = Database::getInstance();

        // Verify company availability
        $slots = $db->querySingle("SELECT available_slots FROM companies WHERE id = :cid", [':cid' => $cid]);
        
        // Check if student already had an allocation to determine slots & preserve departmental supervisor
        $existing = $db->fetchRow("SELECT * FROM allocations WHERE student_id = :sid LIMIT 1", [':sid' => $sid]);
        if (!$existing && $slots <= 0) {
            Session::setFlash('error', 'The selected company has no available slots.');
            return $response->redirect('/coordinator/allocations');
        }

        // Get company supervisor
        $compSupervisorId = !empty($customSupervisorId) ? (int)$customSupervisorId : $db->querySingle("SELECT id FROM supervisors WHERE company_id = :cid AND type = 'company' LIMIT 1", [':cid' => $cid]);
        if (!$compSupervisorId) {
            $compSupervisorId = $db->querySingle("SELECT id FROM supervisors WHERE company_id = :cid LIMIT 1", [':cid' => $cid]);
        }

        // Preserve student's departmental supervisor or default to CSC supervisor
        $acadSupervisorId = $existing['institution_supervisor_id'] ?? ($db->querySingle("SELECT id FROM supervisors WHERE type = 'institution' LIMIT 1") ?? 6);

        $db->getConnection()->exec("BEGIN TRANSACTION;");
        try {
            if ($existing) {
                // Adjust company slots if changing company
                if ($existing['company_id'] != $cid) {
                    $db->query("UPDATE companies SET available_slots = available_slots + 1 WHERE id = :oldCid", [':oldCid' => $existing['company_id']]);
                    $db->query("UPDATE companies SET available_slots = available_slots - 1 WHERE id = :newCid", [':newCid' => $cid]);
                }

                $stmt = $db->getConnection()->prepare("UPDATE allocations SET company_id = :cid, supervisor_id = :svid, institution_supervisor_id = :asvid, status = 'active', allocated_by = 'manual' WHERE id = :aid");
                $stmt->bindValue(':cid', $cid);
                $stmt->bindValue(':svid', $compSupervisorId);
                $stmt->bindValue(':asvid', $acadSupervisorId);
                $stmt->bindValue(':aid', $existing['id']);
                $stmt->execute();
            } else {
                // Decrement slots for new allocation
                $db->query("UPDATE companies SET available_slots = available_slots - 1 WHERE id = :cid", [':cid' => $cid]);

                $stmt = $db->getConnection()->prepare("INSERT INTO allocations (student_id, company_id, supervisor_id, institution_supervisor_id, status, allocated_by) VALUES (:sid, :cid, :svid, :asvid, 'active', 'manual')");
                $stmt->bindValue(':sid', $sid);
                $stmt->bindValue(':cid', $cid);
                $stmt->bindValue(':svid', $compSupervisorId);
                $stmt->bindValue(':asvid', $acadSupervisorId);
                $stmt->execute();
            }

            // Update student status
            $db->query("UPDATE students SET siwes_status = 'allocated' WHERE id = :sid", [':sid' => $sid]);

            // Notify Student
            $user = $db->fetchRow("SELECT s.user_id, s.first_name, s.last_name, s.matric_no FROM students s WHERE s.id = :sid LIMIT 1", [':sid' => $sid]);
            $compName = $db->querySingle("SELECT name FROM companies WHERE id = :cid", [':cid' => $cid]);
            if ($user) {
                $stmtNotif = $db->getConnection()->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (:uid, 'Placement Allocated', :msg, 'success')");
                $stmtNotif->bindValue(':uid', $user['user_id']);
                $stmtNotif->bindValue(':msg', "You have been allocated to {$compName}. Your supervisor will review your documents.");
                $stmtNotif->execute();
            }

            // Notify Company Supervisor
            if ($compSupervisorId) {
                $compSupUserId = $db->querySingle("SELECT user_id FROM supervisors WHERE id = :svid", [':svid' => $compSupervisorId]);
                if ($compSupUserId && $user) {
                    $stmtNotifSup = $db->getConnection()->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (:uid, 'New Student Assigned', :msg, 'info')");
                    $stmtNotifSup->bindValue(':uid', $compSupUserId);
                    $stmtNotifSup->bindValue(':msg', "Student {$user['first_name']} {$user['last_name']} ({$user['matric_no']}) has been assigned to your organization ({$compName}) for SIWES supervision.");
                    $stmtNotifSup->execute();
                }
            }

            $db->getConnection()->exec("COMMIT;");
            Session::setFlash('success', 'Student manually allocated and added to company successfully.');
        } catch (\Exception $e) {
            $db->getConnection()->exec("ROLLBACK;");
            Session::setFlash('error', 'Allocation failed: ' . $e->getMessage());
        }

        return $response->redirect('/coordinator/allocations');
    }

    public function assignSupervisor() {
        Auth::requireRole(['admin', 'coordinator']);
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();

        if (!$request->isPost()) {
            return $response->redirect('/coordinator/allocations');
        }

        $studentId = $request->post('student_id');
        $supervisorId = $request->post('supervisor_id');
        $companyId = $request->post('company_id');

        if (empty($studentId) || empty($supervisorId)) {
            Session::setFlash('error', 'Both student and supervisor must be selected.');
            return $response->redirect('/coordinator/allocations');
        }

        // If company is not passed, check if supervisor has a linked company or existing allocation
        if (empty($companyId)) {
            $companyId = $db->querySingle("SELECT company_id FROM supervisors WHERE id = :svid", [':svid' => $supervisorId]);
            if (empty($companyId)) {
                $companyId = $db->querySingle("SELECT company_id FROM allocations WHERE student_id = :sid", [':sid' => $studentId]);
            }
            if (empty($companyId)) {
                $companyId = $db->querySingle("SELECT id FROM companies LIMIT 1") ?? 1;
            }
        }

        $acadSupervisorId = $db->querySingle("SELECT id FROM supervisors WHERE type = 'institution' LIMIT 1") ?? 1;

        $db->getConnection()->exec("BEGIN TRANSACTION;");
        try {
            // Check if allocation exists
            $existing = $db->querySingle("SELECT id FROM allocations WHERE student_id = :sid", [':sid' => $studentId]);
            if ($existing) {
                $stmt = $db->getConnection()->prepare("UPDATE allocations SET supervisor_id = :svid, company_id = :cid WHERE id = :aid");
                $stmt->bindValue(':svid', $supervisorId);
                $stmt->bindValue(':cid', $companyId);
                $stmt->bindValue(':aid', $existing);
                $stmt->execute();
            } else {
                $stmt = $db->getConnection()->prepare("INSERT INTO allocations (student_id, company_id, supervisor_id, institution_supervisor_id, status, allocated_by) VALUES (:sid, :cid, :svid, :asvid, 'active', 'manual')");
                $stmt->bindValue(':sid', $studentId);
                $stmt->bindValue(':cid', $companyId);
                $stmt->bindValue(':svid', $supervisorId);
                $stmt->bindValue(':asvid', $acadSupervisorId);
                $stmt->execute();
            }

            // Update student status to allocated
            $db->query("UPDATE students SET siwes_status = 'allocated' WHERE id = :sid", [':sid' => $studentId]);

            // Notify Student
            $user = $db->fetchRow("SELECT user_id, first_name FROM students WHERE id = :sid LIMIT 1", [':sid' => $studentId]);
            $sup = $db->fetchRow("SELECT first_name, last_name, user_id FROM supervisors WHERE id = :svid LIMIT 1", [':svid' => $supervisorId]);

            if ($user && $sup) {
                $supName = $sup['first_name'] . ' ' . $sup['last_name'];
                $stmtNotif = $db->getConnection()->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (:uid, 'Supervisor Assigned', :msg, 'info')");
                $stmtNotif->bindValue(':uid', $user['user_id']);
                $stmtNotif->bindValue(':msg', "Coordinator has assigned {$supName} as your SIWES supervisor.");
                $stmtNotif->execute();

                if (!empty($sup['user_id'])) {
                    $stmtNotifSup = $db->getConnection()->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (:uid, 'New Student Assigned', :msg, 'info')");
                    $stmtNotifSup->bindValue(':uid', $sup['user_id']);
                    $stmtNotifSup->bindValue(':msg', "Coordinator has assigned student {$user['first_name']} to you for SIWES supervision.");
                    $stmtNotifSup->execute();
                }
            }

            $db->getConnection()->exec("COMMIT;");
            Session::setFlash('success', 'Supervisor assigned to student successfully!');
        } catch (\Exception $e) {
            $db->getConnection()->exec("ROLLBACK;");
            Session::setFlash('error', 'Failed to assign supervisor: ' . $e->getMessage());
        }

        return $response->redirect('/coordinator/allocations');
    }

    public function assignDepartmentSupervisor() {
        Auth::requireRole(['admin', 'coordinator']);
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();

        if (!$request->isPost()) {
            return $response->redirect('/coordinator/allocations');
        }

        $studentId = (int)$request->post('student_id');
        $deptSupervisorId = (int)$request->post('dept_supervisor_id');

        if (empty($studentId) || empty($deptSupervisorId)) {
            Session::setFlash('error', 'Both student and departmental supervisor must be selected.');
            return $response->redirect('/coordinator/allocations');
        }

        // Verify departmental supervisor exists
        $deptSup = $db->fetchRow("SELECT s.*, u.id as user_id FROM supervisors s JOIN users u ON s.user_id = u.id WHERE s.id = :id AND s.type = 'institution' LIMIT 1", [':id' => $deptSupervisorId]);
        if (!$deptSup) {
            Session::setFlash('error', 'Invalid departmental supervisor selected.');
            return $response->redirect('/coordinator/allocations');
        }

        $student = $db->fetchRow("SELECT s.*, u.id as user_id FROM students s JOIN users u ON s.user_id = u.id WHERE s.id = :id LIMIT 1", [':id' => $studentId]);
        if (!$student) {
            Session::setFlash('error', 'Student not found.');
            return $response->redirect('/coordinator/allocations');
        }

        $db->getConnection()->exec("BEGIN TRANSACTION;");
        try {
            $existing = $db->querySingle("SELECT id FROM allocations WHERE student_id = :sid", [':sid' => $studentId]);
            if ($existing) {
                $db->query("UPDATE allocations SET institution_supervisor_id = :asvid WHERE id = :aid", [
                    ':asvid' => $deptSupervisorId,
                    ':aid' => $existing
                ]);
            } else {
                $defaultCompany = $db->querySingle("SELECT id FROM companies LIMIT 1") ?? 1;
                $defaultCompanySup = $db->querySingle("SELECT id FROM supervisors WHERE type = 'company' LIMIT 1");
                $stmt = $db->getConnection()->prepare("INSERT INTO allocations (student_id, company_id, supervisor_id, institution_supervisor_id, status, allocated_by) VALUES (:sid, :cid, :svid, :asvid, 'active', 'manual')");
                $stmt->bindValue(':sid', $studentId);
                $stmt->bindValue(':cid', $defaultCompany);
                $stmt->bindValue(':svid', $defaultCompanySup);
                $stmt->bindValue(':asvid', $deptSupervisorId);
                $stmt->execute();
            }

            // Notifications
            $supName = $deptSup['first_name'] . ' ' . $deptSup['last_name'];
            $stmtNotif = $db->getConnection()->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (:uid, 'Departmental Supervisor Assigned', :msg, 'info')");
            $stmtNotif->bindValue(':uid', $student['user_id']);
            $stmtNotif->bindValue(':msg', "Coordinator has assigned {$supName} as your SIWES Departmental Supervisor.");
            $stmtNotif->execute();

            $stmtNotifSup = $db->getConnection()->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (:uid, 'New Student Assigned for Supervision', :msg, 'info')");
            $stmtNotifSup->bindValue(':uid', $deptSup['user_id']);
            $stmtNotifSup->bindValue(':msg', "Coordinator has assigned student {$student['first_name']} {$student['last_name']} ({$student['matric_no']}) to your departmental supervision.");
            $stmtNotifSup->execute();

            $db->getConnection()->exec("COMMIT;");
            Session::setFlash('success', "Departmental supervisor {$supName} assigned to student {$student['first_name']} successfully!");
        } catch (\Exception $e) {
            $db->getConnection()->exec("ROLLBACK;");
            Session::setFlash('error', 'Failed to assign departmental supervisor: ' . $e->getMessage());
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
