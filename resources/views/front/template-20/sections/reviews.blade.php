@php
    $T = fn($k, $f = '') => \App\Services\StoreDesign::text($tDesign, $k, $f);
    $ocReviews = collect($storereview ?? [])->filter(fn($r) => !empty($r->description))->take(6);
@endphp
@if ($ocReviews->count())
<section class="section">
  <div class="container">
    <div class="section-head center reveal"><h2>{{ $T('reviews_title', trans('labels.eng_reviews_title')) }}</h2></div>
    <div class="grid g-3 oce-reviews reveal">
      @foreach ($ocReviews as $rv)
        <article class="oce-review">
          <span class="stars">@for ($s = 0; $s < max(1, min(5, (int) ($rv->star ?? 5))); $s++)<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.9 21l1.2-6.8-5-4.9 6.9-1z"/></svg>@endfor</span>
          <p>“{{ \Illuminate\Support\Str::limit($rv->description, 200) }}”</p>
          <strong>{{ $rv->name }}</strong>
        </article>
      @endforeach
    </div>
  </div>
</section>
@endif
