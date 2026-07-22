<?php

// Test Laravel loading with environment variables
header('Content-Type: application/json');

try {
    // Set environment variables (same as api/index.php)
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
            putenv("$var={$_ENV[$var]}");
            $_SERVER[$var] = $_ENV[$var];
        }
    }

    if (!getenv('APP_ENV')) {
        putenv('APP_ENV=production');
        $_SERVER['APP_ENV'] = 'production';
    }
    if (!getenv('LOG_CHANNEL')) {
        putenv('LOG_CHANNEL=stderr');
        $_SERVER['LOG_CHANNEL'] = 'stderr';
    }

    // Create .env file in /tmp
    $tmpEnvPath = '/tmp/.env';
    if (!file_exists($tmpEnvPath)) {
        $envContent = '';
        foreach ($envVars as $var) {
            $value = getenv($var);
            if ($value !== false) {
                $value = str_contains($value, ' ') ? "\"{$value}\"" : $value;
                $envContent .= "$var=$value\n";
            }
        }
        file_put_contents($tmpEnvPath, $envContent);
    }

    putenv("LARAVEL_ENV_PATH=/tmp");
    $_SERVER['LARAVEL_ENV_PATH'] = '/tmp';

    // Load Laravel
    define('LARAVEL_START', microtime(true));
    
    require __DIR__.'/../vendor/autoload.php';
    
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
