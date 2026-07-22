<?php

/**
 * Laravel application entry point for Vercel
 * This handles all PHP requests in the Vercel serverless environment
 */

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Vercel passes environment variables via $_ENV
// We need to make them available before Laravel boots
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
        $_SERVER[$var] = $_ENV[$var]; // Also set in $_SERVER for Laravel
    }
}

// Set critical defaults if not provided
if (!getenv('APP_ENV')) {
    putenv('APP_ENV=production');
    $_SERVER['APP_ENV'] = 'production';
}
if (!getenv('LOG_CHANNEL')) {
    putenv('LOG_CHANNEL=stderr');
    $_SERVER['LOG_CHANNEL'] = 'stderr';
}

// Create a minimal .env file in /tmp (writable in serverless)
// This ensures Laravel's Dotenv loader doesn't fail
$tmpEnvPath = '/tmp/.env';
if (!file_exists($tmpEnvPath)) {
    $envContent = '';
    foreach ($envVars as $var) {
        $value = getenv($var);
        if ($value !== false) {
            // Escape values with spaces or special characters
            $value = str_contains($value, ' ') ? "\"{$value}\"" : $value;
            $envContent .= "$var=$value\n";
        }
    }
    file_put_contents($tmpEnvPath, $envContent);
}

// Point Laravel to the /tmp/.env file
putenv("LARAVEL_ENV_PATH=/tmp");
$_SERVER['LARAVEL_ENV_PATH'] = '/tmp';

// Determine if the application is in maintenance mode
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());