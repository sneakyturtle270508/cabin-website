#!/bin/bash

echo "=== ENTRYPOINT STARTED ===" >> /tmp/entrypoint.log
date >> /tmp/entrypoint.log

mkdir -p storage/framework/{cache/data,sessions,views}
mkdir -p storage/app/public
mkdir -p storage/logs
mkdir -p bootstrap/cache
mkdir -p database
mkdir -p public/img
mkdir -p /tmp/laravel
chmod -R 777 storage bootstrap/cache database public /tmp/laravel

echo "Directories created" >> /tmp/entrypoint.log

# Create admin user
echo "Creating admin user..." >> /tmp/entrypoint.log
php artisan make:user --email=admin@cabins.com --name="Admin User" --password=admin123 --super --force >> /tmp/entrypoint.log 2>&1
echo "Admin user creation done. Exit code: $?" >> /tmp/entrypoint.log

# Check if user exists
echo "Checking users..." >> /tmp/entrypoint.log
php artisan tinker --execute="echo 'Users: ' . \App\Models\User::count();" >> /tmp/entrypoint.log 2>&1

echo "=== ENTRYPOINT DONE ===" >> /tmp/entrypoint.log

exec apache2-foreground
