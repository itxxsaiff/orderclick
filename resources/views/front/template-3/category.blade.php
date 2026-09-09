@php $tActive = 'menu'; @endphp
@extends('front.template-3.layout')

@section('styles')
<style>
  #productGrid.as-list { grid-template-columns: 1fr !important; }
  #productGrid.as-list .product-card { flex-direction: row; }
  #productGrid.as-list .product-card__media { width: 190px; flex: 0 0 190px; }
  @media (max-width:560px){ #productGrid.as-list .product-card__media { width: 120px; flex-basis:120px; } }
</style>
@endsection

@section('content')
    @php
        $cats = collect($getcategory ?? []);
        $items = collect($getitem ?? []);
        $catNames = $cats->pluck('name', 'id');
        // price bounds for the range slider
        $prices = $items->map(function ($i) {
            $hasVar = isset($i['variation']) && $i['variation']->count() > 0;
            return (float) ($hasVar ? $i['variation'][0]->price : $i->item_price);
        });
        $maxPrice = ceil(($prices->max() ?: 100));
        $symbol = helper::currency_formate(0, $storeinfo->id);
        $symbol = trim(preg_replace('/[0-9.,]/', '', $symbol));
    @endphp

    <section class="page-head">
      <div class="container">
        <div class="crumb">
          <a href="{{ $tBase }}">{{ __('Home') }}</a>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M9 6l6 6-6 6"/></svg>
          <span class="now">{{ __('Menu') }}</span>
        </div>
        <div class="head-row">
          <div class="section-head" style="margin-bottom:0">
            <span class="eyebrow">{{ $items->count() }} {{ __('dishes') }} · {{ __('updated daily') }}</span>
            <h1>{{ __('The full menu') }}</h1>
            <p>{{ __('Everything is cooked to order. Filter by category or price — the kitchen handles the rest.') }}</p>
          </div>
          <div class="input-icon" style="min-width:280px;flex:1;max-width:360px">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
            <input class="input" type="search" placeholder="{{ __('Search dishes…') }}" data-menu-search>
          </div>
        </div>
      </div>
    </section>

    <section class="section-tight" style="padding-top:0">
      <div class="container shop-layout">

        <!-- FILTERS -->
        <aside class="panel filters-panel" id="filtersPanel">
          <div class="panel__body">
            @if ($cats->count())
            <div class="filter-group">
              <h5>{{ __('Category') }}</h5>
              @foreach ($cats as $c)
                @php $cnt = $items->where('cat_id', $c->id)->count(); @endphp
                <label class="check">
                  <input type="checkbox" data-cat-filter="cat{{ $c->id }}" checked>
                  <span class="box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span>
                  <span class="lbl">{{ $c->name }}</span><span class="num">{{ $cnt }}</span>
                </label>
              @endforeach
            </div>
            @endif

            <div class="filter-group">
              <h5>{{ __('Price range') }}</h5>
              <input type="range" min="0" max="{{ $maxPrice }}" value="{{ $maxPrice }}" data-price-filter data-symbol="{{ $symbol }}">
              <div class="flex justify-between small muted mt-1">
                <span>{{ $symbol }}0.00</span>
                <span>{{ __('Up to') }} <b data-price-out>{{ $symbol }}{{ number_format($maxPrice, 2) }}</b></span>
              </div>
            </div>

            <button class="btn btn-ghost btn-block" data-menu-reset style="margin-top:8px">{{ __('Reset all') }}</button>
          </div>
        </aside>

        <!-- RESULTS -->
        <div>
          <div class="toolbar">
            <span class="count">{{ __('Showing') }} <b data-menu-count>{{ $items->count() }}</b> {{ __('of') }} <b>{{ $items->count() }}</b> {{ __('dishes') }}</span>
            <div class="toolbar-right">
              <button class="btn btn-soft btn-sm" data-menu-filtertoggle id="mobFilterBtn" style="display:none">{{ __('Filters') }}</button>
              <select class="select btn-sm" style="width:auto;padding-block:9px" aria-label="Sort by" data-menu-sort>
                <option value="popular">{{ __('Sort: Most popular') }}</option>
                <option value="price-asc">{{ __('Price: low to high') }}</option>
                <option value="price-desc">{{ __('Price: high to low') }}</option>
                <option value="rating">{{ __('Top rated') }}</option>
              </select>
              <div class="view-toggle">
                <button class="active" data-menu-view="grid" aria-label="Grid view"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7.5" height="7.5" rx="2"/><rect x="13.5" y="3" width="7.5" height="7.5" rx="2"/><rect x="3" y="13.5" width="7.5" height="7.5" rx="2"/><rect x="13.5" y="13.5" width="7.5" height="7.5" rx="2"/></svg></button>
                <button data-menu-view="list" aria-label="List view"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg></button>
              </div>
            </div>
          </div>

          @if ($cats->count() > 1)
            <div class="hero-chips mb-3">
              <button class="chip active" data-menu-chip="all">{{ __('All dishes') }}</button>
              @foreach ($cats as $c)
                <button class="chip" data-menu-chip="cat{{ $c->id }}">{{ $c->name }}</button>
              @endforeach
            </div>
          @endif

          @if ($items->count())
            <div class="grid g-3 products" id="productGrid">
              @foreach ($items as $item)
                @include('front.template-3.partials.product_card', ['item' => $item, 'catNames' => $catNames])
              @endforeach
            </div>
            <div data-menu-empty style="display:none;text-align:center;padding:60px 20px" class="muted">
              <h3>{{ __('No dishes match your filters') }}</h3>
              <p>{{ __('Try widening the price range or clearing a category.') }}</p>
              <button class="btn btn-dark mt-2" data-menu-reset>{{ __('Reset filters') }}</button>
            </div>
          @else
            <div style="text-align:center;padding:60px 20px" class="muted">
              <h3>{{ __('The menu is being prepared') }}</h3>
              <p>{{ __('Please check back soon.') }}</p>
            </div>
          @endif
        </div>
      </div>
    </section>

    @if (!empty($tWa))
    <section class="section-tight">
      <div class="container">
        <div class="cta-band">
          <div class="cta-band__inner">
            <div>
              <h2>{{ __("Can't decide?") }}</h2>
              <p>{{ __('Message us your budget and cravings — we\'ll build the box and send you the total before we cook.') }}</p>
            </div>
            <a href="https://wa.me/{{ $tWa }}" class="btn btn-primary btn-lg">{{ __('Message us on WhatsApp') }}</a>
          </div>
        </div>
      </div>
    </section>
    @endif
@endsection

@section('scripts')
<script src="{{ url(env('ASSETSPATHURL') . 'web-assets/templates/restaurant/js/oc-menu.js') }}"></script>
<script>
  (function () {
    var b = document.getElementById('mobFilterBtn');
    if (!b) return;
    var sync = function () { b.style.display = window.innerWidth <= 1100 ? '' : 'none'; };
    sync(); window.addEventListener('resize', sync);
  })();
</script>
@endsection
