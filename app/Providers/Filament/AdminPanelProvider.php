<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('KukiTales')
            ->brandLogo(fn () => new HtmlString(
                '<div style="display:flex;align-items:center;gap:10px;">'
                . '<div style="width:36px;height:36px;background:linear-gradient(135deg,#c0392b,#922b21);border-radius:8px;display:flex;align-items:center;justify-content:center;font-family:\'Cormorant Garamond\',serif;font-size:20px;font-weight:900;color:#fff;box-shadow:0 3px 10px rgba(192,57,43,0.35);">K</div>'
                . '<div style="line-height:1.1;">'
                . '<div style="font-family:\'Cormorant Garamond\',serif;font-size:20px;font-weight:700;color:#fff;">KukiTales</div>'
                . '<div style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#d4a843;">Admin Panel</div>'
                . '</div></div>'
            ))
            ->favicon(asset('favicon.ico'))
            ->colors([
                'primary' => Color::Red,
                'danger' => Color::Rose,
                'gray' => Color::Stone,
                'info' => Color::Blue,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
            ])
            ->font('Nunito')
            ->maxContentWidth(MaxWidth::Full)
            ->sidebarCollapsibleOnDesktop()
            ->collapsibleNavigationGroups(false)
            ->navigationGroups([
                NavigationGroup::make('📖 Stories')->collapsible(true),
                NavigationGroup::make('📰 News')->collapsible(true),
                NavigationGroup::make('🏛 History')->collapsible(true),
                NavigationGroup::make('🎵 Culture')->collapsible(true),
                NavigationGroup::make('✍️ Blog')->collapsible(true),
                NavigationGroup::make('💰 Monetization')->collapsible(true),
                NavigationGroup::make('👥 People')->collapsible(true),
                NavigationGroup::make('🛡 Moderation')->collapsible(true),
                NavigationGroup::make('⚙ Settings')->collapsible(true),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            // Default Filament widgets (AccountWidget, FilamentInfoWidget) removed —
            // only your custom StatsOverview is auto-discovered from app/Filament/Widgets.
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => $this->customCss(),
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    protected function customCss(): string
    {
        return <<<HTML
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root {
  --kt-red: #c0392b;
  --kt-red-dark: #922b21;
  --kt-red-soft: #fdf0ee;
  --kt-gold: #d4a843;
  --kt-dark: #1a0a08;
  --kt-text-mid: #5a2a22;
  --kt-border: #edddd9;
}

/* Base font everywhere */
html, body, .fi-body, .fi-sidebar, .fi-topbar, .fi-main {
  font-family: 'Nunito', system-ui, sans-serif !important;
  font-size: 15px !important;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}
body { letter-spacing: 0.005em; }

/* App background */
.fi-body { background: #faf6f4 !important; }
.dark .fi-body { background: #1a0a08 !important; }

/* ─── TOPBAR ─── */
.fi-topbar {
  background: #fff !important;
  border-bottom: 3px solid var(--kt-red) !important;
  box-shadow: 0 2px 14px rgba(192,57,43,0.08) !important;
  height: 64px;
}
.dark .fi-topbar {
  background: #2a1410 !important;
  border-bottom: 3px solid var(--kt-red) !important;
}

/* ─── SIDEBAR (DARK) ─── */

/* Background — dark wine gradient */
aside.fi-sidebar,
.fi-sidebar,
.fi-sidebar .fi-sidebar-nav,
.fi-sidebar .fi-sidebar-header {
  background: linear-gradient(180deg, #1a0a08 0%, #2d1410 100%) !important;
  border-right: 1px solid rgba(192,57,43,0.22) !important;
}

.fi-sidebar-header {
  padding: 14px 18px !important;
  border-bottom: 1px solid rgba(192,57,43,0.22) !important;
  min-height: 64px !important;
  background: rgba(0,0,0,0.2) !important;
}

/* KILL the giant gaps Filament adds (gap-y-7 = 28px between groups) */
.fi-sidebar .fi-sidebar-nav {
  padding: 12px 10px !important;
  gap: 0 !important;
  row-gap: 0 !important;
}
.fi-sidebar .fi-sidebar-nav-groups {
  gap: 0 !important;
  row-gap: 0 !important;
  margin: 0 !important;
  padding: 0 !important;
  list-style: none !important;
}
.fi-sidebar .fi-sidebar-group {
  gap: 0 !important;
  row-gap: 0 !important;
  margin: 0 !important;
  padding: 2px 0 !important;
  list-style: none !important;
}
.fi-sidebar .fi-sidebar-group + .fi-sidebar-group {
  margin-top: 2px !important;
}
.fi-sidebar .fi-sidebar-group-items {
  gap: 0 !important;
  row-gap: 0 !important;
  margin: 0 !important;
  padding: 0 !important;
  list-style: none !important;
}

/* Group label / button */
.fi-sidebar .fi-sidebar-group-button {
  padding: 8px 12px !important;
  margin: 0 !important;
  background: transparent !important;
  border-radius: 6px !important;
}
.fi-sidebar .fi-sidebar-group-button:hover {
  background: rgba(212,168,67,0.08) !important;
}
.fi-sidebar .fi-sidebar-group-label {
  font-family: 'Nunito', sans-serif !important;
  font-size: 11px !important;
  font-weight: 800 !important;
  letter-spacing: 1.5px !important;
  text-transform: uppercase !important;
  color: #d4a843 !important;
  line-height: 1.4 !important;
}
.fi-sidebar .fi-sidebar-group-icon,
.fi-sidebar .fi-sidebar-group-collapse-button,
.fi-sidebar .fi-sidebar-group-button svg {
  color: #d4a843 !important;
}

/* Items */
.fi-sidebar .fi-sidebar-item {
  margin: 0 !important;
  padding: 0 !important;
  list-style: none !important;
}
.fi-sidebar .fi-sidebar-item .fi-sidebar-item-button,
.fi-sidebar .fi-sidebar-item-button {
  font-size: 14px !important;
  font-weight: 600 !important;
  padding: 8px 12px !important;
  border-radius: 7px !important;
  margin: 1px 0 !important;
  background: transparent !important;
  color: #e0cec8 !important;
  transition: all 0.12s ease !important;
}
.fi-sidebar .fi-sidebar-item-button span,
.fi-sidebar .fi-sidebar-item-button .fi-sidebar-item-label {
  color: #e0cec8 !important;
  font-weight: 600 !important;
}
.fi-sidebar .fi-sidebar-item-icon,
.fi-sidebar .fi-sidebar-item-button svg {
  color: #c8a8a4 !important;
  width: 18px !important;
  height: 18px !important;
}

/* Hover */
.fi-sidebar .fi-sidebar-item-button:hover {
  background: rgba(192,57,43,0.22) !important;
}
.fi-sidebar .fi-sidebar-item-button:hover,
.fi-sidebar .fi-sidebar-item-button:hover span,
.fi-sidebar .fi-sidebar-item-button:hover .fi-sidebar-item-label {
  color: #fff !important;
}
.fi-sidebar .fi-sidebar-item-button:hover .fi-sidebar-item-icon,
.fi-sidebar .fi-sidebar-item-button:hover svg {
  color: #fff !important;
}

/* Active */
.fi-sidebar .fi-sidebar-item.fi-active > .fi-sidebar-item-button,
.fi-sidebar .fi-sidebar-item-active .fi-sidebar-item-button,
.fi-sidebar .fi-sidebar-item-button[aria-current="page"] {
  background: linear-gradient(135deg, #c0392b 0%, #922b21 100%) !important;
  color: #fff !important;
  box-shadow: 0 4px 14px rgba(192,57,43,0.4) !important;
  font-weight: 700 !important;
}
.fi-sidebar .fi-sidebar-item.fi-active > .fi-sidebar-item-button *,
.fi-sidebar .fi-sidebar-item-active .fi-sidebar-item-button *,
.fi-sidebar .fi-sidebar-item-button[aria-current="page"] * {
  color: #fff !important;
}

/* Brand logo container */
.fi-sidebar-header a, .fi-sidebar-header .fi-logo {
  color: #fff !important;
}

/* Mobile/desktop collapse button (the "<" arrow) */
.fi-sidebar-close-overlay-btn,
.fi-topbar-open-sidebar-btn svg,
.fi-sidebar svg {
  color: inherit;
}
.fi-sidebar-close-overlay-btn { color: #d4a843 !important; }

/* ─── PAGE HEADER ─── */
.fi-header {
  padding: 24px 0 !important;
  margin-bottom: 18px;
}
.fi-header-heading {
  font-family: 'Cormorant Garamond', serif !important;
  font-size: 34px !important;
  font-weight: 700 !important;
  color: var(--kt-dark) !important;
  letter-spacing: -0.01em;
}
.dark .fi-header-heading { color: #fff !important; }
.fi-header-subheading {
  font-size: 14px !important;
  color: #7a5a52 !important;
  margin-top: 4px;
}

/* ─── BUTTONS ─── */
.fi-btn {
  font-weight: 700 !important;
  font-size: 13px !important;
  letter-spacing: 0.01em;
  border-radius: 8px !important;
  transition: all 0.15s ease !important;
}
.fi-btn-color-primary {
  box-shadow: 0 3px 10px rgba(192,57,43,0.22);
}
.fi-btn-color-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(192,57,43,0.32);
}

/* ─── CARDS / SECTIONS ─── */
.fi-section, .fi-wi {
  border-radius: 12px !important;
  border: 1px solid var(--kt-border) !important;
  box-shadow: 0 2px 10px rgba(192,57,43,0.04) !important;
}
.dark .fi-section, .dark .fi-wi {
  border: 1px solid rgba(192,57,43,0.18) !important;
  background: #2a1410 !important;
}

.fi-section-header { padding: 16px 20px !important; }
.fi-section-header-heading {
  font-family: 'Cormorant Garamond', serif !important;
  font-size: 20px !important;
  font-weight: 700 !important;
}
.fi-section-content { padding: 20px !important; }

/* ─── TABLES ─── */
.fi-ta { border-radius: 12px !important; border: 1px solid var(--kt-border) !important; }
.fi-ta-header { padding: 14px 18px !important; }
.fi-ta-header-cell {
  font-size: 11px !important;
  font-weight: 800 !important;
  letter-spacing: 1px !important;
  text-transform: uppercase !important;
  color: var(--kt-red-dark) !important;
  padding: 12px 14px !important;
}
.fi-ta-cell {
  padding: 14px !important;
  font-size: 14px !important;
}
.fi-ta-row:hover { background: var(--kt-red-soft) !important; }
.dark .fi-ta-row:hover { background: rgba(192,57,43,0.12) !important; }

/* ─── BADGES ─── */
.fi-badge {
  font-weight: 700 !important;
  font-size: 11px !important;
  padding: 4px 9px !important;
  border-radius: 4px !important;
  letter-spacing: 0.4px;
  text-transform: uppercase;
}

/* ─── FORMS ─── */
.fi-input, .fi-select-input, .fi-textarea {
  font-size: 14px !important;
  border-radius: 8px !important;
  border: 1px solid var(--kt-border) !important;
  transition: border-color 0.15s, box-shadow 0.15s;
}
.fi-input:focus, .fi-select-input:focus, .fi-textarea:focus {
  border-color: var(--kt-red) !important;
  box-shadow: 0 0 0 3px rgba(192,57,43,0.12) !important;
}
.fi-fo-field-wrp-label, .fi-fo-field-wrp label {
  font-size: 12px !important;
  font-weight: 700 !important;
  letter-spacing: 0.5px;
  color: var(--kt-text-mid) !important;
}
.dark .fi-fo-field-wrp-label, .dark .fi-fo-field-wrp label { color: #f1948a !important; }

/* ─── TABS ─── */
.fi-tabs {
  background: var(--kt-red-soft);
  border-radius: 10px;
  padding: 4px;
  border: 1px solid var(--kt-border);
}
.dark .fi-tabs { background: rgba(192,57,43,0.12); border-color: rgba(192,57,43,0.18); }
.fi-tabs-tab {
  font-weight: 700 !important;
  font-size: 13px !important;
  padding: 9px 16px !important;
  border-radius: 7px !important;
  border: none !important;
}
.fi-tabs-tab[aria-selected="true"] {
  background: #fff !important;
  color: var(--kt-red) !important;
  box-shadow: 0 2px 6px rgba(192,57,43,0.18) !important;
}
.dark .fi-tabs-tab[aria-selected="true"] { background: #1a0a08 !important; color: #f1948a !important; }

/* ─── STATS WIDGET (dashboard) ─── */
.fi-wi-stats-overview-stat {
  border-radius: 14px !important;
  padding: 22px !important;
  border: 1px solid var(--kt-border) !important;
  background: linear-gradient(135deg, #fff 0%, #fdf6f4 100%) !important;
  box-shadow: 0 4px 16px rgba(192,57,43,0.06) !important;
  transition: transform 0.18s, box-shadow 0.18s;
}
.fi-wi-stats-overview-stat:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 26px rgba(192,57,43,0.14) !important;
}
.dark .fi-wi-stats-overview-stat {
  background: linear-gradient(135deg, #2a1410 0%, #3d1a14 100%) !important;
}
.fi-wi-stats-overview-stat-label {
  font-size: 12px !important;
  font-weight: 700 !important;
  letter-spacing: 1px;
  text-transform: uppercase;
  color: var(--kt-text-mid) !important;
}
.fi-wi-stats-overview-stat-value {
  font-family: 'Cormorant Garamond', serif !important;
  font-size: 38px !important;
  font-weight: 700 !important;
  color: var(--kt-dark) !important;
  line-height: 1.1;
  margin: 6px 0 4px;
}
.dark .fi-wi-stats-overview-stat-value { color: #fff !important; }
.fi-wi-stats-overview-stat-description {
  font-size: 12px !important;
  color: #7a5a52 !important;
}

/* ─── PAGINATION ─── */
.fi-pagination-item {
  font-weight: 700 !important;
  border-radius: 8px !important;
}

/* ─── DROPDOWNS / PANELS ─── */
.fi-dropdown-panel {
  border-radius: 10px !important;
  border: 1px solid var(--kt-border) !important;
  box-shadow: 0 10px 30px rgba(192,57,43,0.14) !important;
}

/* ─── LOGIN PAGE ─── */
.fi-simple-layout {
  background: linear-gradient(135deg, #1a0a08, #2d1410, #3d0f0a) !important;
}
.fi-simple-main {
  background: #fff !important;
  border-radius: 16px !important;
  padding: 32px !important;
  box-shadow: 0 20px 60px rgba(0,0,0,0.3) !important;
}
.dark .fi-simple-main { background: #2a1410 !important; }
</style>
HTML;
    }
}
