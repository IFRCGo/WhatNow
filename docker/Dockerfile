FROM composer:2 as composer

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
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev &&\
    apt-get clean

RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    zip \
    gd && \
    a2enmod rewrite

COPY vhost.conf /etc/apache2/sites-available/000-default.conf
COPY --from=composer /usr/bin/composer /usr/local/bin/composer
