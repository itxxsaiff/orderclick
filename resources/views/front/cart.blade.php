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
                        {{ trans('labels.my_cart') }}
                    </li>
                </ol>
            </nav>
        </div>
    </section>
    <!-- breadcrumb end -->
    <!-- My Cart section start -->

    @if (count($cartdata) > 0)
        <section class="my-5">
            <div class="container">
                @if (App\Models\SystemAddons::where('unique_identifier', 'cart_checkout_countdown')->first() != null &&
                        App\Models\SystemAddons::where('unique_identifier', 'cart_checkout_countdown')->first()->activated == 1)
                    @include('front.cart_checkout_countdown')
                @endif
                <div class="table-responsive cart-view">
                    <table class="table m-0 rounded-2 overflow-hidden" id="update_qty">
                        <thead class="bg-dark bg-change-mode">
                            <tr>
                                <th scope="col" class="cart-table-title text-light p-3">
                                    {{ trans('labels.product') }}
                                </th>
                                <th scope="col" class="cart-table-title text-center text-light p-3">
                                    {{ trans('labels.price') }}
                                </th>
                                <th scope="col" class="cart-table-title text-center text-light p-3">
                                    {{ trans('labels.qty') }}
                                </th>
                                <th scope="col" class="cart-table-title text-center text-light p-3">
                                    {{ trans('labels.total') }}
                                </th>
                                <th scope="col" class="cart-table-title text-center text-light p-3">
                                    {{ trans('labels.action') }}
                                </th>
                            </tr>
                        </thead>
                        @php
                            $subtotal = 0;
                        @endphp
                        <tbody class="order-list">
                            @foreach ($cartdata as $cart)
                                @php
                                    $subtotal += $cart->price * $cart->qty;
                                @endphp
                                <tr class="align-middle text-center">
                                    <td class="p-3">
                                        <div class="tbl_cart_product gap-3">
                                            <div class="item-img d-lg-block d-none">
                                                <img src="{{ !empty($cart->item_image) ? asset('storage/app/public/item/' . $cart->item_image) : helper::food_image($cart->item_name, $cart->item_id) }}"
                                                    class="card-img-top p-0 object-fit-cover border rounded-4"
                                                    alt="...">
                                            </div>
                                            <div
                                                class="row flex-column gap-1 {{ session()->get('direction') == 2 ? 'text-end' : 'text-start' }}">
                                                <div class="item-title mb-0">
                                                    <p class="text-dark td_a m-0 text-capitalize">
                                                        {{ $cart->item_name }}
                                                    </p>
                                                </div>
                                                <li class="m-0">
                                                    @if ($cart->variants_id != '' || $cart->extras_id != '')
                                                        <a href="javascript:void(0)" class="text-muted text-capitalize fs-7"
                                                            onclick="showextra('{{ @$cart->variants_name }}','{{ @$cart->item_price }}','{{ @$cart->extras_name }}','{{ @$cart->extras_price }}','{{ @$cart->item_name }}')">
                                                            {{ trans('labels.customize') }}
                                                        </a>
                                                    @endif
                                                </li>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-3">
                                        <p class="item-price td_a text-center">
                                            {{ helper::currency_formate($cart->price, $storeinfo->id) }}
                                        </p>
                                    </td>
                                    <td class="p-3">
                                        <div class="d-flex justify-content-center">
                                            <div
                                                class="input-group d-flex qty-input-cart responsive-margin m-0 rounded-2 hight-modal-btn align-items-center">
                                                <button class="btn p-0 change-qty-2"
                                                    onclick="qtyupdate('{{ $cart->id }}','{{ $cart->item_id }}','minus')"
                                                    value="minus value" href="javascript:void(0)" aria-label="Previous">
                                                    <i class="fa fa-minus"></i>
                                                </button>
                                                <input type="text" class="border-0 color-changer bg-transparent text-center"
                                                    id="number_{{ $cart->id }}" name="number"
                                                    value="{{ $cart->qty }}" min="1" max="10" readonly>
                                                <button class="btn p-0 change-qty-2"
                                                    onclick="qtyupdate('{{ $cart->id }}','{{ $cart->item_id }}','plus')"
                                                    value="plus value" href="javascript:void(0)" aria-label="Next">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-3">
                                        @php
                                            $grand_total = $cart->price * $cart->qty;
                                        @endphp
                                        <p class="fs-15 fw-600">
                                            {{ helper::currency_formate($grand_total, $storeinfo->id) }}
                                        </p>
                                    </td>

                                    <td class="p-3">
                                        <div class="d-flex justify-content-center">
                                            <a href="javascript:void(0)" onclick="RemoveCart('{{ $cart->id }}')"
                                                class="delete-icon" tooltip="Remove">
                                                <i class="fa-solid fa-trash-can text-light"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="text-muted text-end fs-7 line-2 mt-2">{{ trans('messages.cart_note') }}</p>
                @if (App\Models\SystemAddons::where('unique_identifier', 'cart_checkout_progressbar')->first() != null &&
                        App\Models\SystemAddons::where('unique_identifier', 'cart_checkout_progressbar')->first()->activated == 1)
                    @include('front.cart_checkout_progressbar')
                @endif
                <div class="row g-3 justify-content-between mt-3 mb-xl-0 mb-3">
                    <div class="col-lg-3 col-md-5 col-sm-6">
                        <a href="{{ URL::to(@$storeinfo->slug) }}" type="button"
                            class="btn fs-15 fw-500 btn-outline-dark px-sm-4 py-2 w-100 d-flex gap-2 justify-content-center">
                            <i class="fa-light fa-arrow-left-long fw-600"></i>
                            <span class="fw-500">{{ trans('labels.return_shop') }}</span>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-5 col-sm-6">
                        <button
                            class="btn btn-primary px-sm-4 fs-15 w-100 d-flex gap-2 justify-content-center px-sm-4 py-2"id="cartcheckout">
                            <span class="fw-500">{{ trans('labels.checkout') }}</span>
                            <i class="fa-light fa-arrow-right-long fw-600"></i>
                        </button>
                    </div>
                </div>
            </div>
        </section>
        <input type="hidden" id="removecart" value="{{ URL::to('/cart/deletecartitem') }}" />
        <input type="hidden" id="qtyupdate_url" value="{{ URL::to('/cart/qtyupdate') }}" />
    @else
        @include('front.nodata')
    @endif
    @include('front.sum_qusction')
    <!-- My Cart section end -->
@endsection
@section('script')
    <script>
        var variation_title = "{{ trans('labels.variation') }}";
        var extra_title = "{{ trans('labels.extras') }}";

        $('#cartcheckout').on('click', function() {
            "use strict";
            $('#cartcheckout').prop("disabled", true);
            $('#cartcheckout').html(
                '<span class="loader"></span>');
            @if (Auth::user() && Auth::user()->type == 3)
                location.href = "{{ URL::to(@$storeinfo->slug . '/checkout?buy_now=0') }}";
            @else
                @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                    @if (helper::appdata($storeinfo->id)->is_checkout_login_required == 1)
                        location.href = "{{ URL::to(@$storeinfo->slug . '/login') }}";
                    @else
                        $("#exampleModalToggle").on('hidden.bs.modal', function(e) {
                            $('#cartcheckout').html(
                                "<span class='fw-500'>{{ trans('labels.checkout') }}</span><i class='fa-light fa-arrow-right-long fw-600'></i>"
                            );
                            $('#cartcheckout').prop("disabled", false);
                        });
                        $('#exampleModalToggle').modal('show');
                    @endif
                @else
                    location.href = "{{ URL::to(@$storeinfo->slug . '/checkout?buy_now=0') }}";
                @endif
            @endif
        });
    </script>
    <script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/custom/cart.js') }}" type="text/javascript"></script>
@endsection
