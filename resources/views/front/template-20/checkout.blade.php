@php $tActive = 'cart'; @endphp
@extends('front.template-20.layout')

@section('styles')
<link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'web-assets/css/toastr/toastr.min.css') }}">
<link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'web-assets/css/sweetalert/sweetalert2.min.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
  /* payment + coupon bits styled to the template */
  .oc-pay-list { display:grid; gap:10px; }
  .oc-pay-list .opt img { max-height:26px; width:auto; margin-inline-start:auto; }
  .oc-pay-info { border:1px solid var(--line); border-radius:14px; padding:14px; background:var(--bg-alt,#faf7f2); }
  .oc-bank-list { list-style:none; padding:0; margin:0 0 10px; display:grid; gap:8px; }
  .oc-bank-list li { display:flex; justify-content:space-between; gap:12px; align-items:center; padding:8px 12px; background:var(--surface,#fff); border:1px solid var(--line); border-radius:10px; }
  .oc-bank-list li span { color:var(--text-2,#6b7280); font-size:.85rem; }
  .oc-bank-list li b { font-weight:700; text-align:end; word-break:break-all; }
  .oc-pay-note { font-size:.9rem; line-height:1.55; }
  .oc-pay-note :is(p,ul,ol) { margin:0 0 6px; }
  .oc-coupon-row { display:flex; gap:10px; align-items:center; }
  .oc-coupon-row .input { flex:1; }
  /* Bootstrap-style utilities the checkout JS toggles (template CSS lacks them). */
  .d-none { display:none !important; }
  .d-block { display:block !important; }
</style>
@endsection

@section('content')
    @php
        $total_price = 0;
        foreach ($cartdata as $cart) { $total_price += $cart->price * $cart->qty; }
        $ct_dtype = \App\Models\Settings::where('vendor_id', @$vdata)->value('delivery_type');
        $delivery_types = array_values(array_filter(
            array_map('trim', explode(',', (string) $ct_dtype)),
            fn($t) => $t === '1' || $t === '2'
        ));
        if (empty($delivery_types)) { $delivery_types = ['1', '2']; }
    @endphp

    <section class="page-head">
      <div class="container">
        <div class="crumb">
          <a href="{{ $tBase }}">{{ __('Home') }}</a>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M9 6l6 6-6 6"/></svg>
          <a href="{{ URL::to($tSlug . '/cart') }}">{{ __('Cart') }}</a>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M9 6l6 6-6 6"/></svg>
          <span class="now">{{ __('Checkout') }}</span>
        </div>
        <h1>{{ __('Checkout') }}</h1>
        <p>{{ __("Two minutes and you're done. We only ask for what we actually need.") }}</p>
      </div>
    </section>

    @if (App\Models\SystemAddons::where('unique_identifier', 'cart_checkout_countdown')->first() != null &&
            App\Models\SystemAddons::where('unique_identifier', 'cart_checkout_countdown')->first()->activated == 1)
      <div class="container">@include('front.cart_checkout_countdown')</div>
    @endif

    <section style="padding-bottom:clamp(40px,6vw,72px)">
      <div class="container cart-layout">

        {{-- ============ LEFT: FORM ============ --}}
        <div class="grid" style="gap:22px">

          {{-- Delivery option --}}
          <div class="panel">
            <div class="panel__head">
              <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 16V8a2 2 0 012-2h9v10H3zM14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/></svg></span>
              <h3>{{ trans('labels.delivery_option') }}</h3>
            </div>
            <div class="panel__body">
              <div class="grid g-2" style="gap:12px">
                @foreach ($delivery_types as $key => $delivery_type)
                  <label class="opt">
                    <input type="radio" name="cart-delivery" id="cart-delivery-{{ $delivery_type }}" value="{{ $delivery_type }}" {{ $key == 0 ? 'checked' : '' }}>
                    <span class="mark"></span>
                    <span class="txt"><strong>{{ $delivery_type == 1 ? trans('labels.delivery') : trans('labels.pickup') }}</strong></span>
                  </label>
                @endforeach
              </div>
            </div>
          </div>

          {{-- Date & time --}}
          <div class="panel" id="data_time">
            <div class="panel__head">
              <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span>
              <h3>{{ trans('labels.date_time') }}</h3>
            </div>
            <div class="panel__body">
              <div class="form-grid">
                <div class="field">
                  <label class="form-label" id="delivery_date">{{ trans('labels.delivery_date') }} <span class="req">*</span></label>
                  <label class="form-label" id="pickup_date">{{ trans('labels.pickup_date') }} <span class="req">*</span></label>
                  <input type="text" class="input delivery_pickup_date" id="delivery_dt" value="" placeholder="Delivery date" required>
                </div>
                <div class="field">
                  <label class="form-label" id="delivery">{{ trans('labels.delivery_time') }} <span class="req">*</span></label>
                  <label class="form-label" id="pickup">{{ trans('labels.pickup_time') }} <span class="req">*</span></label>
                  <label id="store_close" class="d-none" style="color:#e5484d">{{ trans('labels.today_store_closed') }}</label>
                  <input type="hidden" name="store_id" id="store_id" value="{{ @$vdata }}">
                  <input type="hidden" name="sloturl" id="sloturl" value="{{ URL::to(@$storeinfo->slug . '/timeslot') }}">
                  <select name="delivery_time" id="delivery_time" class="select" required>
                    <option value="{{ old('delivery_time') }}">{{ trans('labels.select') }}</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          {{-- Delivery info / address --}}
          <div class="panel" id="open">
            <div class="panel__head">
              <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s7-5.6 7-11a7 7 0 10-14 0c0 5.4 7 11 7 11z"/><circle cx="12" cy="10" r="2.6"/></svg></span>
              <h3>{{ trans('labels.delivery_info') }}</h3>
            </div>
            <div class="panel__body">
              <div class="field mb-2">
                <label class="form-label">{{ trans('labels.address') }} <span class="req">*</span></label>
                <input type="text" class="input" name="address" id="address" placeholder="{{ trans('labels.address') }}">
              </div>
              <div class="form-grid mb-2">
                <div class="field">
                  <label class="form-label">{{ trans('labels.landmark') }}</label>
                  <input type="text" class="input" name="landmark" id="landmark" placeholder="{{ trans('labels.landmark') }}">
                </div>
                <div class="field">
                  <label class="form-label">{{ trans('labels.building') }}</label>
                  <input type="text" class="input" name="building" id="building" placeholder="{{ trans('labels.building') }}">
                </div>
              </div>
              <div class="field">
                <label class="form-label">{{ trans('labels.pincode') }}</label>
                <input type="number" class="input" placeholder="{{ trans('labels.pincode') }}" name="postal_code" id="postal_code">
              </div>
            </div>
          </div>

          {{-- Customer --}}
          <div class="panel">
            <div class="panel__head">
              <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="3.6"/><path d="M4.5 20a7.5 7.5 0 0115 0"/></svg></span>
              <h3>{{ trans('labels.customer') }}</h3>
            </div>
            <div class="panel__body">
              <div class="form-grid mb-2">
                <div class="field">
                  <label class="form-label">{{ trans('labels.name') }} <span class="req">*</span></label>
                  <input type="text" class="input" placeholder="{{ trans('labels.name') }}" name="customer_name" id="customer_name" value="{{ @Auth::user() && @Auth::user()->type == 3 ? @Auth::user()->name : '' }}">
                </div>
                <div class="field">
                  <label class="form-label">{{ trans('labels.mobile') }} <span class="req">*</span></label>
                  <input type="number" class="input" placeholder="{{ trans('labels.mobile') }}" name="customer_mobile" id="customer_mobile" value="{{ @Auth::user() && @Auth::user()->type == 3 ? @Auth::user()->mobile : '' }}">
                </div>
              </div>
              <div class="form-grid">
                <div class="field">
                  <label class="form-label">{{ trans('labels.email') }} <span class="req">*</span></label>
                  <input type="email" class="input" placeholder="{{ trans('labels.email') }}" name="customer_email" id="customer_email" value="{{ @Auth::user() && @Auth::user()->type == 3 ? @Auth::user()->email : '' }}">
                </div>
                <div class="field">
                  <label class="form-label">{{ trans('labels.note') }}</label>
                  <textarea id="notes" name="notes" class="textarea" rows="3" placeholder="{{ trans('labels.message') }}"></textarea>
                </div>
              </div>
              <input type="hidden" id="vendor" name="vendor" value="{{ $vdata }}" />
            </div>
          </div>

          {{-- Tip --}}
          @if (App\Models\SystemAddons::where('unique_identifier', 'vendor_tip')->first() != null &&
                  App\Models\SystemAddons::where('unique_identifier', 'vendor_tip')->first()->activated == 1)
            @if (@helper::otherappdata($vdata)->tips_settings == 1)
              <div class="panel">
                <div class="panel__head">
                  <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M9 9h6M9 12h6M9 15h3"/></svg></span>
                  <h3>{{ trans('labels.tips_pro') }}</h3>
                </div>
                <div class="panel__body">
                  <div class="field">
                    <label class="form-label">{{ trans('labels.add_amount') }}</label>
                    <input type="number" class="input" id="add_amount" placeholder="{{ trans('labels.add_amount') }}">
                  </div>
                </div>
              </div>
            @endif
          @endif
        </div>

        {{-- ============ RIGHT: SUMMARY + PAYMENT ============ --}}
        <div>
          <input type="hidden" id="discount_amount" value="{{ Session::get('offer_amount') }}" />
          <input type="hidden" id="offer_type" value="{{ Session::get('offer_type') }}" />
          <input type="hidden" name="coupon_code" id="coupon_code" value="{{ Session::get('offer_code') }}">

          {{-- Coupon --}}
          @php
            $showCoupon = App\Models\SystemAddons::where('unique_identifier', 'coupon')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'coupon')->first()->activated == 1;
            $subAddon = App\Models\SystemAddons::where('unique_identifier', 'subscription')->first();
            if ($subAddon != null && $subAddon->activated == 1) {
                $promocode = helper::vendordata(@$vdata)->allow_without_subscription == 1 ? 1 : helper::get_plan(@$vdata)->coupons;
            } else {
                $promocode = 1;
            }
          @endphp
          @if ($showCoupon && $promocode == 1)
            <div class="panel mb-2 @if (@$coupons->count() == 0 || Session::get('offer_type') == 'loyalty') d-none @endif" id="promocodesection">
              <div class="panel__body">
                <p class="title" style="font-weight:700;margin-bottom:10px">{{ trans('labels.apply_coupon') }}</p>
                <div class="oc-coupon-row">
                  <input type="text" class="input offer-input" value="{{ Session::has('offer_code') ? Session::get('offer_code') : '' }}" name="promocode" id="couponcode" placeholder="{{ trans('labels.coupon_code') }}" readonly>
                  <button class="btn btn-dark d-none" id="btnremove" onclick="RemoveCopon()">{{ trans('labels.remove') }}</button>
                  <button class="btn btn-dark d-block" id="btnapply" onclick="ApplyCopon()">{{ trans('labels.apply') }}</button>
                </div>
                <input type="hidden" id="removecouponurl" value="{{ URL::to('/cart/removepromocode') }}" />
                <input type="hidden" id="applycouponurl" value="{{ URL::to('/cart/applypromocode') }}" />
              </div>
            </div>
          @endif

          {{-- Loyalty --}}
          @if (($subAddon != null && $subAddon->activated == 1) && @Auth::user() && Auth::user()->type == 3)
            @if (loyaltyhelper::getloyaltydata($vdata) != null && loyaltyhelper::getloyaltydata($vdata)->is_available == 1)
              <div class="panel mb-2 @if (Session::get('offer_type') == 'promocode') d-none @endif" id="loyaltysection">
                <div class="panel__body">
                  <p class="title" style="font-weight:700;margin-bottom:6px" id="loyalty_program">{{ trans('labels.loyalty_program') }}</p>
                  <p class="small muted" id="loyalty_desc">{{ trans('labels.you_have_currently') }} <b>{{ @loyaltyhelper::availablepoints(@Auth::user()->id, @$vdata) }}</b> {{ trans('labels.use_it_on_order') }}</p>
                  <h6 class="fw-600 mt-1">1 {{ trans('labels.point') }} = {{ helper::currency_formate(@loyaltyhelper::getloyaltydata(@$vdata)->per_coin_amount, @$vdata) }}</h6>
                  @if (loyaltyhelper::availablepoints(@Auth::user()->id, @$vdata) > 0)
                    <div class="oc-coupon-row mt-2">
                      <input type="text" class="input" name="points" id="points" placeholder="{{ trans('labels.enter_point') }}" value="{{ Session::get('offer_code') }}" @if (Session::get('offer_type') == 'loyalty') readonly @endif>
                      <input type="hidden" id="applyredeempoints" value="{{ URL::to('/cart/applyredeempoints') }}" />
                      <input type="hidden" id="removeredeempoints" value="{{ URL::to('/cart/removeredeempoints') }}" />
                      <button class="btn btn-dark d-none" id="btnremovepoint" onclick="RemovePoints()">{{ trans('labels.remove') }}</button>
                      <button class="btn btn-dark d-block" id="btnredeempoint" onclick="RedeemPoints('{{ @$vdata }}')">{{ trans('labels.redeem') }}</button>
                    </div>
                  @endif
                </div>
              </div>
            @endif
          @endif

          {{-- Shipping area --}}
          @if (App\Models\SystemAddons::where('unique_identifier', 'shipping_area')->first() != null &&
                  App\Models\SystemAddons::where('unique_identifier', 'shipping_area')->first()->activated == 1)
            @if (helper::appdata($vdata)->shipping_area == 1 && count($getshippingarealist) > 0)
              <div class="panel mb-2" id="shippinginfodiv">
                <div class="panel__body">
                  <p class="title" style="font-weight:700;margin-bottom:10px">{{ trans('labels.shipping_area') }}</p>
                  <select name="shipping_area" id="shipping_area" class="select">
                    <option value="" selected disabled>{{ trans('labels.select') }}</option>
                    @foreach ($getshippingarealist as $shippingarea)
                      <option value="{{ $shippingarea->id }}" data-delivery-charge="{{ $shippingarea->delivery_charge }}" data-area-name="{{ $shippingarea->area_name }}">
                        {{ $shippingarea->area_name }}
                        @if (helper::appdata($vdata)->min_order_amount_for_free_shipping > $total_price)
                          @if ($shippingarea->delivery_charge > 0){{ trans('labels.delivery_charge') }} : {{ helper::currency_formate($shippingarea->delivery_charge, @$vdata) }}@endif
                        @else
                          {{ trans('labels.free_delivery') }}
                        @endif
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>
            @endif
          @endif

          {{-- Order summary --}}
          <div class="panel mb-2">
            <div class="panel__head">
              <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16l-1.5 12H5.5z"/><path d="M9 7a3 3 0 016 0"/></svg></span>
              <h3>{{ trans('labels.order_summary') }}</h3>
            </div>
            <div class="panel__body">
              <ul id="payment_summery_list" style="list-style:none;padding:0;margin:0">
                <li class="summary-row"><span>{{ trans('labels.sub_total') }}</span><strong>{{ helper::currency_formate($total_price, @$vdata) }}</strong></li>

                @php
                  $discount = (Session::get('offer_type') == 'promocode' || Session::get('offer_type') == 'loyalty') ? Session::get('offer_amount') : 0;
                @endphp
                <li class="summary-row @if (Session::get('offer_type') == '') d-none @endif" id="discount_1">
                  <span>{{ trans('labels.discount') }}</span><strong id="offer_amount" style="color:var(--brand)">- {{ helper::currency_formate(@$discount, @$vdata) }}</strong>
                </li>

                @php $totalcarttax = 0; @endphp
                @foreach ($taxArr['tax'] as $k => $tax)
                  @php $rate = $taxArr['rate'][$k]; $totalcarttax += (float) $taxArr['rate'][$k]; @endphp
                  <li class="summary-row" id="tax_list"><span>{{ $tax }}</span><strong>{{ helper::currency_formate($rate, $vdata) }}</strong></li>
                @endforeach

                <li class="summary-row" id="delivery-charge-section">
                  <span>{{ trans('labels.delivery_charge') }}</span>
                  @php
                    $freeMin = helper::appdata($vdata)->min_order_amount_for_free_shipping;
                    $shipCharge = helper::appdata($vdata)->shipping_charges;
                    $shippingAddon = App\Models\SystemAddons::where('unique_identifier', 'shipping_area')->first();
                    $shippingOn = $shippingAddon != null && $shippingAddon->activated == 1 && helper::appdata($vdata)->shipping_area == 1 && count($getshippingarealist) > 0;
                    if ($shippingOn) {
                        // area-based: charge resolved by JS on area select; start at 0
                        $deliveryCharge = 0;
                        $grand_total = $total_price - $discount + $totalcarttax;
                    } elseif ($total_price >= $freeMin) {
                        $deliveryCharge = 0;
                        $grand_total = $total_price - $discount + $totalcarttax;
                    } else {
                        $deliveryCharge = $shipCharge;
                        $grand_total = $total_price - $discount + $totalcarttax + $shipCharge;
                    }
                  @endphp
                  <strong class="delivery_charge" style="{{ $deliveryCharge == 0 ? 'color:var(--brand)' : '' }}">{{ $deliveryCharge == 0 ? trans('labels.free') : helper::currency_formate($deliveryCharge, @$vdata) }}</strong>
                  <input type="hidden" name="delivery_charge" id="delivery_charge" value="{{ $deliveryCharge }}">
                </li>

                <li class="summary-row total"><span>{{ trans('labels.grand_total') }}</span><strong id="grand_total_view">{{ helper::currency_formate($grand_total, @$vdata) }}</strong></li>
              </ul>
            </div>
          </div>

          {{-- Payment methods --}}
          <div class="panel mb-2">
            <div class="panel__head">
              <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="2" y="5" width="20" height="14" rx="3"/><path d="M2 10h20"/></svg></span>
              <h3>{{ trans('labels.payment_option') }}</h3>
            </div>
            <div class="panel__body">
              <div class="oc-pay-list">
                @php $key = 0; @endphp
                @foreach ($paymentlist as $payment)
                  @php
                    $systemAddonActivated = $payment->isIncludedGateway();
                    $addon = App\Models\SystemAddons::where('unique_identifier', $payment->unique_identifier)->first();
                    if ($addon != null && $addon->activated == 1) { $systemAddonActivated = true; }
                  @endphp
                  @if ($systemAddonActivated)
                    <div class="select-payment-list-items">
                      <label class="opt" for="{{ $payment->payment_type }}">
                        <input class="form-check-input" type="radio" id="{{ $payment->payment_type }}" name="payment"
                          data-payment_type="{{ $payment->payment_type }}" data-currency="{{ $payment->currency }}"
                          @if ($key++ == 0) checked @endif value="{{ $payment->payment_type }}">
                        <span class="mark"></span>
                        <span class="txt" style="display:flex;align-items:center;gap:10px;width:100%">
                          <strong>{{ $payment->payment_name }}</strong>
                          @if (Auth::user() && $payment->payment_type == 16)<span class="small muted">{{ helper::currency_formate(Auth::user()->wallet, $vdata) }}</span>@endif
                          <img src="{{ helper::image_path($payment->image) }}" alt="" style="max-height:24px;width:auto;margin-inline-start:auto">
                        </span>
                      </label>
                      @if ($payment->payment_type == '2')<input type="hidden" name="razorpay" id="razorpay" value="{{ $payment->public_key }}">@endif
                      @if ($payment->payment_type == '3')<input type="hidden" name="stripekey" id="stripekey" value="{{ $payment->public_key }}"><input type="hidden" name="stripecurrency" id="stripecurrency" value="{{ $payment->currency }}">@endif
                      @if ($payment->payment_type == '4')<input type="hidden" name="flutterwavekey" id="flutterwavekey" value="{{ $payment->public_key }}">@endif
                      @if ($payment->payment_type == '5')<input type="hidden" name="paystackkey" id="paystackkey" value="{{ $payment->public_key }}">@endif
                      @if ($payment->payment_type == '6')<input type="hidden" value="{{ $payment->payment_description }}" id="bank_payment">@endif
                      @php $ocOffline = in_array((string) $payment->payment_type, ['6', '17', '18', '19', '20', '21']); @endphp
                      @if ($ocOffline)
                        <div class="oc-pay-info mt-2" data-for="{{ $payment->payment_type }}" style="display:none;">
                          {{-- QR code (BenefitPay / Bank QR Code) --}}
                          @if (in_array((string) $payment->payment_type, ['19', '20']) && !empty($payment->qr_image))
                            <img src="{{ helper::image_path($payment->qr_image) }}" alt="QR" style="max-width:190px;border-radius:12px;display:block;margin-bottom:10px;">
                            <p class="small muted" style="margin:0 0 10px">{{ trans('labels.scan_the_qr_code_to_pay_then') }}</p>
                          @endif
                          {{-- Payment link --}}
                          @if ((string) $payment->payment_type === '21' && !empty($payment->payment_link))
                            <a href="{{ $payment->payment_link }}" target="_blank" rel="noopener" class="btn btn-primary btn-sm mb-2">{{ trans('labels.pay_via_link') }} <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" style="width:14px;height:14px;vertical-align:-2px"><path d="M7 17L17 7M9 7h8v8"/></svg></a>
                          @endif
                          {{-- Bank transfer: structured details if present --}}
                          @if ((string) $payment->payment_type === '6')
                            @if (!empty($payment->account_holder_name) || !empty($payment->bank_name) || !empty($payment->account_number) || !empty($payment->bank_ifsc_code))
                              <ul class="oc-bank-list">
                                @if (!empty($payment->account_holder_name))<li><span>{{ __('Account name') }}</span><b>{{ $payment->account_holder_name }}</b></li>@endif
                                @if (!empty($payment->bank_name))<li><span>{{ __('Bank') }}</span><b>{{ $payment->bank_name }}</b></li>@endif
                                @if (!empty($payment->account_number))<li><span>{{ __('Account number') }}</span><b>{{ $payment->account_number }}</b></li>@endif
                                @if (!empty($payment->bank_ifsc_code))<li><span>{{ __('IBAN / IFSC') }}</span><b>{{ $payment->bank_ifsc_code }}</b></li>@endif
                              </ul>
                            @endif
                          @endif
                          {{-- Instructions / note (bank transfer stores rich HTML; others plain text) --}}
                          @if (!empty($payment->payment_description))
                            @if ((string) $payment->payment_type === '6')
                              <div class="oc-pay-note">{!! $payment->payment_description !!}</div>
                            @else
                              <p class="oc-pay-note small" style="margin:0">{!! nl2br(e($payment->payment_description)) !!}</p>
                            @endif
                          @endif
                        </div>
                      @endif
                    </div>
                  @endif
                @endforeach
              </div>
              <script>
                (function () {
                  function ocPayInfo() {
                    var sel = document.querySelector('input[name="payment"]:checked');
                    document.querySelectorAll('.oc-pay-info').forEach(function (b) { b.style.display = 'none'; });
                    if (sel) { var box = document.querySelector('.oc-pay-info[data-for="' + sel.value + '"]'); if (box) box.style.display = 'block'; }
                  }
                  document.querySelectorAll('input[name="payment"]').forEach(function (r) { r.addEventListener('change', ocPayInfo); });
                  ocPayInfo();
                })();
              </script>
            </div>
          </div>

          <button class="btn btn-primary btn-block btn-lg checkout" onclick="Order()">{{ trans('labels.place_order') }}</button>
        </div>
      </div>
    </section>

    {{-- Hidden fields required by checkout.js --}}
    <input type="hidden" id="sub_total" value="{{ $total_price }}" />
    <input type="hidden" id="tax" value="{{ implode('|', $taxArr['rate']) }}" />
    <input type="hidden" name="tax_name" id="tax_name" value="{{ implode('|', $taxArr['tax']) }}">
    <input type="hidden" name="totaltax" id="totaltax" value="{{ @$totalcarttax }}">
    <input type="hidden" name="grand_total" id="grand_total" value="{{ @$grand_total }}">
    <input type="hidden" id="table_required" value="{{ trans('messages.table_required') }}">
    <input type="hidden" id="delivery_time_required" value="{{ trans('messages.delivery_time_required') }}">
    <input type="hidden" id="delivery_date_required" value="{{ trans('messages.delivery_date_required') }}">
    <input type="hidden" id="address_required" value="{{ trans('messages.address_required') }}">
    <input type="hidden" id="no_required" value="{{ trans('messages.no_required') }}">
    <input type="hidden" id="landmark_required" value="{{ trans('messages.landmark_required') }}">
    <input type="hidden" id="pincode_required" value="{{ trans('messages.pincode_required') }}">
    <input type="hidden" id="delivery_area_required" value="{{ trans('messages.delivery_area') }}">
    <input type="hidden" id="pickup_date_required" value="{{ trans('messages.pickup_date_required') }}">
    <input type="hidden" id="pickup_time_required" value="{{ trans('messages.pickup_time_required') }}">
    <input type="hidden" id="customer_mobile_required" value="{{ trans('messages.customer_mobile_required') }}">
    <input type="hidden" id="customer_email_required" value="{{ trans('messages.customer_email_required') }}">
    <input type="hidden" id="customer_name_required" value="{{ trans('messages.customer_name_required') }}">
    <input type="hidden" id="currency" value="{{ helper::appdata(@$vdata)->currency }}">
    <input type="hidden" id="checkplanurl" value="{{ URL::to('/orders/checkplan') }}">
    <input type="hidden" id="paymenturl" value="{{ URL::to('/orders/paymentmethod') }}">
    <input type="hidden" id="mecadourl" value="{{ URL::to('/orders/mercadoorderrequest') }}">
    <input type="hidden" id="paypalurl" value="{{ URL::to('/orders/paypalrequest') }}">
    <input type="hidden" id="myfatoorahurl" value="{{ URL::to('/orders/myfatoorahrequest') }}">
    <input type="hidden" id="toyyibpayurl" value="{{ URL::to('/orders/toyyibpayrequest') }}">
    <input type="hidden" id="phonepeurl" value="{{ URL::to('/orders/phoneperequest') }}">
    <input type="hidden" id="paytaburl" value="{{ URL::to('/orders/paytabrequest') }}">
    <input type="hidden" id="mollieurl" value="{{ URL::to('/orders/mollierequest') }}">
    <input type="hidden" id="khaltiurl" value="{{ URL::to('/orders/khaltirequest') }}">
    <input type="hidden" id="xenditurl" value="{{ URL::to('/orders/xenditrequest') }}">
    <input type="hidden" id="payment_url" value="{{ URL::to(@$storeinfo->slug) }}/payment">
    <input type="hidden" id="website_title" value="{{ helper::appdata(@$vdata)->website_title }}">
    <input type="hidden" id="image" value="{{ helper::appdata(@$vdata)->image }}">
    <input type="hidden" id="slug" value="{{ @$storeinfo->slug }}">
    <input type="hidden" id="failure" value="{{ url()->current() . '?buy_now=' . request()->get('buy_now') }}">
    <input type="hidden" name="buynow_key" id="buynow_key" value="{{ request()->get('buy_now') }}">
    <form action="{{ url('/orders/paypalrequest') }}" method="post" class="d-none">
      {{ csrf_field() }}
      <input type="hidden" name="return" value="2">
      <input type="submit" class="callpaypal" name="submit">
    </form>

    {{-- Coupons offcanvas --}}
    @if (count($coupons) > 0)
      <div data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasExample">
        <div class="offers-label {{ session()->get('direction') == 2 ? 'offers-label-rtl' : 'offers-label-ltr' }}">
          <i class="fa-light fa-badge-percent text-white"></i>
          <div class="offers-label-name">{{ trans('labels.offer') }}</div>
        </div>
      </div>
      <div class="offcanvas {{ session()->get('direction') == 2 ? 'offcanvas-start' : 'offcanvas-end' }}" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
        <div class="offcanvas-header justify-content-between border-bottom">
          <h5 class="offcanvas-title" id="offcanvasRightLabel">{{ trans('labels.coupons_offers') }}</h5>
          <button type="button" class="bg-transparent border-0 m-0" data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa-regular fa-xmark fs-4"></i></button>
        </div>
        <div class="offcanvas-body">
          @foreach ($coupons as $coupon)
            <div class="panel mb-2"><div class="panel__body">
              <div class="d-flex align-items-center justify-content-between" data-copy=true>
                <input type="hidden" id="applycoponurl" value="{{ URL::to('/cart/applypromocode') }}" />
                <span id="promocode" class="chip" readonly>{{ $coupon->offer_code }}</span>
                <p class="cursor-pointer fw-600" style="cursor:pointer" onclick="copyToClipboard('{{ $coupon->offer_code }}')">{{ trans('labels.copy') }}</p>
              </div>
              <div class="mt-2">
                <h6 class="mb-2">{{ $coupon->offer_type == 1 ? helper::currency_formate($coupon->offer_amount, $vdata) : $coupon->offer_amount . '%' }} {{ trans('labels.coupons') }}</h6>
                <h6 class="fw-500">{{ $coupon->offer_name }}</h6>
                <p class="small muted">{{ Str::limit($coupon->description, 180) }}</p>
              </div>
            </div></div>
          @endforeach
        </div>
      </div>
    @endif
@endsection

@section('scripts')
    <script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/jquery-3.6.3.min.js') }}"></script>
    <script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/jquery.number.min.js') }}"></script>
    <script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/toastr/toastr.min.js') }}"></script>
    <script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/sweetalert2@11.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        var are_you_sure = "{{ trans('messages.are_you_sure') }}";
        var yes = "{{ trans('messages.yes') }}";
        var no = "{{ trans('messages.no') }}";
        var cancel = "{{ trans('labels.cancel') }}";
        let wrong = "{{ trans('messages.wrong') }}";
        var vendor_id = "{{ $vdata }}";
        var direction = "{{ session('direction') }}";
        toastr.options = { "closeButton": true, "positionClass": "toast-bottom-center" };
        @if (Session::has('success')) toastr.success("{{ session('success') }}"); @endif
        @if (Session::has('error')) toastr.error("{{ session('error') }}"); @endif

        function currency_formate(price) {
            var formate = {{ @helper::currencyinfo($vdata)->decimal_digit ?? 2 }};
            var price = parseFloat(price) * {{ @helper::currencyinfo($vdata)->exchange_rate }};
            var currency = "{{ @helper::currencyinfo($vdata)->currency }}";
            var position = "{{ @helper::currencyinfo($vdata)->currency_position }}";
            var space = "{{ @helper::currencyinfo($vdata)->currency_space }}";
            var decimal_sep = "{{ @helper::currencyinfo($vdata)->decimal_separator }}";
            var oldprice = decimal_sep == 1 ? $.number(price, formate) : $.number(price, formate, ',', '.');
            var newprice = '';
            if (position == "1") { newprice = space == "1" ? currency + ' ' + oldprice : currency + oldprice; }
            else { newprice = space == "1" ? oldprice + ' ' + currency : oldprice + currency; }
            return newprice;
        }

        var showbutton = "{{ Session::get('offer_type') }}";
        var min_order_amount_for_free_shipping = "{{ helper::appdata(@$vdata)->min_order_amount_for_free_shipping }}";
        $(document).ready(function () {
            if (showbutton == 'promocode') { $('#btnremove').removeClass('d-none'); $('#btnapply').addClass('d-none'); }
            else { $('#btnremove').addClass('d-none'); $('#btnapply').removeClass('d-none'); }
            if (showbutton == 'loyalty') { $('#btnremovepoint').removeClass('d-none'); $('#btnredeempoint').addClass('d-none'); }
            else { $('#btnremovepoint').addClass('d-none'); $('#btnredeempoint').removeClass('d-none'); }
        });

        function ApplyCopon() {
            $('#btnapply').prop("disabled", true).html('<span class="loader"></span>');
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: $('#applycouponurl').val(), method: 'post',
                data: { promocode: $('#couponcode').val(), sub_total: $('#sub_total').val(), vendor_id: $('#vendor').val() },
                success: function (response) {
                    if (response.status == 1) {
                        var total = parseFloat($('#sub_total').val());
                        var tax = parseFloat($('#totaltax').val());
                        var delivery_charge = parseFloat($('#delivery_charge').val());
                        var discount = "";
                        if (response.data.offer_type == 1) { discount = response.data.offer_amount; }
                        if (response.data.offer_type == 2) { discount = total * parseFloat(response.data.offer_amount) / 100; }
                        var grandtotal = ($("input[name='cart-delivery']:checked").val() == 1)
                            ? parseFloat(total) + parseFloat(tax) + parseFloat(delivery_charge) - parseFloat(discount)
                            : parseFloat(total) + parseFloat(tax) - parseFloat(discount);
                        $('#loyaltysection').addClass('d-none');
                        $('#discount_1').removeClass('d-none');
                        $('#offer_amount').text('- ' + currency_formate(parseFloat(discount)));
                        $('#grand_total_view').html(currency_formate(grandtotal));
                        $('#grand_total').val(grandtotal);
                        $('#discount_amount').val(discount);
                        $('#coupon_code').val(response.data.offer_code);
                        $('#offer_type').val(response.offer_type);
                        $('#points').val('');
                        $('#btnremove').removeClass('d-none');
                        $('#btnapply').addClass('d-none').html("{{ trans('labels.apply') }}").prop("disabled", false);
                        toastr.success(response.message);
                    } else {
                        $('#btnapply').html("{{ trans('labels.apply') }}").prop("disabled", false);
                        toastr.error(response.message);
                    }
                },
                error: function () { $('#btnapply').html("{{ trans('labels.apply') }}").prop("disabled", false); toastr.error(wrong); }
            });
        }

        function RemoveCopon() {
            const swalWithBootstrapButtons = Swal.mixin({ customClass: { confirmButton: 'btn btn-success mx-1 yes-btn', cancelButton: 'btn btn-danger mx-1 no-btn' }, buttonsStyling: false });
            swalWithBootstrapButtons.fire({ title: are_you_sure, icon: 'warning', showCancelButton: true, confirmButtonText: yes, cancelButtonText: no, reverseButtons: true }).then((result) => {
                if (result.isConfirmed) {
                    $('#btnremove').prop("disabled", true).html('<span class="loader"></span>');
                    $.ajax({
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        url: $('#removecouponurl').val(), method: 'post', data: { promocode: $('#couponcode').val() },
                        success: function (response) {
                            if (response.status == 1) {
                                var total = $('#sub_total').val();
                                var tax = parseFloat($('#totaltax').val());
                                var delivery_charge = $('#delivery_charge').val();
                                var grandtotal = ($("input[name='cart-delivery']:checked").val() == 1)
                                    ? parseFloat(total) + parseFloat(tax) + parseFloat(delivery_charge)
                                    : parseFloat(total) + parseFloat(tax);
                                $('#loyaltysection').removeClass('d-none');
                                $('#discount_1').addClass('d-none');
                                $('#offer_amount').text('- ' + currency_formate(parseFloat(0)));
                                $('#grand_total_view').html(currency_formate(grandtotal));
                                $('#couponcode').val(''); $('#coupon_code').val(''); $('#offer_type').val(''); $('#points').val('');
                                $('#grand_total').val(grandtotal); $('#discount_amount').val(0);
                                $('#btnremove').addClass('d-none'); $('#btnapply').removeClass('d-none');
                                $('#btnremove').html("{{ trans('labels.remove') }}").prop("disabled", false);
                                toastr.success(response.message);
                            } else { $('#btnremove').html("{{ trans('labels.remove') }}").prop("disabled", false); toastr.error(response.message); }
                        },
                        error: function () { $('#btnremove').html("{{ trans('labels.remove') }}").prop("disabled", false); toastr.error(wrong); }
                    });
                }
            });
        }

        var select = "{{ trans('labels.select') }}";
        var dateFormat = "{{ helper::appdata($vdata)->date_format }}";
        var placeholderFormat = dateFormat.replace(/Y/g, 'yyyy').replace(/m/g, 'mm').replace(/d/g, 'dd');
        document.getElementById("delivery_dt").setAttribute("placeholder", placeholderFormat);
        flatpickr(".delivery_pickup_date", { dateFormat: dateFormat, enableTime: false, altInput: true, altFormat: dateFormat, minDate: 'today' });
    </script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="https://checkout.flutterwave.com/v3.js"></script>
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/custom/checkout.js') }}"></script>
@endsection
