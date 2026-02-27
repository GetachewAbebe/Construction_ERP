#!/bin/bash

# --- NATANEM ERP SAFE DEPLOYMENT SCRIPT ---
# This script ensures a clean deployment by bypassing common 
# bootstrap crashes and clearing stale caches before updating.

echo "🚀 Starting Safe Deployment..."

# 1. Force clear bootstrap caches (Bypasses Artisan crashes)
echo "🧹 Clearing bootstrap caches..."
rm -f bootstrap/cache/*.php

# 2. Pull latest code
echo "📥 Pulling latest code from GitHub..."
git pull origin main

# 3. Clean and rebuild dependencies
# We use --no-dev to keep the server light and prevent autoloader pollution.
echo "📦 Rebuilding dependencies..."
if [ -f "composer.phar" ]; then
    php composer.phar install --no-dev --optimize-autoloader --no-scripts
else
    composer install --no-dev --optimize-autoloader --no-scripts
fi

# 4. Run migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# 5. Final optimization
echo "⚡ Optimizing application..."
php artisan optimize:clear
php artisan optimize

echo "✅ Deployment Successful! Your website should be back online."
