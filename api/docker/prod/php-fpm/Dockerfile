FROM php:7.4-fpm-alpine

RUN mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini

COPY ./docker/prod/php-fpm/conf.d /usr/local/etc/php/conf.d

WORKDIR /app

COPY ./ ./