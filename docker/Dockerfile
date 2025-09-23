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
    libfreetype6-dev && \
    apt-get clean


RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    zip \
    gd && \
    a2enmod rewrite

COPY ./docker/vhost.conf /etc/apache2/sites-available/000-default.conf
COPY ./ /var/www/html


WORKDIR /var/www/html


COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public \
 && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public

RUN composer install --prefer-dist --optimize-autoloader

RUN php artisan l5-swagger:generate


RUN mkdir -p storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
 && chmod -R 775 storage bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache


RUN curl -fsSL https://deb.nodesource.com/setup_16.x | bash - && \
    apt-get install -y nodejs && \
    node -v && npm -v

RUN npm install && npm run production
