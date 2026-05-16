@extends('layouts.app')

@section('content')

{{-- HERO --}}
<section class="hero">
  <div class="hero-inner">
    <div>
      <div class="hero-badge">⛰ Northeast India's Cultural Hub</div>
      <h1>Voices of the <em>Hills</em> — stories, history, and the songs we keep alive.</h1>
      <p>KukiTales gathers and preserves the literature of the Kuki people: folktales whispered by elders, the long histories of our chiefs, episode stories from new and established storytellers, and reporting from the hills.</p>
      <div class="hero-btns">
        <a href="{{ route('categories.show', 'short-stories') }}" class="btn-hprimary">📖 Read Stories</a>
        <a href="{{ route('categories.show', 'folktales') }}" class="btn-hghost">🌿 Browse Folktales</a>
      </div>
    </div>
    @if($featured)
      <a href="{{ $featured->url }}" class="hero-card">
        <div class="hero-card-img" @if($featured->cover_url) style="background-image:url('{{ $featured->cover_url }}')" @endif>
          @if(!$featured->cover_url){{ $featured->type === 'history' ? '🏛️' : '📖' }}@endif
          <span class="feat-badge">Featured</span>
        </div>
        <div class="hero-card-body">
          <div class="hc-cat">{{ ucfirst($featured->type) }} · {{ optional($featured->category)->name }}</div>
          <div class="hc-title">{{ $featured->title }}</div>
          <div class="hc-meta">
            <span>By {{ optional($featured->user)->name }}</span>
            <span>{{ $featured->read_time }} min read</span>
            <span>{{ optional($featured->published_at)->diffForHumans() }}</span>
          </div>
        </div>
      </a>
    @endif
  </div>
</section>

@include('partials.ads', ['placement' => 'homepage_hero', 'limit' => 1])

{{-- TICKER --}}
@php
  $tickerEnabled = (bool) \App\Models\SiteSetting::get('breaking_news_enabled', '1');
  $tickerHeadlines = $breaking->isNotEmpty()
    ? $breaking
    : collect(array_filter(array_map('trim', explode('|', \App\Models\SiteSetting::get('breaking_news_text', '')))));
@endphp
@if($tickerEnabled && $tickerHeadlines->isNotEmpty())
<div class="ticker">
  <div class="ticker-label">⚡ Breaking</div>
  <div class="ticker-track">
    <div class="ticker-inner">
      @foreach($tickerHeadlines as $headline)<span>{{ $headline }}</span>@endforeach
      @foreach($tickerHeadlines as $headline)<span>{{ $headline }}</span>@endforeach
    </div>
  </div>
</div>
@endif

{{-- FEATURED STORIES --}}
@if($stories->isNotEmpty())
<section class="section stories-section">
  <div class="section-inner">
    <div class="sec-header">
      <h2 class="sec-title white">Featured Stories</h2>
      <a class="sec-link light" href="{{ route('categories.show', 'short-stories') }}">View All →</a>
    </div>
    <div class="stories-grid">
      @php($main = $stories->first())
      @php($sides = $stories->skip(1)->take(2))
      <a href="{{ $main->url }}" class="story-main">
        <div class="story-img" @if($main->cover_url) style="background-image:url('{{ $main->cover_url }}');background-size:cover;background-position:center" @endif>
          @if(!$main->cover_url)📖@endif
        </div>
        <div class="story-body">
          <span class="story-tag">{{ optional($main->category)->name ?: 'Story' }}</span>
          <h3 class="story-title">{{ $main->title }}</h3>
          <p class="story-excerpt">{{ $main->short_excerpt }}</p>
        </div>
      </a>
      @foreach($sides as $i => $story)
        <a href="{{ $story->url }}" class="story-side">
          <div class="simg s{{ ($i % 3) + 1 }}">{{ $i === 0 ? '🌙' : '🌿' }}</div>
          <div class="sbody">
            <div class="side-title">{{ $story->title }}</div>
            <div class="side-meta">{{ $story->read_time }} min · {{ optional($story->published_at)->diffForHumans() }}</div>
          </div>
        </a>
      @endforeach
    </div>
  </div>
</section>
@endif

{{-- EPISODE STORIES --}}
@if($seriesList->isNotEmpty())
<section class="section episodes-section">
  <div class="section-inner">
    <div class="sec-header">
      <h2 class="sec-title white">Episode Stories</h2>
      <a class="sec-link light" href="{{ route('categories.show', 'episode-stories') }}">View All →</a>
    </div>
    <div class="ep-grid">
      @foreach($seriesList as $i => $series)
        @php($latest = $series->posts->first())
        <a href="{{ route('series.show', $series->slug) }}" class="ep-card">
          <div class="ep-thumb ep{{ ($i % 3) + 1 }}">
            🎭
            <span class="ep-num">Ep {{ $latest?->episode_number ?? '—' }}</span>
            <span class="play-btn">▶</span>
          </div>
          <div class="ep-body">
            <div class="ep-series">{{ $series->title }}</div>
            <div class="ep-title">{{ $latest?->title ?? 'New episodes coming soon' }}</div>
            <div class="ep-meta">
              <span>{{ $series->posts_count ?? $series->posts->count() }} episodes</span>
              <span>{{ ucfirst($series->status) }}</span>
            </div>
          </div>
        </a>
      @endforeach
    </div>
  </div>
</section>
@endif

{{-- KUKI HISTORY --}}
@if($history->isNotEmpty())
<section class="section history-section">
  <div class="section-inner">
    <div class="sec-header">
      <h2 class="sec-title">Kuki History</h2>
      <a class="sec-link" href="{{ route('categories.show', 'kuki-history') }}">View All →</a>
    </div>
    <div class="history-layout">
      <div class="timeline">
        @foreach($history as $h)
          <div class="tl-item">
            <span class="tl-dot"></span>
            @if($h->year_era)<div class="tl-year">{{ $h->year_era }}</div>@endif
            <h3 class="tl-title"><a href="{{ $h->url }}">{{ $h->title }}</a></h3>
            <p class="tl-text">{{ $h->short_excerpt }}</p>
          </div>
        @endforeach
      </div>
      <aside>
        <div class="widget">
          <div class="widget-title">Most Read</div>
          @foreach(\App\Models\Post::published()->orderByDesc('view_count')->limit(5)->get() as $i => $trend)
            <div class="trending-item">
              <span class="trending-num">{{ str_pad($i+1, 2, '0', STR_PAD_LEFT) }}</span>
              <div>
                <div class="trending-title"><a href="{{ $trend->url }}">{{ $trend->title }}</a></div>
                <div class="trending-meta">{{ number_format($trend->view_count) }} views · {{ ucfirst($trend->type) }}</div>
              </div>
            </div>
          @endforeach
        </div>
      </aside>
    </div>
  </div>
</section>
@endif

@include('partials.ads', ['placement' => 'homepage_middle', 'limit' => 1])

{{-- FOLKTALES --}}
@if($folktaleCategories->isNotEmpty())
<section class="section">
  <div class="section-inner">
    <div class="sec-header">
      <h2 class="sec-title">Folktales</h2>
      <a class="sec-link" href="{{ route('categories.show', 'folktales') }}">View All →</a>
    </div>
    <div class="folktales-grid">
      @foreach($folktaleCategories as $f)
        <a href="{{ route('categories.show', $f->slug) }}" class="folk-card">
          <div class="folk-icon">{{ $f->icon }}</div>
          <div class="folk-title">{{ $f->name }}</div>
          <div class="folk-count">{{ $f->posts()->count() }} {{ Str::plural('tale', $f->posts()->count()) }}</div>
        </a>
      @endforeach
    </div>
  </div>
</section>
@endif

{{-- LATEST NEWS --}}
@if($news->isNotEmpty())
<section class="section" style="background:var(--red-soft);">
  <div class="section-inner">
    <div class="sec-header">
      <h2 class="sec-title">Latest News</h2>
      <a class="sec-link" href="{{ route('categories.show', 'community-news') }}">View All →</a>
    </div>
    <div class="cat-pills">
      <span class="pill active">All</span>
      @foreach($newsCategories->take(5) as $nc)
        <a class="pill" href="{{ route('categories.show', $nc->slug) }}">{{ $nc->name }}</a>
      @endforeach
    </div>
    <div class="news-grid">
      @foreach($news->take(6) as $i => $n)
        <a href="{{ $n->url }}" class="news-card">
          <div class="card-thumb t{{ ($i % 6) + 1 }}" @if($n->cover_url) style="background-image:url('{{ $n->cover_url }}')" @endif>
            @if(!$n->cover_url)📰@endif
            <span class="card-cat cat-{{ $n->type }}">{{ $n->type }}</span>
          </div>
          <div class="card-body">
            <h3 class="card-title">{{ $n->title }}</h3>
            <p class="card-excerpt">{{ \Illuminate\Support\Str::limit($n->short_excerpt, 110) }}</p>
            <div class="card-meta">
              <div class="card-author">
                <span class="avatar">{{ strtoupper(substr(optional($n->user)->name ?? '?', 0, 1)) }}</span>
                <span>{{ optional($n->user)->name }}</span>
              </div>
              <span>{{ optional($n->published_at)->diffForHumans() }}</span>
            </div>
          </div>
        </a>
      @endforeach
    </div>
  </div>
</section>
@endif

{{-- BLOG --}}
@if($blogs->isNotEmpty())
<section class="section">
  <div class="section-inner">
    <div class="sec-header">
      <h2 class="sec-title">From the Blog</h2>
      <a class="sec-link" href="{{ route('categories.show', 'blog') }}">View All →</a>
    </div>
    <div class="blog-list">
      @foreach($blogs as $i => $b)
        <a class="blog-item" href="{{ $b->url }}">
          <div class="blog-thumb bt{{ ($i % 3) + 1 }}">✍️</div>
          <div class="blog-content">
            <div class="blog-tags">
              @foreach($b->tags->take(3) as $tg)<span class="tag">{{ $tg->name }}</span>@endforeach
            </div>
            <h3 class="blog-title">{{ $b->title }}</h3>
            <p class="blog-excerpt">{{ \Illuminate\Support\Str::limit($b->short_excerpt, 160) }}</p>
            <div class="blog-footer">
              <div class="blog-author">
                <span class="av">{{ strtoupper(substr(optional($b->user)->name ?? '?', 0, 1)) }}</span>
                <span>{{ optional($b->user)->name }} · {{ optional($b->published_at)->diffForHumans() }}</span>
              </div>
              <span class="read-more">Read More →</span>
            </div>
          </div>
        </a>
      @endforeach
    </div>
  </div>
</section>
@endif

@include('partials.ads', ['placement' => 'homepage_bottom', 'limit' => 1])

{{-- DONATION CTA --}}
@php($featuredCampaign = \App\Models\DonationCampaign::active()->featured()->first() ?? \App\Models\DonationCampaign::active()->first())
@if($featuredCampaign)
<section class="donate-section">
  <div class="sec-header" style="max-width:1200px;margin:0 auto 20px;">
    <h2 class="sec-title">Support a Cause</h2>
    <a class="sec-link" href="{{ route('donate.index') }}">All Campaigns →</a>
  </div>
  <div class="donate-card">
    <div class="donate-img" @if($featuredCampaign->cover_image) style="background-image:url('{{ $featuredCampaign->cover_url }}')" @endif>
      @if(!$featuredCampaign->cover_image)❤️@endif
    </div>
    <div>
      <h3>{{ $featuredCampaign->title }}</h3>
      <p class="donate-cause">{{ \Illuminate\Support\Str::limit(strip_tags($featuredCampaign->description), 220) }}</p>
      @if($featuredCampaign->goal_amount)
        <div class="donate-progress"><div style="width:{{ $featuredCampaign->progress_percent }}%"></div></div>
        <div class="donate-stats">
          <span><strong>{{ $featuredCampaign->currency }} {{ number_format($featuredCampaign->raised_amount, 0) }}</strong> raised</span>
          <span>of {{ $featuredCampaign->currency }} {{ number_format($featuredCampaign->goal_amount, 0) }} · {{ $featuredCampaign->progress_percent }}%</span>
        </div>
      @endif
      <div class="donate-actions">
        <a href="{{ route('donate.show', $featuredCampaign->slug) }}" class="btn-submit">Donate now →</a>
        <a href="{{ route('donate.show', $featuredCampaign->slug) }}" class="btn-hghost" style="border-color:var(--red);color:var(--red);">Learn more</a>
      </div>
    </div>
  </div>
</section>
@endif

{{-- SUBMIT CTA --}}
<section class="submit-section">
  <h2>Have a story to <em>share?</em></h2>
  <p>Become part of KukiTales. Submit stories, folktales, history, or news — our editors review every submission with care.</p>
  <div class="submit-types">
    @foreach([['📖','Story','story'],['🎭','Episode','episode'],['🏛️','History','history'],['🌿','Folktale','folktale'],['📰','News','news'],['✍️','Blog','blog'],['🎵','Music','music'],['🖼️','Gallery','gallery']] as [$icon, $label, $type])
      <a href="{{ auth()->check() ? route('submit.create') . '?type=' . $type : route('register') }}" class="submit-type">
        <span class="st-icon">{{ $icon }}</span>
        <span class="st-label">{{ $label }}</span>
      </a>
    @endforeach
  </div>
  <a href="{{ auth()->check() ? route('submit.create') : route('register') }}" class="btn-submit">Start Submitting →</a>
</section>

@endsection
