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
                        {{ trans('labels.top_deals') }}
                    </li>
                </ol>
            </nav>
        </div>
    </section>
    <!-- breadcrumb end -->
    <section>
        <div class="background-black bg-changer py-5">
            <div class="container">
                <div id="eapps-countdown-timer-1"
                    class="rounded eapps-countdown-timer bg-transparent eapps-countdown-timer-align-center eapps-countdown-timer-position-bottom-bar-floating eapps-countdown-timer-animation-none eapps-countdown-timer-theme-default eapps-countdown-timer-finish-button-show  eapps-countdown-timer-style-combined eapps-countdown-timer-style-blocks eapps-countdown-timer-position-bar eapps-countdown-timer-area-clickable eapps-countdown-timer-has-background">
                    <div class="eapps-countdown-timer-container d-flex">
                        <div class="eapps-countdown-timer-inner row g-md-3 g-5 flex-column flex-sm-row">
                            <div
                                class="eapps-countdown-timer-header d-flex col-sm-6 col-12 align-items-center justify-content-center justify-content-md-start">
                                <div
                                    class="eapps-countdown-timer-header-title {{ session()->get('direction') == 2 ? 'text-center text-md-end' : 'text-center text-md-start' }}">
                                    <div class="eapps-countdown-timer-header-title-text">
                                        <div class="page-title mb-1">
                                            {{ trans('labels.home_page_top_deals_title') }}
                                        </div>
                                        <div class="page-subtitle text-white line-limit-2 m-0">
                                            Special Offer 30% OFF 🔥 Lorem, ipsum dolor sit amet
                                            consectetur adipisicing elit. Enim tempora maiores.
                                            {{ trans('labels.home_page_top_deals_subtitle') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="eapps-countdown-timer-item-container col-sm-6 col-12">
                                <div id="countdown"
                                    class="{{ session()->get('direction') == 2 ? 'justify-content-center justify-content-start' : 'justify-content-md-end justify-content-center' }} gap-2">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="d-flex bg-secondary-rgb bg-change-mode p-3 rounded-3 justify-content-between align-items-center mt-4">
                <span class="fs-15 fw-600 color-changer">
                    {{ trans('labels.showing') }}
                    {{ $topdealsproducts->firstItem() ? $topdealsproducts->firstItem() : 0 }}–{{ $topdealsproducts->lastItem() ? $topdealsproducts->lastItem() : 0 }}
                    {{ trans('labels.of') }}
                    {{ $topdealsproducts->total() }} {{ trans('labels.result') }}
                </span>
                <ul class="d-flex flex-nowrap justify-content-end gap-2 nav nav-pills nav-pills-dark" id="tour-pills-tab"
                    role="tablist">
                    <!-- Tab item -->
                    <li class="nav-item">
                        <div class="dropdown lag-btn">
                            <a class="nav-link view-list-grid cursor-pointer text-dark border border-dark" href="#"
                                role="button" data-bs-toggle="dropdown" aria-expanded="false" tooltip="Filter">
                                <i class="fa-solid fa-filter color-changer"></i>
                            </a>
                            <ul class="dropdown-menu bg-body-secondary p-0 mt-2 shadow border-0 overflow-hidden">
                                <li>
                                    <a class="dropdown-item p-2 d-flex fs-8 @if (isset($_GET['filter']) && $_GET['filter'] == 'price-high-to-low') fw-700 @else @endif"
                                        @if (isset($_GET['filter']) && $_GET['filter'] == 'price-high-to-low') href="{{ URL::to($storeinfo->slug . '/topdeals?type=' . @$_GET['type']) }}" @else href="{{ URL::to($storeinfo->slug . '/topdeals?type=' . @$_GET['type'] . '&filter=price-high-to-low') }}" @endif>
                                        {{ trans('labels.p_high_to_low') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item p-2 d-flex fs-8 @if (isset($_GET['filter']) && $_GET['filter'] == 'price-low-to-high') fw-700 @else @endif"
                                        @if (isset($_GET['filter']) && $_GET['filter'] == 'price-low-to-high') href="{{ URL::to($storeinfo->slug . '/topdeals?type=' . @$_GET['type']) }}" @else href="{{ URL::to($storeinfo->slug . '/topdeals?type=' . @$_GET['type'] . '&filter=price-low-to-high') }}" @endif>
                                        {{ trans('labels.p_low_to_high') }}
                                    </a>
                                </li>
                                @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                        App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                    <li>
                                        <a class="dropdown-item p-2 d-flex fs-8 @if (isset($_GET['filter']) && $_GET['filter'] == 'ratting-high-to-low') fw-700 @else @endif"
                                            @if (isset($_GET['filter']) && $_GET['filter'] == 'ratting-high-to-low') href="{{ URL::to($storeinfo->slug . '/topdeals?type=' . @$_GET['type']) }}" @else href="{{ URL::to($storeinfo->slug . '/topdeals?type=' . @$_GET['type'] . '&filter=ratting-high-to-low') }}" @endif>
                                            {{ trans('labels.r_high_to_low') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item p-2 d-flex fs-8 @if (isset($_GET['filter']) && $_GET['filter'] == 'ratting-low-to-high') fw-700 @else @endif"
                                            @if (isset($_GET['filter']) && $_GET['filter'] == 'ratting-low-to-high') href="{{ URL::to($storeinfo->slug . '/topdeals?type=' . @$_GET['type']) }}" @else href="{{ URL::to($storeinfo->slug . '/topdeals?type=' . @$_GET['type'] . '&filter=ratting-low-to-high') }}" @endif>
                                            {{ trans('labels.r_low_to_high') }}
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link view-list-grid cursor-pointer color-changer text-dark border border-dark service-active"
                            id="column" tooltip="Grid View">
                            <i class="fa-solid fa-grid-2"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link view-list-grid cursor-pointer color-changer text-dark border border-dark" id="grid"
                            tooltip="List View">
                            <i class="fa-solid fa-list-ul"></i>
                        </a>
                    </li>
                    <!-- Tab item -->
                </ul>
            </div>
        </div>
        <div class="container">
            <div class="listing-view">
                @if (helper::appdata($storeinfo->id)->template == 1)
                    <div class="row g-2 g-md-3 pt-4">
                        @foreach ($topdealsproducts as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price >
                                                @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price =
                                                    $item->item_price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item->item_price;
                                            }
                                        }
                                    } else {
                                        if ($item['variation']->count() > 0) {
                                            $price =
                                                $item['variation'][0]->price -
                                                $item['variation'][0]->price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        } else {
                                            $price =
                                                $item->item_price -
                                                $item->item_price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        }
                                    }
                                    if ($item['variation']->count() > 0) {
                                        $original_price = $item['variation'][0]->price;
                                    } else {
                                        $original_price = $item->item_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                } else {
                                    if ($item['variation']->count() > 0) {
                                        $price = $item['variation'][0]->price;
                                        $original_price = $item['variation'][0]->original_price;
                                    } else {
                                        $price = $item->item_price;
                                        $original_price = $item->item_original_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                }
                                $item_name = str_replace("'", "\'", $item->item_name);
                            @endphp
                            <div class="col-6 col-lg-3">
                                <div class="card h-100 position-relative rounded">
                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                        <div class="overflow-hidden theme1grid_image p-2">
                                            <img src="{{ helper::image_path(@$item['item_image']->image) }}"
                                                alt="" class="rounded">
                                            @if ($off > 0)
                                                <span
                                                    class="offer-text rounded fw-500 text-bg-secondary fs-8">{{ $off }}%
                                                    {{ trans('labels.off') }}</span>
                                            @endif
                                        </div>
                                    </a>
                                    <div class="card-body p-2 p-md-3 pb-sm-0 ">
                                        @if (Auth::user() && Auth::user()->type == 3)
                                            <div class="favorite-icon set-fav1-{{ $item->id }}">
                                                @if ($item->is_favorite == 1)
                                                    <a href="javascript:void(0)"
                                                        onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                            class="fa-solid fa-heart"></i></a>
                                                @else
                                                    <a href="javascript:void(0)"
                                                        onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                        <i class="fa-regular fa-heart"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        @endif
                                        @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                            @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                    App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                    <a class="fs-8 d-flex gap-1 align-items-center mb-1"
                                                        onclick="showreviews('{{ $item->id }}')" role="button"
                                                        aria-controls="offcanvasExample">
                                                        <i class="fa-solid fa-star text-warning"></i>
                                                        <p class="cursor-pointer color-changer fw-600 fs-8">
                                                            {{ number_format($item->avg_ratting, 1) }}</p>
                                                    </a>
                                                @endif
                                            @endif
                                        @endif
                                        <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                            class="title color-changer pb-1">
                                            {{ $item->item_name }}
                                        </a>
                                    </div>
                                    <div class="card-footer bg-transparent border-0 p-2 pt-md-0 p-md-3 pt-0">
                                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                            <div class="mb-2 mb-md-0">
                                                <div class="d-flex gap-1 flex-wrap align-items-center">
                                                    <p class="price color-changer">
                                                        {{ helper::currency_formate($price, @$storeinfo->id) }}
                                                    </p>
                                                    @if ($item->item_original_price != null)
                                                        @if ($original_price > $price)
                                                            <del class="text-muted">{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="d-flex col-sm-auto col-12 justify-content-end align-items-center">
                                                <button
                                                    class="btn-primary w-100 px-3 py-2 d-flex gap-2 fw-500 align-items-center justify-content-center fs-7 btn m-0"
                                                    type="button"
                                                    onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                    <div class="addcartbtn-{{ $item->id }}">
                                                        <i class="fa-regular fa-plus"></i>
                                                        {{ trans('labels.add_to_cart') }}
                                                    </div>
                                                    <div class="load showload-{{ $item->id }}" style="display:none">
                                                    </div>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 2)
                    <div class="row g-3 pt-4">
                        @foreach ($topdealsproducts as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price >
                                                @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price =
                                                    $item->item_price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item->item_price;
                                            }
                                        }
                                    } else {
                                        if ($item['variation']->count() > 0) {
                                            $price =
                                                $item['variation'][0]->price -
                                                $item['variation'][0]->price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        } else {
                                            $price =
                                                $item->item_price -
                                                $item->item_price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        }
                                    }
                                    if ($item['variation']->count() > 0) {
                                        $original_price = $item['variation'][0]->price;
                                    } else {
                                        $original_price = $item->item_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                } else {
                                    if ($item['variation']->count() > 0) {
                                        $price = $item['variation'][0]->price;
                                        $original_price = $item['variation'][0]->original_price;
                                    } else {
                                        $price = $item->item_price;
                                        $original_price = $item->item_original_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                }
                                $item_name = str_replace("'", "\'", $item->item_name);
                            @endphp
                            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3">
                                <div class="card border-0 rounded theme-2-products-card h-100">
                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                        <div class="theme_2_grid_img overflow-hidden">
                                            <img src="{{ helper::image_path(@$item['item_image']->image) }}"
                                                class="img-fluid" alt="...">
                                            @if ($off > 0)
                                                <span
                                                    class="offer-text rounded fw-500 text-bg-primary fs-8">{{ $off }}%
                                                    {{ trans('labels.off') }}</span>
                                            @endif
                                        </div>
                                    </a>
                                    <div class="card-body px-2 px-md-3 pb-0">
                                        @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                            @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                    App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                    <a class="fs-8 d-flex gap-1 align-items-center mb-1"
                                                        onclick="showreviews('{{ $item->id }}')" role="button"
                                                        aria-controls="offcanvasExample">
                                                        <i class="fa-solid fa-star text-warning"></i>
                                                        <p class="cursor-pointer color-changer fw-600 fs-8">
                                                            {{ number_format($item->avg_ratting, 1) }}</p>
                                                    </a>
                                                @endif
                                            @endif
                                        @endif
                                        <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                            class="title color-changer">
                                            {{ $item->item_name }}</a>
                                    </div>
                                    <div class="card-footer px-2 px-md-3 pt-0">
                                        <div class="d-flex justify-content-between align-items-center gap-2">
                                            <div class="mb-2 mb-md-0">
                                                <div class="products-price d-flex gap-1 align-items-center">
                                                    <span
                                                        class="price color-changer">{{ helper::currency_formate($price, @$storeinfo->id) }}</span>
                                                    @if ($item->item_original_price != null)
                                                        @if ($original_price > $price)
                                                            <del class="text-muted">{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                        @endif
                                                    @endif

                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-end mb-2 mb-md-0">
                                                <div class="d-flex gap-2 align-items-center justify-content-between">
                                                    <div class="load showload-{{ $item->id }}" style="display:none">
                                                    </div>
                                                    <button
                                                        class="theme-2-product-icon m-0 btn border-0 addcartbtn-{{ $item->id }}"
                                                        type="button"
                                                        onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                        <i class="fa-solid fa-cart-shopping"></i>
                                                    </button>

                                                    @if (Auth::user() && Auth::user()->type == 3)
                                                        <div class="set-fav1-{{ $item->id }}">
                                                            @if ($item->is_favorite == 1)
                                                                <a class="theme-2-product-icon" href="javascript:void(0)"
                                                                    onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                                    <i class="fa-solid fa-heart"></i>
                                                                </a>
                                                            @else
                                                                <a class="theme-2-product-icon" href="javascript:void(0)"
                                                                    onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                                    <i class="fa-regular fa-heart"></i>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 3)
                    <div class="row row-cols-xl-5 row-cols-lg-4 row-cols-md-3 row-cols-sm-2 row-cols-2 g-3 pt-4">
                        @foreach ($topdealsproducts as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price >
                                                @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price =
                                                    $item->item_price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item->item_price;
                                            }
                                        }
                                    } else {
                                        if ($item['variation']->count() > 0) {
                                            $price =
                                                $item['variation'][0]->price -
                                                $item['variation'][0]->price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        } else {
                                            $price =
                                                $item->item_price -
                                                $item->item_price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        }
                                    }
                                    if ($item['variation']->count() > 0) {
                                        $original_price = $item['variation'][0]->price;
                                    } else {
                                        $original_price = $item->item_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                } else {
                                    if ($item['variation']->count() > 0) {
                                        $price = $item['variation'][0]->price;
                                        $original_price = $item['variation'][0]->original_price;
                                    } else {
                                        $price = $item->item_price;
                                        $original_price = $item->item_original_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                }
                                $item_name = str_replace("'", "\'", $item->item_name);
                            @endphp
                            <div class="col">
                                <div class="card thme3girdproduct h-100">
                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                        <img src="{{ helper::image_path(@$item['item_image']->image) }}"
                                            class="card-img-top" alt="...">
                                        @if ($off > 0)
                                            <span
                                                class="offer-text rounded fw-500 text-bg-primary fs-8">{{ $off }}%
                                                {{ trans('labels.off') }}</span>
                                        @endif
                                    </a>
                                    <div class="card-body px-2 px-md-2 pb-md-2 py-0">
                                        <div class="text-section">
                                            @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                    App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                                @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                        App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                    @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                        <a class="fs-8 d-flex gap-1 align-items-center mb-1"
                                                            onclick="showreviews('{{ $item->id }}')" role="button"
                                                            aria-controls="offcanvasExample">
                                                            <i class="fa-solid fa-star text-warning"></i>
                                                            <p class="cursor-pointer color-changer fw-600 fs-8">
                                                                {{ number_format($item->avg_ratting, 1) }}</p>
                                                        </a>
                                                    @endif
                                                @endif
                                            @endif
                                            <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                                class="title color-changer pb-1">
                                                {{ $item->item_name }}</a>
                                        </div>
                                    </div>
                                    <div class="card-footer px-2 px-md-2 pb-2 py-0">
                                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                            <div class="products-price d-flex flex-wrap align-items-center gap-1">
                                                <span
                                                    class="price color-changer">{{ helper::currency_formate($price, @$storeinfo->id) }}</span>
                                                @if ($item->item_original_price != null)
                                                    @if ($original_price > $price)
                                                        <del class="text-muted">{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="d-flex justify-content-end">

                                                <div class="load showload-{{ $item->id }}" style="display:none">
                                                </div>
                                                <a class="theme-3-product-icon m-0 addcartbtn-{{ $item->id }}"
                                                    href="javascript:void(0)"
                                                    onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                    <i class="fa-solid fa-plus"></i>
                                                </a>

                                                @if (Auth::user() && Auth::user()->type == 3)
                                                    <div class="favorite-icon set-fav1-{{ $item->id }}">
                                                        @if ($item->is_favorite == 1)
                                                            <a href="javascript:void(0)"
                                                                onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                    class="fa-solid fa-heart"></i></a>
                                                        @else
                                                            <a href="javascript:void(0)"
                                                                onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                    class="fa-regular fa-heart"></i></a>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 4)
                    <div class="row g-3 theme-4-gridproduct-card pt-4">
                        @foreach ($topdealsproducts as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price >
                                                @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price =
                                                    $item->item_price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item->item_price;
                                            }
                                        }
                                    } else {
                                        if ($item['variation']->count() > 0) {
                                            $price =
                                                $item['variation'][0]->price -
                                                $item['variation'][0]->price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        } else {
                                            $price =
                                                $item->item_price -
                                                $item->item_price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        }
                                    }
                                    if ($item['variation']->count() > 0) {
                                        $original_price = $item['variation'][0]->price;
                                    } else {
                                        $original_price = $item->item_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                } else {
                                    if ($item['variation']->count() > 0) {
                                        $price = $item['variation'][0]->price;
                                        $original_price = $item['variation'][0]->original_price;
                                    } else {
                                        $price = $item->item_price;
                                        $original_price = $item->item_original_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                }
                                $item_name = str_replace("'", "\'", $item->item_name);
                            @endphp
                            <div class="col-6 col-md-4 col-lg-4 col-xl-3">
                                <div class="card card-bg border-0 h-100">
                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                        <img src="{{ helper::image_path(@$item['item_image']->image) }}"
                                            class="card-img-top" alt="...">
                                    </a>
                                    @if ($off > 0)
                                        <span
                                            class="offer-text rounded fw-500 text-bg-secondary fs-8">{{ $off }}%
                                            {{ trans('labels.off') }}</span>
                                    @endif
                                    <div class="card-body">
                                        @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                            @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                    App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                    <a class="fs-8 d-flex gap-1 align-items-center mb-1"
                                                        onclick="showreviews('{{ $item->id }}')" role="button"
                                                        aria-controls="offcanvasExample">
                                                        <i class="fa-solid fa-star text-warning"></i>
                                                        <p class="cursor-pointer color-changer fw-600 fs-8">
                                                            {{ number_format($item->avg_ratting, 1) }}</p>
                                                    </a>
                                                @endif
                                            @endif
                                        @endif
                                        <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                            class="title color-changer pb-1">
                                            {{ $item->item_name }}</a>
                                    </div>
                                    <div class="card-footer px-0 pt-0">
                                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                            <div class="products-price d-flex flex-wrap align-items-center gap-1">
                                                <span
                                                    class="price color-changer">{{ helper::currency_formate($price, @$storeinfo->id) }}</span>
                                                @if ($item->item_original_price != null)
                                                    @if ($original_price > $price)
                                                        <del class="text-muted">{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="">
                                                <div class="load showload-{{ $item->id }}" style="display:none">
                                                </div>
                                                <a type="button" class="addcartbtn-{{ $item->id }} color-changer"
                                                    onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                    <i class="fa-solid fa-circle-plus fs-5"></i>
                                                </a>

                                                @if (Auth::user() && Auth::user()->type == 3)
                                                    <div class="favorite-icon set-fav1-{{ $item->id }}">
                                                        @if ($item->is_favorite == 1)
                                                            <a href="javascript:void(0)" class="text-secondary"
                                                                onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                    class="fa-solid fa-heart"></i></a>
                                                        @else
                                                            <a href="javascript:void(0)" class="text-secondary"
                                                                onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                    class="fa-regular fa-heart"></i></a>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 5)
                    <div class="row row-cols-xl-5 row-cols-lg-4 row-cols-md-3 row-cols-sm-2 row-cols-2 g-3 pt-4">
                        @foreach ($topdealsproducts as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price >
                                                @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price =
                                                    $item->item_price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item->item_price;
                                            }
                                        }
                                    } else {
                                        if ($item['variation']->count() > 0) {
                                            $price =
                                                $item['variation'][0]->price -
                                                $item['variation'][0]->price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        } else {
                                            $price =
                                                $item->item_price -
                                                $item->item_price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        }
                                    }
                                    if ($item['variation']->count() > 0) {
                                        $original_price = $item['variation'][0]->price;
                                    } else {
                                        $original_price = $item->item_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                } else {
                                    if ($item['variation']->count() > 0) {
                                        $price = $item['variation'][0]->price;
                                        $original_price = $item['variation'][0]->original_price;
                                    } else {
                                        $price = $item->item_price;
                                        $original_price = $item->item_original_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                }
                                $item_name = str_replace("'", "\'", $item->item_name);
                            @endphp
                            <div class="col">
                                <div class="card thme3girdproduct h-100">
                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                        <img src="{{ helper::image_path(@$item['item_image']->image) }}"
                                            class="card-img-top" alt="...">
                                    </a>
                                    @if ($off > 0)
                                        <span class="offer-text rounded fw-500 text-bg-primary fs-8">{{ $off }}%
                                            {{ trans('labels.off') }}</span>
                                    @endif
                                    <div class="card-body px-2 px-md-2 pb-md-2 py-0">
                                        <div class="text-section">
                                            @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                    App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                                @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                        App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                    @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                        <a class="fs-8 d-flex gap-1 align-items-center mb-1"
                                                            onclick="showreviews('{{ $item->id }}')" role="button"
                                                            aria-controls="offcanvasExample">
                                                            <i class="fa-solid fa-star text-warning"></i>
                                                            <p class="cursor-pointer color-changer fw-600 fs-8">
                                                                {{ number_format($item->avg_ratting, 1) }}</p>
                                                        </a>
                                                    @endif
                                                @endif
                                            @endif
                                            <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                                class="title color-changer pb-1">
                                                {{ $item->item_name }}</a>
                                        </div>
                                    </div>
                                    <div class="card-footer px-2 px-md-2 pb-2 py-0">
                                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                            <div class="">
                                                <div class="products-price d-flex flex-wrap align-items-center gap-1">
                                                    <span
                                                        class="price color-changer">{{ helper::currency_formate($price, @$storeinfo->id) }}</span>
                                                    @if ($item->item_original_price != null)
                                                        @if ($original_price > $price)
                                                            <del class="text-muted">{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                            <div class=" d-flex justify-content-end">

                                                <div class="load showload-{{ $item->id }}" style="display:none">
                                                </div>
                                                <a class="theme-3-product-icon m-0 addcartbtn-{{ $item->id }}"
                                                    href="javascript:void(0)"
                                                    onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                    <i class="fa-solid fa-plus"></i>
                                                </a>

                                                @if (Auth::user() && Auth::user()->type == 3)
                                                    <div class="favorite-icon set-fav1-{{ $item->id }}">
                                                        @if ($item->is_favorite == 1)
                                                            <a href="javascript:void(0)" class="text-secondary"
                                                                onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                    class="fa-solid fa-heart"></i></a>
                                                        @else
                                                            <a href="javascript:void(0)" class="text-secondary"
                                                                onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                    class="fa-regular fa-heart"></i></a>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 6)
                    <div
                        class="row row-cols-xl-5 row-cols-lg-4 row-cols-md-3 row-cols-sm-2 row-cols-2 g-3 pt-4 theme-6-margin-top">
                        @foreach ($topdealsproducts as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price >
                                                @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price =
                                                    $item->item_price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item->item_price;
                                            }
                                        }
                                    } else {
                                        if ($item['variation']->count() > 0) {
                                            $price =
                                                $item['variation'][0]->price -
                                                $item['variation'][0]->price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        } else {
                                            $price =
                                                $item->item_price -
                                                $item->item_price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        }
                                    }
                                    if ($item['variation']->count() > 0) {
                                        $original_price = $item['variation'][0]->price;
                                    } else {
                                        $original_price = $item->item_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                } else {
                                    if ($item['variation']->count() > 0) {
                                        $price = $item['variation'][0]->price;
                                        $original_price = $item['variation'][0]->original_price;
                                    } else {
                                        $price = $item->item_price;
                                        $original_price = $item->item_original_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                }
                                $item_name = str_replace("'", "\'", $item->item_name);
                            @endphp
                            <div class="col products-img">
                                <div class="card h-100 border-0 bg-light position-relative rounded-none">
                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                        <div class="overflow-hidden theme6grid_image">
                                            <img src="{{ helper::image_path(@$item['item_image']->image) }}"
                                                alt="" class="p-2 p-md-3">
                                        </div>
                                    </a>
                                    <div class="card-body px-sm-3 px-2 py-0">
                                        @if ($off > 0)
                                            <span
                                                class="offer-text rounded fw-500 text-bg-secondary fs-8">{{ $off }}%
                                                {{ trans('labels.off') }}</span>
                                        @endif
                                        @if (Auth::user() && Auth::user()->type == 3)
                                            <div class="favorite-icon set-fav1-{{ $item->id }}">
                                                @if ($item->is_favorite == 1)
                                                    <a href="javascript:void(0)"
                                                        onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                            class="fa-solid fa-heart"></i></a>
                                                @else
                                                    <a href="javascript:void(0)"
                                                        onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                            class="fa-regular fa-heart"></i></a>
                                                @endif
                                            </div>
                                        @endif
                                        @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                            @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                    App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                    <a class="fs-8 d-flex gap-1 align-items-center mb-1"
                                                        onclick="showreviews('{{ $item->id }}')" role="button"
                                                        aria-controls="offcanvasExample">
                                                        <i class="fa-solid fa-star text-warning"></i>
                                                        <p class="cursor-pointer color-changer fw-600 fs-8">
                                                            {{ number_format($item->avg_ratting, 1) }}</p>
                                                    </a>
                                                @endif
                                            @endif
                                        @endif
                                        <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                            class="title pb-1 hover-white color-changer line-limit-1">
                                            {{ $item->item_name }}</a>
                                    </div>
                                    <div class="card-footer bg-transparent border-0 px-sm-3 px-2 py-2">
                                        <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                                            <div class="d-flex flex-wrap gap-1 align-items-center">
                                                <p class="price color-changer">
                                                    {{ helper::currency_formate($price, @$storeinfo->id) }}
                                                </p>
                                                @if ($item->item_original_price != null)
                                                    @if ($original_price > $price)
                                                        <del
                                                            class="hover-white">{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                    @endif
                                                @endif
                                            </div>
                                            <div class=" d-flex justify-content-end">
                                                <div class="load showload-{{ $item->id }}" style="display:none">
                                                </div>
                                                <button
                                                    class="btn-primary btn m-0 product-cart-icon rounded-0 addcartbtn-{{ $item->id }}"
                                                    type="button"
                                                    onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                    <i class="fa-solid fa-cart-shopping"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 7)
                    <div class="row g-3 pt-4 theme-7-margin-top">
                        @foreach ($topdealsproducts as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price >
                                                @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price =
                                                    $item->item_price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item->item_price;
                                            }
                                        }
                                    } else {
                                        if ($item['variation']->count() > 0) {
                                            $price =
                                                $item['variation'][0]->price -
                                                $item['variation'][0]->price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        } else {
                                            $price =
                                                $item->item_price -
                                                $item->item_price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        }
                                    }
                                    if ($item['variation']->count() > 0) {
                                        $original_price = $item['variation'][0]->price;
                                    } else {
                                        $original_price = $item->item_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                } else {
                                    if ($item['variation']->count() > 0) {
                                        $price = $item['variation'][0]->price;
                                        $original_price = $item['variation'][0]->original_price;
                                    } else {
                                        $price = $item->item_price;
                                        $original_price = $item->item_original_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                }
                                $item_name = str_replace("'", "\'", $item->item_name);
                            @endphp
                            <div class="col-6 col-lg-4 col-xl-3">
                                <div class="card h-100 border-0 bg-light position-relative rounded">
                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                        <div class="overflow-hidden theme7grid_image">
                                            <img src="{{ helper::image_path(@$item['item_image']->image) }}"
                                                alt="" class="p-2 p-md-3">
                                        </div>
                                    </a>
                                    <div class="card-body p-2 p-md-3 pb-0 pb-md-3 text-center">
                                        <div class="d-flex justify-content-between flex-wrap align-items-center mb-2">
                                            @if ($off > 0)
                                                <span
                                                    class="p-1 px-2 rounded fw-500 text-bg-primary fs-8">{{ $off }}%
                                                    {{ trans('labels.off') }}</span>
                                            @endif
                                            @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                    App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                                @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                        App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                    @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                        <a class="fs-8 d-flex gap-1 align-items-center"
                                                            onclick="showreviews('{{ $item->id }}')" role="button"
                                                            aria-controls="offcanvasExample">
                                                            <i class="fa-solid fa-star text-warning"></i>
                                                            <p class="cursor-pointer color-changer fw-600 fs-8">
                                                                {{ number_format($item->avg_ratting, 1) }}</p>
                                                        </a>
                                                    @endif
                                                @endif
                                            @endif
                                        </div>
                                        <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                            class="title pb-1 color-changer hover-white">
                                            {{ $item->item_name }}</a>
                                    </div>
                                    <div class="card-footer bg-transparent border-0 pb-3 pt-0">
                                        <div class="row justify-content-center align-items-center gx-0">
                                            <div class="d-flex flex-wrap gap-1 align-items-center justify-content-center">
                                                <p class="price color-changer">
                                                    {{ helper::currency_formate($price, @$storeinfo->id) }}
                                                </p>
                                                @if ($item->item_original_price != null)
                                                    @if ($original_price > $price)
                                                        <del
                                                            class="hover-white">{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="">
                                                <ul class="card-option d-flex gap-sm-2 gap-1">
                                                    <li tooltip="{{ trans('labels.addcart') }}"
                                                        onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')"
                                                        class="m-0 p-0 cursor-pointer">
                                                        <div class="load showload-{{ $item->id }}"
                                                            style="display:none"></div>
                                                        <button
                                                            class="product-cart-icon p-0 addcartbtn-{{ $item->id }}"
                                                            type="button">
                                                            <i class="fa-solid fa-cart-shopping fs-7"></i>
                                                        </button>

                                                    </li>
                                                    @if (Auth::user() && Auth::user()->type == 3)
                                                        <li tooltip="Wishlist" class="m-0 p-0">
                                                            <div
                                                                class="product-cart-icon p-0 d-flex justify-content-center align-items-center set-fav1-{{ $item->id }}">
                                                                @if ($item->is_favorite == 1)
                                                                    <a href="javascript:void(0)"
                                                                        onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                            class="fa-solid fa-heart"></i></a>
                                                                @else
                                                                    <a href="javascript:void(0)"
                                                                        onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                            class="fa-regular fa-heart"></i></a>
                                                                @endif
                                                            </div>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 8)
                    <div class="row g-3 pt-4 theme-8-margin-top">
                        @foreach ($topdealsproducts as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price >
                                                @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price =
                                                    $item->item_price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item->item_price;
                                            }
                                        }
                                    } else {
                                        if ($item['variation']->count() > 0) {
                                            $price =
                                                $item['variation'][0]->price -
                                                $item['variation'][0]->price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        } else {
                                            $price =
                                                $item->item_price -
                                                $item->item_price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        }
                                    }
                                    if ($item['variation']->count() > 0) {
                                        $original_price = $item['variation'][0]->price;
                                    } else {
                                        $original_price = $item->item_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                } else {
                                    if ($item['variation']->count() > 0) {
                                        $price = $item['variation'][0]->price;
                                        $original_price = $item['variation'][0]->original_price;
                                    } else {
                                        $price = $item->item_price;
                                        $original_price = $item->item_original_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                }
                                $item_name = str_replace("'", "\'", $item->item_name);
                            @endphp
                            <div class="col-6 col-lg-4 col-xl-3 products-img">
                                <div class="card h-100 border-0 position-relative">
                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                        <div class="overflow-hidden">
                                            <img src="{{ helper::image_path(@$item['item_image']->image) }}"
                                                alt="" class="p-2 p-md-3">
                                        </div>
                                    </a>
                                    <div class="card-body p-2 p-md-3 pb-0 pb-md-3 text-center">
                                        <div class="d-flex justify-content-between flex-wrap align-items-center mb-2">
                                            @if ($off > 0)
                                                <span
                                                    class="p-1 px-2 rounded fw-500 text-bg-primary fs-8">{{ $off }}%
                                                    {{ trans('labels.off') }}</span>
                                            @endif
                                            @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                    App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                                @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                        App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                    @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                        <a class="fs-8 d-flex gap-1 align-items-center"
                                                            onclick="showreviews('{{ $item->id }}')" role="button"
                                                            aria-controls="offcanvasExample">
                                                            <i class="fa-solid fa-star text-warning"></i>
                                                            <p class="cursor-pointer color-changer fw-600 fs-8">
                                                                {{ number_format($item->avg_ratting, 1) }}</p>
                                                        </a>
                                                    @endif
                                                @endif
                                            @endif
                                        </div>
                                        <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                            class="title pb-1 color-changer hover-white">
                                            {{ $item->item_name }}</a>
                                    </div>
                                    <div class="card-footer bg-transparent border-0 pb-3 pt-0">
                                        <div class="row justify-content-center align-items-center gx-0">
                                            <div class="d-flex flex-wrap align-items-center justify-content-center gap-1">
                                                <p class="price color-changer">
                                                    {{ helper::currency_formate($price, @$storeinfo->id) }}
                                                </p>
                                                @if ($item->item_original_price != null)
                                                    @if ($original_price > $price)
                                                        <del
                                                            class="hover-white text-muted">{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                    @endif
                                                @endif
                                            </div>
                                            <div class=" text-center">
                                                <ul class="card-option d-flex justify-content-center">
                                                    <li tooltip="{{ trans('labels.addcart') }}"
                                                        onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')"
                                                        class="m-0 p-0 cursor-pointer">
                                                        <div class="load showload-{{ $item->id }}"
                                                            style="display:none"></div>
                                                        <button
                                                            class="product-cart-icon p-0 addcartbtn-{{ $item->id }}"
                                                            type="button">
                                                            <i class="fa-solid fa-cart-shopping"></i>
                                                        </button>
                                                    </li>
                                                    @if (Auth::user() && Auth::user()->type == 3)
                                                        <li tooltip="Wishlist" class="m-0 p-0">
                                                            <div class="set-fav-38">
                                                                <div
                                                                    class="product-cart-icon p-0 d-flex align-items-center justify-content-center set-fav1-{{ $item->id }}">
                                                                    @if ($item->is_favorite == 1)
                                                                        <a href="javascript:void(0)" class="d-flex"
                                                                            onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                                class="fa-solid fa-heart"></i></a>
                                                                    @else
                                                                        <a href="javascript:void(0)" class="d-flex"
                                                                            onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                                class="fa-regular fa-heart"></i></a>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 9)
                    <div
                        class="row row-cols-xl-5 row-cols-lg-4 row-cols-md-3 row-cols-sm-2 row-cols-2 g-3 pt-4 theme-9-margin-top">
                        @foreach ($topdealsproducts as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price >
                                                @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price =
                                                    $item->item_price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item->item_price;
                                            }
                                        }
                                    } else {
                                        if ($item['variation']->count() > 0) {
                                            $price =
                                                $item['variation'][0]->price -
                                                $item['variation'][0]->price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        } else {
                                            $price =
                                                $item->item_price -
                                                $item->item_price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        }
                                    }
                                    if ($item['variation']->count() > 0) {
                                        $original_price = $item['variation'][0]->price;
                                    } else {
                                        $original_price = $item->item_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                } else {
                                    if ($item['variation']->count() > 0) {
                                        $price = $item['variation'][0]->price;
                                        $original_price = $item['variation'][0]->original_price;
                                    } else {
                                        $price = $item->item_price;
                                        $original_price = $item->item_original_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                }
                            @endphp
                            <div class="col">
                                <div class="card card-bg thme9categories dark h-100 border-0">
                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                        <img src="{{ helper::image_path(@$item['item_image']->image) }}"
                                            class="card-img-top m-0 img-hight-fiexed" alt="...">
                                        @if ($off > 0)
                                            <span
                                                class="offer-text rounded fw-500 text-bg-secondary fs-8">{{ $off }}%
                                                {{ trans('labels.off') }}</span>
                                        @endif
                                    </a>
                                    <div class="card-body px-0 pb-1">
                                        @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                            @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                    App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                    <a class="fs-8 d-flex gap-1 align-items-center mb-1"
                                                        onclick="showreviews('{{ $item->id }}')" role="button"
                                                        aria-controls="offcanvasExample">
                                                        <i class="fa-solid fa-star text-warning"></i>
                                                        <p class="cursor-pointer color-changer fw-600 fs-8">
                                                            {{ number_format($item->avg_ratting, 1) }}</p>
                                                    </a>
                                                @endif
                                            @endif
                                        @endif
                                        <div class="text-section">
                                            <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                                class="title fs-6 pb-1 color-changer hover-white">
                                                {{ $item->item_name }}</a>
                                        </div>
                                    </div>
                                    @if (Auth::user() && Auth::user()->type == 3)
                                        <div class="theme-9-favorite-icon grid-icon p-1 set-fav1-{{ $item->id }}">
                                            @if ($item->is_favorite == 1)
                                                <a href="javascript:void(0)"
                                                    onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                        class="fa-solid fa-heart"></i></a>
                                            @else
                                                <a href="javascript:void(0)"
                                                    onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                        class="fa-regular fa-heart"></i></a>
                                            @endif
                                        </div>
                                    @endif
                                    <div
                                        class="card-footer d-sm-flex align-items-center justify-content-between bg-transparent border-0 p-0">
                                        <div class="products-price d-flex align-items-center gap-1 flex-wrap">
                                            <span class="price color-changer">
                                                {{ helper::currency_formate($price, @$storeinfo->id) }}</span>
                                            @if ($item->item_original_price != null)
                                                @if ($original_price > $price)
                                                    <del
                                                        class="hover-white text-muted">{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 10)
                    <div class="row g-3 pt-4">
                        @foreach ($topdealsproducts as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price >
                                                @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price =
                                                    $item->item_price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item->item_price;
                                            }
                                        }
                                    } else {
                                        if ($item['variation']->count() > 0) {
                                            $price =
                                                $item['variation'][0]->price -
                                                $item['variation'][0]->price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        } else {
                                            $price =
                                                $item->item_price -
                                                $item->item_price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        }
                                    }
                                    if ($item['variation']->count() > 0) {
                                        $original_price = $item['variation'][0]->price;
                                    } else {
                                        $original_price = $item->item_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                } else {
                                    if ($item['variation']->count() > 0) {
                                        $price = $item['variation'][0]->price;
                                        $original_price = $item['variation'][0]->original_price;
                                    } else {
                                        $price = $item->item_price;
                                        $original_price = $item->item_original_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                }
                                $item_name = str_replace("'", "\'", $item->item_name);
                            @endphp
                            <div class="col-6 col-lg-4 col-xl-3">
                                <div class="card h-100 border-secondary bg-transparent position-relative">
                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                        <div class="overflow-hidden">
                                            <img src="{{ helper::image_path(@$item['item_image']->image) }}"
                                                alt="" class="p-2 p-md-3 theme-10-product-img">
                                        </div>
                                        @if ($off > 0)
                                            <span
                                                class="offer-text rounded fw-500 text-bg-secondary fs-8">{{ $off }}%
                                                {{ trans('labels.off') }}</span>
                                        @endif
                                    </a>
                                    <div class="card-body px-2 px-md-3 py-0 py-md-0">
                                        @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                            @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                    App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                    <a class="fs-8 d-flex gap-1 align-items-center mb-1"
                                                        onclick="showreviews('{{ $item->id }}')" role="button"
                                                        aria-controls="offcanvasExample">
                                                        <i class="fa-solid fa-star text-warning"></i>
                                                        <p class="cursor-pointer color-changer fw-600 fs-8">
                                                            {{ number_format($item->avg_ratting, 1) }}</p>
                                                    </a>
                                                @endif
                                            @endif
                                        @endif
                                        <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                            class="title mb-2 color-changer hover-white">
                                            {{ $item->item_name }}</a>
                                        <div class="d-flex flex-wrap gap-1 align-items-center">
                                            <p class="price color-changer">
                                                {{ helper::currency_formate($price, @$storeinfo->id) }}
                                            </p>

                                            @if ($item->item_original_price != null)
                                                @if ($original_price > $price)
                                                    <del
                                                        class="hover-white">{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent border-0 px-2 px-md-3 pb-3 pt-0">
                                        <div>
                                            <div class="text-center mt-3">
                                                <ul class="mb-0">
                                                    <li>
                                                        <div class="theme-10-load">
                                                            <div class="load-2 showload-{{ $item->id }} "
                                                                style="display:none"></div>
                                                        </div>
                                                        <button
                                                            class="theme-10-product-cart-icon addcartbtn-{{ $item->id }}"
                                                            type="button"
                                                            onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                            {{ trans('labels.addcart') }}
                                                        </button>

                                                    </li>
                                                    @if (Auth::user() && Auth::user()->type == 3)
                                                        <li>
                                                            <div class="set-fav-38 theme-10-heart-option">
                                                                <div
                                                                    class="theme-10-product-heart-icon set-fav1-{{ $item->id }}">
                                                                    @if ($item->is_favorite == 1)
                                                                        <a href="javascript:void(0)"
                                                                            onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                                class="fa-solid fa-heart"></i></a>
                                                                    @else
                                                                        <a href="javascript:void(0)"
                                                                            onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                                class="fa-regular fa-heart"></i></a>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 11)
                    <div class="row row-cols-xl-5 row-cols-lg-4 row-cols-md-3 row-cols-sm-2 row-cols-2 g-3 pt-4">
                        @foreach ($topdealsproducts as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price >
                                                @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price =
                                                    $item->item_price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item->item_price;
                                            }
                                        }
                                    } else {
                                        if ($item['variation']->count() > 0) {
                                            $price =
                                                $item['variation'][0]->price -
                                                $item['variation'][0]->price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        } else {
                                            $price =
                                                $item->item_price -
                                                $item->item_price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        }
                                    }
                                    if ($item['variation']->count() > 0) {
                                        $original_price = $item['variation'][0]->price;
                                    } else {
                                        $original_price = $item->item_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                } else {
                                    if ($item['variation']->count() > 0) {
                                        $price = $item['variation'][0]->price;
                                        $original_price = $item['variation'][0]->original_price;
                                    } else {
                                        $price = $item->item_price;
                                        $original_price = $item->item_original_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                }
                                $item_name = str_replace("'", "\'", $item->item_name);
                            @endphp
                            <div class="col theme-11">
                                <div class="product-grid text-center card h-100 rounded-3">
                                    <div class="product-image position-relative">
                                        <a class="image">
                                            <img class="pic-1"
                                                src="{{ @helper::image_path($item['multi_image'][0]->image) }}"
                                                class="img-fluid" alt="...">
                                            <img class="pic-2"
                                                src="{{ @$item['multi_image']->count() > 1 ? @helper::image_path($item['multi_image'][1]->image) : @helper::image_path($item['multi_image'][0]->image) }}">
                                        </a>
                                        <span class="product-discount-label">
                                            @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                    App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                                @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                        App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                    @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                        <a class="fs-8 d-flex gap-1 align-items-center p-1"
                                                            onclick="showreviews('{{ $item->id }}')" role="button"
                                                            aria-controls="offcanvasExample">
                                                            <i class="fa-solid fa-star text-warning"></i>
                                                            <p class="cursor-pointer text-white fw-600 fs-8">
                                                                {{ number_format($item->avg_ratting, 1) }}</p>
                                                        </a>
                                                    @endif
                                                @endif
                                            @endif
                                        </span>
                                        @if ($off > 0)
                                            <span class="theme-11-ribbon">
                                                <h3>{{ $off }}% {{ trans('labels.off') }}</h3>
                                            </span>
                                        @endif
                                        <ul class="product-links d-flex gap-1 justify-content-center">
                                            @if (Auth::user() && Auth::user()->type == 3)
                                                <li class="set-fav1-{{ $item->id }}">
                                                    @if ($item->is_favorite == 1)
                                                        <a class="theme-2-product-icon" href="javascript:void(0)"
                                                            onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                            <i class="fa-solid fa-heart"></i>
                                                        </a>
                                                    @else
                                                        <a class="theme-2-product-icon" href="javascript:void(0)"
                                                            onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                            <i class="fa-regular fa-heart"></i>
                                                        </a>
                                                    @endif
                                                </li>
                                            @endif
                                            <li>
                                                <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                                    <i class="fa-regular fa-eye"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a type="button"
                                                    onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                    <div class="load showload-{{ $item->id }}" style="display:none">
                                                    </div>
                                                    <i
                                                        class="fa-solid fa-cart-shopping addcartbtn-{{ $item->id }}"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-body product-content pb-0 p-2">
                                        <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                            class="fs-7 color-changer fw-500 line-2">
                                            {{ $item->item_name }}
                                        </a>
                                    </div>
                                    <div class="card-footer p-2">
                                        <div
                                            class="products-price d-flex flex-wrap gap-1 justify-content-center align-items-center">
                                            <span class="fs-15 fw-600 text-primary color-changer">
                                                {{ helper::currency_formate($price, @$storeinfo->id) }}
                                            </span>
                                            @if ($item->item_original_price != null)
                                                @if ($original_price > $price)
                                                    <del>{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 12)
                    <div class="row g-3 row-cols-xl-5 row-cols-lg-4 row-cols-md-3 row-cols-sm-2 row-cols-2 pt-4">
                        @foreach ($topdealsproducts as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price >
                                                @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price =
                                                    $item->item_price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item->item_price;
                                            }
                                        }
                                    } else {
                                        if ($item['variation']->count() > 0) {
                                            $price =
                                                $item['variation'][0]->price -
                                                $item['variation'][0]->price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        } else {
                                            $price =
                                                $item->item_price -
                                                $item->item_price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        }
                                    }
                                    if ($item['variation']->count() > 0) {
                                        $original_price = $item['variation'][0]->price;
                                    } else {
                                        $original_price = $item->item_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                } else {
                                    if ($item['variation']->count() > 0) {
                                        $price = $item['variation'][0]->price;
                                        $original_price = $item['variation'][0]->original_price;
                                    } else {
                                        $price = $item->item_price;
                                        $original_price = $item->item_original_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                }
                                $item_name = str_replace("'", "\'", $item->item_name);
                            @endphp
                            <div class="col theme-12">
                                <div class="product-grid card shadow border-0 rounded-5 overflow-visible h-100">
                                    <div class="product-image">
                                        <a class="image">
                                            <img class="pic-1"
                                                src="{{ @helper::image_path($item['multi_image'][0]->image) }}"
                                                class="img-fluid" alt="...">
                                            <img class="pic-2"
                                                src="{{ @$item['multi_image']->count() > 1 ? @helper::image_path($item['multi_image'][1]->image) : @helper::image_path($item['multi_image'][0]->image) }}">
                                        </a>
                                        @if ($off > 0)
                                            <span class="product-hot-label">
                                                {{ $off }}% {{ trans('labels.off') }}
                                            </span>
                                        @endif
                                        <span class="product-sale-label rounded-5">
                                            @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                    App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                                @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                        App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                    @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                        <a class="fs-8 text-white d-flex gap-1 align-items-center"
                                                            onclick="showreviews('{{ $item->id }}')" role="button"
                                                            aria-controls="offcanvasExample">
                                                            <i class="fa-solid fa-star text-warning"></i>
                                                            <p class="cursor-pointer fw-600 fs-8">
                                                                {{ number_format($item->avg_ratting, 1) }}</p>
                                                        </a>
                                                    @endif
                                                @endif
                                            @endif
                                        </span>
                                        <ul class="product-links">
                                            @if (Auth::user() && Auth::user()->type == 3)
                                                <li class="set-fav1-{{ $item->id }}">
                                                    @if ($item->is_favorite == 1)
                                                        <a class="theme-2-product-icon shadow" href="javascript:void(0)"
                                                            onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                            <i class="fa-solid fa-heart"></i>
                                                        </a>
                                                    @else
                                                        <a class="theme-2-product-icon shadow" href="javascript:void(0)"
                                                            onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                            <i class="fa-regular fa-heart"></i>
                                                        </a>
                                                    @endif
                                                </li>
                                            @endif
                                            <li>
                                                <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                                    class="shadow">
                                                    <i class="fa-regular fa-eye"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a type="button" class="shadow"
                                                    onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                    <div class="load showload-{{ $item->id }}" style="display:none">
                                                    </div>
                                                    <i
                                                        class="fa-solid fa-cart-shopping addcartbtn-{{ $item->id }}"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-body product-content pb-0 p-3">
                                        <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                            class="fs-7 fw-500 color-changer line-2">
                                            {{ $item->item_name }}
                                        </a>
                                    </div>
                                    <div class="card-footer p-2">
                                        <div class="d-flex flex-wrap gap-1 justify-content-center align-items-center">
                                            <span class="fs-15 fw-600 text-primary color-changer">
                                                {{ helper::currency_formate($price, @$storeinfo->id) }}
                                            </span>
                                            @if ($item->item_original_price != null)
                                                @if ($original_price > $price)
                                                    <del>{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 13)
                    <div
                        class="row g-3 pt-4 row-cols-xl-5 row-cols-lg-4 row-cols-md-3 row-cols-sm-2 row-cols-2 products-img">
                        @foreach ($topdealsproducts as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price >
                                                @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price =
                                                    $item->item_price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item->item_price;
                                            }
                                        }
                                    } else {
                                        if ($item['variation']->count() > 0) {
                                            $price =
                                                $item['variation'][0]->price -
                                                $item['variation'][0]->price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        } else {
                                            $price =
                                                $item->item_price -
                                                $item->item_price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        }
                                    }
                                    if ($item['variation']->count() > 0) {
                                        $original_price = $item['variation'][0]->price;
                                    } else {
                                        $original_price = $item->item_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                } else {
                                    if ($item['variation']->count() > 0) {
                                        $price = $item['variation'][0]->price;
                                        $original_price = $item['variation'][0]->original_price;
                                    } else {
                                        $price = $item->item_price;
                                        $original_price = $item->item_original_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                }
                                $item_name = str_replace("'", "\'", $item->item_name);
                            @endphp
                            <div class="col theme-13">
                                <div class="product-grid card border-0 h-100">
                                    <div class="product-image">
                                        <a class="image">
                                            <img class="pic-1"
                                                src="{{ @helper::image_path($item['multi_image'][0]->image) }}"
                                                class="img-fluid" alt="...">
                                            <img class="pic-2"
                                                src="{{ @$item['multi_image']->count() > 1 ? @helper::image_path($item['multi_image'][1]->image) : @helper::image_path($item['multi_image'][0]->image) }}">
                                        </a>
                                        @if ($off > 0)
                                            <span class="product-sale-label">
                                                {{ $off }}% {{ trans('labels.off') }}
                                            </span>
                                        @endif
                                        <ul class="social">
                                            <li>
                                                <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                                    class="shadow">
                                                    <i class="fa-regular fa-eye"></i>
                                                </a>
                                            </li>
                                            @if (Auth::user() && Auth::user()->type == 3)
                                                <li class="set-fav1-{{ $item->id }}">
                                                    @if ($item->is_favorite == 1)
                                                        <a href="javascript:void(0)"
                                                            onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                            <i class="fa-solid fa-heart"></i>
                                                        </a>
                                                    @else
                                                        <a href="javascript:void(0)"
                                                            onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                            <i class="fa-regular fa-heart"></i>
                                                        </a>
                                                    @endif
                                                </li>
                                            @endif
                                        </ul>
                                        <div
                                            class="product-rating flex-wrap d-flex justify-content-between gap-1 align-items-center">
                                            @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                    App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                                @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                        App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                    @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                        <a class="fs-8 text-dark d-flex gap-1 align-items-center"
                                                            onclick="showreviews('{{ $item->id }}')" role="button"
                                                            aria-controls="offcanvasExample">
                                                            <i class="fa-solid fa-star text-warning"></i>
                                                            <p class="cursor-pointer fw-600 fs-8">
                                                                {{ number_format($item->avg_ratting, 1) }}</p>
                                                        </a>
                                                    @endif
                                                @endif
                                            @endif
                                            <a class="add-to-cart cursor-pointer"
                                                onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                <div class="load showload-{{ $item->id }}" style="display:none">
                                                </div>
                                                <span
                                                    class="addcartbtn-{{ $item->id }}">{{ trans('labels.addcart') }}</span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-body bg-change-mode product-content p-3">
                                        <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                            class="fs-7 fw-500 color-changer line-2">
                                            {{ $item->item_name }}
                                        </a>
                                    </div>
                                    <div class="card-footer bg-change-mode p-2">
                                        <div
                                            class="products-price d-flex flex-wrap gap-1 justify-content-center align-items-center">
                                            <span class="fs-15 fw-600 color-changer text-primary">
                                                {{ helper::currency_formate($price, @$storeinfo->id) }}
                                            </span>
                                            @if ($item->item_original_price != null)
                                                @if ($original_price > $price)
                                                    <del>{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 14)
                    <div class="row row-cols-xl-5 row-cols-lg-4 row-cols-md-3 row-cols-sm-2 row-cols-2 g-3 pt-4">
                        @foreach ($topdealsproducts as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price >
                                                @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price =
                                                    $item->item_price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item->item_price;
                                            }
                                        }
                                    } else {
                                        if ($item['variation']->count() > 0) {
                                            $price =
                                                $item['variation'][0]->price -
                                                $item['variation'][0]->price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        } else {
                                            $price =
                                                $item->item_price -
                                                $item->item_price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        }
                                    }
                                    if ($item['variation']->count() > 0) {
                                        $original_price = $item['variation'][0]->price;
                                    } else {
                                        $original_price = $item->item_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                } else {
                                    if ($item['variation']->count() > 0) {
                                        $price = $item['variation'][0]->price;
                                        $original_price = $item['variation'][0]->original_price;
                                    } else {
                                        $price = $item->item_price;
                                        $original_price = $item->item_original_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                }
                                $item_name = str_replace("'", "\'", $item->item_name);
                            @endphp
                            <div class="col theme-14">
                                <div class="product-grid card h-100">
                                    <div class="product-image">
                                        <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                            class="image">
                                            <img src="{{ helper::image_path(@$item['item_image']->image) }}"
                                                class="card-img-top" alt="...">
                                        </a>
                                        @if (Auth::user() && Auth::user()->type == 3)
                                            <div class="product-like-icon set-fav1-{{ $item->id }}">
                                                @if ($item->is_favorite == 1)
                                                    <a href="javascript:void(0)" class="text-danger"
                                                        onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                        <i class="fa-solid fa-heart text-danger"></i>
                                                    </a>
                                                @else
                                                    <a href="javascript:void(0)" class="text-danger"
                                                        onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                        <i class="fa-regular fa-heart text-danger"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-body product-content pb-0 p-3">
                                        <div class="d-flex align-items-center mb-2 justify-content-between gap-1">
                                            @if ($off > 0)
                                                <span
                                                    class="px-2 py-1 text-capitalize rounded bg-primary fw-600 text-white fs-11">
                                                    {{ $off }}% {{ trans('labels.off') }}
                                                </span>
                                            @endif
                                            @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                    App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                                @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                        App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                    @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                        <a class="fs-8 text-dark d-flex gap-1 align-items-center"
                                                            onclick="showreviews('{{ $item->id }}')" role="button"
                                                            aria-controls="offcanvasExample">
                                                            <i class="fa-solid fa-star text-warning"></i>
                                                            <p class="cursor-pointer color-changer fw-600 fs-8">
                                                                {{ number_format($item->avg_ratting, 1) }}</p>
                                                        </a>
                                                    @endif
                                                @endif
                                            @endif
                                        </div>
                                        <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                            class="fs-7 fw-600 color-changer line-2">
                                            {{ $item->item_name }}
                                        </a>
                                    </div>
                                    <div class="card-footer p-3">
                                        <div class="d-flex flex-wrap gap-1 align-items-center justify-content-between">
                                            <div class="products-price d-flex flex-wrap gap-1 align-items-center">
                                                <span class="fs-15 fw-600 text-primary color-changer">
                                                    {{ helper::currency_formate($price, @$storeinfo->id) }}
                                                </span>
                                                @if ($item->item_original_price != null)
                                                    @if ($original_price > $price)
                                                        <del>{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="load showload-{{ $item->id }}" style="display:none">
                                            </div>
                                            <a class="add-to-cart addcartbtn-{{ $item->id }}"
                                                href="javascript:void(0)"
                                                onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                <i class="fas fa-cart-plus"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 15)
                    <div class="row g-3 pt-4 row-cols-xl-5 row-cols-lg-4 row-cols-md-3 row-cols-sm-2 row-cols-2">
                        @foreach ($topdealsproducts as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price >
                                                @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price =
                                                    $item->item_price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item->item_price;
                                            }
                                        }
                                    } else {
                                        if ($item['variation']->count() > 0) {
                                            $price =
                                                $item['variation'][0]->price -
                                                $item['variation'][0]->price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        } else {
                                            $price =
                                                $item->item_price -
                                                $item->item_price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        }
                                    }
                                    if ($item['variation']->count() > 0) {
                                        $original_price = $item['variation'][0]->price;
                                    } else {
                                        $original_price = $item->item_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                } else {
                                    if ($item['variation']->count() > 0) {
                                        $price = $item['variation'][0]->price;
                                        $original_price = $item['variation'][0]->original_price;
                                    } else {
                                        $price = $item->item_price;
                                        $original_price = $item->item_original_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                }
                                $item_name = str_replace("'", "\'", $item->item_name);
                            @endphp
                            <div class="col theme-15">
                                <div class="product-grid card card-bg border-0 h-100">
                                    <div class="product-image">
                                        <a class="image">
                                            <img class="pic-1"
                                                src="{{ @helper::image_path($item['multi_image'][0]->image) }}"
                                                class="img-fluid" alt="...">
                                            <img class="pic-2"
                                                src="{{ @$item['multi_image']->count() > 1 ? @helper::image_path($item['multi_image'][1]->image) : @helper::image_path($item['multi_image'][0]->image) }}">
                                        </a>
                                        @if ($off > 0)
                                            <span class="product-discount-label">
                                                {{ $off }}% {{ trans('labels.off') }}
                                            </span>
                                        @endif
                                        <ul class="social">
                                            @if (Auth::user() && Auth::user()->type == 3)
                                                <li class="set-fav1-{{ $item->id }}">
                                                    @if ($item->is_favorite == 1)
                                                        <a href="javascript:void(0)"
                                                            onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                            <i class="fa-solid fa-heart"></i>
                                                        </a>
                                                    @else
                                                        <a href="javascript:void(0)"
                                                            onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                            <i class="fa-regular fa-heart"></i>
                                                        </a>
                                                    @endif
                                                </li>
                                            @endif
                                            <li>
                                                <a type="button"
                                                    onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                    <div class="load showload-{{ $item->id }}" style="display:none">
                                                    </div>
                                                    <i
                                                        class="fa-solid fa-cart-shopping addcartbtn-{{ $item->id }}"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                                    <i class="fa-regular fa-eye"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-body product-content">
                                        @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                            @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                    App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                    <a class="fs-8 text-dark mb-2 d-flex gap-1 justify-content-between align-items-center"
                                                        onclick="showreviews('{{ $item->id }}')" role="button"
                                                        aria-controls="offcanvasExample">
                                                        @php
                                                            $count = (float) $item->avg_ratting;
                                                            $fullStars = 0;
                                                            if ($count > 4.5) {
                                                                $fullStars = $count >= 4.75 ? 5 : floor($count);
                                                            } elseif ($count > 3.5) {
                                                                $fullStars = $count >= 3.75 ? 4 : floor($count);
                                                            } elseif ($count > 2.5) {
                                                                $fullStars = $count >= 2.75 ? 3 : floor($count);
                                                            } elseif ($count > 1.5) {
                                                                $fullStars = $count >= 1.75 ? 2 : floor($count);
                                                            } elseif ($count > 0.5) {
                                                                $fullStars = $count >= 0.75 ? 1 : floor($count);
                                                            }
                                                            $hasHalfStar = $count - $fullStars >= 0.5 && $fullStars < 5;
                                                        @endphp
                                                        <ul class="d-flex gap-1 m-0">
                                                            @for ($i = 0; $i < 5; $i++)
                                                                @if ($i < $fullStars)
                                                                    <li class="list-inline-item me-0 fs-8">
                                                                        <i class="fa-solid fa-star text-warning"></i>
                                                                    </li>
                                                                @elseif ($i == $fullStars && $hasHalfStar)
                                                                    <li class="list-inline-item me-0 fs-8">
                                                                        <i
                                                                            class="fa-solid fa-star-half-stroke text-warning"></i>
                                                                    </li>
                                                                @else
                                                                    <li class="list-inline-item me-0 fs-8">
                                                                        <i class="fa-regular fa-star text-warning"></i>
                                                                    </li>
                                                                @endif
                                                            @endfor
                                                        </ul>
                                                        <p class="cursor-pointer color-changer fw-600 fs-8">
                                                            {{ number_format($item->avg_ratting, 1) }}</p>
                                                    </a>
                                                @endif
                                            @endif
                                        @endif
                                        <h3 class="title border-bottom m-0">
                                            <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                                class="fs-7 fw-500 color-changer line-2">
                                                {{ $item->item_name }}
                                            </a>
                                        </h3>
                                    </div>
                                    <div class="card-footer p-0">
                                        <div class="d-flex flex-wrap gap-1 align-items-center">
                                            <span class="fs-15 fw-600 text-primary color-changer">
                                                {{ helper::currency_formate($price, @$storeinfo->id) }}
                                            </span>
                                            @if ($item->item_original_price != null)
                                                @if ($original_price > $price)
                                                    <del>{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div id="column-view" class="d-none">
                @if (helper::appdata($storeinfo->id)->template == 1)
                    <div class="row g-2 g-md-3 pt-4">
                        @foreach ($topdealsproducts as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price >
                                                @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price =
                                                    $item->item_price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item->item_price;
                                            }
                                        }
                                    } else {
                                        if ($item['variation']->count() > 0) {
                                            $price =
                                                $item['variation'][0]->price -
                                                $item['variation'][0]->price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        } else {
                                            $price =
                                                $item->item_price -
                                                $item->item_price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        }
                                    }
                                    if ($item['variation']->count() > 0) {
                                        $original_price = $item['variation'][0]->price;
                                    } else {
                                        $original_price = $item->item_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                } else {
                                    if ($item['variation']->count() > 0) {
                                        $price = $item['variation'][0]->price;
                                        $original_price = $item['variation'][0]->original_price;
                                    } else {
                                        $price = $item->item_price;
                                        $original_price = $item->item_original_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                }
                                $item_name = str_replace("'", "\'", $item->item_name);
                            @endphp
                            <div class="col-md-6">
                                <div class="card thme1categories rounded dark h-100">
                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                        <img src="{{ helper::image_path(@$item['item_image']->image) }}"
                                            class="card-img-top rounded" alt="...">
                                    </a>
                                    <div class="card-body">
                                        <div class="text-section">
                                            <div class="d-flex justify-content-between flex-wrap align-items-center">
                                                @if ($off > 0)
                                                    <span
                                                        class="p-1 px-2 rounded fw-500 text-bg-secondary fs-8">{{ $off }}%
                                                        {{ trans('labels.off') }}</span>
                                                @endif
                                                @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                        App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                                    @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                            App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                        @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                            <a class="fs-8 d-flex gap-1 align-items-center mb-1"
                                                                onclick="showreviews('{{ $item->id }}')"
                                                                role="button" aria-controls="offcanvasExample">
                                                                <i class="fa-solid fa-star text-warning"></i>
                                                                <p class="cursor-pointer color-changer fw-600 fs-8">
                                                                    {{ number_format($item->avg_ratting, 1) }}</p>
                                                            </a>
                                                        @endif
                                                    @endif
                                                @endif
                                            </div>
                                            <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                                class="title pb-1 color-changer mt-2">{{ $item->item_name }}</a>
                                            <div class="d-flex align-items-baseline mt-2">
                                                <div class="products-price d-flex gap-2 align-items-center">
                                                    <span class="price color-changer">
                                                        {{ helper::currency_formate($price, @$storeinfo->id) }}</span>
                                                    @if ($item->item_original_price != null)
                                                        @if ($original_price > $price)
                                                            <del class="text-muted">{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                <div class="d-flex col-sm-auto justify-content-end align-items-center">
                                                    <button
                                                        class="btn-primary w-100 px-3 py-2 d-flex gap-2 fw-500 align-items-center justify-content-center fs-7 btn m-0"
                                                        type="button"
                                                        onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                        <div class="addcartbtn-{{ $item->id }}">
                                                            <i class="fa-regular fa-plus"></i>
                                                            {{ trans('labels.add_to_cart') }}
                                                        </div>
                                                        <div class="load showload-{{ $item->id }}"
                                                            style="display:none">
                                                        </div>
                                                    </button>
                                                </div>
                                                @if (Auth::user() && Auth::user()->type == 3)
                                                    <div class="favorite-icon1 p-0 set-fav1-{{ $item->id }}">
                                                        @if ($item->is_favorite == 1)
                                                            <a href="javascript:void(0)" class="text-secondary"
                                                                onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                    class="fa-solid fa-heart"></i></a>
                                                        @else
                                                            <a href="javascript:void(0)" class="text-secondary"
                                                                onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                    class="fa-regular fa-heart"></i></a>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 2)
                    <div class="row g-3 pt-4">
                        @foreach ($topdealsproducts as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price >
                                                @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price =
                                                    $item->item_price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item->item_price;
                                            }
                                        }
                                    } else {
                                        if ($item['variation']->count() > 0) {
                                            $price =
                                                $item['variation'][0]->price -
                                                $item['variation'][0]->price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        } else {
                                            $price =
                                                $item->item_price -
                                                $item->item_price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        }
                                    }
                                    if ($item['variation']->count() > 0) {
                                        $original_price = $item['variation'][0]->price;
                                    } else {
                                        $original_price = $item->item_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                } else {
                                    if ($item['variation']->count() > 0) {
                                        $price = $item['variation'][0]->price;
                                        $original_price = $item['variation'][0]->original_price;
                                    } else {
                                        $price = $item->item_price;
                                        $original_price = $item->item_original_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                }
                                $item_name = str_replace("'", "\'", $item->item_name);
                            @endphp
                            <div class="col-md-6 col-lg-6 col-xl-4">
                                <div class="card border-0 rounded theme-2-products-card h-100 p-2">
                                    <div class="row g-2 justify-content-center align-items-center">
                                        <div class="col-md-5 col-5">
                                            <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                                <img src="{{ helper::image_path(@$item['item_image']->image) }}"
                                                    class="img-fluid rounded" alt="...">
                                            </a>
                                        </div>
                                        <div class="col-md-7 col-7 theme2list">
                                            <div class="card-body p-0">
                                                <div
                                                    class="d-flex justify-content-between flex-wrap mb-2 align-items-center">
                                                    @if ($off > 0)
                                                        <span
                                                            class="p-1 px-2 rounded fw-500 text-bg-primary fs-8">{{ $off }}%
                                                            {{ trans('labels.off') }}</span>
                                                    @endif
                                                    @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                            App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                                        @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                                App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                            @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                                <a class="fs-8 d-flex gap-1 align-items-center mb-1"
                                                                    onclick="showreviews('{{ $item->id }}')"
                                                                    role="button" aria-controls="offcanvasExample">
                                                                    <i class="fa-solid fa-star text-warning"></i>
                                                                    <p class="cursor-pointer color-changer fw-600 fs-8">
                                                                        {{ number_format($item->avg_ratting, 1) }}</p>
                                                                </a>
                                                            @endif
                                                        @endif
                                                    @endif
                                                </div>
                                                <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                                    class="title color-changer pb-1">
                                                    {{ $item->item_name }}</a>
                                                <div class="products-price mb-1 d-flex flex-wrap gap-1 align-items-center">
                                                    <span
                                                        class="price color-changer">{{ helper::currency_formate($price, @$storeinfo->id) }}</span>
                                                    @if ($item->item_original_price != null)
                                                        @if ($original_price > $price)
                                                            <del class="text-muted">{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                        @endif
                                                    @endif
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="load showload-{{ $item->id }}" style="display:none">
                                                    </div>
                                                    <button
                                                        class="theme-2-product-icon btn border-0 addcartbtn-{{ $item->id }}"
                                                        type="button"
                                                        onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                        <i class="fa-solid fa-cart-shopping"></i>
                                                    </button>
                                                    @if (Auth::user() && Auth::user()->type == 3)
                                                        <div class="set-fav1-{{ $item->id }}">

                                                            @if ($item->is_favorite == 1)
                                                                <a class="theme-2-product-icon" href="javascript:void(0)"
                                                                    onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                                    <i class="fa-solid fa-heart"></i>
                                                                </a>
                                                            @else
                                                                <a class="theme-2-product-icon" href="javascript:void(0)"
                                                                    onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                                    <i class="fa-regular fa-heart"></i>
                                                                </a>
                                                            @endif

                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 3)
                    <div class="row g-3 pt-4">
                        @foreach ($topdealsproducts as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price >
                                                @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price =
                                                    $item->item_price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item->item_price;
                                            }
                                        }
                                    } else {
                                        if ($item['variation']->count() > 0) {
                                            $price =
                                                $item['variation'][0]->price -
                                                $item['variation'][0]->price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        } else {
                                            $price =
                                                $item->item_price -
                                                $item->item_price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        }
                                    }
                                    if ($item['variation']->count() > 0) {
                                        $original_price = $item['variation'][0]->price;
                                    } else {
                                        $original_price = $item->item_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                } else {
                                    if ($item['variation']->count() > 0) {
                                        $price = $item['variation'][0]->price;
                                        $original_price = $item['variation'][0]->original_price;
                                    } else {
                                        $price = $item->item_price;
                                        $original_price = $item->item_original_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                }
                                $item_name = str_replace("'", "\'", $item->item_name);
                            @endphp
                            <div class="col-lg-4 col-md-6 col-12">
                                <div class="card card-bg thme3categories dark">
                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                        <img src="{{ helper::image_path(@$item['item_image']->image) }}"
                                            class="card-img-top border" alt="...">
                                    </a>
                                    <div class="card-body {{ session()->get('direction') == 2 ? 'ps-0' : 'pe-0' }}">
                                        <div class="text-section">
                                            <div class="d-flex justify-content-between flex-wrap align-items-center mb-1">
                                                @if ($off > 0)
                                                    <span
                                                        class="p-1 px-2 rounded fw-500 text-bg-primary fs-8">{{ $off }}%
                                                        {{ trans('labels.off') }}</span>
                                                @endif
                                                @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                        App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                                    @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                            App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                        @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                            <a class="fs-8 d-flex gap-1 align-items-center mb-1"
                                                                onclick="showreviews('{{ $item->id }}')"
                                                                role="button" aria-controls="offcanvasExample">
                                                                <i class="fa-solid fa-star text-warning"></i>
                                                                <p class="cursor-pointer color-changer fw-600 fs-8">
                                                                    {{ number_format($item->avg_ratting, 1) }}</p>
                                                            </a>
                                                        @endif
                                                    @endif
                                                @endif
                                            </div>
                                            <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                                class="title color-changer pb-1">
                                                {{ $item->item_name }}</a>
                                            <div class="d-flex align-items-baseline">
                                                <div class="products-price">
                                                    <span
                                                        class="price color-changer">{{ helper::currency_formate($price, @$storeinfo->id) }}</span>
                                                    @if ($item->item_original_price != null)
                                                        @if ($original_price > $price)
                                                            <del class="text-muted">{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center pt-1">
                                                <div class="load showload-{{ $item->id }}" style="display:none">
                                                </div>
                                                <a class="theme-3-product-icon addcartbtn-{{ $item->id }}"
                                                    href="javascript:void(0)"
                                                    onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                    <i class="fa-solid fa-plus"></i>
                                                </a>

                                                @if (Auth::user() && Auth::user()->type == 3)
                                                    <div class="favorite-icon1 p-0 set-fav1-{{ $item->id }}">
                                                        @if ($item->is_favorite == 1)
                                                            <a href="javascript:void(0)"
                                                                onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                    class="fa-solid fa-heart"></i></a>
                                                        @else
                                                            <a href="javascript:void(0)"
                                                                onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                    class="fa-regular fa-heart"></i></a>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 4)
                    <div class="row g-3 theme-4-listproduct-card pt-4">
                        @foreach ($topdealsproducts as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price >
                                                @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price =
                                                    $item->item_price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item->item_price;
                                            }
                                        }
                                    } else {
                                        if ($item['variation']->count() > 0) {
                                            $price =
                                                $item['variation'][0]->price -
                                                $item['variation'][0]->price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        } else {
                                            $price =
                                                $item->item_price -
                                                $item->item_price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        }
                                    }
                                    if ($item['variation']->count() > 0) {
                                        $original_price = $item['variation'][0]->price;
                                    } else {
                                        $original_price = $item->item_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                } else {
                                    if ($item['variation']->count() > 0) {
                                        $price = $item['variation'][0]->price;
                                        $original_price = $item['variation'][0]->original_price;
                                    } else {
                                        $price = $item->item_price;
                                        $original_price = $item->item_original_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                }
                                $item_name = str_replace("'", "\'", $item->item_name);
                            @endphp
                            <div class="col-md-6 col-lg-6 col-xl-4 px-0">
                                <div class="card card-bg thme4categories dark">
                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                        <img src="{{ helper::image_path(@$item['item_image']->image) }}"
                                            class="card-img-top" alt="...">
                                    </a>
                                    <div class="card-body {{ session()->get('direction') == 2 ? 'pe-2' : 'ps-2' }}">
                                        <div class="text-section">
                                            <div class="d-flex justify-content-between flex-wrap align-items-center mb-1">
                                                @if ($off > 0)
                                                    <span
                                                        class="p-1 px-2 rounded fw-500 text-bg-secondary fs-8">{{ $off }}%
                                                        {{ trans('labels.off') }}</span>
                                                @endif
                                                @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                        App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                                    @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                            App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                        @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                            <a class="fs-8 d-flex gap-1 align-items-center mb-1"
                                                                onclick="showreviews('{{ $item->id }}')"
                                                                role="button" aria-controls="offcanvasExample">
                                                                <i class="fa-solid fa-star text-warning"></i>
                                                                <p class="cursor-pointer color-changer fw-600 fs-8">
                                                                    {{ number_format($item->avg_ratting, 1) }}</p>
                                                            </a>
                                                        @endif
                                                    @endif
                                                @endif
                                            </div>
                                            <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                                class="title color-changer pb-1">
                                                {{ $item->item_name }}</a>
                                            <div class="products-price mb-1 d-flex flex-wrap gap-1 align-items-center">
                                                <span class="price color-changer">
                                                    {{ helper::currency_formate($price, @$storeinfo->id) }}</span>
                                                @if ($item->item_original_price != null)
                                                    @if ($original_price > $price)
                                                        <del class="text-muted">{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between gap-2">

                                                <div class="load showload-{{ $item->id }}" style="display:none">
                                                </div>
                                                <a type="button" class="addcartbtn-{{ $item->id }} color-changer"
                                                    onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                    <i class="fa-solid fa-circle-plus fs-4"></i>
                                                </a>

                                                @if (Auth::user() && Auth::user()->type == 3)
                                                    <div class="favorite-icon1 px-0 set-fav1-{{ $item->id }}">
                                                        @if ($item->is_favorite == 1)
                                                            <a href="javascript:void(0)"
                                                                onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                    class="fa-solid fa-heart fs-4"></i></a>
                                                        @else
                                                            <a href="javascript:void(0)"
                                                                onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                    class="fa-regular fa-heart fs-4"></i></a>
                                                        @endif
                                                    </div>
                                                @endif


                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 5)
                    <div class="row g-3 pt-4">
                        @foreach ($topdealsproducts as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price >
                                                @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price =
                                                    $item->item_price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item->item_price;
                                            }
                                        }
                                    } else {
                                        if ($item['variation']->count() > 0) {
                                            $price =
                                                $item['variation'][0]->price -
                                                $item['variation'][0]->price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        } else {
                                            $price =
                                                $item->item_price -
                                                $item->item_price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        }
                                    }
                                    if ($item['variation']->count() > 0) {
                                        $original_price = $item['variation'][0]->price;
                                    } else {
                                        $original_price = $item->item_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                } else {
                                    if ($item['variation']->count() > 0) {
                                        $price = $item['variation'][0]->price;
                                        $original_price = $item['variation'][0]->original_price;
                                    } else {
                                        $price = $item->item_price;
                                        $original_price = $item->item_original_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                }
                                $item_name = str_replace("'", "\'", $item->item_name);
                            @endphp
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="card card-bg thme3categories dark">
                                    <div class="card-body {{ session()->get('direction') == 2 ? 'pe-0' : 'ps-0' }}">
                                        <div class="text-section">
                                            <div class="d-flex justify-content-between flex-wrap align-items-center mb-1">
                                                @if ($off > 0)
                                                    <span
                                                        class="p-1 px-2 rounded fw-500 text-bg-primary fs-8">{{ $off }}%
                                                        {{ trans('labels.off') }}</span>
                                                @endif
                                                @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                        App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                                    @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                            App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                        @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                            <a class="fs-8 d-flex gap-1 align-items-center"
                                                                onclick="showreviews('{{ $item->id }}')"
                                                                role="button" aria-controls="offcanvasExample">
                                                                <i class="fa-solid fa-star text-warning"></i>
                                                                <p class="cursor-pointer color-changer fw-600 fs-8">
                                                                    {{ number_format($item->avg_ratting, 1) }}</p>
                                                            </a>
                                                        @endif
                                                    @endif
                                                @endif
                                            </div>
                                            <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                                class="title pb-1 color-changer">
                                                {{ $item->item_name }}</a>
                                            <div class="products-price d-flex flex-wrap gap-1 align-items-center">
                                                <span
                                                    class="price color-changer">{{ helper::currency_formate($price, @$storeinfo->id) }}</span>
                                                @if ($item->item_original_price != null)
                                                    @if ($original_price > $price)
                                                        <del class="text-muted">{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between pt-1">

                                                <div class="load showload-{{ $item->id }}" style="display:none">
                                                </div>
                                                <a class="theme-3-product-icon addcartbtn-{{ $item->id }}"
                                                    href="javascript:void(0)"
                                                    onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                    <i class="fa-solid fa-plus"></i>
                                                </a>

                                                @if (Auth::user() && Auth::user()->type == 3)
                                                    <div class="favorite-icon1 p-0 set-fav1-{{ $item->id }}">
                                                        @if ($item->is_favorite == 1)
                                                            <a href="javascript:void(0)"
                                                                onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                    class="fa-solid fa-heart"></i></a>
                                                        @else
                                                            <a href="javascript:void(0)"
                                                                onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                    class="fa-regular fa-heart"></i></a>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                        <img src="{{ helper::image_path(@$item['item_image']->image) }}"
                                            class="card-img-top border" alt="...">
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 6)
                    <div class="row g-3 pt-4 theme-6-margin-top">
                        @foreach ($topdealsproducts as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price >
                                                @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price =
                                                    $item->item_price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item->item_price;
                                            }
                                        }
                                    } else {
                                        if ($item['variation']->count() > 0) {
                                            $price =
                                                $item['variation'][0]->price -
                                                $item['variation'][0]->price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        } else {
                                            $price =
                                                $item->item_price -
                                                $item->item_price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        }
                                    }
                                    if ($item['variation']->count() > 0) {
                                        $original_price = $item['variation'][0]->price;
                                    } else {
                                        $original_price = $item->item_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                } else {
                                    if ($item['variation']->count() > 0) {
                                        $price = $item['variation'][0]->price;
                                        $original_price = $item['variation'][0]->original_price;
                                    } else {
                                        $price = $item->item_price;
                                        $original_price = $item->item_original_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                }
                                $item_name = str_replace("'", "\'", $item->item_name);
                            @endphp
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="card thme6categories dark h-100 border-0 bg-light rounded-none">
                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                        <img src="{{ helper::image_path(@$item['item_image']->image) }}"
                                            class="card-img-top" alt="...">
                                    </a>
                                    <div class="card-body p-2">
                                        <div class="text-section">
                                            <div class="d-flex justify-content-between flex-wrap align-items-center mb-1">
                                                @if ($off > 0)
                                                    <span
                                                        class="p-1 px-2 rounded fw-500 text-bg-secondary fs-8">{{ $off }}%
                                                        {{ trans('labels.off') }}</span>
                                                @endif
                                                @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                        App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                                    @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                            App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                        @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                            <a class="fs-8 d-flex gap-1 align-items-center"
                                                                onclick="showreviews('{{ $item->id }}')"
                                                                role="button" aria-controls="offcanvasExample">
                                                                <i class="fa-solid fa-star text-warning"></i>
                                                                <p class="cursor-pointer color-changer fw-600 fs-8">
                                                                    {{ number_format($item->avg_ratting, 1) }}</p>
                                                            </a>
                                                        @endif
                                                    @endif
                                                @endif
                                            </div>
                                            <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                                class="title pb-1 color-changer hover-white line-limit-1">
                                                {{ $item->item_name }}</a>
                                            <div class="d-flex align-items-baseline">
                                                <div class="products-price d-flex flex-wrap gap-1 align-items-center">
                                                    <span class="price color-changer">
                                                        {{ helper::currency_formate($price, @$storeinfo->id) }}</span>
                                                    @if ($item->item_original_price != null)
                                                        @if ($original_price > $price)
                                                            <del
                                                                class="hover-white">{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="d-flex gap-2 align-items-center mt-1">
                                                <div class="load showload-{{ $item->id }}" style="display:none">
                                                </div>
                                                <button
                                                    class="product-cart-icon m-0 rounded-none addcartbtn-{{ $item->id }}"
                                                    type="button"
                                                    onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                    <i class="fa-solid fa-cart-shopping"></i>
                                                </button>
                                                @if (Auth::user() && Auth::user()->type == 3)
                                                    <div
                                                        class="favorite-icon1 d-flex align-items-center rounded-none m-0 product-cart-icon set-fav1-{{ $item->id }}">
                                                        @if ($item->is_favorite == 1)
                                                            <a href="javascript:void(0)" class="text-dark"
                                                                onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                    class="fa-solid fa-heart"></i></a>
                                                        @else
                                                            <a href="javascript:void(0)" class="text-dark"
                                                                onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                    class="fa-regular fa-heart"></i></a>
                                                        @endif
                                                    </div>
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 7)
                    <div class="row g-3 pt-4 theme-7-margin-top">
                        @foreach ($topdealsproducts as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price >
                                                @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price =
                                                    $item->item_price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item->item_price;
                                            }
                                        }
                                    } else {
                                        if ($item['variation']->count() > 0) {
                                            $price =
                                                $item['variation'][0]->price -
                                                $item['variation'][0]->price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        } else {
                                            $price =
                                                $item->item_price -
                                                $item->item_price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        }
                                    }
                                    if ($item['variation']->count() > 0) {
                                        $original_price = $item['variation'][0]->price;
                                    } else {
                                        $original_price = $item->item_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                } else {
                                    if ($item['variation']->count() > 0) {
                                        $price = $item['variation'][0]->price;
                                        $original_price = $item['variation'][0]->original_price;
                                    } else {
                                        $price = $item->item_price;
                                        $original_price = $item->item_original_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                }
                                $item_name = str_replace("'", "\'", $item->item_name);
                            @endphp
                            <div class="col-lg-6 px-sm-2 px-1">
                                <div class="card thme7categories dark h-100 rounded border-0 ">
                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                        <img src="{{ helper::image_path(@$item['item_image']->image) }}"
                                            class="card-img-top m-0 bg-light rounded-0" alt="...">
                                    </a>
                                    <div class="card-body p-sm-3 p-2 d-flex align-items-center">
                                        <div class="text-section w-100">
                                            <div class="d-flex justify-content-between flex-wrap align-items-center mb-2">
                                                @if ($off > 0)
                                                    <span
                                                        class="p-1 px-2 rounded fw-500 text-bg-primary fs-8">{{ $off }}%
                                                        {{ trans('labels.off') }}</span>
                                                @endif
                                                @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                        App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                                    @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                            App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                        @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                            <a class="fs-8 d-flex gap-1 align-items-center"
                                                                onclick="showreviews('{{ $item->id }}')"
                                                                role="button" aria-controls="offcanvasExample">
                                                                <i class="fa-solid fa-star text-warning"></i>
                                                                <p class="cursor-pointer color-changer fw-600 fs-8">
                                                                    {{ number_format($item->avg_ratting, 1) }}</p>
                                                            </a>
                                                        @endif
                                                    @endif
                                                @endif
                                            </div>
                                            <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                                class="title pb-1 color-changer hover-white">
                                                {{ $item->item_name }}</a>
                                            <div class="products-price d-flex flex-wrap align-items-center gap-1">
                                                <span class="price color-changer">
                                                    {{ helper::currency_formate($price, @$storeinfo->id) }}</span>
                                                @if ($item->item_original_price != null)
                                                    @if ($original_price > $price)
                                                        <del
                                                            class="hover-white">{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="d-flex align-items-center mt-1">
                                                <ul class="card-option-two h-100 d-flex gap-2 m-0 mt-2">
                                                    <li tooltip="{{ trans('labels.addcart') }}"
                                                        onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')"
                                                        class="m-0 cursor-pointer">
                                                        <div class="load showload-{{ $item->id }}"
                                                            style="display:none"></div>
                                                        <button class="product-cart-icon addcartbtn-{{ $item->id }}"
                                                            type="button">
                                                            <i class="fa-solid fa-cart-shopping fs-7"></i>
                                                        </button>
                                                    </li>
                                                    @if (Auth::user() && Auth::user()->type == 3)
                                                        <li tooltip="Wishlist" class="m-0">
                                                            <div
                                                                class="product-cart-icon d-flex align-items-center justify-content-center set-fav1-{{ $item->id }}">
                                                                @if ($item->is_favorite == 1)
                                                                    <a href="javascript:void(0)"
                                                                        onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                            class="fa-solid fa-heart"></i></a>
                                                                @else
                                                                    <a href="javascript:void(0)"
                                                                        onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                            class="fa-regular fa-heart"></i></a>
                                                                @endif
                                                            </div>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 8)
                    <div class="row g-3 pt-4  theme-8-margin-top">
                        @foreach ($topdealsproducts as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price >
                                                @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price =
                                                    $item->item_price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item->item_price;
                                            }
                                        }
                                    } else {
                                        if ($item['variation']->count() > 0) {
                                            $price =
                                                $item['variation'][0]->price -
                                                $item['variation'][0]->price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        } else {
                                            $price =
                                                $item->item_price -
                                                $item->item_price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        }
                                    }
                                    if ($item['variation']->count() > 0) {
                                        $original_price = $item['variation'][0]->price;
                                    } else {
                                        $original_price = $item->item_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                } else {
                                    if ($item['variation']->count() > 0) {
                                        $price = $item['variation'][0]->price;
                                        $original_price = $item['variation'][0]->original_price;
                                    } else {
                                        $price = $item->item_price;
                                        $original_price = $item->item_original_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                }
                                $item_name = str_replace("'", "\'", $item->item_name);
                            @endphp
                            <div class="col-xl-4 col-md-6 px-sm-2 px-1">
                                <div class="card thme8categories dark h-100 border-0 ">
                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                        <img src="{{ helper::image_path(@$item['item_image']->image) }}"
                                            class="card-img-top m-0" alt="...">
                                    </a>
                                    <div class="card-body p-sm-3 p-2">
                                        <div class="text-section">
                                            <div class="d-flex justify-content-between flex-wrap align-items-center mb-2">
                                                @if ($off > 0)
                                                    <span
                                                        class="p-1 px-2 rounded fw-500 text-bg-primary fs-8">{{ $off }}%
                                                        {{ trans('labels.off') }}</span>
                                                @endif
                                                @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                        App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                                    @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                            App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                        @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                            <a class="fs-8 d-flex gap-1 align-items-center"
                                                                onclick="showreviews('{{ $item->id }}')"
                                                                role="button" aria-controls="offcanvasExample">
                                                                <i class="fa-solid fa-star text-warning"></i>
                                                                <p class="cursor-pointer color-changer fw-600 fs-8">
                                                                    {{ number_format($item->avg_ratting, 1) }}</p>
                                                            </a>
                                                        @endif
                                                    @endif
                                                @endif
                                            </div>
                                            <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                                class="title fs-6 pb-1 color-changer hover-white">
                                                {{ $item->item_name }}</a>
                                            <div class="products-price d-flex align-items-center gap-1 flex-wrap mb-2">
                                                <span class="price color-changer fw-bold">
                                                    {{ helper::currency_formate($price, @$storeinfo->id) }}</span>
                                                @if ($item->item_original_price != null)
                                                    @if ($original_price > $price)
                                                        <del
                                                            class="hover-white text-muted">{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="d-flex align-items-center mt-1">
                                                <ul class="card-option-three d-flex m-0 h-100">
                                                    <li tooltip="{{ trans('labels.addcart') }}"
                                                        onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')"
                                                        class="m-0 p-0 cursor-pointer">
                                                        <div class="load showload-{{ $item->id }}"
                                                            style="display:none"></div>
                                                        <button
                                                            class="product-cart-icon p-0 addcartbtn-{{ $item->id }}"
                                                            type="button">
                                                            <i class="fa-solid fa-cart-shopping fs-7"></i>
                                                        </button>
                                                    </li>
                                                    @if (Auth::user() && Auth::user()->type == 3)
                                                        <li tooltip="Wishlist" class="m-0 p-0">
                                                            <div class="set-fav-38">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-center set-fav1-{{ $item->id }}">
                                                                    @if ($item->is_favorite == 1)
                                                                        <a href="javascript:void(0)" class="d-flex"
                                                                            onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                                class="fa-solid fa-heart fs-7"></i></a>
                                                                    @else
                                                                        <a href="javascript:void(0)" class="d-flex"
                                                                            onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                                class="fa-regular fa-heart fs-7"></i></a>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 9)
                    <div class="row g-3 pt-4 theme-9-margin-top">
                        @foreach ($topdealsproducts as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price >
                                                @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price =
                                                    $item->item_price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item->item_price;
                                            }
                                        }
                                    } else {
                                        if ($item['variation']->count() > 0) {
                                            $price =
                                                $item['variation'][0]->price -
                                                $item['variation'][0]->price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        } else {
                                            $price =
                                                $item->item_price -
                                                $item->item_price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        }
                                    }
                                    if ($item['variation']->count() > 0) {
                                        $original_price = $item['variation'][0]->price;
                                    } else {
                                        $original_price = $item->item_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                } else {
                                    if ($item['variation']->count() > 0) {
                                        $price = $item['variation'][0]->price;
                                        $original_price = $item['variation'][0]->original_price;
                                    } else {
                                        $price = $item->item_price;
                                        $original_price = $item->item_original_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                }
                            @endphp
                            <div class="col-xl-4 col-lg-6 col-md-6 col-12 products-img">
                                <div class="card h-100 border-0 p-0 bg-light position-relative rounded-3">
                                    <div class="card-body">
                                        <div class="d-flex gap-sm-3 gap-2 align-items-center">
                                            <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                                <div class="theme9list_image">
                                                    <img src="{{ helper::image_path(@$item['item_image']->image) }}"
                                                        alt="" class="p-0 object-fit-cover">
                                                </div>
                                            </a>
                                            <div class="w-100">
                                                <div
                                                    class="d-flex justify-content-between flex-wrap align-items-center mb-2">
                                                    @if ($off > 0)
                                                        <span
                                                            class="p-1 px-2 rounded fw-500 text-bg-primary fs-8">{{ $off }}%
                                                            {{ trans('labels.off') }}</span>
                                                    @endif
                                                    @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                            App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                                        @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                                App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                            @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                                <a class="fs-8 d-flex gap-1 align-items-center"
                                                                    onclick="showreviews('{{ $item->id }}')"
                                                                    role="button" aria-controls="offcanvasExample">
                                                                    <i class="fa-solid fa-star text-warning"></i>
                                                                    <p class="cursor-pointer color-changer fw-600 fs-8">
                                                                        {{ number_format($item->avg_ratting, 1) }}
                                                                    </p>
                                                                </a>
                                                            @endif
                                                        @endif
                                                    @endif
                                                </div>
                                                <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                                    class="title pb-1 color-changer hover-white line-limit-2">
                                                    {{ $item->item_name }}</a>
                                                <div
                                                    class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                                        <p class="price color-changer">
                                                            {{ helper::currency_formate($price, @$storeinfo->id) }}
                                                        </p>
                                                        @if ($item->item_original_price != null)
                                                            @if ($original_price > $price)
                                                                <del class="hover-white text-muted">
                                                                    {{ helper::currency_formate($original_price, @$storeinfo->id) }}
                                                                </del>
                                                            @endif
                                                        @endif
                                                    </div>
                                                    @if (Auth::user() && Auth::user()->type == 3)
                                                        <div class="theme-9-favorite-icon set-fav1-{{ $item->id }}">
                                                            @if ($item->is_favorite == 1)
                                                                <a href="javascript:void(0)"
                                                                    onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                        class="fa-solid fa-heart"></i></a>
                                                            @else
                                                                <a href="javascript:void(0)"
                                                                    onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                        class="fa-regular fa-heart"></i></a>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 10)
                    <div class="row g-3 pt-4">
                        @foreach ($topdealsproducts as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price >
                                                @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price =
                                                    $item->item_price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item->item_price;
                                            }
                                        }
                                    } else {
                                        if ($item['variation']->count() > 0) {
                                            $price =
                                                $item['variation'][0]->price -
                                                $item['variation'][0]->price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        } else {
                                            $price =
                                                $item->item_price -
                                                $item->item_price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        }
                                    }
                                    if ($item['variation']->count() > 0) {
                                        $original_price = $item['variation'][0]->price;
                                    } else {
                                        $original_price = $item->item_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                } else {
                                    if ($item['variation']->count() > 0) {
                                        $price = $item['variation'][0]->price;
                                        $original_price = $item['variation'][0]->original_price;
                                    } else {
                                        $price = $item->item_price;
                                        $original_price = $item->item_original_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                }
                                $item_name = str_replace("'", "\'", $item->item_name);
                            @endphp
                            <div class="col-xxl-4 col-md-6">
                                <div class="card thme10categories bg-transparent dark h-100 rounded border-secondary">
                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                        <img src="{{ helper::image_path(@$item['item_image']->image) }}"
                                            class="card-img-top m-0" alt="...">
                                    </a>
                                    <div class="card-body py-2 px-2">
                                        <div class="text-section">
                                            <div class="d-flex justify-content-between flex-wrap align-items-center mb-2">
                                                @if ($off > 0)
                                                    <span
                                                        class="p-1 px-2 rounded fw-500 text-bg-primary fs-8">{{ $off }}%
                                                        {{ trans('labels.off') }}</span>
                                                @endif
                                                @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                        App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                                    @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                            App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                        @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                            <a class="fs-8 d-flex gap-1 align-items-center"
                                                                onclick="showreviews('{{ $item->id }}')"
                                                                role="button" aria-controls="offcanvasExample">
                                                                <i class="fa-solid fa-star text-warning"></i>
                                                                <p class="cursor-pointer color-changer fw-600 fs-8">
                                                                    {{ number_format($item->avg_ratting, 1) }}</p>
                                                            </a>
                                                        @endif
                                                    @endif
                                                @endif
                                            </div>
                                            <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                                class="title line-limit-1 color-changer pb-1 px-1 hover-white">
                                                {{ $item->item_name }}</a>
                                            <div class="products-price d-flex align-items-center flex-wrap gap-1 mb-sm-1">
                                                <span class="price color-changer">
                                                    {{ helper::currency_formate($price, @$storeinfo->id) }}</span>
                                                @if ($item->item_original_price != null)
                                                    @if ($original_price > $price)
                                                        <del
                                                            class="hover-white">{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="mt-1">
                                                @if ($item->has_variants == 1)
                                                    <ul class="row m-0 g-1">
                                                        <li class="col-lg-9 col-8">
                                                            <div class="load showload-{{ $item->id }}"
                                                                style="display:none"></div>
                                                            <button
                                                                class="theme-10-product-cart-icon px-2 addcartbtn-{{ $item->id }}"
                                                                type="button"
                                                                onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                                {{ trans('labels.addcart') }}
                                                            </button>

                                                        </li>
                                                        @if (Auth::user() && Auth::user()->type == 3)
                                                            <li class="col-auto">
                                                                <div
                                                                    class="theme-10-product-heart-icon set-fav1-{{ $item->id }}">
                                                                    @if ($item->is_favorite == 1)
                                                                        <a href="javascript:void(0)"
                                                                            onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                                class="fa-solid fa-heart"></i></a>
                                                                    @else
                                                                        <a href="javascript:void(0)"
                                                                            onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                                class="fa-regular fa-heart"></i></a>
                                                                    @endif
                                                                </div>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                @else
                                                    <ul class="row m-0 g-1">
                                                        <li class="col-lg-9 col-8">

                                                            <div class="theme-10-load">
                                                                <div class="load-2 showload-{{ $item->id }}"
                                                                    style="display:none"></div>
                                                            </div>
                                                            <button
                                                                class="theme-10-product-cart-icon px-2 addcartbtn-{{ $item->id }}"
                                                                type="button"
                                                                onclick="showitems('{{ $item->id }}','{{ $item->item_name }}','{{ $item->item_price }}')">
                                                                {{ trans('labels.addcart') }}
                                                                {{-- <i class="fa-solid fa-angle-right {{ session()->get('direction') == 2 ? 'pe-1' : 'ps-1' }}"></i> --}}
                                                            </button>

                                                        </li>
                                                        @if (Auth::user() && Auth::user()->type == 3)
                                                            <li class="col-auto">
                                                                <div
                                                                    class="theme-10-product-heart-icon set-fav1-{{ $item->id }}">
                                                                    @if ($item->is_favorite == 1)
                                                                        <a href="javascript:void(0)"
                                                                            onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                                class="fa-solid fa-heart"></i></a>
                                                                    @else
                                                                        <a href="javascript:void(0)"
                                                                            onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                                class="fa-regular fa-heart"></i></a>
                                                                    @endif
                                                                </div>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 11)
                    <div class="row row-cols-xl-3 row-cols-lg-2 row-cols-md-2 row-cols-1 g-3 pt-4">
                        @foreach ($topdealsproducts as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price >
                                                @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price =
                                                    $item->item_price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item->item_price;
                                            }
                                        }
                                    } else {
                                        if ($item['variation']->count() > 0) {
                                            $price =
                                                $item['variation'][0]->price -
                                                $item['variation'][0]->price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        } else {
                                            $price =
                                                $item->item_price -
                                                $item->item_price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        }
                                    }
                                    if ($item['variation']->count() > 0) {
                                        $original_price = $item['variation'][0]->price;
                                    } else {
                                        $original_price = $item->item_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                } else {
                                    if ($item['variation']->count() > 0) {
                                        $price = $item['variation'][0]->price;
                                        $original_price = $item['variation'][0]->original_price;
                                    } else {
                                        $price = $item->item_price;
                                        $original_price = $item->item_original_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                }
                                $item_name = str_replace("'", "\'", $item->item_name);
                            @endphp
                            <div class="col theme-11-list">
                                <div class="product-grid card rounded-3 h-100">
                                    <div class="d-flex h-100">
                                        <div class="product-image position-relative">
                                            <a class="image h-100">
                                                <img class="pic-1"
                                                    src="{{ @helper::image_path($item['multi_image'][0]->image) }}"
                                                    class="img-fluid" alt="...">
                                                <img class="pic-2"
                                                    src="{{ @$item['multi_image']->count() > 1 ? @helper::image_path($item['multi_image'][1]->image) : @helper::image_path($item['multi_image'][0]->image) }}">
                                            </a>
                                            <span class="product-discount-label">

                                            </span>
                                            @if ($off > 0)
                                                <span class="theme-11-ribbon py-1">
                                                    <h3>{{ $off }}% {{ trans('labels.off') }}</h3>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="h-100 w-100 d-flex gap-2 flex-column justify-content-center">
                                            <div
                                                class="card-body w-100 p-2 product-content d-flex gap-2 flex-column justify-content-center">
                                                <div class="d-flex justify-content-between gap-1 w-100">
                                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                                        class="fs-7 fw-500 color-changer line-2">
                                                        {{ $item->item_name }}
                                                    </a>
                                                    @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                            App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                                        @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                                App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                            @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                                <a class="fs-8 d-flex gap-1 align-items-center p-1"
                                                                    onclick="showreviews('{{ $item->id }}')"
                                                                    role="button" aria-controls="offcanvasExample">
                                                                    <i class="fa-solid fa-star text-warning"></i>
                                                                    <p class="cursor-pointer color-changer fw-600 fs-8">
                                                                        {{ number_format($item->avg_ratting, 1) }}</p>
                                                                </a>
                                                            @endif
                                                        @endif
                                                    @endif
                                                </div>
                                                <div class="products-price flex-wrap d-flex gap-1 align-items-center">
                                                    <span class="fs-15 fw-600 text-primary color-changer">
                                                        {{ helper::currency_formate($price, @$storeinfo->id) }}
                                                    </span>
                                                    @if ($item->item_original_price != null)
                                                        @if ($original_price > $price)
                                                            <del>{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="card-footer p-2">
                                                <ul class="d-flex gap-1 m-0 ">
                                                    @if (Auth::user() && Auth::user()->type == 3)
                                                        <li class="set-fav1-{{ $item->id }}">
                                                            @if ($item->is_favorite == 1)
                                                                <a class="theme-2-product-icon"
                                                                    href="javascript:void(0)"
                                                                    onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                                    <i class="fa-solid fa-heart"></i>
                                                                </a>
                                                            @else
                                                                <a class="theme-2-product-icon"
                                                                    href="javascript:void(0)"
                                                                    onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                                    <i class="fa-regular fa-heart"></i>
                                                                </a>
                                                            @endif
                                                        </li>
                                                    @endif
                                                    <li>
                                                        <a
                                                            href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                                            <i class="fa-regular fa-eye"></i>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a type="button"
                                                            onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                            <div class="load showload-{{ $item->id }}"
                                                                style="display:none">
                                                            </div>
                                                            <i
                                                                class="fa-solid fa-cart-shopping addcartbtn-{{ $item->id }}"></i>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 12)
                    <div class="row g-3 pt-4 row-cols-xl-3 row-cols-lg-2 row-cols-md-2 row-cols-1">
                        @foreach ($topdealsproducts as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price >
                                                @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price =
                                                    $item->item_price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item->item_price;
                                            }
                                        }
                                    } else {
                                        if ($item['variation']->count() > 0) {
                                            $price =
                                                $item['variation'][0]->price -
                                                $item['variation'][0]->price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        } else {
                                            $price =
                                                $item->item_price -
                                                $item->item_price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        }
                                    }
                                    if ($item['variation']->count() > 0) {
                                        $original_price = $item['variation'][0]->price;
                                    } else {
                                        $original_price = $item->item_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                } else {
                                    if ($item['variation']->count() > 0) {
                                        $price = $item['variation'][0]->price;
                                        $original_price = $item['variation'][0]->original_price;
                                    } else {
                                        $price = $item->item_price;
                                        $original_price = $item->item_original_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                }
                                $item_name = str_replace("'", "\'", $item->item_name);
                            @endphp
                            <div class="col theme-12-list">
                                <div class="product-grid card shadow overflow-visible border-0 rounded-5 h-100">
                                    <div class="d-flex h-100">
                                        <div class="product-image position-relative">
                                            <a class="image h-100">
                                                <img class="pic-1"
                                                    src="{{ @helper::image_path($item['multi_image'][0]->image) }}"
                                                    class="img-fluid" alt="...">
                                                <img class="pic-2"
                                                    src="{{ @$item['multi_image']->count() > 1 ? @helper::image_path($item['multi_image'][1]->image) : @helper::image_path($item['multi_image'][0]->image) }}">
                                            </a>
                                            @if ($off > 0)
                                                <span class="product-hot-label">
                                                    {{ $off }}% {{ trans('labels.off') }}
                                                </span>
                                            @endif

                                        </div>
                                        <div class="h-100 w-100 d-flex gap-2 flex-column justify-content-center">
                                            <div
                                                class="card-body w-100 p-3 pb-0 product-content d-flex gap-2 flex-column justify-content-center">
                                                <div class="d-flex justify-content-between gap-1 w-100">
                                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                                        class="fs-7 fw-500 color-changer line-2">
                                                        {{ $item->item_name }}
                                                    </a>
                                                    @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                            App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                                        @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                                App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                            @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                                <a class="fs-8 d-flex gap-1 align-items-center p-1"
                                                                    onclick="showreviews('{{ $item->id }}')"
                                                                    role="button" aria-controls="offcanvasExample">
                                                                    <i class="fa-solid fa-star text-warning"></i>
                                                                    <p class="cursor-pointer color-changer fw-600 fs-8">
                                                                        {{ number_format($item->avg_ratting, 1) }}</p>
                                                                </a>
                                                            @endif
                                                        @endif
                                                    @endif
                                                </div>
                                                <div class="products-price flex-wrap d-flex gap-1 align-items-center">
                                                    <span class="fs-15 color-changer fw-600 text-primary">
                                                        {{ helper::currency_formate($price, @$storeinfo->id) }}
                                                    </span>
                                                    @if ($item->item_original_price != null)
                                                        @if ($original_price > $price)
                                                            <del>{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="card-footer p-3">
                                                <ul class="d-flex gap-1 m-0 ">
                                                    @if (Auth::user() && Auth::user()->type == 3)
                                                        <li class="set-fav1-{{ $item->id }}">
                                                            @if ($item->is_favorite == 1)
                                                                <a class="theme-2-product-icon"
                                                                    href="javascript:void(0)"
                                                                    onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                                    <i class="fa-solid fa-heart"></i>
                                                                </a>
                                                            @else
                                                                <a class="theme-2-product-icon"
                                                                    href="javascript:void(0)"
                                                                    onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                                    <i class="fa-regular fa-heart"></i>
                                                                </a>
                                                            @endif
                                                        </li>
                                                    @endif
                                                    <li>
                                                        <a
                                                            href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                                            <i class="fa-regular fa-eye"></i>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a type="button"
                                                            onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                            <div class="load showload-{{ $item->id }}"
                                                                style="display:none">
                                                            </div>
                                                            <i
                                                                class="fa-solid fa-cart-shopping addcartbtn-{{ $item->id }}"></i>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 13)
                    <div class="row g-3 pt-4 row-cols-xl-2 row-cols-lg-2 row-cols-md-2 row-cols-1">
                        @foreach ($topdealsproducts as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price >
                                                @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price =
                                                    $item->item_price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item->item_price;
                                            }
                                        }
                                    } else {
                                        if ($item['variation']->count() > 0) {
                                            $price =
                                                $item['variation'][0]->price -
                                                $item['variation'][0]->price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        } else {
                                            $price =
                                                $item->item_price -
                                                $item->item_price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        }
                                    }
                                    if ($item['variation']->count() > 0) {
                                        $original_price = $item['variation'][0]->price;
                                    } else {
                                        $original_price = $item->item_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                } else {
                                    if ($item['variation']->count() > 0) {
                                        $price = $item['variation'][0]->price;
                                        $original_price = $item['variation'][0]->original_price;
                                    } else {
                                        $price = $item->item_price;
                                        $original_price = $item->item_original_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                }
                                $item_name = str_replace("'", "\'", $item->item_name);
                            @endphp
                            <div class="col theme-list-13">
                                <div class="product-grid card h-100">
                                    <div class="d-flex">
                                        <div class="product-image">
                                            <a class="image">
                                                <img class="pic-1"
                                                    src="{{ @helper::image_path($item['multi_image'][0]->image) }}"
                                                    class="img-fluid" alt="...">
                                                <img class="pic-2"
                                                    src="{{ @$item['multi_image']->count() > 1 ? @helper::image_path($item['multi_image'][1]->image) : @helper::image_path($item['multi_image'][0]->image) }}">
                                            </a>
                                            <ul class="social">
                                                <li>
                                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                                        class="shadow">
                                                        <i class="fa-regular fa-eye"></i>
                                                    </a>
                                                </li>
                                                @if (Auth::user() && Auth::user()->type == 3)
                                                    <li class="set-fav1-{{ $item->id }}">
                                                        @if ($item->is_favorite == 1)
                                                            <a href="javascript:void(0)"
                                                                onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                                <i class="fa-solid fa-heart"></i>
                                                            </a>
                                                        @else
                                                            <a href="javascript:void(0)"
                                                                onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                                <i class="fa-regular fa-heart"></i>
                                                            </a>
                                                        @endif
                                                    </li>
                                                @endif
                                            </ul>
                                            <div
                                                class="product-rating flex-wrap d-flex justify-content-center gap-1 align-items-center">
                                                <a class="add-to-cart cursor-pointer"
                                                    onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                    <div class="load showload-{{ $item->id }}"
                                                        style="display:none">
                                                    </div>
                                                    <span
                                                        class="addcartbtn-{{ $item->id }}">{{ trans('labels.addcart') }}</span>
                                                </a>
                                            </div>
                                        </div>
                                        <div
                                            class="w-100 product-content bg-change-mode d-flex flex-column align-items-center justify-content-center h-100">
                                            <div class="p-2 w-100">
                                                @if ($off > 0)
                                                    <div class="mb-2">
                                                        <span
                                                            class="px-2 py-1 text-capitalize rounded-5 bg-primary fw-600 text-white fs-8">
                                                            {{ $off }}% {{ trans('labels.off') }}
                                                        </span>
                                                    </div>
                                                @endif
                                                <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                                    class="fs-7 fw-500 color-changer line-2">
                                                    {{ $item->item_name }}
                                                </a>
                                            </div>
                                            <div class="p-2 w-100">
                                                <div class="d-flex align-items-center justify-content-between gap-1">
                                                    <div class="products-price d-flex flex-wrap gap-1 align-items-center">
                                                        <span class="fs-15 fw-600 color-changer text-primary">
                                                            {{ helper::currency_formate($price, @$storeinfo->id) }}
                                                        </span>
                                                        @if ($item->item_original_price != null)
                                                            @if ($original_price > $price)
                                                                <del>{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                            @endif
                                                        @endif
                                                    </div>
                                                    @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                            App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                                        @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                                App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                            @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                                <a class="fs-8 text-dark d-flex gap-1 align-items-center"
                                                                    onclick="showreviews('{{ $item->id }}')"
                                                                    role="button" aria-controls="offcanvasExample">
                                                                    <i class="fa-solid fa-star text-warning"></i>
                                                                    <p class="cursor-pointer color-changer fw-600 fs-8">
                                                                        {{ number_format($item->avg_ratting, 1) }}</p>
                                                                </a>
                                                            @endif
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 14)
                    <div class="row g-3 pt-4">
                        @foreach ($topdealsproducts as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price >
                                                @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price =
                                                    $item->item_price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item->item_price;
                                            }
                                        }
                                    } else {
                                        if ($item['variation']->count() > 0) {
                                            $price =
                                                $item['variation'][0]->price -
                                                $item['variation'][0]->price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        } else {
                                            $price =
                                                $item->item_price -
                                                $item->item_price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        }
                                    }
                                    if ($item['variation']->count() > 0) {
                                        $original_price = $item['variation'][0]->price;
                                    } else {
                                        $original_price = $item->item_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                } else {
                                    if ($item['variation']->count() > 0) {
                                        $price = $item['variation'][0]->price;
                                        $original_price = $item['variation'][0]->original_price;
                                    } else {
                                        $price = $item->item_price;
                                        $original_price = $item->item_original_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                }
                                $item_name = str_replace("'", "\'", $item->item_name);
                            @endphp
                            <div class="col-md-6 col-xl-4 pointer theme-14">
                                <div class="card h-100 border rounded-4 thme3categories dark">
                                    <div class="card-body p-2">
                                        <div class="d-flex gap-3 align-items-center">
                                            <div class="text-section w-100">
                                                <div
                                                    class="d-flex justify-content-between flex-wrap align-items-center mb-1">
                                                    @if ($off > 0)
                                                        <span
                                                            class="p-1 px-2 rounded fw-500 text-bg-primary fs-8">{{ $off }}%
                                                            {{ trans('labels.off') }}</span>
                                                    @endif
                                                    @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                            App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                                        @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                                App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                            @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                                <a class="fs-8 d-flex gap-1 align-items-center"
                                                                    onclick="showreviews('{{ $item->id }}')"
                                                                    role="button" aria-controls="offcanvasExample">
                                                                    <i class="fa-solid fa-star text-warning"></i>
                                                                    <p class="cursor-pointer color-changer fw-600 fs-8">
                                                                        {{ number_format($item->avg_ratting, 1) }}</p>
                                                                </a>
                                                            @endif
                                                        @endif
                                                    @endif
                                                </div>
                                                <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                                    class="title color-changer pb-1">
                                                    {{ $item->item_name }}</a>
                                                <div class="products-price d-flex flex-wrap gap-1 align-items-center">
                                                    <span
                                                        class="price color-changer">{{ helper::currency_formate($price, @$storeinfo->id) }}</span>
                                                    @if ($item->item_original_price != null)
                                                        @if ($original_price > $price)
                                                            <del>{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                        @endif
                                                    @endif
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between pt-1">
                                                    <div class="load showload-{{ $item->id }}"
                                                        style="display:none">
                                                    </div>
                                                    <a class="add-to-cart addcartbtn-{{ $item->id }}"
                                                        href="javascript:void(0)"
                                                        onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                        <i class="fas fa-cart-plus"></i>
                                                    </a>

                                                    @if (Auth::user() && Auth::user()->type == 3)
                                                        <div class="hart-icon p-0 set-fav1-{{ $item->id }}">
                                                            @if ($item->is_favorite == 1)
                                                                <a href="javascript:void(0)" class="shadow text-danger"
                                                                    onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                        class="fa-solid fa-heart text-danger"></i></a>
                                                            @else
                                                                <a href="javascript:void(0)" class="shadow text-danger"
                                                                    onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')"><i
                                                                        class="fa-regular fa-heart text-danger  "></i></a>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                                <img src="{{ helper::image_path(@$item['item_image']->image) }}"
                                                    class="card-img-top border bg-primary-rgb" alt="...">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (helper::appdata($storeinfo->id)->template == 15)
                    <div class="row g-3 pt-4 row-cols-xl-3 row-cols-lg-2 row-cols-md-2 row-cols-1">
                        @foreach ($topdealsproducts as $item)
                            @php
                                if ($item->top_deals == 1 && helper::top_deals($vdata) != null) {
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price >
                                                @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price =
                                                    $item->item_price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item->item_price;
                                            }
                                        }
                                    } else {
                                        if ($item['variation']->count() > 0) {
                                            $price =
                                                $item['variation'][0]->price -
                                                $item['variation'][0]->price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        } else {
                                            $price =
                                                $item->item_price -
                                                $item->item_price *
                                                    (@helper::top_deals($vdata)->offer_amount / 100);
                                        }
                                    }
                                    if ($item['variation']->count() > 0) {
                                        $original_price = $item['variation'][0]->price;
                                    } else {
                                        $original_price = $item->item_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                } else {
                                    if ($item['variation']->count() > 0) {
                                        $price = $item['variation'][0]->price;
                                        $original_price = $item['variation'][0]->original_price;
                                    } else {
                                        $price = $item->item_price;
                                        $original_price = $item->item_original_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                }
                                $item_name = str_replace("'", "\'", $item->item_name);
                            @endphp
                            <div class="col theme-list-15">
                                <div class="product-grid card h-100">
                                    <div class="card-body">
                                        <div class="d-flex product-content p-0 gap-3 w-100 h-100">
                                            <div class="product-image">
                                                <a class="image">
                                                    <img class="pic-1 rounded"
                                                        src="{{ @helper::image_path($item['multi_image'][0]->image) }}"
                                                        class="img-fluid" alt="...">
                                                    <img class="pic-2 rounded"
                                                        src="{{ @$item['multi_image']->count() > 1 ? @helper::image_path($item['multi_image'][1]->image) : @helper::image_path($item['multi_image'][0]->image) }}">
                                                </a>
                                                @if ($off > 0)
                                                    <span class="product-discount-label">
                                                        {{ $off }}% {{ trans('labels.off') }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="w-100 d-flex flex-column justify-content-center">
                                                <div>
                                                    @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                                            App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                                        @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                                App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                            @if (helper::appdata($storeinfo->id)->checkout_login_required == 1)
                                                                <a class="fs-8 text-dark mb-2 d-flex gap-1 justify-content-between align-items-center"
                                                                    onclick="showreviews('{{ $item->id }}')"
                                                                    role="button" aria-controls="offcanvasExample">
                                                                    @php
                                                                        $count = (float) $item->avg_ratting;
                                                                        $fullStars = 0;
                                                                        if ($count > 4.5) {
                                                                            $fullStars =
                                                                                $count >= 4.75 ? 5 : floor($count);
                                                                        } elseif ($count > 3.5) {
                                                                            $fullStars =
                                                                                $count >= 3.75 ? 4 : floor($count);
                                                                        } elseif ($count > 2.5) {
                                                                            $fullStars =
                                                                                $count >= 2.75 ? 3 : floor($count);
                                                                        } elseif ($count > 1.5) {
                                                                            $fullStars =
                                                                                $count >= 1.75 ? 2 : floor($count);
                                                                        } elseif ($count > 0.5) {
                                                                            $fullStars =
                                                                                $count >= 0.75 ? 1 : floor($count);
                                                                        }
                                                                        $hasHalfStar =
                                                                            $count - $fullStars >= 0.5 &&
                                                                            $fullStars < 5;
                                                                    @endphp
                                                                    <ul class="d-flex gap-1 m-0">
                                                                        @for ($i = 0; $i < 5; $i++)
                                                                            @if ($i < $fullStars)
                                                                                <li class="list-inline-item me-0 fs-8">
                                                                                    <i
                                                                                        class="fa-solid fa-star text-warning"></i>
                                                                                </li>
                                                                            @elseif ($i == $fullStars && $hasHalfStar)
                                                                                <li class="list-inline-item me-0 fs-8">
                                                                                    <i
                                                                                        class="fa-solid fa-star-half-stroke text-warning"></i>
                                                                                </li>
                                                                            @else
                                                                                <li class="list-inline-item me-0 fs-8">
                                                                                    <i
                                                                                        class="fa-regular fa-star text-warning"></i>
                                                                                </li>
                                                                            @endif
                                                                        @endfor
                                                                    </ul>
                                                                    <p class="cursor-pointer color-changer fw-600 fs-8">
                                                                        {{ number_format($item->avg_ratting, 1) }}
                                                                    </p>
                                                                </a>
                                                            @endif
                                                        @endif
                                                    @endif
                                                    <h3 class="title border-bottom m-0">
                                                        <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                                            class="fs-7 color-changer fw-500 line-2">
                                                            {{ $item->item_name }}
                                                        </a>
                                                    </h3>
                                                </div>
                                                <div class="card-footer pt-2 p-0">
                                                    <div class="d-flex flex-wrap gap-1 align-items-center">
                                                        <span class="fs-15 color-changer fw-600 text-primary">
                                                            {{ helper::currency_formate($price, @$storeinfo->id) }}
                                                        </span>
                                                        @if ($item->item_original_price != null)
                                                            @if ($original_price > $price)
                                                                <del>{{ helper::currency_formate($original_price, @$storeinfo->id) }}</del>
                                                            @endif
                                                        @endif
                                                    </div>
                                                    <ul class="social d-flex gap-2 align-items-center mt-2">
                                                        @if (Auth::user() && Auth::user()->type == 3)
                                                            <li class="set-fav1-{{ $item->id }}">
                                                                @if ($item->is_favorite == 1)
                                                                    <a href="javascript:void(0)"
                                                                        onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                                        <i class="fa-solid fa-heart"></i>
                                                                    </a>
                                                                @else
                                                                    <a href="javascript:void(0)"
                                                                        onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',1,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                                        <i class="fa-regular fa-heart"></i>
                                                                    </a>
                                                                @endif
                                                            </li>
                                                        @endif
                                                        <li>
                                                            <a type="button"
                                                                onclick="showitems('{{ $item->id }}','{{ $item_name }}','{{ $item->item_price }}')">
                                                                <div class="load showload-{{ $item->id }}"
                                                                    style="display:none">
                                                                </div>
                                                                <i
                                                                    class="fa-solid fa-cart-shopping addcartbtn-{{ $item->id }}"></i>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a
                                                                href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                                                <i class="fa-regular fa-eye"></i>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="mt-2 d-flex justify-content-center">
                {{ $topdealsproducts->appends(request()->query())->links() }}
            </div>
        </div>
    </section>
    <!-- blog detail section end -->
    @include('front.sum_qusction')
@endsection

@section('script')
    <script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/custom/cart.js') }}" type="text/javascript"></script>
    <script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/custom/top_deals.js') }}"></script>
@endsection
