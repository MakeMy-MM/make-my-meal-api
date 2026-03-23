FROM php:8.5-cli-alpine

RUN apk upgrade --no-cache && apk add --no-cache \
        postgresql-client \
        libpq-dev \
        libzip-dev \
        libpng-dev \
        icu-dev \
        linux-headers \
        git \
        unzip \
        curl \
        $PHPIZE_DEPS \
    && docker-php-ext-install \
        pdo_pgsql \
        pgsql \
        gd \
        zip \
        bcmath \
        intl \
        pcntl \
    && pecl install xdebug redis \
    && docker-php-ext-enable xdebug redis \
    && apk del $PHPIZE_DEPS linux-headers \
    && rm -rf /tmp/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY php.ini /usr/local/etc/php/conf.d/99-dev.ini

WORKDIR /var/www/html

EXPOSE 80

CMD ["php", "-d", "variables_order=EGPCS", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
