# ───────────────────────────────────────────────────────────────────
# KukiTales — Bunny Magic Containers image
# PHP 8.4-FPM + Nginx + Supervisor in one container
# Database: MariaDB (compatible with our MySQL migrations)
# ───────────────────────────────────────────────────────────────────

# ─── Stage 1: full composer install with optimized autoloader ───────
FROM composer:2 AS vendor

WORKDIR /app
COPY . .
RUN composer install \
    --no-dev \
    --no-scripts \
    --optimize-autoloader \
    --classmap-authoritative \
    --prefer-dist \
    --no-interaction \
    --ignore-platform-reqs

# ─── Stage 2: runtime image ─────────────────────────────────────────
FROM php:8.4-fpm-alpine

# OS packages + PHP extensions
RUN apk add --no-cache \
        nginx \
        supervisor \
        mariadb-client \
        bash \
        git \
        unzip \
        oniguruma-dev \
        libxml2-dev \
        icu-dev \
        libpng-dev \
        libzip-dev \
        zlib-dev \
        freetype-dev \
        libjpeg-turbo-dev \
        autoconf \
        g++ \
        make \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        gd \
        intl \
        mbstring \
        opcache \
        pdo_mysql \
        zip \
    && apk del autoconf g++ make \
    && rm -rf /var/cache/apk/* /tmp/*

WORKDIR /var/www/html

# Copy entire app (vendor included) from stage 1
COPY --from=vendor /app /var/www/html

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Nginx, php-fpm, supervisor configs + entrypoint
COPY bunny/docker/nginx.conf       /etc/nginx/nginx.conf
COPY bunny/docker/php-fpm.conf     /usr/local/etc/php-fpm.d/zz-overrides.conf
COPY bunny/docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY bunny/docker/entrypoint.sh    /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
