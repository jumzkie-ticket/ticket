<?php

/**
 * Laravel application entry point for Vercel
 * This handles all PHP requests in the Vercel serverless environment
 */

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Vercel passes environment variables via $_ENV
// We need to copy them to putenv() so Laravel's Dotenv loader can find them
$envVars = [
    'APP_NAME', 'APP_ENV', 'APP_KEY', 'APP_DEBUG', 'APP_URL',
    'LOG_CHANNEL', 'LOG_LEVEL',
    'DB_CONNECTION', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD', 'DB_SSLMODE',
    'SESSION_DRIVER', 'SESSION_LIFETIME', 'SESSION_ENCRYPT',
    'CACHE_DRIVER', 'QUEUE_CONNECTION', 'FILESYSTEM_DISK'
];

foreach ($envVars as $var) {
    if (isset($_ENV[$var])) {
        putenv("$var={$_ENV[$var]}");
    }
}

// Set defaults if not provided
if (!getenv('APP_ENV')) putenv('APP_ENV=production');
if (!getenv('LOG_CHANNEL')) putenv('LOG_CHANNEL=stderr');

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