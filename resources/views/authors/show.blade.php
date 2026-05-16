@extends('layouts.app')

@section('title', $author->name . ' — KukiTales')

@section('content')
<section class="page">
  <div class="author-bio" style="margin-bottom:24px;">
    <span class="avatar" style="width:64px;height:64px;font-size:22px;">{{ strtoupper(substr($author->name, 0, 1)) }}</span>
    <div>
      <h4 style="font-size:24px;">{{ $author->name }}</h4>
      <p>{{ $author->bio ?: 'A contributor to KukiTales.' }}</p>
      @if($author->location)<p style="margin-top:4px;font-size:12px;">📍 {{ $author->location }}</p>@endif
    </div>
  </div>

  @if($posts->isEmpty())
    <p style="color:var(--text-muted);">No published posts yet.</p>
  @else
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
    <div class="pagination-wrap">{{ $posts->links() }}</div>
  @endif
</section>
@endsection
