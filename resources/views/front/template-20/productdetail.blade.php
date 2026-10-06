@php $tActive = 'menu'; @endphp
@extends('front.template-20.layout')

@section('content')
    @php
        $it = $getitem;
        $catName = optional(\App\Models\Category::find($it->cat_id))->name;
        $imgs = collect($itemimages ?? []);
        $mainUp = @$it['item_image']->image ?: $it->image;
        $mainImg = !empty($mainUp) ? helper::image_path($mainUp) : \App\Services\StoreDesign::stockImage($it->item_name . ' ' . $catName, $it->id, $storeinfo->id ?? ($vdata ?? null));
        $variation = collect($it['variation'] ?? []);
        $extras = collect($it['extras'] ?? []);
        $vjson = is_array($it->variants_json) ? $it->variants_json : [];
        $basePrice = $variation->count() ? $variation[0]->price : $it->item_price;
        $hasDiscount = !empty($it->item_original_price) && $it->item_original_price > $it->item_price;
        $symbol = trim(preg_replace('/[0-9.,]/', '', helper::currency_formate(0, $storeinfo->id)));
        $reviews = collect($itemreviewdata ?? []);
        $totalReviews = $reviews->count();
    @endphp

    <section class="page-head">
      <div class="container">
        <div class="crumb">
          <a href="{{ $tBase }}">{{ __('Home') }}</a>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M9 6l6 6-6 6"/></svg>
          <a href="{{ URL::to($tSlug . '/categories') }}">{{ __('Menu') }}</a>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M9 6l6 6-6 6"/></svg>
          <span class="now">{{ \Illuminate\Support\Str::limit($it->item_name, 30) }}</span>
        </div>
      </div>
    </section>

    <section style="padding-bottom:clamp(40px,6vw,72px)">
      <div class="container pd-layout" data-detail
           data-vendor="{{ $storeinfo->id }}" data-item="{{ $it->id }}"
           data-name="{{ $it->item_name }}" data-image="{{ $mainImg }}"
           data-base-price="{{ $basePrice }}" data-orig="{{ $it->item_original_price ?: $basePrice }}"
           data-tax="{{ $it->tax }}" data-min="{{ $it->min_order }}" data-max="{{ $it->max_order }}"
           data-stock="{{ $it->stock_management }}" data-symbol="{{ $symbol }}">

        <div data-gallery>
          <div class="gallery__main">
            <img src="{{ $mainImg }}" alt="{{ $it->item_name }}" data-gallery-main>
          </div>
          @if ($imgs->count())
          <div class="gallery__thumbs">
            <button class="active" data-thumb="{{ $mainImg }}"><img src="{{ $mainImg }}" alt=""></button>
            @foreach ($imgs as $im)
              @php $u = $im->image_url ?? helper::image_path($im->image); @endphp
              <button data-thumb="{{ $u }}"><img src="{{ $u }}" alt=""></button>
            @endforeach
          </div>
          @endif
        </div>

        <div>
          <div class="flex items-center gap-2 wrap mb-2">
            <span class="open-badge"><span class="pulse"></span> {{ $it->is_available == 1 ? __('Available now') : __('Currently unavailable') }}</span>
            @if (!empty($it->avg_ratting))<span class="small muted">⭐ {{ number_format($it->avg_ratting, 1) }}@if ($totalReviews) · {{ $totalReviews }} {{ __('reviews') }}@endif</span>@endif
          </div>
          <h1 style="font-size:clamp(1.9rem,3.6vw,2.8rem)">{{ $it->item_name }}</h1>

          @if (!empty($it->description))
            <p class="lead mt-2">{{ strip_tags($it->description) }}</p>
          @endif

          <div class="pd-price">
            <span class="now" data-detail-price>{{ helper::currency_formate($basePrice, $storeinfo->id) }}</span>
            @if ($hasDiscount)<s>{{ helper::currency_formate($it->item_original_price, $storeinfo->id) }}</s>@endif
          </div>

          {{-- Priced variations (Variants table) --}}
          @if ($variation->count())
          <div class="opt-group">
            <div class="opt-group__title"><h4>{{ __('Choose an option') }}</h4><span>{{ __('Required') }}</span></div>
            <div class="pill-opts">
              @foreach ($variation as $i => $v)
                <label class="pill-opt">
                  <input type="radio" name="oc-variant" data-vname="{{ $v->name }}" data-vprice="{{ $v->price }}" {{ $i == 0 ? 'checked' : '' }}>
                  <span>{{ $v->name }} <small>{{ helper::currency_formate($v->price, $storeinfo->id) }}</small></span>
                </label>
              @endforeach
            </div>
          </div>
          @endif

          {{-- Attribute options (variants_json) --}}
          @foreach ($vjson as $grp)
            @if (!empty($grp['variant_options']))
            <div class="opt-group">
              <div class="opt-group__title"><h4>{{ $grp['variant_name'] ?? __('Options') }}</h4></div>
              <div class="pill-opts">
                @foreach ($grp['variant_options'] as $oi => $opt)
                  <label class="pill-opt">
                    <input type="radio" class="oc-opt" name="oc-opt-{{ \Illuminate\Support\Str::slug($grp['variant_name'] ?? 'opt') }}" value="{{ $opt }}" {{ $oi == 0 ? 'checked' : '' }}>
                    <span>{{ $opt }}</span>
                  </label>
                @endforeach
              </div>
            </div>
            @endif
          @endforeach

          {{-- Extras --}}
          @if ($extras->count())
          <div class="opt-group">
            <div class="opt-group__title"><h4>{{ __('Add extras') }}</h4><span>{{ __('Optional') }}</span></div>
            <div class="pill-opts">
              @foreach ($extras as $ex)
                <label class="pill-opt">
                  <input type="checkbox" class="oc-extra" value="{{ $ex->id }}" data-ename="{{ $ex->name }}" data-eprice="{{ $ex->price }}">
                  <span>{{ $ex->name }} <small>+{{ helper::currency_formate($ex->price, $storeinfo->id) }}</small></span>
                </label>
              @endforeach
            </div>
          </div>
          @endif

          <div class="pd-actions">
            <div class="qty" style="padding:6px">
              <button type="button" data-detail-step="down" aria-label="Decrease"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M5 12h14"/></svg></button>
              <input type="number" value="1" min="1" aria-label="Quantity" data-detail-qty readonly>
              <button type="button" data-detail-step="up" aria-label="Increase"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg></button>
            </div>
            @if ($it->is_available == 1)
              <button class="btn btn-primary btn-lg" style="flex:1;min-width:200px" type="button" data-detail-add>{{ __('Add to cart') }} · <span data-detail-total>{{ helper::currency_formate($basePrice, $storeinfo->id) }}</span></button>
              <button class="btn btn-dark btn-lg" type="button" data-detail-buynow>{{ __('Buy now') }}</button>
            @else
              <button class="btn btn-ghost btn-lg" style="flex:1" disabled>{{ __('Unavailable') }}</button>
            @endif
          </div>

          <ul class="pd-meta">
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg> {{ __('Freshly prepared to order') }}</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg> {{ __('Secure encrypted checkout') }}</li>
            @if (!empty($tWa))<li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg> {{ __('Questions? Message us on WhatsApp') }}</li>@endif
          </ul>
        </div>
      </div>
    </section>

    {{-- Description + reviews --}}
    @if (!empty($it->description) || $totalReviews)
    <section class="section-tight">
      <div class="container">
        <div data-tabs>
          <div class="tabs">
            <button class="tab active" data-tab="desc">{{ __('Description') }}</button>
            @if ($totalReviews)<button class="tab" data-tab="reviews">{{ __('Reviews') }} ({{ $totalReviews }})</button>@endif
          </div>
        </div>
        <div class="tab-panel active" data-panel="desc">
          <div class="prose">{!! $it->description ?: '<p>' . e($it->item_name) . '</p>' !!}</div>
        </div>
        @if ($totalReviews)
        <div class="tab-panel" data-panel="reviews">
          <div style="max-width:900px">
            @foreach ($reviews as $rv)
              <article class="testimonial mb-2">
                <div class="stars">@for ($s = 0; $s < (int) $rv->star; $s++)<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.9 21l1.2-6.8-5-4.9 6.9-1z"/></svg>@endfor</div>
                <p>"{{ $rv->description }}"</p>
                <div class="who"><div><strong>{{ optional($rv->user_info)->name ?? __('Guest') }}</strong><span>{{ __('Verified order') }}</span></div></div>
              </article>
            @endforeach
          </div>
        </div>
        @endif
      </div>
    </section>
    @endif

    {{-- Related --}}
    @php $related = collect($getrelateditems ?? [])->take(4); @endphp
    @if ($related->count())
    <section class="section bg-alt">
      <div class="container">
        <div class="head-row">
          <div class="section-head" style="margin-bottom:0">
            <span class="eyebrow">{{ __('Goes well with') }}</span>
            <h2>{{ __('Round out the order') }}</h2>
          </div>
          <a href="{{ URL::to($tSlug . '/categories') }}" class="link-arrow">{{ __('Full menu') }} <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
        </div>
        <div class="grid g-4 products">
          @foreach ($related as $ritem)
            @include('front.template-20.partials.product_card', ['item' => $ritem, 'catNames' => collect([$it->cat_id => $catName])])
          @endforeach
        </div>
      </div>
    </section>
    @endif
@endsection

@section('scripts')
<script src="{{ url(env('ASSETSPATHURL') . 'web-assets/templates/restaurant/js/oc-detail.js') }}"></script>
@endsection
