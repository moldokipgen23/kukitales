<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
@php
  $siteName = \App\Models\SiteSetting::get('site_name', 'KukiTales');
  $siteTagline = \App\Models\SiteSetting::get('site_tagline', 'Voices of the Hills');
  $siteDescription = \App\Models\SiteSetting::get('site_description');
  $defaultOg = \App\Models\SiteSetting::get('default_og_image');
  $defaultOgUrl = $defaultOg ? (str_starts_with($defaultOg, 'http') ? $defaultOg : asset('storage/' . $defaultOg)) : null;
  $publisherLogo = \App\Models\SiteSetting::get('publisher_logo');
  $publisherLogoUrl = $publisherLogo ? (str_starts_with($publisherLogo, 'http') ? $publisherLogo : asset('storage/' . $publisherLogo)) : null;
  $twitterHandle = \App\Models\SiteSetting::get('twitter_handle', '@kukitales');
  $facebookAppId = \App\Models\SiteSetting::get('facebook_app_id');
  $gaId = \App\Models\SiteSetting::get('google_analytics_id');
  $gtmId = \App\Models\SiteSetting::get('google_tag_manager_id');
  $gscToken = \App\Models\SiteSetting::get('gsc_verification');
  $bingToken = \App\Models\SiteSetting::get('bing_verification');
  $siteLang = \App\Models\SiteSetting::get('site_language', 'en');

  // Per-page SEO can be overridden via @section or by setting these vars in the view
  $pageTitle = trim($__env->yieldContent('title')) ?: ($siteName . ' — ' . $siteTagline);
  $pageDescription = trim($__env->yieldContent('meta_description')) ?: $siteDescription;
  $pageCanonical = trim($__env->yieldContent('canonical')) ?: url()->current();
  $pageOgImage = trim($__env->yieldContent('og_image')) ?: $defaultOgUrl;
  $pageOgType = trim($__env->yieldContent('og_type')) ?: 'website';
  $pageNoindex = trim($__env->yieldContent('noindex')) === '1';
@endphp
<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $pageDescription }}">
<link rel="canonical" href="{{ $pageCanonical }}">
@if($pageNoindex)
  <meta name="robots" content="noindex, nofollow">
@else
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
@endif

{{-- Open Graph (Facebook, WhatsApp, LinkedIn) --}}
<meta property="og:site_name"  content="{{ $siteName }}">
<meta property="og:type"       content="{{ $pageOgType }}">
<meta property="og:locale"     content="{{ $siteLang === 'en' ? 'en_US' : $siteLang }}">
<meta property="og:title"      content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $pageDescription }}">
<meta property="og:url"        content="{{ $pageCanonical }}">
@if($pageOgImage)
  <meta property="og:image"        content="{{ $pageOgImage }}">
  <meta property="og:image:width"  content="1200">
  <meta property="og:image:height" content="630">
@endif
@if($facebookAppId)<meta property="fb:app_id" content="{{ $facebookAppId }}">@endif

{{-- Twitter / X --}}
<meta name="twitter:card"    content="{{ $pageOgImage ? 'summary_large_image' : 'summary' }}">
<meta name="twitter:site"    content="{{ $twitterHandle }}">
<meta name="twitter:title"   content="{{ $pageTitle }}">
<meta name="twitter:description" content="{{ $pageDescription }}">
@if($pageOgImage)<meta name="twitter:image" content="{{ $pageOgImage }}">@endif

{{-- Discovery --}}
<link rel="alternate" type="application/rss+xml" title="{{ $siteName }} RSS" href="{{ route('seo.rss') }}">
<link rel="sitemap" type="application/xml" href="{{ route('seo.sitemap') }}">

{{-- Search Engine verification --}}
@if($gscToken)<meta name="google-site-verification" content="{{ $gscToken }}">@endif
@if($bingToken)<meta name="msvalidate.01" content="{{ $bingToken }}">@endif

{{-- Organization JSON-LD (every page) --}}
<script type="application/ld+json">
{!! json_encode([
  '@context' => 'https://schema.org',
  '@type' => 'NewsMediaOrganization',
  'name' => $siteName,
  'url' => url('/'),
  'logo' => $publisherLogoUrl ?: ($defaultOgUrl ?: url('/')),
  'description' => $siteDescription,
  'sameAs' => array_values(array_filter([
    \App\Models\SiteSetting::get('social_facebook'),
    \App\Models\SiteSetting::get('social_twitter'),
    \App\Models\SiteSetting::get('social_instagram'),
    \App\Models\SiteSetting::get('social_youtube'),
  ])),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

{{-- WebSite JSON-LD (enables sitelinks search box) --}}
<script type="application/ld+json">
{!! json_encode([
  '@context' => 'https://schema.org',
  '@type' => 'WebSite',
  'name' => $siteName,
  'url' => url('/'),
  'potentialAction' => [
    '@type' => 'SearchAction',
    'target' => url('/search') . '?q={search_term_string}',
    'query-input' => 'required name=search_term_string',
  ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

@stack('jsonld')

{{-- Google Tag Manager (head) --}}
@if($gtmId)
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{{ $gtmId }}');</script>
@endif

{{-- Google Analytics 4 --}}
@if($gaId)
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{{ $gaId }}');</script>
@endif
@php
  $faviconRaw = \App\Models\SiteSetting::get('favicon');
@endphp
@if($faviconRaw)
  <link rel="icon" type="image/png" href="{{ str_starts_with($faviconRaw, 'http') ? $faviconRaw : asset('storage/' . $faviconRaw) }}">
@endif
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root {
  --red: #c0392b; --red-dark: #922b21; --red-deep: #7b241c; --red-light: #e74c3c;
  --red-soft: #fdf0ee; --red-muted: #f9e0dd; --gold: #d4a843;
  --bg: #faf8f7; --dark: #1a0a08; --dark2: #2d1410;
  --text: #1a0a08; --text-mid: #5a2a22; --text-muted: #9a6058;
  --border: #edddd9; --white: #fff; --pad: 20px;
}
* { margin:0; padding:0; box-sizing:border-box; }
html { scroll-behavior:smooth; }
body { font-family:'Nunito',sans-serif; background:var(--bg); color:var(--text); overflow-x:hidden; }
a { text-decoration:none; color:inherit; }
img { max-width:100%; display:block; }

/* TOPBAR */
.topbar { background:var(--dark); color:var(--gold); font-size:11px; padding:6px 20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:4px; }
.topbar a { color:var(--gold); margin-left:10px; }
.topbar a:hover { color:#fff; }
.topbar-right { display:flex; align-items:center; flex-wrap:wrap; gap:4px; }

/* HEADER */
header { background:#fff; border-bottom:3px solid var(--red); position:sticky; top:0; z-index:200; box-shadow:0 2px 16px rgba(192,57,43,0.12); }
.header-inner { display:flex; align-items:center; justify-content:space-between; padding:0 var(--pad); height:64px; max-width:1200px; margin:0 auto; }
.logo { display:flex; align-items:center; gap:10px; }
.logo-mark { width:40px; height:40px; flex-shrink:0; background:linear-gradient(135deg,var(--red),var(--red-dark)); border-radius:9px; display:flex; align-items:center; justify-content:center; font-family:'Cormorant Garamond',serif; font-size:22px; font-weight:900; color:#fff; box-shadow:0 3px 12px rgba(192,57,43,0.35); }
.logo-text .name { font-family:'Cormorant Garamond',serif; font-size:22px; font-weight:700; color:var(--red); line-height:1; display:block; }
.logo-text .sub { font-size:9px; color:var(--text-muted); letter-spacing:2px; text-transform:uppercase; }

.desktop-nav { display:flex; gap:2px; align-items:center; }
.nav-item { position:relative; }
.nav-item > a { display:block; padding:7px 12px; font-size:13px; font-weight:700; color:var(--text-mid); border-radius:6px; transition:all 0.2s; }
.nav-item > a:hover, .nav-item > a.active { color:var(--red); background:var(--red-soft); }
.dropdown { position:absolute; top:calc(100% + 4px); left:0; background:#fff; border:1px solid var(--border); border-radius:10px; padding:6px; min-width:200px; box-shadow:0 8px 28px rgba(0,0,0,0.12); opacity:0; visibility:hidden; transform:translateY(-6px); transition:all 0.2s; z-index:300; }
.nav-item:hover .dropdown { opacity:1; visibility:visible; transform:translateY(0); }
.dropdown a { display:flex; align-items:center; gap:8px; padding:8px 10px; border-radius:6px; color:var(--text-mid); font-size:13px; }
.dropdown a:hover { background:var(--red-soft); color:var(--red); }
.header-actions { display:flex; gap:8px; align-items:center; }
.btn { padding:7px 16px; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer; border:none; font-family:'Nunito',sans-serif; display:inline-flex; align-items:center; gap:5px; transition:all 0.2s; }
.btn-outline { background:transparent; border:2px solid var(--red); color:var(--red); }
.btn-outline:hover { background:var(--red); color:#fff; }
.btn-solid { background:var(--dark); color:var(--gold); }
.btn-solid:hover { background:var(--red); color:#fff; }

.hamburger { display:none; flex-direction:column; gap:5px; cursor:pointer; padding:6px; border-radius:6px; background:var(--red-soft); border:none; }
.hamburger span { display:block; width:22px; height:2px; background:var(--red); border-radius:2px; transition:all 0.3s; }
.hamburger.open span:nth-child(1) { transform:translateY(7px) rotate(45deg); }
.hamburger.open span:nth-child(2) { opacity:0; }
.hamburger.open span:nth-child(3) { transform:translateY(-7px) rotate(-45deg); }

/* MOBILE MENU */
.mobile-menu { display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:#fff; z-index:150; overflow-y:auto; padding:80px 20px 40px; flex-direction:column; transform:translateX(100%); transition:transform 0.3s ease; }
.mobile-menu.open { transform:translateX(0); }
.mob-nav-item { border-bottom:1px solid var(--border); }
.mob-nav-link { display:flex; align-items:center; justify-content:space-between; padding:14px 4px; font-size:16px; font-weight:700; color:var(--text-mid); cursor:pointer; }
.mob-nav-link:hover { color:var(--red); }
.mob-sub { display:none; padding:0 0 8px 16px; }
.mob-sub.open { display:block; }
.mob-sub a { display:flex; align-items:center; gap:8px; padding:9px 8px; font-size:14px; color:var(--text-mid); border-radius:6px; }
.mob-sub a:hover { background:var(--red-soft); color:var(--red); }
.mob-actions { display:flex; flex-direction:column; gap:10px; margin-top:24px; }
.mob-actions .btn { justify-content:center; padding:12px; font-size:14px; }

/* SEARCH BAR */
.search-bar { background:var(--red-muted); padding:10px var(--pad); display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.search-bar form { display:flex; align-items:center; gap:10px; flex:1; flex-wrap:wrap; max-width:1200px; margin:0 auto; width:100%; }
.search-input { display:flex; align-items:center; background:#fff; border:2px solid var(--border); border-radius:30px; padding:0 14px; gap:8px; height:38px; flex:1; min-width:180px; }
.search-input:focus-within { border-color:var(--red); }
.search-input input { border:none; outline:none; font-family:'Nunito',sans-serif; font-size:13px; flex:1; background:transparent; color:var(--text); width:100%; }
.search-tags { display:flex; gap:6px; flex-wrap:wrap; }
.stag { font-size:11px; color:var(--text-mid); background:#fff; border:1px solid #ddd; padding:4px 10px; border-radius:20px; cursor:pointer; white-space:nowrap; }
.stag:hover { background:var(--red); color:#fff; border-color:var(--red); }

/* HERO */
.hero { background:linear-gradient(150deg,var(--dark) 0%,var(--dark2) 60%,#3d0f0a 100%); padding:50px var(--pad) 44px; position:relative; overflow:hidden; }
.hero::before { content:''; position:absolute; inset:0; pointer-events:none; background:radial-gradient(ellipse at 70% 50%,rgba(192,57,43,0.18) 0%,transparent 70%); }
.hero-inner { max-width:1200px; margin:0 auto; display:grid; grid-template-columns:1fr 1fr; gap:40px; align-items:center; position:relative; }
.hero-badge { display:inline-flex; align-items:center; gap:6px; background:rgba(192,57,43,0.2); border:1px solid rgba(192,57,43,0.4); color:#f1948a; font-size:10px; font-weight:700; letter-spacing:2px; text-transform:uppercase; padding:5px 12px; border-radius:20px; margin-bottom:16px; }
.hero h1 { font-family:'Cormorant Garamond',serif; font-size:44px; font-weight:700; color:#fff; line-height:1.18; margin-bottom:16px; }
.hero h1 em { color:var(--gold); font-style:italic; }
.hero p { color:#c8a8a4; font-size:14px; line-height:1.7; margin-bottom:24px; }
.hero-btns { display:flex; gap:10px; flex-wrap:wrap; }
.btn-hprimary { background:var(--red); color:#fff; padding:11px 24px; border-radius:9px; font-size:13px; font-weight:700; display:inline-flex; align-items:center; gap:6px; }
.btn-hprimary:hover { background:var(--red-light); }
.btn-hghost { background:transparent; border:2px solid rgba(192,57,43,0.5); color:#f1948a; padding:11px 24px; border-radius:9px; font-size:13px; font-weight:700; display:inline-flex; align-items:center; gap:6px; }
.btn-hghost:hover { border-color:var(--red); color:#fff; }
.hero-card { background:rgba(255,255,255,0.06); border:1px solid rgba(192,57,43,0.25); border-radius:14px; overflow:hidden; cursor:pointer; transition:transform 0.3s; display:block; }
.hero-card:hover { transform:translateY(-4px); }
.hero-card-img { height:200px; background:linear-gradient(135deg,#3d0f0a,#5a1a14); display:flex; align-items:center; justify-content:center; font-size:72px; position:relative; }
.hero-card-img.has-cover { background-size:cover; background-position:center; }
.feat-badge { position:absolute; top:12px; left:12px; background:var(--red); color:#fff; font-size:9px; font-weight:800; letter-spacing:1.5px; text-transform:uppercase; padding:4px 9px; border-radius:4px; }
.hero-card-body { padding:18px; }
.hc-cat { font-size:10px; font-weight:700; color:var(--gold); letter-spacing:1.5px; text-transform:uppercase; margin-bottom:7px; }
.hc-title { font-family:'Cormorant Garamond',serif; font-size:20px; font-weight:700; color:#fff; line-height:1.3; margin-bottom:8px; }
.hc-meta { font-size:11px; color:#9a6058; display:flex; gap:10px; flex-wrap:wrap; }

/* TICKER */
.ticker { background:var(--red); padding:9px var(--pad); display:flex; align-items:center; gap:12px; overflow:hidden; }
.ticker-label { background:var(--dark); color:var(--gold); font-size:10px; font-weight:800; letter-spacing:2px; text-transform:uppercase; padding:3px 10px; border-radius:4px; white-space:nowrap; flex-shrink:0; }
.ticker-track { overflow:hidden; flex:1; }
.ticker-inner { display:flex; gap:36px; animation:scroll-ticker 35s linear infinite; white-space:nowrap; }
@keyframes scroll-ticker { 0%{transform:translateX(0)} 100%{transform:translateX(-50%)} }
.ticker-inner span { font-size:12px; font-weight:600; color:#fff; }
.ticker-inner span::before { content:'▶ '; font-size:8px; margin-right:5px; opacity:0.7; }

/* SECTIONS */
.section { padding:48px var(--pad); }
.section-inner { max-width:1200px; margin:0 auto; }
.sec-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:22px; gap:10px; }
.sec-title { font-family:'Cormorant Garamond',serif; font-size:28px; font-weight:700; color:var(--text); display:flex; align-items:center; gap:10px; }
.sec-title::before { content:''; display:block; width:4px; height:28px; background:var(--red); border-radius:3px; flex-shrink:0; }
.sec-title.white { color:#fff; }
.sec-link { font-size:12px; font-weight:700; color:var(--red); white-space:nowrap; }
.sec-link:hover { text-decoration:underline; }
.sec-link.light { color:var(--gold); }

.cat-pills { display:flex; gap:7px; margin-bottom:22px; flex-wrap:wrap; }
.pill { padding:6px 15px; border-radius:30px; font-size:11px; font-weight:700; cursor:pointer; border:2px solid var(--border); color:var(--text-mid); background:#fff; transition:all 0.2s; white-space:nowrap; display:inline-block; }
.pill.active, .pill:hover { background:var(--red); border-color:var(--red); color:#fff; }

/* GRIDS */
.news-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:20px; }
.news-card { background:#fff; border-radius:12px; overflow:hidden; border:1px solid var(--border); transition:all 0.25s; cursor:pointer; display:block; }
.news-card:hover { transform:translateY(-4px); box-shadow:0 14px 36px rgba(192,57,43,0.12); border-color:var(--red); }
.card-thumb { height:160px; display:flex; align-items:center; justify-content:center; font-size:48px; position:relative; background-size:cover; background-position:center; }
.t1{background:linear-gradient(135deg,#fde8e6,#f1948a);}
.t2{background:linear-gradient(135deg,#fde8d5,#f0a070);}
.t3{background:linear-gradient(135deg,#e8fdf0,#80e8a0);}
.t4{background:linear-gradient(135deg,#f0e8fd,#c080e8);}
.t5{background:linear-gradient(135deg,#e0f5ff,#7ec8e3);}
.t6{background:linear-gradient(135deg,#fff5d6,#e8c060);}
.card-cat { position:absolute; bottom:8px; left:8px; font-size:9px; font-weight:800; letter-spacing:1.5px; text-transform:uppercase; padding:3px 8px; border-radius:4px; color:#fff; }
.cat-news{background:var(--red);}
.cat-story{background:var(--gold);color:#1a0a08!important;}
.cat-history{background:#6b3a2a;}
.cat-folktale{background:#2a6b4a;}
.cat-episode{background:#3a2a6b;}
.cat-blog{background:#2a4a6b;}
.cat-music{background:#7b241c;}
.cat-gallery{background:#922b21;}
.card-body { padding:14px; }
.card-title { font-family:'Cormorant Garamond',serif; font-size:17px; font-weight:700; line-height:1.3; margin-bottom:7px; color:var(--text); }
.card-excerpt { font-size:12px; color:var(--text-muted); line-height:1.6; margin-bottom:10px; }
.card-meta { display:flex; align-items:center; justify-content:space-between; font-size:11px; color:var(--text-muted); flex-wrap:wrap; gap:5px; }
.card-author { display:flex; align-items:center; gap:6px; }
.avatar { width:20px; height:20px; border-radius:50%; background:var(--red); display:flex; align-items:center; justify-content:center; font-size:9px; color:#fff; font-weight:700; flex-shrink:0; overflow:hidden; }
.avatar img { width:100%; height:100%; object-fit:cover; }

/* STORIES (dark) */
.stories-section { background:var(--dark); }
.stories-grid { display:grid; grid-template-columns:2fr 1fr 1fr; gap:18px; }
.story-main { background:rgba(255,255,255,0.05); border:1px solid rgba(192,57,43,0.2); border-radius:14px; overflow:hidden; cursor:pointer; transition:transform 0.3s; display:block; }
.story-main:hover { transform:scale(1.01); }
.story-main .story-img { height:240px; background:linear-gradient(135deg,#3d0f0a,#5a1a14); display:flex; align-items:center; justify-content:center; font-size:80px; }
.story-main .story-body { padding:20px; }
.story-tag { display:inline-block; font-size:9px; font-weight:800; letter-spacing:2px; text-transform:uppercase; color:var(--gold); border:1px solid rgba(212,168,67,0.4); padding:3px 9px; border-radius:4px; margin-bottom:10px; }
.story-main .story-title { font-family:'Cormorant Garamond',serif; font-size:22px; font-weight:700; color:#fff; line-height:1.3; margin-bottom:8px; }
.story-main .story-excerpt { font-size:13px; color:#c8a8a4; line-height:1.7; }
.story-side { background:rgba(255,255,255,0.04); border:1px solid rgba(192,57,43,0.15); border-radius:12px; overflow:hidden; cursor:pointer; transition:all 0.25s; display:block; }
.story-side:hover { border-color:rgba(192,57,43,0.4); }
.simg { height:130px; display:flex; align-items:center; justify-content:center; font-size:46px; }
.s1{background:linear-gradient(135deg,#2a1a3d,#3d2a5a);}
.s2{background:linear-gradient(135deg,#1a3d2a,#2a5a3d);}
.s3{background:linear-gradient(135deg,#3d2a1a,#5a3d2a);}
.sbody { padding:14px; }
.side-title { font-family:'Cormorant Garamond',serif; font-size:16px; font-weight:700; color:#fff; line-height:1.3; margin-bottom:5px; }
.side-meta { font-size:11px; color:#9a6058; }

/* HISTORY */
.history-section { background:#f9f2f0; }
.history-layout { display:grid; grid-template-columns:1fr 1fr; gap:40px; align-items:start; }
.timeline { position:relative; padding-left:36px; }
.timeline::before { content:''; position:absolute; left:14px; top:0; bottom:0; width:2px; background:linear-gradient(to bottom,var(--red),transparent); }
.tl-item { position:relative; padding-bottom:28px; }
.tl-dot { position:absolute; left:-30px; top:4px; width:14px; height:14px; border-radius:50%; background:var(--red); border:3px solid #f9f2f0; box-shadow:0 0 0 3px rgba(192,57,43,0.25); }
.tl-year { font-size:11px; font-weight:800; color:var(--red); letter-spacing:1px; margin-bottom:3px; }
.tl-title { font-family:'Cormorant Garamond',serif; font-size:18px; font-weight:700; margin-bottom:5px; color:var(--text); }
.tl-text { font-size:12px; color:var(--text-muted); line-height:1.7; }
.widget { background:#fff; border-radius:12px; border:1px solid var(--border); padding:18px; margin-bottom:16px; }
.widget-title { font-family:'Cormorant Garamond',serif; font-size:17px; font-weight:700; color:var(--text); padding-bottom:10px; border-bottom:2px solid var(--red); margin-bottom:14px; }
.trending-item { display:flex; gap:10px; align-items:flex-start; padding:9px 0; border-bottom:1px solid #f5eae8; }
.trending-item:last-child { border-bottom:none; }
.trending-num { font-size:20px; font-weight:900; color:#edddd9; font-family:'Cormorant Garamond',serif; min-width:24px; line-height:1; }
.trending-title { font-size:12px; font-weight:600; color:var(--text); line-height:1.4; }
.trending-meta { font-size:10px; color:var(--text-muted); margin-top:2px; }

/* FOLKTALES */
.folktales-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; }
.folk-card { background:#fff; border-radius:12px; border:1px solid var(--border); padding:20px 16px; text-align:center; cursor:pointer; transition:all 0.25s; display:block; }
.folk-card:hover { transform:translateY(-4px); box-shadow:0 10px 28px rgba(192,57,43,0.1); border-color:var(--red); }
.folk-icon { font-size:40px; margin-bottom:10px; }
.folk-title { font-family:'Cormorant Garamond',serif; font-size:15px; font-weight:700; color:var(--text); margin-bottom:4px; line-height:1.3; }
.folk-count { font-size:11px; color:var(--text-muted); }

/* EPISODES */
.episodes-section { background:var(--dark); }
.ep-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:18px; }
.ep-card { background:rgba(255,255,255,0.04); border:1px solid rgba(192,57,43,0.15); border-radius:12px; overflow:hidden; cursor:pointer; transition:all 0.25s; display:block; }
.ep-card:hover { border-color:rgba(192,57,43,0.4); transform:translateY(-4px); }
.ep-thumb { height:150px; display:flex; align-items:center; justify-content:center; font-size:52px; position:relative; }
.ep1{background:linear-gradient(135deg,#1a2a3d,#2a3d5a);}
.ep2{background:linear-gradient(135deg,#3d0f0a,#5a1a14);}
.ep3{background:linear-gradient(135deg,#1a3d2a,#2a5a3d);}
.ep-num { position:absolute; top:10px; right:10px; background:rgba(192,57,43,0.9); color:#fff; font-size:10px; font-weight:800; padding:3px 8px; border-radius:4px; }
.play-btn { position:absolute; width:40px; height:40px; border-radius:50%; background:rgba(255,255,255,0.15); border:2px solid rgba(255,255,255,0.4); display:flex; align-items:center; justify-content:center; font-size:14px; color:#fff; transition:all 0.2s; }
.ep-card:hover .play-btn { background:var(--red); border-color:var(--red); transform:scale(1.1); }
.ep-body { padding:14px; }
.ep-series { font-size:9px; font-weight:800; letter-spacing:1.5px; text-transform:uppercase; color:var(--gold); margin-bottom:5px; }
.ep-title { font-family:'Cormorant Garamond',serif; font-size:15px; font-weight:700; color:#fff; margin-bottom:7px; line-height:1.3; }
.ep-meta { font-size:10px; color:#9a6058; display:flex; gap:10px; flex-wrap:wrap; }

/* BLOG */
.blog-list { display:flex; flex-direction:column; gap:16px; }
.blog-item { background:#fff; border-radius:12px; border:1px solid var(--border); padding:16px; display:flex; gap:16px; align-items:flex-start; cursor:pointer; transition:all 0.2s; }
.blog-item:hover { border-color:var(--red); box-shadow:0 6px 20px rgba(192,57,43,0.1); }
.blog-thumb { width:80px; height:72px; border-radius:9px; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:28px; }
.bt1{background:linear-gradient(135deg,#fde8e6,#f1948a);}
.bt2{background:linear-gradient(135deg,#e8fdf0,#80e8a0);}
.bt3{background:linear-gradient(135deg,#f0e8fd,#c080e8);}
.blog-content { flex:1; min-width:0; }
.blog-tags { display:flex; gap:5px; margin-bottom:6px; flex-wrap:wrap; }
.tag { font-size:9px; font-weight:700; letter-spacing:1px; text-transform:uppercase; padding:3px 7px; border-radius:4px; background:var(--red-soft); color:var(--red); }
.blog-title { font-family:'Cormorant Garamond',serif; font-size:17px; font-weight:700; color:var(--text); margin-bottom:5px; line-height:1.3; }
.blog-excerpt { font-size:12px; color:var(--text-muted); line-height:1.6; margin-bottom:8px; }
.blog-footer { display:flex; align-items:center; justify-content:space-between; font-size:11px; color:var(--text-muted); flex-wrap:wrap; gap:6px; }
.blog-author { display:flex; align-items:center; gap:6px; }
.av { width:22px; height:22px; border-radius:50%; background:var(--red); display:flex; align-items:center; justify-content:center; font-size:10px; color:#fff; font-weight:700; flex-shrink:0; overflow:hidden; }
.av img { width:100%; height:100%; object-fit:cover; }
.read-more { color:var(--red); font-weight:700; font-size:12px; white-space:nowrap; }
.read-more:hover { text-decoration:underline; }

/* SUBMIT */
.submit-section { background:linear-gradient(135deg,var(--dark),var(--dark2)); color:#fff; text-align:center; padding:60px var(--pad); }
.submit-section h2 { font-family:'Cormorant Garamond',serif; font-size:34px; font-weight:700; margin-bottom:12px; }
.submit-section h2 em { color:var(--gold); font-style:italic; }
.submit-section p { color:#c8a8a4; font-size:14px; margin-bottom:28px; max-width:480px; margin-left:auto; margin-right:auto; line-height:1.7; }
.submit-types { display:flex; gap:10px; justify-content:center; flex-wrap:wrap; margin-bottom:28px; }
.submit-type { background:rgba(192,57,43,0.1); border:1px solid rgba(192,57,43,0.25); border-radius:10px; padding:14px 16px; cursor:pointer; transition:all 0.2s; display:flex; flex-direction:column; align-items:center; gap:5px; min-width:84px; color:#f1948a; }
.submit-type:hover { background:rgba(192,57,43,0.25); border-color:var(--red); transform:translateY(-2px); }
.st-icon { font-size:24px; }
.st-label { font-size:11px; font-weight:700; color:#f1948a; }
.btn-submit { background:var(--red); color:#fff; padding:13px 34px; border-radius:9px; font-size:14px; font-weight:800; border:none; cursor:pointer; font-family:'Nunito',sans-serif; transition:all 0.2s; display:inline-block; text-decoration:none; }
.btn-submit:hover { background:var(--red-light); transform:translateY(-2px); }

/* FOOTER */
footer { background:#0f0704; color:#b5a898; padding:44px var(--pad) 16px; }
.footer-inner { max-width:1200px; margin:0 auto; }
.footer-grid { display:grid; grid-template-columns:2fr 1fr 1fr 1fr; gap:36px; margin-bottom:36px; }
.f-logo { font-family:'Cormorant Garamond',serif; font-size:26px; font-weight:700; color:var(--red); margin-bottom:10px; }
.footer-brand p { font-size:12px; line-height:1.7; color:#8a7060; }
.footer-social { display:flex; gap:8px; margin-top:14px; flex-wrap:wrap; }
.social-btn { width:32px; height:32px; border-radius:7px; background:rgba(192,57,43,0.1); border:1px solid rgba(192,57,43,0.2); display:flex; align-items:center; justify-content:center; font-size:13px; cursor:pointer; transition:all 0.2s; color:var(--red); }
.social-btn:hover { background:var(--red); color:#fff; border-color:var(--red); }
.footer-col h4 { font-size:12px; font-weight:800; letter-spacing:1.5px; text-transform:uppercase; color:var(--red); margin-bottom:12px; }
.footer-col ul { list-style:none; display:flex; flex-direction:column; gap:7px; }
.footer-col ul a { font-size:12px; color:#7a6058; transition:color 0.2s; }
.footer-col ul a:hover { color:var(--red); }
.footer-bottom { border-top:1px solid rgba(192,57,43,0.1); padding-top:16px; display:flex; justify-content:space-between; font-size:11px; color:#4a3028; flex-wrap:wrap; gap:8px; }

/* FLASH */
.flash { max-width:1200px; margin:14px auto 0; padding:0 var(--pad); }
.flash-msg { background:#dff5e8; color:#1a6b3a; border:1px solid #80e8a0; border-radius:8px; padding:10px 14px; font-size:13px; font-weight:600; }
.flash-msg.err { background:#fde8e6; color:var(--red-deep); border-color:#f1948a; }

/* ARTICLE PAGE */
.reading-progress { position:fixed; top:0; left:0; height:3px; background:var(--red); width:0; z-index:300; transition:width 0.1s; }
.breadcrumb { max-width:1000px; margin:0 auto; padding:18px var(--pad) 4px; font-size:12px; color:var(--text-muted); }
.breadcrumb a { color:var(--text-mid); }
.breadcrumb a:hover { color:var(--red); }
.article-hero { max-width:1000px; margin:0 auto; padding:14px var(--pad) 0; }
.article-cover { height:340px; border-radius:14px; background:linear-gradient(135deg,#3d0f0a,#5a1a14); display:flex; align-items:center; justify-content:center; font-size:96px; margin-bottom:24px; background-size:cover; background-position:center; }
.article-header { max-width:680px; margin:0 auto; padding:0 var(--pad); }
.article-cat { display:inline-block; font-size:10px; font-weight:800; letter-spacing:2px; text-transform:uppercase; padding:4px 10px; border-radius:4px; margin-bottom:14px; color:#fff; background:var(--red); }
.article-title { font-family:'Cormorant Garamond',serif; font-size:38px; font-weight:700; line-height:1.18; margin-bottom:14px; color:var(--text); }
.article-meta { font-size:13px; color:var(--text-muted); display:flex; flex-wrap:wrap; gap:12px; align-items:center; margin-bottom:24px; }
.article-meta .author { display:flex; align-items:center; gap:8px; font-weight:600; color:var(--text-mid); }
.article-meta .author .avatar { width:30px; height:30px; font-size:12px; }
.tts-bar { background:linear-gradient(135deg,var(--red),var(--red-dark)); color:#fff; border-radius:10px; padding:10px 14px; display:flex; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:24px; }
.tts-bar button { background:rgba(255,255,255,0.18); border:1px solid rgba(255,255,255,0.3); color:#fff; padding:6px 12px; border-radius:6px; cursor:pointer; font-family:inherit; font-size:12px; font-weight:700; display:inline-flex; align-items:center; gap:5px; }
.tts-bar button:hover { background:rgba(255,255,255,0.3); }
.tts-bar select { background:rgba(0,0,0,0.25); border:1px solid rgba(255,255,255,0.25); color:#fff; padding:5px 8px; border-radius:6px; font-size:12px; font-family:inherit; }
.tts-bar .tts-title { font-size:12px; opacity:0.85; flex:1; min-width:120px; }
.tts-warn { font-size:11px; color:#fde8e6; }
.article-body { max-width:680px; margin:0 auto; padding:0 var(--pad); font-size:17px; line-height:1.8; color:var(--text); }
.article-body p { margin-bottom:18px; }
.article-body h2, .article-body h3 { font-family:'Cormorant Garamond',serif; margin-top:30px; margin-bottom:14px; color:var(--text); }
.article-body h2 { font-size:28px; }
.article-body h3 { font-size:22px; }
.article-body blockquote { border-left:3px solid var(--red); padding:8px 18px; margin:18px 0; color:var(--text-mid); font-style:italic; background:var(--red-soft); border-radius:0 8px 8px 0; }
.article-body a { color:var(--red); text-decoration:underline; }
.article-footer { max-width:680px; margin:32px auto 0; padding:0 var(--pad); }
.share-bar { display:flex; gap:8px; flex-wrap:wrap; padding:20px 0; border-top:1px solid var(--border); }
.share-bar a, .share-bar button { padding:8px 14px; border-radius:8px; background:var(--red-soft); border:1px solid var(--red-muted); color:var(--red); font-size:12px; font-weight:700; cursor:pointer; font-family:inherit; }
.share-bar a:hover, .share-bar button:hover { background:var(--red); color:#fff; }
.tag-list { display:flex; flex-wrap:wrap; gap:6px; margin:16px 0 24px; }
.tag-list a { padding:5px 10px; border-radius:30px; background:var(--red-soft); color:var(--red); font-size:11px; font-weight:700; border:1px solid var(--red-muted); }
.tag-list a:hover { background:var(--red); color:#fff; }
.author-bio { background:#fff; border:1px solid var(--border); border-radius:12px; padding:20px; display:flex; gap:14px; align-items:flex-start; margin-bottom:30px; }
.author-bio .avatar { width:54px; height:54px; font-size:18px; flex-shrink:0; }
.author-bio h4 { font-family:'Cormorant Garamond',serif; font-size:18px; font-weight:700; margin-bottom:4px; }
.author-bio p { font-size:13px; color:var(--text-muted); line-height:1.6; }

/* COMMENTS */
.comments { max-width:680px; margin:0 auto 40px; padding:0 var(--pad); }
.comment-form { background:#fff; border:1px solid var(--border); border-radius:12px; padding:16px; margin-bottom:24px; }
.comment-form textarea { width:100%; min-height:90px; padding:10px; border:1px solid var(--border); border-radius:8px; font-family:inherit; font-size:13px; resize:vertical; }
.comment-form textarea:focus { outline:none; border-color:var(--red); }
.comment-form button { margin-top:10px; }
.comment { border-top:1px solid var(--border); padding:14px 0; }
.comment-head { display:flex; gap:8px; align-items:center; margin-bottom:6px; font-size:12px; color:var(--text-muted); }
.comment-head strong { color:var(--text); }
.comment-body { font-size:14px; color:var(--text-mid); line-height:1.6; }

/* AUTH */
.auth-wrap { max-width:420px; margin:60px auto; padding:0 var(--pad); }
.auth-card { background:#fff; border:1px solid var(--border); border-radius:14px; padding:28px; box-shadow:0 6px 22px rgba(192,57,43,0.06); }
.auth-card h1 { font-family:'Cormorant Garamond',serif; font-size:30px; font-weight:700; margin-bottom:6px; color:var(--text); }
.auth-card .lead { font-size:13px; color:var(--text-muted); margin-bottom:22px; }
.field { margin-bottom:14px; }
.field label { display:block; font-size:12px; font-weight:700; color:var(--text-mid); margin-bottom:5px; letter-spacing:0.5px; text-transform:uppercase; }
.field input, .field select, .field textarea { width:100%; padding:10px 12px; border:1px solid var(--border); border-radius:8px; font-family:inherit; font-size:14px; color:var(--text); background:#fff; }
.field input:focus, .field select:focus, .field textarea:focus { outline:none; border-color:var(--red); }
.field .err { color:var(--red); font-size:12px; margin-top:4px; display:block; }
.btn-primary { display:inline-block; background:var(--red); color:#fff; border:none; padding:11px 22px; border-radius:9px; font-size:13px; font-weight:700; cursor:pointer; font-family:inherit; }
.btn-primary:hover { background:var(--red-light); }
.btn-block { display:block; width:100%; }
.auth-foot { font-size:13px; color:var(--text-muted); margin-top:14px; text-align:center; }
.auth-foot a { color:var(--red); font-weight:700; }

/* PAGE COMMON */
.page { max-width:1200px; margin:0 auto; padding:36px var(--pad); }
.page-title { font-family:'Cormorant Garamond',serif; font-size:34px; font-weight:700; margin-bottom:6px; color:var(--text); }
.page-sub { color:var(--text-muted); font-size:14px; margin-bottom:24px; }
.cat-hero { background:linear-gradient(135deg,var(--red),var(--red-dark)); color:#fff; padding:42px var(--pad); }
.cat-hero-inner { max-width:1200px; margin:0 auto; }
.cat-hero h1 { font-family:'Cormorant Garamond',serif; font-size:38px; font-weight:700; display:flex; align-items:center; gap:14px; }
.cat-hero p { color:#fde8e6; margin-top:6px; font-size:14px; }
.pagination-wrap { display:flex; justify-content:center; padding:24px 0; }
.pagination { display:flex; gap:6px; flex-wrap:wrap; }
.pagination a, .pagination span { padding:6px 12px; border-radius:8px; background:#fff; color:var(--text-mid); border:1px solid var(--border); font-size:13px; font-weight:700; }
.pagination a:hover { background:var(--red-soft); color:var(--red); }
.pagination .active span, .pagination span[aria-current] { background:var(--red); color:#fff; border-color:var(--red); }

/* RESPONSIVE */
@media (max-width:1024px) {
  .news-grid { grid-template-columns:1fr 1fr; }
  .stories-grid { grid-template-columns:1fr 1fr; }
  .stories-grid .story-main { grid-column:1/-1; }
  .ep-grid { grid-template-columns:1fr 1fr; }
  .footer-grid { grid-template-columns:1fr 1fr; }
  .history-layout { grid-template-columns:1fr; }
  .hero h1 { font-size:36px; }
}
@media (max-width:768px) {
  :root { --pad:16px; }
  .desktop-nav, .header-actions { display:none; }
  .hamburger { display:flex; }
  .mobile-menu { display:flex; }
  .header-inner { height:58px; }
  .topbar > span { display:none; }
  .search-tags { display:none; }
  .hero { padding:32px var(--pad) 28px; }
  .hero-inner { grid-template-columns:1fr; gap:24px; }
  .hero h1 { font-size:30px; }
  .hero p { font-size:13px; }
  .hero-btns { flex-direction:column; }
  .btn-hprimary, .btn-hghost { justify-content:center; text-align:center; }
  .hero-card-img { height:160px; font-size:52px; }
  .news-grid { grid-template-columns:1fr; }
  .stories-grid { grid-template-columns:1fr; }
  .story-main { grid-column:auto; }
  .story-main .story-img { height:180px; font-size:60px; }
  .history-layout { grid-template-columns:1fr; }
  .folktales-grid { grid-template-columns:1fr 1fr; }
  .ep-grid { grid-template-columns:1fr; }
  .blog-item { flex-direction:column; }
  .blog-thumb { width:100%; height:110px; }
  .blog-footer { flex-direction:column; align-items:flex-start; }
  .submit-section h2 { font-size:26px; }
  .submit-types { gap:8px; }
  .submit-type { min-width:76px; padding:12px 10px; }
  .footer-grid { grid-template-columns:1fr 1fr; gap:24px; }
  .footer-bottom { flex-direction:column; text-align:center; }
  .section { padding:36px var(--pad); }
  .sec-title { font-size:24px; }
  .article-title { font-size:28px; }
  .article-cover { height:200px; font-size:64px; }
}
@media (max-width:480px) {
  .hero h1 { font-size:26px; }
  .folktales-grid { grid-template-columns:1fr 1fr; }
  .footer-grid { grid-template-columns:1fr; }
  .submit-type { min-width:70px; }
  .topbar-right a:not(:last-child) { display:none; }
}

/* ─── ADVERTISEMENT SLOTS ─── */
.ad-slot { max-width:1200px; margin:24px auto; padding:0 var(--pad); display:block; }
.ad-slot.ad-article_top, .ad-slot.ad-article_middle, .ad-slot.ad-article_bottom { max-width:680px; }
.ad-slot.ad-sidebar { margin:0 0 16px; padding:0; }
.ad-slot img { border-radius:10px; overflow:hidden; }
.ad-label { font-size:10px; font-weight:700; color:var(--text-muted); letter-spacing:1.5px; text-transform:uppercase; margin-bottom:6px; padding-left:4px; }
.ad-video { position:relative; padding-bottom:56.25%; height:0; overflow:hidden; border-radius:12px; background:#000; }
.ad-video iframe, .ad-video video { position:absolute; top:0; left:0; width:100%; height:100%; border:0; border-radius:12px; }
.ad-html { border-radius:10px; overflow:hidden; }
@media (max-width:768px) { .ad-slot { margin:18px auto; } }

/* ─── DONATION CARD STYLES ─── */
.donate-section { background:linear-gradient(135deg,#fff8f6,#fdf0ee); padding:48px var(--pad); }
.donate-card { background:#fff; border:1px solid var(--border); border-radius:14px; padding:24px; max-width:1200px; margin:0 auto; box-shadow:0 4px 20px rgba(192,57,43,0.08); display:grid; grid-template-columns:1fr 1.5fr; gap:28px; align-items:center; }
.donate-card .donate-img { height:220px; border-radius:12px; background:linear-gradient(135deg,#c0392b,#7b241c); display:flex; align-items:center; justify-content:center; font-size:64px; color:#fff; background-size:cover; background-position:center; }
.donate-card h3 { font-family:'Cormorant Garamond',serif; font-size:26px; font-weight:700; color:var(--text); margin-bottom:8px; }
.donate-card .donate-cause { font-size:13px; color:var(--text-muted); line-height:1.7; margin-bottom:16px; }
.donate-progress { background:var(--red-soft); border-radius:99px; height:10px; overflow:hidden; margin:12px 0 6px; }
.donate-progress > div { background:linear-gradient(90deg,var(--red),var(--red-light)); height:100%; border-radius:99px; transition:width 0.4s; }
.donate-stats { display:flex; justify-content:space-between; font-size:12px; color:var(--text-mid); font-weight:700; margin-bottom:14px; }
.donate-stats strong { color:var(--red); font-size:15px; }
.donate-actions { display:flex; gap:10px; flex-wrap:wrap; }
@media (max-width:768px) { .donate-card { grid-template-columns:1fr; } }

/* Donate list page */
.donate-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:20px; }
@media (max-width:768px) { .donate-grid { grid-template-columns:1fr; } }
.donate-tile { background:#fff; border:1px solid var(--border); border-radius:12px; overflow:hidden; transition:all 0.2s; }
.donate-tile:hover { transform:translateY(-4px); box-shadow:0 14px 30px rgba(192,57,43,0.12); border-color:var(--red); }
.donate-tile .donate-tile-img { height:160px; background:linear-gradient(135deg,#c0392b,#7b241c); display:flex; align-items:center; justify-content:center; font-size:48px; color:#fff; background-size:cover; background-position:center; }
.donate-tile .donate-tile-body { padding:18px; }
.donate-tile h4 { font-family:'Cormorant Garamond',serif; font-size:20px; font-weight:700; color:var(--text); margin-bottom:6px; }

/* Donation form */
.donate-form { background:#fff; border:1px solid var(--border); border-radius:14px; padding:24px; box-shadow:0 4px 20px rgba(192,57,43,0.06); }
.donate-amounts { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:14px; }
.donate-amounts button { background:var(--red-soft); border:2px solid var(--border); color:var(--red); padding:10px 18px; border-radius:8px; font-weight:700; font-size:14px; cursor:pointer; font-family:inherit; }
.donate-amounts button.active, .donate-amounts button:hover { background:var(--red); color:#fff; border-color:var(--red); }
.pay-method-list { display:flex; flex-direction:column; gap:8px; margin-top:14px; }
.pay-method { padding:12px; border:1px solid var(--border); border-radius:9px; background:#fafafa; font-size:13px; }
.pay-method strong { color:var(--red); font-family:'Cormorant Garamond',serif; font-size:16px; }
</style>
@stack('head')
</head>
<body>

@if($gtmId)
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmId }}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
@endif

@auth
  @php $currentUser = auth()->user(); @endphp
@endauth

<div class="reading-progress" id="readingProgress"></div>

<div class="topbar">
  <span>📍 {{ \App\Models\SiteSetting::get('topbar_text', "Northeast India's Cultural Platform") }}</span>
  <div class="topbar-right">
    <span>{{ now()->format('D, M j Y') }}</span>
    @auth
      <form method="POST" action="{{ route('logout') }}" style="display:inline">@csrf<button type="submit" style="background:none;border:none;color:var(--gold);font-size:11px;cursor:pointer;font-family:inherit;margin-left:14px;">Sign out</button></form>
    @endauth
  </div>
</div>

<header>
  <div class="header-inner">
    @php
      $logoRaw = \App\Models\SiteSetting::get('site_logo');
    @endphp
    <a href="{{ route('home') }}" class="logo">
      @if($logoRaw)
        <img src="{{ str_starts_with($logoRaw, 'http') ? $logoRaw : asset('storage/' . $logoRaw) }}" alt="{{ \App\Models\SiteSetting::get('site_name', 'KukiTales') }}" style="height:42px;width:auto;">
      @else
        <div class="logo-mark">{{ substr(\App\Models\SiteSetting::get('site_name', 'KukiTales'), 0, 1) }}</div>
        <div class="logo-text">
          <span class="name">{{ \App\Models\SiteSetting::get('site_name', 'KukiTales') }}</span>
          <span class="sub">{{ \App\Models\SiteSetting::get('site_tagline', 'Voices of the Hills') }}</span>
        </div>
      @endif
    </a>

    <nav class="desktop-nav">
      <div class="nav-item"><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a></div>
      <div class="nav-item">
        <a href="#">News ▾</a>
        <div class="dropdown">
          @foreach(\App\Models\Category::where('type','news')->orderBy('sort_order')->limit(8)->get() as $c)
            <a href="{{ route('categories.show', $c->slug) }}"><span>{{ $c->icon }}</span> {{ $c->name }}</a>
          @endforeach
        </div>
      </div>
      <div class="nav-item">
        <a href="#">Stories ▾</a>
        <div class="dropdown">
          <a href="{{ route('categories.show', 'short-stories') }}"><span>📖</span> Short Stories</a>
          <a href="{{ route('categories.show', 'episode-stories') }}"><span>🎭</span> Episode Stories</a>
          <a href="{{ route('categories.show', 'folktales') }}"><span>🌿</span> Folktales</a>
        </div>
      </div>
      <div class="nav-item">
        <a href="#">History ▾</a>
        <div class="dropdown">
          <a href="{{ route('categories.show', 'kuki-history') }}"><span>🏛️</span> Kuki History</a>
        </div>
      </div>
      <div class="nav-item">
        <a href="#">Culture ▾</a>
        <div class="dropdown">
          <a href="{{ route('categories.show', 'music-songs') }}"><span>🎵</span> Music & Songs</a>
          <a href="{{ route('categories.show', 'gallery') }}"><span>🖼️</span> Gallery</a>
          <a href="{{ route('categories.show', 'folktales') }}"><span>🌿</span> Folktales</a>
        </div>
      </div>
      <div class="nav-item"><a href="{{ route('categories.show', 'blog') }}">Blog</a></div>
      <div class="nav-item"><a href="{{ route('donate.index') }}" style="color:var(--red);">❤️ Donate</a></div>
    </nav>

    <div class="header-actions">
      @auth
        <a href="{{ route('submit.create') }}" class="btn btn-outline">✏️ Submit</a>
        <a href="{{ route('user.dashboard') }}" class="btn btn-solid">Dashboard</a>
      @else
        <a href="{{ route('register') }}" class="btn btn-outline">✏️ Submit</a>
        <a href="{{ route('login') }}" class="btn btn-solid">Login</a>
      @endauth
    </div>

    <button class="hamburger" id="hamburger" aria-label="Menu" onclick="toggleMenu()">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>

<nav class="mobile-menu" id="mobileMenu">
  <div class="mob-nav-item"><a href="{{ route('home') }}" class="mob-nav-link">🏠 Home</a></div>
  <div class="mob-nav-item">
    <div class="mob-nav-link" onclick="toggleSub(this)">📰 News <span>▾</span></div>
    <div class="mob-sub">
      @foreach(\App\Models\Category::where('type','news')->orderBy('sort_order')->limit(8)->get() as $c)
        <a href="{{ route('categories.show', $c->slug) }}">{{ $c->icon }} {{ $c->name }}</a>
      @endforeach
    </div>
  </div>
  <div class="mob-nav-item">
    <div class="mob-nav-link" onclick="toggleSub(this)">📖 Stories <span>▾</span></div>
    <div class="mob-sub">
      <a href="{{ route('categories.show', 'short-stories') }}">📖 Short Stories</a>
      <a href="{{ route('categories.show', 'episode-stories') }}">🎭 Episode Stories</a>
      <a href="{{ route('categories.show', 'folktales') }}">🌿 Folktales</a>
    </div>
  </div>
  <div class="mob-nav-item">
    <div class="mob-nav-link" onclick="toggleSub(this)">🏛️ History <span>▾</span></div>
    <div class="mob-sub">
      <a href="{{ route('categories.show', 'kuki-history') }}">🏛️ Kuki History</a>
    </div>
  </div>
  <div class="mob-nav-item">
    <div class="mob-nav-link" onclick="toggleSub(this)">🎵 Culture <span>▾</span></div>
    <div class="mob-sub">
      <a href="{{ route('categories.show', 'music-songs') }}">🎵 Music & Songs</a>
      <a href="{{ route('categories.show', 'gallery') }}">🖼️ Gallery</a>
    </div>
  </div>
  <div class="mob-nav-item"><a href="{{ route('categories.show', 'blog') }}" class="mob-nav-link">✍️ Blog</a></div>
  <div class="mob-nav-item"><a href="{{ route('donate.index') }}" class="mob-nav-link" style="color:var(--red);">❤️ Donate</a></div>
  <div class="mob-actions">
    @auth
      <a href="{{ route('submit.create') }}" class="btn btn-outline">✏️ Submit a Story</a>
      <a href="{{ route('user.dashboard') }}" class="btn btn-solid">Dashboard</a>
    @else
      <a href="{{ route('register') }}" class="btn btn-outline">Register</a>
      <a href="{{ route('login') }}" class="btn btn-solid">Login</a>
    @endauth
  </div>
</nav>

<div class="search-bar">
  <form method="GET" action="{{ route('search') }}">
    <div class="search-input">
      <span>🔍</span>
      <input type="text" name="q" value="{{ request('q') }}" placeholder="Search news, stories, history, folktales...">
    </div>
    <div class="search-tags">
      <a class="stag" href="{{ route('categories.show', 'folktales') }}">Folktales</a>
      <a class="stag" href="{{ route('categories.show', 'kuki-history') }}">Anglo-Kuki War</a>
      <a class="stag" href="{{ route('categories.show', 'short-stories') }}">Stories</a>
      <a class="stag" href="{{ route('categories.show', 'music-songs') }}">Songs</a>
    </div>
  </form>
</div>

@if(session('success'))
  <div class="flash"><div class="flash-msg">{{ session('success') }}</div></div>
@endif
@if(session('error'))
  <div class="flash"><div class="flash-msg err">{{ session('error') }}</div></div>
@endif

@yield('content')

<footer>
  <div class="footer-inner">
    <div class="footer-grid">
      <div class="footer-brand">
        <div class="f-logo">{{ \App\Models\SiteSetting::get('site_name', 'KukiTales') }}</div>
        <p>{{ \App\Models\SiteSetting::get('footer_about', \App\Models\SiteSetting::get('site_description', "Northeast India's premier cultural platform — preserving Kuki stories, history, folktales, and traditions.")) }}</p>

        @php
          $socials = [
            ['key' => 'social_facebook',  'label' => 'f', 'name' => 'Facebook'],
            ['key' => 'social_twitter',   'label' => '𝕏', 'name' => 'Twitter / X'],
            ['key' => 'social_instagram', 'label' => '◯', 'name' => 'Instagram'],
            ['key' => 'social_youtube',   'label' => '▶', 'name' => 'YouTube'],
            ['key' => 'social_linkedin',  'label' => 'in','name' => 'LinkedIn'],
            ['key' => 'social_whatsapp',  'label' => '✆', 'name' => 'WhatsApp'],
          ];
        @endphp
        <div class="footer-social">
          @foreach($socials as $s)
            @if($url = \App\Models\SiteSetting::get($s['key']))
              <a href="{{ $url }}" class="social-btn" target="_blank" rel="noopener" aria-label="{{ $s['name'] }}">{{ $s['label'] }}</a>
            @endif
          @endforeach
        </div>

        @php
          $iosUrl = \App\Models\SiteSetting::get('app_store_url');
          $androidUrl = \App\Models\SiteSetting::get('play_store_url');
        @endphp
        @if($iosUrl || $androidUrl)
          <div style="margin-top:18px;display:flex;gap:8px;flex-wrap:wrap;">
            @if($iosUrl)
              <a href="{{ $iosUrl }}" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;gap:8px;background:#000;color:#fff;border-radius:8px;padding:8px 12px;font-size:11px;text-decoration:none;">
                <span style="font-size:18px;">🍎</span>
                <span style="line-height:1.1;">
                  <span style="font-size:9px;opacity:0.8;display:block;">Download on the</span>
                  <strong style="font-size:13px;">App Store</strong>
                </span>
              </a>
            @endif
            @if($androidUrl)
              <a href="{{ $androidUrl }}" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;gap:8px;background:#000;color:#fff;border-radius:8px;padding:8px 12px;font-size:11px;text-decoration:none;">
                <span style="font-size:18px;">▶</span>
                <span style="line-height:1.1;">
                  <span style="font-size:9px;opacity:0.8;display:block;">GET IT ON</span>
                  <strong style="font-size:13px;">Google Play</strong>
                </span>
              </a>
            @endif
          </div>
        @endif
      </div>

      <div class="footer-col">
        <h4>Content</h4>
        <ul>
          <li><a href="{{ route('categories.show', 'short-stories') }}">Stories</a></li>
          <li><a href="{{ route('categories.show', 'episode-stories') }}">Episodes</a></li>
          <li><a href="{{ route('categories.show', 'kuki-history') }}">History</a></li>
          <li><a href="{{ route('categories.show', 'folktales') }}">Folktales</a></li>
          <li><a href="{{ route('categories.show', 'music-songs') }}">Music</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Community</h4>
        <ul>
          <li><a href="{{ auth()->check() ? route('submit.create') : route('register') }}">Submit a story</a></li>
          <li><a href="{{ route('donate.index') }}">❤️ Donate</a></li>
          <li><a href="{{ route('register') }}">Register</a></li>
          <li><a href="{{ route('login') }}">Sign in</a></li>
          <li><a href="{{ route('seo.rss') }}" target="_blank">RSS feed</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Contact</h4>
        <ul>
          @if($email = \App\Models\SiteSetting::get('contact_email'))<li><a href="mailto:{{ $email }}">{{ $email }}</a></li>@endif
          @if($phone = \App\Models\SiteSetting::get('contact_phone'))<li><a href="tel:{{ $phone }}">{{ $phone }}</a></li>@endif
          @if($addr = \App\Models\SiteSetting::get('contact_address'))<li style="white-space:pre-line;">{{ $addr }}</li>@endif
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© {{ date('Y') }} {{ \App\Models\SiteSetting::get('site_name', 'KukiTales') }}. {{ \App\Models\SiteSetting::get('footer_text', 'All rights reserved.') }}</span>
      <span>Manipur · Mizoram · Nagaland · Assam · Tripura</span>
    </div>
  </div>
</footer>

<script>
function toggleMenu() {
  document.getElementById('hamburger').classList.toggle('open');
  document.getElementById('mobileMenu').classList.toggle('open');
}
function toggleSub(el) {
  el.nextElementSibling.classList.toggle('open');
}
(function () {
  const bar = document.getElementById('readingProgress');
  if (!bar) return;
  function update() {
    const h = document.documentElement;
    const max = h.scrollHeight - h.clientHeight;
    if (max <= 0) { bar.style.width = '0'; return; }
    const pct = (h.scrollTop / max) * 100;
    bar.style.width = pct + '%';
  }
  window.addEventListener('scroll', update, { passive: true });
  update();
})();
</script>
@stack('scripts')
</body>
</html>
