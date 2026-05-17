#!/bin/bash
# KukiTales container entrypoint for Bunny Magic Containers.
# Bunny injects env vars at RUNTIME, so we config-cache here (not in Dockerfile).

set -e

cd /var/www/html

echo "════════════════════════════════════════════"
echo " KukiTales Bunny container booting"
echo "════════════════════════════════════════════"
echo " PHP: $(php -v | head -1)"
echo " DB_HOST: ${DB_HOST:-not set}"
echo " APP_URL: ${APP_URL:-not set}"

# Wait for MariaDB sidecar to be reachable (up to 60s)
if [ -n "$DB_HOST" ]; then
    echo "==> Waiting for database at $DB_HOST:${DB_PORT:-3306}..."
    for i in $(seq 1 30); do
        if mariadb -h "$DB_HOST" -P "${DB_PORT:-3306}" -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1" >/dev/null 2>&1; then
            echo "    Database reachable ✓"
            break
        fi
        echo "    waiting ($i/30)..."
        sleep 2
    done
fi

# Auto-generate APP_KEY if missing
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    GENERATED_KEY="base64:$(openssl rand -base64 32)"
    export APP_KEY="$GENERATED_KEY"
    echo "==> Generated APP_KEY (set as env var)"
fi

# Clear any stale caches
php artisan optimize:clear || true

# Migrations + base seeds (idempotent)
echo "==> Running migrations"
php artisan migrate --force

echo "==> Seeding base data (idempotent — skips if already done)"
php artisan db:seed --class="Database\Seeders\AdminUserSeeder"   --force || true
php artisan db:seed --class="Database\Seeders\CategorySeeder"     --force || true
php artisan db:seed --class="Database\Seeders\SiteSettingSeeder" --force || true

# Publish Filament assets (CSS/JS) so they're served by Nginx
echo "==> Publishing Filament assets"
php artisan filament:assets || true

# Storage symlink
php artisan storage:link || true

# Cache configs with RUNTIME env values
echo "==> Caching config / routes / views"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "════════════════════════════════════════════"
echo " Boot complete — starting Nginx + PHP-FPM"
echo "════════════════════════════════════════════"

# Hand off to supervisord (php-fpm + nginx)
exec "$@"
