<?php

/**
 * Laravel application entry point for Vercel
 * This handles all PHP requests in the Vercel serverless environment
 */

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Vercel doesn't deploy .env files, so we need to tell Laravel to use environment variables
// Set a flag so Laravel knows to read from $_ENV instead of .env file
putenv('APP_ENV=production');

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