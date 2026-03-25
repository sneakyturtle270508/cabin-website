#!/bin/bash
echo "🔧 Setting up on Render..."
touch database/database.sqlite
chmod 666 database/database.sqlite
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "✅ Setup complete!"
echo "Run: php artisan make:user to create admin account"
