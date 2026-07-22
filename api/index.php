<?php

// Laravel application entry point for Vercel
// This forwards all requests to Laravel's public/index.php

// Define the base path
define('LARAVEL_START', microtime(true));

// Check if running in Vercel environment
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
} else {
    // Vercel doesn't run composer install by default for PHP
    // We need to handle this differently
    http_response_code(500);
    echo json_encode([
        'error' => 'Application dependencies not installed. Please configure Vercel to install Composer dependencies.'
    ]);
    exit(1);
}

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Handle the request
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);