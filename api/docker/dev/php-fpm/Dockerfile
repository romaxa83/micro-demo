FROM php:7.4-fpm-alpine

RUN apk add --no-cache bash coreutils

# устанавливаем xdebug
RUN apk add --no-cache autoconf g++ make \
    && pecl install xdebug \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable xdebug

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY ./common/php/conf.d /usr/local/etc/php/conf.d
COPY ./dev/php/conf.d /usr/local/etc/php/conf.d
COPY ./dev/php-fpm/conf.d /usr/local/etc/php/conf.d

WORKDIR /app

# копируем скрипт ,который запускаеться в оригинальном образе, и дополняем его своей логикой
# чтоб он автоматически отработал при создании образа.
# Логика, в скрипте, заключаеться - в добавлении в файл etc/hosts под доменом host.docker.internal ip-шлюза для докера
# данные домен в системах windows и ios заполнена,а в линуксе нет
# (нужна для подключение xDebug из сервиса к PHPStorm) , поэтому для линукса мы его тоже заполняем
# чтоб данный домен универсально использовался для всех систем
COPY ./dev/php-fpm/entrypoint.sh /usr/local/bin/docker-php-entrypoint
# делаем скрипт исполняемым
RUN chmod +x /usr/local/bin/docker-php-entrypoint