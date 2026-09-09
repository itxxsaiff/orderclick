@php
    $cats = collect($getcategory ?? []);
    $items = collect($getitem ?? []);
    $catNames = $cats->pluck('name', 'id');
    $heroBanner = optional(collect($bannerimage ?? [])->first())->image;
    $heroImg = !empty($heroBanner) ? helper::image_path($heroBanner) : helper::food_image($tName . ' fresh grocery produce', $storeinfo->id, 'food');
    $picks = $items->take(12);
    $reviews = collect($storereview ?? [])->take(6);
@endphp

<!-- HERO -->
<section class="hero">
  <div class="container">
    <div class="hero-fresh">
      <div>
        @if (!empty($tApp->tag_line))<span class="hero-badge"><span class="dot"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 16V8a2 2 0 012-2h9v10H3zM14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/></svg></span> {{ $tApp->tag_line }}</span>@endif
        <h1>{{ $tName }}</h1>
        <p>{{ !empty($tDesc) ? \Illuminate\Support\Str::limit(strip_tags($tDesc), 220) : __('Fresh produce and everyday essentials — packed by hand and delivered to your door.') }}</p>
        <div class="hero-cta" style="display:flex;gap:12px;flex-wrap:wrap;margin-top:8px">
          <a href="{{ URL::to($tSlug . '/categories') }}" class="btn btn-primary btn-lg">{{ __('Start shopping') }}</a>
          @if (!empty($tWa))<a href="https://wa.me/{{ $tWa }}" class="btn btn-ghost btn-lg">{{ __('Order on WhatsApp') }}</a>@endif
        </div>
        @if ($cats->count())
        <div class="hero-quick" style="margin-top:16px">
          @foreach ($cats->take(5) as $c)<a href="{{ URL::to($tSlug . '/categories') }}#cat-{{ $c->id }}">{{ $c->name }}</a>@endforeach
        </div>
        @endif
        <ul class="hero-usps">
          <li><span class="tick"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span> {{ __('Fast delivery') }}</li>
          <li><span class="tick"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span> {{ __('Fresh guarantee') }}</li>
          <li><span class="tick"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span> {{ __('Secure checkout') }}</li>
        </ul>
      </div>
      <div class="hero-collage">
        <div class="hero-collage__main"><img src="{{ $heroImg }}" alt="{{ $tName }}"></div>
      </div>
    </div>

    @if ($cats->count())
    <div class="cat-rail">
      @foreach ($cats as $c)
        @php $cimg = !empty($c->image) ? helper::image_path($c->image) : helper::food_image($c->name, $c->id, 'food'); @endphp
        <a href="{{ URL::to($tSlug . '/categories') }}#cat-{{ $c->id }}"><img src="{{ $cimg }}" alt="{{ $c->name }}" loading="lazy"><strong>{{ $c->name }}</strong><span>{{ $items->where('cat_id', $c->id)->count() }} {{ __('items') }}</span></a>
      @endforeach
    </div>
    @endif
  </div>
</section>

<!-- TRUST -->
<section class="section-tight" style="padding-top:0">
  <div class="container">
    <div class="trust-row">
      <div class="trust"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 16V8a2 2 0 012-2h9v10H3zM14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/></svg></span><div><strong>{{ __('Fast delivery') }}</strong><span>{{ __('To your door') }}</span></div></div>
      <div class="trust"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M20 4C10 4 4 9 4 17c0 1.5.4 2.6.4 2.6S9 12 20 10c0 0-3.5 8-11 9"/></svg></span><div><strong>{{ __('Fresh guarantee') }}</strong><span>{{ __('Picked fresh daily') }}</span></div></div>
      <div class="trust"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10" width="16" height="11" rx="2.5"/><path d="M8 10V7a4 4 0 018 0v3"/></svg></span><div><strong>{{ __('Secure checkout') }}</strong><span>{{ __('Card, wallet & cash') }}</span></div></div>
      <div class="trust"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a10 10 0 00-8.6 15L2 22l5.2-1.4A10 10 0 1012 2z"/></svg></span><div><strong>{{ __('Order on WhatsApp') }}</strong><span>{{ __('One tap to order') }}</span></div></div>
    </div>
  </div>
</section>

<!-- FRESH PICKS -->
@if ($picks->count())
<section class="section bg-alt">
  <div class="container">
    <div class="head-row reveal">
      <div class="section-head"><span class="eyebrow">{{ __('Fresh picks') }}</span><h2>{{ __('Popular this week') }}</h2></div>
      @if ($cats->count() > 1)
        <div class="chip-row" data-filter-group>
          <button class="chip active" data-filter="all">{{ __('All') }}</button>
          @foreach ($cats->take(5) as $c)<button class="chip" data-filter="cat{{ $c->id }}">{{ $c->name }}</button>@endforeach
        </div>
      @endif
    </div>
    <div class="grid g-6 products reveal">
      @foreach ($picks as $item)
        @include('front.template-5.partials.product_card', ['item' => $item, 'catNames' => $catNames])
      @endforeach
    </div>
    <div class="text-center mt-4"><a href="{{ URL::to($tSlug . '/categories') }}" class="btn btn-ghost btn-lg">{{ __('Shop all products') }}</a></div>
  </div>
</section>
@endif

<!-- SOURCING BAND -->
<section class="section-tight">
  <div class="container">
    <div class="split reveal" style="align-items:center;background:var(--brand-soft);border-radius:var(--r-xl);padding:clamp(24px,3vw,44px);gap:clamp(24px,4vw,44px)">
      <div>
        <span class="eyebrow" style="background:var(--brand);color:#fff"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M20 4C10 4 4 9 4 17c0 1.5.4 2.6.4 2.6S9 12 20 10c0 0-3.5 8-11 9"/></svg> {{ __('Fresh every day') }}</span>
        <h2 class="mt-2">{{ __('Real food, picked fresh — not from a warehouse') }}</h2>
        <p class="lead mt-2">{{ __('We pick the freshest produce and everyday essentials, pack them by hand, and get them to your door fast.') }}</p>
        <ul class="check-list">
          <li><span class="tick" style="background:#fff"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span><span><b>{{ __('Fresh guarantee.') }}</b> {{ __('Not up to standard? We make it right.') }}</span></li>
          <li><span class="tick" style="background:#fff"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span><span><b>{{ __('Everyday essentials.') }}</b> {{ __('Everything you need in one basket.') }}</span></li>
          <li><span class="tick" style="background:#fff"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span><span><b>{{ __('Order your way.') }}</b> {{ __('Checkout online or send it on WhatsApp.') }}</span></li>
          <li><span class="tick" style="background:#fff"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span><span><b>{{ __('Fast delivery.') }}</b> {{ __('Packed and on its way in no time.') }}</span></li>
        </ul>
        <div class="flex gap-1 wrap">
          <a href="{{ URL::to($tSlug . '/categories') }}" class="btn btn-primary btn-lg">{{ __('Shop fresh produce') }}</a>
          <a href="{{ URL::to($tSlug . '/aboutus') }}" class="btn btn-ghost btn-lg">{{ __('About us') }}</a>
        </div>
      </div>
      <div class="media-frame"><img src="{{ helper::food_image($tName . ' fresh market vegetables', $storeinfo->id + 5, 'food') }}" alt="{{ $tName }}"></div>
    </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="section">
  <div class="container">
    <div class="section-head center reveal"><span class="eyebrow">{{ __('How it works') }}</span><h2>{{ __('Groceries in a few taps') }}</h2></div>
    <div class="steps reveal">
      <div class="step"><h4>{{ __('Browse the aisles') }}</h4><p>{{ __('Explore fresh produce, dairy, bakery and pantry — all in one place.') }}</p></div>
      <div class="step"><h4>{{ __('Fill your basket') }}</h4><p>{{ __('Add what you need and adjust quantities right from the grid.') }}</p></div>
      <div class="step"><h4>{{ __('Checkout your way') }}</h4><p>{{ __('Pay by card, wallet, bank transfer or cash — or order on WhatsApp.') }}</p></div>
      <div class="step"><h4>{{ __('Delivered fresh') }}</h4><p>{{ __('We pick, pack and deliver to your door, with tracking every step.') }}</p></div>
    </div>
  </div>
</section>

<!-- STATS -->
<section class="section-tight">
  <div class="container">
    <div class="stat-row reveal">
      <div class="stat"><b><span data-count="{{ max($items->count(), 1) }}">0</span></b><span>{{ __('Products in stock') }}</span></div>
      <div class="stat"><b><span data-count="{{ max($cats->count(), 1) }}">0</span></b><span>{{ __('Categories') }}</span></div>
      <div class="stat"><b><span data-count="45" data-suffix=" min">0</span></b><span>{{ __('Fast delivery') }}</span></div>
      <div class="stat"><b><span data-count="4.9">0</span></b><span>{{ __('Average rating') }}</span></div>
    </div>
  </div>
</section>

<!-- REVIEWS -->
@if ($reviews->count())
<section class="section-tight">
  <div class="container">
    <div class="head-row"><div class="section-head" style="margin-bottom:0"><span class="eyebrow">{{ __('Loved by shoppers') }}</span><h2>{{ __('What customers say') }}</h2></div></div>
    <div class="tst-scroller reveal">
      @foreach ($reviews as $rv)
        <article class="testimonial">
          <span class="stars">@for ($s = 0; $s < (int) ($rv->star ?? 5); $s++)<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.9 21l1.2-6.8-5-4.9 6.9-1z"/></svg>@endfor</span>
          <p>"{{ \Illuminate\Support\Str::limit($rv->description, 180) }}"</p>
          <div class="who">@if (!empty($rv->image))<img src="{{ helper::image_path($rv->image) }}" alt="">@endif<div><strong>{{ $rv->name }}</strong><span>{{ __('Verified shopper') }}</span></div></div>
        </article>
      @endforeach
    </div>
  </div>
</section>
@endif

<!-- CTA -->
<section class="section-tight">
  <div class="container">
    <div class="cta-band reveal">
      <div class="cta-band__inner">
        <div>
          <span class="eyebrow">{{ __('Your basket awaits') }}</span>
          <h2 class="mt-2">{{ __('Fresh groceries, delivered to your door.') }}</h2>
          <p>{{ __('Browse the aisles and order in minutes — or send your basket straight to WhatsApp.') }}</p>
        </div>
        <div class="flex gap-1 wrap">
          <a href="{{ URL::to($tSlug . '/categories') }}" class="btn btn-primary btn-lg">{{ __('Start shopping') }}</a>
          @if (!empty($tWa))<a href="https://wa.me/{{ $tWa }}" class="btn btn-ghost btn-lg" style="color:#fff;border-color:rgba(255,255,255,.28)"><svg viewBox="0 0 24 24" fill="currentColor" style="width:18px;height:18px"><path d="M12 2a10 10 0 00-8.6 15L2 22l5.2-1.4A10 10 0 1012 2z"/></svg> {{ __('Order on WhatsApp') }}</a>@endif
        </div>
      </div>
    </div>
  </div>
</section>
