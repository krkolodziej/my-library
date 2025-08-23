<?php
// Pure PHP debug script - bypasses Laravel
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>PHP Debug Information</h1>";
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>Current Time:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Script Name:</strong> " . $_SERVER['SCRIPT_NAME'] . "</p>";

echo "<h2>File System Check</h2>";
$laravel_root = dirname(__DIR__);
echo "<p><strong>Laravel Root:</strong> " . $laravel_root . "</p>";
echo "<p><strong>Vendor exists:</strong> " . (is_dir($laravel_root . '/vendor') ? 'YES' : 'NO') . "</p>";
echo "<p><strong>Bootstrap exists:</strong> " . (file_exists($laravel_root . '/bootstrap/app.php') ? 'YES' : 'NO') . "</p>";
echo "<p><strong>.env exists:</strong> " . (file_exists($laravel_root . '/.env') ? 'YES' : 'NO') . "</p>";
echo "<p><strong>Database exists:</strong> " . (file_exists($laravel_root . '/database/database.sqlite') ? 'YES' : 'NO') . "</p>";

echo "<h2>Laravel Bootstrap Test</h2>";
try {
    require_once $laravel_root . '/vendor/autoload.php';
    echo "<p>✅ Composer autoloader loaded</p>";
    
    $app = require_once $laravel_root . '/bootstrap/app.php';
    echo "<p>✅ Laravel app bootstrapped</p>";
    
    echo "<p><strong>App Environment:</strong> " . $app->environment() . "</p>";
    echo "<p><strong>App Debug:</strong> " . ($app->hasDebugModeEnabled() ? 'true' : 'false') . "</p>";
    
} catch (Exception $e) {
    echo "<p>❌ <strong>Bootstrap Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
} catch (Error $e) {
    echo "<p>❌ <strong>Fatal Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "<h2>Environment Variables</h2>";
$env_vars = ['APP_ENV', 'APP_DEBUG', 'APP_KEY', 'DB_CONNECTION'];
foreach ($env_vars as $var) {
    $value = $_ENV[$var] ?? getenv($var) ?? 'not set';
    if ($var === 'APP_KEY' && $value !== 'not set') {
        $value = substr($value, 0, 10) . '...';
    }
    echo "<p><strong>$var:</strong> $value</p>";
}

echo "<p>✅ Debug script completed successfully</p>";
?>