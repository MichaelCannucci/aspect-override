FROM php:8

RUN apt-get update && apt-get install libzip-dev -y

RUN docker-php-ext-install zip

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN curl https://getcomposer.org/download/latest-stable/composer.phar --output ./composer && \
    mv composer /usr/local/bin/ && \
    chmod 0777 /usr/local/bin/composer