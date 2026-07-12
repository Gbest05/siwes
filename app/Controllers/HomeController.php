<?php

namespace App\Controllers;

use App\Core\Response;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;

class HomeController {
    public function index() {
        $db = Database::getInstance();
        
        // Fetch public aggregate statistics
        $stats = [
            'students' => $db->querySingle("SELECT COUNT(*) FROM students"),
            'companies' => $db->querySingle("SELECT COUNT(*) FROM companies WHERE status = 'verified'"),
            'supervisors' => $db->querySingle("SELECT COUNT(*) FROM supervisors"),
            'allocations' => $db->querySingle("SELECT COUNT(*) FROM allocations")
        ];

        // Fetch settings
        $settingsRaw = $db->fetchAll("SELECT * FROM settings");
        $settings = [];
        foreach ($settingsRaw as $s) {
            $settings[$s['key']] = $s['value'];
        }

        // Fetch specialized fields
        $fields = $db->fetchAll("SELECT * FROM specialized_fields WHERE status = 'Active' ORDER BY id ASC");

        $response = new Response();
        return $response->render('landing', [
            'stats' => $stats,
            'settings' => $settings,
            'specialized_fields' => $fields
        ]);
    }

    public function contact() {
        $request = new Request();
        $response = new Response();
        
        if (!$request->isPost()) {
            return $response->redirect('/');
        }

        $name = trim($request->post('name'));
        $email = strtolower(trim($request->post('email')));
        $subject = trim($request->post('subject') ?? 'SIWES Contact Message');
        $message = trim($request->post('message'));

        if (empty($name) || empty($email) || empty($message)) {
            Session::setFlash('error', 'All fields are required.');
            return $response->redirect('/#contact');
        }

        $db = Database::getInstance();
        
        // Save to contact_messages table
        $db->query("INSERT INTO contact_messages (name, email, subject, message, status) VALUES (:name, :email, :subject, :message, 'unread')", [
            ':name' => $name,
            ':email' => $email,
            ':subject' => $subject,
            ':message' => $message
        ]);

        // Insert audit log
        $db->query("INSERT INTO audit_logs (action, ip_address) VALUES (:act, :ip)", [
            ':act' => "Contact form message submitted by $name ($email)",
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ]);

        Session::setFlash('success', 'Thank you for your message! Our coordinator will contact you shortly.');
        return $response->redirect('/');
    }
}
