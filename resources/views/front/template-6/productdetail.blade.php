@php $tActive = 'menu'; @endphp
@extends('front.template-6.layout')

@section('styles')
<style>
  .pill-opts { display:flex; flex-wrap:wrap; gap:10px; }
  .pill-opt { position:relative; }
  .pill-opt input { position:absolute; opacity:0; }
  .pill-opt span { display:inline-flex; gap:6px; align-items:center; padding:10px 16px; border:1.6px solid var(--line); border-radius:10px; cursor:pointer; font-weight:600; font-size:.9rem; transition:.18s; }
  .pill-opt input:checked ~ span { border-color:var(--brand); background:var(--brand); color:#fff; }
  .pill-opt span small { opacity:.7; font-weight:500; }
  .opt-group { margin-top:22px; }
  .opt-group__title { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; }
  .opt-group__title h4 { font-size:1rem; }
  .opt-group__title span { font-size:.8rem; color:var(--text-2); }
</style>
@endsection

@section('content')
    @php
        $it = $getitem;
        $catName = optional(\App\Models\Category::find($it->cat_id))->name;
        $imgs = collect($itemimages ?? []);
        $mainUp = @$it['item_image']->image ?: $it->image;
        $mainImg = !empty($mainUp) ? helper::image_path($mainUp) : helper::food_image($it->item_name . ' ' . $catName, $it->id, 'retail');
        $variation = collect($it['variation'] ?? []);
        $extras = collect($it['extras'] ?? []);
        $vjson = is_array($it->variants_json) ? $it->variants_json : [];
        $basePrice = $variation->count() ? $variation[0]->price : $it->item_price;
        $hasDiscount = !empty($it->item_original_price) && $it->item_original_price > $it->item_price;
        $symbol = trim(preg_replace('/[0-9.,]/', '', helper::currency_formate(0, $storeinfo->id)));
        $reviews = collect($itemreviewdata ?? []);
        $totalReviews = $reviews->count();
    @endphp

    <section class="page-head" style="padding-bottom:0">
      <div class="container">
        <div class="crumb"><a href="{{ $tBase }}">{{ __('Home') }}</a> <span>/</span> <a href="{{ URL::to($tSlug . '/categories') }}">{{ __('Shop') }}</a> <span>/</span> <span class="now">{{ \Illuminate\Support\Str::limit($it->item_name, 30) }}</span></div>
      </div>
    </section>

    <section style="padding-bottom:clamp(40px,6vw,72px)">
      <div class="container pd-layout" data-detail
           data-vendor="{{ $storeinfo->id }}" data-item="{{ $it->id }}" data-name="{{ $it->item_name }}" data-image="{{ $mainImg }}"
           data-base-price="{{ $basePrice }}" data-orig="{{ $it->item_original_price ?: $basePrice }}" data-tax="{{ $it->tax }}"
           data-min="{{ $it->min_order }}" data-max="{{ $it->max_order }}" data-stock="{{ $it->stock_management }}" data-symbol="{{ $symbol }}">

        <div data-gallery>
          <div class="gallery__main"><img src="{{ $mainImg }}" alt="{{ $it->item_name }}" data-gallery-main></div>
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
          <span class="product-card__cat">{{ $catName }}</span>
          <h1 style="font-size:clamp(1.8rem,3.4vw,2.6rem);margin-top:6px">{{ $it->item_name }}</h1>
          @if (!empty($it->avg_ratting))<div class="product-card__rating" style="margin-top:8px"><span class="stars">@for ($s = 0; $s < 5; $s++)<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.9 21l1.2-6.8-5-4.9 6.9-1z"/></svg>@endfor</span><span>{{ number_format($it->avg_ratting, 1) }}@if ($totalReviews) · {{ $totalReviews }} {{ __('reviews') }}@endif</span></div>@endif

          @if (!empty($it->description))<p class="lead mt-2">{{ strip_tags($it->description) }}</p>@endif

          <div class="pd-price"><span class="now" data-detail-price>{{ helper::currency_formate($basePrice, $storeinfo->id) }}</span>@if ($hasDiscount)<s>{{ helper::currency_formate($it->item_original_price, $storeinfo->id) }}</s>@endif</div>

          @if ($variation->count())
          <div class="opt-group">
            <div class="opt-group__title"><h4>{{ __('Choose an option') }}</h4><span>{{ __('Required') }}</span></div>
            <div class="pill-opts">
              @foreach ($variation as $i => $v)
                <label class="pill-opt"><input type="radio" name="oc-variant" data-vname="{{ $v->name }}" data-vprice="{{ $v->price }}" {{ $i == 0 ? 'checked' : '' }}><span>{{ $v->name }} <small>{{ helper::currency_formate($v->price, $storeinfo->id) }}</small></span></label>
              @endforeach
            </div>
          </div>
          @endif

          @foreach ($vjson as $grp)
            @if (!empty($grp['variant_options']))
            <div class="opt-group">
              <div class="opt-group__title"><h4>{{ $grp['variant_name'] ?? __('Options') }}</h4></div>
              <div class="pill-opts">
                @foreach ($grp['variant_options'] as $oi => $opt)
                  <label class="pill-opt"><input type="radio" class="oc-opt" name="oc-opt-{{ \Illuminate\Support\Str::slug($grp['variant_name'] ?? 'opt') }}" value="{{ $opt }}" {{ $oi == 0 ? 'checked' : '' }}><span>{{ $opt }}</span></label>
                @endforeach
              </div>
            </div>
            @endif
          @endforeach

          @if ($extras->count())
          <div class="opt-group">
            <div class="opt-group__title"><h4>{{ __('Add-ons') }}</h4><span>{{ __('Optional') }}</span></div>
            <div class="pill-opts">
              @foreach ($extras as $ex)
                <label class="pill-opt"><input type="checkbox" class="oc-extra" value="{{ $ex->id }}" data-ename="{{ $ex->name }}" data-eprice="{{ $ex->price }}"><span>{{ $ex->name }} <small>+{{ helper::currency_formate($ex->price, $storeinfo->id) }}</small></span></label>
              @endforeach
            </div>
          </div>
          @endif

          <div class="pd-actions">
            <div class="qty" style="padding:6px">
              <button type="button" data-detail-step="down" aria-label="Decrease"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M5 12h14"/></svg></button>
              <input type="number" value="1" min="1" aria-label="Quantity" data-detail-qty readonly>
              <button type="button" data-detail-step="up" aria-label="Increase"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg></button>
            </div>
            @if ($it->is_available == 1)
              <button class="btn btn-primary btn-lg" style="flex:1;min-width:200px" type="button" data-detail-add>{{ __('Add to bag') }} · <span data-detail-total>{{ helper::currency_formate($basePrice, $storeinfo->id) }}</span></button>
              <button class="btn btn-dark btn-lg" type="button" data-detail-buynow>{{ __('Buy now') }}</button>
            @else
              <button class="btn btn-ghost btn-lg" style="flex:1" disabled>{{ __('Sold out') }}</button>
            @endif
          </div>

          <ul class="pd-meta">
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg> {{ __('Fast delivery & easy returns') }}</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg> {{ __('Secure encrypted checkout') }}</li>
            @if (!empty($tWa))<li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg> {{ __('Questions? Message us on WhatsApp') }}</li>@endif
          </ul>
        </div>
      </div>
    </section>

    @if (!empty($it->description) || $totalReviews)
    <section class="section-tight">
      <div class="container">
        <div data-tabs>
          <div class="tabs">
            <button class="tab active" data-tab="desc">{{ __('Details') }}</button>
            @if ($totalReviews)<button class="tab" data-tab="reviews">{{ __('Reviews') }} ({{ $totalReviews }})</button>@endif
          </div>
        </div>
        <div class="tab-panel active" data-panel="desc"><div class="prose">{!! $it->description ?: '<p>' . e($it->item_name) . '</p>' !!}</div></div>
        @if ($totalReviews)
        <div class="tab-panel" data-panel="reviews"><div style="max-width:900px">
          @foreach ($reviews as $rv)
            <article class="testimonial mb-2"><div class="stars">@for ($s = 0; $s < (int) $rv->star; $s++)<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.9 21l1.2-6.8-5-4.9 6.9-1z"/></svg>@endfor</div><p>"{{ $rv->description }}"</p><div class="who"><div><strong>{{ optional($rv->user_info)->name ?? __('Guest') }}</strong><span>{{ __('Verified order') }}</span></div></div></article>
          @endforeach
        </div></div>
        @endif
      </div>
    </section>
    @endif

    @php $related = collect($getrelateditems ?? [])->take(4); @endphp
    @if ($related->count())
    <section class="section bg-alt">
      <div class="container">
        <div class="head-row"><div class="section-head" style="margin-bottom:0"><span class="eyebrow">{{ __('You may also like') }}</span><h2>{{ __('Complete the look') }}</h2></div><a href="{{ URL::to($tSlug . '/categories') }}" class="link-arrow">{{ __('Shop all') }}</a></div>
        <div class="grid g-4 products">
          @foreach ($related as $ritem)
            @include('front.template-6.partials.product_card', ['item' => $ritem, 'catNames' => collect([$it->cat_id => $catName])])
          @endforeach
        </div>
      </div>
    </section>
    @endif
@endsection

@section('scripts')
<script src="{{ url($tCss . 'js/oc-detail.js') }}"></script>
@endsection
