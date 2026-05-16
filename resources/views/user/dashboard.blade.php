@extends('layouts.app')

@section('title', 'Your Dashboard — KukiTales')

@section('content')
<section class="page">
  <h1 class="page-title">Hello, {{ $user->name }}</h1>
  <p class="page-sub">{{ ucfirst($user->role) }} · joined {{ $user->created_at->format('M Y') }}</p>

  <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:24px;">
    <a href="{{ route('submit.create') }}" class="btn-primary">✏️ Submit a story</a>
    <a href="{{ route('bookmarks.index') }}" class="btn-primary" style="background:var(--dark);color:var(--gold);">🔖 Bookmarks</a>
    @if(in_array($user->role, ['admin', 'editor']))
      <a href="/admin" class="btn-primary" style="background:var(--gold);color:var(--dark);">⚙ Admin panel</a>
    @endif
  </div>

  <h2 style="font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:700;margin:18px 0 12px;color:var(--text);">Your submissions</h2>
  @if($posts->isEmpty())
    <p style="color:var(--text-muted);">No submissions yet. <a href="{{ route('submit.create') }}" style="color:var(--red);font-weight:700;">Submit your first story →</a></p>
  @else
    <div class="blog-list">
      @foreach($posts as $p)
        <div class="blog-item">
          <div class="blog-thumb bt1">{{ ['story'=>'📖','history'=>'🏛️','folktale'=>'🌿','news'=>'📰','blog'=>'✍️','music'=>'🎵','gallery'=>'🖼️','episode'=>'🎭'][$p->type] ?? '📖' }}</div>
          <div class="blog-content">
            <div class="blog-tags">
              <span class="tag">{{ $p->type }}</span>
              <span class="tag" style="background:var(--dark);color:var(--gold);">{{ $p->status }}</span>
            </div>
            <h3 class="blog-title">{{ $p->title }}</h3>
            <p class="blog-excerpt">{{ \Illuminate\Support\Str::limit($p->short_excerpt, 140) }}</p>
            <div class="blog-footer">
              <span>{{ $p->created_at->diffForHumans() }} · {{ number_format($p->view_count) }} views</span>
              @if($p->status === 'published')<a class="read-more" href="{{ $p->url }}">View →</a>@endif
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif

  <h2 style="font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:700;margin:30px 0 12px;color:var(--text);">Recent bookmarks</h2>
  @if($bookmarks->isEmpty())
    <p style="color:var(--text-muted);">You haven't bookmarked anything yet.</p>
  @else
    <ul style="list-style:none;display:flex;flex-direction:column;gap:8px;">
      @foreach($bookmarks as $b)
        @if($b->post)
          <li><a href="{{ $b->post->url }}" style="color:var(--text-mid);font-weight:600;">→ {{ $b->post->title }}</a> <span style="color:var(--text-muted);font-size:12px;">· {{ $b->created_at->diffForHumans() }}</span></li>
        @endif
      @endforeach
    </ul>
  @endif
</section>
@endsection
