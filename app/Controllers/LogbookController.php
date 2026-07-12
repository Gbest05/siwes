<?php

namespace App\Controllers;

use App\Core\Response;
use App\Core\Request;
use App\Core\Session;
use App\Core\Auth;
use App\Core\Database;

class LogbookController {
    
    public function studentIndex() {
        Auth::requireRole('student');
        $db = Database::getInstance();
        $user = Auth::user();
        $studentId = $user['profile']['id'];

        // Get daily logbooks
        $logs = $db->fetchAll("SELECT * FROM logbooks WHERE student_id = :sid ORDER BY date DESC", [
            ':sid' => $studentId
        ]);

        // Get weekly reports
        $weeklyReports = $db->fetchAll("SELECT * FROM weekly_reports WHERE student_id = :sid ORDER BY week_number DESC", [
            ':sid' => $studentId
        ]);

        $response = new Response();
        return $response->render('logbook/student', [
            'logs' => $logs,
            'weeklyReports' => $weeklyReports
        ]);
    }

    public function addLog() {
        Auth::requireRole('student');
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();
        $user = Auth::user();
        $studentId = $user['profile']['id'];

        if (!$request->isPost()) {
            return $response->redirect('/student/logbook');
        }

        $date = $request->post('date');
        $activity = $request->post('activity_description');
        $weekNo = (int) $request->post('week_number');

        if (empty($date) || empty($activity) || empty($weekNo)) {
            Session::setFlash('error', 'Date, activity description, and week number are required.');
            return $response->redirect('/student/logbook');
        }

        $dayOfWeek = date('l', strtotime($date));
        $imgPath = null;

        // Process File Upload if present
        $file = $request->file('image');
        if ($file) {
            $uploadDir = dirname(dirname(__DIR__)) . '/public/uploads/logbook/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'svg'])) {
                $filename = 'log_' . $studentId . '_' . time() . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                    $imgPath = '/siwes/public/uploads/logbook/' . $filename;
                }
            } else {
                Session::setFlash('error', 'Only image files (jpg, jpeg, png, svg) are allowed.');
                return $response->redirect('/student/logbook');
            }
        }

        try {
            // Save Daily Log
            $stmt = $db->prepare("INSERT OR REPLACE INTO logbooks (student_id, date, day_of_week, activity_description, image_path, supervisor_status, week_number) VALUES (:sid, :dt, :day, :act, :img, 'pending', :week)");
            $stmt->bindValue(':sid', $studentId);
            $stmt->bindValue(':dt', $date);
            $stmt->bindValue(':day', $dayOfWeek);
            $stmt->bindValue(':act', $activity);
            $stmt->bindValue(':img', $imgPath);
            $stmt->bindValue(':week', $weekNo);
            $stmt->execute();

            // Log Attendance Auto
            $stmtAttend = $db->prepare("INSERT OR IGNORE INTO attendance (student_id, date, status) VALUES (:sid, :dt, 'present')");
            $stmtAttend->bindValue(':sid', $studentId);
            $stmtAttend->bindValue(':dt', $date);
            $stmtAttend->execute();

            Session::setFlash('success', 'Daily logbook entry submitted successfully.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Error saving logbook: ' . $e->getMessage());
        }

        return $response->redirect('/student/logbook');
    }

    public function submitWeekly() {
        Auth::requireRole('student');
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();
        $user = Auth::user();
        $studentId = $user['profile']['id'];

        if (!$request->isPost()) {
            return $response->redirect('/student/logbook');
        }

        $weekNo = (int) $request->post('week_number');
        $summary = $request->post('summary');

        if (empty($weekNo) || empty($summary)) {
            Session::setFlash('error', 'Week number and summary are required.');
            return $response->redirect('/student/logbook');
        }

        try {
            $stmt = $db->prepare("INSERT OR REPLACE INTO weekly_reports (student_id, week_number, summary, supervisor_status) VALUES (:sid, :week, :sum, 'pending')");
            $stmt->bindValue(':sid', $studentId);
            $stmt->bindValue(':week', $weekNo);
            $stmt->bindValue(':sum', $summary);
            $stmt->execute();

            Session::setFlash('success', "Weekly summary for Week $weekNo submitted successfully.");
        } catch (\Exception $e) {
            Session::setFlash('error', 'Error submitting weekly report: ' . $e->getMessage());
        }

        return $response->redirect('/student/logbook');
    }

    public function supervisorIndex() {
        Auth::requireRole('supervisor');
        $db = Database::getInstance();
        $user = Auth::user();
        $supervisorId = $user['profile']['id'];

        // Get daily logbooks for assigned students
        $logs = $db->fetchAll("SELECT l.*, s.first_name || ' ' || s.last_name as student_name, s.matric_no FROM logbooks l JOIN students s ON l.student_id = s.id JOIN allocations a ON a.student_id = s.id WHERE a.supervisor_id = :svid ORDER BY l.date DESC", [
            ':svid' => $supervisorId
        ]);

        // Get weekly summaries for assigned students
        $weeklyReports = $db->fetchAll("SELECT w.*, s.first_name || ' ' || s.last_name as student_name, s.matric_no FROM weekly_reports w JOIN students s ON w.student_id = s.id JOIN allocations a ON a.student_id = s.id WHERE a.supervisor_id = :svid ORDER BY w.week_number DESC", [
            ':svid' => $supervisorId
        ]);

        $response = new Response();
        return $response->render('logbook/supervisor', [
            'logs' => $logs,
            'weeklyReports' => $weeklyReports
        ]);
    }

    public function verifyLog() {
        Auth::requireRole(['supervisor', 'coordinator']);
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();
        $user = Auth::user();

        if (!$request->isPost()) {
            return $response->redirect('/supervisor/logbooks');
        }

        $type = $request->post('type'); // daily or weekly
        $id = $request->post('id');
        $status = $request->post('status'); // approved or queried
        $comments = $request->post('comments');

        if (empty($id) || empty($status)) {
            Session::setFlash('error', 'Record ID and Approval Status are required.');
            return $response->redirect('/supervisor/logbooks');
        }

        try {
            if ($type === 'daily') {
                if ($user['role'] === 'supervisor') {
                    $stmt = $db->prepare("UPDATE logbooks SET supervisor_status = :s, supervisor_comments = :c WHERE id = :id");
                } else { // coordinator
                    $stmt = $db->prepare("UPDATE logbooks SET coordinator_comments = :c WHERE id = :id");
                }
                $stmt->bindValue(':c', $comments);
                if ($user['role'] === 'supervisor') {
                    $stmt->bindValue(':s', $status);
                }
                $stmt->bindValue(':id', $id);
                $stmt->execute();
                
                Session::setFlash('success', 'Daily log entry reviewed successfully.');
            } else { // weekly
                $grade = $request->post('grade'); // 0-10 grade
                
                if ($user['role'] === 'supervisor') {
                    $stmt = $db->prepare("UPDATE weekly_reports SET supervisor_status = :s, supervisor_comments = :c, supervisor_grade = :g WHERE id = :id");
                    $stmt->bindValue(':g', !empty($grade) ? (int)$grade : null);
                } else { // coordinator
                    $stmt = $db->prepare("UPDATE weekly_reports SET coordinator_comments = :c WHERE id = :id");
                }
                $stmt->bindValue(':c', $comments);
                if ($user['role'] === 'supervisor') {
                    $stmt->bindValue(':s', $status);
                }
                $stmt->bindValue(':id', $id);
                $stmt->execute();

                Session::setFlash('success', 'Weekly summary reviewed successfully.');
            }
        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to save review: ' . $e->getMessage());
        }

        return $response->redirect($user['role'] === 'supervisor' ? '/supervisor/logbooks' : '/coordinator/dashboard');
    }

    public function evaluateStudent() {
        Auth::requireRole('supervisor');
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();

        if (!$request->isPost()) {
            return $response->redirect('/supervisor/dashboard');
        }

        $sid = $request->post('student_id');
        $score = (int) $request->post('score');
        $remarks = $request->post('remarks');
        $recommendations = $request->post('recommendations');

        if (empty($sid)) {
            Session::setFlash('error', 'Student selection is required.');
            return $response->redirect('/supervisor/dashboard');
        }

        // Determine letter grade
        $grade = 'F';
        if ($score >= 80) $grade = 'A';
        elseif ($score >= 60) $grade = 'B';
        elseif ($score >= 50) $grade = 'C';
        elseif ($score >= 40) $grade = 'D';

        $user = Auth::user();
        $companyId = $user['profile']['company_id'];

        try {
            $stmt = $db->prepare("INSERT OR REPLACE INTO evaluations (student_id, company_id, supervisor_score, supervisor_remarks, final_grade, recommendations) VALUES (:sid, :cid, :score, :rem, :gr, :rec)");
            $stmt->bindValue(':sid', $sid);
            $stmt->bindValue(':cid', $companyId);
            $stmt->bindValue(':score', $score);
            $stmt->bindValue(':rem', $remarks);
            $stmt->bindValue(':gr', $grade);
            $stmt->bindValue(':rec', $recommendations);
            $stmt->execute();

            Session::setFlash('success', 'Student performance evaluation submitted successfully.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Evaluation save error: ' . $e->getMessage());
        }

        return $response->redirect('/supervisor/dashboard');
    }
}
