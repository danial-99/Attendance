<?php
/**
 * Simple router for PHP built-in server
 * This file handles routing when using php -S
 */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Handle static files
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false; // Let PHP serve the file
}

// Route to index.php for all other requests
$_SERVER['REQUEST_URI'] = $uri;
require_once 'index.php';
?>