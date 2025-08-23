<?php
echo "Testing Inertia components...\n";

try {
    require_once __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    
    echo "1. Testing Inertia class loading...\n";
    $inertia = new Inertia\Inertia();
    echo "✓ Inertia loads\n";
    
    echo "2. Testing route registration...\n";
    $router = $app->make('router');
    $routes = $router->getRoutes();
    echo "Total routes: " . count($routes) . "\n";
    
    echo "3. Testing Welcome view file...\n";
    $welcomePath = __DIR__.'/../resources/js/Pages/Welcome.vue';
    if (file_exists($welcomePath)) {
        echo "✓ Welcome.vue exists\n";
    } else {
        echo "✗ Welcome.vue missing at: $welcomePath\n";
    }
    
    echo "4. Testing Vite manifest...\n";
    $manifestPath = __DIR__.'/build/manifest.json';
    if (file_exists($manifestPath)) {
        echo "✓ Vite manifest exists\n";
        $manifest = json_decode(file_get_contents($manifestPath), true);
        echo "Manifest entries: " . count($manifest) . "\n";
    } else {
        echo "✗ Vite manifest missing at: $manifestPath\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}