#!/bin/bash
echo "🚀 Starting local development..."
docker-compose up -d
sleep 5
docker-compose exec app php artisan key:generate --force
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan make:user --email=admin@cabins.com --name="Admin User" --password=admin123 --super --force
echo ""
echo "✅ Site running at: http://localhost:8080"
echo "🔑 Admin: http://localhost:8080/cp"
echo "   Email: admin@cabins.com"
echo "   Password: admin123"
