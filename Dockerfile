FROM php:8.3-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev sqlite3 ca-certificates gnupg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && a2enmod rewrite \
    && apt-get clean

# Install Node.js 20
RUN mkdir -p /etc/apt/keyrings \
    && curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg \
    && echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_20.x nodistro main" > /etc/apt/sources.list.d/nodesource.list \
    && apt-get update \
    && apt-get install -y nodejs

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy everything
COPY . .

# Create all necessary directories
RUN mkdir -p \
    storage/framework/{cache/data,sessions,views} \
    storage/app/public \
    storage/logs \
    bootstrap/cache \
    database \
    public/img \
    && chmod -R 777 storage bootstrap/cache database public

# Create views compiled directory
RUN mkdir -p storage/framework/views && chmod 777 storage/framework/views

# Remove .env and copy from .env.example
RUN rm -f .env && cp .env.example .env

# Set APP_URL and APP_KEY for production
RUN sed -i 's|APP_URL=.*|APP_URL=https://cabin-website.onrender.com|' .env \
    && sed -i 's|APP_KEY=|APP_KEY=base64:a8X3BVNt3ccxeuN9WAqXxmiQQUk34Ix8OGWCeUXjFTc=|' .env

# Install dependencies (skip scripts)
RUN composer install --no-interaction --no-dev --optimize-autoloader --no-scripts

# Publish Statamic assets
RUN php artisan vendor:publish --force --all || true

# Install Node dependencies and build assets (devDependencies included for asset building)
RUN npm install --include=dev && npm run build

# Create SQLite database and run migrations
RUN touch database/database.sqlite && chmod 666 database/database.sqlite \
    && php artisan migrate --force || echo "Migrations skipped"

# Copy entrypoint script that will create admin user at runtime
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Configure Apache with HTTPS redirect
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && echo '<Directory /var/www/html/public>' >> /etc/apache2/sites-available/000-default.conf \
    && echo '    AllowOverride All' >> /etc/apache2/sites-available/000-default.conf \
    && echo '    Require all granted' >> /etc/apache2/sites-available/000-default.conf \
    && echo '</Directory>' >> /etc/apache2/sites-available/000-default.conf \
    && echo 'RewriteEngine On' >> /etc/apache2/sites-available/000-default.conf \
    && echo 'RewriteCond %{HTTPS} off' >> /etc/apache2/sites-available/000-default.conf \
    && echo 'RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]' >> /etc/apache2/sites-available/000-default.conf

EXPOSE 80
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT ["docker-entrypoint.sh"]
