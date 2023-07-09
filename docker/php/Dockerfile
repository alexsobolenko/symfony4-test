FROM php:8.1-fpm

RUN apt-get update && apt-get install -y zlib1g-dev g++ libicu-dev zip libzip-dev curl wget mc \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

WORKDIR /var/www/project

