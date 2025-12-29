<?php
session_start();

// Include necessary files
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');

require_once CONFIG_PATH . '/database.php';
require_once APP_PATH . '/core/Controller.php';

// Create a test controller instance
class TestController extends Controller {
    public function testUrls() {
        echo "<h3>URL Generation Test</h3>";
        echo "Current URI: " . $this->getCurrentUri() . "<br>";
        echo "Base URL: " . $this->url() . "<br>";
        echo "Login URL: " . $this->url('login') . "<br>";
        echo "Dashboard URL: " . $this->url('dashboard') . "<br>";
        echo "Signup URL: " . $this->url('signup') . "<br>";
        
        echo "<h4>Server Variables:</h4>";
        echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "<br>";
        echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "<br>";
        echo "HTTP_HOST: " . $_SERVER['HTTP_HOST'] . "<br>";
        echo "dirname(SCRIPT_NAME): " . dirname($_SERVER['SCRIPT_NAME']) . "<br>";
    }
    
    // Make getCurrentUri public for testing
    public function getCurrentUri() {
        return parent::getCurrentUri();
    }
}

$test = new TestController();
$test->testUrls();
?>