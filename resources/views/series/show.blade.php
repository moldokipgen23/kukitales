@extends('layouts.app')

@section('title', $series->title . ' — Series — KukiTales')

@section('content')
<section style="background:linear-gradient(135deg,var(--dark),var(--dark2));color:#fff;padding:48px var(--pad);">
  <div style="max-width:900px;margin:0 auto;">
    <div style="color:var(--gold);font-size:11px;letter-spacing:2px;text-transform:uppercase;font-weight:800;margin-bottom:10px;">Episode Series</div>
    <h1 style="font-family:'Cormorant Garamond',serif;font-size:40px;font-weight:700;line-height:1.2;margin-bottom:10px;">{{ $series->title }}</h1>
    @if($series->description)<p style="color:#c8a8a4;font-size:14px;line-height:1.7;max-width:680px;">{{ $series->description }}</p>@endif
    <div style="margin-top:14px;font-size:12px;color:#9a6058;">{{ $series->posts_count }} episodes · {{ ucfirst($series->status) }}</div>
  </div>
</section>

<section class="page">
  <h2 style="font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:700;margin-bottom:16px;color:var(--text);">Episodes</h2>
  @if($series->posts->isEmpty())
    <p style="color:var(--text-muted);">No episodes published yet.</p>
  @else
    <div class="blog-list">
      @foreach($series->posts as $ep)
        <a class="blog-item" href="{{ route('posts.episode', ['series' => $series->slug, 'slug' => $ep->slug]) }}">
          <div class="blog-thumb bt{{ ($loop->index % 3) + 1 }}" style="font-weight:800;font-family:'Cormorant Garamond',serif;font-size:24px;">{{ $ep->episode_number }}</div>
          <div class="blog-content">
            <div class="blog-tags"><span class="tag">Episode {{ $ep->episode_number }}</span></div>
            <h3 class="blog-title">{{ $ep->title }}</h3>
            <p class="blog-excerpt">{{ \Illuminate\Support\Str::limit($ep->short_excerpt, 160) }}</p>
            <div class="blog-footer">
              <span>{{ $ep->read_time }} min · {{ optional($ep->published_at)->diffForHumans() }}</span>
              <span class="read-more">Read →</span>
            </div>
          </div>
        </a>
      @endforeach
    </div>
  @endif
</section>
@endsection
