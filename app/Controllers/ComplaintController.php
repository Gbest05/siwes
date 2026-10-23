<?php

namespace App\Controllers;

use App\Core\Response;
use App\Core\Request;
use App\Core\Session;
use App\Core\Auth;
use App\Core\Database;

class ComplaintController {
    public function index() {
        Auth::requireRole(['admin', 'coordinator', 'supervisor']);
        $request = new Request();
        $db = Database::getInstance();
        $user = Auth::user();

        $statusFilter = $request->get('status', '');
        $search = trim($request->get('search', ''));

        $sql = "SELECT c.*, s.first_name || ' ' || s.last_name as student_name, s.matric_no, s.phone as student_phone, u.username as creator_name, u.role as creator_role 
                FROM complaints c 
                JOIN students s ON c.student_id = s.id 
                JOIN users u ON c.created_by_user_id = u.id 
                WHERE 1=1";
        $params = [];

        if (!empty($statusFilter)) {
            $sql .= " AND c.status = :status";
            $params[':status'] = $statusFilter;
        }

        if (!empty($search)) {
            $sql .= " AND (s.first_name LIKE :search OR s.last_name LIKE :search OR s.matric_no LIKE :search OR c.title LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY c.created_at DESC";
        $complaints = $db->fetchAll($sql, $params);

        // Fetch students for the add modal
        $students = $db->fetchAll("SELECT id, matric_no, first_name, last_name FROM students ORDER BY matric_no ASC");

        $response = new Response();
        return $response->render('complaints/index', [
            'complaints' => $complaints,
            'students' => $students,
            'filters' => [
                'status' => $statusFilter,
                'search' => $search
            ]
        ]);
    }

    public function add() {
        Auth::requireRole(['admin', 'coordinator', 'supervisor']);
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();
        $user = Auth::user();

        if (!$request->isPost()) {
            return $response->redirect('/coordinator/complaints');
        }

        $studentId = (int)$request->post('student_id');
        $title = trim($request->post('title', ''));
        $description = trim($request->post('description', ''));

        if (empty($studentId) || empty($title) || empty($description)) {
            Session::setFlash('error', 'Student, title, and complaint description are required.');
            return $response->redirect('/coordinator/complaints');
        }

        $student = $db->fetchRow("SELECT s.*, u.id as user_id FROM students s JOIN users u ON s.user_id = u.id WHERE s.id = :sid LIMIT 1", [':sid' => $studentId]);
        if (!$student) {
            Session::setFlash('error', 'Student not found.');
            return $response->redirect('/coordinator/complaints');
        }

        $db->getConnection()->exec("BEGIN TRANSACTION;");
        try {
            $stmt = $db->getConnection()->prepare("INSERT INTO complaints (student_id, created_by_user_id, title, description, status) VALUES (:sid, :uid, :t, :d, 'open')");
            $stmt->bindValue(':sid', $studentId);
            $stmt->bindValue(':uid', $user['id']);
            $stmt->bindValue(':t', $title);
            $stmt->bindValue(':d', $description);
            $stmt->execute();

            // Also flag student's document feedback
            $db->query("UPDATE students SET document_status = 'queried', supervisor_feedback = :fb WHERE id = :sid", [
                ':fb' => $title . ': ' . $description,
                ':sid' => $studentId
            ]);

            // Notify student
            $stmtNotif = $db->getConnection()->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (:uid, 'New SIWES Query/Complaint', :msg, 'warning')");
            $stmtNotif->bindValue(':uid', $student['user_id']);
            $stmtNotif->bindValue(':msg', "Complaint / Query filed: {$title}. Details: {$description}");
            $stmtNotif->execute();

            $db->getConnection()->exec("COMMIT;");
            Session::setFlash('success', "Complaint filed successfully against {$student['first_name']} {$student['last_name']}.");
        } catch (\Exception $e) {
            $db->getConnection()->exec("ROLLBACK;");
            Session::setFlash('error', 'Failed to record complaint: ' . $e->getMessage());
        }

        return $response->redirect('/coordinator/complaints');
    }

    public function updateStatus() {
        Auth::requireRole(['admin', 'coordinator', 'supervisor']);
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();

        if (!$request->isPost()) {
            return $response->redirect('/coordinator/complaints');
        }

        $complaintId = (int)$request->post('complaint_id');
        $status = $request->post('status'); // 'open', 'in_progress', 'resolved'

        if (empty($complaintId) || !in_array($status, ['open', 'in_progress', 'resolved'])) {
            Session::setFlash('error', 'Valid complaint and status are required.');
            return $response->redirect('/coordinator/complaints');
        }

        $complaint = $db->fetchRow("SELECT c.*, s.user_id FROM complaints c JOIN students s ON c.student_id = s.id WHERE c.id = :cid LIMIT 1", [':cid' => $complaintId]);

        $db->getConnection()->exec("BEGIN TRANSACTION;");
        try {
            $stmt = $db->getConnection()->prepare("UPDATE complaints SET status = :s WHERE id = :cid");
            $stmt->bindValue(':s', $status);
            $stmt->bindValue(':cid', $complaintId);
            $stmt->execute();

            if ($status === 'resolved') {
                // If resolved, clear student document query status
                $db->query("UPDATE students SET document_status = 'approved' WHERE id = :sid", [':sid' => $complaint['student_id']]);
            }

            if ($complaint && !empty($complaint['user_id'])) {
                $statusLabel = ucfirst(str_replace('_', ' ', $status));
                $stmtNotif = $db->getConnection()->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (:uid, 'Complaint Status Updated', :msg, 'info')");
                $stmtNotif->bindValue(':uid', $complaint['user_id']);
                $stmtNotif->bindValue(':msg', "Your SIWES complaint '{$complaint['title']}' has been marked as: {$statusLabel}.");
                $stmtNotif->execute();
            }

            $db->getConnection()->exec("COMMIT;");
            Session::setFlash('success', "Complaint status updated to " . ucfirst(str_replace('_', ' ', $status)) . ".");
        } catch (\Exception $e) {
            $db->getConnection()->exec("ROLLBACK;");
            Session::setFlash('error', 'Failed to update complaint status: ' . $e->getMessage());
        }

        return $response->redirect('/coordinator/complaints');
    }
}
