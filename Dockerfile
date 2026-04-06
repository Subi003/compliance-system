FROM php:8.3-cli

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev zip libpq-dev libicu-dev \
    && docker-php-ext-install zip pdo pdo_pgsql intl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

# ✅ THIS IS IMPORTANT
RUN touch .env

RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs

CMD php artisan config:clear && php artisan cache:clear && php artisan serve --host=0.0.0.0 --port=10000