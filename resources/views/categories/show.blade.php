@extends('layouts.app')

@section('title', $category->name . ' — KukiTales')

@section('content')
<section class="cat-hero">
  <div class="cat-hero-inner">
    <h1>{{ $category->icon }} {{ $category->name }}</h1>
    @if($category->description)<p>{{ $category->description }}</p>@endif
  </div>
</section>

<section class="page">
  @if($posts->isEmpty())
    <p style="color:var(--text-muted);font-size:14px;">No published content here yet — check back soon.</p>
  @else
    <div class="news-grid">
      @foreach($posts as $i => $p)
        <a href="{{ $p->url }}" class="news-card">
          <div class="card-thumb t{{ ($i % 6) + 1 }}" @if($p->cover_url) style="background-image:url('{{ $p->cover_url }}')" @endif>
            @if(!$p->cover_url){{ ['story'=>'📖','history'=>'🏛️','folktale'=>'🌿','news'=>'📰','blog'=>'✍️','music'=>'🎵','gallery'=>'🖼️','episode'=>'🎭'][$p->type] ?? '📖' }}@endif
            <span class="card-cat cat-{{ $p->type }}">{{ $p->type }}</span>
          </div>
          <div class="card-body">
            <h3 class="card-title">{{ $p->title }}</h3>
            <p class="card-excerpt">{{ \Illuminate\Support\Str::limit($p->short_excerpt, 110) }}</p>
            <div class="card-meta">
              <div class="card-author">
                <span class="avatar">{{ strtoupper(substr(optional($p->user)->name ?? '?', 0, 1)) }}</span>
                <span>{{ optional($p->user)->name }}</span>
              </div>
              <span>{{ optional($p->published_at)->diffForHumans() }}</span>
            </div>
          </div>
        </a>
      @endforeach
    </div>
    <div class="pagination-wrap">{{ $posts->links() }}</div>
  @endif
</section>
@endsection
