FROM php:7.3-fpm-buster

RUN apt-get update && apt-get install -y \
        libmcrypt-dev \
        zlib1g-dev \
        libicu-dev \
        libzip-dev

RUN docker-php-ext-install \
        intl \
        opcache \
        pcntl \
        iconv \
        mbstring \
        zip \
        bcmath

RUN docker-php-ext-enable \
        intl \
        opcache \
        pcntl \
        iconv \
        mbstring \
        zip \
        bcmath

RUN usermod -u 1000 www-data
RUN mkdir /app
RUN chown -R www-data:www-data /app

