@php
    $catName = $catNames[$item->cat_id] ?? '';
    $up = @$item['item_image']->image;
    $ocFood = in_array(optional($tApp ?? null)->business_type, ['food', 'cafe', 'grocery', null, ''], true);
    $img = !empty($up) ? helper::image_path($up) : ($ocFood ? helper::food_image($item->item_name . ' ' . $catName, $item->id) : null);
    $detail = URL::to($tSlug . '/details-' . $item->slug);
    $hasVar = isset($item['variation']) && $item['variation']->count() > 0;
    $price = $hasVar ? $item['variation'][0]->price : $item->item_price;
    $hasDiscount = !$hasVar && !empty($item->discount) && $item->discount > 0 && $item->discount < $item->item_price;
    $tag = null;
    if (!empty($item->is_new)) { $tag = ['new', __('New')]; }
    elseif ($hasDiscount) { $tag = ['hot', __('Deal')]; }
@endphp
<article class="product-card" data-cat="cat{{ $item->cat_id }}" data-price="{{ $price }}" data-name="{{ strtolower($item->item_name . ' ' . $catName) }}" data-pop="{{ $item->avg_ratting ?? 0 }}">
  <div class="product-card__media">
    @if ($tag)<span class="tag {{ $tag[0] }}">{{ $tag[1] }}</span>@endif
    <a href="{{ $detail }}">@if ($img)<img src="{{ $img }}" alt="{{ $item->item_name }}" loading="lazy">@else<span class="oce-ph">{{ mb_strtoupper(mb_substr($item->item_name, 0, 1)) }}</span>@endif</a>
  </div>
  <div class="product-card__body">
    <div class="product-card__meta">
      <span>{{ $catName }}</span>
      @if (!empty($item->avg_ratting))<i class="dot"></i><span>⭐ {{ number_format($item->avg_ratting, 1) }}</span>@endif
    </div>
    <h4><a href="{{ $detail }}">{{ \Illuminate\Support\Str::limit($item->item_name, 30) }}</a></h4>
    @if (!empty($item->description))<p class="product-card__desc">{{ \Illuminate\Support\Str::limit(strip_tags($item->description), 72) }}</p>@endif
    <div class="product-card__foot">
      <span class="price">{{ $hasVar ? __('From') . ' ' : '' }}{{ helper::currency_formate($price, $storeinfo->id) }}@if ($hasDiscount)<s>{{ helper::currency_formate($item->item_price, $storeinfo->id) }}</s>@endif</span>
      @if (($tFlow ?? 'orders') === 'service')
      {{-- Service businesses take requests, not cart orders. --}}
      <a class="add-btn" href="{{ URL::to($tSlug . '/service') }}?service={{ $item->id }}"><span>{{ __('Request') }}</span></a>
      @else
      <button class="add-btn" type="button" onclick="ocAdd(this)"
        data-id="{{ $item->id }}"
        data-name="{{ $item->item_name }}"
        data-price="{{ $item->item_price }}"
        data-image="{{ $img ?: helper::image_path('') }}"
        data-tax="0"
        @if ($hasVar) data-hasvar="1" data-detail="{{ $detail }}" @endif>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg><span>{{ $hasVar ? __('Options') : __('Add') }}</span>
      </button>
      @endif
    </div>
  </div>
</article>
