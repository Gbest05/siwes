<?php

namespace App\Core;

class Request {
    public function getMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function isPost() {
        return $this->getMethod() === 'POST';
    }

    public function isGet() {
        return $this->getMethod() === 'GET';
    }

    public function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    public function get($key = null, $default = null) {
        if ($key === null) {
            return $this->sanitize($_GET);
        }
        return isset($_GET[$key]) ? $this->sanitize($_GET[$key]) : $default;
    }

    public function post($key = null, $default = null) {
        if ($key === null) {
            return $this->sanitize($_POST);
        }
        return isset($_POST[$key]) ? $this->sanitize($_POST[$key]) : $default;
    }

    public function file($key) {
        return isset($_FILES[$key]) && $_FILES[$key]['error'] !== UPLOAD_ERR_NO_FILE ? $_FILES[$key] : null;
    }

    public function all() {
        $data = array_merge($_GET, $_POST);
        return $this->sanitize($data);
    }

    public function sanitize($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->sanitize($value);
            }
            return $data;
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    public function getHeaders() {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) <> 'HTTP_') {
                continue;
            }
            $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            $headers[$header] = $value;
        }
        return $headers;
    }
}
