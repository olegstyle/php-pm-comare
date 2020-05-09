FROM php:7.4.5-cli-buster

RUN apt-get update && apt-get install -y \
        libmcrypt-dev zlib1g-dev libicu-dev libzip-dev libonig-dev libssl-dev \
        autoconf file build-essential apt-transport-https software-properties-common autotools-dev libtool m4 \
        vim automake git openssl wget procps htop \
    && docker-php-ext-install \
        intl \
        opcache \
        pcntl \
        iconv \
        mbstring \
        zip \
        bcmath \
        sockets \
    && docker-php-ext-enable \
        intl \
        opcache \
        pcntl \
        iconv \
        mbstring \
        zip \
        bcmath \
        sockets

RUN usermod -u 1000 www-data
RUN mkdir /app
RUN chown -R www-data:www-data /app
