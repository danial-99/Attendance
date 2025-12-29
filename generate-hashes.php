<?php
// Generate correct password hashes for demo users

$passwords = [
    'admin123' => 'admin@school.com',
    'teacher123' => 'teacher@school.com', 
    'student123' => 'student@school.com'
];

echo "<h3>Generated Password Hashes</h3>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Email</th><th>Password</th><th>Hash</th></tr>";

foreach ($passwords as $password => $email) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    echo "<tr>";
    echo "<td>$email</td>";
    echo "<td>$password</td>";
    echo "<td>$hash</td>";
    echo "</tr>";
}

echo "</table>";

// Also generate SQL update statements
echo "<h4>SQL Update Statements:</h4>";
echo "<pre>";
foreach ($passwords as $password => $email) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    echo "UPDATE users SET password = '$hash' WHERE email = '$email';\n";
}
echo "</pre>";
?>