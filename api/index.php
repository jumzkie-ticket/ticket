<?php

/**
 * Laravel application entry point for Vercel
 * This handles all PHP requests in the Vercel serverless environment
 */

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Create .env file from environment variables if it doesn't exist
$envPath = __DIR__.'/../.env';
if (!file_exists($envPath)) {
    $envContent = "APP_NAME=" . ($_ENV['APP_NAME'] ?? 'Laravel') . "\n";
    $envContent .= "APP_ENV=" . ($_ENV['APP_ENV'] ?? 'production') . "\n";
    $envContent .= "APP_KEY=" . ($_ENV['APP_KEY'] ?? '') . "\n";
    $envContent .= "APP_DEBUG=" . ($_ENV['APP_DEBUG'] ?? 'false') . "\n";
    $envContent .= "APP_URL=" . ($_ENV['APP_URL'] ?? '') . "\n";
    $envContent .= "LOG_CHANNEL=" . ($_ENV['LOG_CHANNEL'] ?? 'stderr') . "\n";
    $envContent .= "LOG_LEVEL=" . ($_ENV['LOG_LEVEL'] ?? 'error') . "\n";
    $envContent .= "DB_CONNECTION=" . ($_ENV['DB_CONNECTION'] ?? 'sqlite') . "\n";
    $envContent .= "DB_HOST=" . ($_ENV['DB_HOST'] ?? '') . "\n";
    $envContent .= "DB_PORT=" . ($_ENV['DB_PORT'] ?? '5432') . "\n";
    $envContent .= "DB_DATABASE=" . ($_ENV['DB_DATABASE'] ?? '') . "\n";
    $envContent .= "DB_USERNAME=" . ($_ENV['DB_USERNAME'] ?? '') . "\n";
    $envContent .= "DB_PASSWORD=" . ($_ENV['DB_PASSWORD'] ?? '') . "\n";
    $envContent .= "DB_SSLMODE=" . ($_ENV['DB_SSLMODE'] ?? '') . "\n";
    $envContent .= "SESSION_DRIVER=" . ($_ENV['SESSION_DRIVER'] ?? 'cookie') . "\n";
    $envContent .= "SESSION_LIFETIME=" . ($_ENV['SESSION_LIFETIME'] ?? '120') . "\n";
    $envContent .= "CACHE_DRIVER=" . ($_ENV['CACHE_DRIVER'] ?? 'array') . "\n";
    $envContent .= "QUEUE_CONNECTION=" . ($_ENV['QUEUE_CONNECTION'] ?? 'sync') . "\n";
    $envContent .= "FILESYSTEM_DISK=" . ($_ENV['FILESYSTEM_DISK'] ?? 'public') . "\n";
    
    // Try to write to /tmp first (writable in serverless), then symlink
    $tmpEnv = '/tmp/.env_' . uniqid();
    file_put_contents($tmpEnv, $envContent);
    @symlink($tmpEnv, $envPath);
}

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