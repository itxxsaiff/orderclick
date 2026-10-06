{{-- Booking: bookable services, drawn with the product-card styles so every card variant applies. --}}
@php
    $T = fn($k, $f = '') => \App\Services\StoreDesign::text($tDesign, $k, $f);
    $ocSvcs = collect($services ?? [])->take(8);
@endphp
@if ($ocSvcs->count())
<section class="section bg-alt">
  <div class="container">
    <div class="head-row reveal">
      <div class="section-head">
        <h2>{{ $T('services_title', trans('labels.eng_services_title')) }}</h2>
        @php $ocSub = $T('services_text'); @endphp
        @if ($ocSub !== '')<p>{{ $ocSub }}</p>@endif
      </div>
      <a href="{{ URL::to($tSlug . '/booking') }}" class="link-arrow">{{ __('See all') }}
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
      </a>
    </div>
    <div class="grid g-4 products reveal">
      @foreach ($ocSvcs as $s)
        @php
          $img = !empty($s->image) ? url(env('ASSETSPATHURL') . 'item/' . $s->image) : null;
          $url = URL::to($tSlug . '/booking') . '?service=' . $s->id;
        @endphp
        <article class="product-card">
          <div class="product-card__media"><a href="{{ $url }}">@if ($img)<img src="{{ $img }}" alt="{{ $s->name }}" loading="lazy">@else<span class="oce-ph">{{ mb_strtoupper(mb_substr($s->name, 0, 1)) }}</span>@endif</a></div>
          <div class="product-card__body">
            @if ($s->category)<span class="product-card__meta">{{ $s->category }}</span>@endif
            <h4><a href="{{ $url }}">{{ $s->name }}</a></h4>
            @if (!empty($s->description))<p class="product-card__desc">{{ \Illuminate\Support\Str::limit(strip_tags($s->description), 80) }}</p>@endif
            <div class="product-card__foot">
              <span class="price">{{ helper::currency_formate($s->price, $storeinfo->id) }}@if (!empty($s->duration))<small> · {{ $s->duration }}</small>@endif</span>
              <a href="{{ $url }}" class="btn btn-primary btn-sm">{{ __('Book') }}</a>
            </div>
          </div>
        </article>
      @endforeach
    </div>
  </div>
</section>
@endif
