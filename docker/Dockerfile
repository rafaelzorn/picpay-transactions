FROM php:8.0-fpm-alpine

RUN apk update \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && docker-php-ext-install -j$(nproc) pdo_mysql \
