<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

/**
 * Silent first-run installer.
 *
 * On the FIRST request after upload, this provider:
 *   1. Generates APP_KEY if missing (writes to .env)
 *   2. Detects that database tables don't exist yet
 *   3. Runs migrations + base seeders silently in the background
 *   4. Creates an installed.lock file so it never runs again
 *
 * That makes cPanel-style deploys a 2-step process:
 *   1. Upload + extract the release zip
 *   2. Edit .env with the DB credentials you created in cPanel
 *   Visit your domain → site is live, admin@kukitales.com / password works.
 */
class AutoInstallProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Auto-generate APP_KEY if missing — runs in BOTH console and web,
        // because without a key Laravel can't bootstrap anything else.
        $this->ensureAppKey();

        // Never run the rest during console commands (artisan migrate etc.)
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

    /**
     * Generate APP_KEY automatically if missing or empty.
     * Writes it directly to the .env file so it persists.
     */
    protected function ensureAppKey(): void
    {
        $currentKey = config('app.key');
        if (! empty($currentKey)) {
            return;
        }

        $envPath = base_path('.env');
        if (! file_exists($envPath) || ! is_writable($envPath)) {
            return; // can't write — admin will have to do it manually
        }

        try {
            // Generate a real Laravel-compatible 256-bit key
            $newKey = 'base64:' . base64_encode(random_bytes(32));

            $contents = file_get_contents($envPath);
            if (preg_match('/^APP_KEY=.*/m', $contents)) {
                $contents = preg_replace('/^APP_KEY=.*/m', "APP_KEY={$newKey}", $contents);
            } else {
                $contents .= "\nAPP_KEY={$newKey}\n";
            }
            file_put_contents($envPath, $contents);

            // Update runtime config so the current request can use it
            config(['app.key' => $newKey]);

            // Bust any stale compiled config cache
            $cached = base_path('bootstrap/cache/config.php');
            if (file_exists($cached)) {
                @unlink($cached);
            }
        } catch (\Throwable $e) {
            \Log::warning('Auto APP_KEY generation failed: ' . $e->getMessage());
        }
    }
}
