@extends('layouts.app')

@section('title', 'Login — KukiTales')

@section('content')
<div class="auth-wrap">
  <div class="auth-card">
    <h1>Welcome back</h1>
    <p class="lead">Log in to bookmark stories, follow series, and submit your work.</p>

    <form method="POST" action="{{ route('login') }}">
      @csrf
      <div class="field">
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" autofocus required>
        @error('email')<span class="err">{{ $message }}</span>@enderror
      </div>
      <div class="field">
        <label>Password</label>
        <input type="password" name="password" required>
        @error('password')<span class="err">{{ $message }}</span>@enderror
      </div>
      <div class="field">
        <label style="display:inline-flex;align-items:center;gap:6px;text-transform:none;font-weight:500;font-size:13px;color:var(--text-mid);">
          <input type="checkbox" name="remember" style="width:auto;"> Remember me
        </label>
      </div>
      <button type="submit" class="btn-primary btn-block">Log in</button>
    </form>

    @include('auth.partials.social')

    <p class="auth-foot">No account? <a href="{{ route('register') }}">Register</a></p>
  </div>
</div>
@endsection
