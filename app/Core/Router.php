<?php

namespace App\Core;

class Router {
    private $routes = [];

    public function get($path, $handler) {
        $this->addRoute('GET', $path, $handler);
    }

    public function post($path, $handler) {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute($method, $path, $handler) {
        // Convert dynamic path parameters (e.g. {id}) to regex
        // e.g. /student/view/{id} -> ^/student/view/([^/]+)$
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[^/]+)', $path);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $pattern,
            'handler' => $handler
        ];
    }

    public function dispatch($requestUri, $requestMethod) {
        // Strip query string from URI
        $parts = explode('?', $requestUri);
        $uri = rtrim($parts[0], '/');
        if (empty($uri)) {
            $uri = '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                // Extract named parameters from regex match
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = $value;
                    }
                }

                $handler = $route['handler'];
                if (is_array($handler)) {
                    $controllerName = $handler[0];
                    $methodName = $handler[1];

                    if (class_exists($controllerName)) {
                        $controller = new $controllerName();
                        if (method_exists($controller, $methodName)) {
                            return call_user_func_array([$controller, $methodName], $params);
                        }
                    }
                } elseif (is_callable($handler)) {
                    return call_user_func_array($handler, $params);
                }

                // Handler execution failed
                $this->send44();
                return;
            }
        }

        // No route matched
        $this->send44();
    }

    private function send44() {
        http_response_code(404);
        echo "<h1>404 Not Found</h1>";
        echo "<p>The page you requested could not be found.</p>";
    }
}
