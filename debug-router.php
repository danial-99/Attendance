<?php
// Debug router information
echo "<h3>Debug Information:</h3>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "PHP_SELF: " . $_SERVER['PHP_SELF'] . "<br>";

if (isset($_SERVER['REDIRECT_URL'])) {
    echo "REDIRECT_URL: " . $_SERVER['REDIRECT_URL'] . "<br>";
}

// Test the router logic
$uri = $_SERVER['REQUEST_URI'];
$uri = parse_url($uri, PHP_URL_PATH);
$uri = trim($uri, '/');

$scriptName = $_SERVER['SCRIPT_NAME'];
$basePath = dirname($scriptName);
echo "Base Path: " . $basePath . "<br>";

if ($basePath !== '/' && strpos($uri, trim($basePath, '/')) === 0) {
    $uri = substr($uri, strlen(trim($basePath, '/')));
    $uri = trim($uri, '/');
}

echo "Processed URI: '" . $uri . "'<br>";
?>