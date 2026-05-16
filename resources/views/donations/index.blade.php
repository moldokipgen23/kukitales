@extends('layouts.app')

@section('title', 'Donate — KukiTales')

@section('content')
<section class="cat-hero">
  <div class="cat-hero-inner">
    <h1>❤️ Support KukiTales</h1>
    <p>Your donation helps us preserve Kuki stories, history, and culture for future generations.</p>
  </div>
</section>

<section class="page">
  @if($campaigns->isEmpty())
    <p style="color:var(--text-muted);">No active donation campaigns right now.</p>
  @else
    <div class="donate-grid">
      @foreach($campaigns as $c)
        <a class="donate-tile" href="{{ route('donate.show', $c->slug) }}">
          <div class="donate-tile-img" @if($c->cover_url) style="background-image:url('{{ $c->cover_url }}')" @endif>
            @if(!$c->cover_url)❤️@endif
          </div>
          <div class="donate-tile-body">
            <h4>{{ $c->title }}</h4>
            <p class="card-excerpt">{{ \Illuminate\Support\Str::limit($c->short_description ?: strip_tags($c->description), 130) }}</p>
            @if($c->goal_amount)
              <div class="donate-progress"><div style="width:{{ $c->progress_percent }}%"></div></div>
              <div class="donate-stats">
                <span><strong>{{ $c->currency }} {{ number_format($c->raised_amount, 0) }}</strong> raised</span>
                <span>{{ $c->progress_percent }}% of goal</span>
              </div>
            @endif
            <span class="read-more">Donate →</span>
          </div>
        </a>
      @endforeach
    </div>
  @endif
</section>
@endsection
