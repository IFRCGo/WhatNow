FROM composer:2 AS composer

FROM php:7.4-apache

RUN apt-get update && \
    apt-get install -y \
    libzip-dev \
    unzip \
    zip \
    libjpeg62-turbo-dev \
    libpng-dev \
    wkhtmltopdf \
    curl \
    default-mysql-client \
    build-essential \
    libssl-dev \
    zlib1g-dev \
    libjpeg-dev \
    libfreetype6-dev \
    nodejs \
    npm && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_mysql zip gd && \
    a2enmod rewrite


COPY --from=composer /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --prefer-dist --no-scripts --no-autoloader

COPY package.json package-lock.json ./

RUN npm install

COPY . .


RUN composer dump-autoload --optimize


RUN npm run production

COPY ./docker/vhost.conf /etc/apache2/sites-available/000-default.conf


RUN mkdir -p storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

RUN php artisan l5-swagger:generate
