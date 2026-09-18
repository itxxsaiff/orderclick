@extends('admin.layout.default')
@php
    if (Auth::user()->type == 4) {
        $vendor_id = Auth::user()->vendor_id;
        $role_id = Auth::user()->role_id;
    } else {
        $vendor_id = Auth::user()->id;
        $role_id = '';
    }
@endphp
@section('content')

    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-12">
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.welcome_dashboard') }}</h5>
        </div>
    </div>

    {{-- AI store builder. It used to open by itself right after login, which merchants read as a
         broken sign-in. It now lives here: loud while the store is still empty, quieter once the
         catalogue exists, and always reachable from the sidebar. --}}
    @if (!empty($ocAiEnabled))
        @php $ocAr = app()->getLocale() === 'ar'; @endphp
        <style>
            .oc-ai { position: relative; overflow: hidden; border-radius: 18px; color: #fff; margin-bottom: 1.5rem;
                background: linear-gradient(120deg, #0f7a4a 0%, #16a34a 45%, #0ea5a4 100%); }
            .oc-ai__in { position: relative; z-index: 2; padding: 26px 28px; display: flex; flex-wrap: wrap;
                align-items: center; justify-content: space-between; gap: 18px; }
            .oc-ai__eyebrow { display: inline-flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 700;
                letter-spacing: .12em; text-transform: uppercase; background: rgba(255,255,255,.18);
                padding: 5px 12px; border-radius: 999px; }
            .oc-ai h3 { font-size: clamp(20px, 2.4vw, 28px); font-weight: 800; margin: 12px 0 6px; color: #fff; }
            .oc-ai p { margin: 0; opacity: .92; max-width: 46em; font-size: 14.5px; }
            .oc-ai__chips { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 14px; }
            .oc-ai__chips span { background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.25);
                padding: 5px 12px; border-radius: 999px; font-size: 12.5px; font-weight: 600; }
            .oc-ai__btn { background: #fff; color: #0f7a4a; font-weight: 700; border-radius: 999px;
                padding: 13px 26px; white-space: nowrap; border: 0; text-decoration: none; display: inline-flex;
                align-items: center; gap: 9px; box-shadow: 0 14px 30px -14px rgba(0,0,0,.55); transition: transform .15s ease; }
            .oc-ai__btn:hover { transform: translateY(-2px); color: #0f7a4a; }
            .oc-ai__spark { position: absolute; inset: 0; z-index: 1; opacity: .5;
                background: radial-gradient(520px 170px at 88% 18%, rgba(255,255,255,.35), transparent 62%),
                            radial-gradient(360px 150px at 12% 92%, rgba(255,255,255,.22), transparent 60%); }
            .oc-ai__shine { position: absolute; top: 0; bottom: 0; width: 42%; z-index: 1; transform: skewX(-18deg);
                background: linear-gradient(90deg, transparent, rgba(255,255,255,.16), transparent);
                animation: ocAiShine 4.5s ease-in-out infinite; }
            @keyframes ocAiShine { 0% { left: -45%; } 55%, 100% { left: 115%; } }
            @media (prefers-reduced-motion: reduce) { .oc-ai__shine { animation: none; opacity: 0; } }
            .oc-ai--compact .oc-ai__in { padding: 18px 22px; }
            .oc-ai--compact h3 { font-size: 18px; margin: 8px 0 4px; }
            .oc-ai--compact .oc-ai__chips { display: none; }
        </style>
        <div class="oc-ai {{ empty($ocStoreEmpty) ? 'oc-ai--compact' : '' }}">
            <div class="oc-ai__spark"></div>
            <div class="oc-ai__shine"></div>
            <div class="oc-ai__in">
                <div>
                    <span class="oc-ai__eyebrow"><i class="fa-solid fa-wand-magic-sparkles"></i>
                        {{ trans('labels.ai_assistant') }}</span>
                    @if (!empty($ocStoreEmpty))
                        <h3>{{ trans('labels.let_ai_build_your_store_in_a') }}</h3>
                        <p>{{ trans('labels.tell_it_what_you_offer_or_upload') }}</p>
                        <div class="oc-ai__chips">
                            <span><i class="fa-solid fa-layer-group mx-1"></i>{{ trans('labels.categories') }}</span>
                            <span><i class="fa-solid fa-box mx-1"></i>{{ trans('labels.products') }}</span>
                            <span><i class="fa-solid fa-pen-nib mx-1"></i>{{ trans('labels.descriptions') }}</span>
                            <span><i class="fa-solid fa-file-arrow-up mx-1"></i>{{ trans('labels.menu_photo_or_pdf') }}</span>
                        </div>
                    @else
                        <h3>{{ trans('labels.add_more_products_with_ai') }}</h3>
                        <p>{{ trans('labels.upload_a_new_menu_or_type_what') }}</p>
                    @endif
                </div>
                <a href="{{ URL::to('admin/store-setup') }}" class="oc-ai__btn">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    {{ !empty($ocStoreEmpty)
                        ? ($ocAr ? 'ابدأ الإعداد بالذكاء الاصطناعي' : 'Build my store with AI')
                        : ($ocAr ? 'فتح مساعد الذكاء الاصطناعي' : 'Open AI Assistant') }}
                </a>
            </div>
        </div>
    @endif
    <div class="row mb-0 mb-md-4">
        <div class="col-12 col-md-12 col-lg-12 col-xl-6">
            <div class="card h-100 border-0 shadow desh_left">
                <div class="card-body p-4">
                    <div class="row justify-content-between align-items-center">
                        <div class="col-12 col-md-6">
                            <h4 class="card-title fw-600 fs-2">{{ trans('labels.quick_access_card_title') }}</h4>
                            <p class="card-text pb-3">
                                {{ trans('labels.quick_access_card_description') }}
                            </p>
                            <div class="dropwdown d-inline-block">
                                <a class="btn bg-white border-0 dropwdown-desh-card" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <span class="ms-1">{{ trans('labels.quick_access') }}</span>
                                    <i class="fa-regular fa-angle-down"></i>
                                </a>
                                <div class="dropdown-menu shadow border-0">

                                    {{-- for Admin  --}}

                                    @if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1))
                                        <a href="{{ URL::to('admin/users') }}"
                                            class="dropdown-item d-flex align-items-center px-3 py-2">
                                            {{ trans('labels.restaurants') }}
                                        </a>

                                        @if (App\Models\SystemAddons::where('unique_identifier', 'subscription')->first() != null &&
                                                App\Models\SystemAddons::where('unique_identifier', 'subscription')->first()->activated == 1)
                                            <a href="{{ URL::to('admin/plan') }}"
                                                class="dropdown-item d-flex align-items-center px-3 py-2">
                                                {{ trans('labels.pricing_plans') }}
                                            </a>
                                        @endif

                                        <a href="{{ URL::to('admin/transaction') }}"
                                            class="dropdown-item d-flex align-items-center px-3 py-2">
                                            {{ trans('labels.transaction') }}
                                        </a>
                                    @endif

                                    {{-- for  vendor  --}}

                                    @if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1))
                                        <a href="{{ URL::to('admin/categories') }}"
                                            class="dropdown-item d-flex align-items-center px-3 py-2 {{ helper::check_menu($role_id, 'role_categories') == 1 ? 'd-block' : 'd-none' }}">
                                            {{ trans('labels.category') }}
                                        </a>
                                        <a href="{{ URL::to('admin/products') }}"
                                            class="dropdown-item d-flex align-items-center px-3 py-2 {{ helper::check_menu($role_id, 'role_products') == 1 ? 'd-block' : 'd-none' }}">
                                            {{ trans('labels.products') }}
                                        </a>
                                        <a href="{{ URL::to('admin/orders') }}"
                                            class="dropdown-item d-flex align-items-center px-3 py-2 {{ helper::check_menu($role_id, 'role_orders') == 1 ? 'd-block' : 'd-none' }}">
                                            {{ trans('labels.orders') }}
                                        </a>
                                    @endif

                                    {{-- common for admin & vendor  --}}

                                    <a href="{{ URL::to('admin/settings') }}"
                                        class="dropdown-item d-flex align-items-center px-3 py-2 {{ helper::check_menu($role_id, 'role_settings') == 1 ? 'd-block' : 'd-none' }}">
                                        {{ trans('labels.setting') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 d-flex justify-content-end">
                            <img src="{{ url(env('ASSETSPATHURL') . 'admin-assets/images/about/seo-dashboard.png') }}"
                                alt="" class="desh-chart-img d-none d-md-block">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-12 col-lg-12 col-xl-6 mt-4 mt-xl-0">
            <div class="row h-100">
                @if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1))
                    <div class="col-xxl-6 col-lg-6 col-md-6 col-sm-6 mb-4">
                        <div class="card border-0 box-shadow h-100 deshcard1 rgb-warning-light">
                            <div class="card-body">
                                <div class="dashboard-card">
                                    <span class="{{ session()->get('direction') == '2' ? 'text-start' : 'text-start' }}">
                                        <p class="fw-semibold fs-5 mb-1 color-changer text-dark">{{ trans('labels.users') }}</p>
                                        <h4 class="text-dark color-changer fw-bold fs-2">{{ $totalvendors }}</h4>
                                    </span>
                                    <span class="card-icon bg-warning">
                                        <i class="fa-solid fa-user-plus fs-5"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-6 col-lg-6 col-md-6 col-sm-6 mb-4">
                        <div class="card border-0 box-shadow h-100 deshcard2 rgb-success-light">
                            <div class="card-body">
                                <div class="dashboard-card">
                                    <span class="{{ session()->get('direction') == 2 ? 'text-end' : 'text-start' }}">
                                        <p class="fw-semibold fs-5 mb-1 color-changer text-dark">{{ trans('labels.pricing_plans') }}</p>
                                        <h4 class="text-dark color-changer fw-bold fs-2">{{ $totalplans }}</h4>
                                    </span>
                                    <span class="card-icon bg-success">
                                        <i class="fa-regular fa-medal fs-5"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @if (Auth::user()->type == 2)
                    <div class="col-xxl-6 col-lg-6 col-md-6 col-sm-6 mb-4">
                        <div class="card border-0 box-shadow h-100 deshcard02">
                            <div class="card-body">
                                <div class="dashboard-card">
                                    <span class="{{ session()->get('direction') == '2' ? 'text-end' : 'text-start' }}">
                                        <p class="fw-semibold fs-5 mb-1 color-changer text-dark">{{ trans('labels.products') }}</p>
                                        <h4 class="text-dark color-changer fw-bold fs-2">{{ $totalvendors }}</h4>
                                    </span>
                                    <span class="card-icon card-color2">
                                        <i class="fa-solid fa-list-timeline fs-5"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-6 col-lg-6 col-md-6 col-sm-6 mb-4">
                        <div class="card border-0 box-shadow h-100 deshcard03">
                            <div class="card-body">
                                <div class="dashboard-card">
                                    <span class="{{ session()->get('direction') == '2' ? 'text-end' : 'text-start' }}">
                                        <p class="fw-semibold fs-5 mb-1 color-changer text-dark">{{ trans('labels.current_plan') }}</p>
                                        @if (!empty($currentplanname))
                                            <h4 class="text-dark color-changer fw-bold fs-2"> {{ @$currentplanname->name }} </h4>
                                        @else
                                            <i class="fa-regular fa-exclamation-triangle h4 text-muted"></i>
                                        @endif
                                    </span>
                                    <span class="card-icon card-color1">
                                        <i class="fa-solid fa-cart-flatbed-suitcase fs-5"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-xxl-6 col-lg-6 col-md-6 col-sm-6 mb-4 mb-md-0">
                    <div class="card h-100 border-0 box-shadow deshcard3 rgb-secondary-light">
                        <div class="card-body">
                            <div class="dashboard-card">
                                <span class="{{ session()->get('direction') == 2 ? 'text-end' : 'text-start' }}">
                                    <p class="fw-semibold fs-5 mb-1 color-changer text-dark">
                                        {{ Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1) ? trans('labels.transaction') : trans('labels.orders') }}
                                    </p>
                                    <h4 class="text-dark color-changer fw-bold fs-2">{{ $totalorders }}</h4>
                                </span>
                                <span class="card-icon bg-secondary">
                                    @if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1))
                                        <i class="fa-solid fa-chart-line fs-5"></i>
                                    @else
                                        <i class="fa-regular fa-cart-shopping fs-5"></i>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-6 col-lg-6 col-md-6 col-sm-6 mb-4 mb-md-0">
                    <div class="card h-100 border-0 box-shadow deshcard4 rgb-danger-light">
                        <div class="card-body">
                            <div class="dashboard-card">
                                <span class="{{ session()->get('direction') == 2 ? 'text-end' : 'text-start' }}">
                                    <p class="fw-semibold fs-5 mb-1 color-changer text-dark">{{ trans('labels.revenue') }}</p>
                                    <h4 class="text-dark color-changer fw-bold fs-2">
                                        {{ helper::currency_formate($totalrevenue, $vendor_id) }}</h4>
                                </span>
                                <span class="card-icon bg-danger">
                                    <i class="fa-solid fa-chart-pie fs-5"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-xl-6 mb-4">
            <div class="card border-0 box-shadow h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title color-changer">{{ trans('labels.revenue') }}</h5>
                        <select class="form-select form-select-sm w-auto selectdrop" id="revenueyear"
                            data-url="{{ URL::to('/admin/dashboard') }}">
                            @if (count($revenue_years) > 0 && !in_array(date('Y'), array_column($revenue_years->toArray(), 'year')))
                                <option value="{{ date('Y') }}" selected>{{ date('Y') }}</option>
                            @endif
                            @forelse ($revenue_years as $revenue)
                                <option value="{{ $revenue->year }}" {{ date('Y') == $revenue->year ? 'selected' : '' }}>
                                    {{ $revenue->year }}
                                </option>
                            @empty
                                <option value="" selected disabled>{{ trans('labels.select') }}</option>
                            @endforelse
                        </select>
                    </div>
                    <div class="row">
                        <canvas id="revenuechart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-xl-6 mb-4">
            <div class="card border-0 box-shadow h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title color-changer closing-button-1-right">
                            {{ Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1) ? trans('labels.users') : trans('labels.orders') }}
                        </h5>
                        <select class="form-select form-select-sm w-auto selectdrop" id="doughnutyear"
                            data-url="{{ request()->url() }}">
                            @if (count($doughnut_years) > 0 && !in_array(date('Y'), array_column($doughnut_years->toArray(), 'year')))
                                <option value="{{ date('Y') }}" selected>{{ date('Y') }}</option>
                            @endif
                            @forelse ($doughnut_years as $useryear)
                                <option value="{{ $useryear->year }}"
                                    {{ date('Y') == $useryear->year ? 'selected' : '' }}>{{ $useryear->year }}
                                </option>
                            @empty
                                <option value="" selected disabled>{{ trans('labels.select') }}</option>
                            @endforelse
                        </select>
                    </div>
                    <div class="row">
                        <canvas id="doughnut"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1))
        @php
            $ran = [
                'gradient-1',
                'gradient-2',
                'gradient-3',
                'gradient-4',
                'gradient-5',
                'gradient-6',
                'gradient-7',
                'gradient-8',
                'gradient-9',
            ];
        @endphp
        <div class="row">
            <div class="col-xl-6 mb-4">
                <div class="card border-0 box-shadow h-100">
                    <div class="card-body">
                        <h5 class="card-title color-changer pb-3 m-0">{{ trans('labels.top_product') }}</h5>
                        <div class="table-responsive" id="table-items">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="fs-15 fw-500">{{ trans('labels.image') }}</th>
                                        <th class="fs-15 fw-500">{{ trans('labels.item_name') }}</th>
                                        <th class="fs-15 fw-500">{{ trans('labels.category') }}</th>
                                        <th class="fs-15 fw-500">{{ trans('labels.orders') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($topitems) > 0)
                                        @foreach (@$topitems as $row)
                                            <tr class="fs-7 fw-500 text-dark align-middle">
                                                <td>
                                                    <img src="{{ Helper::image_path(optional($row['item_image'])->image) }}"
                                                        class="rounded hw-50 object" alt="">
                                                </td>
                                                <td>
                                                    <a
                                                        href="{{ URL::to('admin/products/edit-' . $row->slug) }}" class="td_a">{{ $row->item_name }}</a>
                                                </td>
                                                <td>{{ @$row['category_info']->name }}</td>
                                                <td>
                                                    @php
                                                        $per =
                                                            $getorderdetailscount > 0
                                                                ? ($row->item_order_counter * 100) /
                                                                    $getorderdetailscount
                                                                : 0;
                                                    @endphp
                                                    {{ number_format($per, 2) }}%
                                                    <div class="progress h-10-px">
                                                        <div class="progress-bar gradient-6 {{ $ran[array_rand($ran, 1)] }}"
                                                            style="width: {{ $per }}%;" role="progressbar">
                                                            <span class="sr-only">{{ $per }}%
                                                                {{ trans('labels.orders') }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4">
                                                <h6 class="text-center fw-600">
                                                    {{ trans('labels.data_not_found') }}
                                                </h6>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 mb-4">
                <div class="card border-0 box-shadow h-100">
                    <div class="card-body">
                        <h5 class="card-title pb-3 color-changer m-0">{{ trans('labels.top_customers') }}</h5>
                        <div class="table-responsive" id="table-users">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="fs-15 fw-500">{{ trans('labels.image') }}</th>
                                        <th class="fs-15 fw-500">{{ trans('labels.name') }}</th>
                                        <th class="fs-15 fw-500">{{ trans('labels.email') }}</th>
                                        <th class="fs-15 fw-500">{{ trans('labels.orders') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($topusers) > 0)
                                        @foreach (@$topusers as $user)
                                            <tr class="fs-7 fw-500 text-dark align-middle">
                                                <td>
                                                    <img src="{{ Helper::image_path($user->image) }}"
                                                        class="rounded hw-50 object" alt="">
                                                </td>
                                                <td>
                                                    <div class="fs-7 fw-500">
                                                        <p>{{ $user->name }}</p>
                                                        <p>{{ $user->mobile }}</p>
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ $user->email }}
                                                </td>
                                                <td>
                                                    @php
                                                        $per =
                                                            ($user->user_order_counter * 100) / @$getorderdetailscount;
                                                    @endphp
                                                    {{ number_format($per, 2) }}%
                                                    <div class="progress h-10-px">
                                                        <div class="progress-bar {{ $ran[array_rand($ran, 1)] }}"
                                                            style="width: {{ $per }}%;" role="progressbar">
                                                            <span class="sr-only">{{ $per }}%
                                                                {{ trans('labels.orders') }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4">
                                                <h6 class="text-center fw-600">
                                                    {{ trans('labels.data_not_found') }}
                                                </h6>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-12">
            <h5 class="pages-title color-changer fs-2 py-2">
                {{ Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1) ? trans('labels.today_transaction') : trans('labels.processing_orders') }}
            </h5>
        </div>
        <div class="col-12 mb-7">
            <div class="card border-0 box-shadow">
                <div class="card-body">
                    <div class="table-responsive">
                        @if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1))
                            @include('admin.dashboard.admintransaction')
                        @else
                            @include('admin.orders.orderstable')
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <!--- Admin -------- users-chart-script --->
    <!--- VendorAdmin -------- orders-count-chart-script --->
    <script type="text/javascript">
        var doughnut = null;
        var doughnutlabels = {{ Js::from($doughnutlabels) }};
        var doughnutdata = {{ Js::from($doughnutdata) }};
    </script>
    <!--- Admin ------ revenue-by-plans-chart-script --->
    <!--- vendorAdmin ------ revenue-by-orders-script --->
    <script type="text/javascript">
        var revenuechart = null;
        var labels = {{ Js::from($revenuelabels) }};
        var revenuedata = {{ Js::from($revenuedata) }};
    </script>
    <script src="{{ url(env('ASSETSPATHURL') . 'admin-assets/js/dashboard.js') }}"></script>
@endsection