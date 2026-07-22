<?php

// Test Laravel loading with environment variables
header('Content-Type: application/json');

try {
    define('LARAVEL_START', microtime(true));
    
    // Load Composer autoloader FIRST
    require __DIR__.'/../vendor/autoload.php';
    
    // Manually load the .env file using Dotenv
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/..');
    $dotenv->load();
    
    // Now bootstrap Laravel - it should use the environment we just loaded
    $app = require_once __DIR__.'/../bootstrap/app.php';
    
    // Don't call $app->environment() as it fails - just test config
    echo json_encode([
        'success' => true,
        'message' => 'Laravel loaded successfully!',
        'laravel_version' => $app->version(),
        'env_from_dotenv' => env('APP_ENV', 'not found'),
        'env_from_getenv' => getenv('APP_ENV'),
        'config_app_name' => config('app.name'),
        'config_app_env' => config('app.env'),
        'config_app_key_set' => !empty(config('app.key')),
        'config_db_connection' => config('database.default'),
        'config_db_host' => config('database.connections.pgsql.host'),
    ], JSON_PRETTY_PRINT);
    
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => explode("\n", $e->getTraceAsString())
    ], JSON_PRETTY_PRINT);
}
