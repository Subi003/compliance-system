FROM php:8.3-cli

WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev zip libpq-dev libicu-dev \
    && docker-php-ext-install zip pdo pdo_pgsql intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy project
COPY . .

# Create .env from example
RUN cp .env.example .env

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs

# Remove old config cache
RUN rm -f bootstrap/cache/config.php

# FINAL START COMMAND
CMD php artisan key:generate --force && php artisan optimize:clear && php artisan serve --host=0.0.0.0 --port=10000