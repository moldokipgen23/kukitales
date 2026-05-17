#!/bin/bash
# KukiTales container entrypoint for Bunny Magic Containers.
# Bunny injects env vars at RUNTIME, so we config-cache here (not in Dockerfile).

set -e

cd /var/www/html

# Ensure runtime-writable dirs exist and are owned by web user.
# Container restarts can lose runtime-created dirs; guarantee them here.
mkdir -p \
    storage/app/public \
    storage/app/livewire-tmp \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/testing \
    storage/logs \
    bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache public
chmod -R ug+rwX storage bootstrap/cache

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
# All mariadb calls are wrapped to never abort the script (set -e safe).
if [ -z "$APP_KEY" ]; then
    echo "==> APP_KEY not set — bootstrapping from shared DB store"
    MYSQL="mariadb -h $DB_HOST -P ${DB_PORT:-3306} -u $DB_USERNAME -p$DB_PASSWORD $DB_DATABASE"

    set +e
    $MYSQL -e "CREATE TABLE IF NOT EXISTS app_meta (k VARCHAR(64) PRIMARY KEY, v TEXT NOT NULL)" 2>&1 | sed 's/^/    [db] /'
    STORED_KEY=$($MYSQL -N -B -e "SELECT v FROM app_meta WHERE k='app_key' LIMIT 1" 2>/dev/null)
    if [ -z "$STORED_KEY" ]; then
        STORED_KEY="base64:$(openssl rand -base64 32)"
        ESC_KEY=$(printf '%s' "$STORED_KEY" | sed "s/'/''/g")
        $MYSQL -e "INSERT INTO app_meta (k,v) VALUES ('app_key','$ESC_KEY') ON DUPLICATE KEY UPDATE v=v" 2>&1 | sed 's/^/    [db] /'
        STORED_KEY=$($MYSQL -N -B -e "SELECT v FROM app_meta WHERE k='app_key' LIMIT 1" 2>/dev/null)
        echo "    Generated and stored new APP_KEY in DB"
    else
        echo "    Loaded existing APP_KEY from DB"
    fi
    set -e

    if [ -z "$STORED_KEY" ]; then
        echo "    !! DB bootstrap failed, falling back to ephemeral key (sessions won't survive restarts)"
        STORED_KEY="base64:$(openssl rand -base64 32)"
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

# Storage symlink (force recreate — handles container rebuilds cleanly)
rm -f public/storage
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
