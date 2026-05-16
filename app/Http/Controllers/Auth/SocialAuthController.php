<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /** Providers we support. Keep in sync with config/services.php and the routes. */
    protected array $providers = ['google', 'facebook'];

    public function redirect(string $provider)
    {
        abort_unless(in_array($provider, $this->providers), 404);
        if (! $this->isConfigured($provider)) {
            return redirect()->route('login')->with('error', ucfirst($provider) . ' sign-in is not configured yet.');
        }
        $this->applyRuntimeConfig($provider);
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider)
    {
        abort_unless(in_array($provider, $this->providers), 404);
        if (! $this->isConfigured($provider)) {
            return redirect()->route('login')->with('error', ucfirst($provider) . ' sign-in is not configured yet.');
        }
        $this->applyRuntimeConfig($provider);

        try {
            $oauthUser = Socialite::driver($provider)->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'Sign-in failed: ' . $e->getMessage());
        }

        // 1. Already linked to this provider?
        $user = User::where('provider', $provider)
            ->where('provider_id', $oauthUser->getId())
            ->first();

        // 2. Same email exists locally — link the social account
        if (! $user && $oauthUser->getEmail()) {
            $user = User::where('email', $oauthUser->getEmail())->first();
            if ($user) {
                $user->forceFill([
                    'provider' => $provider,
                    'provider_id' => $oauthUser->getId(),
                    'avatar' => $user->avatar ?: $oauthUser->getAvatar(),
                    'email_verified_at' => $user->email_verified_at ?: now(),
                ])->save();
            }
        }

        // 3. Brand new user — create as a reader
        if (! $user) {
            $user = User::create([
                'name' => $oauthUser->getName() ?: $oauthUser->getNickname() ?: 'Friend',
                'email' => $oauthUser->getEmail() ?: $provider . '_' . $oauthUser->getId() . '@noreply.kukitales.local',
                'password' => bcrypt(Str::random(40)),
                'role' => 'reader',
                'provider' => $provider,
                'provider_id' => $oauthUser->getId(),
                'avatar' => $oauthUser->getAvatar(),
                'email_verified_at' => now(),
                'is_active' => true,
            ]);
        }

        if (! $user->is_active) {
            return redirect()->route('login')->with('error', 'Your account has been deactivated.');
        }

        Auth::login($user, true);

        return redirect()->intended(route('user.dashboard'));
    }

    /**
     * A provider is "configured" if its admin toggle is ON and
     * credentials are present in either DB settings or .env config.
     */
    protected function isConfigured(string $provider): bool
    {
        if (! (bool) SiteSetting::get("social_{$provider}_enabled")) {
            return false;
        }
        return (bool) $this->credentialValue($provider, 'client_id')
            && (bool) $this->credentialValue($provider, 'client_secret');
    }

    /**
     * Push DB-stored credentials into the runtime services config so
     * Socialite picks them up. Settings override env values.
     */
    protected function applyRuntimeConfig(string $provider): void
    {
        $clientId = $this->credentialValue($provider, 'client_id');
        $clientSecret = $this->credentialValue($provider, 'client_secret');

        Config::set("services.$provider.client_id", $clientId);
        Config::set("services.$provider.client_secret", $clientSecret);

        if (! config("services.$provider.redirect")) {
            Config::set("services.$provider.redirect", url("/auth/$provider/callback"));
        }
    }

    protected function credentialValue(string $provider, string $field): ?string
    {
        return SiteSetting::get("social_{$provider}_{$field}")
            ?: config("services.$provider.$field");
    }

    /**
     * Static helper used by the auth partial Blade — keeps the same
     * "buttons appear only when enabled + credentials present" rule.
     */
    public static function providerEnabled(string $provider): bool
    {
        if (! (bool) SiteSetting::get("social_{$provider}_enabled")) return false;
        $id = SiteSetting::get("social_{$provider}_client_id") ?: config("services.$provider.client_id");
        $secret = SiteSetting::get("social_{$provider}_client_secret") ?: config("services.$provider.client_secret");
        return $id && $secret;
    }
}
