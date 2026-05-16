@extends('layouts.app')

@section('title', 'Your Bookmarks — KukiTales')

@section('content')
<section class="page">
  <h1 class="page-title">Your Bookmarks</h1>
  <p class="page-sub">Stories and articles you've saved for later.</p>

  @if($posts->isEmpty())
    <p style="color:var(--text-muted);">You haven't bookmarked anything yet.</p>
  @else
    <div class="news-grid">
      @foreach($posts as $i => $bm)
        @if($bm->post)
          @php($p = $bm->post)
          <a href="{{ $p->url }}" class="news-card">
            <div class="card-thumb t{{ ($i % 6) + 1 }}">
              {{ ['story'=>'📖','history'=>'🏛️','folktale'=>'🌿','news'=>'📰','blog'=>'✍️','music'=>'🎵','gallery'=>'🖼️','episode'=>'🎭'][$p->type] ?? '📖' }}
              <span class="card-cat cat-{{ $p->type }}">{{ $p->type }}</span>
            </div>
            <div class="card-body">
              <h3 class="card-title">{{ $p->title }}</h3>
              <p class="card-excerpt">Saved {{ $bm->created_at->diffForHumans() }}</p>
            </div>
          </a>
        @endif
      @endforeach
    </div>
    <div class="pagination-wrap">{{ $posts->links() }}</div>
  @endif
</section>
@endsection
