@php
    $slug = $storeinfo->slug;
@endphp
@if ($cartdata->count())
  <div class="drawer__body">
    @foreach ($cartdata as $c)
      @php
          $img = filter_var($c->item_image, FILTER_VALIDATE_URL)
              ? $c->item_image
              : (!empty($c->item_image) ? helper::image_path($c->item_image) : helper::food_image($c->item_name, $c->item_id));
          $opts = trim(collect([$c->variants_name, $c->extras_name])->filter()->implode(' · '));
      @endphp
      <div class="cart-item" data-cart-id="{{ $c->id }}" data-item-id="{{ $c->item_id }}">
        <img class="cart-item__img" src="{{ $img }}" alt="{{ $c->item_name }}">
        <div>
          <h4>{{ \Illuminate\Support\Str::limit($c->item_name, 28) }}</h4>
          @if ($opts)<p class="opts">{{ $opts }}</p>@endif
          <span class="price" style="font-size:.98rem">{{ helper::currency_formate($c->price * $c->qty, $storeinfo->id) }}</span>
        </div>
        <div class="cart-item__right">
          <div class="qty">
            <button data-oc-qty="down" aria-label="Decrease"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M5 12h14"/></svg></button>
            <input type="number" value="{{ $c->qty }}" min="1" aria-label="Quantity" readonly>
            <button data-oc-qty="up" aria-label="Increase"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg></button>
          </div>
          <button class="remove-btn" data-oc-remove aria-label="Remove"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M9 7V5h6v2M6 7l1 13h10l1-13"/></svg></button>
        </div>
      </div>
    @endforeach
  </div>

  <div class="drawer__foot">
    <div class="summary-row"><span>{{ __('Subtotal') }}</span><strong>{{ helper::currency_formate($subtotal, $storeinfo->id) }}</strong></div>
    <a href="{{ URL::to($slug . '/checkout') }}" class="btn btn-primary btn-block btn-lg mt-2">{{ __('Checkout') }} · {{ helper::currency_formate($subtotal, $storeinfo->id) }}</a>
    <a href="{{ URL::to($slug . '/cart') }}" class="btn btn-ghost btn-block" style="margin-top:8px">{{ __('View full cart') }}</a>
  </div>
@else
  <div class="drawer__body">
    <div style="text-align:center;padding:48px 20px;color:var(--muted)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" style="width:56px;height:56px;opacity:.4;margin-bottom:14px"><path d="M3 4h2l2.4 11.2a2 2 0 002 1.6h7.7a2 2 0 002-1.6L21 8H6"/><circle cx="10" cy="20" r="1.4"/><circle cx="18" cy="20" r="1.4"/></svg>
      <h4 style="margin-bottom:6px">{{ __('Your cart is empty') }}</h4>
      <p class="small muted">{{ __('Add something delicious to get started.') }}</p>
    </div>
  </div>
  <div class="drawer__foot">
    <a href="{{ URL::to($slug . '/categories') }}" class="btn btn-primary btn-block btn-lg">{{ __('Browse the menu') }}</a>
  </div>
@endif
