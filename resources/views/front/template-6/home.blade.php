@php
    $cats = collect($getcategory ?? []);
    $items = collect($getitem ?? []);
    $catNames = $cats->pluck('name', 'id');
    $heroBanner = optional(collect($bannerimage ?? [])->first())->image;
    $heroImg = !empty($heroBanner) ? helper::image_path($heroBanner) : helper::food_image($tName . ' fashion store', $storeinfo->id, 'retail');
    $arrivals = $items->take(8);
@endphp

<!-- HERO -->
<section class="hero">
  <div class="hero-split">
    <div class="hero-copy">
      @if (!empty($tApp->tag_line))<span class="eyebrow">{{ $tApp->tag_line }}</span>@endif
      <h1>{{ $tName }}</h1>
      <p class="lead">{{ !empty($tDesc) ? \Illuminate\Support\Str::limit(strip_tags($tDesc), 220) : __('Considered pieces, made to last. Browse the collection and order in a few taps.') }}</p>
      <div class="hero-cta">
        <a href="{{ URL::to($tSlug . '/categories') }}" class="btn btn-dark btn-lg">{{ __('Shop the collection') }} <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
        @if (!empty($tWa))<a href="https://wa.me/{{ $tWa }}" class="btn btn-ghost btn-lg">{{ __('Order on WhatsApp') }}</a>@endif
      </div>
      <div class="hero-stats">
        <div><b>{{ $items->count() }}</b><span>{{ __('Products') }}</span></div>
        <div><b>{{ $cats->count() }}</b><span>{{ __('Categories') }}</span></div>
        <div><b>4.9</b><span>{{ __('Average review') }}</span></div>
      </div>
    </div>
    <div class="hero-visual">
      <img src="{{ $heroImg }}" alt="{{ $tName }}">
    </div>
  </div>
</section>

<!-- USP STRIP -->
<section class="usp-strip">
  <div class="container">
    <div class="grid g-4" style="gap:0">
      <div class="usp-item"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 16V8a2 2 0 012-2h9v10H3zM14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/></svg></span><div><strong>{{ __('Fast delivery') }}</strong><span>{{ __('Quick & reliable') }}</span></div></div>
      <div class="usp-item"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 113.2 6.9M3 20v-5h5"/></svg></span><div><strong>{{ __('Easy returns') }}</strong><span>{{ __('Hassle-free') }}</span></div></div>
      <div class="usp-item"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><rect x="4" y="10" width="16" height="11" rx="2.5"/><path d="M8 10V7a4 4 0 018 0v3"/></svg></span><div><strong>{{ __('Secure checkout') }}</strong><span>{{ __('Card, wallet or cash') }}</span></div></div>
      <div class="usp-item"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M12 2a10 10 0 00-8.6 15L2 22l5.2-1.4A10 10 0 1012 2z"/></svg></span><div><strong>{{ __('Order on WhatsApp') }}</strong><span>{{ __('One tap to order') }}</span></div></div>
    </div>
  </div>
</section>

<!-- CATEGORIES -->
@if ($cats->count())
<section class="section">
  <div class="container">
    <div class="head-row reveal">
      <div class="section-head">
        <span class="eyebrow">{{ __('Shop by category') }}</span>
        <h2>{{ __('Start somewhere') }}</h2>
        <p>{{ __('Browse the collection by category.') }}</p>
      </div>
      <a href="{{ URL::to($tSlug . '/categories') }}" class="link-arrow">{{ __('View everything') }}</a>
    </div>
    <div class="cat-grid reveal">
      @foreach ($cats->take(4) as $c)
        @php
          $cimg = !empty($c->image) ? helper::image_path($c->image) : helper::food_image($c->name, $c->id, 'retail');
          $cnt = $items->where('cat_id', $c->id)->count();
        @endphp
        <a href="{{ URL::to($tSlug . '/categories') }}#cat-{{ $c->id }}" class="cat-tile">
          <img src="{{ $cimg }}" alt="{{ $c->name }}" loading="lazy">
          <span class="cat-tile__label"><strong>{{ $c->name }}</strong><span>{{ $cnt }} {{ __('pieces') }}</span><span class="go">{{ __('Shop now') }} <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span></span>
        </a>
      @endforeach
    </div>
  </div>
</section>
@endif

<!-- NEW ARRIVALS -->
@if ($arrivals->count())
<section class="section bg-alt">
  <div class="container">
    <div class="head-row reveal">
      <div class="section-head">
        <span class="eyebrow">{{ __('Just landed') }}</span>
        <h2>{{ __('New this week') }}</h2>
      </div>
      @if ($cats->count() > 1)
        <div class="chip-row" data-filter-group>
          <button class="chip active" data-filter="all">{{ __('All') }}</button>
          @foreach ($cats->take(4) as $c)
            <button class="chip" data-filter="cat{{ $c->id }}">{{ $c->name }}</button>
          @endforeach
        </div>
      @endif
    </div>
    <div class="grid g-4 products reveal">
      @foreach ($arrivals as $item)
        @include('front.template-6.partials.product_card', ['item' => $item, 'catNames' => $catNames])
      @endforeach
    </div>
    <div class="text-center mt-4"><a href="{{ URL::to($tSlug . '/categories') }}" class="btn btn-ghost btn-lg">{{ __('Shop all products') }}</a></div>
  </div>
</section>
@endif

<!-- HOW IT WORKS -->
<section class="section">
  <div class="container">
    <div class="section-head center reveal">
      <span class="eyebrow">{{ __('Ordering') }}</span>
      <h2>{{ __('Simple, and reversible') }}</h2>
      <p>{{ __('Browse, order in a few taps, and pay your way — with easy returns if it\'s not right.') }}</p>
    </div>
    <div class="steps reveal">
      <div class="step"><h4>{{ __('Find your size') }}</h4><p>{{ __('Every product page has the details and options you need to choose with confidence.') }}</p></div>
      <div class="step"><h4>{{ __('Order your way') }}</h4><p>{{ __('Checkout with card, wallet, bank transfer or cash — or send your order straight to WhatsApp.') }}</p></div>
      <div class="step"><h4>{{ __('Delivered to you') }}</h4><p>{{ __('Fast delivery to your door, with tracking so you always know where your order is.') }}</p></div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="section-tight">
  <div class="container">
    <div class="editorial reveal">
      <img src="{{ helper::food_image($tName . ' editorial fashion', $storeinfo->id + 7, 'retail') }}" alt="{{ $tName }}">
      <div class="editorial__body">
        <span class="eyebrow">{{ __('New season') }}</span>
        <h2 class="mt-2">{{ __('Your next favourite is a tap away.') }}</h2>
        <p>{{ __('Browse the full collection and order in minutes — or message us on WhatsApp and we\'ll help you pick.') }}</p>
        <a href="{{ URL::to($tSlug . '/categories') }}" class="btn btn-primary btn-lg mt-3">{{ __('Shop the collection') }}</a>
      </div>
    </div>
  </div>
</section>
