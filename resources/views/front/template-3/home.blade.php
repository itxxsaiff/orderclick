@php
    $cats = collect($getcategory ?? []);
    $items = collect($getitem ?? []);
    $heroBanner = optional(collect($bannerimage ?? [])->first())->image;
    $heroImg = !empty($heroBanner)
        ? helper::image_path($heroBanner)
        : helper::food_image($tName . ' restaurant food', $storeinfo->id);
    $popular = $items->take(8);
@endphp

<!-- HERO -->
<section class="hero">
  <div class="container hero-grid">
    <div>
      @if (!empty($tApp->tag_line))<span class="eyebrow">{{ $tApp->tag_line }}</span>@endif
      <h1>{{ $tName }}</h1>
      <p class="lead">{{ !empty($tDesc) ? \Illuminate\Support\Str::limit(strip_tags($tDesc), 220) : __('Freshly made, delivered fast. Pick your favourites, tap once, and we\'ll have it hot at your door.') }}</p>

      <div class="hero-cta">
        <a href="{{ URL::to($tSlug . '/categories') }}" class="btn btn-dark btn-lg">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 2v7c0 1.7 1.3 3 3 3s3-1.3 3-3V2M6 12v10M18 2c-2 0-3 2.5-3 5.5S16 13 18 13s3-2 3-5.5S20 2 18 2zM18 13v9"/></svg>
          {{ __('Explore the menu') }}
        </a>
        @if (!empty($tWa))
          <a href="https://wa.me/{{ $tWa }}" class="btn btn-primary btn-lg">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.5 14.4c-.3-.2-1.7-.9-2-1s-.5-.1-.7.1-.7.9-.9 1.1-.4.2-.7.1a8.2 8.2 0 01-2.4-1.5 9 9 0 01-1.7-2.1c-.2-.3 0-.5.1-.6l.5-.6.3-.5v-.5l-.9-2.2c-.3-.6-.5-.5-.7-.5h-.6a1.2 1.2 0 00-.8.4A3.4 3.4 0 005 8.8a5.9 5.9 0 001.3 3.2 13.5 13.5 0 005.2 4.6 17 17 0 001.7.6 4.2 4.2 0 001.9.1 3.1 3.1 0 002-1.4 2.5 2.5 0 00.2-1.4c-.1-.1-.3-.2-.6-.3zM12 2a10 10 0 00-8.6 15L2 22l5.2-1.4A10 10 0 1012 2zm0 18.3a8.3 8.3 0 01-4.2-1.2l-.3-.2-3.1.8.8-3-.2-.3A8.3 8.3 0 1112 20.3z"/></svg>
            {{ __('Order on WhatsApp') }}
          </a>
        @endif
      </div>

      @if ($cats->count())
        <div class="hero-chips">
          @foreach ($cats->take(4) as $c)
            <a href="{{ URL::to($tSlug . '/categories') }}#cat-{{ $c->id }}" class="chip">{{ $c->name }}</a>
          @endforeach
        </div>
      @endif
    </div>

    <div class="hero-visual">
      <div class="hero-media">
        <img src="{{ $heroImg }}" alt="{{ $tName }}">
      </div>
      <div class="float-card float-card--rating">
        <div class="stars">
          @for ($i = 0; $i < 5; $i++)<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.9 21l1.2-6.8-5-4.9 6.9-1z"/></svg>@endfor
        </div>
        <div><span>{{ __('Loved by our regulars') }}</span></div>
      </div>
      <div class="float-card float-card--time">
        <span class="brand-mark" style="width:38px;height:38px;border-radius:12px;font-size:1rem">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="width:18px;height:18px"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
        </span>
        <div><strong>25–35 min</strong><span>{{ __('Average delivery') }}</span></div>
      </div>
    </div>
  </div>
</section>

<!-- VALUE STRIP -->
<section class="value-strip">
  <div class="container">
    <div class="grid g-4">
      <div class="value-item"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L4.1 13.3a.6.6 0 00.5 1H11l-1 7.7 8.9-11.3a.6.6 0 00-.5-1H12z"/></svg></span><div><strong>{{ __('Fast delivery') }}</strong><span>{{ __('Hot and on time') }}</span></div></div>
      <div class="value-item"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="4" y="10" width="16" height="11" rx="2.5"/><path d="M8 10V7a4 4 0 018 0v3"/></svg></span><div><strong>{{ __('Secure checkout') }}</strong><span>{{ __('Card, wallet & cash') }}</span></div></div>
      <div class="value-item"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 11-3.2-6.9M21 4v5h-5"/></svg></span><div><strong>{{ __('Order on WhatsApp') }}</strong><span>{{ __('One tap to order') }}</span></div></div>
      <div class="value-item"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.6 5.6 6.1.8-4.5 4.2 1.2 6-5.4-3-5.4 3 1.2-6L3.3 8.4l6.1-.8z"/></svg></span><div><strong>{{ __('Made fresh daily') }}</strong><span>{{ __('Nothing frozen, ever') }}</span></div></div>
    </div>
  </div>
</section>

<!-- CATEGORIES -->
@if ($cats->count())
<section class="section">
  <div class="container">
    <div class="head-row reveal">
      <div class="section-head">
        <span class="eyebrow">{{ __('Browse by craving') }}</span>
        <h2>{{ __('What are you in the mood for?') }}</h2>
        <p>{{ __('Mix and match — everything cooks together and arrives together.') }}</p>
      </div>
      <a href="{{ URL::to($tSlug . '/categories') }}" class="link-arrow">{{ __('See full menu') }}
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
      </a>
    </div>

    <div class="cat-rail reveal">
      @foreach ($cats as $c)
        @php
          $cimg = !empty($c->image) ? helper::image_path($c->image) : helper::food_image($c->name, $c->id);
          $cnt = $items->where('cat_id', $c->id)->count();
        @endphp
        <a href="{{ URL::to($tSlug . '/categories') }}#cat-{{ $c->id }}" class="cat-card">
          <img src="{{ $cimg }}" alt="{{ $c->name }}" loading="lazy">
          <span class="cat-card__label"><strong>{{ $c->name }}</strong><span>{{ $cnt }} {{ __('items') }}</span></span>
        </a>
      @endforeach
    </div>
  </div>
</section>
@endif

<!-- POPULAR -->
@if ($popular->count())
<section class="section bg-alt">
  <div class="container">
    <div class="head-row reveal">
      <div class="section-head">
        <span class="eyebrow">{{ __('Popular picks') }}</span>
        <h2>{{ __('Dishes everyone keeps re-ordering') }}</h2>
        <p>{{ __('A taste of what we do best.') }}</p>
      </div>
      @if ($cats->count() > 1)
        <div class="hero-chips" data-filter-group>
          <button class="chip active" data-filter="all">{{ __('All') }}</button>
          @foreach ($cats->take(4) as $c)
            <button class="chip" data-filter="cat{{ $c->id }}">{{ $c->name }}</button>
          @endforeach
        </div>
      @endif
    </div>

    <div class="grid g-4 products reveal">
      @foreach ($popular as $item)
        @include('front.template-3.partials.product_card', ['item' => $item, 'catNames' => $cats->pluck('name', 'id')])
      @endforeach
    </div>

    <div class="text-center mt-4">
      <a href="{{ URL::to($tSlug . '/categories') }}" class="btn btn-ghost btn-lg">{{ __('View the full menu') }}</a>
    </div>
  </div>
</section>
@endif

<!-- HOW IT WORKS -->
<section class="section bg-alt">
  <div class="container">
    <div class="section-head center reveal">
      <span class="eyebrow">{{ __('How it works') }}</span>
      <h2>{{ __('Three taps between you and dinner') }}</h2>
      <p>{{ __('No account needed, no long forms. Order as a guest and we\'ll remember you next time.') }}</p>
    </div>
    <div class="steps reveal">
      <div class="step"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16M4 12h16M4 18h10"/></svg></span><h4>{{ __('Pick your food') }}</h4><p>{{ __('Browse the menu, choose sizes and add-ons, and build the order exactly how you like it.') }}</p></div>
      <div class="step"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="3"/><path d="M2 10h20"/></svg></span><h4>{{ __('Pay your way') }}</h4><p>{{ __('Card, wallet, bank transfer or plain cash on delivery — whatever suits you.') }}</p></div>
      <div class="step"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 16V8a2 2 0 012-2h9v10H3zM14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/></svg></span><h4>{{ __('Track it to your door') }}</h4><p>{{ __('Live status from the kitchen, plus your rider\'s number the moment they pick it up.') }}</p></div>
    </div>
  </div>
</section>

<!-- CTA BAND -->
<section class="section-tight">
  <div class="container">
    <div class="cta-band reveal">
      <div class="cta-band__inner">
        <div>
          <span class="eyebrow" style="color:#7FE0A4">{{ __('Hungry yet?') }}</span>
          <h2 class="mt-1">{{ __('Your order is just a few taps away.') }}</h2>
          <p>{{ __('Browse the menu, or send your order straight to WhatsApp and skip the queue entirely.') }}</p>
        </div>
        <div class="flex gap-2 wrap">
          <a href="{{ URL::to($tSlug . '/categories') }}" class="btn btn-primary btn-lg">{{ __('Order delivery') }}</a>
          @if (!empty($tWa))
            <a href="https://wa.me/{{ $tWa }}" class="btn btn-ghost btn-lg" style="color:#fff;border-color:rgba(255,255,255,.28)">{{ __('Order on WhatsApp') }}</a>
          @endif
        </div>
      </div>
    </div>
  </div>
</section>
