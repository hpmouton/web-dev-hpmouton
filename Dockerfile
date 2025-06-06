FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    libzip-dev \
    postgresql-dev \
    mysql-client \
    npm \
    nodejs \
    git \
    && docker-php-ext-install pdo_mysql zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
ARG UID=1000
ARG GID=1000
RUN addgroup -g ${GID} laravel \
    && adduser -u ${UID} -G laravel -s /bin/sh -D laravel
USER laravel

EXPOSE 80
