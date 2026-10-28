FROM composer:2 AS composer

FROM php:8.4-apache-bookworm AS php-runtime
RUN apt-get update && apt-get install -y --no-install-recommends \
    libsqlite3-dev libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev libonig-dev \
    unzip curl default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql pdo_sqlite mbstring zip gd bcmath opcache \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*
COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf
COPY docker/verify-php-runtime.sh /usr/local/bin/verify-php-runtime
RUN chmod +x /usr/local/bin/verify-php-runtime && verify-php-runtime
WORKDIR /var/www/html

FROM node:22-bookworm-slim AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY webpack.mix.js ./
COPY resources ./resources
COPY public ./public
RUN npm run production

FROM php-runtime AS dependencies
COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
# A stale Laravel 5 lock must stop the build; resolve and commit it before release.
RUN composer validate --strict --no-check-publish \
    && composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction
COPY . .
RUN composer dump-autoload --no-dev --optimize \
    && composer check-platform-reqs --no-dev

FROM dependencies AS application
ARG SWAGGER_URL
ENV APP_ENV=production APP_DEBUG=false
COPY --from=frontend /app/public ./public
RUN : "${SWAGGER_URL:?SWAGGER_URL build argument is required}" \
    && mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache/data storage/logs bootstrap/cache \
    && SWAGGER_URL="$SWAGGER_URL" php artisan l5-swagger:generate \
    && chmod -R 775 storage bootstrap/cache public \
    && chown -R www-data:www-data storage bootstrap/cache public \
    && verify-php-runtime
