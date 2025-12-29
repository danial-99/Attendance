<?php
/**
 * Simple Router Class
 */

class Router {
    private $routes = [];
    
    public function add($route, $handler) {
        $this->routes[$route] = $handler;
    }
    
    public function dispatch() {
        $uri = $this->getUri();
        
        // Handle PHP built-in server with query parameters
        if (php_sapi_name() === 'cli-server' && isset($_GET['route'])) {
            $uri = $_GET['route'];
        }
        
        if (array_key_exists($uri, $this->routes)) {
            $handler = $this->routes[$uri];
            $this->callHandler($handler);
        } else {
            $this->notFound();
        }
    }
    
    private function getUri() {
        // Handle PHP built-in server with query string routing
        if (php_sapi_name() === 'cli-server' && isset($_GET['route'])) {
            return trim($_GET['route'], '/');
        }
        
        // Handle normal URI routing
        $uri = $_SERVER['REQUEST_URI'];
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = trim($uri, '/');
        
        // Remove project folder from URI if present
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $basePath = dirname($scriptName);
        if ($basePath !== '/' && strpos($uri, trim($basePath, '/')) === 0) {
            $uri = substr($uri, strlen(trim($basePath, '/')));
            $uri = trim($uri, '/');
        }
        
        // Remove index.php if present
        $uri = str_replace('index.php/', '', $uri);
        $uri = str_replace('index.php', '', $uri);
        
        return $uri;
    }
    
    private function callHandler($handler) {
        list($controller, $method) = explode('@', $handler);
        
        if (class_exists($controller)) {
            $controllerInstance = new $controller();
            if (method_exists($controllerInstance, $method)) {
                $controllerInstance->$method();
            } else {
                $this->notFound();
            }
        } else {
            $this->notFound();
        }
    }
    
    private function notFound() {
        http_response_code(404);
        echo "404 - Page Not Found";
    }
}