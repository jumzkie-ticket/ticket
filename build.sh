#!/bin/bash

# Vercel build script for Laravel application with frontend and backend

set -e  # Exit on error

echo "========================================="
echo "Starting Vercel Build Process"
echo "========================================="

# Install Composer if not available (Vercel doesn't have it by default)
if ! command -v composer &> /dev/null; then
    echo "Installing Composer..."
    EXPECTED_CHECKSUM="$(php -r 'copy("https://composer.github.io/installer.sig", "php://stdout");')"
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"

    if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]; then
        >&2 echo 'ERROR: Invalid installer checksum'
        rm composer-setup.php
        exit 1
    fi

    php composer-setup.php --quiet
    rm composer-setup.php
    COMPOSER_BIN="php composer.phar"
else
    COMPOSER_BIN="composer"
fi

echo "Installing PHP dependencies..."
$COMPOSER_BIN install --no-dev --optimize-autoloader --no-interaction --prefer-dist

echo "Installing NPM dependencies..."
npm install

echo "Building frontend assets..."
npm run build

echo "Setting up Laravel for production..."

# Create storage directories if they don't exist
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Set permissions
chmod -R 775 storage bootstrap/cache

# Clear and cache config (optional, might cause issues in serverless)
# php artisan config:cache
# php artisan route:cache
# php artisan view:cache

echo "========================================="
echo "Build completed successfully!"
echo "========================================="
