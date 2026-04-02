FROM php:8.2-cli

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev zip libpq-dev \
    && docker-php-ext-install zip pdo pdo_pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

# Create dummy .env (IMPORTANT FIX)
RUN cp .env.example .env || true

# Install dependencies WITHOUT scripts (FIX)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Generate key
RUN php artisan key:generate || true

CMD php artisan serve --host=0.0.0.0 --port=10000