FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    default-mysql-client \
    && apt-get clean && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo_mysql zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY composer.json composer.lock ./

RUN mkdir -p bootstrap/cache \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/testing \
    storage/framework/views \
    storage/logs \
    storage/app/public \
    storage/app/private

RUN cp -n .env.example .env 2>/dev/null || true

RUN COMPOSER_ALLOW_SUPERUSER=1 composer install \
    --no-interaction \
    --prefer-dist \
    --no-progress \
    --no-scripts \
    --optimize-autoloader

COPY . .

RUN COMPOSER_ALLOW_SUPERUSER=1 composer dump-autoload --optimize \
    && php artisan package:discover --ansi || true

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
