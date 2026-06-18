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

DEPLOYMENT_WENT_DOWN=0

cleanup() {
    status=$?

    if [ "$status" -ne 0 ] && [ "$DEPLOYMENT_WENT_DOWN" -eq 1 ]; then
        echo "⚠️ Deployment failed. Restoring application availability..."
        php artisan up || true
    fi

    rm -f "$LOCK_FILE"
    echo "🔓 Deployment lock released."

    exit "$status"
}

# Ensure lock file is removed and the app is not left in maintenance mode.
trap cleanup EXIT

# Set HOME and COMPOSER_HOME for environments where it's missing (e.g. shell_exec)
if [ -z "$HOME" ]; then
    export HOME=$(getent passwd $(whoami) | cut -d: -f6)
fi
export COMPOSER_HOME=$HOME/.composer

# ==========================================
# 1. Enter Maintenance Mode
# ==========================================
echo "🚧 Entering maintenance mode..."
php artisan down || true
DEPLOYMENT_WENT_DOWN=1

# ==========================================
# 2. Pull Latest Code from GitHub
# ==========================================
echo "📥 Pulling latest code..."
git fetch origin
git reset --hard origin/main

# ==========================================
# 3. Enforce Production Environment
# ==========================================
echo "🔧 Enforcing production environment..."
sed -i 's/^APP_ENV=.*/APP_ENV=production/' .env
sed -i 's/^APP_DEBUG=.*/APP_DEBUG=false/' .env
sed -i 's|^APP_URL=.*|APP_URL=https://erp.natanemengineering.com|' .env

# ==========================================
# 4. Clean Stale Caches
# ==========================================
echo "🧹 Clearing old bootstrap/cache..."
rm -f bootstrap/cache/*.php

# ==========================================
# 5. Rebuild Composer Dependencies
# ==========================================
echo "📦 Updating dependencies..."

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

# Subshell to handle fallback clean rebuild
(
  $COMPOSER install --no-dev --optimize-autoloader
) || (
  echo "⚠️ Composer install failed. Attempting a clean rebuild (purging vendor)..."
  rm -rf vendor
  $COMPOSER install --no-dev --optimize-autoloader --no-scripts
)

# ==========================================
# 6. Run Database Migrations
# ==========================================
echo "🗄️ Running database migrations..."
php artisan migrate --force

# ==========================================
# 7. Atomic Optimization Cache Update
# ==========================================
echo "⚡ Updating production caches atomically..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ==========================================
# 8. Sync Public Assets to Web Root
# ==========================================
echo "📂 Syncing public assets to web root..."

# Kill any lingering dev-server indicator in the repository folder
rm -f public/hot

# Sync the fresh, compiled public assets to your live web root
/bin/cp -rT /home/natanewn/repositories/Construction_ERP/public /home/natanewn/public_html/erp

# Final safety sweep: make sure no dev indicator survived the copy into public_html
rm -f /home/natanewn/public_html/erp/hot

# ==========================================
# 9. Exit Maintenance Mode & Go Live
# ==========================================
echo "🌐 Bringing system back online..."
php artisan up
DEPLOYMENT_WENT_DOWN=0

echo "✅ Deployment Successful! Natanem ERP is stable and live."
