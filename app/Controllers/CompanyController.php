<?php

namespace App\Controllers;

use App\Core\Response;
use App\Core\Request;
use App\Core\Session;
use App\Core\Auth;
use App\Core\Database;

class CompanyController {
    public function index() {
        Auth::requireRole(['admin', 'coordinator', 'officer', 'student']);
        $request = new Request();
        $db = Database::getInstance();

        $search = $request->get('search', '');
        $category = $request->get('category', '');
        $status = $request->get('status', '');

        // Query Builder
        $sql = "SELECT * FROM companies WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (name LIKE :search OR contact_person LIKE :search OR address LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        if (!empty($category)) {
            $sql .= " AND industry_category = :cat";
            $params[':cat'] = $category;
        }

        // Student role can only see verified companies
        $role = Auth::getRole();
        if ($role === 'student') {
            $sql .= " AND status = 'verified'";
        } elseif (!empty($status)) {
            $sql .= " AND status = :status";
            $params[':status'] = $status;
        }

        $sql .= " ORDER BY name ASC";
        $companies = $db->fetchAll($sql, $params);

        // Fetch categories for dropdown
        $categories = $db->fetchAll("SELECT DISTINCT industry_category FROM companies");

        $response = new Response();
        return $response->render('companies/index', [
            'companies' => $companies,
            'categories' => $categories,
            'filters' => [
                'search' => $search,
                'category' => $category,
                'status' => $status
            ]
        ]);
    }

    public function verify() {
        Auth::requireRole(['admin', 'coordinator']);
        $request = new Request();
        $response = new Response();

        if (!$request->isPost()) {
            return $response->redirect('/coordinator/companies');
        }

        $cid = $request->post('company_id');
        $status = $request->post('status'); // verified or rejected

        if (empty($cid) || empty($status)) {
            Session::setFlash('error', 'Company ID and Status are required.');
            return $response->redirect('/coordinator/companies');
        }

        $db = Database::getInstance();
        $db->query("UPDATE companies SET status = :s WHERE id = :cid", [
            ':s' => $status,
            ':cid' => $cid
        ]);

        // Send notifications to supervisors linked to this company
        $supervisors = $db->fetchAll("SELECT user_id FROM supervisors WHERE company_id = :cid", [':cid' => $cid]);
        foreach ($supervisors as $s) {
            $stmtNotif = $db->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (:uid, 'Company Verification Updated', :msg, 'info')");
            $stmtNotif->bindValue(':uid', $s['user_id']);
            $stmtNotif->bindValue(':msg', "Your linked company account verification status has been changed to: $status.");
            $stmtNotif->execute();
        }

        Session::setFlash('success', "Company status updated to $status successfully.");
        return $response->redirect('/coordinator/companies');
    }
}
