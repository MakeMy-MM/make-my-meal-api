FROM php:8.4-alpine

RUN apk add --no-cache git unzip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN php -v && composer --version

WORKDIR /var/www

COPY . .
