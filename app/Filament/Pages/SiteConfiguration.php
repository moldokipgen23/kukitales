<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;

class SiteConfiguration extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Site Configuration';
    protected static ?string $navigationGroup = '⚙ Settings';
    protected static ?int $navigationSort = 0;
    protected static ?string $title = 'Site Configuration';
    protected static string $view = 'filament.pages.site-configuration';

    /**
     * Every key the page reads + writes. Group → list of keys.
     */
    protected array $keys = [
        // General
        'site_name', 'site_tagline', 'site_description', 'topbar_text',
        // Branding
        'site_logo', 'favicon', 'publisher_logo',
        // Social URLs (footer)
        'social_facebook', 'social_twitter', 'social_instagram',
        'social_youtube', 'social_linkedin', 'social_whatsapp',
        // Mobile apps (footer)
        'app_store_url', 'play_store_url',
        // Footer
        'footer_text', 'footer_about', 'contact_email', 'contact_phone', 'contact_address',
        // Social login
        'social_google_enabled', 'social_google_client_id', 'social_google_client_secret',
        'social_facebook_enabled', 'social_facebook_client_id', 'social_facebook_client_secret',
        // Donations
        'donation_upi_id', 'donation_bank_name', 'donation_bank_account', 'donation_bank_ifsc',
        'donation_razorpay_link', 'donation_stripe_link', 'donation_paypal_link',
        // Breaking news
        'breaking_news_enabled', 'breaking_news_text',
    ];

    public array $data = [];

    public function mount(): void
    {
        foreach ($this->keys as $k) {
            $this->data[$k] = SiteSetting::get($k);
        }
        // Cast toggle keys to booleans for the form
        foreach (['social_google_enabled', 'social_facebook_enabled', 'breaking_news_enabled'] as $bk) {
            $this->data[$bk] = (bool) ($this->data[$bk] ?? false);
        }
        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form->statePath('data')->schema([
            Forms\Components\Tabs::make()->columnSpanFull()->tabs([

                // ─────────────── GENERAL ───────────────
                Forms\Components\Tabs\Tab::make('General')
                    ->icon('heroicon-o-globe-alt')
                    ->schema([
                        Forms\Components\TextInput::make('site_name')
                            ->label('Site Name')
                            ->required()
                            ->helperText('Shown in the header logo, footer, and browser tab.'),
                        Forms\Components\TextInput::make('site_tagline')
                            ->label('Tagline')
                            ->helperText('e.g. "Voices of the Hills". Shown under the logo.'),
                        Forms\Components\Textarea::make('site_description')
                            ->label('Site Description')
                            ->rows(2)->maxLength(280)
                            ->helperText('Used in the footer brand panel and as default meta description.'),
                        Forms\Components\TextInput::make('topbar_text')
                            ->label('Topbar Banner Text')
                            ->maxLength(120)
                            ->helperText('Small text shown in the dark bar at the very top.'),
                    ])->columns(1),

                // ─────────────── BRANDING ───────────────
                Forms\Components\Tabs\Tab::make('Branding')
                    ->icon('heroicon-o-paint-brush')
                    ->schema([
                        Forms\Components\FileUpload::make('site_logo')
                            ->label('Header Logo (optional)')
                            ->image()->directory('branding')
                            ->helperText('Leave blank to use the red "K" mark + text logo.'),
                        Forms\Components\FileUpload::make('favicon')
                            ->label('Favicon')
                            ->image()->directory('branding')
                            ->helperText('Small icon shown in the browser tab. 32×32 or 64×64 PNG/ICO.'),
                        Forms\Components\FileUpload::make('publisher_logo')
                            ->label('Publisher Logo (for Google News)')
                            ->image()->directory('branding')
                            ->helperText('Used in Google News & rich results. Square PNG, ideally 600×60 or square.'),
                    ])->columns(1),

                // ─────────────── SOCIAL MEDIA ───────────────
                Forms\Components\Tabs\Tab::make('Social Media')
                    ->icon('heroicon-o-share')
                    ->schema([
                        Forms\Components\Placeholder::make('hint_social')->label('')
                            ->content('These URLs appear as icon buttons in the site footer. Leave blank to hide that icon.'),
                        Forms\Components\TextInput::make('social_facebook')->label('Facebook URL')->url()->prefixIcon('heroicon-o-link'),
                        Forms\Components\TextInput::make('social_twitter')->label('Twitter / X URL')->url()->prefixIcon('heroicon-o-link'),
                        Forms\Components\TextInput::make('social_instagram')->label('Instagram URL')->url()->prefixIcon('heroicon-o-link'),
                        Forms\Components\TextInput::make('social_youtube')->label('YouTube URL')->url()->prefixIcon('heroicon-o-link'),
                        Forms\Components\TextInput::make('social_linkedin')->label('LinkedIn URL')->url()->prefixIcon('heroicon-o-link'),
                        Forms\Components\TextInput::make('social_whatsapp')->label('WhatsApp / Community Link')->url()->prefixIcon('heroicon-o-link'),
                    ])->columns(2),

                // ─────────────── MOBILE APPS ───────────────
                Forms\Components\Tabs\Tab::make('Mobile Apps')
                    ->icon('heroicon-o-device-phone-mobile')
                    ->schema([
                        Forms\Components\Placeholder::make('hint_apps')->label('')
                            ->content('"Get the app" badges shown in the footer. Leave blank to hide.'),
                        Forms\Components\TextInput::make('app_store_url')->label('App Store URL (iOS)')->url()->placeholder('https://apps.apple.com/...'),
                        Forms\Components\TextInput::make('play_store_url')->label('Play Store URL (Android)')->url()->placeholder('https://play.google.com/store/apps/details?id=...'),
                    ])->columns(2),

                // ─────────────── CONTACT / FOOTER ───────────────
                Forms\Components\Tabs\Tab::make('Footer & Contact')
                    ->icon('heroicon-o-rectangle-stack')
                    ->schema([
                        Forms\Components\Textarea::make('footer_about')
                            ->label('About paragraph (footer)')
                            ->rows(3)->maxLength(400)
                            ->helperText('Shown in the footer next to the logo.'),
                        Forms\Components\TextInput::make('contact_email')->label('Public contact email')->email(),
                        Forms\Components\TextInput::make('contact_phone')->label('Public phone'),
                        Forms\Components\Textarea::make('contact_address')->label('Office / postal address')->rows(2),
                        Forms\Components\TextInput::make('footer_text')
                            ->label('Footer copyright line')
                            ->helperText('Tiny text at the very bottom of the footer.'),
                    ])->columns(2),

                // ─────────────── SOCIAL LOGIN ───────────────
                Forms\Components\Tabs\Tab::make('Social Login')
                    ->icon('heroicon-o-key')
                    ->schema([
                        Forms\Components\Placeholder::make('hint_login')->label('')
                            ->content(new \Illuminate\Support\HtmlString(
                                '<div style="font-size:13px;line-height:1.6;">'
                                . '<strong>Setup steps:</strong><br>'
                                . '<strong>Google:</strong> <a href="https://console.cloud.google.com/apis/credentials" target="_blank" style="color:#c0392b;">console.cloud.google.com/apis/credentials</a> → Create OAuth Client ID (Web) → Add redirect URI: <code style="background:#fdf0ee;padding:2px 6px;border-radius:4px;">' . url('/auth/google/callback') . '</code><br>'
                                . '<strong>Facebook:</strong> <a href="https://developers.facebook.com/apps" target="_blank" style="color:#c0392b;">developers.facebook.com/apps</a> → Add Facebook Login → Redirect URI: <code style="background:#fdf0ee;padding:2px 6px;border-radius:4px;">' . url('/auth/facebook/callback') . '</code>'
                                . '</div>'
                            )),
                        Forms\Components\Section::make('Google')
                            ->schema([
                                Forms\Components\Toggle::make('social_google_enabled')->label('Enable Google sign-in'),
                                Forms\Components\TextInput::make('social_google_client_id')
                                    ->label('Google Client ID')->placeholder('xxxxxxxx.apps.googleusercontent.com'),
                                Forms\Components\TextInput::make('social_google_client_secret')
                                    ->label('Google Client Secret')->password()->revealable(),
                            ])->columns(1),
                        Forms\Components\Section::make('Facebook')
                            ->schema([
                                Forms\Components\Toggle::make('social_facebook_enabled')->label('Enable Facebook sign-in'),
                                Forms\Components\TextInput::make('social_facebook_client_id')->label('Facebook App ID'),
                                Forms\Components\TextInput::make('social_facebook_client_secret')->label('Facebook App Secret')->password()->revealable(),
                            ])->columns(1),
                    ]),

                // ─────────────── DONATIONS ───────────────
                Forms\Components\Tabs\Tab::make('Donations')
                    ->icon('heroicon-o-heart')
                    ->schema([
                        Forms\Components\Placeholder::make('hint_don')->label('')
                            ->content('These payment methods appear on every donation page. Configured methods are auto-shown; blank fields are hidden.'),
                        Forms\Components\Section::make('UPI (recommended for India)')
                            ->schema([
                                Forms\Components\TextInput::make('donation_upi_id')->label('UPI ID')->placeholder('kukitales@upi'),
                            ])->columns(1),
                        Forms\Components\Section::make('Bank Transfer')
                            ->schema([
                                Forms\Components\TextInput::make('donation_bank_name')->label('Bank name'),
                                Forms\Components\TextInput::make('donation_bank_account')->label('Account number'),
                                Forms\Components\TextInput::make('donation_bank_ifsc')->label('IFSC / SWIFT code'),
                            ])->columns(3),
                        Forms\Components\Section::make('Payment Links')
                            ->schema([
                                Forms\Components\TextInput::make('donation_razorpay_link')->label('Razorpay link')->url()->placeholder('https://razorpay.me/@yourhandle'),
                                Forms\Components\TextInput::make('donation_stripe_link')->label('Stripe payment link')->url(),
                                Forms\Components\TextInput::make('donation_paypal_link')->label('PayPal.me link')->url(),
                            ])->columns(1),
                    ]),

                // ─────────────── BREAKING NEWS ───────────────
                Forms\Components\Tabs\Tab::make('Breaking News Ticker')
                    ->icon('heroicon-o-bolt')
                    ->schema([
                        Forms\Components\Toggle::make('breaking_news_enabled')
                            ->label('Show the breaking news ticker')
                            ->helperText('When ON, the red ticker is shown on the homepage. Falls back to the text below when no posts are marked "breaking".'),
                        Forms\Components\Textarea::make('breaking_news_text')
                            ->label('Fallback ticker text')
                            ->rows(3)
                            ->helperText('Multiple headlines separated by " | " (pipe with spaces).'),
                    ])->columns(1),

            ]),
        ]);
    }

    public function save(): void
    {
        $values = $this->form->getState();
        foreach ($values as $key => $value) {
            // Booleans stored as "1"/"0", everything else as-is
            $stored = is_bool($value) ? ($value ? '1' : '0') : ($value === '' ? null : $value);
            SiteSetting::set($key, is_array($stored) ? null : $stored);
        }
        Cache::flush();
        Notification::make()->title('Site configuration saved.')->success()->send();
    }
}
