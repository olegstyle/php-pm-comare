FROM php:7.3-cli-buster

RUN apt-get update && apt-get install -y \
        libmcrypt-dev \
        zlib1g-dev \
        libicu-dev \
    && docker-php-ext-install \
        intl \
        opcache \
        pcntl \
        iconv \
        mbstring \
        zip \
        bcmath \
    && docker-php-ext-enable \
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

