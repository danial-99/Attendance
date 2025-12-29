<?php
/**
 * Base Controller Class
 */

class Controller {
    protected $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->checkAuth();
    }
    
    protected function view($view, $data = []) {
        extract($data);
        
        // Make URL helper available in views
        $url = function($path = '') {
            return $this->url($path);
        };
        
        $viewFile = APP_PATH . '/views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View not found: " . $view);
        }
    }
    
    protected function redirect($url) {
        header("Location: " . $this->url($url));
        exit;
    }
    
    protected function url($path = '') {
        // For PHP built-in server, use query string routing
        if (php_sapi_name() === 'cli-server') {
            if (empty($path)) {
                return '/index.php';
            }
            return '/index.php?route=' . ltrim($path, '/');
        }
        
        // For Apache with mod_rewrite
        $scheme = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
        $baseUrl = $scheme . '://' . $_SERVER['HTTP_HOST'];
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        
        if ($scriptName !== '/') {
            $baseUrl .= $scriptName;
        }
        
        return $baseUrl . '/' . ltrim($path, '/');
    }
    
    protected function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    protected function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return $this->db->fetch(
            "SELECT * FROM users WHERE id = ?",
            [$_SESSION['user_id']]
        );
    }
    
    protected function hasRole($role) {
        $user = $this->getCurrentUser();
        return $user && $user['role'] === $role;
    }
    
    protected function checkAuth() {
        // Skip auth check for login page
        $currentUri = $this->getCurrentUri();
        if (in_array($currentUri, ['', 'login'])) {
            return;
        }
        
        if (!$this->isLoggedIn()) {
            $this->redirect('login');
        }
    }
    
    protected function requireRole($role) {
        if (!$this->hasRole($role)) {
            http_response_code(403);
            die("Access denied");
        }
    }
    
    private function getCurrentUri() {
        $uri = $_SERVER['REQUEST_URI'];
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = trim($uri, '/');
        $uri = str_replace('index.php/', '', $uri);
        $uri = str_replace('index.php', '', $uri);
        return $uri;
    }
    
    protected function validateCsrf() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
                die("CSRF token mismatch");
            }
        }
    }
    
    protected function generateCsrf() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}