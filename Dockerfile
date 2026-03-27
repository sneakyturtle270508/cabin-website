FROM php:8.3-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev sqlite3 \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && a2enmod rewrite \
    && apt-get clean

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy everything
COPY . .

# Create ALL necessary directories BEFORE any composer commands
RUN mkdir -p \
    storage/framework/{cache,sessions,views} \
    storage/app/public \
    bootstrap/cache \
    database \
    public/img \
    && chmod -R 777 storage bootstrap/cache database public

# Remove .env and copy from .env.example
RUN rm -f .env && cp .env.example .env

# Install dependencies
RUN composer install --no-interaction --no-dev --optimize-autoloader --no-scripts

# Generate app key
RUN php artisan key:generate --force

# Create SQLite database and run migrations
RUN touch database/database.sqlite && chmod 666 database/database.sqlite \
    && php artisan migrate --force || echo "Migrations skipped"

# Create admin user if it doesn't exist
RUN php artisan make:user --email=admin@cabins.com --name="Admin User" --password=admin123 --super --force || echo "Admin user creation skipped"

# Configure Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

EXPOSE 80
CMD ["apache2-foreground"]
