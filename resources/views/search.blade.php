@extends('layouts.app')

@section('title', 'Search — KukiTales')

@section('content')
<section class="page">
  <h1 class="page-title">Search</h1>
  <p class="page-sub">{{ $q ? 'Results for "' . $q . '"' : 'Enter a search above.' }} {{ $q ? '— ' . $posts->total() . ' ' . \Illuminate\Support\Str::plural('result', $posts->total()) : '' }}</p>

  @if($q && $posts->isEmpty())
    <p style="color:var(--text-muted);">No matches found. Try different keywords.</p>
  @elseif($q)
    <div class="news-grid">
      @foreach($posts as $i => $p)
        <a href="{{ $p->url }}" class="news-card">
          <div class="card-thumb t{{ ($i % 6) + 1 }}">
            {{ ['story'=>'📖','history'=>'🏛️','folktale'=>'🌿','news'=>'📰','blog'=>'✍️','music'=>'🎵','gallery'=>'🖼️','episode'=>'🎭'][$p->type] ?? '📖' }}
            <span class="card-cat cat-{{ $p->type }}">{{ $p->type }}</span>
          </div>
          <div class="card-body">
            <h3 class="card-title">{{ $p->title }}</h3>
            <p class="card-excerpt">{{ \Illuminate\Support\Str::limit($p->short_excerpt, 110) }}</p>
          </div>
        </a>
      @endforeach
    </div>
    <div class="pagination-wrap">{{ $posts->withQueryString()->links() }}</div>
  @endif
</section>
@endsection
