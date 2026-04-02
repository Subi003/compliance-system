FROM php:8.3-cli

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev zip libpq-dev libicu-dev \
    && docker-php-ext-install zip pdo pdo_pgsql intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs

# Generate key (safe)
RUN php artisan key:generate || true

# ❌ REMOVE config cache from build
# ❌ REMOVE .env copy
ENV DB_CONNECTION=pgsql

CMD php artisan serve --host=0.0.0.0 --port=10000