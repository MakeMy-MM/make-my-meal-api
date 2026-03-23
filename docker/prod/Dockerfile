FROM php:8.5-fpm-alpine

RUN apk upgrade --no-cache && apk add --no-cache \
        nginx \
        postgresql-client \
        libpq-dev \
        libzip-dev \
        libpng-dev \
        icu-dev \
        $PHPIZE_DEPS \
    && docker-php-ext-install \
        pdo_pgsql \
        pgsql \
        gd \
        zip \
        bcmath \
        intl \
        pcntl \
        opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS \
    && rm -rf /tmp/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY docker/prod/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/prod/php.ini /usr/local/etc/php/conf.d/99-prod.ini
COPY docker/prod/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80

CMD ["/usr/local/bin/entrypoint.sh"]
