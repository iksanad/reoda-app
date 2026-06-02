#!/bin/sh

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Clear any stale cache from previous build
echo "Clearing stale caches..."
php artisan optimize:clear

# Rebuild all caches for production performance
echo "Rebuilding production caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Create storage symlink if not exists
echo "Linking storage..."
php artisan storage:link --force 2>/dev/null || true

# Start Apache
echo "Starting Apache..."
exec apache2-foreground
