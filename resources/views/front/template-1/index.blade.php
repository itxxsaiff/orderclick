@extends('front.theme.default')
@section('content')
    <!-- banner Section Start -->
    @if (count(helper::footer_features(@$vdata)) > 0 ||
            (count($getcategory) > 0 && count($getitem) > 0) ||
            count($bannerimage) > 0 ||
            count($blogs) > 0)
        @if (helper::appdata($storeinfo->id)->banner != null)
            <section class="mt-0 position-relative">
                <div class="theme-1">
                    <img src="{{ helper::image_path(helper::appdata($storeinfo->id)->banner) }}" alt="">
                </div>
            </section>
        @endif
        <!-- banner Section End -->
        <!-- fhishar Section Start -->
        @if (count(helper::footer_features(@$vdata)) > 0)
            <section class="bg-light bg-change-mode py-3 mt-0">
                <div class="container">
                    <div class="row my-1 g-3 justify-content-center align-items-center overflow-hidden">
                        @foreach (helper::footer_features(@$vdata) as $feature)
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                                <div class="card footer-card">
                                    <div class="card-body d-flex gap-3 align-items-center">
                                        <div class="quality-icon">
                                            {!! $feature->icon !!}
                                        </div>
                                        <div class="quality-content">
                                            <h3 class="color-changer">{{ $feature->title }}</h3>
                                            <p class="fs-7">{{ $feature->description }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
        <!-- fhishar Section end -->
        <!-- Banner Slider Section Start -->
        @if (count($bannerimage) > 0)
            <section class="banner-slider-section">
                <div class="container">
                    <div class="row py-5">
                        <div class="col">
                            <div class="owl-carousel banner-imges-slider-2 owl-theme">
                                @foreach ($bannerimage as $image)
                                    <div class="item">
                                        <div class="overflow-hidden rounded">
                                            <img src="{{ helper::image_path($image->banner_image) }}" alt=""
                                                class="rounded">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif
        <!-- Banner Slider Section End -->

        <!-- Why People Choose us Section Start -->
        @if (count($whowearedata) > 0)
            <section class="theme-1">
                <div class="container Who_We_Are">
                    <div class="row my-md-4 my-3 g-3">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 d-flex justify-content-center align-items-center">
                            <div>
                                <div class="menu-heading mb-4">
                                    <h3 class="page-title mb-1 color-changer text-capitalize">
                                        {{ helper::appdata($storeinfo->id)->whoweare_title }}</h3>
                                    <p class="page-subtitle line-limit-2 text-muted mt-0 fs-7">
                                        {{ helper::appdata($storeinfo->id)->whoweare_subtitle }}
                                    </p>
                                </div>
                                <div class="row g-xl-4 g-3">
                                    @foreach ($whowearedata as $key => $whoweare)
                                        <div class="col-lg-6 col-12 d-flex gap-2">
                                            <div class="icon rounded-2">
                                                <img src="{{ helper::image_path($whoweare->image) }}"
                                                    class="w-100 h-100 rounded-2">
                                            </div>
                                            <div class="text-content col">
                                                <h6 class="mb-2 fs-15 color-changer text-capitalize fw-600 line-2">{{ $whoweare->title }}
                                                </h6>
                                                <p class="fs-7 m-0 text-muted line-3">{{ $whoweare->sub_title }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <div class="image1 rounded overflow-hidden">
                                <img src="{{ helper::image_path(helper::appdata($storeinfo->id)->whoweare_image) }}"
                                    alt="" class="object rounded">
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif
        <!-- Why People Choose us Section End -->

        <!-- Categories Section Start -->
        @if (count($getcategory) > 0 && count($getitem) > 0)
            <section class="theme-1-margin-top">
                <div class="container">
                    <div class="menu-heading mb-4">
                        <h3 class="page-title color-changer  mb-1">{{ trans('labels.our_products') }}</h3>
                        <p class="page-subtitle line-limit-2 mt-0">
                            {{ trans('labels.our_products_desc') }}
                        </p>
                    </div>
                    <ul class="navgation_lower overflow-auto theme-1-category-card gap-1 py-3 m-0 flex-lg-wrap">
                        @foreach ($getcategory as $key => $category)
                            @php
                                $check_cat_count = 0;
                            @endphp
                            @foreach ($getitem as $item)
                                @if ($category->id == $item->cat_id)
                                    @php
                                        $check_cat_count++;
                                    @endphp
                                @endif
                            @endforeach
                            @if ($check_cat_count > 0)
                                <li class="{{ $key == 0 ? 'active1' : '' }} mb-4 mx-lg-0 mx-4 theme-1category-width"
                                    id="specs-{{ $category->id }}">
                                    <div class="theme-1active">
                                        <img src="{{ helper::image_path($category->image) }}" alt="">
                                        <p class="act-1 line-2 color-changer fw-600">{{ $category->name }}</p>
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    </ul>

                    @if (helper::appdata($storeinfo->id)->template_type == 1)
                        @include('front.template-1.theme-grid')
                    @else
                        @include('front.template-1.theme-list')
                    @endif
            </section>
        @endif
        <!-- Categories Section End -->

        <!-- DEALS START -->
        @if (App\Models\SystemAddons::where('unique_identifier', 'top_deals')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'top_deals')->first()->activated == 1)
            @if (count($topdealsproducts) > 0 && @helper::top_deals($vdata) != null)
                <section class="deals mb-5 pro-hover" id="topdeals">
                    <div class="background-black bg-changer py-5">
                        <div class="container">
                            <div id="eapps-countdown-timer-1"
                                class="rounded eapps-countdown-timer bg-changer eapps-countdown-timer-align-center eapps-countdown-timer-position-bottom-bar-floating eapps-countdown-timer-animation-none eapps-countdown-timer-theme-default eapps-countdown-timer-finish-button-show   eapps-countdown-timer-style-combined eapps-countdown-timer-style-blocks eapps-countdown-timer-position-bar eapps-countdown-timer-area-clickable eapps-countdown-timer-has-background">
                                <div class="eapps-countdown-timer-container d-flex">
                                    <div class="eapps-countdown-timer-inner row g-3 flex-column flex-sm-row">
                                        <div
                                            class="eapps-countdown-timer-header d-flex col-lg-4 col-md-6 align-items-center justify-content-center justify-content-md-start">
                                            <div class="eapps-countdown-timer-header-title">
                                                <div
                                                    class="eapps-countdown-timer-header-title-text {{ session()->get('direction') == 2 ? 'text-sm-end' : 'text-sm-start' }} text-center">
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
                                            <div class="eapps-countdown-timer-header-caption"></div>
                                        </div>
                                        <div class="eapps-countdown-timer-item-container col-lg-4 col-md-6">
                                            <div id="countdown"></div>
                                        </div>
                                        <div
                                            class="eapps-countdown-timer-button-container d-flex col-lg-4 col-md-12 align-items-center justify-content-center justify-content-lg-end">
                                            <a href="{{ URL::to(@$storeinfo->slug . '/topdeals?type=1') }}"
                                                class="btn btn-secondary px-sm-4 px-3 py-2 rounded fw-500 fs-15">
                                                {{ trans('labels.viewall') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container pt-5">
                        <div class="owl-carousel topdeals-slider owl-theme">
                            @foreach ($topdealsproducts as $item)
                                @php
                                    if (@helper::top_deals($vdata)->offer_type == 1) {
                                        if ($item['variation']->count() > 0) {
                                            if (
                                                $item['variation'][0]->price > @helper::top_deals($vdata)->offer_amount
                                            ) {
                                                $price =
                                                    $item['variation'][0]->price -
                                                    @helper::top_deals($vdata)->offer_amount;
                                            } else {
                                                $price = $item['variation'][0]->price;
                                            }
                                        } else {
                                            if ($item->item_price > @helper::top_deals($vdata)->offer_amount) {
                                                $price = $item->item_price - @helper::top_deals($vdata)->offer_amount;
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
                                                $item->item_price * (@helper::top_deals($vdata)->offer_amount / 100);
                                        }
                                    }
                                    if ($item['variation']->count() > 0) {
                                        $original_price = $item['variation'][0]->price;
                                    } else {
                                        $original_price = $item->item_price;
                                    }
                                    $off = $original_price > 0 ? round(100 - ($price * 100) / $original_price) : 0;
                                    $item_name = str_replace("'", "\'", $item->item_name);
                                @endphp
                                <div class="item h-100">
                                    <div class="col-auto theme1grid">
                                        @if (helper::appdata($storeinfo->id)->template_type == 1)
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
                                                                    onclick="showreviews('{{ $item->id }}')"
                                                                    role="button" aria-controls="offcanvasExample">
                                                                    <i class="fa-solid fa-star text-warning"></i>
                                                                    <p class="cursor-pointer color-changer fw-600 fs-8">
                                                                        {{ number_format($item->avg_ratting, 1) }}</p>
                                                                </a>
                                                            @endif
                                                        @endif
                                                    @endif
                                                    <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}"
                                                        class="title pb-1 color-changer">
                                                        {{ $item->item_name }}
                                                    </a>
                                                </div>
                                                <div class="card-footer bg-transparent border-0 p-2 pt-md-0 p-md-3 pt-0">
                                                    <div
                                                        class="d-flex flex-wrap justify-content-between align-items-center gap-2">
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
                                                        <div
                                                            class="d-flex col-sm-auto col-12 justify-content-end align-items-center">
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
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="card thme1categories rounded dark h-100">
                                                <a href="{{ URL::to($storeinfo->slug . '/details-' . $item->slug) }}">
                                                    <img src="{{ helper::image_path(@$item['item_image']->image) }}"
                                                        class="card-img-top rounded" alt="...">
                                                </a>
                                                <div class="card-body">
                                                    <div class="text-section">
                                                        <div
                                                            class="d-flex justify-content-between flex-wrap align-items-center">
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
                                                                            role="button"
                                                                            aria-controls="offcanvasExample">
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
                                                            class="title pb-1 mt-2 color-changer">{{ $item->item_name }}</a>
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

                                                        <div
                                                            class="d-flex justify-content-between align-items-center mt-2">
                                                            <div
                                                                class="d-flex col-sm-auto justify-content-end align-items-center">
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
                                                                <div
                                                                    class="favorite-icon1 p-0 set-fav1-{{ $item->id }}">
                                                                    @if ($item->is_favorite == 1)
                                                                        <a href="javascript:void(0)"
                                                                            class="text-secondary"
                                                                            onclick="managefavorite('{{ $storeinfo->id }}','{{ $item->id }}',0,'{{ URL::to($storeinfo->slug . '/managefavorite') }}','{{ request()->url() }}')">
                                                                            <i class="fa-solid fa-heart"></i>
                                                                        </a>
                                                                    @else
                                                                        <a href="javascript:void(0)"
                                                                            class="text-secondary"
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
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif
        @endif
        <!-- DEALS END -->

        <!-- Blogs Section Start -->
        @if (App\Models\SystemAddons::where('unique_identifier', 'blog')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'blog')->first()->activated == 1)
            @php
                if (helper::vendordata(@$vdata)->allow_without_subscription == 1) {
                    $blog = 1;
                } else {
                    $blog = @helper::get_plan($storeinfo->id)->blogs;
                }
            @endphp
            @if ($blog == 1)
                @if (count($blogs) > 0)
                    <section class="bg-light bg-change-mode theme-1-margin-top blogs-card theme-1 py-5">
                        <div class="container overflow-hidden">
                            <div class="d-md-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <h3 class="page-title mb-1"> {{ trans('labels.blogs') }}</h3>
                                    <p class="page-subtitle line-limit-2 mb-4">
                                        {{ trans('labels.blog_desc') }}
                                    </p>
                                </div>
                                <div>
                                    <a href="{{ URL::to(@$storeinfo->slug . '/blog-list') }}"
                                        class="btn btn-secondary extra-paddings rounded px-sm-4 px-3 py-2 fs-15 fw-500">
                                        {{ trans('labels.view_all') }}
                                    </a>
                                </div>
                            </div>
                            <div class="owl-carousel blogs-slider owl-theme">
                                @foreach ($blogs as $blog)
                                    <a href="{{ URL::to(@$storeinfo->slug . '/blog-details-' . $blog->slug) }}">
                                        <div class="item h-100">
                                            <div class="card h-100 rounded">
                                                <img src="{{ helper::image_path($blog->image) }}" alt=""
                                                    class="rounded">
                                                <div class="card-body py-4">
                                                    <p class="title color-changer mt-2 blog-title">{{ $blog->title }}</p>
                                                    <span class="blog-description text-muted">{!! $blog->description !!}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif
            @endif
        @endif
        <!-- Blogs Section End -->
        <!-- Store Review Section Start -->
        @if (App\Models\SystemAddons::where('unique_identifier', 'store_reviews')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'store_reviews')->first()->activated == 1)
            @if (count($storereview) > 0)
                <section class="theme-1 testimonial my-5">
                    <div class="container">
                        <!-- Testimonials -->
                        <div class="row">
                            <!-- Title -->
                            <div class="d-md-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h3 class="page-title mb-1"> {{ trans('labels.review_tital') }}</h3>
                                    <p class="page-subtitle line-limit-2 m-md-0 mb-3">
                                        {{ trans('labels.review_note') }}
                                    </p>
                                </div>
                                <div>
                                    <a href="{{ URL::to($storeinfo->slug . '/storereviewshow') }}"
                                        class="btn btn-secondary extra-paddings rounded px-sm-4 px-3 py-2 fs-15 fw-500">
                                        {{ trans('labels.view_all') }}
                                    </a>
                                </div>
                            </div>
                            <div class="owl-carousel testimonial-slider owl-theme testimonials pt-4">
                                @foreach ($storereview as $review)
                                    <div class="item">
                                        <div class="card rounded h-100">
                                            <div class="card-body p-sm-4 p-3">
                                                <div class="d-flex gap-2 align-items-center">
                                                    <div class="client-img">
                                                        <img src="{{ helper::image_path($review->image) }}"
                                                            alt="" class="object">
                                                    </div>
                                                    <div>
                                                        <p class="fs-15 fw-600 color-changer d-flex flex-wrap gap-1 align-items-center">
                                                            {{ $review->name }} - <span
                                                                class="fs-8">{{ $review->position }}</span>
                                                        </p>
                                                        <ul class="d-flex mt-1 gap-1 m-0">
                                                            @if ($review->star == 1)
                                                                <li class="fs-15 text-warning">
                                                                    <i class="fa-solid fa-star"></i>
                                                                </li>
                                                                <li class="fs-15 text-warning">
                                                                    <i class="fa-regular fa-star"></i>
                                                                </li>
                                                                <li class="fs-15 text-warning">
                                                                    <i class="fa-regular fa-star"></i>
                                                                </li>
                                                                <li class="fs-15 text-warning">
                                                                    <i class="fa-regular fa-star"></i>
                                                                </li>
                                                                <li class="fs-15 text-warning">
                                                                    <i class="fa-regular fa-star"></i>
                                                                </li>
                                                            @elseif ($review->star == 2)
                                                                <li class="fs-15 text-warning">
                                                                    <i class="fa-solid fa-star"></i>
                                                                </li>
                                                                <li class="fs-15 text-warning">
                                                                    <i class="fa-solid fa-star"></i>
                                                                </li>
                                                                <li class="fs-15 text-warning">
                                                                    <i class="fa-regular fa-star"></i>
                                                                </li>
                                                                <li class="fs-15 text-warning">
                                                                    <i class="fa-regular fa-star"></i>
                                                                </li>
                                                                <li class="fs-15 text-warning">
                                                                    <i class="fa-regular fa-star"></i>
                                                                </li>
                                                            @elseif ($review->star == 3)
                                                                <li class="fs-15 text-warning">
                                                                    <i class="fa-solid fa-star"></i>
                                                                </li>
                                                                <li class="fs-15 text-warning">
                                                                    <i class="fa-solid fa-star"></i>
                                                                </li>
                                                                <li class="fs-15 text-warning">
                                                                    <i class="fa-solid fa-star"></i>
                                                                </li>
                                                                <li class="fs-15 text-warning">
                                                                    <i class="fa-regular fa-star"></i>
                                                                </li>
                                                                <li class="fs-15 text-warning">
                                                                    <i class="fa-regular fa-star"></i>
                                                                </li>
                                                            @elseif ($review->star == 4)
                                                                <li class="fs-15 text-warning">
                                                                    <i class="fa-solid fa-star"></i>
                                                                </li>
                                                                <li class="fs-15 text-warning">
                                                                    <i class="fa-solid fa-star"></i>
                                                                </li>
                                                                <li class="fs-15 text-warning">
                                                                    <i class="fa-solid fa-star"></i>
                                                                </li>
                                                                <li class="fs-15 text-warning">
                                                                    <i class="fa-solid fa-star"></i>
                                                                </li>
                                                                <li class="fs-15 text-warning">
                                                                    <i class="fa-regular fa-star"></i>
                                                                </li>
                                                            @elseif ($review->star == 5)
                                                                <li class="fs-15 text-warning">
                                                                    <i class="fa-solid fa-star"></i>
                                                                </li>
                                                                <li class="fs-15 text-warning">
                                                                    <i class="fa-solid fa-star"></i>
                                                                </li>
                                                                <li class="fs-15 text-warning">
                                                                    <i class="fa-solid fa-star"></i>
                                                                </li>
                                                                <li class="fs-15 text-warning">
                                                                    <i class="fa-solid fa-star"></i>
                                                                </li>
                                                                <li class="fs-15 text-warning">
                                                                    <i class="fa-solid fa-star"></i>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="review-client mt-2">
                                                    <p class="fs-7 text-muted">
                                                        {{ Str::limit($review->description, 180) }}
                                                    </p>

                                                    <div class="d-flex flex-wrap justify-content-between mt-3">
                                                        <div class="d-flex gap-1 color-changer align-items-center text-dark">
                                                            <i class="fa-solid fa-clock fs-8"></i>
                                                            <p class="fs-8">
                                                                {{ date('l', strtotime($review->created_at)) }},
                                                                {{ helper::time_format($review->created_at, $vdata) }}</p>
                                                        </div>
                                                        <div class="d-flex gap-1 color-changer align-items-center text-dark">
                                                            <i class="fa-solid fa-calendar-days fs-8"></i>
                                                            <p class="fs-8">
                                                                {{ helper::date_format($review->created_at, $vdata) }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>
            @endif
        @endif
        <!-- Store Review Section End -->

        <!-- Subscription Section Start -->
        <section class="theme-1-margin-top extra-margin my-5">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="subscription-main bg-change-mode position-relative w-100 overflow-hidden">
                            <div class="overflow-hidden rounded">
                                <img src="{{ helper::image_path(helper::appdata($storeinfo->id)->subscribe_background) }}"
                                    class="img-fluid subscription-image rounded">
                                <div class="caption-subscription col-md-7 col-lg-6">
                                    <div class="subscription-text">
                                        <h3>{{ trans('labels.subscribe_title') }}</h3>
                                        <p>{{ trans('labels.subscribe_description') }}</p>
                                        <form action="{{ URL::to($storeinfo->slug . '/subscribe') }}" method="post">
                                            @csrf
                                            <div class="subscribe-input form-control col-md-6">
                                                <input type="hidden" value="{{ $storeinfo->id }}" name="id">
                                                <input type="email" name="email" class="form-control border-0"
                                                    placeholder="{{ trans('labels.enter_email') }}" required>
                                                <button type="submit"
                                                    class="btn btn-primary fw-500 fs-15 m-0">{{ trans('labels.subscribe') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Subscription Section End -->
    @else
        @include('front.nodata')
    @endif
@endsection
@section('script')
    <script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/custom/top_deals.js') }}"></script>
@endsection
