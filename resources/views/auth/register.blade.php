@extends('layouts.app')

@section('title', 'Register — KukiTales')

@section('content')
<div class="auth-wrap">
  <div class="auth-card">
    <h1>Join KukiTales</h1>
    <p class="lead">Create an account to bookmark, follow series, and submit stories of your own.</p>

    <form method="POST" action="{{ route('register') }}">
      @csrf
      <div class="field"><label>Full name</label>
        <input type="text" name="name" value="{{ old('name') }}" required>
        @error('name')<span class="err">{{ $message }}</span>@enderror
      </div>
      <div class="field"><label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required>
        @error('email')<span class="err">{{ $message }}</span>@enderror
      </div>
      <div class="field"><label>Password</label>
        <input type="password" name="password" required>
        @error('password')<span class="err">{{ $message }}</span>@enderror
      </div>
      <div class="field"><label>Confirm password</label>
        <input type="password" name="password_confirmation" required>
      </div>
      <button type="submit" class="btn-primary btn-block">Create account</button>
    </form>

    @include('auth.partials.social')

    <p class="auth-foot">Already have an account? <a href="{{ route('login') }}">Log in</a></p>
  </div>
</div>
@endsection
