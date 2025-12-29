<?php
// Check if mod_rewrite is enabled
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    if (in_array('mod_rewrite', $modules)) {
        echo "mod_rewrite is ENABLED";
    } else {
        echo "mod_rewrite is DISABLED - Enable it in Apache configuration";
    }
} else {
    echo "Cannot check mod_rewrite status (not running on Apache or function not available)";
}

// Also check if .htaccess is being processed
if (isset($_SERVER['REDIRECT_URL'])) {
    echo "<br>URL rewriting is working - REDIRECT_URL: " . $_SERVER['REDIRECT_URL'];
} else {
    echo "<br>URL rewriting might not be working";
}
?>