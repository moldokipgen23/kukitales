@extends('layouts.app')

@section('title', 'Submit a Story — KukiTales')

@section('content')
<section class="page" style="max-width:760px;">
  <h1 class="page-title">Submit your story</h1>
  <p class="page-sub">Stories, folktales, history, news — your submission goes to our editors for review.</p>

  <form method="POST" action="{{ route('submit.store') }}" enctype="multipart/form-data" class="auth-card" style="padding:24px;">
    @csrf

    <div class="field">
      <label>Content type</label>
      <select name="type" required>
        @php($preselected = request('type'))
        @foreach(['story'=>'📖 Short Story','episode'=>'🎭 Episode','history'=>'🏛️ History','folktale'=>'🌿 Folktale','news'=>'📰 News','blog'=>'✍️ Blog','music'=>'🎵 Music','gallery'=>'🖼️ Gallery'] as $k => $label)
          <option value="{{ $k }}" {{ $preselected === $k ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
      </select>
    </div>

    <div class="field">
      <label>Title</label>
      <input type="text" name="title" value="{{ old('title') }}" required>
      @error('title')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field">
      <label>Category (optional)</label>
      <select name="category_id">
        <option value="">— Choose a category —</option>
        @foreach($categories as $c)
          <option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>{{ $c->icon }} {{ $c->name }}</option>
        @endforeach
      </select>
    </div>

    <div class="field">
      <label>Excerpt / one-line summary (optional)</label>
      <input type="text" name="excerpt" value="{{ old('excerpt') }}" maxlength="500">
    </div>

    <div class="field">
      <label>Content</label>
      <textarea name="content" rows="12" required>{{ old('content') }}</textarea>
      @error('content')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field">
      <label>Cover image (optional)</label>
      <input type="file" name="cover_image" accept="image/*">
      @error('cover_image')<span class="err">{{ $message }}</span>@enderror
    </div>

    <button type="submit" class="btn-primary">Submit for review</button>
  </form>
</section>
@endsection
