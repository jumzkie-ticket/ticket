<?php

// Simple PHP test - no Laravel dependencies
header('Content-Type: application/json');

$checks = [
    'php_version' => PHP_VERSION,
    'current_directory' => getcwd(),
    'api_directory' => __DIR__,
    'parent_directory' => dirname(__DIR__),
    'vendor_path' => __DIR__.'/../vendor/autoload.php',
    'vendor_exists' => file_exists(__DIR__.'/../vendor/autoload.php'),
    'bootstrap_exists' => file_exists(__DIR__.'/../bootstrap/app.php'),
    'storage_exists' => file_exists(__DIR__.'/../storage'),
    'env_file_exists' => file_exists(__DIR__.'/../.env'),
    'files_in_parent' => is_dir(dirname(__DIR__)) ? scandir(dirname(__DIR__)) : 'cannot read',
];

echo json_encode($checks, JSON_PRETTY_PRINT);
