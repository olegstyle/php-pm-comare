FROM php:7.4.5-cli

ARG ARG_VERSION=4.5.0

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

RUN cd /tmp && \
    wget "https://github.com/swoole/swoole-src/archive/v${ARG_VERSION}.tar.gz" && \
    tar -xzf "v${ARG_VERSION}.tar.gz" && \
    cd "swoole-src-${ARG_VERSION}" && \
    phpize  && \
    ./configure --enable-openssl && \
    make && make install && \
    cd ../ && rm -rf "swoole-src-${ARG_VERSION}" && \
    docker-php-ext-enable swoole

RUN usermod -u 1000 www-data

EXPOSE 9000

CMD ["php", "/app/bin/swoole-worker.php"]
