<?php
session_start();

// Include necessary files
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');

require_once CONFIG_PATH . '/database.php';

try {
    $db = Database::getInstance();
    
    echo "<h3>Database Users Check</h3>";
    
    // Check if users table exists
    $tables = $db->fetchAll("SHOW TABLES LIKE 'users'");
    if (empty($tables)) {
        echo "<div class='alert alert-danger'>Users table does not exist!</div>";
        exit;
    }
    
    // Get all users
    $users = $db->fetchAll("SELECT id, email, role, created_at FROM users");
    
    if (empty($users)) {
        echo "<div class='alert alert-warning'>No users found in database!</div>";
        
        // Create demo users
        echo "<h4>Creating demo users...</h4>";
        
        $demoUsers = [
            ['email' => 'admin@school.com', 'password' => 'admin123', 'role' => 'admin'],
            ['email' => 'teacher@school.com', 'password' => 'teacher123', 'role' => 'teacher'],
            ['email' => 'student@school.com', 'password' => 'student123', 'role' => 'student']
        ];
        
        foreach ($demoUsers as $user) {
            $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);
            $db->query(
                "INSERT INTO users (email, password, role, created_at) VALUES (?, ?, ?, NOW())",
                [$user['email'], $hashedPassword, $user['role']]
            );
            echo "Created user: " . $user['email'] . " (password: " . $user['password'] . ")<br>";
        }
        
        echo "<div class='alert alert-success'>Demo users created successfully!</div>";
        
        // Refresh users list
        $users = $db->fetchAll("SELECT id, email, role, created_at FROM users");
    }
    
    echo "<h4>Current Users:</h4>";
    echo "<table class='table table-striped'>";
    echo "<tr><th>ID</th><th>Email</th><th>Role</th><th>Created</th><th>Password Test</th></tr>";
    
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>" . $user['id'] . "</td>";
        echo "<td>" . $user['email'] . "</td>";
        echo "<td>" . $user['role'] . "</td>";
        echo "<td>" . $user['created_at'] . "</td>";
        
        // Test password for demo accounts
        $testPassword = '';
        switch ($user['email']) {
            case 'admin@school.com':
                $testPassword = 'admin123';
                break;
            case 'teacher@school.com':
                $testPassword = 'teacher123';
                break;
            case 'student@school.com':
                $testPassword = 'student123';
                break;
        }
        
        if ($testPassword) {
            $fullUser = $db->fetch("SELECT password FROM users WHERE id = ?", [$user['id']]);
            $passwordValid = password_verify($testPassword, $fullUser['password']);
            echo "<td>" . ($passwordValid ? "✅ Valid" : "❌ Invalid") . "</td>";
        } else {
            echo "<td>-</td>";
        }
        
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-body">
                <!-- Content will be displayed above -->
                <hr>
                <p><a href="debug-login.php" class="btn btn-primary">Test Debug Login</a></p>
                <p><a href="index.php?route=login" class="btn btn-info">Back to Normal Login</a></p>
            </div>
        </div>
    </div>
</body>
</html>