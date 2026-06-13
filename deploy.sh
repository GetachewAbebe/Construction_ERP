#!/bin/bash

# --- NATANEM ERP ROBUST DEPLOYMENT ENGINE ---
# This script ensures high availability and prevents deployment crashes.
# It uses maintenance mode and rigorous error checking.

# Exit immediately if a command exits with a non-zero status.
set -e

echo "🚀 Initiating Robust Deployment..."

LOCK_FILE="/tmp/natanem_deploy.lock"

if [ -f "$LOCK_FILE" ]; then
    echo "❌ Deployment is already in progress. Lock file exists at $LOCK_FILE"
    exit 1
fi

touch "$LOCK_FILE"

# Ensure lock file is removed on script exit (success or failure)
trap 'rm -f "$LOCK_FILE"; echo "🔓 Deployment lock released."' EXIT

# Set HOME and COMPOSER_HOME for environments where it's missing (e.g. shell_exec)
if [ -z "$HOME" ]; then
    export HOME=$(getent passwd $(whoami) | cut -d: -f6)
fi
export COMPOSER_HOME=$HOME/.composer

# 1. Enter Maintenance Mode
# This prevents users from hitting the site while it's in a transitional state.
# We ignore failures here in case the app is already "broken".
echo "🚧 Entering maintenance mode..."
php artisan down || true

# 2. Pull latest code from GitHub
echo "📥 Pulling latest code..."
git fetch origin
git reset --hard origin/main

# 3. Enforce production environment
echo "🔧 Enforcing production environment..."
sed -i 's/^APP_ENV=.*/APP_ENV=production/' .env
sed -i 's/^APP_DEBUG=.*/APP_DEBUG=false/' .env
sed -i 's|^APP_URL=.*|APP_URL=https://erp.natanemengineering.com|' .env

# 3. Clean stale caches
echo "🧹 Clearing old bootstrap/cache..."
rm -f bootstrap/cache/*.php

# 4. Rebuild dependencies
echo "📦 Updating dependencies..."

# Ensure we have a composer binary (always use /tmp to avoid polluting the repo)
if ! command -v composer &> /dev/null; then
    if [ ! -f "/tmp/composer.phar" ]; then
        echo "📥 Composer not found. Downloading to /tmp..."
        php -r "copy('https://getcomposer.org/installer', '/tmp/composer-setup.php');"
        php /tmp/composer-setup.php --install-dir=/tmp --filename=composer.phar --quiet
        php -r "unlink('/tmp/composer-setup.php');"
    fi
    COMPOSER="php /tmp/composer.phar"
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

# ==========================================
# 7. Final Optimization & Warmup
# ==========================================
echo "⚡ Updating production caches atomically..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
# 8. Exit Maintenance Mode
echo "🌐 Bringing system back online..."
php artisan up

echo "✅ Deployment Successful! Natanem ERP is stable and live."


# ==========================================
# 9. Sync public assets to web root
# ==========================================
echo "📂 Syncing public assets to web root..."

# 1. Kill any lingering dev-server indicator in the repository folder
rm -f public/hot

# 2. Sync the fresh, compiled public assets to your live web root
/bin/cp -rT /home/natanewn/repositories/Construction_ERP/public /home/natanewn/public_html/erp

# 3. Final safety sweep: make sure no dev indicator survived the copy into public_html
rm -f /home/natanewn/public_html/erp/hot