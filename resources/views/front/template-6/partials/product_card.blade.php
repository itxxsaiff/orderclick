@php
    $catName = $catNames[$item->cat_id] ?? '';
    $up = @$item['item_image']->image;
    $img = !empty($up) ? helper::image_path($up) : helper::food_image($item->item_name . ' ' . $catName, $item->id, 'retail');
    // alt (hover) image = second uploaded image if present, else the same
    $altImg = $img;
    if (isset($item['multi_image']) && $item['multi_image']->count() > 0) {
        $altImg = helper::image_path($item['multi_image'][0]->image);
    }
    $detail = URL::to($tSlug . '/details-' . $item->slug);
    $hasVar = isset($item['variation']) && $item['variation']->count() > 0;
    $price = $hasVar ? $item['variation'][0]->price : $item->item_price;
    $hasDiscount = !$hasVar && !empty($item->item_original_price) && $item->item_original_price > $item->item_price;
    $off = $hasDiscount ? round(100 - ($item->item_price / $item->item_original_price * 100)) : 0;
    $badge = null;
    if (!empty($item->is_new)) { $badge = ['new', __('New')]; }
    elseif ($hasDiscount) { $badge = ['sale', __('Sale')]; }
@endphp
<article class="product-card" data-cat="cat{{ $item->cat_id }}" data-price="{{ $price }}" data-name="{{ strtolower($item->item_name . ' ' . $catName) }}" data-pop="{{ $item->avg_ratting ?? 0 }}">
  <div class="product-card__media">
    @if ($badge)<div class="badges"><span class="badge {{ $badge[0] }}">{{ $badge[1] }}</span></div>@endif
    <button class="wish" aria-label="Add to wishlist"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 20.5l-7.1-7a4.6 4.6 0 016.5-6.5l.6.6.6-.6a4.6 4.6 0 016.5 6.5z"/></svg></button>
    <a href="{{ $detail }}">
      <img class="main" src="{{ $img }}" alt="{{ $item->item_name }}" loading="lazy">
      <img class="alt" src="{{ $altImg }}" alt="" loading="lazy">
    </a>
    <button class="quick-add" type="button" onclick="ocAdd(this)"
      data-id="{{ $item->id }}" data-name="{{ $item->item_name }}" data-price="{{ $item->item_price }}"
      data-image="{{ $img }}" data-tax="0" @if ($hasVar) data-hasvar="1" data-detail="{{ $detail }}" @endif>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 8h14l1 12H4z"/><path d="M9 8V6a3 3 0 016 0v2"/></svg><span>{{ $hasVar ? __('Options') : __('Quick add') }}</span>
    </button>
  </div>
  <div class="product-card__body">
    <span class="product-card__cat">{{ $catName }}</span>
    <h4><a href="{{ $detail }}">{{ \Illuminate\Support\Str::limit($item->item_name, 40) }}</a></h4>
    @if (!empty($item->avg_ratting))
      <div class="product-card__rating"><span class="stars">@for ($i = 0; $i < 5; $i++)<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.9 21l1.2-6.8-5-4.9 6.9-1z"/></svg>@endfor</span><span>{{ number_format($item->avg_ratting, 1) }}</span></div>
    @endif
    <span class="price">{{ $hasVar ? __('From') . ' ' : '' }}{{ helper::currency_formate($price, $storeinfo->id) }}@if ($hasDiscount)<s>{{ helper::currency_formate($item->item_original_price, $storeinfo->id) }}</s> <span class="off">−{{ $off }}%</span>@endif</span>
  </div>
</article>
