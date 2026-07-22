<?php

/**
 * Laravel application entry point for Vercel
 * This handles all PHP requests in the Vercel serverless environment
 */

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader
require __DIR__.'/../vendor/autoload.php';

// Manually bootstrap Dotenv with Vercel's environment variables
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

// Set critical defaults
if (!$repository->has('APP_ENV')) $repository->set('APP_ENV', 'production');
if (!$repository->has('LOG_CHANNEL')) $repository->set('LOG_CHANNEL', 'stderr');

// Bootstrap Laravel and handle the request
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());