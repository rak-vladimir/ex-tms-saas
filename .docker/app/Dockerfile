# PHP
FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    git \
    unzip \
    libpq-dev \
    icu-dev \
    oniguruma-dev \
    libzip-dev \
    bash \
    supervisor

# PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    mbstring \
    intl \
    bcmath \
    pcntl \
    zip

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Supervisord
COPY .docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

WORKDIR /var/www/html

ENTRYPOINT ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
