<?php

namespace App\Core;

class Response {
    public function setStatusCode(int $code) {
        http_response_code($code);
    }

    public function redirect(string $url) {
        header("Location: " . $url);
        exit;
    }

    public function json($data, int $statusCode = 200) {
        $this->setStatusCode($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    public function render(string $view, array $data = [], string $layout = 'main') {
        // Extract data variables to the local symbol table
        extract($data);

        // Start buffering output for the view content
        ob_start();
        $viewFile = dirname(__DIR__) . "/Views/{$view}.php";
        if (file_exists($viewFile)) {
            include_once $viewFile;
        } else {
            echo "View file [{$view}.php] not found.";
        }
        $content = ob_get_clean();

        // Load the main layout shell
        $layoutFile = dirname(__DIR__) . "/Views/layouts/{$layout}.php";
        if (file_exists($layoutFile)) {
            include_once $layoutFile;
        } else {
            echo $content; // If layout does not exist, render raw content
        }
        exit;
    }
}
