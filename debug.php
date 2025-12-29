<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 Debug Information</h2>";

// Test 1: Check if files exist
echo "<h3>1. File Structure Check</h3>";
$files = [
    'index.php',
    'app/core/Router.php',
    'app/core/Controller.php',
    'app/controllers/AuthController.php',
    'config/database.php'
];

foreach ($files as $file) {
    echo $file . ": " . (file_exists($file) ? "✅ EXISTS" : "❌ MISSING") . "<br>";
}

// Test 2: Check database connection
echo "<h3>2. Database Connection Test</h3>";
try {
    require_once 'config/database.php';
    $db = Database::getInstance();
    echo "✅ Database connection successful<br>";
    
    // Test query
    $result = $db->fetch("SELECT COUNT(*) as count FROM users");
    echo "✅ Users table accessible, found " . $result['count'] . " users<br>";
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Test 3: Check PHP version and extensions
echo "<h3>3. PHP Environment</h3>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "PDO Extension: " . (extension_loaded('pdo') ? "✅ Loaded" : "❌ Missing") . "<br>";
echo "PDO MySQL: " . (extension_loaded('pdo_mysql') ? "✅ Loaded" : "❌ Missing") . "<br>";

// Test 4: Check session functionality
echo "<h3>4. Session Test</h3>";
session_start();
$_SESSION['test'] = 'working';
echo "Session test: " . ($_SESSION['test'] === 'working' ? "✅ Working" : "❌ Failed") . "<br>";

echo "<hr>";
echo "<p><strong>Navigation: <a href='nav.php'>Back to Main</a></strong></p>";
?>