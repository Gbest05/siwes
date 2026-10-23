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
        $departments = $db->fetchAll("SELECT * FROM departments ORDER BY name ASC");
        // Fetch academic levels
        $levels = $db->fetchAll("SELECT * FROM academic_levels WHERE status = 'active' ORDER BY year_value ASC");

        $response = new Response();
        return $response->render('students/index', [
            'students' => $students,
            'departments' => $departments,
            'levels' => $levels,
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
                $stmt = $db->getConnection()->prepare("INSERT INTO users (username, email, password_hash, role, status) VALUES (:u, :e, :p, 'student', 'active')");
                $stmt->bindValue(':u', $username);
                $stmt->bindValue(':e', $email);
                $stmt->bindValue(':p', $passwordHash);
                $stmt->execute();
                
                $userId = $db->lastInsertRowID();

                // Insert Student
                $stmtStud = $db->getConnection()->prepare("INSERT INTO students (user_id, matric_no, first_name, last_name, department_id, course_of_study, year_of_study, phone, siwes_status) VALUES (:uid, :mat, :first, :last, :dept, :course, :yr, :phone, 'unallocated')");
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
        Auth::requireRole(['student', 'admin', 'coordinator']);
        $db = Database::getInstance();
        $user = Auth::user();
        $role = Auth::getRole();
        $studentId = ($role === 'student') ? $user['profile']['id'] : (int)(new Request())->get('student_id', 1);
        $response = new Response();

        // Get student information and allocation
        $student = $db->fetchRow("SELECT s.*, d.name as department_name, c.name as company_name, c.address as company_address, c.contact_person FROM students s JOIN departments d ON s.department_id = d.id LEFT JOIN allocations a ON a.student_id = s.id LEFT JOIN companies c ON a.company_id = c.id WHERE s.id = :sid LIMIT 1", [
            ':sid' => $studentId
        ]);

        if (empty($student['company_name'])) {
            Session::setFlash('error', 'Student has not been allocated to a company yet.');
            return $response->redirect($role === 'student' ? '/student/dashboard' : '/coordinator/allocations');
        }

        if ($role === 'student' && ($student['document_status'] ?? 'pending') !== 'approved' && ($student['siwes_status'] ?? 'unallocated') !== 'allocated') {
            Session::setFlash('warning', 'Your Placement Letter will be available once your Departmental Supervisor reviews and approves your uploaded SIWES documents.');
            return $response->redirect('/student/dashboard');
        }

        $settingsRaw = $db->fetchAll("SELECT * FROM settings");
        $settings = [];
        foreach ($settingsRaw as $row) {
            $settings[$row['key']] = $row['value'];
        }

        // Render clean print template
        return $response->render('students/letter', [
            'student' => $student,
            'settings' => $settings,
            'role' => $role
        ], 'main');
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

    public function addStudent() {
        Auth::requireRole(['admin', 'coordinator']);
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();

        if (!$request->isPost()) {
            return $response->redirect('/coordinator/students');
        }

        $username = strtolower(trim($request->post('username', '')));
        $email = strtolower(trim($request->post('email', '')));
        $password = $request->post('password', '');
        $matric = strtoupper(trim($request->post('matric_no', '')));
        $first = trim($request->post('first_name', ''));
        $last = trim($request->post('last_name', ''));
        $phone = trim($request->post('phone', ''));
        $levelInput = $request->post('level', '400 Level');

        if (empty($username) || empty($email) || empty($password) || empty($matric) || empty($first) || empty($last)) {
            Session::setFlash('error', 'All required student fields must be filled.');
            return $response->redirect('/coordinator/students');
        }

        // Check if username, email or matric exists
        $existsUser = $db->fetchRow("SELECT id FROM users WHERE username = :u OR email = :e LIMIT 1", [
            ':u' => $username,
            ':e' => $email
        ]);
        if ($existsUser) {
            Session::setFlash('error', 'Username or email already in use.');
            return $response->redirect('/coordinator/students');
        }

        $existsMatric = $db->fetchRow("SELECT id FROM students WHERE matric_no = :m LIMIT 1", [':m' => $matric]);
        if ($existsMatric) {
            Session::setFlash('error', "Matric number '{$matric}' already registered.");
            return $response->redirect('/coordinator/students');
        }

        // Parse level into year of study
        $levelObj = $db->fetchRow("SELECT * FROM academic_levels WHERE name = :lvl OR code = :lvl LIMIT 1", [':lvl' => $levelInput]);
        if ($levelObj) {
            $year = (int)$levelObj['year_value'];
        } else {
            preg_match('/\d+/', $levelInput, $matches);
            $year = !empty($matches) ? (int)substr($matches[0], 0, 1) : 4;
        }

        // Resolve Department
        $deptIdInput = (int)$request->post('department_id');
        $dept = null;
        if ($deptIdInput > 0) {
            $dept = $db->fetchRow("SELECT * FROM departments WHERE id = :id LIMIT 1", [':id' => $deptIdInput]);
        }
        if (!$dept) {
            $dept = $db->fetchRow("SELECT * FROM departments ORDER BY id ASC LIMIT 1");
        }
        $deptId = $dept ? $dept['id'] : 1;
        $course = $dept ? $dept['name'] : 'Computer Science';

        $db->getConnection()->exec("BEGIN TRANSACTION;");
        try {
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            // Insert into users
            $stmt = $db->getConnection()->prepare("INSERT INTO users (username, email, password_hash, role, status) VALUES (:u, :e, :p, 'student', 'active')");
            $stmt->bindValue(':u', $username);
            $stmt->bindValue(':e', $email);
            $stmt->bindValue(':p', $passwordHash);
            $stmt->execute();

            $userId = $db->lastInsertRowID();

            $stmtStud = $db->getConnection()->prepare("INSERT INTO students (user_id, matric_no, first_name, last_name, department_id, course_of_study, year_of_study, phone, siwes_status, document_status) VALUES (:uid, :mat, :fn, :ln, :dept, :course, :yr, :ph, 'unallocated', 'pending')");
            $stmtStud->bindValue(':uid', $userId);
            $stmtStud->bindValue(':mat', $matric);
            $stmtStud->bindValue(':fn', $first);
            $stmtStud->bindValue(':ln', $last);
            $stmtStud->bindValue(':dept', $deptId);
            $stmtStud->bindValue(':course', $course);
            $stmtStud->bindValue(':yr', $year);
            $stmtStud->bindValue(':ph', $phone);
            $stmtStud->execute();

            $db->getConnection()->exec("COMMIT;");
            Session::setFlash('success', "Student {$first} {$last} ({$matric}) created successfully!");
        } catch (\Exception $e) {
            $db->getConnection()->exec("ROLLBACK;");
            Session::setFlash('error', 'Failed to create student: ' . $e->getMessage());
        }

        return $response->redirect('/coordinator/students');
    }

    public function uploadSIWESDoc() {
        Auth::requireRole('student');
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();
        $user = Auth::user();
        $studentId = $user['profile']['id'];

        if (!$request->isPost()) {
            return $response->redirect('/student/dashboard');
        }

        $student = $db->fetchRow("SELECT * FROM students WHERE id = :sid LIMIT 1", [':sid' => $studentId]);
        if (!$student) {
            Session::setFlash('error', 'Student profile not found.');
            return $response->redirect('/student/dashboard');
        }

        // Check if student has already submitted documents and is not queried
        $isSubmitted = !empty($student['documents_submitted']) || ($student['document_status'] ?? '') === 'approved';
        if ($isSubmitted && ($student['document_status'] ?? '') !== 'queried') {
            Session::setFlash('error', 'Your SIWES documents have already been submitted and locked. You cannot upload another document.');
            return $response->redirect('/student/dashboard');
        }

        $passportFile = $request->file('passport_file');
        $acceptanceFile = $request->file('acceptance_file');

        // Backward compatibility if single document_file was posted
        if (!$passportFile && !$acceptanceFile && $request->file('document_file')) {
            $legacyType = $request->post('doc_type');
            if ($legacyType === 'passport') {
                $passportFile = $request->file('document_file');
            } else {
                $acceptanceFile = $request->file('document_file');
            }
        }

        $hasExistingPassport = !empty($student['passport_path']);
        $hasExistingAcceptance = !empty($student['acceptance_letter_path']);

        $hasNewPassport = $passportFile && !empty($passportFile['name']) && ($passportFile['error'] ?? 1) === UPLOAD_ERR_OK;
        $hasNewAcceptance = $acceptanceFile && !empty($acceptanceFile['name']) && ($acceptanceFile['error'] ?? 1) === UPLOAD_ERR_OK;

        if (!$hasNewPassport && !$hasExistingPassport) {
            Session::setFlash('error', 'Passport Photograph is required. Please select a JPG or PNG image.');
            return $response->redirect('/student/dashboard');
        }

        if (!$hasNewAcceptance && !$hasExistingAcceptance) {
            Session::setFlash('error', 'SIWES Acceptance Letter is required. Please select a PDF or image file.');
            return $response->redirect('/student/dashboard');
        }

        $uploadDir = dirname(dirname(__DIR__)) . '/public/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $newPassportPath = null;
        $newAcceptancePath = null;

        if ($hasNewPassport) {
            $ext = strtolower(pathinfo($passportFile['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
                Session::setFlash('error', 'Passport photo must be a JPG or PNG image.');
                return $response->redirect('/student/dashboard');
            }
            $filename = 'passport_' . $studentId . '_' . time() . '.' . $ext;
            if (move_uploaded_file($passportFile['tmp_name'], $uploadDir . $filename)) {
                $newPassportPath = '/uploads/' . $filename;
            } else {
                Session::setFlash('error', 'Failed to upload passport photograph. Please try again.');
                return $response->redirect('/student/dashboard');
            }
        }

        if ($hasNewAcceptance) {
            $ext = strtolower(pathinfo($acceptanceFile['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['pdf', 'jpg', 'jpeg', 'png'])) {
                Session::setFlash('error', 'Acceptance Letter must be a PDF or image file (JPG, PNG).');
                return $response->redirect('/student/dashboard');
            }
            $filename = 'acceptance_letter_' . $studentId . '_' . time() . '.' . $ext;
            if (move_uploaded_file($acceptanceFile['tmp_name'], $uploadDir . $filename)) {
                $newAcceptancePath = '/uploads/' . $filename;
            } else {
                Session::setFlash('error', 'Failed to upload acceptance letter. Please try again.');
                return $response->redirect('/student/dashboard');
            }
        }

        if ($newPassportPath) {
            $db->query("UPDATE students SET passport_path = :p WHERE id = :sid", [
                ':p' => $newPassportPath,
                ':sid' => $studentId
            ]);
            $db->query("UPDATE users SET profile_pic = :p WHERE id = :uid", [
                ':p' => $newPassportPath,
                ':uid' => $user['id']
            ]);
        }

        if ($newAcceptancePath) {
            $db->query("UPDATE students SET acceptance_letter_path = :p WHERE id = :sid", [
                ':p' => $newAcceptancePath,
                ':sid' => $studentId
            ]);
        }

        // Mark documents as submitted and pending review - locking future uploads
        $db->query("UPDATE students SET documents_submitted = 1, document_status = 'pending' WHERE id = :sid", [
            ':sid' => $studentId
        ]);

        // Notify departmental or assigned supervisor
        $alloc = $db->fetchRow("SELECT institution_supervisor_id, supervisor_id FROM allocations WHERE student_id = :sid LIMIT 1", [':sid' => $studentId]);
        $supervisorUserId = null;
        if ($alloc && !empty($alloc['institution_supervisor_id'])) {
            $supervisorUserId = $db->querySingle("SELECT user_id FROM supervisors WHERE id = :svid", [':svid' => $alloc['institution_supervisor_id']]);
        } elseif ($alloc && !empty($alloc['supervisor_id'])) {
            $supervisorUserId = $db->querySingle("SELECT user_id FROM supervisors WHERE id = :svid", [':svid' => $alloc['supervisor_id']]);
        }

        if ($supervisorUserId) {
            $stmtNotif = $db->getConnection()->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (:uid, 'SIWES Documents Submitted', :msg, 'info')");
            $stmtNotif->bindValue(':uid', $supervisorUserId);
            $stmtNotif->bindValue(':msg', "Student {$user['profile']['first_name']} {$user['profile']['last_name']} ({$student['matric_no']}) has submitted SIWES documents for review.");
            $stmtNotif->execute();
        }

        Session::setFlash('success', 'Documents uploaded and submitted successfully for supervisor verification. Your documents are now locked for review.');
        return $response->redirect('/student/dashboard');
    }

    public function downloadApplicationLetter() {
        Auth::requireRole(['student', 'admin', 'coordinator', 'supervisor']);
        $db = Database::getInstance();
        $user = Auth::user();
        $role = Auth::getRole();
        $studentId = ($role === 'student') ? $user['profile']['id'] : (int)(new Request())->get('student_id', 1);

        $student = $db->fetchRow("SELECT s.*, d.name as department_name, c.name as company_name, c.address as company_address, c.contact_person, c.email as company_email, c.phone as company_phone, c.application_letter_title, c.application_letter_content, c.application_letter_signatory 
            FROM students s 
            JOIN departments d ON s.department_id = d.id 
            LEFT JOIN allocations a ON a.student_id = s.id 
            LEFT JOIN companies c ON a.company_id = c.id 
            WHERE s.id = :sid LIMIT 1", [
            ':sid' => $studentId
        ]);

        $settingsRaw = $db->fetchAll("SELECT * FROM settings");
        $settings = [];
        foreach ($settingsRaw as $row) {
            $settings[$row['key']] = $row['value'];
        }

        $response = new Response();
        return $response->render('students/application_letter', [
            'student' => $student,
            'settings' => $settings,
            'role' => $role
        ], 'main');
    }
}
