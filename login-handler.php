<?php
session_start();
require_once 'config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        try {
            $db = Database::getInstance();
            $user = $db->fetch("SELECT * FROM users WHERE email = ?", [$email]);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_email'] = $user['email'];
                
                // Redirect based on role
                switch ($user['role']) {
                    case 'admin':
                        header('Location: index.php?route=dashboard');
                        break;
                    case 'teacher':
                        header('Location: index.php?route=dashboard');
                        break;
                    case 'student':
                        header('Location: index.php?route=dashboard');
                        break;
                    default:
                        header('Location: index.php?route=login');
                }
                exit;
            } else {
                $error = 'Invalid email or password';
            }
        } catch (Exception $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

// If we get here, show login form with error
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Attendance Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh;">
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-primary">Attendance Portal</h2>
                        <p class="text-muted">Sign in to your account</p>
                    </div>
                    
                    <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($error) ?>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="login-handler.php">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">Sign In</button>
                        </div>
                    </form>
                    
                    <div class="mt-4">
                        <h6 class="text-muted">Demo Credentials:</h6>
                        <small class="text-muted">
                            <strong>Admin:</strong> admin@school.com / admin123<br>
                            <strong>Teacher:</strong> teacher@school.com / teacher123<br>
                            <strong>Student:</strong> student@school.com / student123
                        </small>
                    </div>
                    
                    <div class="mt-3 text-center">
                        <a href="test-routes.php" class="btn btn-outline-secondary btn-sm">Test Routes</a>
                        <a href="nav.php" class="btn btn-outline-info btn-sm">Navigation</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>