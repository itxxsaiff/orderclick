@php
    $cats = collect($getcategory ?? []);
    $items = collect($getitem ?? []);
    $catNames = $cats->pluck('name', 'id');
    $heroBanner = optional(collect($bannerimage ?? [])->first())->image;
    $heroImg = !empty($heroBanner) ? helper::image_path($heroBanner) : helper::food_image($tName . ' pharmacy medicine', $storeinfo->id, 'retail');
    $picks = $items->take(12);
    $reviews = collect($storereview ?? [])->take(6);
    $needIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M8.5 3.5a5 5 0 017 7l-5 5a5 5 0 01-7-7z"/><path d="M6.8 6.8l7 7"/></svg>';
@endphp

<!-- HERO -->
<section class="hero">
  <div class="container hero-grid">
    <div class="hero-main">
      <img class="hero-main__img" src="{{ $heroImg }}" alt="{{ $tName }}">
      <div class="hero-main__body">
        @if (!empty($tApp->tag_line))<span class="eyebrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 16V8a2 2 0 012-2h9v10H3zM14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/></svg> {{ $tApp->tag_line }}</span>@endif
        <h1 class="mt-2">{{ $tName }}</h1>
        <p>{{ !empty($tDesc) ? \Illuminate\Support\Str::limit(strip_tags($tDesc), 200) : __('Everyday medicines, vitamins and health essentials — delivered to your door.') }}</p>
        <div class="hero-cta">
          <a href="{{ URL::to($tSlug . '/categories') }}" class="btn btn-lg" style="background:#fff;color:var(--brand-dark)">{{ __('Shop medicines') }}</a>
          @if (!empty($tWa))<a href="https://wa.me/{{ $tWa }}" class="btn btn-ghost btn-lg">{{ __('Order on WhatsApp') }}</a>@endif
        </div>
        <p class="small mt-3" style="color:rgba(255,255,255,.75)">{{ __('100% genuine, licensed stock') }}</p>
      </div>
    </div>
    <div class="hero-side">
      @foreach ($cats->take(2) as $i => $c)
        @php $cimg = !empty($c->image) ? helper::image_path($c->image) : helper::food_image($c->name, $c->id, 'retail'); @endphp
        <a href="{{ URL::to($tSlug . '/categories') }}#cat-{{ $c->id }}" class="promo-card {{ $i == 0 ? 'promo-card--green' : 'promo-card--lilac' }}">
          <img src="{{ $cimg }}" alt="{{ $c->name }}">
          <div class="promo-card__body">
            <span class="promo-tag" style="background:{{ $i == 0 ? 'var(--rx)' : 'var(--accent)' }}">{{ $items->where('cat_id', $c->id)->count() }} {{ __('products') }}</span>
            <h3>{{ $c->name }}</h3>
            <span class="link-arrow mt-1">{{ __('Shop now') }} <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
          </div>
        </a>
      @endforeach
    </div>
  </div>
</section>

<!-- TRUST -->
<section class="section-tight" style="padding-top:0">
  <div class="container">
    <div class="trust-row">
      <div class="trust"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l8 3v6c0 5-3.4 8.4-8 9.6C7.4 20.4 4 17 4 12V6z"/><path d="M9 12l2 2 4-4"/></svg></span><div><strong>{{ __('100% genuine') }}</strong><span>{{ __('Licensed distributors') }}</span></div></div>
      <div class="trust"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 16V8a2 2 0 012-2h9v10H3zM14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/></svg></span><div><strong>{{ __('Fast delivery') }}</strong><span>{{ __('To your door') }}</span></div></div>
      <div class="trust"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"><rect x="4" y="10" width="16" height="11" rx="2.5"/><path d="M8 10V7a4 4 0 018 0v3"/></svg></span><div><strong>{{ __('Secure checkout') }}</strong><span>{{ __('Card, wallet or cash') }}</span></div></div>
      <div class="trust"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a10 10 0 00-8.6 15L2 22l5.2-1.4A10 10 0 1012 2z"/></svg></span><div><strong>{{ __('Order on WhatsApp') }}</strong><span>{{ __('One tap to order') }}</span></div></div>
    </div>
  </div>
</section>

<!-- SHOP BY CATEGORY -->
@if ($cats->count())
<section class="section-tight">
  <div class="container">
    <div class="head-row">
      <div class="section-head" style="margin-bottom:0"><h2>{{ __('Shop by category') }}</h2><p class="mt-1">{{ __('Jump straight to what you need.') }}</p></div>
      <a href="{{ URL::to($tSlug . '/categories') }}" class="link-arrow">{{ __('All categories') }} <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
    </div>
    <div class="need-grid reveal">
      @foreach ($cats->take(12) as $c)
        <a href="{{ URL::to($tSlug . '/categories') }}#cat-{{ $c->id }}" class="need">
          <span class="ic">{!! $needIcon !!}</span>
          <strong>{{ $c->name }}</strong><span>{{ $items->where('cat_id', $c->id)->count() }} {{ __('products') }}</span>
        </a>
      @endforeach
    </div>
  </div>
</section>
@endif

<!-- POPULAR -->
@if ($picks->count())
<section class="section-tight">
  <div class="container">
    <div class="head-row reveal">
      <div class="section-head" style="margin-bottom:0"><span class="eyebrow">{{ __('Best sellers') }}</span><h2>{{ __('Popular right now') }}</h2></div>
      @if ($cats->count() > 1)
        <div class="chip-row" data-filter-group>
          <button class="chip active" data-filter="all">{{ __('All') }}</button>
          @foreach ($cats->take(5) as $c)<button class="chip" data-filter="cat{{ $c->id }}">{{ $c->name }}</button>@endforeach
        </div>
      @endif
    </div>
    <div class="grid g-6 products reveal">
      @foreach ($picks as $item)
        @include('front.template-7.partials.product_card', ['item' => $item, 'catNames' => $catNames])
      @endforeach
    </div>
    <div class="text-center mt-4"><a href="{{ URL::to($tSlug . '/categories') }}" class="btn btn-ghost btn-lg">{{ __('Shop all products') }}</a></div>
  </div>
</section>
@endif

<!-- HOW IT WORKS -->
<section class="section bg-alt">
  <div class="container">
    <div class="section-head center reveal"><span class="eyebrow">{{ __('How ordering works') }}</span><h2>{{ __('Delivered in four simple steps') }}</h2></div>
    <div class="steps reveal">
      <div class="step"><span class="n">1</span><h4>{{ __('Find what you need') }}</h4><p>{{ __('Search by product or brand, or browse by category.') }}</p><span class="when">{{ __('30 seconds') }}</span></div>
      <div class="step"><span class="n">2</span><h4>{{ __('Add to your basket') }}</h4><p>{{ __('Pick what you need and set the quantity right from the grid.') }}</p><span class="when">{{ __('One tap') }}</span></div>
      <div class="step"><span class="n">3</span><h4>{{ __('Checkout your way') }}</h4><p>{{ __('Card, wallet, bank transfer or cash — or order on WhatsApp.') }}</p><span class="when">{{ __('About a minute') }}</span></div>
      <div class="step"><span class="n">4</span><h4>{{ __('Delivered to you') }}</h4><p>{{ __('Packed discreetly and delivered to your door, with tracking.') }}</p><span class="when">{{ __('Same day') }}</span></div>
    </div>
  </div>
</section>

<!-- STATS -->
<section class="section-tight">
  <div class="container">
    <div class="stat-row reveal">
      <div class="stat"><b><span data-count="{{ max($items->count(), 1) }}">0</span></b><span>{{ __('Products in stock') }}</span></div>
      <div class="stat"><b><span data-count="{{ max($cats->count(), 1) }}">0</span></b><span>{{ __('Categories') }}</span></div>
      <div class="stat"><b><span data-count="100" data-suffix="%">0</span></b><span>{{ __('Genuine stock') }}</span></div>
      <div class="stat"><b><span data-count="4.9">0</span></b><span>{{ __('Average rating') }}</span></div>
    </div>
  </div>
</section>

<!-- REVIEWS -->
@if ($reviews->count())
<section class="section-tight">
  <div class="container">
    <div class="head-row"><div class="section-head" style="margin-bottom:0"><span class="eyebrow">{{ __('Trusted by customers') }}</span><h2>{{ __('What customers say') }}</h2></div></div>
    <div class="tst-scroller reveal">
      @foreach ($reviews as $rv)
        <article class="testimonial">
          <span class="stars">@for ($s = 0; $s < (int) ($rv->star ?? 5); $s++)<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.9 21l1.2-6.8-5-4.9 6.9-1z"/></svg>@endfor</span>
          <p>"{{ \Illuminate\Support\Str::limit($rv->description, 180) }}"</p>
          <div class="who">@if (!empty($rv->image))<img src="{{ helper::image_path($rv->image) }}" alt="">@endif<div><strong>{{ $rv->name }}</strong><span>{{ __('Verified customer') }}</span></div></div>
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
          <span class="eyebrow">{{ __('Feel better, faster') }}</span>
          <h2 class="mt-2">{{ __('Your medicines, delivered to your door.') }}</h2>
          <p>{{ __('Browse and order in minutes — or send your list straight to WhatsApp.') }}</p>
        </div>
        <div class="flex gap-1 wrap">
          <a href="{{ URL::to($tSlug . '/categories') }}" class="btn btn-primary btn-lg">{{ __('Shop medicines') }}</a>
          @if (!empty($tWa))<a href="https://wa.me/{{ $tWa }}" class="btn btn-ghost btn-lg" style="color:#fff;border-color:rgba(255,255,255,.28)"><svg viewBox="0 0 24 24" fill="currentColor" style="width:18px;height:18px"><path d="M12 2a10 10 0 00-8.6 15L2 22l5.2-1.4A10 10 0 1012 2z"/></svg> {{ __('Order on WhatsApp') }}</a>@endif
        </div>
      </div>
    </div>
  </div>
</section>
