<?php
session_start();
require_once 'config/database.php';

$message = '';
$user = null;

// Check if already logged in
if (isset($_SESSION['user_id'])) {
    try {
        $db = Database::getInstance();
        $user = $db->fetch("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
    } catch (Exception $e) {
        // Ignore error, will show login form
    }
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$user) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $message = '<div class="alert alert-danger">Please fill in all fields</div>';
    } else {
        try {
            $db = Database::getInstance();
            $loginUser = $db->fetch("SELECT * FROM users WHERE email = ?", [$email]);
            
            if ($loginUser && password_verify($password, $loginUser['password'])) {
                $_SESSION['user_id'] = $loginUser['id'];
                $_SESSION['user_role'] = $loginUser['role'];
                $_SESSION['user_email'] = $loginUser['email'];
                $user = $loginUser;
                $message = '<div class="alert alert-success">Login successful! Welcome ' . htmlspecialchars($loginUser['role']) . '</div>';
            } else {
                $message = '<div class="alert alert-danger">Invalid email or password</div>';
            }
        } catch (Exception $e) {
            $message = '<div class="alert alert-danger">Database error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: working-login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Working Login - Attendance Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .login-card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-radius: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-8 col-lg-6">
                <div class="login-card p-4 shadow">
                    
                    <?php if ($user): ?>
                        <!-- User is logged in - Show dashboard -->
                        <div class="text-center mb-4">
                            <h2 class="text-primary">✅ Login Successful!</h2>
                            <p class="text-muted">Welcome to Attendance Portal</p>
                        </div>
                        
                        <?= $message ?>
                        
                        <div class="card">
                            <div class="card-header">
                                <h5>👤 User Information</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                                <p><strong>Role:</strong> 
                                    <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'teacher' ? 'success' : 'primary') ?>">
                                        <?= ucfirst($user['role']) ?>
                                    </span>
                                </p>
                                <p><strong>User ID:</strong> <?= $user['id'] ?></p>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <h6>🚀 Next Steps:</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="index.php?route=dashboard" class="btn btn-primary w-100 mb-2">
                                        📊 Go to Dashboard
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="working-login.php?logout=1" class="btn btn-outline-secondary w-100 mb-2">
                                        🚪 Logout
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                    <?php else: ?>
                        <!-- Show login form -->
                        <div class="text-center mb-4">
                            <h2 class="text-primary">🎓 Attendance Portal</h2>
                            <p class="text-muted">Sign in to your account</p>
                        </div>
                        
                        <?= $message ?>
                        
                        <form method="POST" action="working-login.php">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">Sign In</button>
                            </div>
                        </form>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <h6 class="text-muted">Admin:</h6>
                                <small>admin@school.com<br>admin123</small>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-muted">Teacher:</h6>
                                <small>teacher@school.com<br>teacher123</small>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-muted">Student:</h6>
                                <small>student@school.com<br>student123</small>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <hr class="my-4">
                    
                    <div class="text-center">
                        <h6>🛠️ Debug Tools:</h6>
                        <div class="btn-group" role="group">
                            <a href="debug.php" class="btn btn-outline-info btn-sm">System Debug</a>
                            <a href="simple-login.php" class="btn btn-outline-success btn-sm">Simple Login</a>
                            <a href="index.php" class="btn btn-outline-primary btn-sm">Main App</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>