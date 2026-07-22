#!/bin/bash

# Vercel install script - runs before build
# This installs Composer dependencies

set -e

echo "Installing Composer..."

# Download and install Composer
EXPECTED_CHECKSUM="$(wget -q -O - https://composer.github.io/installer.sig)"
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"

if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]; then
    echo 'ERROR: Invalid Composer installer checksum'
    rm composer-setup.php
    exit 1
fi

php composer-setup.php --quiet --install-dir=. --filename=composer
rm composer-setup.php

echo "Installing PHP dependencies..."
./composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

echo "Installing Node dependencies..."
npm install
