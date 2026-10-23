<?php

namespace App\Controllers;

use App\Core\Response;
use App\Core\Request;
use App\Core\Session;
use App\Core\Auth;
use App\Core\Database;

class LetterController {

    /**
     * Admin & Coordinator view and edit the official University SIWES Placement Letter Template
     */
    public function editPlacementLetter() {
        Auth::requireRole(['admin', 'coordinator']);
        $db = Database::getInstance();

        $settingsRaw = $db->fetchAll("SELECT * FROM settings");
        $settings = [];
        foreach ($settingsRaw as $row) {
            $settings[$row['key']] = $row['value'];
        }

        // Sample student preview data
        $sampleStudent = [
            'first_name' => 'Samuel',
            'last_name' => 'Alao',
            'matric_no' => 'CSC/2021/045',
            'department_name' => 'Computer Science',
            'company_name' => 'Flutterwave Inc.',
            'company_address' => 'Plot 8, Providence Street, Lekki Phase 1, Lagos'
        ];

        $response = new Response();
        return $response->render('letters/edit_placement', [
            'settings' => $settings,
            'sampleStudent' => $sampleStudent
        ]);
    }

    /**
     * Save updated SIWES Placement Letter Template
     */
    public function updatePlacementLetter() {
        Auth::requireRole(['admin', 'coordinator']);
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();

        if (!$request->isPost()) {
            return $response->redirect('/coordinator/letters/placement');
        }

        $fields = [
            'placement_letter_institution',
            'placement_letter_faculty',
            'placement_letter_unit',
            'placement_letter_address',
            'placement_letter_ref_prefix',
            'placement_letter_subject',
            'placement_letter_body',
            'placement_letter_signatory_name',
            'placement_letter_signatory_title'
        ];

        $conn = $db->getConnection();
        $conn->exec("BEGIN TRANSACTION;");
        try {
            foreach ($fields as $field) {
                $val = trim($request->post($field, ''));
                $stmt = $conn->prepare("INSERT INTO settings (key, value) VALUES (:k, :v) ON CONFLICT(key) DO UPDATE SET value = :v, updated_at = CURRENT_TIMESTAMP");
                $stmt->bindValue(':k', $field);
                $stmt->bindValue(':v', $val);
                $stmt->execute();
            }
            $conn->exec("COMMIT;");
            Session::setFlash('success', 'Official SIWES Placement Letter template updated successfully!');
        } catch (\Exception $e) {
            $conn->exec("ROLLBACK;");
            Session::setFlash('error', 'Failed to update placement letter: ' . $e->getMessage());
        }

        return $response->redirect('/coordinator/letters/placement');
    }

    /**
     * Company Supervisor (or Admin/Coordinator) view and edit their specific Company Application Letter
     */
    public function editCompanyApplicationLetter() {
        Auth::requireRole(['supervisor', 'admin', 'coordinator']);
        $request = new Request();
        $db = Database::getInstance();
        $user = Auth::user();
        $role = Auth::getRole();

        $companyId = null;
        if ($role === 'supervisor') {
            $companyId = $user['profile']['company_id'] ?? null;
            if (empty($companyId)) {
                // If institutional supervisor, show first company or redirect
                $companyId = (int)$request->get('company_id', 1);
            }
        } else {
            // Admin or coordinator can select which company to edit
            $companyId = (int)$request->get('company_id', 1);
        }

        $company = $db->fetchRow("SELECT * FROM companies WHERE id = :cid LIMIT 1", [':cid' => $companyId]);
        if (!$company) {
            // fallback to first company
            $company = $db->fetchRow("SELECT * FROM companies LIMIT 1");
        }

        // List all companies for selection dropdown if admin/coordinator
        $allCompanies = ($role === 'admin' || $role === 'coordinator') 
            ? $db->fetchAll("SELECT id, name FROM companies ORDER BY name ASC")
            : [];

        // Sample student preview data
        $sampleStudent = [
            'first_name' => 'Samuel',
            'last_name' => 'Alao',
            'matric_no' => 'CSC/2021/045',
            'department_name' => 'Computer Science'
        ];

        $response = new Response();
        return $response->render('letters/edit_company', [
            'company' => $company,
            'allCompanies' => $allCompanies,
            'sampleStudent' => $sampleStudent,
            'role' => $role
        ]);
    }

    /**
     * Save updated Company Application Letter Template
     */
    public function updateCompanyApplicationLetter() {
        Auth::requireRole(['supervisor', 'admin', 'coordinator']);
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();
        $user = Auth::user();
        $role = Auth::getRole();

        if (!$request->isPost()) {
            return $response->redirect('/company/application-letter/edit');
        }

        $companyId = (int)$request->post('company_id');
        if ($role === 'supervisor' && !empty($user['profile']['company_id'])) {
            $companyId = (int)$user['profile']['company_id']; // Enforce their own company
        }

        $title = trim($request->post('application_letter_title', ''));
        $content = trim($request->post('application_letter_content', ''));
        $signatory = trim($request->post('application_letter_signatory', ''));

        if (empty($companyId) || empty($content)) {
            Session::setFlash('error', 'Company ID and letter content are required.');
            return $response->redirect('/company/application-letter/edit?company_id=' . $companyId);
        }

        try {
            $stmt = $db->getConnection()->prepare("UPDATE companies SET 
                application_letter_title = :t,
                application_letter_content = :c,
                application_letter_signatory = :s,
                updated_at = CURRENT_TIMESTAMP
                WHERE id = :cid");
            $stmt->bindValue(':t', $title);
            $stmt->bindValue(':c', $content);
            $stmt->bindValue(':s', $signatory);
            $stmt->bindValue(':cid', $companyId);
            $stmt->execute();

            Session::setFlash('success', 'Company application & acceptance letter template saved successfully!');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to update company letter: ' . $e->getMessage());
        }

        return $response->redirect('/company/application-letter/edit?company_id=' . $companyId);
    }
}
