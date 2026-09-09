@php
    $cd = \App\Models\Cart::where('vendor_id', $storeinfo->id)->where('buynow', 0);
    if (Auth::user() && Auth::user()->type == 3) {
        $cd->where('user_id', Auth::user()->id);
    } else {
        $cd->where('session_id', session()->getId());
    }
    $cartdata = $cd->get();
    $subtotal = $cartdata->sum(fn($c) => $c->price * $c->qty);
@endphp
<div class="drawer" id="cartDrawer" role="dialog" aria-modal="true" aria-label="Shopping bag">
  <div class="drawer__scrim" data-close></div>
  <div class="drawer__panel">
    <div class="drawer__head">
      <div><h3 style="font-family:var(--font);font-size:1.1rem;font-weight:700">{{ __('Shopping bag') }}</h3><span class="small muted oc-cart-sub">{{ $cartdata->count() }} {{ $cartdata->count() == 1 ? __('item') : __('items') }}</span></div>
      <button class="modal__close" data-close aria-label="Close"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
    </div>
    <div id="ocCartWrap">
      @include('front.template-7.partials.cart_body')
    </div>
  </div>
</div>
