<?php

namespace App\Controllers;

use App\Core\Response;
use App\Core\Request;
use App\Core\Session;
use App\Core\Auth;
use App\Core\Database;

class SupervisorController {
    public function reviewStudent() {
        Auth::requireRole('supervisor');
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();
        $user = Auth::user();
        $supervisorId = $user['profile']['id'];

        if (!$request->isPost()) {
            return $response->redirect('/supervisor/dashboard');
        }

        $studentId = (int)$request->post('student_id');
        $action = $request->post('review_action'); // 'approve', 'allocate_company', 'query'
        $companyId = !empty($request->post('company_id')) ? (int)$request->post('company_id') : null;
        $feedback = trim($request->post('feedback', ''));
        $complaintTitle = trim($request->post('complaint_title', 'SIWES Document Query'));

        if (empty($studentId) || empty($action)) {
            Session::setFlash('error', 'Student ID and review action are required.');
            return $response->redirect('/supervisor/dashboard');
        }

        // Verify that this student is assigned to this supervisor
        $allocation = $db->fetchRow("SELECT a.*, c.name as company_name FROM allocations a LEFT JOIN companies c ON a.company_id = c.id WHERE a.student_id = :sid AND (a.supervisor_id = :svid OR a.institution_supervisor_id = :svid) LIMIT 1", [
            ':sid' => $studentId,
            ':svid' => $supervisorId
        ]);

        $student = $db->fetchRow("SELECT s.*, u.id as user_id, u.email FROM students s JOIN users u ON s.user_id = u.id WHERE s.id = :sid LIMIT 1", [
            ':sid' => $studentId
        ]);

        if (!$student) {
            Session::setFlash('error', 'Student record not found.');
            return $response->redirect('/supervisor/dashboard');
        }

        $db->getConnection()->exec("BEGIN TRANSACTION;");
        try {
            if ($action === 'approve') {
                // 1. Approve student documents
                $stmt = $db->getConnection()->prepare("UPDATE students SET document_status = 'approved', siwes_status = 'allocated', supervisor_feedback = :fb WHERE id = :sid");
                $approvedNote = !empty($feedback) ? $feedback : 'Your SIWES documents have been reviewed and approved by your supervisor.';
                $stmt->bindValue(':fb', $approvedNote);
                $stmt->bindValue(':sid', $studentId);
                $stmt->execute();

                // If company was passed or changed, update allocation
                if ($companyId) {
                    // Resolve Company Supervisor for the selected company
                    $compSupervisorId = $db->querySingle("SELECT id FROM supervisors WHERE company_id = :cid AND type = 'company' LIMIT 1", [':cid' => $companyId]);
                    if (!$compSupervisorId) {
                        $compSupervisorId = $db->querySingle("SELECT id FROM supervisors WHERE company_id = :cid LIMIT 1", [':cid' => $companyId]);
                    }

                    // Keep existing departmental supervisor or assign current user if institution type
                    $deptSupervisorId = ($user['profile']['type'] === 'institution') ? $supervisorId : ($allocation['institution_supervisor_id'] ?? null);
                    if (!$deptSupervisorId) {
                        $deptSupervisorId = $db->querySingle("SELECT id FROM supervisors WHERE type = 'institution' LIMIT 1") ?? 6;
                    }

                    if ($allocation) {
                        $db->query("UPDATE allocations SET company_id = :cid, supervisor_id = COALESCE(:svid, supervisor_id), institution_supervisor_id = COALESCE(:asvid, institution_supervisor_id), status = 'active' WHERE id = :aid", [
                            ':cid' => $companyId,
                            ':svid' => $compSupervisorId,
                            ':asvid' => $deptSupervisorId,
                            ':aid' => $allocation['id']
                        ]);
                    } else {
                        $stmtAlloc = $db->getConnection()->prepare("INSERT INTO allocations (student_id, company_id, supervisor_id, institution_supervisor_id, status, allocated_by) VALUES (:sid, :cid, :svid, :asvid, 'active', 'manual')");
                        $stmtAlloc->bindValue(':sid', $studentId);
                        $stmtAlloc->bindValue(':cid', $companyId);
                        $stmtAlloc->bindValue(':svid', $compSupervisorId);
                        $stmtAlloc->bindValue(':asvid', $deptSupervisorId);
                        $stmtAlloc->execute();
                    }
                }

                // Send notification to student
                $stmtNotif = $db->getConnection()->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (:uid, 'SIWES Documents Approved', 'Congratulations! Your supervisor has reviewed and approved your uploaded documents. Your official Placement Letter is now unlocked.', 'success')");
                $stmtNotif->bindValue(':uid', $student['user_id']);
                $stmtNotif->execute();

                Session::setFlash('success', "Student {$student['first_name']} {$student['last_name']}'s documents approved! Placement unlocked.");

            } elseif ($action === 'allocate_company') {
                if (empty($companyId)) {
                    throw new \Exception('Please select a company to allocate to this student.');
                }

                // Resolve Company Supervisor for the selected company
                $compSupervisorId = $db->querySingle("SELECT id FROM supervisors WHERE company_id = :cid AND type = 'company' LIMIT 1", [':cid' => $companyId]);
                if (!$compSupervisorId) {
                    $compSupervisorId = $db->querySingle("SELECT id FROM supervisors WHERE company_id = :cid LIMIT 1", [':cid' => $companyId]);
                }

                // Keep existing departmental supervisor or assign current user if institution type
                $deptSupervisorId = ($user['profile']['type'] === 'institution') ? $supervisorId : ($allocation['institution_supervisor_id'] ?? null);
                if (!$deptSupervisorId) {
                    $deptSupervisorId = $db->querySingle("SELECT id FROM supervisors WHERE type = 'institution' LIMIT 1") ?? 6;
                }

                if ($allocation) {
                    $db->query("UPDATE allocations SET company_id = :cid, supervisor_id = :svid, institution_supervisor_id = COALESCE(:asvid, institution_supervisor_id), status = 'active' WHERE id = :aid", [
                        ':cid' => $companyId,
                        ':svid' => $compSupervisorId,
                        ':asvid' => $deptSupervisorId,
                        ':aid' => $allocation['id']
                    ]);
                } else {
                    $stmtAlloc = $db->getConnection()->prepare("INSERT INTO allocations (student_id, company_id, supervisor_id, institution_supervisor_id, status, allocated_by) VALUES (:sid, :cid, :svid, :asvid, 'active', 'manual')");
                    $stmtAlloc->bindValue(':sid', $studentId);
                    $stmtAlloc->bindValue(':cid', $companyId);
                    $stmtAlloc->bindValue(':svid', $compSupervisorId);
                    $stmtAlloc->bindValue(':asvid', $deptSupervisorId);
                    $stmtAlloc->execute();
                }

                $db->query("UPDATE students SET siwes_status = 'allocated' WHERE id = :sid", [':sid' => $studentId]);

                $compName = $db->querySingle("SELECT name FROM companies WHERE id = :cid", [':cid' => $companyId]);
                $stmtNotif = $db->getConnection()->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (:uid, 'Company Allocated', :msg, 'info')");
                $stmtNotif->bindValue(':uid', $student['user_id']);
                $stmtNotif->bindValue(':msg', "Your supervisor has assigned/updated your placement to: {$compName}.");
                $stmtNotif->execute();

                // Notify Company Supervisor if found
                if ($compSupervisorId) {
                    $compSupUserId = $db->querySingle("SELECT user_id FROM supervisors WHERE id = :id", [':id' => $compSupervisorId]);
                    if ($compSupUserId) {
                        $stmtNotifComp = $db->getConnection()->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (:uid, 'New Student Attached', :msg, 'info')");
                        $stmtNotifComp->bindValue(':uid', $compSupUserId);
                        $stmtNotifComp->bindValue(':msg', "Student {$student['first_name']} {$student['last_name']} ({$student['matric_no']}) has been assigned to your organization ({$compName}) for SIWES supervision.");
                        $stmtNotifComp->execute();
                    }
                }

                Session::setFlash('success', "Student {$student['first_name']} {$student['last_name']} assigned to {$compName} successfully.");

            } elseif ($action === 'query') {
                if (empty($feedback)) {
                    throw new \Exception('Please enter the query / complaint description explaining what the student needs to fix.');
                }

                // Update student status to queried and allow re-upload of corrections
                $stmt = $db->getConnection()->prepare("UPDATE students SET document_status = 'queried', documents_submitted = 0, supervisor_feedback = :fb WHERE id = :sid");
                $stmt->bindValue(':fb', $feedback);
                $stmt->bindValue(':sid', $studentId);
                $stmt->execute();

                // Insert into complaints table
                $stmtComp = $db->getConnection()->prepare("INSERT INTO complaints (student_id, created_by_user_id, title, description, status) VALUES (:sid, :uid, :t, :d, 'open')");
                $stmtComp->bindValue(':sid', $studentId);
                $stmtComp->bindValue(':uid', $user['id']);
                $stmtComp->bindValue(':t', $complaintTitle);
                $stmtComp->bindValue(':d', $feedback);
                $stmtComp->execute();

                // Send notification to student
                $stmtNotif = $db->getConnection()->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (:uid, 'Document Query Issued', :msg, 'warning')");
                $stmtNotif->bindValue(':uid', $student['user_id']);
                $stmtNotif->bindValue(':msg', "Query from supervisor: {$feedback}. Please check your dashboard to upload corrected documents.");
                $stmtNotif->execute();

                Session::setFlash('success', "Query/complaint submitted to student {$student['first_name']}. The student has been notified.");
            }

            $db->getConnection()->exec("COMMIT;");
        } catch (\Exception $e) {
            $db->getConnection()->exec("ROLLBACK;");
            Session::setFlash('error', 'Operation failed: ' . $e->getMessage());
        }

        return $response->redirect('/supervisor/dashboard');
    }
}
