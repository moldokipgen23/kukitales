@php
  $__ads = \App\Models\Advertisement::forPlacement($placement ?? '')->limit($limit ?? 1)->get();
@endphp
@foreach($__ads as $__ad)
  {!! $__ad->render() !!}
@endforeach
