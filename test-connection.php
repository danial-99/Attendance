<?php
// Database connection test with different passwords
echo "<h2>🔍 Database Connection Test</h2>";

$passwords = ['', 'root', 'root123', 'password', 'mysql'];

foreach ($passwords as $password) {
    echo "<h3>Testing with password: '" . ($password ?: 'NO PASSWORD') . "'</h3>";
    
    try {
        $pdo = new PDO(
            "mysql:host=localhost;dbname=attendance_portal;charset=utf8mb4",
            "root",
            $password
        );
        
        echo "✅ <strong>SUCCESS!</strong> Connected with password: '" . ($password ?: 'NO PASSWORD') . "'<br>";
        
        // Test if tables exist
        $tables = $pdo->query("SHOW TABLES")->fetchAll();
        echo "📊 Found " . count($tables) . " tables in database<br>";
        
        if (count($tables) > 0) {
            echo "📋 Tables: ";
            foreach ($tables as $table) {
                echo $table[0] . " ";
            }
            echo "<br>";
        } else {
            echo "⚠️ No tables found. Need to import database.sql<br>";
        }
        
        echo "<hr>";
        echo "<strong>🎯 Use this password in config/database.php:</strong><br>";
        echo "<code>private \$password = '" . $password . "';</code><br>";
        break;
        
    } catch (PDOException $e) {
        echo "❌ Failed: " . $e->getMessage() . "<br><br>";
    }
}

echo "<hr>";
echo "<h3>🔧 Manual Setup Commands:</h3>";
echo "<pre>";
echo "# 1. Start MySQL
sudo systemctl start mysql

# 2. Connect to MySQL (try without password first)
mysql -u root

# 3. If above fails, try with password
mysql -u root -p

# 4. Create database (run in MySQL)
CREATE DATABASE attendance_portal;
EXIT;

# 5. Import data
mysql -u root -p attendance_portal < database.sql

# 6. Test connection
mysql -u root -p attendance_portal -e 'SHOW TABLES;'
";
echo "</pre>";
?>