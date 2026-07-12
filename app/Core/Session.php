<?php

namespace App\Core;

class Session {
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null) {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has($key) {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove($key) {
        self::start();
        if (self::has($key)) {
            unset($_SESSION[$key]);
        }
    }

    public static function destroy() {
        self::start();
        session_destroy();
        $_SESSION = [];
    }

    // Flash Messages
    public static function setFlash($key, $message) {
        self::start();
        $_SESSION['flash'][$key] = $message;
    }

    public static function getFlash($key) {
        self::start();
        if (isset($_SESSION['flash'][$key])) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $message;
        }
        return null;
    }

    public static function hasFlash($key) {
        self::start();
        return isset($_SESSION['flash'][$key]);
    }

    // CSRF Protection
    public static function generateCsrfToken() {
        self::start();
        if (!self::has('csrf_token')) {
            $token = bin2hex(random_bytes(32));
            self::set('csrf_token', $token);
        }
        return self::get('csrf_token');
    }

    public static function verifyCsrfToken($token) {
        self::start();
        if (!self::has('csrf_token') || empty($token)) {
            return false;
        }
        return hash_equals(self::get('csrf_token'), $token);
    }
}
