#!/bin/bash

mkdir -p storage/framework/{cache/data,sessions,views}
mkdir -p storage/app/public
mkdir -p storage/logs
mkdir -p bootstrap/cache
mkdir -p database
mkdir -p public/img
mkdir -p /tmp/laravel
chmod -R 777 storage bootstrap/cache database public /tmp/laravel

# Create admin user if it doesn't exist
php artisan make:user --email=admin@cabins.com --name="Admin User" --password=admin123 --super --force 2>/dev/null || true

exec apache2-foreground
