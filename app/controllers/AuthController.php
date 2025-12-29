<?php
/**
 * Authentication Controller
 */

class AuthController extends Controller {
    private $userModel;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->userModel = new User();
    }
    
    public function login() {
        // Redirect if already logged in
        if ($this->isLoggedIn()) {
            $this->redirect('dashboard');
        }
        
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                $error = 'Please fill in all fields';
            } else {
                $user = $this->userModel->authenticate($email, $password);
                
                if ($user) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['user_email'] = $user['email'];
                    
                    $this->redirect('dashboard');
                } else {
                    $error = 'Invalid email or password';
                }
            }
        }
        
        $this->view('auth/login', [
            'error' => $error,
            'csrf_token' => $this->generateCsrf(),
            'login_url' => '/index.php?route=login'
        ]);
    }
    
    public function logout() {
        session_destroy();
        $this->redirect('login');
    }
}