<?php
session_start();

// Include necessary files
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');

require_once CONFIG_PATH . '/database.php';
require_once APP_PATH . '/models/User.php';

$error = '';
$debug = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $debug['email'] = $email;
    $debug['password_length'] = strlen($password);
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        try {
            $userModel = new User();
            $debug['user_model_created'] = true;
            
            // Check if user exists first
            $userExists = $userModel->findByEmail($email);
            $debug['user_exists'] = $userExists ? 'Yes' : 'No';
            
            if ($userExists) {
                $debug['stored_password_hash'] = substr($userExists['password'], 0, 20) . '...';
                $debug['password_verify'] = password_verify($password, $userExists['password']) ? 'Yes' : 'No';
            }
            
            $user = $userModel->authenticate($email, $password);
            $debug['authentication_result'] = $user ? 'Success' : 'Failed';
            
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_email'] = $user['email'];
                $debug['session_set'] = 'Yes';
                $debug['redirect_to'] = 'dashboard';
            } else {
                $error = 'Invalid email or password';
            }
        } catch (Exception $e) {
            $error = 'Database error: ' . $e->getMessage();
            $debug['exception'] = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Debug Login</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        
                        <?php if (!empty($debug)): ?>
                            <div class="alert alert-info">
                                <h6>Debug Information:</h6>
                                <pre><?= print_r($debug, true) ?></pre>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Debug Login</button>
                        </form>
                        
                        <hr>
                        <h6>Test Credentials:</h6>
                        <small>
                            <strong>Admin:</strong> admin@school.com / admin123<br>
                            <strong>Teacher:</strong> teacher@school.com / teacher123<br>
                            <strong>Student:</strong> student@school.com / student123
                        </small>
                        
                        <hr>
                        <p><a href="index.php?route=login" class="btn btn-info btn-sm">Back to Normal Login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>