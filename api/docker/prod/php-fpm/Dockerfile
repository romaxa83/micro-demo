FROM php:7.4-cli-alpine AS builder

RUN docker-php-ext-install opcache

RUN apk add --no-cache unzip

ENV COMPOSER_ALLOW_SUPERUSER 1

# устанавливаем пает hirak/prestissimo - чтоб ускорить работу composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet \
    && composer global require hirak/prestissimo --no-plugins --no-scripts \
    && rm -rf /root/.composer/cache

WORKDIR /app

# копируем файлы , чтоб установить вендор
COPY ./composer.json ./composer.lock ./

RUN composer install --no-dev --prefer-dist --no-progress --no-suggest --no-scripts --optimize-autoload \
    && rm -rf /root/.composer/cache

#### FPM

FROM php:7.4-fpm-alpine

RUN docker-php-ext-install opcache

RUN mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini

# копируем настройки
COPY ./docker/common/php/conf.d /usr/local/etc/php/conf.d
COPY ./docker/prod/php/conf.d /usr/local/etc/php/conf.d

WORKDIR /app

COPY --from=builder /app ./
COPY ./ ./
