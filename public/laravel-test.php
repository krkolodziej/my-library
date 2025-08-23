<?php
echo "Testing Laravel bootstrap...\n";

try {
    echo "1. Checking autoloader...\n";
    require_once __DIR__.'/../vendor/autoload.php';
    echo "✓ Autoloader works\n";
    
    echo "2. Checking Laravel app bootstrap...\n";
    $app = require_once __DIR__.'/../bootstrap/app.php';
    echo "✓ App bootstrap works\n";
    
    echo "3. Checking .env loading...\n";
    echo "APP_ENV: " . ($_ENV['APP_ENV'] ?? 'not set') . "\n";
    echo "APP_KEY: " . (empty($_ENV['APP_KEY']) ? 'not set' : 'set') . "\n";
    
    echo "4. Testing kernel...\n";
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "✓ Kernel created\n";
    
    echo "All Laravel components working!\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}