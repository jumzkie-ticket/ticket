<?php

// Test Laravel loading with environment variables
header('Content-Type: application/json');

try {
    define('LARAVEL_START', microtime(true));
    
    // Load Composer autoloader FIRST
    require __DIR__.'/../vendor/autoload.php';
    
    // Manually load Dotenv and set environment variables
    $repository = Dotenv\Repository\RepositoryBuilder::createWithNoAdapters()
        ->addAdapter(Dotenv\Repository\Adapter\EnvConstAdapter::class)
        ->addAdapter(Dotenv\Repository\Adapter\PutenvAdapter::class)
        ->immutable()
        ->make();

    // Set environment variables from Vercel $_ENV
    $envVars = [
        'APP_NAME', 'APP_ENV', 'APP_KEY', 'APP_DEBUG', 'APP_URL', 'APP_LOCALE', 'APP_FALLBACK_LOCALE',
        'LOG_CHANNEL', 'LOG_LEVEL',
        'DB_CONNECTION', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD', 'DB_SSLMODE',
        'SESSION_DRIVER', 'SESSION_LIFETIME', 'SESSION_ENCRYPT',
        'CACHE_DRIVER', 'QUEUE_CONNECTION', 'FILESYSTEM_DISK',
        'MAIL_MAILER', 'MAIL_FROM_ADDRESS', 'MAIL_FROM_NAME'
    ];

    foreach ($envVars as $var) {
        if (isset($_ENV[$var])) {
            $repository->set($var, $_ENV[$var]);
        }
    }

    // Set defaults
    if (!$repository->has('APP_ENV')) $repository->set('APP_ENV', 'production');
    if (!$repository->has('LOG_CHANNEL')) $repository->set('LOG_CHANNEL', 'stderr');

    // Now bootstrap Laravel - it should use the environment we just set
    $app = require_once __DIR__.'/../bootstrap/app.php';
    
    echo json_encode([
        'success' => true,
        'message' => 'Laravel loaded successfully!',
        'laravel_version' => $app->version(),
        'environment' => $app->environment(),
        'config_app_name' => config('app.name'),
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
