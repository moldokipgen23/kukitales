#!/bin/sh
# Production boot script for Railway.
# Verbose, fails loudly so we can SEE what's wrong instead of silent failures.

set -e

echo "════════════════════════════════════════════"
echo " KukiTales boot starting"
echo "════════════════════════════════════════════"
echo "PHP: $(php -v | head -1)"
echo "PORT: ${PORT:-not set}"
echo "APP_URL: ${APP_URL:-not set}"
echo "DB_HOST: ${DB_HOST:-not set}"
echo ""

echo "==> [1/8] Clearing stale caches"
php artisan optimize:clear || true

echo "==> [2/8] Running migrations"
php artisan migrate --force

echo "==> [3/8] Seeding (idempotent)"
php artisan db:seed --force || echo "    (seed step had errors — continuing anyway)"

echo "==> [4/8] Publishing Filament CSS/JS to public/"
php artisan filament:assets

echo "==> [5/8] Creating storage symlink"
php artisan storage:link || echo "    (storage:link skipped — already exists)"

echo "==> [6/8] Caching config"
php artisan config:cache

echo "==> [7/8] Caching routes + views"
php artisan route:cache
php artisan view:cache

echo "==> [8/8] Starting HTTP server on 0.0.0.0:${PORT}"
echo "════════════════════════════════════════════"
exec php artisan serve --host=0.0.0.0 --port="$PORT"
