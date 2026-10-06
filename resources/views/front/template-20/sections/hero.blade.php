{{-- Hero. Layout (split / centered / fullbleed / minimal) comes from the body class. --}}
@php
    $T = fn($k, $f = '') => \App\Services\StoreDesign::text($tDesign, $k, $f);
    // The vendor's own banner or cover photo; stock photos only where they fit (food).
    $ocBanner = optional(collect($bannerimage ?? [])->first())->image;
    $ocFood = in_array(optional($tApp)->business_type, ['food', 'cafe', 'grocery', null, ''], true) && $tFlow === 'orders';
    $ocHeroImg = !empty($ocBanner) ? helper::image_path($ocBanner)
        : (!empty(optional($tApp)->cover_image) ? helper::image_path($tApp->cover_image)
        : ($ocFood ? helper::food_image($tName . ' ' . optional($tApp)->business_type, $storeinfo->id) : null));
    $ocDefaultText = ['orders' => trans('labels.eng_hero_text_orders'), 'booking' => trans('labels.eng_hero_text_booking'), 'service' => trans('labels.eng_hero_text_service')][$tFlow];
@endphp
<section class="hero oce-hero {{ $ocHeroImg ? '' : 'oce-hero-noimg' }}">
  @if ($ocHeroImg)<div class="oce-hero-bg"><img src="{{ $ocHeroImg }}" alt=""></div>@endif
  <div class="container hero-grid">
    <div class="oce-hero-copy">
      @php $ocEyebrow = $T('hero_eyebrow', (string) optional($tApp)->tag_line); @endphp
      @if ($ocEyebrow !== '')<span class="eyebrow">{{ $ocEyebrow }}</span>@endif
      <h1>{{ $T('hero_title', $tName) }}</h1>
      <p class="lead">{{ $T('hero_text', !empty($tDesc) ? \Illuminate\Support\Str::limit(strip_tags($tDesc), 220) : $ocDefaultText) }}</p>
      <div class="hero-cta">
        <a href="{{ $tCta['url'] }}" class="btn btn-primary btn-lg">{{ $T('hero_cta', $tCta['label']) }}</a>
        @if (!empty($tWa))
          <a href="https://wa.me/{{ $tWa }}" class="btn btn-ghost btn-lg oce-wa-btn" target="_blank" rel="noopener">
            @include('front.template-20.partials.icon', ['name' => 'whatsapp']) {{ __('WhatsApp') }}
          </a>
        @endif
      </div>
      @if ($tFlow === 'orders' && collect($getcategory ?? [])->count())
        <div class="hero-chips">
          @foreach (collect($getcategory)->take(4) as $c)
            <a href="{{ URL::to($tSlug . '/categories') }}#cat-{{ $c->id }}" class="chip">{{ $c->name }}</a>
          @endforeach
        </div>
      @endif
    </div>
    @if ($ocHeroImg)
      <div class="hero-visual">
        <div class="hero-media"><img src="{{ $ocHeroImg }}" alt="{{ $tName }}"></div>
      </div>
    @endif
  </div>
</section>
