<?php
/**
 * Front Controller - Entry point for all requests
 */

session_start();

// Handle PHP built-in server routing
if (php_sapi_name() === 'cli-server') {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
    // Serve static files directly
    if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
        return false;
    }
    
    // Handle query string routing for PHP server
    if (isset($_GET['route'])) {
        $_SERVER['REQUEST_URI'] = '/' . $_GET['route'];
    }
}

// Define constants
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('PUBLIC_PATH', ROOT_PATH . '/public');

// Autoloader
spl_autoload_register(function ($class) {
    $paths = [
        APP_PATH . '/controllers/',
        APP_PATH . '/models/',
        APP_PATH . '/core/',
        CONFIG_PATH . '/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Include configuration
require_once CONFIG_PATH . '/database.php';
require_once APP_PATH . '/core/Router.php';

// Initialize router
$router = new Router();

// Define routes
$router->add('', 'AuthController@login');
$router->add('login', 'AuthController@login');
$router->add('signup', 'AuthController@signup');
$router->add('logout', 'AuthController@logout');
$router->add('dashboard', 'DashboardController@index');

// Admin routes
$router->add('admin/users', 'AdminController@users');
$router->add('admin/classes', 'AdminController@classes');
$router->add('admin/subjects', 'AdminController@subjects');
$router->add('admin/reports', 'AdminController@reports');

// Teacher routes
$router->add('teacher/attendance', 'TeacherController@attendance');
$router->add('teacher/mark-attendance', 'TeacherController@markAttendance');

// Student routes
$router->add('student/attendance', 'StudentController@attendance');

// API routes
$router->add('api/save-attendance', 'ApiController@saveAttendance');
$router->add('api/export-csv', 'ApiController@exportCsv');

// Handle the request
$router->dispatch();