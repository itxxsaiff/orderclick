@extends('front.theme.default')
@section('content')
    <!-- breadcrumb start -->
    <section class="breadcrumb-sec bg-change-mode">
        <div class="container">
            <nav>
                <ol class="breadcrumb d-flex m-0 text-capitalize">
                    <li class="breadcrumb-item"><a href="{{ URL::to($storeinfo->slug) }}"
                            class="text-dark color-changer">{{ trans('labels.home') }}</a></li>

                    <li
                        class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-item-right' : 'breadcrumb-item-left' }}">
                        {{ trans('labels.order_details') }}
                    </li>
                </ol>
            </nav>
        </div>
    </section>
    <!-- breadcrumb end -->
    <section class="py-sm-5 py-4">
        <div class="container">
            <div class="row g-3">
                <div class="col-lg-8 order-det-card">
                    <div class="card rounded mb-4">
                        <div class="card-body">
                            <div class="d-flex color-changer align-items-center pb-3 border-bottom mb-3">
                                <i class="fa-solid fa-cart-flatbed"></i>
                                <p class="title px-2">{{ trans('labels.order_details') }}</p>
                            </div>
                            <div class="card border-0 p-0">
                                <div class="card-body p-0">
                                    <div class="order-details bg-change-mode">
                                        <ul class="row">
                                            <li class="col-md-6 col-lg-3 col-6">
                                                <a>{{ trans('labels.order_date') }}</a>
                                                <p>{{ helper::date_format($orderdata->created_at, $orderdata->vendor_id) }}
                                                </p>
                                            </li>
                                            <li class="border-start col-md-6 col-lg-3 mt-md-0 mt-lg-0 col-6">
                                                <a>{{ trans('labels.status') }}</a>
                                                <div class="d-flex align-items-center pt-1">
                                                    <p class="pt-0 text-center m-auto">
                                                        {{ @helper::gettype($orderdata->status, $orderdata->status_type, $orderdata->order_type, $orderdata->vendor_id)->name == null ? '-' : @helper::gettype($orderdata->status, $orderdata->status_type, $orderdata->order_type, $orderdata->vendor_id)->name }}

                                                    </p>
                                                </div>
                                            </li>
                                            <li class="border-start col-md-6 col-lg-3 mt-4 mt-lg-0 col-6">
                                                <a>{{ trans('labels.type') }}</a>
                                                <p>
                                                    @if ($orderdata->order_type == 1)
                                                        {{ trans('labels.delivery') }}
                                                    @elseif($orderdata->order_type == 2)
                                                        {{ trans('labels.pickup') }}
                                                    @elseif($orderdata->order_type == 3)
                                                        {{ trans('labels.dine_in') }}
                                                    @endif
                                                </p>
                                                @if ($orderdata->order_type == 3)
                                                    <span class="fs-8">( {{ $orderdata['tableqr']->name }} )</span>
                                                @endif
                                            </li>
                                            <li class="border-start col-md-6 col-lg-3 mt-4 mt-lg-0 col-6">
                                                <a>{{ trans('labels.order') }}</a>
                                                <div class="d-flex justify-content-center align-items-center pt-1">
                                                    <strong class="pt-0 color-changer">#{{ $orderdata->order_number }}</strong>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive cart-view mt-3">
                                <table class="table m-0 rounded-2 overflow-hidden" id="update_qty">
                                    <thead class="bg-dark">
                                        <tr>
                                            <th scope="col" class="cart-table-title fs-15 fw-500 text-light p-3">
                                                {{ trans('labels.products') }}
                                            </th>
                                            <th scope="col"
                                                class="cart-table-title fs-15 fw-500 text-center text-light p-3">
                                                {{ trans('labels.price') }}
                                            </th>
                                            <th scope="col"
                                                class="cart-table-title fs-15 fw-500 text-center text-light p-3">
                                                {{ trans('labels.qty') }}
                                            </th>
                                            <th scope="col"
                                                class="cart-table-title fs-15 fw-500 text-center text-light p-3">
                                                {{ trans('labels.total') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="order-list">
                                        @foreach ($orderdetails as $odata)
                                            <tr class="align-middle text-center">
                                                <td class="p-3">
                                                    <div class="tbl_cart_product gap-3">
                                                        <div class="item-img">
                                                            <img src="{{ asset('storage/app/public/item/' . $odata->item_image) }}"
                                                                class="card-img-top p-0 object-fit-cover border rounded"
                                                                alt="...">
                                                        </div>
                                                        <div
                                                            class="row flex-column gap-1 {{ session()->get('direction') == 2 ? 'text-end' : 'text-start' }}">
                                                            <div class="item-title mb-0">
                                                                <p class="text-dark td_a m-0 text-capitalize">
                                                                    {{ $odata->item_name }}
                                                                </p>
                                                            </div>
                                                            <li class="m-0">
                                                                @if ($odata->variants_id != '' || $odata->extras_id != '')
                                                                    <a class="customisable td_a cursor-pointer"
                                                                        onclick="showextra('{{ $odata->variants_name }}','{{ $odata->variants_price }}','{{ $odata->extras_name }}','{{ $odata->extras_price }}','{{ $odata->item_name }}')">{{ trans('labels.customize') }}</a>
                                                                @endif
                                                                <a class="customisable cursor-pointer"></a>
                                                            </li>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="p-3">
                                                    <p class="item-price td_a text-center">
                                                        {{ helper::currency_formate($odata->price, $orderdata->vendor_id) }}
                                                    </p>
                                                </td>
                                                <td class="p-3">
                                                    <p class="item-price td_a text-center">
                                                        {{ $odata->qty }}
                                                    </p>
                                                </td>
                                                <td class="p-3">
                                                    <p class="fs-15 td_a fw-600">
                                                        {{ helper::currency_formate($odata->qty * $odata->price, $orderdata->vendor_id) }}
                                                    </p>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card rounded mb-4">
                        <div class="card-body">
                            <div class="d-flex gap-2 color-changer align-items-center pb-3 border-bottom">
                                <i class="fa-regular fa-credit-card"></i>
                                <p class="title">{{ trans('labels.payment_summary') }}</p>
                            </div>
                            <ul class="list-group list-group-flush order-summary-list">
                                <li class="list-group-item color-changer">
                                    {{ trans('labels.sub_total') }}
                                    <span class="color-changer">
                                        {{ helper::currency_formate(@$summery['sub_total'], $orderdata->vendor_id) }}
                                    </span>
                                </li>
                                @php
                                    $tax = explode('|', $summery['tax']);
                                    $tax_name = explode('|', $summery['tax_name']);
                                @endphp
                                @if ($summery['tax'] != null && $summery['tax'] != '')
                                    @foreach ($tax as $key => $tax_value)
                                        <li class="list-group-item color-changer">
                                            {{ $tax_name[$key] }}
                                            <span class="color-changer">
                                                {{ helper::currency_formate(@(float) $tax[$key], $orderdata->vendor_id) }}
                                            </span>
                                        </li>
                                    @endforeach
                                @endif
                                @if ($summery['order_type'] == 1)
                                    <li class="list-group-item color-changer">
                                        {{ trans('labels.delivery') }}
                                        @if ($summery['delivery_area'] != '')
                                            ({{ $summery['delivery_area'] }})
                                        @endif
                                        <span class="color-changer">
                                            @if ($summery['delivery_charge'] > 0)
                                                {{ helper::currency_formate(@$summery['delivery_charge'], $orderdata->vendor_id) }}
                                            @else
                                                {{ trans('labels.free') }}
                                            @endif
                                        </span>
                                    </li>
                                @endif
                                @if ($summery['discount_amount'] > 0)
                                    <li class="list-group-item color-changer">
                                        @if ($summery['offer_type'] == 'loyalty')
                                            {{ trans('labels.loyalty_discount') }} (-)
                                        @endif
                                        @if ($summery['offer_type'] == 'promocode')
                                            {{ trans('labels.discount') }}
                                            ({{ $summery['couponcode'] }})
                                        @endif
                                        <span class="color-changer">
                                            {{ helper::currency_formate(@$summery['discount_amount'], $orderdata->vendor_id) }}
                                        </span>
                                    </li>
                                @endif
                                <li class="list-group-item fw-700 text-success">
                                    {{ trans('labels.grand_total') }}
                                    <span class="fw-700 text-success">
                                        {{ helper::currency_formate($summery['grand_total'], $orderdata->vendor_id) }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 customer-left-side">
                    <div class="card rounded mb-4">
                        <div class="card-body">
                            <div class="d-flex gap-2 color-changer align-items-center pb-3 mb-3 border-bottom">
                                <i class="fa-solid fa-circle-info text-dark color-changer"></i>
                                <p class="title">{{ trans('labels.customer') }}</p>
                            </div>
                            <div class="cust-info">
                                @if ($summery['customer_name'] != null)
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fa-regular fa-user color-changer"></i>
                                        <p class="px-2 color-changer">{{ $summery['customer_name'] }}</p>
                                    </div>
                                @endif
                                @if ($summery['customer_email'] != null)
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fa-regular fa-envelope color-changer"></i>
                                        <a href="#" class="px-2 color-changer">{{ $summery['customer_email'] }}</a>
                                    </div>
                                @endif
                                @if ($summery['mobile'] != null)
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fa-solid fa-phone color-changer"></i>
                                        <b class="px-2 fw-500 color-changer">{{ $summery['mobile'] }}</b>
                                    </div>
                                @endif
                                @if ($summery['order_notes'] != null)
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fa-regular fa-clipboard color-changer"></i>
                                        <b class="px-2 fw-500 color-changer">{{ $summery['order_notes'] }}</b>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    {{-- <div class="row rounded py-3 mb-4">
                        <p class="title px-3">{{ trans('labels.customer') }}</p>
                        <div class="card border-0 px-0 py-2">
                            <div class="card-body cust-info pt-2 pb-0">
                              
                            </div>
                        </div>
                    </div> --}}
                    @if ($summery['order_type'] == 1)
                        <div class="card mb-4">
                            <div class="card-body cust-info">
                                <div class="d-flex gap-2 align-items-center pb-3 mb-3 border-bottom">
                                    <i class="fa-solid fa-truck-fast text-dark color-changer"></i>
                                    <p class="title color-changer">{{ trans('labels.delivery_info') }}</p>
                                </div>
                                <div class="d-flex align-items-start mb-2">
                                    <i class="fa-solid fa-location-dot pt-1 color-changer"></i>
                                    <address class="px-2 color-changer">
                                        <b> {{ $summery['building'] }}, {{ $summery['address'] }},
                                            {{ $summery['landmark'] }}, {{ $summery['pincode'] }} </b>
                                    </address>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="row rounded py-3 mb-4">
                            <div class="d-flex gap-2 align-items-center pb-3 border-bottom">
                                <i class="fa-regular fa-credit-card"></i>
                                <p class="title">{{ trans('labels.payment_summary') }}</p>
                            </div>
                            <p class="title px-3"></p>
                            <div class="card border-0 px-0 py-2">
                                <div class="card-body cust-info pt-2 pb-0">
                                    
                                </div>
                            </div>
                        </div> --}}
                    @endif
                    {{-- <div class="row rounded py-3 "> --}}
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex gap-2 align-items-center pb-3 mb-3 border-bottom">
                                <i class="fa-regular fa-money-bill-1 color-changer text-dark"></i>
                                <p class="title color-changer">{{ trans('labels.payment_method') }}</p>
                            </div>
                            <div class="cust-info">
                                <div class="d-flex align-items-center mb-2">
                                    <p class="color-changer">
                                        @if ($orderdata->payment_type == 0)
                                            <i class="fa-solid fa-money-bill-wave"></i>
                                            {{ trans('labels.online') }}
                                        @else
                                            {{ @helper::getpayment($orderdata->payment_type, $orderdata->vendor_id)->payment_name }}
                                            <span class="text-muted">
                                                @if (in_array($orderdata->payment_type, [2, 3, 4, 5, 7, 8, 9, 10, 11, 12, 13, 14, 15]))
                                                    : {{ $orderdata->payment_id }}
                                                @endif
                                            </span>
                                        @endif
                                    </p>
                                </div>

                                @if (in_array($orderdata->payment_type, [2, 3, 4, 5, 7, 8, 9, 10, 11, 12, 13, 14, 15]))
                                    <div class="mb-2">
                                        <p class="color-changer">
                                            {{ trans('labels.payment_id') }} :
                                            <span class="text-muted">{{ $orderdata->payment_id }}</span>
                                        </p>
                                    </div>
                                @endif
                                @if (App\Models\SystemAddons::where('unique_identifier', 'vendor_tip')->first() != null &&
                                        App\Models\SystemAddons::where('unique_identifier', 'vendor_tip')->first()->activated == 1)
                                    @if (@helper::otherappdata($vdata)->tips_settings == 1)
                                        <div>
                                            <p class="color-changer">
                                                {{ trans('labels.tips_pro') }} :
                                                <span
                                                    class="text-muted">{{ helper::currency_formate($summery['tips'], $orderdata->vendor_id) }}</span>
                                            </p>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    @if ($summery['status_type'] == 1)
                        <a href="{{ URL::to($storeinfo->slug . '/cancel-order/' . $summery['order_number']) }}"
                            class="btn-danger btn text-center w-100 py-3 fs-15 fw-500">{{ trans('labels.cancel') }}</a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @include('front.sum_qusction')
@endsection
@section('script')
    <script>
        var variation_title = "{{ trans('labels.variation') }}";
        var extra_title = "{{ trans('labels.extras') }}";
    </script>
    <script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/custom/cart.js') }}" type="text/javascript"></script>
@endsection
