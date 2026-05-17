<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

/**
 * Silent first-run installer.
 *
 * On the FIRST request after upload, this provider:
 *   1. Detects that database tables don't exist yet (or installed.lock missing)
 *   2. Runs migrations + base seeders silently in the background
 *   3. Creates an installed.lock file so it never runs again
 *
 * That makes cPanel-style deploys a 2-step process:
 *   1. Upload + extract the release zip
 *   2. Edit .env with the DB credentials you created in cPanel
 *   Visit your domain → site is live, admin@kukitales.com / password works.
 *
 * If anything goes wrong (DB credentials bad, etc.), the request just shows
 * a standard Laravel DB connection error — same as any other broken connection.
 */
class AutoInstallProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Never run during console commands (artisan migrate, key:generate, etc.)
        if (app()->runningInConsole()) return;

        $lockPath = storage_path('installed.lock');
        if (file_exists($lockPath)) return;

        // Don't trigger during the installer's own auto-actions (avoid recursion)
        if (defined('KUKITALES_INSTALLING')) return;

        try {
            // Only fire if the schema is fresh (users table missing).
            if (Schema::hasTable('users')) {
                // Already migrated by hand; just lock and move on.
                @file_put_contents($lockPath, now()->toIso8601String());
                return;
            }

            define('KUKITALES_INSTALLING', true);

            // 1. Migrate
            Artisan::call('migrate', ['--force' => true]);

            // 2. Seed base data only (admin user, categories, settings) — no demo posts.
            // SiteSettingSeeder is idempotent; AdminUserSeeder creates the default
            // admin@kukitales.com / password (user should change immediately).
            Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\AdminUserSeeder',
                '--force' => true,
            ]);
            Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\CategorySeeder',
                '--force' => true,
            ]);
            Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\SiteSettingSeeder',
                '--force' => true,
            ]);

            // 3. Storage symlink (best-effort — fails silently on shared hosts)
            try { Artisan::call('storage:link'); } catch (\Throwable) {}

            // 4. Mark installed so this never runs again.
            @file_put_contents($lockPath, now()->toIso8601String());
        } catch (\Throwable $e) {
            // Silent failure — Laravel's normal error handler will surface DB
            // connection errors so the admin can fix the .env and refresh.
            \Log::warning('AutoInstall skipped: ' . $e->getMessage());
        }
    }
}
