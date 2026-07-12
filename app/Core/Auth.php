<?php

namespace App\Core;

use App\Core\Session;
use App\Core\Database;

class Auth {
    public static function login($username, $password) {
        $db = Database::getInstance();
        $user = $db->fetchRow("SELECT * FROM users WHERE username = :u OR email = :e LIMIT 1", [
            ':u' => $username,
            ':e' => $username
        ]);

        if ($user && $user['status'] === 'active' && password_verify($password, $user['password_hash'])) {
            Session::start();
            Session::set('user_id', $user['id']);
            Session::set('username', $user['username']);
            Session::set('role', $user['role']);
            Session::set('email', $user['email']);
            
            // Record activity log
            $db->query("INSERT INTO audit_logs (user_id, action, ip_address, user_agent) VALUES (:uid, :act, :ip, :ua)", [
                ':uid' => $user['id'],
                ':act' => 'User logged in',
                ':ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                ':ua' => $_SERVER['HTTP_USER_AGENT'] ?? 'CLI'
            ]);

            return $user;
        }
        return false;
    }

    public static function logout() {
        Session::start();
        $userId = Session::get('user_id');
        if ($userId) {
            $db = Database::getInstance();
            $db->query("INSERT INTO audit_logs (user_id, action, ip_address, user_agent) VALUES (:uid, :act, :ip, :ua)", [
                ':uid' => $userId,
                ':act' => 'User logged out',
                ':ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                ':ua' => $_SERVER['HTTP_USER_AGENT'] ?? 'CLI'
            ]);
        }
        Session::destroy();
    }

    public static function check() {
        return Session::has('user_id');
    }

    public static function user() {
        if (!self::check()) return null;
        
        $db = Database::getInstance();
        $user = $db->fetchRow("SELECT id, username, email, role, status, profile_pic FROM users WHERE id = :id LIMIT 1", [
            ':id' => Session::get('user_id')
        ]);
        
        if ($user) {
            // Retrieve additional profile details depending on role
            if ($user['role'] === 'student') {
                $student = $db->fetchRow("SELECT * FROM students WHERE user_id = :uid LIMIT 1", [':uid' => $user['id']]);
                if ($student) {
                    $user['profile'] = $student;
                }
            } elseif ($user['role'] === 'supervisor') {
                $supervisor = $db->fetchRow("SELECT * FROM supervisors WHERE user_id = :uid LIMIT 1", [':uid' => $user['id']]);
                if ($supervisor) {
                    $user['profile'] = $supervisor;
                }
            }
        }
        return $user;
    }

    public static function getRole() {
        return Session::get('role');
    }

    public static function requireAuth() {
        if (!self::check()) {
            Session::setFlash('error', 'Please log in to access this page.');
            (new Response())->redirect('/auth/login');
        }
    }

    public static function requireRole($allowedRoles) {
        self::requireAuth();
        
        if (is_string($allowedRoles)) {
            $allowedRoles = [$allowedRoles];
        }

        $userRole = self::getRole();
        if (!in_array($userRole, $allowedRoles)) {
            Session::setFlash('error', 'Unauthorized access to that section.');
            
            // Redirect to appropriate dashboard based on their role
            switch ($userRole) {
                case 'admin':
                    (new Response())->redirect('/admin/dashboard');
                    break;
                case 'coordinator':
                    (new Response())->redirect('/coordinator/dashboard');
                    break;
                case 'officer':
                    (new Response())->redirect('/officer/dashboard');
                    break;
                case 'student':
                    (new Response())->redirect('/student/dashboard');
                    break;
                case 'supervisor':
                    (new Response())->redirect('/supervisor/dashboard');
                    break;
                default:
                    (new Response())->redirect('/');
            }
        }
    }
}
