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
            $stmtNotif = $db->getConnection()->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (:uid, 'Company Verification Updated', :msg, 'info')");
            $stmtNotif->bindValue(':uid', $s['user_id']);
            $stmtNotif->bindValue(':msg', "Your linked company account verification status has been changed to: $status.");
            $stmtNotif->execute();
        }

        Session::setFlash('success', "Company status updated to $status successfully.");
        return $response->redirect('/coordinator/companies');
    }

    public function delete() {
        Auth::requireRole(['admin', 'coordinator']);
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();

        if (!$request->isPost()) {
            return $response->redirect('/coordinator/companies');
        }

        $cid = $request->post('company_id');
        if (empty($cid)) {
            Session::setFlash('error', 'Company ID is required.');
            return $response->redirect('/coordinator/companies');
        }

        $db->getConnection()->exec("BEGIN TRANSACTION;");
        try {
            $stmt = $db->getConnection()->prepare("DELETE FROM companies WHERE id = :cid");
            $stmt->bindValue(':cid', $cid);
            $stmt->execute();

            $db->getConnection()->exec("COMMIT;");
            Session::setFlash('success', 'Company permanently deleted.');
        } catch (\Exception $e) {
            $db->getConnection()->exec("ROLLBACK;");
            Session::setFlash('error', 'Deletion failed: ' . $e->getMessage());
        }

        return $response->redirect('/coordinator/companies');
    }

    public function add() {
        Auth::requireRole(['admin', 'coordinator']);
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();

        if (!$request->isPost()) {
            return $response->redirect('/coordinator/companies');
        }

        $name = trim($request->post('name', ''));
        $email = strtolower(trim($request->post('email', '')));
        $phone = trim($request->post('phone', ''));
        $address = trim($request->post('address', ''));
        $category = trim($request->post('industry_category', 'Information Technology'));
        $slots = max(1, (int)$request->post('available_slots', 5));
        $contactPerson = trim($request->post('contact_person', ''));
        $specializations = trim($request->post('specializations', 'Software Development, Networking'));

        if (empty($name) || empty($email) || empty($address) || empty($contactPerson)) {
            Session::setFlash('error', 'Company name, email, address, and contact person are required.');
            return $response->redirect('/coordinator/companies');
        }

        $exists = $db->fetchRow("SELECT id FROM companies WHERE email = :e LIMIT 1", [':e' => $email]);
        if ($exists) {
            Session::setFlash('error', "A company with email '{$email}' already exists.");
            return $response->redirect('/coordinator/companies');
        }

        $db->getConnection()->exec("BEGIN TRANSACTION;");
        try {
            $stmt = $db->getConnection()->prepare("INSERT INTO companies (name, email, phone, address, latitude, longitude, industry_category, available_slots, contact_person, status, rating, specializations) VALUES (:name, :email, :phone, :address, 7.3912, 3.8424, :cat, :slots, :cp, 'verified', 5, :spec)");
            $stmt->bindValue(':name', $name);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':phone', $phone);
            $stmt->bindValue(':address', $address);
            $stmt->bindValue(':cat', $category);
            $stmt->bindValue(':slots', $slots);
            $stmt->bindValue(':cp', $contactPerson);
            $stmt->bindValue(':spec', $specializations);
            $stmt->execute();

            $db->getConnection()->exec("COMMIT;");
            Session::setFlash('success', "Company '{$name}' created and verified successfully!");
        } catch (\Exception $e) {
            $db->getConnection()->exec("ROLLBACK;");
            Session::setFlash('error', 'Failed to add company: ' . $e->getMessage());
        }

        return $response->redirect('/coordinator/companies');
    }
}
