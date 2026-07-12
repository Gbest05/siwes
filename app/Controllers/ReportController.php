<?php

namespace App\Controllers;

use App\Core\Response;
use App\Core\Request;
use App\Core\Auth;
use App\Core\Database;

class ReportController {
    public function index() {
        Auth::requireRole(['admin', 'coordinator', 'officer']);
        $request = new Request();
        $db = Database::getInstance();

        $reportType = $request->get('type', 'allocations'); // allocations, students, companies, attendance, performance

        $data = [];
        
        if ($reportType === 'allocations') {
            $data = $db->fetchAll("SELECT a.*, s.first_name || ' ' || s.last_name as student_name, s.matric_no, d.code as dept_code, c.name as company_name, sup.first_name || ' ' || sup.last_name as supervisor_name 
                                   FROM allocations a 
                                   JOIN students s ON a.student_id = s.id 
                                   JOIN departments d ON s.department_id = d.id 
                                   JOIN companies c ON a.company_id = c.id 
                                   LEFT JOIN supervisors sup ON a.supervisor_id = sup.id 
                                   ORDER BY c.name ASC");
        } elseif ($reportType === 'students') {
            $data = $db->fetchAll("SELECT s.*, d.name as department_name, d.code as dept_code, u.email 
                                   FROM students s 
                                   JOIN departments d ON s.department_id = d.id 
                                   JOIN users u ON s.user_id = u.id 
                                   ORDER BY s.matric_no ASC");
        } elseif ($reportType === 'companies') {
            // Include filled slots count
            $data = $db->fetchAll("SELECT c.*, 
                                   (SELECT COUNT(*) FROM allocations a WHERE a.company_id = c.id) as filled_slots 
                                   FROM companies c 
                                   ORDER BY c.name ASC");
        } elseif ($reportType === 'attendance') {
            $data = $db->fetchAll("SELECT s.id, s.matric_no, s.first_name || ' ' || s.last_name as student_name, d.code as dept_code,
                                   (SELECT COUNT(*) FROM attendance att WHERE att.student_id = s.id AND att.status = 'present') as present_days,
                                   (SELECT COUNT(*) FROM attendance att WHERE att.student_id = s.id AND att.status = 'absent') as absent_days
                                   FROM students s
                                   JOIN departments d ON s.department_id = d.id
                                   WHERE s.siwes_status = 'allocated'
                                   ORDER BY s.matric_no ASC");
        } elseif ($reportType === 'performance') {
            $data = $db->fetchAll("SELECT e.*, s.matric_no, s.first_name || ' ' || s.last_name as student_name, d.code as dept_code, c.name as company_name 
                                   FROM evaluations e
                                   JOIN students s ON e.student_id = s.id
                                   JOIN departments d ON s.department_id = d.id
                                   JOIN companies c ON e.company_id = c.id
                                   ORDER BY s.matric_no ASC");
        }

        $departments = $db->fetchAll("SELECT * FROM departments");

        $response = new Response();
        return $response->render('reports/index', [
            'reportType' => $reportType,
            'data' => $data,
            'departments' => $departments
        ]);
    }
}
