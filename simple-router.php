<?php
/**
 * Simple Router for PHP Built-in Server
 */

// Get the requested URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// If it's a static file and exists, serve it
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false; // Let PHP serve the static file
}

// For root or any other path, load index.php
require_once __DIR__ . '/index.php';
?>