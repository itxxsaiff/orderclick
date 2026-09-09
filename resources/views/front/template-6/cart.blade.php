@php $tActive = 'cart'; @endphp
@extends('front.template-6.layout')

@section('content')
    @php
        $cd = collect($cartdata ?? []);
        $subtotal = $cd->sum(fn($c) => $c->price * $c->qty);
    @endphp

    <section class="page-head">
      <div class="container">
        <div class="crumb"><a href="{{ $tBase }}">{{ __('Home') }}</a> <span>/</span> <span class="now">{{ __('Bag') }}</span></div>
        <h1>{{ __('Your bag') }}</h1>
        <p>{{ __('Review your pieces before checking out.') }}</p>
      </div>
    </section>

    @if ($cd->count())
    <section style="padding-bottom:clamp(40px,6vw,72px)">
      <div class="container cart-layout">
        <div class="panel">
          <div class="panel__head">
            <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 8h14l1 12H4z"/><path d="M9 8V6a3 3 0 016 0v2"/></svg></span>
            <h3>{{ $cd->count() }} {{ $cd->count() == 1 ? __('item in your bag') : __('items in your bag') }}</h3>
            <a href="{{ URL::to($tSlug . '/categories') }}" class="link-arrow" style="margin-left:auto;font-size:.88rem">{{ __('Add more') }}</a>
          </div>
          <div class="panel__body" style="padding-top:4px" id="cartPageList">
            @foreach ($cd as $c)
              @php
                $img = filter_var($c->item_image, FILTER_VALIDATE_URL) ? $c->item_image
                    : (!empty($c->item_image) ? helper::image_path($c->item_image) : helper::food_image($c->item_name, $c->item_id, 'retail'));
                $opts = trim(collect([$c->variants_name, $c->extras_name])->filter()->implode(' · '));
              @endphp
              <div class="cart-item" data-cart-id="{{ $c->id }}" data-item-id="{{ $c->item_id }}">
                <img class="cart-item__img" src="{{ $img }}" alt="{{ $c->item_name }}">
                <div>
                  <h4><a href="{{ URL::to($tSlug . '/details-' . ($c->slug ?? $c->item_id)) }}">{{ $c->item_name }}</a></h4>
                  @if ($opts)<p class="opts">{{ $opts }}</p>@endif
                  <span class="small muted">{{ helper::currency_formate($c->price, $storeinfo->id) }} {{ __('each') }}</span>
                </div>
                <div class="cart-item__right">
                  <div class="qty">
                    <button data-oc-qty="down" aria-label="Decrease"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M5 12h14"/></svg></button>
                    <input type="number" value="{{ $c->qty }}" min="1" aria-label="Quantity" readonly>
                    <button data-oc-qty="up" aria-label="Increase"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg></button>
                  </div>
                  <span class="price">{{ helper::currency_formate($c->price * $c->qty, $storeinfo->id) }}</span>
                  <button class="remove-btn" data-oc-remove aria-label="Remove item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 7h16M9 7V5h6v2M6 7l1 13h10l1-13"/></svg></button>
                </div>
              </div>
            @endforeach
          </div>
        </div>

        <div>
          <div class="panel panel--sticky">
            <div class="panel__head"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16l-1.5 12H5.5z"/><path d="M9 7a3 3 0 016 0"/></svg></span><h3>{{ __('Order summary') }}</h3></div>
            <div class="panel__body">
              <div class="summary-row"><span>{{ __('Subtotal') }}</span><strong>{{ helper::currency_formate($subtotal, $storeinfo->id) }}</strong></div>
              <div class="summary-row"><span>{{ __('Delivery & tax') }}</span><strong class="muted" style="font-weight:600">{{ __('At checkout') }}</strong></div>
              <div class="summary-row total"><span>{{ __('Total') }}</span><strong>{{ helper::currency_formate($subtotal, $storeinfo->id) }}</strong></div>
              <a href="{{ URL::to($tSlug . '/checkout') }}" class="btn btn-primary btn-block btn-lg mt-3">{{ __('Continue to checkout') }}</a>
              <a href="{{ URL::to($tSlug . '/categories') }}" class="btn btn-ghost btn-block" style="margin-top:10px">{{ __('Keep shopping') }}</a>
            </div>
          </div>
        </div>
      </div>
    </section>
    @else
    <section style="padding:clamp(40px,7vw,90px) 0">
      <div class="container">
        <div style="text-align:center;max-width:520px;margin:0 auto">
          <div style="color:var(--brand);margin-bottom:12px"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" style="width:64px;height:64px"><path d="M5 8h14l1 12H4z"/><path d="M9 8V6a3 3 0 016 0v2"/></svg></div>
          <h2>{{ __('Your bag is empty') }}</h2>
          <p class="lead mt-1">{{ __('Nothing here yet. The collection is one tap away.') }}</p>
          <a href="{{ URL::to($tSlug . '/categories') }}" class="btn btn-primary btn-lg mt-3">{{ __('Shop the collection') }}</a>
        </div>
      </div>
    </section>
    @endif
@endsection

@section('scripts')
<script>
  document.getElementById('cartPageList')?.addEventListener('click', function (e) {
    if (e.target.closest('[data-oc-qty],[data-oc-remove]')) { setTimeout(function () { window.location.reload(); }, 500); }
  });
</script>
@endsection
