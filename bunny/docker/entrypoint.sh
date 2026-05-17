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

# APP_KEY: must be stable across all pods (load balancer routes between them).
# Strategy: shared key persisted in the DB via a tiny `app_meta` table.
# First pod to boot generates + stores it; later pods read it back.
if [ -z "$APP_KEY" ]; then
    echo "==> APP_KEY not set — bootstrapping from shared DB store"
    mariadb -h "$DB_HOST" -P "${DB_PORT:-3306}" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" <<'SQL' >/dev/null 2>&1
CREATE TABLE IF NOT EXISTS app_meta (
  k VARCHAR(64) PRIMARY KEY,
  v TEXT NOT NULL
);
SQL
    STORED_KEY=$(mariadb -h "$DB_HOST" -P "${DB_PORT:-3306}" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" -N -B -e "SELECT v FROM app_meta WHERE k='app_key' LIMIT 1" 2>/dev/null)
    if [ -z "$STORED_KEY" ]; then
        STORED_KEY="base64:$(openssl rand -base64 32)"
        ESC_KEY=$(printf '%s' "$STORED_KEY" | sed "s/'/''/g")
        mariadb -h "$DB_HOST" -P "${DB_PORT:-3306}" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" -e "INSERT INTO app_meta (k,v) VALUES ('app_key','$ESC_KEY') ON DUPLICATE KEY UPDATE v=v"
        STORED_KEY=$(mariadb -h "$DB_HOST" -P "${DB_PORT:-3306}" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" -N -B -e "SELECT v FROM app_meta WHERE k='app_key' LIMIT 1")
        echo "    Generated and stored new APP_KEY in DB"
    else
        echo "    Loaded existing APP_KEY from DB"
    fi
    export APP_KEY="$STORED_KEY"
fi

# Migrations (creates cache/sessions tables before any cache ops)
echo "==> Running migrations"
php artisan migrate --force

# Now safe to clear caches (tables exist)
php artisan config:clear || true
php artisan route:clear  || true
php artisan view:clear   || true
php artisan cache:clear  || true

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
