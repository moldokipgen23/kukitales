<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SeoSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';
    protected static ?string $navigationLabel = 'SEO & Search';
    protected static ?string $navigationGroup = '⚙ Settings';
    protected static ?int $navigationSort = 0;
    protected static ?string $title = 'SEO & Search Engine Configuration';
    protected static string $view = 'filament.pages.seo-settings';

    public array $data = [];

    public function mount(): void
    {
        $keys = [
            'site_language', 'twitter_handle', 'default_og_image', 'publisher_logo',
            'google_analytics_id', 'google_tag_manager_id',
            'gsc_verification', 'bing_verification', 'facebook_app_id',
            'auto_ping_search_engines',
        ];
        $this->data = [];
        foreach ($keys as $k) {
            $this->data[$k] = SiteSetting::get($k);
        }
        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form->statePath('data')->schema([
            Forms\Components\Section::make('Branding for Shares & Search')
                ->description('Used when your pages are shared on social media or appear in Google results.')
                ->schema([
                    Forms\Components\FileUpload::make('publisher_logo')
                        ->label('Publisher Logo')
                        ->image()
                        ->directory('seo')
                        ->helperText('Square logo used in Google News & search results. PNG, ideally 600×60 or square.'),
                    Forms\Components\FileUpload::make('default_og_image')
                        ->label('Default Share Image (Open Graph)')
                        ->image()
                        ->directory('seo')
                        ->helperText('Shown when the homepage or any post-without-cover is shared on Facebook/WhatsApp/Twitter. 1200×630px.'),
                    Forms\Components\TextInput::make('twitter_handle')
                        ->label('Twitter / X Handle')
                        ->placeholder('@kukitales')
                        ->helperText('Used in Twitter Card meta tags.'),
                    Forms\Components\Select::make('site_language')
                        ->label('Primary Site Language')
                        ->options(['en' => 'English', 'hi' => 'Hindi', 'as' => 'Assamese', 'mni' => 'Meitei (Manipuri)'])
                        ->default('en'),
                ])->columns(2),

            Forms\Components\Section::make('Analytics & Tracking')
                ->description('Paste IDs here — scripts auto-inject site-wide.')
                ->schema([
                    Forms\Components\TextInput::make('google_analytics_id')
                        ->label('Google Analytics 4 Measurement ID')
                        ->placeholder('G-XXXXXXXXXX')
                        ->helperText('Find this at analytics.google.com → Admin → Data Streams.'),
                    Forms\Components\TextInput::make('google_tag_manager_id')
                        ->label('Google Tag Manager Container ID')
                        ->placeholder('GTM-XXXXXX')
                        ->helperText('Use this instead of GA4 if you also need conversion tracking, ads pixels, etc.'),
                    Forms\Components\TextInput::make('facebook_app_id')
                        ->label('Facebook App ID')
                        ->placeholder('e.g. 1234567890123456')
                        ->helperText('Optional. Needed if you use Facebook Insights or want fb:app_id in OG tags.'),
                ])->columns(1),

            Forms\Components\Section::make('Search Engine Verification')
                ->description('Paste the verification tokens you get when adding the site to Google Search Console or Bing Webmaster Tools.')
                ->schema([
                    Forms\Components\TextInput::make('gsc_verification')
                        ->label('Google Search Console')
                        ->helperText('Just the token, not the full meta tag. Get it at search.google.com/search-console.'),
                    Forms\Components\TextInput::make('bing_verification')
                        ->label('Bing Webmaster Tools')
                        ->helperText('Just the token. Get it at bing.com/webmasters.'),
                ])->columns(2),

            Forms\Components\Section::make('Automatic Indexing')
                ->schema([
                    Forms\Components\Toggle::make('auto_ping_search_engines')
                        ->label('Auto-ping Google & Bing when a post is published')
                        ->helperText('When ON, every publish sends a sitemap-ping so search engines recrawl faster. Recommended.')
                        ->default(true),
                ]),

            Forms\Components\Section::make('Public SEO Endpoints')
                ->description('These are live and ready to submit to Google Search Console.')
                ->schema([
                    Forms\Components\Placeholder::make('endpoints')
                        ->label('')
                        ->content(new \Illuminate\Support\HtmlString(
                            '<div style="font-family:ui-monospace,monospace;font-size:13px;line-height:1.9;">'
                            . '• <a href="' . url('/sitemap.xml') . '" target="_blank" style="color:#c0392b;font-weight:700;">/sitemap.xml</a> &nbsp; — Sitemap index (submit this one)<br>'
                            . '• <a href="' . url('/sitemap-news.xml') . '" target="_blank" style="color:#c0392b;font-weight:700;">/sitemap-news.xml</a> &nbsp; — Google News sitemap (last 48h)<br>'
                            . '• <a href="' . url('/sitemap-posts.xml') . '" target="_blank" style="color:#c0392b;font-weight:700;">/sitemap-posts.xml</a> &nbsp; — All posts<br>'
                            . '• <a href="' . url('/sitemap-categories.xml') . '" target="_blank" style="color:#c0392b;font-weight:700;">/sitemap-categories.xml</a> &nbsp; — All categories<br>'
                            . '• <a href="' . url('/sitemap-series.xml') . '" target="_blank" style="color:#c0392b;font-weight:700;">/sitemap-series.xml</a> &nbsp; — All series<br>'
                            . '• <a href="' . url('/robots.txt') . '" target="_blank" style="color:#c0392b;font-weight:700;">/robots.txt</a> &nbsp; — Crawler directives<br>'
                            . '• <a href="' . url('/feed.xml') . '" target="_blank" style="color:#c0392b;font-weight:700;">/feed.xml</a> &nbsp; — RSS feed (30 latest posts)'
                            . '</div>'
                        )),
                ]),
        ]);
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label('Save SEO Settings')
                ->submit('save')
                ->color('primary'),
        ];
    }

    public function save(): void
    {
        $values = $this->form->getState();
        foreach ($values as $key => $value) {
            SiteSetting::set($key, is_array($value) || $value === '' ? (is_array($value) ? null : $value) : (string) $value);
        }
        \Illuminate\Support\Facades\Cache::flush();
        Notification::make()->title('SEO settings saved.')->success()->send();
    }
}
