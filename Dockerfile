# Single stage build for Laravel + Vue application
FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    nodejs \
    npm \
    sqlite-dev

# Install PHP extensions
RUN docker-php-ext-install pdo_sqlite pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application code first
COPY . .
# Ensure .env is copied (if not in .gitignore)
COPY .env.example .env

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-interaction

# Install node dependencies
RUN npm ci

# Build frontend assets
RUN npm run build

# Create SQLite database file and run migrations
RUN touch /var/www/database/database.sqlite \
    && php artisan migrate --force \
    && php artisan config:clear \
    && php artisan route:clear \
    && php artisan view:clear

# Set proper permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache \
    && chmod 664 /var/www/database/database.sqlite \
    && chmod 775 /var/www/database \
    && mkdir -p /var/www/storage/framework/cache \
    && mkdir -p /var/www/storage/framework/sessions \
    && mkdir -p /var/www/storage/framework/views \
    && chmod -R 775 /var/www/storage/framework

# Copy nginx configuration
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Copy PHP-FPM configuration
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.conf

# Copy supervisor configuration
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create necessary directories
RUN mkdir -p /run/nginx \
    && mkdir -p /run/php \
    && mkdir -p /var/log/supervisor \
    && mkdir -p /var/run

# Expose port
EXPOSE 80

# Start supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]