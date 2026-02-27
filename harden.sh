#!/bin/bash

# Natanem ERP Production Hardening & Optimization Script
# This script prepares the application for peak performance and security.

echo "🚀 Starting Production Hardening..."

# 1. Clear existing caches to ensure a clean state
echo "🧹 Clearing existing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 2. Generate Production Caches
# These commands pre-compile resources into single optimized files.
echo "⚡ Generating production performance caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Optimize Composer Autoloader
echo "📦 Optimizing Composer autoloader..."
composer install --optimize-autoloader --no-dev --quiet

# 4. Frontend Asset Production Build
echo "🎨 Building production frontend assets..."
npm run build --quiet

echo "✅ System Hardening Complete. Natanem ERP is now in High-Performance mode."
