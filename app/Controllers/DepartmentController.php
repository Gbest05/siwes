<?php

namespace App\Controllers;

use App\Core\Response;
use App\Core\Request;
use App\Core\Session;
use App\Core\Auth;
use App\Core\Database;

class DepartmentController {
    public function index() {
        Auth::requireRole(['admin', 'coordinator']);
        $request = new Request();
        $db = Database::getInstance();

        $search = trim($request->get('search', ''));
        $tab = trim($request->get('tab', 'departments'));

        // Base Departments Query
        $sql = "SELECT d.*, (SELECT COUNT(*) FROM students s WHERE s.department_id = d.id) as student_count FROM departments d WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (d.name LIKE :search OR d.code LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY d.id ASC";
        $departments = $db->fetchAll($sql, $params);

        // Fetch Academic Levels with student counts
        $levels = $db->fetchAll("SELECT al.*, (SELECT COUNT(*) FROM students s WHERE s.year_of_study = al.year_value) as student_count 
            FROM academic_levels al 
            ORDER BY al.year_value ASC, al.id ASC");

        $response = new Response();
        return $response->render('departments/index', [
            'departments' => $departments,
            'levels' => $levels,
            'search' => $search,
            'activeTab' => in_array($tab, ['departments', 'levels']) ? $tab : 'departments'
        ]);
    }

    public function add() {
        Auth::requireRole(['admin', 'coordinator']);
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();

        if (!$request->isPost()) {
            return $response->redirect('/coordinator/departments?tab=departments');
        }

        $name = trim($request->post('name', ''));
        $code = strtoupper(trim($request->post('code', '')));
        $status = in_array($request->post('status'), ['active', 'inactive']) ? $request->post('status') : 'active';
        $showOnSignup = ($request->post('show_on_signup') !== null && $request->post('show_on_signup') !== '0') ? 1 : 0;

        if (empty($name) || empty($code)) {
            Session::setFlash('error', 'Department Name and Department Code are required.');
            return $response->redirect('/coordinator/departments?tab=departments');
        }

        // Check code uniqueness
        $exists = $db->fetchRow("SELECT id FROM departments WHERE UPPER(code) = :c LIMIT 1", [':c' => $code]);
        if ($exists) {
            Session::setFlash('error', "Department Code '$code' already exists. Please choose a unique code.");
            return $response->redirect('/coordinator/departments?tab=departments');
        }

        $db->getConnection()->exec("BEGIN TRANSACTION;");
        try {
            $stmt = $db->getConnection()->prepare("INSERT INTO departments (name, code, status, show_on_signup) VALUES (:n, :c, :s, :sos)");
            $stmt->bindValue(':n', $name);
            $stmt->bindValue(':c', $code);
            $stmt->bindValue(':s', $status);
            $stmt->bindValue(':sos', $showOnSignup);
            $stmt->execute();

            $db->getConnection()->exec("COMMIT;");
            Session::setFlash('success', "Department '{$name}' ({$code}) created successfully!");
        } catch (\Exception $e) {
            $db->getConnection()->exec("ROLLBACK;");
            Session::setFlash('error', 'Failed to create department: ' . $e->getMessage());
        }

        return $response->redirect('/coordinator/departments?tab=departments');
    }

    public function update() {
        Auth::requireRole(['admin', 'coordinator']);
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();

        if (!$request->isPost()) {
            return $response->redirect('/coordinator/departments?tab=departments');
        }

        $id = (int)$request->post('department_id');
        $name = trim($request->post('name', ''));
        $code = strtoupper(trim($request->post('code', '')));
        $status = in_array($request->post('status'), ['active', 'inactive']) ? $request->post('status') : 'active';
        $showOnSignup = ($request->post('show_on_signup') !== null && $request->post('show_on_signup') !== '0') ? 1 : 0;

        if (empty($id) || empty($name) || empty($code)) {
            Session::setFlash('error', 'All fields are required.');
            return $response->redirect('/coordinator/departments?tab=departments');
        }

        // Check code uniqueness for other departments
        $exists = $db->fetchRow("SELECT id FROM departments WHERE UPPER(code) = :c AND id != :id LIMIT 1", [
            ':c' => $code,
            ':id' => $id
        ]);
        if ($exists) {
            Session::setFlash('error', "Department Code '$code' is already in use by another department.");
            return $response->redirect('/coordinator/departments?tab=departments');
        }

        $db->getConnection()->exec("BEGIN TRANSACTION;");
        try {
            $stmt = $db->getConnection()->prepare("UPDATE departments SET name = :n, code = :c, status = :s, show_on_signup = :sos WHERE id = :id");
            $stmt->bindValue(':n', $name);
            $stmt->bindValue(':c', $code);
            $stmt->bindValue(':s', $status);
            $stmt->bindValue(':sos', $showOnSignup);
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            $db->getConnection()->exec("COMMIT;");
            Session::setFlash('success', 'Department details updated successfully.');
        } catch (\Exception $e) {
            $db->getConnection()->exec("ROLLBACK;");
            Session::setFlash('error', 'Update failed: ' . $e->getMessage());
        }

        return $response->redirect('/coordinator/departments?tab=departments');
    }

    public function delete() {
        Auth::requireRole(['admin', 'coordinator']);
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();

        if (!$request->isPost()) {
            return $response->redirect('/coordinator/departments?tab=departments');
        }

        $id = (int)$request->post('department_id');

        if (empty($id)) {
            Session::setFlash('error', 'Department ID is required.');
            return $response->redirect('/coordinator/departments?tab=departments');
        }

        // Check minimum departments
        $totalDepts = (int)$db->querySingle("SELECT COUNT(*) FROM departments");
        if ($totalDepts <= 1) {
            Session::setFlash('error', 'Cannot delete the only existing department. At least one department must remain.');
            return $response->redirect('/coordinator/departments?tab=departments');
        }

        // Check if students are linked to this department
        $studentCount = (int)$db->querySingle("SELECT COUNT(*) FROM students WHERE department_id = :id", [':id' => $id]);
        if ($studentCount > 0) {
            Session::setFlash('error', "Cannot delete this department because {$studentCount} registered student(s) are linked to it. Reassign students first.");
            return $response->redirect('/coordinator/departments?tab=departments');
        }

        $db->getConnection()->exec("BEGIN TRANSACTION;");
        try {
            $stmt = $db->getConnection()->prepare("DELETE FROM departments WHERE id = :id");
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            $db->getConnection()->exec("COMMIT;");
            Session::setFlash('success', 'Department deleted successfully.');
        } catch (\Exception $e) {
            $db->getConnection()->exec("ROLLBACK;");
            Session::setFlash('error', 'Deletion failed: ' . $e->getMessage());
        }

        return $response->redirect('/coordinator/departments?tab=departments');
    }

    public function toggleSignup() {
        Auth::requireRole(['admin', 'coordinator']);
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();

        if (!$request->isPost()) {
            return $response->redirect('/coordinator/departments?tab=departments');
        }

        $id = (int)$request->post('department_id');
        $dept = $db->fetchRow("SELECT * FROM departments WHERE id = :id LIMIT 1", [':id' => $id]);

        if (!$dept) {
            Session::setFlash('error', 'Department not found.');
            return $response->redirect('/coordinator/departments?tab=departments');
        }

        $newVal = ($dept['show_on_signup'] == 1) ? 0 : 1;
        $db->query("UPDATE departments SET show_on_signup = :val WHERE id = :id", [
            ':val' => $newVal,
            ':id' => $id
        ]);

        $statusText = ($newVal === 1) ? 'now visible' : 'now hidden';
        Session::setFlash('success', "Department '{$dept['name']}' is {$statusText} on the student sign-up page.");
        return $response->redirect('/coordinator/departments?tab=departments');
    }

    // ==========================================
    // Academic Level Actions
    // ==========================================

    public function addLevel() {
        Auth::requireRole(['admin', 'coordinator']);
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();

        if (!$request->isPost()) {
            return $response->redirect('/coordinator/departments?tab=levels');
        }

        $name = trim($request->post('name', ''));
        $code = trim($request->post('code', ''));
        $yearValue = (int)$request->post('year_value', 4);
        $status = in_array($request->post('status'), ['active', 'inactive']) ? $request->post('status') : 'active';
        $showOnSignup = ($request->post('show_on_signup') !== null && $request->post('show_on_signup') !== '0') ? 1 : 0;

        if (empty($name) || empty($code)) {
            Session::setFlash('error', 'Level Name and Level Code are required.');
            return $response->redirect('/coordinator/departments?tab=levels');
        }

        // Check uniqueness
        $exists = $db->fetchRow("SELECT id FROM academic_levels WHERE UPPER(code) = :c LIMIT 1", [':c' => strtoupper($code)]);
        if ($exists) {
            Session::setFlash('error', "Academic Level Code '{$code}' already exists. Please choose a unique code.");
            return $response->redirect('/coordinator/departments?tab=levels');
        }

        $db->getConnection()->exec("BEGIN TRANSACTION;");
        try {
            $stmt = $db->getConnection()->prepare("INSERT INTO academic_levels (name, code, year_value, status, show_on_signup) VALUES (:n, :c, :y, :s, :sos)");
            $stmt->bindValue(':n', $name);
            $stmt->bindValue(':c', $code);
            $stmt->bindValue(':y', $yearValue);
            $stmt->bindValue(':s', $status);
            $stmt->bindValue(':sos', $showOnSignup);
            $stmt->execute();

            $db->getConnection()->exec("COMMIT;");
            Session::setFlash('success', "Academic Level '{$name}' created successfully!");
        } catch (\Exception $e) {
            $db->getConnection()->exec("ROLLBACK;");
            Session::setFlash('error', 'Failed to create academic level: ' . $e->getMessage());
        }

        return $response->redirect('/coordinator/departments?tab=levels');
    }

    public function updateLevel() {
        Auth::requireRole(['admin', 'coordinator']);
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();

        if (!$request->isPost()) {
            return $response->redirect('/coordinator/departments?tab=levels');
        }

        $id = (int)$request->post('level_id');
        $name = trim($request->post('name', ''));
        $code = trim($request->post('code', ''));
        $yearValue = (int)$request->post('year_value', 4);
        $status = in_array($request->post('status'), ['active', 'inactive']) ? $request->post('status') : 'active';
        $showOnSignup = ($request->post('show_on_signup') !== null && $request->post('show_on_signup') !== '0') ? 1 : 0;

        if (empty($id) || empty($name) || empty($code)) {
            Session::setFlash('error', 'All fields are required.');
            return $response->redirect('/coordinator/departments?tab=levels');
        }

        // Check code uniqueness excluding current
        $exists = $db->fetchRow("SELECT id FROM academic_levels WHERE UPPER(code) = :c AND id != :id LIMIT 1", [
            ':c' => strtoupper($code),
            ':id' => $id
        ]);
        if ($exists) {
            Session::setFlash('error', "Academic Level Code '{$code}' is already in use by another level.");
            return $response->redirect('/coordinator/departments?tab=levels');
        }

        $db->getConnection()->exec("BEGIN TRANSACTION;");
        try {
            $stmt = $db->getConnection()->prepare("UPDATE academic_levels SET name = :n, code = :c, year_value = :y, status = :s, show_on_signup = :sos WHERE id = :id");
            $stmt->bindValue(':n', $name);
            $stmt->bindValue(':c', $code);
            $stmt->bindValue(':y', $yearValue);
            $stmt->bindValue(':s', $status);
            $stmt->bindValue(':sos', $showOnSignup);
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            $db->getConnection()->exec("COMMIT;");
            Session::setFlash('success', 'Academic level updated successfully.');
        } catch (\Exception $e) {
            $db->getConnection()->exec("ROLLBACK;");
            Session::setFlash('error', 'Update failed: ' . $e->getMessage());
        }

        return $response->redirect('/coordinator/departments?tab=levels');
    }

    public function deleteLevel() {
        Auth::requireRole(['admin', 'coordinator']);
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();

        if (!$request->isPost()) {
            return $response->redirect('/coordinator/departments?tab=levels');
        }

        $id = (int)$request->post('level_id');
        $level = $db->fetchRow("SELECT * FROM academic_levels WHERE id = :id LIMIT 1", [':id' => $id]);

        if (!$level) {
            Session::setFlash('error', 'Academic Level not found.');
            return $response->redirect('/coordinator/departments?tab=levels');
        }

        // Check minimum levels
        $totalLevels = (int)$db->querySingle("SELECT COUNT(*) FROM academic_levels");
        if ($totalLevels <= 1) {
            Session::setFlash('error', 'Cannot delete the only existing academic level.');
            return $response->redirect('/coordinator/departments?tab=levels');
        }

        // Check student references
        $studentCount = (int)$db->querySingle("SELECT COUNT(*) FROM students WHERE year_of_study = :y", [':y' => $level['year_value']]);
        if ($studentCount > 0) {
            Session::setFlash('error', "Cannot delete '{$level['name']}' because {$studentCount} registered student(s) are currently recorded at this academic level.");
            return $response->redirect('/coordinator/departments?tab=levels');
        }

        $db->getConnection()->exec("BEGIN TRANSACTION;");
        try {
            $stmt = $db->getConnection()->prepare("DELETE FROM academic_levels WHERE id = :id");
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            $db->getConnection()->exec("COMMIT;");
            Session::setFlash('success', "Academic Level '{$level['name']}' deleted successfully.");
        } catch (\Exception $e) {
            $db->getConnection()->exec("ROLLBACK;");
            Session::setFlash('error', 'Deletion failed: ' . $e->getMessage());
        }

        return $response->redirect('/coordinator/departments?tab=levels');
    }

    public function toggleLevelSignup() {
        Auth::requireRole(['admin', 'coordinator']);
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();

        if (!$request->isPost()) {
            return $response->redirect('/coordinator/departments?tab=levels');
        }

        $id = (int)$request->post('level_id');
        $level = $db->fetchRow("SELECT * FROM academic_levels WHERE id = :id LIMIT 1", [':id' => $id]);

        if (!$level) {
            Session::setFlash('error', 'Academic level not found.');
            return $response->redirect('/coordinator/departments?tab=levels');
        }

        $newVal = ($level['show_on_signup'] == 1) ? 0 : 1;
        $db->query("UPDATE academic_levels SET show_on_signup = :val WHERE id = :id", [
            ':val' => $newVal,
            ':id' => $id
        ]);

        $statusText = ($newVal === 1) ? 'now visible' : 'now hidden';
        Session::setFlash('success', "Academic Level '{$level['name']}' is {$statusText} on the student sign-up page.");
        return $response->redirect('/coordinator/departments?tab=levels');
    }
}
