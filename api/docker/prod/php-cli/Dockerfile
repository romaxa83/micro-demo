# Для оптимизации используем мультистадийную сборку
# весь сборочный софт используем в первом образе, а
# второй собираем максимально облегченым ,и нужные для работы файлы копируем из первого образа (билдера).
# То что меняеться часто прописываем внизу, а что реже вверху, чтобы сборка происходил быстрее
# т.к. файлы в которых нет изменений тянуться из кеша
FROM php:7.4-cli-alpine AS biulder

# устанавливаем пакет unzip что распаковать пакета с composer
RUN apk add --no-cache unzip

ENV COMPOSER_ALLOW_SUPERUSER 1

# устанавливаем пает hirak/prestissimo - чтоб ускорить работу composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet \
    && composer global require hirak/prestissimo --no-plugins --no-scripts \
    && rm -rf /root/.composer/cache

WORKDIR /app

# копируем файлы , чтоб установить вендор
COPY ./composer.json ./composer.lock ./

# устанавливаем вендор
RUN composer install --no-dev --prefer-dist --no-progress --no-suggest --no-scripts --optimize-autoload \
    && rm -rf /root/.composer/cache

####### CLI

FROM php:7.4-cli-alpine

RUN mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini

# копируем настройки
COPY ./docker/common/php/conf.d /usr/local/etc/php/conf.d
COPY ./docker/prod/php/conf.d /usr/local/etc/php/conf.d

WORKDIR /app

# копируем вендор из образа билдер (выше)
COPY --from=builder /app ./

# копируем всю папку(api) в образ
COPY ./ ./