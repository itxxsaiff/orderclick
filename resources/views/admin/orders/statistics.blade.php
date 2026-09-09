<div class="col-12">
    <div class="row g-3">

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
            <div
                class="card box-shadow h-100 ordercard1 rgb-secondary-light {{ request()->get('status') == '' ? 'border border-primary' : 'border-0' }}">
                @if (request()->is('admin/report'))
                    <a
                        href="{{ URL::to(request()->url() . '?status=&customer_id=' . request()->get('customer_id') . '&startdate=' . request()->get('startdate') . '&enddate=' . request()->get('enddate')) }}">
                    @elseif(request()->is('admin/orders'))
                        <a href="{{ URL::to('admin/orders?status=') }}">
                        @elseif(request()->is('admin/customers/orders*'))
                            <a href="{{ URL::to('admin/customers/orders-' . @$userinfo->id . '?status=') }}">
                @endif
                <div class="card-body">
                    <div class="dashboard-card">
                        <span class="{{ session()->get('direction') == 2 ? 'text-end' : 'text-start' }}">
                            <p class="fw-semibold fs-5 mb-1 text-dark color-changer">{{ trans('labels.total_orders') }}</p>
                            <h4 class="text-dark fw-bold fs-2 color-changer">{{ $totalorders }}</h4>
                        </span>
                        <span class="card-icon bg-secondary">
                            <i class="fa-regular fa-rectangle-list fs-5"></i>
                        </span>
                    </div>
                </div>
                </a>
            </div>
        </div>
        @if (request()->is('admin/orders') || request()->is('admin/customers/orders*'))
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div
                    class="card box-shadow h-100 ordercard2 rgb-warning-light {{ request()->get('status') == 'processing' ? 'border border-primary' : 'border-0' }}">
                    @if (request()->is('admin/report'))
                        <a
                            href="{{ URL::to(request()->url() . '?status=processing&customer_id=' . request()->get('customer_id') . '&startdate=' . request()->get('startdate') . '&enddate=' . request()->get('enddate')) }}">
                        @elseif(request()->is('admin/orders'))
                            <a href="{{ URL::to('admin/orders?status=processing') }}">
                            @elseif(request()->is('admin/customers/orders*'))
                                <a
                                    href="{{ URL::to('admin/customers/orders-' . @$userinfo->id . '?status=processing') }}">
                    @endif
                    <div class="card-body">
                        <div class="dashboard-card">
                            <span class="{{ session()->get('direction') == 2 ? 'text-end' : 'text-start' }}">
                                <p class="fw-semibold fs-5 mb-1 color-changer text-dark">{{ trans('labels.processing') }}</p>
                                <h4 class="text-dark fw-bold fs-2 color-changer">{{ $totalprocessing }}</h4>
                            </span>
                            <span class="card-icon bg-warning">
                                <i class="fa-solid fa-hourglass-half fs-5"></i>
                            </span>
                        </div>
                    </div>
                    </a>
                </div>
            </div>
        @endif
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
            <div
                class="card box-shadow h-100 ordercard3 rgb-success-light {{ request()->get('status') == 'delivered' ? 'border border-primary' : 'border-0' }}">
                @if (request()->is('admin/report'))
                    <a
                        href="{{ URL::to(request()->url() . '?status=delivered&customer_id=' . request()->get('customer_id') . '&startdate=' . request()->get('startdate') . '&enddate=' . request()->get('enddate')) }}">
                    @elseif(request()->is('admin/orders'))
                        <a href="{{ URL::to('admin/orders?status=delivered') }}">
                        @elseif(request()->is('admin/customers/orders*'))
                            <a href="{{ URL::to('admin/customers/orders-' . @$userinfo->id . '?status=delivered') }}">
                @endif
                <div class="card-body">
                    <div class="dashboard-card">
                        <span class="{{ session()->get('direction') == 2 ? 'text-end' : 'text-start' }}">
                            <p class="fw-semibold color-changer fs-5 mb-1 text-dark">{{ trans('labels.delivered') }}</p>
                            <h4 class="text-dark color-changer fw-bold fs-2">{{ $totalcompleted }}</h4>
                        </span>
                        <span class="card-icon bg-success">
                            <i class="fa-solid fa-check-to-slot fs-5"></i>
                        </span>
                    </div>
                </div>
                </a>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
            <div
                class="card box-shadow h-100 ordercard4 rgb-danger-light {{ request()->get('status') == 'cancelled' ? 'border border-primary' : 'border-0' }}">
                @if (request()->is('admin/report'))
                    <a
                        href="{{ URL::to(request()->url() . '?status=cancelled&customer_id=' . request()->get('customer_id') . '&startdate=' . request()->get('startdate') . '&enddate=' . request()->get('enddate')) }}">
                    @elseif(request()->is('admin/orders'))
                        <a href="{{ URL::to('admin/orders?status=cancelled') }}">
                        @elseif(request()->is('admin/customers/orders*'))
                            <a href="{{ URL::to('admin/customers/orders-' . @$userinfo->id . '?status=cancelled') }}">
                @endif
                <div class="card-body">
                    <div class="dashboard-card">
                        <span class="{{ session()->get('direction') == 2 ? 'text-end' : 'text-start' }}">
                            <p class="fw-semibold fs-5 color-changer mb-1 text-dark">{{ trans('labels.cancelled') }}</p>
                            <h4 class="text-dark color-changer fw-bold fs-2">{{ $totalcancelled }}</h4>
                        </span>
                        <span class="card-icon bg-danger">
                            <i class="fa-solid fa-circle-xmark fs-5"></i>
                        </span>
                    </div>
                </div>
                </a>
            </div>
        </div>

        @if (request()->is('admin/report*'))
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="card box-shadow h-100 deshcard02">
                    <div class="card-body">
                        <div class="dashboard-card">
                            <span class="{{ session()->get('direction') == 2 ? 'text-end' : 'text-start' }}">
                                <p class="fw-semibold fs-5 mb-1 color-changer text-dark">{{ trans('labels.revenue') }}</p>
                                <h4 class="text-dark color-changer fw-bold fs-2">
                                    {{ helper::currency_formate($totalrevenue, $vendor_id) }}</h4>
                            </span>
                            <span class="card-icon card-color2">
                                <i class="fa-regular fa-money-bill-1-wave"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
