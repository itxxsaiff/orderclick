@extends('front.theme.default')
@section('content')
    <!-- breadcrumb start -->
    <section class="breadcrumb-sec bg-change-mode">
        <div class="container">
            <nav>
                <ol class="breadcrumb d-flex m-0 text-capitalize">
                    <li class="breadcrumb-item"><a href="{{ URL::to(@$storeinfo->slug) }}"
                            class="text-dark color-changer">{{ trans('labels.home') }}</a></li>

                    <li
                        class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-item-right' : 'breadcrumb-item-left' }}">
                        {{ trans('labels.orders') }}
                    </li>
                </ol>
            </nav>
        </div>
    </section>
    <!-- breadcrumb end -->
    <!-- Orders section end -->
    <section class="mt-0 py-sm-5 py-4">
        <div class="container">
            <div class="row gx-sm-3 gx-2">
                @include('front.theme.user_sidebar')
                <div class="col-md-12 col-lg-9">
                    <div class="card rounded">
                        <div class="card-body py-4">
                            <div class="">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h2 class="page-title mb-0">{{ trans('labels.orders') }}</h2>
                                    <div class="dropdown">
                                        <button class="btn btn-primary not-hover-secondary dropdown-toggle px-3 py-2"
                                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            {{ trans('labels.status') }}
                                        </button>
                                        <ul class="dropdown-menu custom">
                                            <li><a class="dropdown-item"
                                                    href="{{ URL::to($storeinfo->slug . '/orders?type=preparing') }}">{{ trans('labels.preparing') }}</a>
                                            </li>
                                            <li><a class="dropdown-item"
                                                    href="{{ URL::to($storeinfo->slug . '/orders?type=cancelled') }}">{{ trans('labels.cancelled') }}</a>
                                            </li>
                                            <li><a class="dropdown-item"
                                                    href="{{ URL::to($storeinfo->slug . '/orders?type=delivered') }}">{{ trans('labels.delivered') }}</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <p class="page-subtitle mt-2 mb-0 line-limit-2">{{ trans('labels.orders_desc') }}</p>
                            </div>
                            <!-- data table start -->
                            @if (count($getorders) > 0)
                                <div class="settings-box-body dashboard-section mt-3">
                                    <div class="table-responsive rounded">
                                        <table class="table table-striped table-hover m-0">
                                            <thead class="table-light">
                                                <tr class="fs-7 fw-600">
                                                    <th scope="col">{{ trans('labels.order_no') }}</th>
                                                    <th scope="col">{{ trans('labels.order_date') }}</th>
                                                    <th scope="col">{{ trans('labels.total') }}</th>
                                                    <th scope="col">{{ trans('labels.payment_type') }}</th>
                                                    <th scope="col">{{ trans('labels.action') }}</th>
                                                    <th scope="col">{{ trans('labels.view') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($getorders as $orderdata)
                                                    <tr class="fs-7">
                                                        <td>
                                                            <a class="color-changer"
                                                                href="{{ URL::to($storeinfo->slug . '/track-order/' . $orderdata->order_number) }}">
                                                                # {{ $orderdata->order_number }}
                                                            </a>
                                                        </td>
                                                        <td>{{ helper::date_format($orderdata->created_at, $orderdata->vendor_id) }}
                                                        </td>
                                                        <td>
                                                            {{ helper::currency_formate($orderdata->grand_total, $orderdata->vendor_id) }}
                                                        </td>
                                                        <td>
                                                            @if ($orderdata->payment_type == 6)
                                                                {{ @helper::getpayment($orderdata->payment_type, $orderdata->vendor_id)->payment_name }}
                                                                : <small>
                                                                    <a href="{{ helper::image_path($orderdata->screenshot) }}"
                                                                        target="_blank"
                                                                        class="text-danger">{{ trans('labels.click_here') }}</a>
                                                                </small>
                                                            @else
                                                                {{ @helper::getpayment($orderdata->payment_type, $orderdata->vendor_id)->payment_name }}
                                                            @endif
                                                            @if (in_array($orderdata->payment_type, [2, 3, 4, 5, 7, 8, 9, 10, 11, 12, 13, 14, 15]))
                                                                : {{ $orderdata->payment_id }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($orderdata->status_type == '1')
                                                                <div
                                                                    class="badge bg-debit custom-badge rounded-0 bg-warninged">
                                                                    <p class="text-break m-0 text-warning fw-500 fs-15">
                                                                        {{ @helper::gettype($orderdata->status, $orderdata->status_type, $orderdata->order_type, $orderdata->vendor_id)->name == null ? '-' : @helper::gettype($orderdata->status, $orderdata->status_type, $orderdata->order_type, $orderdata->vendor_id)->name }}
                                                                    </p>
                                                                </div>
                                                            @elseif($orderdata->status_type == '2')
                                                                <div
                                                                    class="badge bg-debit custom-badge rounded-0 bg-infoed">
                                                                    <p class="text-break m-0 text-info fw-500 fs-15">
                                                                        {{ @helper::gettype($orderdata->status, $orderdata->status_type, $orderdata->order_type, $orderdata->vendor_id)->name == null ? '-' : @helper::gettype($orderdata->status, $orderdata->status_type, $orderdata->order_type, $orderdata->vendor_id)->name }}
                                                                    </p>
                                                                </div>
                                                            @elseif($orderdata->status_type == '3')
                                                                <div
                                                                    class="badge bg-debit custom-badge rounded-0 bg-completed">
                                                                    <p class="text-break m-0 text-success fw-500 fs-15">
                                                                        {{ @helper::gettype($orderdata->status, $orderdata->status_type, $orderdata->order_type, $orderdata->vendor_id)->name == null ? '-' : @helper::gettype($orderdata->status, $orderdata->status_type, $orderdata->order_type, $orderdata->vendor_id)->name }}
                                                                    </p>
                                                                </div>
                                                            @elseif($orderdata->status_type == '4')
                                                                <div
                                                                    class="badge bg-debit custom-badge rounded-0 bg-cancelled">
                                                                    <p class="text-break m-0 text-danger fw-500 fs-15">
                                                                        {{ @helper::gettype($orderdata->status, $orderdata->status_type, $orderdata->order_type, $orderdata->vendor_id)->name == null ? '-' : @helper::gettype($orderdata->status, $orderdata->status_type, $orderdata->order_type, $orderdata->vendor_id)->name }}
                                                                    </p>
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <a class="detail-btn fw-500 fs-7"
                                                                    tooltip="{{ trans('labels.detail') }}"
                                                                    href="{{ URL::to($storeinfo->slug . '/track-order/' . $orderdata->order_number) }}">
                                                                    <i class="fa-solid fa-eye"></i>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @else
                                @include('front.nodata')
                            @endif
                            <!-- data table end -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Orders section end -->

    @include('front.sum_qusction')

@endsection
