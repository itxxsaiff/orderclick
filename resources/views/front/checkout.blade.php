@extends('front.theme.default')
@section('content')
    <!-- breadcrumb start -->
    <section class="breadcrumb-sec bg-change-mode">
        <div class="container">
            <nav>
                <ol class="breadcrumb d-flex m-0 text-capitalize">
                    <li class="breadcrumb-item">
                        <a href="{{ URL::to(@$storeinfo->slug) }}" class="text-dark color-changer">
                            {{ trans('labels.home') }}
                        </a>
                    </li>
                    <li
                        class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-item-right' : 'breadcrumb-item-left' }}">
                        {{ trans('labels.checkout') }}
                    </li>
                </ol>
            </nav>
        </div>
    </section>
    <!-- breadcrumb end -->
    <section class="my-5">
        <div class="container">
            @if (App\Models\SystemAddons::where('unique_identifier', 'cart_checkout_countdown')->first() != null &&
                    App\Models\SystemAddons::where('unique_identifier', 'cart_checkout_countdown')->first()->activated == 1)
                @include('front.cart_checkout_countdown')
            @endif
            <div class="row g-sm-3 g-0">
                <div class="col-md-12 col-lg-8">
                    <div class="border rounded py-0 mb-4">
                        @php
                            $total_price = 0;
                        @endphp
                        @foreach ($cartdata as $cart)
                            @php
                                $total_price += $cart->price * $cart->qty;
                            @endphp
                        @endforeach
                        <div class="card border-0 select-delivery rounded">
                            <div class="card-body row">
                                <div class="radio-item-container">
                                    <div class="d-flex color-changer align-items-center mb-3 px-0 border-bottom pb-2">
                                        <i class="fa-solid fa-truck"></i>
                                        <p class="title px-2">{{ trans('labels.delivery_option') }}</p>
                                    </div>
                                    <form class="px-3">
                                        @php
                                            // Delivery options for THIS store. Dine-in (3) removed platform-wide;
                                            // read the store's own setting (avoids the appdata wrong-vendor fallback),
                                            // keep only Delivery (1) / Pickup (2), and default to both if unset.
                                            $ct_dtype = \App\Models\Settings::where('vendor_id', @$vdata)->value('delivery_type');
                                            $delivery_types = array_values(array_filter(
                                                array_map('trim', explode(',', (string) $ct_dtype)),
                                                fn($t) => $t === '1' || $t === '2'
                                            ));
                                            if (empty($delivery_types)) {
                                                $delivery_types = ['1', '2'];
                                            }
                                        @endphp
                                        @foreach ($delivery_types as $key => $delivery_type)
                                            <div class="col-12 px-0 mb-2">
                                                <label
                                                    class="form-check-label d-flex mx-0 justify-content-between align-items-center"
                                                    for="cart-delivery-{{ $delivery_type }}">
                                                    <div class="d-flex align-items-center">
                                                        <input class="form-check-input m-0" type="radio"
                                                            name="cart-delivery" id="cart-delivery-{{ $delivery_type }}"
                                                            value="{{ $delivery_type }}" {{ $key == 0 ? 'checked' : '' }}>
                                                        <p class="px-2">
                                                            @if ($delivery_type == 1)
                                                                {{ trans('labels.delivery') }}
                                                            @elseif($delivery_type == 2)
                                                                {{ trans('labels.pickup') }}
                                                            @endif
                                                        </p>

                                                    </div>
                                                </label>
                                            </div>
                                        @endforeach
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="border rounded py-0 mb-4" id="data_time">
                        <div class="card border-0 select-delivery rounded-4">
                            <div class="card-body">
                                <div class="row gx-sm-3 gx-0">
                                    <div class="d-flex color-changer align-items-center mb-3 border-bottom pb-2">
                                        <i class="fa-regular fa-clock"></i>
                                        <p class="title px-2">{{ trans('labels.date_time') }}</p>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label for="Name" class="form-label"
                                            id="delivery_date">{{ trans('labels.delivery_date') }}
                                            <span class="text-danger"> * </span></label>
                                        <label for="Name" class="form-label"
                                            id="pickup_date">{{ trans('labels.pickup_date') }}
                                            <span class="text-danger"> * </span></label>
                                        <input type="text" class="form-control input-h delivery_pickup_date"
                                            id="delivery_dt" value="" placeholder="Delivery date" required>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label for="Name" class="form-label"
                                            id="delivery">{{ trans('labels.delivery_time') }}
                                            <span class="text-danger"> * </span></label>
                                        <label for="Name" class="form-label"
                                            id="pickup">{{ trans('labels.pickup_time') }}
                                            <span class="text-danger"> * </span></label>
                                        <label id="store_close"
                                            class="d-none text-danger">{{ trans('labels.today_store_closed') }}</label>
                                        <input type="hidden" name="store_id" id="store_id" value="{{ @$vdata }}">
                                        <input type="hidden" name="sloturl" id="sloturl"
                                            value="{{ URL::to(@$storeinfo->slug . '/timeslot') }}">
                                        <select name="delivery_time" id="delivery_time" class="form-select input-h"
                                            required>
                                            <option value="{{ old('delivery_time') }}">{{ trans('labels.select') }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="border rounded py-0 mb-4" id="open">
                        <div class="card border-0 select-delivery rounded-4">
                            <div class="card-body">
                                <form action="#" method="get">
                                    <div class="row gx-sm-3 gx-0">
                                        <div class="d-flex color-changer align-items-center mb-3 border-bottom pb-2">
                                            <i class="fa-regular fa-circle-question"></i>
                                            <p class="title px-2">{{ trans('labels.delivery_info') }}</p>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label for="validationDefault"
                                                class="form-label">{{ trans('labels.address') }}<span
                                                    class="text-danger"> * </span></label>
                                            <input type="text" class="form-control input-h" name="address"
                                                id="address" placeholder="{{ trans('labels.address') }}">
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label for="validationDefault"
                                                class="form-label">{{ trans('labels.landmark') }}<span
                                                    class="text-danger"> </span></label>
                                            <input type="text" class="form-control input-h" name="landmark"
                                                id="landmark" placeholder="{{ trans('labels.landmark') }}">
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label for="validationDefault"
                                                class="form-label">{{ trans('labels.building') }}</label>
                                            <input type="text" class="form-control input-h" name="building"
                                                id="building" placeholder="{{ trans('labels.building') }}">
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label for="validationDefault"
                                                class="form-label">{{ trans('labels.pincode') }}</label>
                                            <input type="number" class="form-control input-h"
                                                placeholder="{{ trans('labels.pincode') }}" name="postal_code"
                                                id="postal_code">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="border rounded py-0 mb-4">
                        <div class="card border-0 select-delivery rounded-4">
                            <div class="card-body">
                                <form action="#" method="get">
                                    <div class="row gx-sm-3 gx-0">
                                        <div class="d-flex color-changer align-items-center mb-3 border-bottom pb-2">
                                            <i class="fa-regular fa-address-card"></i>
                                            <p class="title px-2">{{ trans('labels.customer') }}</p>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label for="validationDefault"
                                                class="form-label">{{ trans('labels.name') }}<span class="text-danger">*
                                                </span></label>
                                            <input type="text" class="form-control input-h"
                                                placeholder="{{ trans('labels.name') }}" name="customer_name"
                                                id="customer_name"
                                                value="{{ @Auth::user() && @Auth::user()->type == 3 ? @Auth::user()->name : '' }}">
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label for="validationDefault"
                                                class="form-label">{{ trans('labels.mobile') }}<span class="text-danger">
                                                    * </span></label>
                                            <input type="number" class="form-control input-h"
                                                placeholder="{{ trans('labels.mobile') }}" name="customer_mobile"
                                                id="customer_mobile"
                                                value="{{ @Auth::user() && @Auth::user()->type == 3 ? @Auth::user()->mobile : '' }}">
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label for="validationDefault"
                                                class="form-label">{{ trans('labels.email') }}<span class="text-danger">*
                                                </span></label>
                                            <input type="email" class="form-control input-h"
                                                placeholder="{{ trans('labels.email') }}" name="customer_email"
                                                id="customer_email"
                                                value="{{ @Auth::user() && @Auth::user()->type == 3 ? @Auth::user()->email : '' }}">
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label">{{ trans('labels.note') }}<span
                                                    class="text-danger"> </span></label>
                                            <textarea id="notes" name="notes" class="form-control input-h" rows="5" aria-label="With textarea"
                                                placeholder="{{ trans('labels.message') }}" value=""></textarea>
                                        </div>
                                        <input type="hidden" id="vendor" name="vendor"
                                            value="{{ $vdata }}" />
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @if (App\Models\SystemAddons::where('unique_identifier', 'vendor_tip')->first() != null &&
                            App\Models\SystemAddons::where('unique_identifier', 'vendor_tip')->first()->activated == 1)
                        @if (@helper::otherappdata($vdata)->tips_settings == 1)
                            <div class="border rounded py-0 mb-4">
                                <div class="card border-0 select-delivery rounded-4">
                                    <div class="card-body">
                                        <div class="d-flex color-changer align-items-center mb-3 border-bottom pb-2">
                                            <i class="fa-solid fa-coins"></i>
                                            <p class="title px-2">{{ trans('labels.tips_pro') }}</p>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group m-0">
                                                    <label for="add_amount"
                                                        class="form-label">{{ trans('labels.add_amount') }}</label>
                                                    <input type="number" class="form-control input-h" id="add_amount"
                                                        placeholder="{{ trans('labels.add_amount') }} . . . .">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
                <div class="col-md-12 col-lg-4">
                    <input type="hidden" id="discount_amount" value="{{ Session::get('offer_amount') }}" />
                    <input type="hidden" id="offer_type" value="{{ Session::get('offer_type') }}" />
                    <input type="hidden" name="coupon_code" id="coupon_code" value="{{ Session::get('offer_code') }}">
                    @if (App\Models\SystemAddons::where('unique_identifier', 'subscription')->first() != null &&
                            App\Models\SystemAddons::where('unique_identifier', 'subscription')->first()->activated == 1)
                        @if (App\Models\SystemAddons::where('unique_identifier', 'coupon')->first() != null &&
                                App\Models\SystemAddons::where('unique_identifier', 'coupon')->first()->activated == 1)
                            @php
                                if (helper::vendordata(@$vdata)->allow_without_subscription == 1) {
                                    $promocode = 1;
                                } else {
                                    $promocode = helper::get_plan(@$vdata)->coupons;
                                }
                            @endphp
                            @if ($promocode == 1)
                                <div class="border rounded py-0 mb-4 @if (@$coupons->count() == 0 || Session::get('offer_type') == 'loyalty') d-none @endif"
                                    id="promocodesection">
                                    <div class="card border-0 select-delivery rounded-4">
                                        <div class="card-body  px-sm-3 px-2 justify-content-between align-items-center">
                                            <p class="title color-changer border-bottom px-2 pb-2 mb-2">
                                                <i class="fa-solid fa-badge-percent"></i>
                                                <span class="px-2">
                                                    {{ trans('labels.apply_coupon') }}
                                                </span>
                                            </p>
                                            <div class="d-flex gap-3 align-items-center">
                                                <input type="text" class="form-control rounded-2 offer-input"
                                                    value="{{ Session::has('offer_code') ? Session::get('offer_code') : '' }}"
                                                    name="promocode" id="couponcode"
                                                    placeholder="{{ trans('labels.coupon_code') }}" readonly>

                                                <button class="btn btn-md mb-0 btn-store d-none" id="btnremove"
                                                    onclick="RemoveCopon()">{{ trans('labels.remove') }}</button>

                                                <button class="btn btn-md mb-0 btn-store d-block" id="btnapply"
                                                    onclick="ApplyCopon()">{{ trans('labels.apply') }}</button>
                                            </div>
                                            <input type="hidden" id="removecouponurl"
                                                value="{{ URL::to('/cart/removepromocode') }}" />
                                            <input type="hidden" id="applycouponurl"
                                                value="{{ URL::to('/cart/applypromocode') }}" />
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if (@Auth::user() && Auth::user()->type == 3)
                                @if (loyaltyhelper::getloyaltydata($vdata) != null && loyaltyhelper::getloyaltydata($vdata)->is_available == 1)
                                    <div class="border rounded py-0 mb-4 @if (Session::get('offer_type') == 'promocode') d-none @endif"
                                        id="loyaltysection">
                                        <div class="card border-0 select-delivery rounded-4">
                                            <div class="card-body px-sm-3 px-2">
                                                <div class="d-flex align-items-start border-bottom pb-2 px-0">
                                                    <div class="color-changer">
                                                        <p class="title px-2"><i class="fa-solid fa-trophy"></i>
                                                            <span class="px-2"
                                                                id="loyalty_program">{{ trans('labels.loyalty_program') }}</span>
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="row mt-2 align-items-center">
                                                    <p class="apply-coupon-subtitle col-10" id="loyalty_desc">
                                                        {{ trans('labels.you_have_currently') }}
                                                        <b>{{ @loyaltyhelper::availablepoints(@Auth::user()->id, @$vdata) }}</b>
                                                        {{ trans('labels.use_it_on_order') }}
                                                    </p>
                                                    <div class="px-2 py-2">
                                                        <h6 class="fw-600 color-changer">1 {{ trans('labels.point') }} =
                                                            {{ helper::currency_formate(@loyaltyhelper::getloyaltydata(@$vdata)->per_coin_amount, @$vdata) }}
                                                        </h6>

                                                        @if (loyaltyhelper::availablepoints(@Auth::user()->id, @$vdata) > 0)
                                                            <div class="d-flex mt-2 gap-2">
                                                                <input type="text" class="form-control input-h"
                                                                    name="points" id="points"
                                                                    placeholder="{{ trans('labels.enter_point') }}"
                                                                    value="{{ Session::get('offer_code') }}"
                                                                    @if (Session::get('offer_type') == 'loyalty') readonly @endif>
                                                                <input type="hidden" id="applyredeempoints"
                                                                    value="{{ URL::to('/cart/applyredeempoints') }}" />
                                                                <input type="hidden" id="removeredeempoints"
                                                                    value="{{ URL::to('/cart/removeredeempoints') }}" />
                                                                <button class="btn btn-md mb-0 btn-store d-none"
                                                                    href="javascript:void(0)" id="btnremovepoint"
                                                                    onclick="RemovePoints()">{{ trans('labels.remove') }}</button>
                                                                <button class="btn btn-md mb-0 btn-store d-block"
                                                                    id="btnredeempoint"
                                                                    onclick="RedeemPoints('{{ @$vdata }}')">{{ trans('labels.redeem') }}</button>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        @endif
                    @else
                        @if (App\Models\SystemAddons::where('unique_identifier', 'coupon')->first() != null &&
                                App\Models\SystemAddons::where('unique_identifier', 'coupon')->first()->activated == 1)
                            <div class="border rounded py-0 mb-4" id="promocodesection">
                                <div class="card border-0 select-delivery rounded-4">
                                    <div class="card-body  px-sm-3 px-2 justify-content-between align-items-center">
                                        <p class="title border-bottom color-changer px-2 pb-2 mb-2">
                                            <i class="fa-solid fa-badge-percent"></i>
                                            <span class="px-2">
                                                {{ trans('labels.apply_coupon') }}
                                            </span>
                                        </p>
                                        <div class="d-flex gap-3 align-items-center">
                                            <input type="text" class="form-control rounded-2 offer-input"
                                                value="{{ Session::has('offer_code') ? Session::get('offer_code') : '' }}"
                                                name="promocode" id="couponcode"
                                                placeholder="{{ trans('labels.coupon_code') }}" readonly>

                                            <button class="btn btn-md mb-0 btn-store d-none" id="btnremove"
                                                onclick="RemoveCopon()">{{ trans('labels.remove') }}</button>

                                            <button class="btn btn-md mb-0 btn-store d-block" id="btnapply"
                                                onclick="ApplyCopon()">{{ trans('labels.apply') }}</button>
                                        </div>
                                        <input type="hidden" id="removecouponurl"
                                            value="{{ URL::to('/cart/removepromocode') }}" />
                                        <input type="hidden" id="applycouponurl"
                                            value="{{ URL::to('/cart/applypromocode') }}" />
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

                    @if (App\Models\SystemAddons::where('unique_identifier', 'shipping_area')->first() != null &&
                            App\Models\SystemAddons::where('unique_identifier', 'shipping_area')->first()->activated == 1)
                        @if (helper::appdata($vdata)->shipping_area == 1)
                            @if (count($getshippingarealist) > 0)
                                <div class="border rounded py-0 mb-4" id="shippinginfodiv">
                                    <div class="card border-0 select-delivery rounded-4">
                                        <div class="card-body  px-sm-3 px-2 justify-content-between align-items-center">
                                            <p class="title color-changer border-bottom px-2 pb-2 mb-2">
                                                <i class="fa-solid fa-truck-fast"></i>
                                                <span class="px-2">
                                                    {{ trans('labels.shipping_area') }}
                                                </span>
                                            </p>
                                            <div class="d-flex gap-3 align-items-center">
                                                <select name="shipping_area" id="shipping_area"
                                                    class="form-select input-h">
                                                    <option value="" selected disabled>{{ trans('labels.select') }}
                                                    </option>
                                                    @foreach ($getshippingarealist as $shippingarea)
                                                        <option value="{{ $shippingarea->id }}"
                                                            data-delivery-charge="{{ $shippingarea->delivery_charge }}"
                                                            data-area-name="{{ $shippingarea->area_name }}">
                                                            {{ $shippingarea->area_name }}
                                                            @if (helper::appdata($vdata)->min_order_amount_for_free_shipping > $total_price)
                                                                @if ($shippingarea->delivery_charge > 0)
                                                                    {{ trans('labels.delivery_charge') }}
                                                                    :
                                                                    {{ helper::currency_formate($shippingarea->delivery_charge, @$vdata) }}
                                                                @endif
                                                            @else
                                                                {{ trans('labels.free_delivery') }}
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    @endif
                    <div class="border rounded py-0 mb-4">
                        <div class="card border-0 select-delivery rounded-4">
                            <div class="card-body row gx-sm-3 gx-0">
                                <div class="d-flex color-changer align-items-center border-bottom pb-2">
                                    <i class="fa-solid fa-basket-shopping"></i>
                                    <p class="title px-2">{{ trans('labels.order_summary') }}</p>
                                </div>
                                <div class="col">

                                    <ul class="list-group list-group-flush order-summary-list" id="payment_summery_list">
                                        <li class="list-group-item color-changer">
                                            {{ trans('labels.sub_total') }}
                                            <span class="color-changer">
                                                {{ helper::currency_formate($total_price, @$vdata) }}
                                            </span>
                                        </li>

                                        @php
                                            if (
                                                Session::get('offer_type') == 'promocode' ||
                                                Session::get('offer_type') == 'loyalty'
                                            ) {
                                                $discount = Session::get('offer_amount');
                                            } else {
                                                $discount = 0;
                                            }
                                        @endphp
                                        <li class="list-group-item color-changer @if (Session::get('offer_type') == '') d-none @endif"
                                            id="discount_1">
                                            {{ trans('labels.discount') }}
                                            <span id="offer_amount color-changer">
                                                - {{ helper::currency_formate(@$discount, @$vdata) }}
                                            </span>
                                        </li>
                                        @php
                                            $totalcarttax = 0;
                                        @endphp
                                        @foreach ($taxArr['tax'] as $k => $tax)
                                            @php
                                                $rate = $taxArr['rate'][$k];
                                                $totalcarttax += (float) $taxArr['rate'][$k];
                                            @endphp
                                            <li class="list-group-item color-changer" id="tax_list">
                                                {{ $tax }}
                                                <span class="color-changer">
                                                    {{ helper::currency_formate($rate, $vdata) }}
                                                </span>
                                            </li>
                                        @endforeach

                                        <li class="list-group-item color-changer" id="delivery-charge-section">
                                            {{ trans('labels.delivery_charge') }}
                                            @if (App\Models\SystemAddons::where('unique_identifier', 'shipping_area')->first() != null &&
                                                    App\Models\SystemAddons::where('unique_identifier', 'shipping_area')->first()->activated == 1)
                                                @if (helper::appdata($vdata)->shipping_area == 1)
                                                    @if (count($getshippingarealist) > 0)
                                                        @php
                                                            $grand_total = $total_price - $discount + $totalcarttax;
                                                        @endphp
                                                        @if ($total_price >= helper::appdata($vdata)->min_order_amount_for_free_shipping)
                                                            <input type="hidden" name="delivery_charge"
                                                                id="delivery_charge" value="0">

                                                            <span
                                                                class="delivery_charge color-changer">{{ trans('labels.free') }}</span>
                                                        @else
                                                            <span
                                                                class="delivery_charge color-changer">{{ helper::currency_formate(0, @$vdata) }}</span>

                                                            <input type="hidden" name="delivery_charge"
                                                                id="delivery_charge" value="0">
                                                        @endif
                                                    @else
                                                        @if ($total_price >= helper::appdata($vdata)->min_order_amount_for_free_shipping)
                                                            @php
                                                                $grand_total = $total_price - $discount + $totalcarttax;
                                                            @endphp

                                                            <input type="hidden" name="delivery_charge"
                                                                id="delivery_charge" value="0">

                                                            <span
                                                                class="delivery_charge color-changer">{{ trans('labels.free') }}</span>
                                                        @else
                                                            @php
                                                                $grand_total =
                                                                    $total_price -
                                                                    $discount +
                                                                    $totalcarttax +
                                                                    helper::appdata($vdata)->shipping_charges;
                                                            @endphp

                                                            <span
                                                                class="delivery_charge color-changer">{{ helper::currency_formate(helper::appdata($vdata)->shipping_charges, @$vdata) }}</span>

                                                            <input type="hidden" name="delivery_charge"
                                                                id="delivery_charge"
                                                                value="{{ helper::appdata($vdata)->shipping_charges }}">
                                                        @endif
                                                    @endif
                                                @else
                                                    @if ($total_price >= helper::appdata($vdata)->min_order_amount_for_free_shipping)
                                                        @php
                                                            $grand_total = $total_price - $discount + $totalcarttax;
                                                        @endphp

                                                        <input type="hidden" name="delivery_charge" id="delivery_charge"
                                                            value="0">

                                                        <span class="delivery_charge color-changer">{{ trans('labels.free') }}</span>
                                                    @else
                                                        @php
                                                            $grand_total =
                                                                $total_price -
                                                                $discount +
                                                                $totalcarttax +
                                                                helper::appdata($vdata)->shipping_charges;
                                                        @endphp

                                                        <span
                                                            class="delivery_charge color-changer">{{ helper::currency_formate(helper::appdata($vdata)->shipping_charges, @$vdata) }}</span>

                                                        <input type="hidden" name="delivery_charge" id="delivery_charge"
                                                            value="{{ helper::appdata($vdata)->shipping_charges }}">
                                                    @endif
                                                @endif
                                            @else
                                                @if ($total_price >= helper::appdata($vdata)->min_order_amount_for_free_shipping)
                                                    @php
                                                        $grand_total = $total_price - $discount + $totalcarttax;
                                                    @endphp

                                                    <input type="hidden" name="delivery_charge" id="delivery_charge"
                                                        value="0">

                                                    <span class="delivery_charge color-changer">{{ trans('labels.free') }}</span>
                                                @else
                                                    @php
                                                        $grand_total =
                                                            $total_price -
                                                            $discount +
                                                            $totalcarttax +
                                                            helper::appdata($vdata)->shipping_charges;
                                                    @endphp

                                                    <span
                                                        class="delivery_charge color-changer">{{ helper::currency_formate(helper::appdata($vdata)->shipping_charges, @$vdata) }}</span>

                                                    <input type="hidden" name="delivery_charge" id="delivery_charge"
                                                        value="{{ helper::appdata($vdata)->shipping_charges }}">
                                                @endif
                                            @endif
                                        </li>
                                        <li class="list-group-item fw-700 text-success">
                                            {{ trans('labels.grand_total') }}
                                            <span class="fw-700 text-success" id="grand_total_view">
                                                {{ helper::currency_formate($grand_total, @$vdata) }}
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    @include('front.product.service-trusted')
                    <div class="border rounded py-0 mb-4">
                        <div class="card border-0 select-delivery rounded-4">
                            <div class="card-body">
                                <div class="radio-item-container px-sm-2 px-0">
                                    <div class="d-flex color-changer align-items-center border-bottom pb-2">
                                        <i class="fa-solid fa-money-check-dollar"></i>
                                        <p class="title px-2"> {{ trans('labels.payment_option') }}</p>
                                    </div>
                                    @php $key = 0; @endphp
                                    @foreach ($paymentlist as $payment)
                                        @php
                                            // Check if the current $payment is a system addon and activated
                                            if ($payment->isIncludedGateway()) {
                                                $systemAddonActivated = true;
                                            } else {
                                                $systemAddonActivated = false;
                                            }
                                            $addon = App\Models\SystemAddons::where(
                                                'unique_identifier',
                                                $payment->unique_identifier,
                                            )->first();
                                            if ($addon != null && $addon->activated == 1) {
                                                $systemAddonActivated = true;
                                            }
                                        @endphp
                                        @if ($systemAddonActivated)
                                            <div class="col-12 select-payment-list-items">
                                                <div class="form-check p-0 d-flex align-items-center gap-2">
                                                    <input class="form-check-input m-0" type="radio"
                                                        id="{{ $payment->payment_type }}" name="payment"
                                                        data-payment_type="{{ $payment->payment_type }}"
                                                        data-currency="{{ $payment->currency }}"
                                                        @if ($key++ == 0) checked @endif
                                                        value="{{ $payment->payment_type }}">
                                                    <label class="form-check-label m-0 w-100"
                                                        for="{{ $payment->payment_type }}">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <p class="fs-7">{{ $payment->payment_name }}</p>
                                                                @if (Auth::user())
                                                                    @if ($payment->payment_type == 16)
                                                                        <span
                                                                            class="fs-7 fw-500 text-dark">{{ helper::currency_formate(Auth::user()->wallet, $vdata) }}</span>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                            <img src="{{ helper::image_path($payment->image) }}"
                                                                alt="" class="select-paymentimages">
                                                        </div>
                                                    </label>
                                                    @if ($payment->payment_type == '2')
                                                        <input type="hidden" name="razorpay" id="razorpay"
                                                            value="{{ $payment->public_key }}">
                                                    @endif
                                                    @if ($payment->payment_type == '3')
                                                        <input type="hidden" name="stripekey" id="stripekey"
                                                            value="{{ $payment->public_key }}">
                                                        <input type="hidden" name="stripecurrency" id="stripecurrency"
                                                            value="{{ $payment->currency }}">
                                                    @endif
                                                    @if ($payment->payment_type == '4')
                                                        <input type="hidden" name="flutterwavekey" id="flutterwavekey"
                                                            value="{{ $payment->public_key }}">
                                                    @endif
                                                    @if ($payment->payment_type == '5')
                                                        <input type="hidden" name="paystackkey" id="paystackkey"
                                                            value="{{ $payment->public_key }}">
                                                    @endif
                                                    @if ($payment->payment_type == '6')
                                                        <input type="hidden"
                                                            value="{{ $payment->payment_description }}"
                                                            id="bank_payment">
                                                    @endif
                                                    {{-- V2 manual method info (shown when selected) --}}
                                                    @if (in_array($payment->payment_type, ['17', '18', '19', '20', '21']))
                                                        <div class="oc-pay-info w-100 mt-2 p-3 rounded border bg-light"
                                                            data-for="{{ $payment->payment_type }}" style="display:none;">
                                                            @if (in_array($payment->payment_type, ['19', '20']) && !empty($payment->qr_image))
                                                                <img src="{{ helper::image_path($payment->qr_image) }}" alt="QR"
                                                                    style="max-width:170px;border-radius:8px;display:block;margin-bottom:8px;">
                                                            @endif
                                                            @if ($payment->payment_type == '21' && !empty($payment->payment_link))
                                                                <a href="{{ $payment->payment_link }}" target="_blank"
                                                                    class="btn btn-sm btn-primary mb-2">
                                                                    <i class="fa-solid fa-up-right-from-square"></i>
                                                                    {{ trans('labels.pay_via_link') }}
                                                                </a>
                                                            @endif
                                                            @if (!empty($payment->payment_description))
                                                                <p class="fs-7 m-0 text-dark">{!! nl2br(e($payment->payment_description)) !!}</p>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        (function () {
                            function ocPayInfo() {
                                var sel = document.querySelector('input[name="payment"]:checked');
                                document.querySelectorAll('.oc-pay-info').forEach(function (b) { b.style.display = 'none'; });
                                if (sel) {
                                    var box = document.querySelector('.oc-pay-info[data-for="' + sel.value + '"]');
                                    if (box) box.style.display = 'block';
                                }
                            }
                            document.querySelectorAll('input[name="payment"]').forEach(function (r) { r.addEventListener('change', ocPayInfo); });
                            ocPayInfo();
                        })();
                    </script>
                    <button class="btn-primary btn py-3 fs-15 fw-500 text-center w-100 checkout"
                        onclick="Order()">{{ trans('labels.place_order') }}</button>
                </div>
            </div>
        </div>
    </section>

    @include('front.sum_qusction')

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

    @if (count($coupons) > 0)
        <div data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasExample">
            <div class="offers-label color-changer {{ session()->get('direction') == 2 ? 'offers-label-rtl' : 'offers-label-ltr' }}">
                <i class="fa-light fa-badge-percent text-white"></i>
                <div class="offers-label-name">{{ trans('labels.offer') }}</div>
            </div>
        </div>
    @endif
    <!-- Apply Coupon Modal Promocode -->
    <div class="offcanvas  {{ session()->get('direction') == 2 ? 'offcanvas-start' : 'offcanvas-end' }}" tabindex="-1"
        id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
        <div class="offcanvas-header justify-content-between border-bottom offcanvas-header-coupons">
            <h5 class="offcanvas-title color-changer" id="offcanvasRightLabel">{{ trans('labels.coupons_offers') }}</h5>
            <button type="button" class="bg-transparent border-0 m-0" data-bs-dismiss="offcanvas" aria-label="Close">
            <i class="fa-regular fa-xmark color-changer fs-4"></i>
        </button>
        </div>
        <div class="offcanvas-body">
            @foreach ($coupons as $coupon)
            <div class="card mb-3 promo-card position-relative rounded">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between" data-copy=true>
                                    <input type="hidden" id="applycoponurl"
                                        value="{{ URL::to('/cart/applypromocode') }}" />
                                    <span id="promocode" type="text" class="rounded color-changer coupons-label px-2 input-h"
                                        readonly value="">{{ $coupon->offer_code }}</span>
                                    <p class="cursor-pointer color-changer fs-15 fw-600"
                                        onclick="copyToClipboard('{{ $coupon->offer_code }}')">
                                        {{ trans('labels.copy') }}</p>
                                </div>
                                <div class="mt-2">
                                    <h6 class="mb-2 color-changer">
                                        {{ $coupon->offer_type == 1 ? helper::currency_formate($coupon->offer_amount, $vdata) : $coupon->offer_amount . '%' }}
                                        {{ trans('labels.coupons') }}</h6>
                                    <div class="coupons-content">
                                        <div class="mt-2">
                                            <h6 class="fs-15 color-changer fw-500">{{ $coupon->offer_name }}</h6>
                                            <p class="text-muted fw-400 fs-8 pt-1 mb-0">
                                                {{ Str::limit($coupon->description, 180) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
            @endforeach
        </div>
    </div>
@endsection
@section('script')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        let storeid = "{{ @$vdata }}";
        var showbutton = "{{ Session::get('offer_type') }}";
        var min_order_amount_for_free_shipping =
            "{{ helper::appdata(@$vdata)->min_order_amount_for_free_shipping }}";
        $(document).ready(function() {
            if (showbutton == 'promocode') {
                $('#btnremove').removeClass('d-none');
                $('#btnapply').addClass('d-none');
            } else {
                $('#btnremove').addClass('d-none');
                $('#btnapply').removeClass('d-none');
            }
            if (showbutton == 'loyalty') {
                $('#btnremovepoint').removeClass('d-none');
                $('#btnredeempoint').addClass('d-none');
            } else {
                $('#btnremovepoint').addClass('d-none');
                $('#btnredeempoint').removeClass('d-none');
            }
        });

        function ApplyCopon() {
            $('#btnapply').prop("disabled", true);
            $('#btnapply').html(
                '<span class="loader"></span>');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: $('#applycouponurl').val(),
                method: 'post',
                data: {
                    promocode: $('#couponcode').val(),
                    sub_total: $('#sub_total').val(),
                    vendor_id: $('#vendor').val(),
                },
                success: function(response) {
                    if (response.status == 1) {
                        var total = parseFloat($('#sub_total').val());
                        var tax = parseFloat($('#totaltax').val());
                        var delivery_charge = parseFloat($('#delivery_charge').val());
                        var discount = "";
                        if (response.data.offer_type == 1) {
                            discount = response.data.offer_amount;
                        }
                        if (response.data.offer_type == 2) {
                            discount = total * parseFloat(response.data.offer_amount) / 100;
                        }
                        if ($("input[name='cart-delivery']:checked").val() == 1) {
                            var grandtotal = parseFloat(total) + parseFloat(tax) + parseFloat(delivery_charge) -
                                parseFloat(discount);
                        } else {
                            var grandtotal = parseFloat(total) + parseFloat(tax) - parseFloat(discount);
                        }
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
                        $('#btnapply').addClass('d-none');
                        $('#btnapply').html("{{ trans('labels.apply') }}");
                        $('#btnapply').prop("disabled", false);
                        toastr.success(response.message);
                    } else {
                        $('#btnapply').html("{{ trans('labels.apply') }}");
                        $('#btnapply').prop("disabled", false);
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    $('#btnapply').html("{{ trans('labels.apply') }}");
                    $('#btnapply').prop("disabled", false);
                    toastr.error(wrong);
                }
            });
        }

        function RemoveCopon() {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success mx-1 yes-btn',
                    cancelButton: 'btn btn-danger mx-1 no-btn'
                },
                buttonsStyling: false
            })
            swalWithBootstrapButtons.fire({
                title: are_you_sure,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: yes,
                cancelButtonText: no,
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#btnremove').prop("disabled", true);
                    $('#btnremove').html(
                        '<span class="loader"></span>');
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: $('#removecouponurl').val(),
                        method: 'post',
                        data: {
                            promocode: $('#couponcode').val()
                        },
                        success: function(response) {
                            if (response.status == 1) {
                                var total = $('#sub_total').val();
                                var tax = parseFloat($('#totaltax').val());
                                var delivery_charge = $('#delivery_charge').val();
                                var discount = 0;
                                if ($("input[name='cart-delivery']:checked").val() == 1) {
                                    var grandtotal = parseFloat(total) + parseFloat(tax) + parseFloat(
                                            delivery_charge) -
                                        parseFloat(discount);
                                } else {
                                    var grandtotal = parseFloat(total) + parseFloat(tax) - parseFloat(
                                        discount);
                                }
                                $('#loyaltysection').removeClass('d-none');
                                $('#discount_1').addClass('d-none');
                                $('#offer_amount').text('- ' + currency_formate(parseFloat(0)));
                                $('#grand_total_view').html(currency_formate(grandtotal));
                                $('#couponcode').val('');
                                $('#coupon_code').val('');
                                $('#offer_type').val('');
                                $('#points').val('');
                                $('#grand_total').val(grandtotal);
                                $('#discount_amount').val(discount);
                                $('#btnremove').addClass('d-none');
                                $('#btnapply').removeClass('d-none');
                                $('#btnremove').html("{{ trans('labels.remove') }}");
                                $('#btnremove').prop("disabled", false);
                                toastr.success(response.message);
                            } else {
                                $('#btnremove').html("{{ trans('labels.remove') }}");
                                $('#btnremove').prop("disabled", false);
                                toastr.error(response.message);
                            }
                        },
                        error: function() {
                            $('#btnremove').html("{{ trans('labels.remove') }}");
                            $('#btnremove').prop("disabled", false);
                            toastr.error(wrong);
                        }
                    });
                } else if (
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    Swal.DismissReason.cancel
                }
            })
        }

        var select = "{{ trans('labels.select') }}";
        var dateFormat = "{{ helper::appdata($vdata)->date_format }}";
        var placeholderFormat = dateFormat
            .replace(/Y/g, 'yyyy') // Full year
            .replace(/m/g, 'mm') // Month
            .replace(/d/g, 'dd'); // Day

        document.getElementById("delivery_dt").setAttribute("placeholder", placeholderFormat);

        flatpickr(".delivery_pickup_date", {
            dateFormat: dateFormat,
            enableTime: false,
            altInput: true,
            altFormat: dateFormat,
            minDate: 'today'
        });
    </script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="https://checkout.flutterwave.com/v3.js"></script>
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/custom/checkout.js') }}"></script>
@endsection
