<?php

namespace App\Controllers;

use App\Core\Response;
use App\Core\Request;
use App\Core\Session;
use App\Core\Auth;
use App\Core\Database;

class UserController {
    public function index() {
        Auth::requireRole(['admin', 'coordinator']);
        $request = new Request();
        $db = Database::getInstance();

        $search = trim($request->get('search', ''));
        $roleFilter = trim($request->get('role', ''));
        $statusFilter = trim($request->get('status', ''));

        // Pagination
        $page = max(1, (int)$request->get('page', 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Base Query Build
        $sql = "SELECT id, username, email, role, status, created_at FROM users WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (username LIKE :search OR email LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        if (!empty($roleFilter)) {
            $sql .= " AND role = :role";
            $params[':role'] = $roleFilter;
        }

        if (!empty($statusFilter)) {
            $sql .= " AND status = :status";
            $params[':status'] = $statusFilter;
        }

        // Count Total
        $countSql = str_replace("SELECT id, username, email, role, status, created_at", "SELECT COUNT(*)", $sql);
        $totalItems = $db->querySingle($countSql, $params);
        $totalPages = ceil($totalItems / $limit);

        // Fetch paginated results
        $sql .= " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
        $users = $db->fetchAll($sql, $params);

        $response = new Response();
        return $response->render('users/index', [
            'users' => $users,
            'filters' => [
                'search' => $search,
                'role' => $roleFilter,
                'status' => $statusFilter
            ],
            'pagination' => [
                'current' => $page,
                'total' => $totalPages,
                'total_items' => $totalItems
            ]
        ]);
    }

    public function suspend() {
        Auth::requireRole(['admin', 'coordinator']);
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();

        if (!$request->isPost()) {
            return $response->redirect('/coordinator/users');
        }

        $userId = $request->post('user_id');
        $status = $request->post('status'); // active or inactive

        if (empty($userId) || !in_array($status, ['active', 'inactive'])) {
            Session::setFlash('error', 'Valid user ID and status are required.');
            return $response->redirect('/coordinator/users');
        }

        // Prevent self-suspension
        $currentUser = Auth::user();
        if ($currentUser['id'] == $userId) {
            Session::setFlash('error', 'You cannot suspend your own account.');
            return $response->redirect('/coordinator/users');
        }

        $db->getConnection()->exec("BEGIN TRANSACTION;");
        try {
            $stmt = $db->getConnection()->prepare("UPDATE users SET status = :s WHERE id = :uid");
            $stmt->bindValue(':s', $status);
            $stmt->bindValue(':uid', $userId);
            $stmt->execute();
            
            $db->getConnection()->exec("COMMIT;");
            $msg = ($status === 'active') ? 'User account activated successfully.' : 'User account suspended successfully.';
            Session::setFlash('success', $msg);
        } catch (\Exception $e) {
            $db->getConnection()->exec("ROLLBACK;");
            Session::setFlash('error', 'Operation failed: ' . $e->getMessage());
        }

        return $response->redirect('/coordinator/users');
    }

    public function delete() {
        Auth::requireRole(['admin', 'coordinator']);
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();

        if (!$request->isPost()) {
            return $response->redirect('/coordinator/users');
        }

        $userId = $request->post('user_id');

        if (empty($userId)) {
            Session::setFlash('error', 'User ID is required.');
            return $response->redirect('/coordinator/users');
        }

        // Prevent self-deletion
        $currentUser = Auth::user();
        if ($currentUser['id'] == $userId) {
            Session::setFlash('error', 'You cannot delete your own account.');
            return $response->redirect('/coordinator/users');
        }

        $db->getConnection()->exec("BEGIN TRANSACTION;");
        try {
            $stmt = $db->getConnection()->prepare("DELETE FROM users WHERE id = :uid");
            $stmt->bindValue(':uid', $userId);
            $stmt->execute();

            $db->getConnection()->exec("COMMIT;");
            Session::setFlash('success', 'User account deleted successfully.');
        } catch (\Exception $e) {
            $db->getConnection()->exec("ROLLBACK;");
            Session::setFlash('error', 'Deletion failed: ' . $e->getMessage());
        }

        return $response->redirect('/coordinator/users');
    }
}
