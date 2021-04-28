FROM php:8

RUN apt-get update && apt-get install libzip-dev -y

RUN docker-php-ext-install zip

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug