<?php

namespace App\Controllers;

use App\Core\Response;
use App\Core\Request;
use App\Core\Session;
use App\Core\Auth;
use App\Core\Database;

class StudentController {
    public function index() {
        Auth::requireRole(['admin', 'coordinator', 'officer']);
        $request = new Request();
        $db = Database::getInstance();

        $search = $request->get('search', '');
        $deptFilter = $request->get('department', '');
        $statusFilter = $request->get('status', '');
        
        // Pagination
        $page = max(1, (int)$request->get('page', 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Base Query Build
        $sql = "SELECT s.*, u.username, u.email, u.status as account_status, d.code as dept_code, d.name as department_name 
                FROM students s 
                JOIN users u ON s.user_id = u.id 
                JOIN departments d ON s.department_id = d.id WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (s.first_name LIKE :search OR s.last_name LIKE :search OR s.matric_no LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        if (!empty($deptFilter)) {
            $sql .= " AND s.department_id = :dept";
            $params[':dept'] = $deptFilter;
        }

        if (!empty($statusFilter)) {
            $sql .= " AND s.siwes_status = :status";
            $params[':status'] = $statusFilter;
        }

        // Count Total for Pagination
        $countSql = str_replace("SELECT s.*, u.username, u.email, u.status as account_status, d.code as dept_code, d.name as department_name", "SELECT COUNT(*)", $sql);
        $totalItems = $db->querySingle($countSql, $params);
        $totalPages = ceil($totalItems / $limit);

        // Fetch paginated results
        $sql .= " ORDER BY s.matric_no ASC LIMIT $limit OFFSET $offset";
        $students = $db->fetchAll($sql, $params);

        // Fetch departments for filter dropdown
        $departments = $db->fetchAll("SELECT * FROM departments");

        $response = new Response();
        return $response->render('students/index', [
            'students' => $students,
            'departments' => $departments,
            'filters' => [
                'search' => $search,
                'department' => $deptFilter,
                'status' => $statusFilter
            ],
            'pagination' => [
                'current' => $page,
                'total' => $totalPages,
                'total_items' => $totalItems
            ]
        ]);
    }

    public function deactivate() {
        Auth::requireRole(['admin', 'coordinator']);
        $request = new Request();
        $response = new Response();

        if (!$request->isPost()) {
            return $response->redirect('/coordinator/students');
        }

        $uid = $request->post('user_id');
        $status = $request->post('status'); // active or inactive

        if (empty($uid)) {
            Session::setFlash('error', 'User ID is required.');
            return $response->redirect('/coordinator/students');
        }

        $db = Database::getInstance();
        $db->query("UPDATE users SET status = :s WHERE id = :uid", [
            ':s' => $status,
            ':uid' => $uid
        ]);

        $msg = ($status === 'active') ? 'Student account activated successfully.' : 'Student account deactivated successfully.';
        Session::setFlash('success', $msg);
        return $response->redirect('/coordinator/students');
    }

    public function importCsv() {
        Auth::requireRole(['admin', 'coordinator']);
        $request = new Request();
        $response = new Response();

        if (!$request->isPost()) {
            return $response->redirect('/coordinator/students');
        }

        $file = $request->file('csv_file');
        if (!$file) {
            Session::setFlash('error', 'Please upload a valid CSV file.');
            return $response->redirect('/coordinator/students');
        }

        $db = Database::getInstance();
        $handle = fopen($file['tmp_name'], 'r');
        
        // Skip header
        fgetcsv($handle);

        $db->exec("BEGIN TRANSACTION;");
        $successCount = 0;

        try {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 7) continue;

                $username = strtolower(trim($row[0]));
                $email = strtolower(trim($row[1]));
                $matric = trim($row[2]);
                $first = trim($row[3]);
                $last = trim($row[4]);
                $deptCode = trim($row[5]);
                $course = trim($row[6]);
                $year = (int)$row[7] ?? 4;
                $phone = $row[8] ?? '';

                // Get department id
                $deptId = $db->querySingle("SELECT id FROM departments WHERE code = :c", [':c' => $deptCode]);
                if (!$deptId) {
                    // Default to first department if code not found
                    $deptId = $db->querySingle("SELECT id FROM departments LIMIT 1") ?? 1;
                }

                // Check duplicates
                $exists = $db->querySingle("SELECT id FROM users WHERE username = :u OR email = :e", [':u' => $username, ':e' => $email]);
                if ($exists) continue;

                $passwordHash = password_hash($matric, PASSWORD_BCRYPT); // default password is matric number

                // Insert User
                $stmt = $db->prepare("INSERT INTO users (username, email, password_hash, role, status) VALUES (:u, :e, :p, 'student', 'active')");
                $stmt->bindValue(':u', $username);
                $stmt->bindValue(':e', $email);
                $stmt->bindValue(':p', $passwordHash);
                $stmt->execute();
                
                $userId = $db->lastInsertRowID();

                // Insert Student
                $stmtStud = $db->prepare("INSERT INTO students (user_id, matric_no, first_name, last_name, department_id, course_of_study, year_of_study, phone, siwes_status) VALUES (:uid, :mat, :first, :last, :dept, :course, :yr, :phone, 'unallocated')");
                $stmtStud->bindValue(':uid', $userId);
                $stmtStud->bindValue(':mat', $matric);
                $stmtStud->bindValue(':first', $first);
                $stmtStud->bindValue(':last', $last);
                $stmtStud->bindValue(':dept', $deptId);
                $stmtStud->bindValue(':course', $course);
                $stmtStud->bindValue(':yr', $year);
                $stmtStud->bindValue(':phone', $phone);
                $stmtStud->execute();

                $successCount++;
            }

            fclose($handle);
            $db->exec("COMMIT;");
            Session::setFlash('success', "Import completed. Successfully loaded $successCount new students.");

        } catch (\Exception $e) {
            fclose($handle);
            $db->exec("ROLLBACK;");
            Session::setFlash('error', 'CSV Import error: ' . $e->getMessage());
        }

        return $response->redirect('/coordinator/students');
    }

    public function uploadDoc() {
        Auth::requireRole('student');
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();
        $user = Auth::user();
        $studentId = $user['profile']['id'];

        if (!$request->isPost()) {
            return $response->redirect('/settings');
        }

        $type = $request->post('doc_type'); // passport, cv, placement_letter
        $file = $request->file('upload_file');

        if (empty($type) || !$file) {
            Session::setFlash('error', 'File and document type are required.');
            return $response->redirect('/settings');
        }

        $uploadDir = dirname(dirname(__DIR__)) . '/public/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Validation rules
        if ($type === 'passport') {
            if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
                Session::setFlash('error', 'Passport must be an image (jpg, jpeg, png).');
                return $response->redirect('/settings');
            }
        } else {
            if ($ext !== 'pdf') {
                Session::setFlash('error', 'Document must be a PDF file.');
                return $response->redirect('/settings');
            }
        }

        $filename = $type . '_' . $studentId . '_' . time() . '.' . $ext;
        $destPath = $uploadDir . $filename;
        $dbPath = '/siwes/public/uploads/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $destPath)) {
            $dbColumn = $type . '_path';
            $db->query("UPDATE students SET {$dbColumn} = :p WHERE id = :sid", [
                ':p' => $dbPath,
                ':sid' => $studentId
            ]);

            Session::setFlash('success', ucfirst(str_replace('_', ' ', $type)) . ' uploaded successfully.');
        } else {
            Session::setFlash('error', 'Failed to upload document to storage.');
        }

        return $response->redirect('/settings');
    }

    public function downloadLetter() {
        Auth::requireRole('student');
        $db = Database::getInstance();
        $user = Auth::user();
        $studentId = $user['profile']['id'];

        // Get student information and allocation
        $student = $db->fetchRow("SELECT s.*, d.name as department_name, c.name as company_name, c.address as company_address, c.contact_person FROM students s JOIN departments d ON s.department_id = d.id LEFT JOIN allocations a ON a.student_id = s.id LEFT JOIN companies c ON a.company_id = c.id WHERE s.id = :sid LIMIT 1", [
            ':sid' => $studentId
        ]);

        if (empty($student['company_name'])) {
            Session::setFlash('error', 'You must be allocated to a company before printing your placement letter.');
            $response = new Response();
            return $response->redirect('/student/dashboard');
        }

        // Render clean print template
        $response = new Response();
        return $response->render('students/letter', ['student' => $student], 'main');
    }

    public function update() {
        Auth::requireRole(['admin', 'coordinator']);
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();

        if (!$request->isPost()) {
            return $response->redirect('/coordinator/students');
        }

        $sid = $request->post('student_id');
        $first = trim($request->post('first_name'));
        $last = trim($request->post('last_name'));
        $email = strtolower(trim($request->post('email')));
        $phone = trim($request->post('phone'));
        $matric = trim($request->post('matric_no'));
        $deptId = $request->post('department_id');
        $course = trim($request->post('course_of_study'));
        $year = $request->post('year_of_study');

        if (empty($sid) || empty($first) || empty($last) || empty($email) || empty($matric) || empty($deptId) || empty($course) || empty($year)) {
            Session::setFlash('error', 'All fields except phone number are required.');
            return $response->redirect('/coordinator/students');
        }

        // Get user_id from student ID
        $userId = $db->querySingle("SELECT user_id FROM students WHERE id = :sid", [':sid' => $sid]);
        if (!$userId) {
            Session::setFlash('error', 'Student record not found.');
            return $response->redirect('/coordinator/students');
        }

        $db->getConnection()->exec("BEGIN TRANSACTION;");
        try {
            // Update users table email
            $stmt = $db->getConnection()->prepare("UPDATE users SET email = :e WHERE id = :uid");
            $stmt->bindValue(':e', $email);
            $stmt->bindValue(':uid', $userId);
            $stmt->execute();

            // Update students table fields
            $stmtStud = $db->getConnection()->prepare("UPDATE students SET 
                matric_no = :mat, 
                first_name = :first, 
                last_name = :last, 
                department_id = :dept, 
                course_of_study = :course, 
                year_of_study = :yr, 
                phone = :phone 
                WHERE id = :sid");
            $stmtStud->bindValue(':mat', $matric);
            $stmtStud->bindValue(':first', $first);
            $stmtStud->bindValue(':last', $last);
            $stmtStud->bindValue(':dept', $deptId);
            $stmtStud->bindValue(':course', $course);
            $stmtStud->bindValue(':yr', $year);
            $stmtStud->bindValue(':phone', $phone);
            $stmtStud->bindValue(':sid', $sid);
            $stmtStud->execute();

            $db->getConnection()->exec("COMMIT;");
            Session::setFlash('success', 'Student details updated successfully.');
        } catch (\Exception $e) {
            $db->getConnection()->exec("ROLLBACK;");
            Session::setFlash('error', 'Update failed: ' . $e->getMessage());
        }

        return $response->redirect('/coordinator/students');
    }
}
