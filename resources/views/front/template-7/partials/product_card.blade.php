@php
    $catName = $catNames[$item->cat_id] ?? '';
    $up = @$item['item_image']->image;
    $img = !empty($up) ? helper::image_path($up) : helper::food_image($item->item_name . ' ' . $catName, $item->id, 'retail');
    $detail = URL::to($tSlug . '/details-' . $item->slug);
    $hasVar = isset($item['variation']) && $item['variation']->count() > 0;
    $price = $hasVar ? $item['variation'][0]->price : $item->item_price;
    $hasDiscount = !$hasVar && !empty($item->item_original_price) && $item->item_original_price > $item->item_price;
    $off = $hasDiscount ? round(100 - ($item->item_price / $item->item_original_price * 100)) : 0;
    $inStock = $item->is_available == 1 && (!$item->stock_management || $item->qty > 0);
@endphp
<article class="p-card {{ $inStock ? '' : 'out' }}" data-cat="cat{{ $item->cat_id }}" data-price="{{ $price }}" data-name="{{ strtolower($item->item_name . ' ' . $catName) }}" data-pop="{{ $item->avg_ratting ?? 0 }}">
  <div class="p-card__media">
    <div class="tags">
      @if (!empty($item->is_new))<span class="tag new">{{ __('New') }}</span>@elseif ($hasDiscount)<span class="tag save">{{ __('Save') }} {{ $off }}%</span>@endif
    </div>
    <button class="fav" aria-label="Save"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 20.5l-7.1-7a4.6 4.6 0 016.5-6.5l.6.6.6-.6a4.6 4.6 0 016.5 6.5z"/></svg></button>
    <a href="{{ $detail }}"><img src="{{ $img }}" alt="{{ $item->item_name }}" loading="lazy"></a>
  </div>
  <div class="p-card__body">
    @if ($catName)<span class="p-card__brand">{{ $catName }}</span>@endif
    <h4><a href="{{ $detail }}">{{ \Illuminate\Support\Str::limit($item->item_name, 42) }}</a></h4>
    @if (!empty($item->avg_ratting))
      <div class="p-card__rating"><span class="stars">@for ($i = 0; $i < 5; $i++)<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.9 21l1.2-6.8-5-4.9 6.9-1z"/></svg>@endfor</span><span>{{ number_format($item->avg_ratting, 1) }}</span></div>
    @endif
    <span class="stock-note {{ $inStock ? 'good' : '' }}">{{ $inStock ? __('In stock') : __('Out of stock') }}</span>
    <div class="p-card__foot">
      <span class="price">{{ helper::currency_formate($price, $storeinfo->id) }}@if ($hasDiscount)<s>{{ helper::currency_formate($item->item_original_price, $storeinfo->id) }}</s>@endif</span>
      @if ($inStock)
        <div class="add-wrap" data-name="{{ $item->item_name }}">
          <button class="add-btn" type="button" onclick="event.stopPropagation(); ocAdd(this)"
            data-id="{{ $item->id }}" data-name="{{ $item->item_name }}" data-price="{{ $item->item_price }}"
            data-image="{{ $img }}" data-tax="0" @if ($hasVar) data-hasvar="1" data-detail="{{ $detail }}" @endif>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>{{ $hasVar ? __('Options') : __('Add') }}
          </button>
        </div>
      @endif
    </div>
  </div>
</article>
