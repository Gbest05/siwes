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

        // Base Query Build
        $sql = "SELECT d.*, (SELECT COUNT(*) FROM students s WHERE s.department_id = d.id) as student_count FROM departments d WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (d.name LIKE :search OR d.code LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY d.code ASC";
        $departments = $db->fetchAll($sql, $params);

        $response = new Response();
        return $response->render('departments/index', [
            'departments' => $departments,
            'search' => $search
        ]);
    }

    public function add() {
        Auth::requireRole(['admin', 'coordinator']);
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();

        if (!$request->isPost()) {
            return $response->redirect('/coordinator/departments');
        }

        $name = trim($request->post('name', ''));
        $code = strtoupper(trim($request->post('code', '')));

        if (empty($name) || empty($code)) {
            Session::setFlash('error', 'Both Department Name and Code are required.');
            return $response->redirect('/coordinator/departments');
        }

        // Check uniqueness of code
        $exists = $db->fetchRow("SELECT id FROM departments WHERE code = :c LIMIT 1", [':c' => $code]);
        if ($exists) {
            Session::setFlash('error', "Department Code '$code' is already in use.");
            return $response->redirect('/coordinator/departments');
        }

        $db->getConnection()->exec("BEGIN TRANSACTION;");
        try {
            $stmt = $db->getConnection()->prepare("INSERT INTO departments (name, code) VALUES (:name, :code)");
            $stmt->bindValue(':name', $name);
            $stmt->bindValue(':code', $code);
            $stmt->execute();

            $db->getConnection()->exec("COMMIT;");
            Session::setFlash('success', "Department '$code' added successfully.");
        } catch (\Exception $e) {
            $db->getConnection()->exec("ROLLBACK;");
            Session::setFlash('error', 'Failed to add department: ' . $e->getMessage());
        }

        return $response->redirect('/coordinator/departments');
    }

    public function update() {
        Auth::requireRole(['admin', 'coordinator']);
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();

        if (!$request->isPost()) {
            return $response->redirect('/coordinator/departments');
        }

        $id = $request->post('department_id');
        $name = trim($request->post('name', ''));
        $code = strtoupper(trim($request->post('code', '')));

        if (empty($id) || empty($name) || empty($code)) {
            Session::setFlash('error', 'All fields are required.');
            return $response->redirect('/coordinator/departments');
        }

        // Check code uniqueness for other departments
        $exists = $db->fetchRow("SELECT id FROM departments WHERE code = :c AND id != :id LIMIT 1", [
            ':c' => $code,
            ':id' => $id
        ]);
        if ($exists) {
            Session::setFlash('error', "Department Code '$code' is already in use by another department.");
            return $response->redirect('/coordinator/departments');
        }

        $db->getConnection()->exec("BEGIN TRANSACTION;");
        try {
            $stmt = $db->getConnection()->prepare("UPDATE departments SET name = :n, code = :c WHERE id = :id");
            $stmt->bindValue(':n', $name);
            $stmt->bindValue(':c', $code);
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            $db->getConnection()->exec("COMMIT;");
            Session::setFlash('success', 'Department details updated successfully.');
        } catch (\Exception $e) {
            $db->getConnection()->exec("ROLLBACK;");
            Session::setFlash('error', 'Update failed: ' . $e->getMessage());
        }

        return $response->redirect('/coordinator/departments');
    }

    public function delete() {
        Auth::requireRole(['admin', 'coordinator']);
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();

        if (!$request->isPost()) {
            return $response->redirect('/coordinator/departments');
        }

        $id = $request->post('department_id');

        if (empty($id)) {
            Session::setFlash('error', 'Department ID is required.');
            return $response->redirect('/coordinator/departments');
        }

        // Check if students are linked to this department
        $studentCount = (int)$db->querySingle("SELECT COUNT(*) FROM students WHERE department_id = :id", [':id' => $id]);
        if ($studentCount > 0) {
            Session::setFlash('error', 'Cannot delete this department because it has registered students. Move or delete students first.');
            return $response->redirect('/coordinator/departments');
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

        return $response->redirect('/coordinator/departments');
    }
}
