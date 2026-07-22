#!/usr/bin/env node

/**
 * Vercel installation script for Laravel
 * Installs Composer and PHP dependencies before building
 */

const { execSync } = require('child_process');
const fs = require('fs');
const path = require('path');

function exec(command, ignoreError = false) {
  console.log(`> ${command}`);
  try {
    execSync(command, { stdio: 'inherit' });
  } catch (error) {
    if (!ignoreError) {
      console.error(`Error executing: ${command}`);
      process.exit(1);
    }
  }
}

console.log('========================================');
console.log('Vercel Build: Installing Dependencies');
console.log('========================================');

// Check if composer is available
let composerCmd = 'composer';
try {
  execSync('composer --version', { stdio: 'ignore' });
  console.log('Composer is available in PATH');
} catch {
  console.log('Composer not found, downloading...');
  
  try {
    // Download Composer installer
    exec('php -r "copy(\'https://getcomposer.org/installer\', \'composer-setup.php\');"', true);
    
    // Install Composer locally
    exec('php composer-setup.php --quiet --install-dir=. --filename=composer', true);
    
    // Clean up installer
    if (fs.existsSync('composer-setup.php')) {
      fs.unlinkSync('composer-setup.php');
    }
    
    composerCmd = './composer';
  } catch (error) {
    console.error('Failed to install Composer. Trying to continue without it...');
    // Continue anyway, vendor might be cached
  }
}

// Install PHP dependencies if vendor doesn't exist
const vendorExists = fs.existsSync(path.join(__dirname, 'vendor', 'autoload.php'));

if (!vendorExists) {
  console.log('Installing PHP dependencies...');
  try {
    exec(`${composerCmd} install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-progress`);
  } catch (error) {
    console.error('Warning: Composer install failed. This may cause deployment issues.');
  }
} else {
  console.log('PHP dependencies already installed (using cache)');
}

// Create storage directories
console.log('Setting up storage directories...');
const dirs = [
  'storage/framework/sessions',
  'storage/framework/views',
  'storage/framework/cache/data',
  'storage/logs',
  'bootstrap/cache'
];

dirs.forEach(dir => {
  const fullPath = path.join(__dirname, dir);
  if (!fs.existsSync(fullPath)) {
    fs.mkdirSync(fullPath, { recursive: true });
    console.log(`Created: ${dir}`);
  }
});

// Create .gitkeep files
dirs.forEach(dir => {
  const gitkeepPath = path.join(__dirname, dir, '.gitkeep');
  if (!fs.existsSync(gitkeepPath)) {
    fs.writeFileSync(gitkeepPath, '');
  }
});

// Clean up local composer if we downloaded it
if (composerCmd === './composer' && fs.existsSync('composer')) {
  fs.unlinkSync('composer');
}

console.log('========================================');
console.log('Installation completed successfully!');
console.log('========================================');
