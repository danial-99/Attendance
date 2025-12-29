<?php
// Fix demo user passwords
require_once 'config/database.php';

try {
    $db = Database::getInstance();
    
    echo "<h3>Fixing Demo User Passwords</h3>";
    
    // Update passwords for demo users
    $updates = [
        'admin@school.com' => 'admin123',
        'teacher@school.com' => 'teacher123', 
        'student@school.com' => 'student123'
    ];
    
    foreach ($updates as $email => $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $result = $db->query(
            "UPDATE users SET password = ? WHERE email = ?",
            [$hashedPassword, $email]
        );
        
        if ($result) {
            echo "✅ Updated password for $email (password: $password)<br>";
            
            // Verify the password works
            $user = $db->fetch("SELECT password FROM users WHERE email = ?", [$email]);
            $verified = password_verify($password, $user['password']);
            echo "   Password verification: " . ($verified ? "✅ Success" : "❌ Failed") . "<br><br>";
        } else {
            echo "❌ Failed to update password for $email<br>";
        }
    }
    
    echo "<div class='alert alert-success mt-3'>Password update complete!</div>";
    echo "<p><a href='index.php?route=login' class='btn btn-primary'>Try Login Now</a></p>";
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fix Passwords</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-body">
                <!-- Content displayed above -->
            </div>
        </div>
    </div>
</body>
</html>