#!/bin/bash

# --- NATANEM ERP ROBUST DEPLOYMENT ENGINE ---
# This script ensures high availability and prevents deployment crashes.
# It uses maintenance mode and rigorous error checking.

# Exit immediately if a command exits with a non-zero status.
set -e

echo "🚀 Initiating Robust Deployment..."

# 1. Enter Maintenance Mode
# This prevents users from hitting the site while it's in a transitional state.
# We ignore failures here in case the app is already "broken".
echo "🚧 Entering maintenance mode..."
php artisan down || true

# 2. Pull latest code
echo "📥 Pulling latest code from GitHub..."
git pull origin main

# 3. Clean stale caches
echo "🧹 Clearing old bootstrap/cache..."
rm -f bootstrap/cache/*.php

# 4. Rebuild dependencies
echo "📦 Updating dependencies..."

# Ensure we have a composer binary
if ! command -v composer &> /dev/null && [ ! -f "composer.phar" ]; then
    echo "📥 Composer not found. Downloading composer.phar..."
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php composer-setup.php --quiet
    php -r "unlink('composer-setup.php');"
fi

# Determine composer command
if [ -f "composer.phar" ]; then
    COMPOSER="php composer.phar"
else
    COMPOSER="composer"
fi

# We use a subshell to catch failures and attempt a clean rebuild if it fails
(
  $COMPOSER install --no-dev --optimize-autoloader
) || (
  echo "⚠️ Composer install failed. Attempting a clean rebuild (purging vendor)..."
  rm -rf vendor
  $COMPOSER install --no-dev --optimize-autoloader --no-scripts
)

# 5. Run migrations (CRITICAL STEP)
# We run this BEFORE optimization so the app is schema-ready.
echo "🗄️ Running database migrations..."
php artisan migrate --force

# 6. Final Optimization & Warmup
echo "⚡ Generating production caches..."
php artisan optimize:clear
php artisan optimize

# 7. Exit Maintenance Mode
echo "🌐 Bringing system back online..."
php artisan up

echo "✅ Deployment Successful! Natanem ERP is stable and live."
