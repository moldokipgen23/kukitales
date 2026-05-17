# ───────────────────────────────────────────────────────────────────
# KukiTales — Bunny Magic Containers image
# PHP 8.4-FPM + Nginx + Supervisor in one container
# Database: MariaDB (compatible with our MySQL migrations)
# ───────────────────────────────────────────────────────────────────

# ─── Stage 1: composer dependencies ─────────────────────────────────
FROM composer:2 AS vendor

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-interaction \
    --ignore-platform-reqs

# ─── Stage 2: runtime image ─────────────────────────────────────────
FROM php:8.4-fpm-alpine

# Install OS packages, PHP extensions, nginx, supervisor
RUN apk add --no-cache \
        nginx \
        supervisor \
        mariadb-client \
        oniguruma-dev \
        libxml2-dev \
        icu-dev \
        libpng-dev \
        libzip-dev \
        zlib-dev \
        freetype-dev \
        libjpeg-turbo-dev \
        bash \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        gd \
        intl \
        mbstring \
        opcache \
        pdo \
        pdo_mysql \
        xml \
        zip \
    && rm -rf /var/cache/apk/*

# Workdir
WORKDIR /var/www/html

# Copy composer install from stage 1
COPY --from=vendor /app/vendor /var/www/html/vendor

# Copy app source
COPY . /var/www/html/

# Finish composer autoload now that app files are in place
RUN cd /var/www/html \
    && composer dump-autoload --optimize --no-dev --classmap-authoritative

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Nginx, php-fpm, supervisor configs
COPY bunny/docker/nginx.conf       /etc/nginx/nginx.conf
COPY bunny/docker/php-fpm.conf     /usr/local/etc/php-fpm.d/zz-overrides.conf
COPY bunny/docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY bunny/docker/entrypoint.sh    /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
