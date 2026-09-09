@php $tActive = 'cart'; @endphp
@extends('front.template-3.layout')

@section('content')
    @php
        $cd = collect($cartdata ?? []);
        $subtotal = $cd->sum(fn($c) => $c->price * $c->qty);
    @endphp

    <section class="page-head">
      <div class="container">
        <div class="crumb">
          <a href="{{ $tBase }}">{{ __('Home') }}</a>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M9 6l6 6-6 6"/></svg>
          <span class="now">{{ __('Cart') }}</span>
        </div>
        <h1>{{ __('Your cart') }}</h1>
        <p>{{ __('One kitchen, one delivery. Change anything you like before checking out.') }}</p>
      </div>
    </section>

    @if ($cd->count())
    <section style="padding-bottom:clamp(40px,6vw,72px)">
      <div class="container cart-layout">

        <div class="panel">
          <div class="panel__head">
            <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 4h2l2.4 11.2a2 2 0 002 1.6h7.7a2 2 0 002-1.6L21 8H6"/><circle cx="10" cy="20" r="1.4"/><circle cx="18" cy="20" r="1.4"/></svg></span>
            <h3>{{ $cd->count() }} {{ $cd->count() == 1 ? __('item in your order') : __('items in your order') }}</h3>
            <a href="{{ URL::to($tSlug . '/categories') }}" class="link-arrow" style="margin-left:auto;font-size:.88rem">{{ __('Add more') }}</a>
          </div>
          <div class="panel__body" style="padding-top:4px" id="cartPageList">
            @foreach ($cd as $c)
              @php
                $img = filter_var($c->item_image, FILTER_VALIDATE_URL)
                    ? $c->item_image
                    : (!empty($c->item_image) ? helper::image_path($c->item_image) : helper::food_image($c->item_name, $c->item_id));
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
                    <button data-oc-qty="down" aria-label="Decrease"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M5 12h14"/></svg></button>
                    <input type="number" value="{{ $c->qty }}" min="1" aria-label="Quantity" readonly>
                    <button data-oc-qty="up" aria-label="Increase"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg></button>
                  </div>
                  <span class="price">{{ helper::currency_formate($c->price * $c->qty, $storeinfo->id) }}</span>
                  <button class="remove-btn" data-oc-remove aria-label="Remove item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M9 7V5h6v2M6 7l1 13h10l1-13"/></svg></button>
                </div>
              </div>
            @endforeach
          </div>
        </div>

        <div>
          <div class="panel panel--sticky">
            <div class="panel__head">
              <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16l-1.5 12H5.5z"/><path d="M9 7a3 3 0 016 0"/></svg></span>
              <h3>{{ __('Order summary') }}</h3>
            </div>
            <div class="panel__body">
              <div class="summary-row"><span>{{ __('Subtotal') }}</span><strong>{{ helper::currency_formate($subtotal, $storeinfo->id) }}</strong></div>
              <div class="summary-row"><span>{{ __('Delivery & tax') }}</span><strong class="muted" style="font-weight:600">{{ __('At checkout') }}</strong></div>
              <div class="summary-row total"><span>{{ __('Total') }}</span><strong>{{ helper::currency_formate($subtotal, $storeinfo->id) }}</strong></div>

              <a href="{{ URL::to($tSlug . '/checkout') }}" class="btn btn-primary btn-block btn-lg mt-3">{{ __('Continue to checkout') }}</a>
              <a href="{{ URL::to($tSlug . '/categories') }}" class="btn btn-ghost btn-block" style="margin-top:10px">{{ __('Keep browsing') }}</a>

              <ul class="pd-meta" style="margin-top:22px">
                <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg> {{ __('Secure encrypted checkout') }}</li>
                <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg> {{ __('Multiple payment methods') }}</li>
              </ul>
            </div>
          </div>

          @if (!empty($tWa))
          <div class="panel mt-2">
            <div class="panel__body flex items-center gap-2">
              <span class="brand-mark" style="width:44px;height:44px"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 00-8.6 15L2 22l5.2-1.4A10 10 0 1012 2zm5.5 12.4c-.3-.2-1.7-.9-2-1s-.5-.1-.7.1-.7.9-.9 1.1-.4.2-.7.1a8.2 8.2 0 01-2.4-1.5 9 9 0 01-1.7-2.1c-.2-.3 0-.5.1-.6l.5-.6.3-.5v-.5l-.9-2.2c-.3-.6-.5-.5-.7-.5h-.6a1.2 1.2 0 00-.8.4A3.4 3.4 0 005 8.8a5.9 5.9 0 001.3 3.2 13.5 13.5 0 005.2 4.6 17 17 0 001.7.6 4.2 4.2 0 001.9.1 3.1 3.1 0 002-1.4 2.5 2.5 0 00.2-1.4c-.1-.1-.3-.2-.6-.3z"/></svg></span>
              <div>
                <strong style="font-size:.95rem;letter-spacing:-.02em">{{ __('Rather order on WhatsApp?') }}</strong>
                <p class="small">{{ __('Finish checkout and send it straight to the kitchen.') }}</p>
              </div>
              <a href="{{ URL::to($tSlug . '/checkout') }}" class="btn btn-primary btn-sm" style="margin-left:auto">{{ __('Go') }}</a>
            </div>
          </div>
          @endif
        </div>
      </div>
    </section>
    @else
    <section style="padding:clamp(40px,7vw,90px) 0">
      <div class="container">
        <div class="empty-state" style="text-align:center;max-width:520px;margin:0 auto">
          <div class="ic" style="color:var(--brand);margin-bottom:12px"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" style="width:64px;height:64px"><path d="M3 4h2l2.4 11.2a2 2 0 002 1.6h7.7a2 2 0 002-1.6L21 8H6"/><circle cx="10" cy="20" r="1.4"/><circle cx="18" cy="20" r="1.4"/></svg></div>
          <h2>{{ __('Your cart is empty') }}</h2>
          <p class="lead mt-1">{{ __('Nothing here yet. The menu is one tap away.') }}</p>
          <a href="{{ URL::to($tSlug . '/categories') }}" class="btn btn-primary btn-lg mt-3">{{ __('Browse the menu') }}</a>
        </div>
      </div>
    </section>
    @endif
@endsection

@section('scripts')
<script>
  // Cart page: after a qty/remove mutation, reload so summary totals refresh.
  document.getElementById('cartPageList')?.addEventListener('click', function (e) {
    if (e.target.closest('[data-oc-qty],[data-oc-remove]')) {
      setTimeout(function () { window.location.reload(); }, 500);
    }
  });
</script>
@endsection
