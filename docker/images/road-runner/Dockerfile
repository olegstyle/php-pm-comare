FROM golang:1.14 as build

ARG ARG_VERSION=1.7.1

RUN apt-get update && apt-get install -y git wget
RUN cd /tmp && \
    wget "https://github.com/spiral/roadrunner/archive/v${ARG_VERSION}.tar.gz" && \
    tar -xzf "v${ARG_VERSION}.tar.gz" && \
    mv "roadrunner-${ARG_VERSION}" /roadrunner && \
    cd /roadrunner && ls && make

FROM php:7.4.5-cli

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

COPY --from=build /roadrunner/rr /roadrunner/rr
COPY ./.rr.yaml /roadrunner

EXPOSE 9000

CMD ["/roadrunner/rr", "serve"]
