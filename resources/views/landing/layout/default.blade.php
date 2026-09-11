<!DOCTYPE html>
<html lang="{{ session()->get('locale', app()->getLocale()) }}" dir="{{ session()->get('direction') == 2 ? 'rtl' : 'ltr' }}" class="light">

<head>
    <meta charset="UTF-8">
    @include('partials.google_tag')
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:title" content="{{ helper::appdata('')->meta_title }}" />
    <meta property="og:description" content="{{ helper::appdata('')->meta_description }}" />
    <meta property="og:image" content='{{ helper::image_path(helper::appdata('')->og_image) }}' />
    <link rel="icon" type="image" sizes="16x16" href="{{ helper::image_path(helper::appdata('')->favicon) }}">
    <!-- Favicon icon -->
    <title>{{ helper::appdata('')->landing_website_title }}</title>
    @php
        $landingAssetPath = 'storage/app/public/landing/';
    @endphp
    <!-- Font Awesome icon css-->

    <link rel="stylesheet" href="{{ asset($landingAssetPath . 'css/all.min.css') }}">

    <!-- owl carousel css -->

    <link rel="stylesheet" href="{{ asset($landingAssetPath . 'css/owl.carousel.min.css') }}">

    <!-- owl carousel css -->

    <link rel="stylesheet" href="{{ asset($landingAssetPath . 'css/owl.theme.default.min.css') }}">

    <!-- Poppins fonts -->

    <link rel="stylesheet" href="{{ asset($landingAssetPath . 'fonts/poppins.css') }}">

    <!-- bootstrap-icons css -->

    <link rel="stylesheet" href="{{ asset($landingAssetPath . 'css/bootstrap-icons.css') }}">

    <!-- bootstrap css -->

    <link rel="stylesheet" href="{{ asset($landingAssetPath . 'css/bootstrap.min.css') }}">

    <!-- style css -->

    <link rel="stylesheet" href="{{ asset($landingAssetPath . 'css/style.css') }}">

    <!-- responsive css -->

    <link rel="stylesheet" href="{{ asset($landingAssetPath . 'css/responsive.css') }}">
    <style>
        :root {

            /* Color */
            --bs-primary: {{ helper::appdata('')->primary_color }};
            --bs-secondary: {{ helper::appdata('')->secondary_color }};

        }
    </style>
    @include('landing.layout.oc_theme_styles')
    @yield('styles')
        <script>
        const theme = localStorage.getItem('theme');
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.add('light');
        }
    </script>
</head>

<body>
    @include('landing.layout.header')
    <div>

        @yield('content')
    </div>
    @include('landing.layout.footer')

    <!-- Quick call -->
    @if (App\Models\SystemAddons::where('unique_identifier', 'quick_call')->first() != null &&
            App\Models\SystemAddons::where('unique_identifier', 'quick_call')->first()->activated == 1)
        @if (helper::appdata('')->quick_call == 1)
            <div
                class="{{ helper::appdata('')->quick_call_mobile_view_on_off == 1 ? 'd-block' : 'd-lg-block d-none' }}">
                @include('front.quick_call')
            </div>
        @endif
    @endif


    <!-- Modal -->
    <div class="d-flex align-items-center float-end">
        <div class="modal fade"  tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content search-modal-content rounded-4">
                    {{-- <div class="modal-header border-0 p-3 justify-content-between align-items-center">
                        <h3 class="page-title mb-0 d-block d-md-none">search</h3>
                        <button type="button" class="btn-close p-0 m-0" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div> --}}
                    <div class="modal-body">
                        <form class="" action="{{ URL::to('stores') }}" method="get">
                            <div class="col-12">
                                <div class="row align-items-center justify-content-between">
                                    <div class="col-6 d-none d-lg-block">
                                        <div class="Search-left-img">
                                            <img src="{{ asset($landingAssetPath . 'images/search.webp') }}"
                                                alt="search-left-img" class="w-100 object-fit-cover search-left-img">
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <div class="search-content text-capitalize">
                                            <div class="d-flex justify-content-between gap-2 mb-2 align-items-center ">
                                                <h4 class="fs-2 text-dark color-changer fw-bolder m-0">
                                                    {{ trans('labels.search') }}
                                                </h4>
                                                <a type="button" class="bg-transparent text-dark border-0 m-0" data-bs-dismiss="modal" aria-label="Close">
                                                    <i class="fa-regular fa-xmark color-changer fs-4"></i>
                                                </a>
                                            </div>
                                            <p class="fs-6 color-changer">{{ trans('labels.search_title') }}</p>
                                        </div>
                                        <div class="select-input-box">
                                            <select name="store"
                                                class="py-2 input-width px-2 mt-sm-4 mt-2 mb-1 w-100 border rounded-5 fs-7"
                                                id="store">
                                                <option value="">{{ trans('landing.select_store_category') }}
                                                </option>
                                                @foreach (@helper::storecategory() as $store)
                                                    <option value="{{ $store->name }}"
                                                        {{ request()->get('store') == $store->name ? 'selected' : '' }}>
                                                        {{ $store->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <select name="city" id="city"
                                            class="py-2 input-width px-2 mt-2 mb-1 w-100 border rounded-5 fs-7">
                                            <option value=""
                                                data-value="{{ URL::to('/stores?city=' . '&area=' . request()->get('area')) }}"
                                                data-id="0" selected>{{ trans('landing.select_city') }}</option>

                                            @foreach (helper::get_city() as $city)
                                                <option value="{{ $city->name }}"
                                                    data-value="{{ URL::to('/stores?city=' . request()->get('city') . '&area=' . request()->get('area')) }}"
                                                    data-id={{ $city->id }}
                                                    {{ request()->get('city') == $city->name ? 'selected' : '' }}>
                                                    {{ $city->name }}</option>
                                            @endforeach
                                        </select>

                                        <select name="area" id="area"
                                            class="py-2 input-width px-2 mt-2 mb-1 w-100 border rounded-5 fs-7">
                                            <option value="">{{ trans('landing.select_area') }}</option>
                                            @if (request()->get('area'))
                                                <option value="{{ request()->get('area') }}" selected>
                                                    {{ request()->get('area') }}</option>
                                            @endif


                                        </select>

                                        <div class="search-btn-group">
                                            <div
                                                class="row g-2 justify-content-between align-items-center mt-sm-5 mt-3">
                                                <div class="col-6">
                                                    <a type="submit"
                                                        class="btn-primary bg-danger px-3 py-3 w-100 rounded-3 rounded-3 text-center"
                                                        data-bs-dismiss="modal">{{ trans('labels.cancel') }} </a>
                                                </div>
                                                <div class="col-6">
                                                    <input type="submit"
                                                        class="btn-primary w-100 rounded-3 px-3 py-3 rounded-3 text-center"
                                                        value="{{ trans('labels.submit') }}" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- whatsapp modal start -->
    @if (App\Models\SystemAddons::where('unique_identifier', 'whatsapp_message')->first() != null &&
            App\Models\SystemAddons::where('unique_identifier', 'whatsapp_message')->first()->activated == 1)
        @if (@whatsapp_helper::whatsapp_message_config(1)->whatsapp_chat_on_off == 1)
            <div
                class="{{ @whatsapp_helper::whatsapp_message_config(1)->whatsapp_mobile_view_on_off == 1 ? 'd-block' : 'd-lg-block d-none' }}">
                <input type="checkbox" id="check" class="d-none">
                <label
                    class="chat-btn {{ @whatsapp_helper::whatsapp_message_config(1)->whatsapp_chat_position == 1 ? 'chat-btn_rtl' : 'chat-btn_ltr' }}"
                    for="check">
                    <i class="fa-brands fa-whatsapp comment"></i>
                    <i class="fa fa-close close"></i>
                </label>
                <div
                    class="{{ @whatsapp_helper::whatsapp_message_config(1)->whatsapp_chat_position == 1 ? 'wrapper_rtl' : 'wrapper' }} shadow wp_chat_box">
                    <div class="msg_header">
                        <h6>{{ helper::appdata('')->website_title }}</h6>
                    </div>
                    <div class="text-start p-3 bg-msg">
                        <div class="card color-changer p-2 msg">
                            {{ trans('labels.whatsapp_modal_description') }}
                        </div>
                    </div>
                    <div class="chat-form">
                        <form action="https://api.whatsapp.com/send" method="get" target="_blank"
                            class="d-flex align-items-center d-grid gap-2">
                            <textarea class="form-control" name="text" placeholder="Your Text Message" required></textarea>
                            <input type="hidden" name="phone"
                                value="{{ @whatsapp_helper::whatsapp_message_config(1)->whatsapp_number }}">
                            <button type="submit" class="btn btn-success p-2 btn-block">
                                <i class="fa-solid fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endif
    <!-- whatsapp modal end -->
    <!--Start of Tawk.to Script-->
    @if (App\Models\SystemAddons::where('unique_identifier', 'tawk')->first() != null &&
            App\Models\SystemAddons::where('unique_identifier', 'tawk')->first()->activated == 1)
        @if (helper::appdata('')->tawk_on_off == 1)
            {!! helper::appdata('')->tawk_widget_id !!}
        @endif
    @endif
    <!--End of Tawk.to Script-->
    @if (App\Models\SystemAddons::where('unique_identifier', 'wizz_chat')->first() != null &&
            App\Models\SystemAddons::where('unique_identifier', 'wizz_chat')->first()->activated == 1)
        @if (helper::appdata('')->wizz_chat_on_off == 1)
            <!-- Wizz Chat -->
            {!! helper::appdata('')->wizz_chat_settings !!}
        @endif
    @endif

    <!---------------- sidebar ---------------->
    <div class="offcanvas offcanvas-{{ session()->get('direction') == 2 ? 'end' : 'start' }}" tabindex="-1"
        id="sidebarfooter" aria-controls="sidebarfooter">
        <div class="offcanvas-header justify-content-between align-items-center border-bottom border-dark">
            <p class="menu_title color-changer">{{ trans('landing.menu') }}</p>
            <button type="button" class="bg-transparent border-0 m-0" data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="fa-regular fa-xmark color-changer fs-4"></i>
            </button>
        </div>
        <div class="offcanvas-body">
            <ul class="list-group list-add list-group-flush border-bottom">
                <li class="list-group-item px-0 py-2">
                    <a class="fs-7 fw-500 d-flex gap-2 align-items-center text-dark color-changer" href="{{ URL::to('/#home') }}">
                        <i class="fa-solid fa-circle-dot fs-7"></i>
                        {{ trans('landing.home') }}
                    </a>
                </li>
                <li class="list-group-item px-0 py-2">
                    <a class="fs-7 fw-500 d-flex gap-2 align-items-center text-dark color-changer"
                        href="{{ URL::to('/#features') }}">
                        <i class="fa-solid fa-circle-dot fs-7"></i>
                        {{ trans('landing.features') }}
                    </a>
                </li>
                <li class="list-group-item px-0 py-2">
                    <a class="fs-7 fw-500 d-flex gap-2 align-items-center text-dark color-changer"
                        href="{{ URL::to('/#our-stores') }}">
                        <i class="fa-solid fa-circle-dot fs-7"></i>
                        {{ trans('landing.our_stores') }}
                    </a>
                </li>
                <li class="list-group-item px-0 py-2">
                    <a class="fs-7 fw-500 d-flex gap-2 align-items-center text-dark color-changer"
                        href="{{ URL::to('/#pricing-plans') }}">
                        <i class="fa-solid fa-circle-dot fs-7"></i>
                        {{ trans('landing.pricing_plan') }}
                    </a>
                </li>
                <li class="list-group-item px-0 py-2">
                    <a class="fs-7 fw-500 d-flex gap-2 align-items-center text-dark color-changer"
                        href="{{ URL::to('blog_list') }}">
                        <i class="fa-solid fa-circle-dot fs-7"></i>
                        {{ trans('landing.blogs') }}
                    </a>
                </li>
                <li class="list-group-item px-0 py-2">
                    <a class="fs-7 fw-500 d-flex gap-2 align-items-center text-dark color-changer"
                        href="{{ URL::to('/#contect-us') }}">
                        <i class="fa-solid fa-circle-dot fs-7"></i>
                        {{ trans('landing.contact_us') }}
                    </a>
                </li>
                <li class="list-group-item px-0 py-2">
                    <a class="fs-7 fw-500 d-flex gap-2 align-items-center text-dark color-changer" aria-current="page"
                        data-bs-toggle="modal" data-bs-target="#searchModal">
                        <i class="fa-solid fa-circle-dot fs-7"></i>
                        {{ trans('landing.search_store') }}
                    </a>
                </li>
            </ul>
            {{-- <ul class="navbar-nav nav-menu">
                <li class="nav-item text-uppercase border-bottom">
                    <a aria-current="page" href="{{ URL::to('/#home') }}"
                        class="active">{{ trans('landing.home') }}</a>
                </li>
                <li class="nav-item text-uppercase border-bottom">
                    <a aria-current="page" href="{{ URL::to('/#features') }}">{{ trans('landing.features') }}</a>
                </li>
                <li class="nav-item text-uppercase border-bottom">
                    <a aria-current="page"
                        href="{{ URL::to('/#our-stores') }}">{{ trans('landing.our_stores') }}</a>
                </li>
                <li class="nav-item text-uppercase border-bottom">
                    <a aria-current="page"
                        href="{{ URL::to('/#pricing-plans') }}">{{ trans('landing.pricing_plan') }}</a>
                </li>
                <li class="nav-item text-uppercase border-bottom">
                    <a aria-current="page" href="{{ URL::to('blog_list') }}">{{ trans('landing.blogs') }}</a>
                </li>
                <li class="nav-item text-uppercase border-bottom">
                    <a aria-current="page"
                        href="{{ URL::to('/#contect-us') }} ">{{ trans('landing.contact_us') }}</a>
                </li>
                <li class="nav-item text-uppercase border-bottom">
                    <a aria-current="page" data-bs-toggle="modal"
                        data-bs-target="#searchModal">{{ trans('landing.search_store') }}</a>
                </li>
            </ul> --}}
        </div>
    </div>
    <!---------------- sidebar ---------------->

    <!-- Jquery Min js -->
    <script>
        let direction = "{{ session()->get('direction') }}";
    </script>

    <script src="{{ asset($landingAssetPath . 'js/jquery.min.js') }}"></script>

    <!-- Bootstrap js -->

    <script src="{{ asset($landingAssetPath . 'js/bootstrap.bundle.min.js') }}"></script>

    <!-- owl carousel js -->

    <script src="{{ asset($landingAssetPath . 'js/owl.carousel.min.js') }}"></script>

    <!-- custom js -->

    <script src="{{ asset($landingAssetPath . 'js/custom.js') }}"></script>

    @yield('scripts')

    <script>
        var areaurl = "{{ URL::to('admin/getarea') }}";
        var select = "{{ trans('landing.select_area') }}";
        var areaname = "{{ request()->get('area') }}";
        var env = "{{ env('Environment') }}";

        $('.whatsapp_icon').on("click", function(event) {
            $(".wp_chat_box").toggleClass("d-none");
        });
    </script>

</body>

</html>
