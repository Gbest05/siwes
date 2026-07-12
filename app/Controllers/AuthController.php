<?php

namespace App\Controllers;

use App\Core\Response;
use App\Core\Request;
use App\Core\Session;
use App\Core\Auth;
use App\Core\Database;

class AuthController {
    public function loginView() {
        if (Auth::check()) {
            $this->redirectToDashboard(Auth::getRole());
        }
        
        $response = new Response();
        return $response->render('auth/login', [], 'main');
    }

    public function login() {
        $request = new Request();
        $response = new Response();

        if (Auth::check()) {
            $this->redirectToDashboard(Auth::getRole());
        }

        $username = $request->post('username');
        $password = $request->post('password');

        if (empty($username) || empty($password)) {
            Session::setFlash('error', 'Please fill in both fields.');
            return $response->redirect('/auth/login');
        }

        $user = Auth::login($username, $password);
        if ($user) {
            Session::setFlash('success', 'Logged in successfully! Welcome back.');
            $this->redirectToDashboard($user['role']);
        } else {
            Session::setFlash('error', 'Invalid username, email, or password.');
            return $response->redirect('/auth/login');
        }
    }

    public function registerView() {
        $db = Database::getInstance();
        $departments = $db->fetchAll("SELECT * FROM departments");
        $companies = $db->fetchAll("SELECT * FROM companies WHERE status = 'verified'");

        $response = new Response();
        return $response->render('auth/register', [
            'departments' => $departments,
            'companies' => $companies
        ], 'main');
    }

    public function register() {
        $request = new Request();
        $response = new Response();
        $db = Database::getInstance();

        $username = strtolower(trim($request->post('username')));
        $email = strtolower(trim($request->post('email')));
        $password = $request->post('password');
        $role = $request->post('role');

        if (empty($username) || empty($email) || empty($password) || empty($role)) {
            Session::setFlash('error', 'All fields are required.');
            return $response->redirect('/auth/register');
        }

        // Check if username or email already exists
        $exists = $db->fetchRow("SELECT id FROM users WHERE username = :u OR email = :e LIMIT 1", [
            ':u' => $username,
            ':e' => $email
        ]);

        if ($exists) {
            Session::setFlash('error', 'Username or email already exists.');
            return $response->redirect('/auth/register');
        }

        // Begin Transaction
        $db->getConnection()->exec("BEGIN TRANSACTION;");

        try {
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            
            // Insert User
            $stmt = $db->getConnection()->prepare("INSERT INTO users (username, email, password_hash, role, status) VALUES (:u, :e, :p, :r, 'active')");
            $stmt->bindValue(':u', $username);
            $stmt->bindValue(':e', $email);
            $stmt->bindValue(':p', $passwordHash);
            $stmt->bindValue(':r', $role);
            $stmt->execute();
            
            $userId = $db->lastInsertRowID();

            if ($role === 'student') {
                $matric = trim($request->post('matric_no'));
                $first = trim($request->post('first_name'));
                $last = trim($request->post('last_name'));
                $deptId = $request->post('department_id');
                $course = trim($request->post('course_of_study'));
                $year = $request->post('year_of_study');
                $phone = trim($request->post('phone'));

                if (empty($matric) || empty($first) || empty($last) || empty($deptId)) {
                    throw new \Exception("Required student details are missing.");
                }

                $stmt = $db->getConnection()->prepare("INSERT INTO students (user_id, matric_no, first_name, last_name, department_id, course_of_study, year_of_study, phone, siwes_status) VALUES (:uid, :mat, :first, :last, :dept, :course, :yr, :phone, 'unallocated')");
                $stmt->bindValue(':uid', $userId);
                $stmt->bindValue(':mat', $matric);
                $stmt->bindValue(':first', $first);
                $stmt->bindValue(':last', $last);
                $stmt->bindValue(':dept', $deptId);
                $stmt->bindValue(':course', $course);
                $stmt->bindValue(':yr', $year);
                $stmt->bindValue(':phone', $phone);
                $stmt->execute();

            } elseif ($role === 'supervisor') {
                $first = trim($request->post('first_name'));
                $last = trim($request->post('last_name'));
                $companyId = $request->post('company_id');
                $phone = trim($request->post('phone'));

                if (empty($first) || empty($last)) {
                    throw new \Exception("Required supervisor details are missing.");
                }

                $stmt = $db->getConnection()->prepare("INSERT INTO supervisors (user_id, company_id, first_name, last_name, phone, type) VALUES (:uid, :cid, :first, :last, :phone, 'company')");
                $stmt->bindValue(':uid', $userId);
                $stmt->bindValue(':cid', !empty($companyId) ? $companyId : null);
                $stmt->bindValue(':first', $first);
                $stmt->bindValue(':last', $last);
                $stmt->bindValue(':phone', $phone);
                $stmt->execute();
            }

            $db->getConnection()->exec("COMMIT;");
            
            Session::setFlash('success', 'Registration successful! You can now log in.');
            return $response->redirect('/auth/login');

        } catch (\Exception $e) {
            $db->getConnection()->exec("ROLLBACK;");
            Session::setFlash('error', 'Registration failed: ' . $e->getMessage());
            return $response->redirect('/auth/register');
        }
    }

    public function forgotView() {
        $response = new Response();
        return $response->render('auth/forgot', [], 'main');
    }

    public function forgot() {
        $request = new Request();
        $response = new Response();
        $email = $request->post('email');

        if (empty($email)) {
            Session::setFlash('error', 'Email field is required.');
            return $response->redirect('/auth/forgot');
        }

        $db = Database::getInstance();
        $user = $db->fetchRow("SELECT id FROM users WHERE email = :e LIMIT 1", [':e' => $email]);

        if ($user) {
            // In a production system, we would email a token. Here, we simulate by showing a success alert.
            Session::setFlash('success', 'Password reset instructions have been simulated! Please check the reset URL: /auth/reset?email=' . urlencode($email));
        } else {
            Session::setFlash('error', 'No user account found with that email.');
        }
        return $response->redirect('/auth/forgot');
    }

    public function resetView() {
        $request = new Request();
        $email = $request->get('email');
        
        $response = new Response();
        return $response->render('auth/reset', ['email' => $email], 'main');
    }

    public function reset() {
        $request = new Request();
        $response = new Response();
        $email = $request->post('email');
        $password = $request->post('password');

        if (empty($email) || empty($password)) {
            Session::setFlash('error', 'All fields are required.');
            return $response->redirect('/auth/reset?email=' . urlencode($email));
        }

        $db = Database::getInstance();
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        
        $stmt = $db->getConnection()->prepare("UPDATE users SET password_hash = :p WHERE email = :e");
        $stmt->bindValue(':p', $passwordHash);
        $stmt->bindValue(':e', $email);
        $stmt->execute();

        Session::setFlash('success', 'Your password has been reset successfully. You can now login.');
        return $response->redirect('/auth/login');
    }

    public function logout() {
        Auth::logout();
        Session::setFlash('success', 'Logged out successfully.');
        $response = new Response();
        return $response->redirect('/');
    }

    private function redirectToDashboard($role) {
        $response = new Response();
        switch ($role) {
            case 'admin':
                $response->redirect('/admin/dashboard');
                break;
            case 'coordinator':
                $response->redirect('/coordinator/dashboard');
                break;
            case 'officer':
                $response->redirect('/officer/dashboard');
                break;
            case 'student':
                $response->redirect('/student/dashboard');
                break;
            case 'supervisor':
                $response->redirect('/supervisor/dashboard');
                break;
            default:
                $response->redirect('/');
        }
    }
}
