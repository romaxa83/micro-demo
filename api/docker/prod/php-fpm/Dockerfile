FROM php:7.4-fpm-alpine

RUN mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini

# копируем настройки
COPY ./docker/common/php/conf.d /usr/local/etc/php/conf.d

WORKDIR /app

# копируем всю папку(api) в образ
COPY ./ ./