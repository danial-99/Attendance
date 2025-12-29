<?php
// Quick database connection test
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=attendance_portal;charset=utf8mb4",
        "root",  // Change username if different
        ""       // Add password if set
    );
    echo "✅ Database connection successful!";
    
    // Test if tables exist
    $tables = $pdo->query("SHOW TABLES")->fetchAll();
    echo "<br>📊 Found " . count($tables) . " tables in database";
    
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage();
    echo "<br><br>🔧 <strong>Solutions:</strong>";
    echo "<br>1. Check if MySQL is running";
    echo "<br>2. Verify database name: attendance_portal";
    echo "<br>3. Check username/password in config/database.php";
    echo "<br>4. Import database.sql file";
}
?>