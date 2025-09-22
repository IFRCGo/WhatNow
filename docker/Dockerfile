FROM composer:2 AS composer

FROM php:7.4-apache

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

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
    libfreetype6-dev &&\
    apt-get clean

RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    zip \
    gd && \
    a2enmod rewrite

COPY ./docker/vhost.conf /etc/apache2/sites-available/000-default.conf
COPY ./ /var/www/html

# Set working directory
WORKDIR /var/www/html

# Set correct permissions for Laravel
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public


# Install Composer dependencies
RUN composer install --prefer-dist --optimize-autoloader

# Generate Swagger documentation
RUN php artisan l5-swagger:generate

# Set permissions for Laravel storage and cache
RUN mkdir -p storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

RUN curl -fsSL https://deb.nodesource.com/setup_16.x | bash - && \
    apt-get install -y nodejs && \
    node -v && npm -v

RUN npm install
RUN npm run production
