@php $tActive = 'cart'; $ocSet = 'retail'; @endphp
@extends('front.template-6.layout')

@section('content')
    @php
        $vid = $storeinfo->id ?? $orderdata->vendor_id;
        $typeName = @helper::gettype($orderdata->status, $orderdata->status_type, $orderdata->order_type, $orderdata->vendor_id)->name ?: '-';
        $orderType = (int) $summery['order_type'];
        $payName = $orderdata->payment_type == 0 ? 'COD' : (@helper::getpayment($orderdata->payment_type, $orderdata->vendor_id)->payment_name ?: '-');
        $tax = array_filter(explode('|', (string) $summery['tax']), fn($v) => $v !== '');
        $taxName = explode('|', (string) $summery['tax_name']);
    @endphp

    <section class="page-head">
      <div class="container">
        <div class="crumb"><a href="{{ $tBase }}">{{ __('Home') }}</a> <span>/</span> <span class="now">{{ __('Your Order Details') }}</span></div>
      </div>
    </section>

    <section style="padding-bottom:clamp(40px,6vw,72px)">
      <div class="container cart-layout">

        {{-- LEFT --}}
        <div class="grid" style="gap:22px">
          <div class="panel">
            <div class="panel__head">
              <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M4 7l1.5 12h13L20 7M4 7l2-4h12l2 4"/></svg></span>
              <h3>{{ __('Your Order Details') }}</h3>
            </div>
            <div class="panel__body">
              <div class="grid g-4" style="gap:0;border:1px solid var(--line);border-radius:14px;overflow:hidden;text-align:center">
                <div style="padding:14px;border-right:1px solid var(--line)"><span class="small muted" style="letter-spacing:.1em;text-transform:uppercase">{{ __('Order date') }}</span><br><strong>{{ $summery['created_at'] }}</strong></div>
                <div style="padding:14px;border-right:1px solid var(--line)"><span class="small muted" style="letter-spacing:.1em;text-transform:uppercase">{{ __('Status') }}</span><br><strong>{{ $typeName }}</strong></div>
                <div style="padding:14px;border-right:1px solid var(--line)"><span class="small muted" style="letter-spacing:.1em;text-transform:uppercase">{{ __('Type') }}</span><br><strong>{{ $orderType == 1 ? __('Delivery') : __('Pickup') }}</strong></div>
                <div style="padding:14px"><span class="small muted" style="letter-spacing:.1em;text-transform:uppercase">{{ __('Order') }}</span><br><strong>#{{ $summery['order_number'] }}</strong></div>
              </div>

              <div style="margin-top:18px;border:1px solid var(--line);border-radius:14px;overflow:hidden">
                <div class="grid" style="grid-template-columns:2fr 1fr .7fr 1fr;background:var(--ink,#1a1a1a);color:#fff;padding:12px 16px;font-weight:700;font-size:.86rem">
                  <span>{{ __('Products') }}</span><span style="text-align:center">{{ __('Price') }}</span><span style="text-align:center">{{ __('QTY') }}</span><span style="text-align:end">{{ __('Total') }}</span>
                </div>
                @foreach ($orderdetails as $odata)
                  @php $pimg = filter_var($odata->item_image, FILTER_VALIDATE_URL) ? $odata->item_image : (!empty($odata->item_image) ? asset(env('ASSETSPATHURL') . 'item/' . $odata->item_image) : helper::food_image($odata->item_name, $odata->id, $ocSet)); @endphp
                  <div class="grid" style="grid-template-columns:2fr 1fr .7fr 1fr;align-items:center;padding:12px 16px;border-top:1px solid var(--line)">
                    <span style="display:flex;align-items:center;gap:10px"><img src="{{ $pimg }}" alt="" style="width:44px;height:44px;border-radius:8px;object-fit:cover;flex:none"><b>{{ $odata->item_name }}</b></span>
                    <span style="text-align:center">{{ helper::currency_formate($odata->price, $vid) }}</span>
                    <span style="text-align:center">{{ $odata->qty }}</span>
                    <span style="text-align:end;font-weight:700">{{ helper::currency_formate($odata->qty * $odata->price, $vid) }}</span>
                  </div>
                @endforeach
              </div>
            </div>
          </div>

          <div class="panel">
            <div class="panel__head"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"><rect x="2" y="5" width="20" height="14" rx="3"/><path d="M2 10h20"/></svg></span><h3>{{ __('Payment Summary') }}</h3></div>
            <div class="panel__body">
              <div class="summary-row"><span>{{ __('Subtotal') }}</span><strong>{{ helper::currency_formate(@$summery['sub_total'], $vid) }}</strong></div>
              @foreach ($tax as $key => $tval)
                <div class="summary-row"><span>{{ $taxName[$key] ?? __('Tax') }}</span><strong>{{ helper::currency_formate((float) $tval, $vid) }}</strong></div>
              @endforeach
              @if ($orderType == 1)
                <div class="summary-row"><span>{{ __('Delivery') }}@if ($summery['delivery_area']) ({{ $summery['delivery_area'] }})@endif</span><strong>{{ $summery['delivery_charge'] > 0 ? helper::currency_formate($summery['delivery_charge'], $vid) : __('Free') }}</strong></div>
              @endif
              @if ($summery['discount_amount'] > 0)
                <div class="summary-row"><span>{{ __('Discount') }}@if ($summery['offer_type'] == 'promocode') ({{ $summery['couponcode'] }})@endif</span><strong style="color:var(--brand)">- {{ helper::currency_formate(@$summery['discount_amount'], $vid) }}</strong></div>
              @endif
              @if (!empty($summery['tips']) && $summery['tips'] > 0)
                <div class="summary-row"><span>{{ __('Tip') }}</span><strong>{{ helper::currency_formate($summery['tips'], $vid) }}</strong></div>
              @endif
              <div class="summary-row total"><span>{{ __('Grand total') }}</span><strong>{{ helper::currency_formate($summery['grand_total'], $vid) }}</strong></div>
            </div>
          </div>
        </div>

        {{-- RIGHT --}}
        <div class="grid" style="gap:16px;align-content:start">
          <div class="panel">
            <div class="panel__head"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="8" r="3.4"/><path d="M4.5 20a7.5 7.5 0 0115 0"/></svg></span><h3>{{ __('Customer Info') }}</h3></div>
            <div class="panel__body" style="display:grid;gap:12px">
              @if ($summery['customer_name'])<div style="display:flex;gap:10px;align-items:center"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="width:18px;height:18px;color:var(--brand)"><circle cx="12" cy="8" r="3.4"/><path d="M4.5 20a7.5 7.5 0 0115 0"/></svg><span>{{ $summery['customer_name'] }}</span></div>@endif
              @if ($summery['customer_email'])<div style="display:flex;gap:10px;align-items:center"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="width:18px;height:18px;color:var(--brand)"><rect x="2.5" y="4.5" width="19" height="15" rx="2.5"/><path d="M3 7l9 6 9-6"/></svg><a href="mailto:{{ $summery['customer_email'] }}">{{ $summery['customer_email'] }}</a></div>@endif
              @if ($summery['mobile'])<div style="display:flex;gap:10px;align-items:center"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="width:18px;height:18px;color:var(--brand)"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3 19.5 19.5 0 01-6-6 19.8 19.8 0 01-3-8.7A2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/></svg><a href="tel:{{ $summery['mobile'] }}">{{ $summery['mobile'] }}</a></div>@endif
              @if ($summery['order_notes'])<div style="display:flex;gap:10px;align-items:center"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="width:18px;height:18px;color:var(--brand)"><path d="M9 4h6a2 2 0 012 2v14l-5-3-5 3V6a2 2 0 012-2z"/></svg><span>{{ $summery['order_notes'] }}</span></div>@endif
            </div>
          </div>

          @if ($orderType == 1 && ($summery['address'] || $summery['building']))
          <div class="panel">
            <div class="panel__head"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M3 16V8a2 2 0 012-2h9v10H3zM14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/></svg></span><h3>{{ __('Delivery Info') }}</h3></div>
            <div class="panel__body"><div style="display:flex;gap:10px"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="width:18px;height:18px;color:var(--brand);flex:none;margin-top:2px"><path d="M12 21s7-5.6 7-11a7 7 0 10-14 0c0 5.4 7 11 7 11z"/><circle cx="12" cy="10" r="2.6"/></svg><span>{{ trim($summery['building'] . ', ' . $summery['address'] . ', ' . $summery['landmark'] . ', ' . $summery['pincode'], ', ') }}</span></div></div>
          </div>
          @endif

          <div class="panel">
            <div class="panel__head"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"><rect x="2" y="5" width="20" height="14" rx="3"/><path d="M2 10h20"/></svg></span><h3>{{ __('Payment Method') }}</h3></div>
            <div class="panel__body"><strong>{{ $payName }}</strong>@if (in_array($orderdata->payment_type, [2, 3, 4, 5, 7, 8, 9, 10, 11, 12, 13, 14, 15]) && !empty($orderdata->payment_id))<span class="small muted"> · {{ trans('labels.payment_id') }}: {{ $orderdata->payment_id }}</span>@endif</div>
          </div>

          @if (!in_array((int) $orderdata->status_type, [3, 4]))
            <a href="{{ URL::to($tSlug . '/cancel-order/' . $summery['order_number']) }}" class="btn btn-lg btn-block" style="background:var(--danger,#e5484d);color:#fff;border-color:var(--danger,#e5484d)">{{ trans('labels.cancel') }}</a>
          @endif
        </div>
      </div>
    </section>
@endsection
