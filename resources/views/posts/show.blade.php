@extends('layouts.app')

@section('title', $post->seo_title)
@section('meta_description', $post->seo_description)
@section('canonical', $post->canonical)
@section('og_image', $post->og_image_url ?? '')
@section('og_type', in_array($post->type, ['news','story','history','folktale','blog']) ? 'article' : 'website')
@if($post->noindex)@section('noindex', '1')@endif

@push('jsonld')
@php
  $siteName = \App\Models\SiteSetting::get('site_name', 'KukiTales');
  $publisherLogoRaw = \App\Models\SiteSetting::get('publisher_logo');
  $publisherLogoUrl = $publisherLogoRaw
    ? (str_starts_with($publisherLogoRaw, 'http') ? $publisherLogoRaw : asset('storage/' . $publisherLogoRaw))
    : null;
  $articleType = $post->type === 'news' ? 'NewsArticle' : 'Article';
  $articleSchema = [
    '@context' => 'https://schema.org',
    '@type' => $articleType,
    'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $post->canonical],
    'headline' => \Illuminate\Support\Str::limit($post->title, 110, ''),
    'description' => $post->seo_description,
    'datePublished' => optional($post->published_at)->toAtomString(),
    'dateModified' => $post->updated_at->toAtomString(),
    'author' => [
      '@type' => 'Person',
      'name' => optional($post->user)->name ?? 'KukiTales',
      'url' => $post->user ? route('authors.show', $post->user_id) : null,
    ],
    'publisher' => [
      '@type' => 'NewsMediaOrganization',
      'name' => $siteName,
      'logo' => [
        '@type' => 'ImageObject',
        'url' => $publisherLogoUrl ?: ($post->og_image_url ?: url('/')),
      ],
    ],
    'articleSection' => optional($post->category)->name,
    'keywords' => $post->tags->pluck('name')->merge([$post->focus_keyword])->filter()->values()->all(),
    'wordCount' => str_word_count(strip_tags($post->content)),
  ];
  if ($post->og_image_url) {
    $articleSchema['image'] = [$post->og_image_url];
  }
  $breadcrumbItems = [
    ['@type'=>'ListItem','position'=>1,'name'=>'Home','item'=>url('/')],
  ];
  if ($post->category) {
    $breadcrumbItems[] = ['@type'=>'ListItem','position'=>2,'name'=>$post->category->name,'item'=>route('categories.show', $post->category->slug)];
  }
  $breadcrumbItems[] = ['@type'=>'ListItem','position'=>count($breadcrumbItems)+1,'name'=>$post->title,'item'=>$post->canonical];
  $breadcrumbSchema = ['@context'=>'https://schema.org','@type'=>'BreadcrumbList','itemListElement'=>$breadcrumbItems];
@endphp
<script type="application/ld+json">{!! json_encode($articleSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
<script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')

<div class="breadcrumb">
  <a href="{{ route('home') }}">Home</a> ›
  @if($post->category)<a href="{{ route('categories.show', $post->category->slug) }}">{{ $post->category->name }}</a> ›@endif
  <span>{{ \Illuminate\Support\Str::limit($post->title, 60) }}</span>
</div>

<div class="article-hero">
  <div class="article-cover" @if($post->cover_url) style="background-image:url('{{ $post->cover_url }}')" @endif>
    @if(!$post->cover_url){{ ['story'=>'📖','history'=>'🏛️','folktale'=>'🌿','news'=>'📰','blog'=>'✍️','music'=>'🎵','gallery'=>'🖼️','episode'=>'🎭'][$post->type] ?? '📖' }}@endif
  </div>
</div>

<div class="article-header">
  <span class="article-cat cat-{{ $post->type }}">{{ optional($post->category)->name ?: ucfirst($post->type) }}</span>
  <h1 class="article-title">{{ $post->title }}</h1>
  <div class="article-meta">
    <span class="author">
      <span class="avatar">{{ strtoupper(substr(optional($post->user)->name ?? '?', 0, 1)) }}</span>
      <a href="{{ route('authors.show', $post->user_id) }}">{{ optional($post->user)->name }}</a>
    </span>
    <span>· {{ optional($post->published_at)->format('M j, Y') }}</span>
    <span>· {{ $post->read_time }} min read</span>
    <span>· {{ number_format($post->view_count) }} views</span>
    @if($post->series)<span>· <a href="{{ route('series.show', $post->series->slug) }}" style="color:var(--red)">Series: {{ $post->series->title }}</a></span>@endif
  </div>

  @if($post->allow_tts)
    <div class="tts-bar" id="ttsBar">
      <button type="button" id="ttsToggle">▶ Listen</button>
      <button type="button" id="ttsStop">■ Stop</button>
      <select id="ttsSpeed" title="Playback speed">
        <option value="0.75">0.75×</option>
        <option value="1" selected>1×</option>
        <option value="1.25">1.25×</option>
        <option value="1.5">1.5×</option>
        <option value="2">2×</option>
      </select>
      <span class="tts-title" id="ttsStatus">Text-to-speech ready</span>
      <noscript><span class="tts-warn">Enable JavaScript to use audio playback.</span></noscript>
    </div>
  @endif
</div>

@include('partials.ads', ['placement' => 'article_top', 'limit' => 1])

<article class="article-body" id="article-body">
  {!! $post->content !!}
</article>

@include('partials.ads', ['placement' => 'article_bottom', 'limit' => 1])

<div class="article-footer">
  @if($post->tags->isNotEmpty())
    <div class="tag-list">
      @foreach($post->tags as $t)<a href="{{ route('search', ['q' => $t->name]) }}">#{{ $t->name }}</a>@endforeach
    </div>
  @endif

  <div class="share-bar">
    @php($url = url()->current())
    @php($title = urlencode($post->title))
    <a href="https://wa.me/?text={{ $title }}%20{{ urlencode($url) }}" target="_blank" rel="noopener">WhatsApp</a>
    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($url) }}" target="_blank" rel="noopener">Facebook</a>
    <a href="https://twitter.com/intent/tweet?text={{ $title }}&url={{ urlencode($url) }}" target="_blank" rel="noopener">Twitter / X</a>
    <button type="button" onclick="navigator.clipboard.writeText('{{ $url }}').then(()=>this.innerText='Copied!')">Copy Link</button>
    @auth
      <form method="POST" action="{{ route('bookmarks.toggle', $post) }}" style="display:inline">
        @csrf
        <button type="submit">🔖 Bookmark</button>
      </form>
    @endauth
  </div>

  @if($post->user)
    <div class="author-bio">
      <span class="avatar">{{ strtoupper(substr($post->user->name, 0, 1)) }}</span>
      <div>
        <h4><a href="{{ route('authors.show', $post->user_id) }}">{{ $post->user->name }}</a></h4>
        <p>{{ $post->user->bio ?: 'A contributor to KukiTales.' }}</p>
      </div>
    </div>
  @endif

  @if($related->isNotEmpty())
    <h3 style="font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:700;margin:18px 0 14px;color:var(--text);">Related</h3>
    <div class="news-grid">
      @foreach($related as $i => $r)
        <a href="{{ $r->url }}" class="news-card">
          <div class="card-thumb t{{ ($i % 6) + 1 }}" @if($r->cover_url) style="background-image:url('{{ $r->cover_url }}')" @endif>
            @if(!$r->cover_url){{ ['story'=>'📖','history'=>'🏛️','folktale'=>'🌿','news'=>'📰','blog'=>'✍️','music'=>'🎵','gallery'=>'🖼️','episode'=>'🎭'][$r->type] ?? '📖' }}@endif
            <span class="card-cat cat-{{ $r->type }}">{{ $r->type }}</span>
          </div>
          <div class="card-body">
            <h3 class="card-title">{{ $r->title }}</h3>
            <p class="card-excerpt">{{ \Illuminate\Support\Str::limit($r->short_excerpt, 90) }}</p>
          </div>
        </a>
      @endforeach
    </div>
  @endif
</div>

{{-- COMMENTS --}}
<section class="comments">
  <h3 style="font-family:'Cormorant Garamond',serif;font-size:22px;font-weight:700;margin:24px 0 14px;color:var(--text);">Comments ({{ $comments->count() }})</h3>
  @auth
    <form method="POST" action="{{ route('comments.store') }}" class="comment-form">
      @csrf
      <input type="hidden" name="post_id" value="{{ $post->id }}">
      <textarea name="content" placeholder="Share your thoughts — comments are reviewed before publishing." required></textarea>
      @error('content')<span class="field"><span class="err">{{ $message }}</span></span>@enderror
      <button type="submit" class="btn-primary">Post Comment</button>
    </form>
  @else
    <p style="font-size:13px;color:var(--text-muted);margin-bottom:18px;"><a href="{{ route('login') }}" style="color:var(--red);font-weight:700;">Log in</a> to leave a comment.</p>
  @endauth

  @forelse($comments as $c)
    <div class="comment">
      <div class="comment-head">
        <span class="avatar">{{ strtoupper(substr(optional($c->user)->name ?? '?', 0, 1)) }}</span>
        <strong>{{ optional($c->user)->name }}</strong> · {{ $c->created_at->diffForHumans() }}
      </div>
      <div class="comment-body">{{ $c->content }}</div>
      @foreach($c->replies as $reply)
        <div class="comment" style="margin-left:24px;border-top:1px dashed var(--border);">
          <div class="comment-head">
            <span class="avatar">{{ strtoupper(substr(optional($reply->user)->name ?? '?', 0, 1)) }}</span>
            <strong>{{ optional($reply->user)->name }}</strong> · {{ $reply->created_at->diffForHumans() }}
          </div>
          <div class="comment-body">{{ $reply->content }}</div>
        </div>
      @endforeach
    </div>
  @empty
    <p style="font-size:13px;color:var(--text-muted);">No comments yet — be the first.</p>
  @endforelse
</section>

@push('scripts')
@if($post->allow_tts)
<script>
(function () {
  const toggle = document.getElementById('ttsToggle');
  const stop = document.getElementById('ttsStop');
  const speedSel = document.getElementById('ttsSpeed');
  const status = document.getElementById('ttsStatus');
  if (!toggle || !('speechSynthesis' in window)) {
    if (status) status.textContent = 'Audio playback not supported in this browser.';
    return;
  }
  let utter = null;
  let playing = false;

  function getText() {
    const el = document.getElementById('article-body');
    return (el ? el.innerText : '').replace(/\s+/g, ' ').trim();
  }

  function speak() {
    speechSynthesis.cancel();
    utter = new SpeechSynthesisUtterance(getText());
    utter.rate = parseFloat(speedSel.value);
    utter.onend = function () { playing = false; toggle.innerHTML = '▶ Listen'; status.textContent = 'Finished.'; };
    utter.onerror = function () { playing = false; toggle.innerHTML = '▶ Listen'; status.textContent = 'Playback error.'; };
    speechSynthesis.speak(utter);
    playing = true;
    toggle.innerHTML = '⏸ Pause';
    status.textContent = 'Playing…';
  }

  toggle.addEventListener('click', function () {
    if (!playing) {
      if (speechSynthesis.paused) {
        speechSynthesis.resume();
        playing = true;
        toggle.innerHTML = '⏸ Pause';
        status.textContent = 'Resumed.';
      } else {
        speak();
      }
    } else {
      speechSynthesis.pause();
      playing = false;
      toggle.innerHTML = '▶ Resume';
      status.textContent = 'Paused.';
    }
  });

  stop.addEventListener('click', function () {
    speechSynthesis.cancel();
    playing = false;
    toggle.innerHTML = '▶ Listen';
    status.textContent = 'Stopped.';
  });

  speedSel.addEventListener('change', function () {
    if (playing && utter) { speak(); }
  });
})();
</script>
@endif
@endpush
@endsection
