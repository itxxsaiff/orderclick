<!DOCTYPE html>
<html lang="{{ session()->get('locale', app()->getLocale()) }}" dir="{{ session()->get('direction') == 2 ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:title" content="{{ @helper::appdata($vdata)->meta_title }}" />
    <meta property="og:description" content="{{ @helper::appdata($vdata)->meta_description }}" />
    <meta property="og:image" content='{{ @helper::image_path(helper::appdata($vdata)->og_image) }}' />
    {{-- <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"> --}}
    <script>
        const theme = localStorage.getItem('theme');
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.add('light');
        }
    </script>

    <title>{{ @helper::appdata($vdata)->website_title }}</title>
    <link rel="icon" href="{{ @helper::image_path(@helper::appdata(@$vdata)->favicon) }}" type="image"
        sizes="16x16">
    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'admin-assets/css/fontawesome/all.min.css') }}">
    <!-- FontAwesome CSS -->
    <!--Aos animetion  -->
    <link href="{{ url(env('ASSETSPATHURL') . 'web-assets/css/unpkg.com_aos@2.3.1_dist_aos.css') }}" rel="stylesheet">
    <!-- swiper Css -->
    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'web-assets/css/swiper-bundle.min.css') }}">
    <!-- Font-Family -->
    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'web-assets/font/outfit.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'web-assets/font-awesome/css/all.min.css') }}">
    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'web-assets/css/toastr/toastr.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'web-assets/css/dataTables.bootstrap4.min.css') }}">
    <!-- Owl Carousel Css -->
    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'web-assets/css/owl.carousel.min.css') }}">
    <!-- Owl Carousel Css -->
    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'web-assets/css/owl.theme.default.css') }}">
    <!-- Bootstrap Min Css -->
    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'web-assets/css/bootstrap.min.css') }}">
    <!-- Style Css -->
    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'web-assets/css/style.css') }}">
    <!-- Responsive Css -->
    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'web-assets/css/responsive.css') }}">
    <!-- Sweetalert CSS -->
    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'web-assets/css/sweetalert/sweetalert2.min.css') }}">

    @yield('recaptcha_script')
    

    <!-- PWA  -->
    @if (App\Models\SystemAddons::where('unique_identifier', 'subscription')->first() != null &&
            App\Models\SystemAddons::where('unique_identifier', 'subscription')->first()->activated == 1)
        @if (App\Models\SystemAddons::where('unique_identifier', 'pwa')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'pwa')->first()->activated == 1)
            @php
                $checkplan = App\Models\Transaction::where('vendor_id', $vdata)->orderByDesc('id')->first();
                $user = App\Models\User::where('id', $vdata)->first();
                if ($user->allow_without_subscription == 1) {
                    $pwa = 1;
                } else {
                    $pwa = @$checkplan->pwa;
                }
            @endphp
            @if ($pwa == 1)
                @if (helper::appdata($vdata)->pwa == 1)
                    @include('front.pwa.pwa')
                @endif
            @endif
        @else
            @if (App\Models\SystemAddons::where('unique_identifier', 'pwa')->first() != null &&
                    App\Models\SystemAddons::where('unique_identifier', 'pwa')->first()->activated == 1)
                @if (helper::appdata($vdata)->pwa == 1)
                    @include('front.pwa.pwa')
                @endif
            @endif
        @endif
    @endif
    <style>
        :root {
            --bs-primary: #ce6a19;
            --bs-secondary: #5a0bee;

            @if (helper::appdata($vdata)->primary_color != null)
                --bs-primary: {{ helper::appdata($vdata)->primary_color }};
            @endif
            @if (helper::appdata($vdata)->secondary_color != null)
                --bs-secondary: {{ helper::appdata($vdata)->secondary_color }};
            @endif
            --secondary-color: #000;
        }
    </style>

    {{-- ===== Order Click V2 — clean storefront chrome (dynamic vendor colour) ===== --}}
    <style>
        :root { --ocs-ink: #17201a; --ocs-muted: #6a756c; --ocs-line: #e7ece4; --ocs-bg: #f7faf7; }
        body { background: #fff; }

        /* ---------- Header ---------- */
        .header-main { background: rgba(255,255,255,.96) !important; backdrop-filter: saturate(180%) blur(8px);
            border-bottom: 1px solid var(--ocs-line) !important; box-shadow: none !important; }
        .header-main .navbar { padding-top: 12px !important; padding-bottom: 12px !important; }
        .header-main .logo img, #logoimage { height: 58px !important; width: auto !important; object-fit: contain; }
        .header-main .nav-link { color: var(--ocs-ink) !important; font-weight: 500; font-size: 15px; }
        .header-main .nav-link:hover, .header-main .nav-link.active { color: var(--bs-primary) !important; }
        .header-main .fa-bars, .header-main .navbar-toggler i { color: var(--ocs-ink) !important; }
        .header-main .language-dropdown-image, .header-main .bg-secondary {
            background: #f1f5ef !important; color: var(--ocs-ink) !important; border: 1px solid var(--ocs-line) !important;
            width: 40px; height: 40px; border-radius: 50% !important; }
        .header-main .language-dropdown-image i, .header-main .language-dropdown-image p { color: var(--ocs-ink) !important; }
        .header-main .cart-counting, #cartcount { background: var(--bs-primary) !important; color: #fff !important; border-radius: 20px; }
        .header-main .login-buuton { background: var(--bs-primary) !important; color: #fff !important; border: 0 !important;
            border-radius: 10px !important; font-weight: 600; }
        .header-main .login-buuton:hover { filter: brightness(.93); color: #fff !important; }
        .header-main .profile_image { border: 2px solid var(--bs-primary); border-radius: 50%; width: 40px; height: 40px; object-fit: cover; }
        .header-main .sale { background: var(--bs-primary) !important; }
        .header-main .sale a, .header-main .sale p { color: #fff !important; }

        /* ---------- Footer ---------- */
        footer.bg-changer, main + footer, footer { background: #0f1a13 !important; color: #c7d3cb !important; }
        footer .footer-title { color: #fff !important; font-weight: 600; }
        footer .footersubtitle, footer .footer-right-side a, footer p, footer li a { color: #9fb0a4 !important; }
        footer .footer-right-side a:hover { color: var(--bs-primary) !important; }
        footer .footer-logo img { background: #fff; border-radius: 10px; padding: 6px 10px; height: 48px; width: auto; }
        footer hr { border-color: rgba(255,255,255,.08) !important; }

        /* ---------- Mobile bottom nav ---------- */
        .mobile-menu-footer { background: #fff !important; border-top: 1px solid var(--ocs-line) !important; box-shadow: 0 -4px 20px -12px rgba(0,0,0,.15); }
        .mobile-menu-footer a { color: var(--ocs-muted) !important; }
        .mobile-menu-footer a.active, .mobile-menu-footer a.active i { color: var(--bs-primary) !important; }
        .mobile-menu-footer .cart-3 { background: var(--bs-primary) !important; color: #fff !important; }

        /* ---------- Primary buttons / accents across the store ---------- */
        .btn-primary, .bg-primary { background: var(--bs-primary) !important; border-color: var(--bs-primary) !important; }
        .text-primary { color: var(--bs-primary) !important; }
        /* Main "Add to Cart" CTA uses the vendor's dynamic colour */
        .btn-store, button.addtocart.btn-store { background: var(--bs-primary) !important; color: #fff !important;
            border: 1px solid var(--bs-primary) !important; border-radius: 10px !important; }
        .btn-store:hover { filter: brightness(.93); color: #fff !important; }
        .ct-add, .add-cart-btn { background: var(--bs-primary) !important; color: #fff !important; border-color: var(--bs-primary) !important; }
        /* Price accent */
        .details_item_price.pricing, .pro-price, .now { color: var(--bs-primary) !important; }
    </style>
    {{-- V2: premium styling for ALL pages (details, cart, about, contact, modals) on premium-theme stores --}}
    @php
        $ocVd = $vdata ?? (isset($storeinfo) ? optional($storeinfo)->id : null);
        $ocTpl = $ocVd ? (int) optional(\App\Models\Settings::where('vendor_id', $ocVd)->first())->template : 0;
    @endphp
    @if (in_array($ocTpl, [3, 4, 5, 6], true))
        @include('front.premium.pages')
    @endif
</head>

<body class="">
    <main>
        @php
            $baseurl = url('/') . '/' . $storeinfo->slug;
            $basecaturl = url('/') . '/' . $storeinfo->slug . '/categories';
        @endphp
        @if (helper::appdata(@$vdata)->template != 3 &&
                helper::appdata(@$vdata)->template != 5 &&
                helper::appdata(@$vdata)->template != 14)
            @include('front.theme.header')
        @else
            @if ($baseurl != request()->url() && $basecaturl != request()->url())
                @include('front.theme.header')
            @endif
        @endif
        @if (App\Models\SystemAddons::where('unique_identifier', 'age_verification')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'age_verification')->first()->activated == 1)
            @if (@helper::getagedetails($vdata)->age_verification_on_off == 1)
                @php
                    $class = 'blurred';
                @endphp
            @else
                @php
                    $class = '';
                @endphp
            @endif
        @else
            @php
                $class = '';
            @endphp
        @endif
        <div class="{{ $class }}" id="main-content">
            @yield('content')

            {{-- V2: current store's business type (direct query — appdata() is host/port-sensitive) --}}
            @php $oc_store_btype = optional(\App\Models\Settings::where('vendor_id', $vdata)->first())->business_type; @endphp

            {{-- V2 Booking: floating "Book Appointment" button for booking-type stores --}}
            @if ($oc_store_btype == 'booking' && !request()->is('*/booking'))
                @php $ocbAr = app()->getLocale() === 'ar'; @endphp
                <a href="{{ URL::to($storeinfo->slug . '/booking') }}"
                    style="position:fixed;left:50%;transform:translateX(-50%);bottom:20px;z-index:1030;
                           display:inline-flex;align-items:center;gap:9px;height:54px;padding:0 26px;
                           background:var(--bs-primary,#1f9d55);color:#fff;border-radius:40px;font-weight:650;
                           font-size:15.5px;text-decoration:none;box-shadow:0 14px 30px -10px rgba(0,0,0,.4);">
                    <i class="fa-solid fa-calendar-check"></i>
                    {{ $ocbAr ? 'احجز موعد' : 'Book Appointment' }}
                </a>
            @endif

            {{-- V2 Service Request: floating "Request Service" button for service-type stores --}}
            @if ($oc_store_btype == 'service' && !request()->is('*/service'))
                @php $ocrAr = app()->getLocale() === 'ar'; @endphp
                <a href="{{ URL::to($storeinfo->slug . '/service') }}"
                    style="position:fixed;left:50%;transform:translateX(-50%);bottom:20px;z-index:1030;
                           display:inline-flex;align-items:center;gap:9px;height:54px;padding:0 26px;
                           background:var(--bs-primary,#1f9d55);color:#fff;border-radius:40px;font-weight:650;
                           font-size:15.5px;text-decoration:none;box-shadow:0 14px 30px -10px rgba(0,0,0,.4);">
                    <i class="fa-solid fa-clipboard-list"></i>
                    {{ $ocrAr ? 'اطلب خدمة' : 'Request Service' }}
                </a>
            @endif

            @if (helper::appdata(@$vdata)->template != 3 &&
                    helper::appdata(@$vdata)->template != 5 &&
                    helper::appdata(@$vdata)->template != 14)
                @include('front.theme.footer')
            @else
                @if ($baseurl != request()->url() && $basecaturl != request()->url())
                    @include('front.theme.footer')
                @endif
            @endif
            @if (App\Models\SystemAddons::where('unique_identifier', 'sales_notification')->first() != null &&
                    App\Models\SystemAddons::where('unique_identifier', 'sales_notification')->first()->activated == 1)
                @include('front.sales_notification')
            @endif
            {{-- cookie_consent --}}
            @if (App\Models\SystemAddons::where('unique_identifier', 'cookie_consent')->first() != null &&
                    App\Models\SystemAddons::where('unique_identifier', 'cookie_consent')->first()->activated == 1)
                @include('cookie-consent::index')
            @endif

            {{-- WhatsApp Icon --}}
            @if (App\Models\SystemAddons::where('unique_identifier', 'subscription')->first() != null &&
                    App\Models\SystemAddons::where('unique_identifier', 'subscription')->first()->activated == 1)
                @if (App\Models\SystemAddons::where('unique_identifier', 'whatsapp_message')->first() != null &&
                        App\Models\SystemAddons::where('unique_identifier', 'whatsapp_message')->first()->activated == 1)
                    @php
                        $checkplan = App\Models\Transaction::where('vendor_id', $vdata)->orderByDesc('id')->first();
                        $user = App\Models\User::where('id', $vdata)->first();
                        if (@$user->allow_without_subscription == 1) {
                            $whatsapp_message = 1;
                        } else {
                            $whatsapp_message = @$checkplan->whatsapp_message;
                        }

                    @endphp
                    @if ($whatsapp_message == 1 && @whatsapp_helper::whatsapp_message_config($vdata)->whatsapp_chat_on_off == 1)
                        <div
                            class="{{ @whatsapp_helper::whatsapp_message_config($vdata)->whatsapp_mobile_view_on_off == 1 ? 'd-block' : 'd-lg-block d-none' }}">
                            <input type="checkbox" id="check" class="d-none">
                            <label
                                class="chat-btn {{ @whatsapp_helper::whatsapp_message_config($vdata)->whatsapp_chat_position == 1 ? 'chat-btn_rtl' : 'chat-btn_ltr' }}"
                                for="check">
                                <i class="fa-brands fa-whatsapp comment"></i>
                                <i class="fa fa-close close"></i>
                            </label>
                            <div
                                class="{{ @whatsapp_helper::whatsapp_message_config($vdata)->whatsapp_chat_position == 1 ? 'wrapper_rtl' : 'wrapper' }} wp_chat_box shadow">
                                <div class="msg_header">
                                    <h6>{{ helper::appdata(@$vdata)->website_title }}</h6>
                                </div>
                                <div class="text-start p-3 bg-msg">
                                    <div class="card p-2 msg">
                                        {{ trans('labels.whatsapp_modal_description') }}
                                    </div>
                                </div>
                                <div class="chat-form">
                                    <form action="https://api.whatsapp.com/send" method="get" target="_blank"
                                        class="d-flex align-items-center d-grid gap-2">
                                        <textarea class="form-control" name="text" placeholder="Your Text Message" required></textarea>
                                        <input type="hidden" name="phone"
                                            value="{{ @whatsapp_helper::whatsapp_message_config($vdata)->whatsapp_number }}">
                                        <button type="submit" class="btn btn-success btn-block hover-1">
                                            <i class="fa-solid fa-paper-plane"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            @else
                @if (App\Models\SystemAddons::where('unique_identifier', 'whatsapp_message')->first() != null &&
                        App\Models\SystemAddons::where('unique_identifier', 'whatsapp_message')->first()->activated == 1)
                    @if (@whatsapp_helper::whatsapp_message_config($vdata)->whatsapp_chat_on_off == 1)
                        <div
                            class="{{ @whatsapp_helper::whatsapp_message_config($vdata)->whatsapp_mobile_view_on_off == 1 ? 'd-block' : 'd-lg-block d-none' }}">
                            <input type="checkbox" id="check">
                            <label
                                class="chat-btn {{ @whatsapp_helper::whatsapp_message_config($vdata)->whatsapp_chat_position == 1 ? 'chat-btn_rtl' : 'chat-btn_ltr' }}"
                                for="check">
                                <i class="fa-brands fa-whatsapp comment"></i>
                                <i class="fa fa-close close"></i>
                            </label>
                            <div
                                class="{{ @whatsapp_helper::whatsapp_message_config($vdata)->whatsapp_chat_position == 1 ? 'wrapper_rtl' : 'wrapper' }} wp_chat_box shadow">
                                <div class="msg_header">
                                    <h6>{{ helper::appdata(@$vdata)->website_title }}</h6>
                                </div>
                                <div class="text-start p-3 bg-msg">
                                    <div class="card p-2 msg">
                                        {{ trans('labels.whatsapp_modal_description') }}
                                    </div>
                                </div>
                                <div class="chat-form">
                                    <form action="https://api.whatsapp.com/send" method="get" target="_blank"
                                        class="d-flex align-items-center d-grid gap-2">
                                        <textarea class="form-control" name="text" placeholder="Your Text Message" required></textarea>
                                        <input type="hidden" name="phone"
                                            value="{{ @whatsapp_helper::whatsapp_message_config($vdata)->whatsapp_number }}">
                                        <button type="submit" class="btn btn-success btn-block hover-1">
                                            <i class="fa-solid fa-paper-plane"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            @endif

            <!-- Quick call -->
            @if (App\Models\SystemAddons::where('unique_identifier', 'quick_call')->first() != null &&
                    App\Models\SystemAddons::where('unique_identifier', 'quick_call')->first()->activated == 1)
                @if (helper::appdata($vdata)->quick_call == 1)
                    <div
                        class="{{ helper::appdata($vdata)->quick_call_mobile_view_on_off == 1 ? 'd-block' : 'd-lg-block d-none' }}">
                        @include('front.quick_call')
                    </div>
                @endif
            @endif
        </div>
    </main>
    @if (App\Models\SystemAddons::where('unique_identifier', 'age_verification')->first() != null &&
            App\Models\SystemAddons::where('unique_identifier', 'age_verification')->first()->activated == 1)
        @if (@helper::getagedetails($vdata)->age_verification_on_off == 1)
            @include('front.theme.age_model')
        @endif
    @endif

    <!-- Customisation modal Start -->
    <div class="modal" id="customisation" tabindex="-1" aria-labelledby="customisationLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded">
                <div class="modal-header gap-2 justify-content-between p-3">
                    <p class="title fs-5 color-changer" id="cart_item_name"></p>
                    <button type="button" class="bg-transparent border-0 m-0" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="fa-regular fa-xmark color-changer fs-4"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="p-12px">
                        <div id="item-variations" class="mt-2">

                        </div>
                        <!-- Extras -->
                        <div id="item-extras" class="mt-3">
                            <h5 class="fw-normal color-changer m-0 d-none" id="extras_title">{{ trans('labels.extras') }} </h5>
                            <ul class="m-0 ps-2">
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscribe Modal -->
    <div class="modal subscription-popup" id="subscribe_modal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-lights">
                <div class="modal-body p-md-0">
                    <div class="card rounded-4 border-0 bg-lights p-md-3">
                        <div class="d-flex justify-content-end">
                            <button type="button" class="bg-transparent border-0 m-0" data-bs-dismiss="modal"
                                aria-label="Close">
                                <i class="fa-regular fa-xmark color-changer fs-4"></i>
                            </button>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-4 d-none d-lg-block">
                                <img src="{{ helper::image_path(helper::appdata($storeinfo->id)->subscribe_background) }}"
                                    alt="" class="w-100 object-fit-cover rounded-4">
                            </div>
                            <div class="col-12 col-lg-8">
                                <h4 class="fs-2 mb-2 color-changer fw-600">{{ trans('labels.subscribe_title') }}</h4>
                                <p class="text-muted pb-3">{{ trans('labels.subscribe_description') }}</p>
                                <form action="{{ URL::to($storeinfo->slug . '/subscribe') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $storeinfo->id }}">
                                    <div class="bg-body bg-change-mode rounded p-2 shadow-lg mb-3 mb-md-0">
                                        <div class="input-group">
                                            <input class="form-control border-0 me-1" type="email" name="email"
                                                placeholder="{{ trans('labels.enter_email') }}" required>
                                            <button type="submit"
                                                class="btn btn-primary fs-15 fw-500 mb-0 btn-submit px-sm-4 px-3 py-2 rounded d-none d-md-inline-block">{{ trans('labels.subscribe') }}!</button>
                                        </div>
                                    </div>
                                    <button type="submit"
                                        class="btn w-100 btn-primary fs-15 fw-500 mb-0 btn-submit rounded px-sm-4 px-3 py-2 d-inline-block d-md-none">{{ trans('labels.subscribe') }}!</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Review Modal start --}}
    <div class="modal" id="addreview" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header justify-content-between align-items-center">
                    <h1 class="modal-title color-changer fs-5" id="product_name"></h1>
                    <button type="button" class="bg-transparent border-0 m-0" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="fa-regular fa-xmark color-changer fs-4"></i>
                    </button>
                </div>
                <form action="{{ URL::to('add-review') }}" method="post" id="product_ratting">
                    @csrf
                    <div class="modal-body">
                        <h5 class="fw-600 mb-2 color-changer text-center">{{ trans('labels.product_reviews_note_1') }}</h5>
                        <p class="m-0 mb-2 fs-7 text-muted text-center">{{ trans('labels.product_reviews_note_2') }}
                        </p>
                        <div class="info col-12 d-flex flex-column justify-content-center">
                            <div class="emoji fs-1 m-0 text-center"></div>
                            <div class="status fw-500 fs-6 mt-2 text-center"></div>
                        </div>
                        <input type="hidden" name="item_id" id="item_id">
                        <input type="hidden" name="vendor_id" id="vendor_id" value="{{ @$vdata }}">
                        <div class="stars mt-2 text-center {{ session()->get('direction') == 2 ? 'rtl' : '' }}">
                            <input type="radio" name="star" value="5" id="star5">
                            <label for="star5" class="star" data-rate="5">
                                <i class="fa-solid fa-star"></i>
                            </label>
                            <input type="radio" name="star" value="4" id="star4">
                            <label for="star4" class="star" data-rate="4">
                                <i class="fa-solid fa-star"></i>
                            </label>
                            <input type="radio" name="star" value="3" id="star3">
                            <label for="star3" class="star" data-rate="3">
                                <i class="fa-solid fa-star"></i>
                            </label>
                            <input type="radio" name="star" value="2" id="star2">
                            <label for="star2" class="star" data-rate="2">
                                <i class="fa-solid fa-star"></i>
                            </label>
                            <input type="radio" name="star" value="1" id="star1">
                            <label for="star1" class="star" data-rate="1">
                                <i class="fa-solid fa-star"></i>
                            </label>
                        </div>
                        <div class="col-12 mt-3">
                            <textarea class="form-control fs-7 bg-light" placeholder="Write comment here..." name="description" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer py-3">
                        <div class="col-12 m-0 w-100">
                            <div class="row m-0 g-2">
                                <div class="col-6 m-0">
                                    <button type="button" class="btn btn-secondary fs-15 fw-500 py-3 w-100"
                                        data-bs-dismiss="modal">{{ trans('labels.close') }}</button>
                                </div>
                                <div class="col-6 m-0">
                                    <button type="submit"
                                        class="btn btn-primary fs-15 fw-500 py-3 w-100">{{ trans('labels.submit') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Review Modal end --}}

    <!-- Modal -->
    <div class="d-flex align-items-center float-end">
        <div class="modal" id="searchModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-centered">
                <div class="modal-content search-modal-content rounded-4">
                    <div class="modal-header justify-content-between align-items-center p-3">
                        <h3 class="page-title mb-0 text-dark color-changer fw-bolder">{{ trans('labels.search') }}</h3>
                        <button type="button" class="bg-transparent border-0 m-0" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="fa-regular fa-xmark color-changer fs-4"></i>
                        </button>
                    </div>
                    <form class="" action="{{ URL::to($storeinfo->slug . '/search/') }}" method="get">
                        <div class="modal-body mb-0">
                            <div class="col-12">
                                <div class="row align-items-center justify-content-between g-0">
                                    <span class="color-changer">{{ trans('labels.search_desc') }}</span>
                                    <div class="col-12">
                                        <input type="hidden" name="vendor_id" value="{{ $vdata }}">
                                        <input type="text" placeholder="{{ trans('labels.search_here') }}"
                                            name="search" id="searchText"
                                            class="py-2 input-width bg-transparent color-changer rounded-2 px-2 mt-3 mb-1 w-100 border fs-7 search_input"
                                            value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <div class="w-100 search-btn-group">
                                <div class="row g-3 justify-content-between align-items-center">
                                    <div class="col-6 m-sm-0">
                                        <a type="submit"
                                            class="btn btn-danger w-100 rounded px-sm-4 px-3 py-3 fw-500 fs-15 text-center"
                                            data-bs-dismiss="modal">{{ trans('labels.cancel') }} </a>
                                    </div>
                                    <div class="col-6 m-sm-0">
                                        <input type="submit"
                                            class="btn-primary btn w-100 rounded px-sm-4 px-3 py-3 fw-500 fs-15 text-center"
                                            value="{{ trans('labels.submit') }}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Hours Modal Start -->
    <div class="modal" id="examplehours" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4">
                <div class="modal-header justify-content-between p-3">
                    <p class="title color-changer fs-5"> {{ trans('labels.working_hours') }}</p>
                     <button type="button" class="bg-transparent border-0 m-0" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="fa-regular fa-xmark color-changer fs-4"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="m-0">
                        @if (is_array(@helper::timings($vdata)) || is_object(@helper::timings($vdata)))
                            @foreach (@helper::timings($vdata) as $time)
                                @if ($time->is_always_close != 1)
                                    <li class="working-hours-main pb-3">
                                        <p>
                                            <i class="fa-regular fa-calendar-days hours-to"></i>
                                            <span class="px-2 fw-600">
                                                {{ trans('labels.' . strtolower($time->day)) }}</span>
                                        </p>
                                        <div class="hours-list">
                                            <button type="button"
                                                class="btn btn-outline-dark text-dark hours-to fs-7">{{ $time->open_time }}</button>
                                            <p class="to">{{ trans('labels.to') }}</p>
                                            <button type="button"
                                                class="btn btn-outline-dark text-dark hours-to fs-7">{{ $time->close_time }}</button>
                                        </div>
                                    </li>
                                @else
                                    <li class="d-flex align-items-center justify-content-end pb-3">
                                        <p class="sunday">
                                            <i class="fa-regular fa-calendar-days hours-to"></i>
                                            <span
                                                class="px-2 fw-600 text-danger sunday">{{ trans('labels.' . strtolower($time->day)) }}
                                            </span>
                                        </p>
                                        <div class="hours-list justify-content-center m-auto">
                                            <button type="button"
                                                class="btn border text-dark bg-danger text-white fs-7"
                                                data-bs-dismiss="modal">{{ trans('labels.closed') }}</button>
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- Hours Modal end -->

    <div class="modal" id="additems" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div id="viewproduct_body"></div>
        </div>
    </div>

    <!-- Useroption Modal Start -->
    <div class="modal" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel"
        tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4">
                <div class="modal-header justify-content-between p-3">
                    <h5 class="modal-title color-changer mb-0">{{ trans('labels.proceed_as_guest_or_login') }}</h5>
                    <button type="button" class="bg-transparent border-0 m-0" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="fa-regular fa-xmark color-changer fs-4"></i>
                    </button>
                </div>
                <div class="modal-body p-3">
                    <small class="color-changer">{{ trans('labels.dont_have_account_guest') }}</small>
                </div>
                <div class="modal-footer d-block p-3 pt-2">
                    <div class="row justify-content-between align-items-center g-2">
                        <div class="col-md-6 md-mt-0">
                            <a class="btn-outline-dark px-sm-4 px-3 py-2 btn w-100 text-center my-cart-account-btn"
                                href="{{ URL::to(@$storeinfo->slug . '/login') }}">{{ trans('labels.login_with_your_account') }}</a>
                        </div>
                        <div class="col-md-6 md-mt-0">
                            <a class="btn-primary btn px-sm-4 px-3 py-2 rounded-2 w-100 text-center"
                                href="{{ URL::to(@$storeinfo->slug . '/checkout?buy_now=0') }}">{{ trans('labels.continue_as_guest') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Useroption Modal end -->

    <!-- Bankdetails Modal Start -->
    <div class="modal" id="modalbankdetails" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="modalbankdetailsLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4">
                <div class="modal-header justify-content-between">
                    <h5 class="modal-title color-changer" id="modalbankdetailsLabel">{{ trans('labels.banktransfer') }}</h5>
                    <button type="button" class="bg-transparent border-0 m-0" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa-regular fa-xmark color-changer fs-4"></i>
                    </button>
                </div>
                <form enctype="multipart/form-data" action="{{ URL::to('/orders/paymentmethod') }}" method="POST">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="payment_type" id="payment_type" value="">
                        <input type="hidden" name="modal_customer_name" id="modal_customer_name" value="">
                        <input type="hidden" name="modal_customer_email" id="modal_customer_email" value="">
                        <input type="hidden" name="modal_customer_mobile" id="modal_customer_mobile"
                            value="">
                        <input type="hidden" name="modal_delivery_date" id="modal_delivery_date" value="">
                        <input type="hidden" name="modal_delivery_time" id="modal_delivery_time" value="">
                        <input type="hidden" name="modal_delivery_area" id="modal_delivery_area" value="">
                        <input type="hidden" name="modal_delivery_charge" id="modal_delivery_charge"
                            value="">
                        <input type="hidden" name="modal_address" id="modal_address" value="">
                        <input type="hidden" name="modal_landmark" id="modal_landmark" value="">
                        <input type="hidden" name="modal_postal_code" id="modal_postal_code" value="">
                        <input type="hidden" name="modal_building" id="modal_building" value="">
                        <input type="hidden" name="modal_message" id="modal_message" value="">
                        <input type="hidden" name="modal_subtotal" id="modal_subtotal" value="">
                        <input type="hidden" name="modal_discount_amount" id="modal_discount_amount"
                            value="">
                        <input type="hidden" name="modal_couponcode" id="modal_couponcode" value="">
                        <input type="hidden" name="modal_ordertype" id="modal_ordertype" value="">
                        <input type="hidden" name="modal_vendor_id" id="modal_vendor_id" value="">
                        <input type="hidden" name="modal_slug" id="modal_slug" value="">
                        <input type="hidden" name="modal_grand_total" id="modal_grand_total" value="">
                        <input type="hidden" name="modal_tax" id="modal_tax" value="">
                        <input type="hidden" name="modal_tax_name" id="modal_tax_name" value="">
                        <input type="hidden" name="modal_order_type" id="modal_order_type" value="">
                        <input type="hidden" name="modal_table" id="modal_table" value="">
                        <input type="hidden" name="modal_offer_type" id="modal_offer_type" value="">
                        <input type="hidden" name="modal_buynow" id="modal_buynow" value="">
                        <input type="hidden" name="modal_tips" id="modal_tips" value="">
                        <p class="color-changer">{{ trans('labels.payment_description') }}</p>
                        <div class="border-bottom my-3"></div>
                        <p class="payment_description color-changer" id="payment_description"></p>
                        <div class="border-bottom my-3"></div>
                        <div class="form-group col-md-12">
                            <label for="screenshot" class="form-label"> {{ trans('labels.screenshot') }} </label>
                            <div class="controls">
                                <input type="file" name="screenshot" id="screenshot"
                                    class="form-control  @error('screenshot') is-invalid @enderror" required>
                                @error('screenshot')
                                    <span class="text-danger"> {{ $message }} </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer pt-0 p-3">
                        <div class="row w-100 justify-content-between align-items-center g-2 pt-2">
                            <div class="col-md-6 md-mt-0">
                                <button class="btn-danger btn w-100 fw-500 fs-15 py-3 text-center"
                                    data-bs-dismiss="modal">{{ trans('labels.close') }}</button>
                            </div>
                            <div class="col-md-6 md-mt-0">
                                <button type="submit"
                                    class="btn-primary btn rounded-2 w-100 fw-500 fs-15 py-3 text-center">{{ trans('labels.save') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Bankdetails Modal end -->
    <div class="col-md-6 d-flex justify-content-center m-auto">
        <div class="offcanvas offcanvas-bottom categories_theme6_offcanvas" tabindex="-1" id="offcanvasBottom"
            aria-labelledby="offcanvasBottomLabel">
            <div class="offcanvas-header justify-content-between border-bottom">
                <h5 class="offcanvas-title color-changer" id="offcanvascategori">
                    {{ trans('labels.categories') }}
                </h5>
                <button type="button" class="bg-transparent border-0 m-0" data-bs-dismiss="offcanvas"
                    aria-label="Close">
                    <i class="fa-regular fa-xmark color-changer fs-4"></i>
                </button>
            </div>

            <!-- ----- modal start ---- -->
            <div class="offcanvas-body small overflow-auto ">
                <div class="tab-row" id="menu-center">
                    <ul
                        class="swiper-wrapper navgation_lower pb-1 d-block theme-7-category-card pb-1 mb-0 category-card">
                        @if (is_array(@$getcategory) || is_object(@$getcategory))
                            @foreach (@$getcategory as $key => $category)
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
                                    <li class="{{ $key == 0 ? 'active1' : '' }} d-flex py-2 swiper-slide w-100 justify-content-between border-bottom-1"
                                        data-bs-dismiss="offcanvas" id="specs-{{ $category->id }}">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ helper::image_path($category->image) }}" alt="">
                                            <p class="act-7 fw-500 color-changer px-2 text-start line-limit-1 fs-7">
                                                {{ $category->name }}</p>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="m-0">{{ $check_cat_count }}</span>
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Product Review offcanvas --}}
    <div class="" id="viewreviewsbody"></div>

    <script>
        const stars = document.querySelectorAll(".star");
        const emojiEl = document.querySelector(".emoji");
        const statusEl = document.querySelector(".status");
        const defaultRatingIndex = 0;
        let currentRatingIndex = 0;

        const ratings = [{
                emoji: "✨",
                name: "Rating"
            },
            {
                emoji: "😔",
                name: "Very Bad",
                color: "#ff0000"
            },
            {
                emoji: "🙁",
                name: "Bad",
                color: "#ff4d00"
            },
            {
                emoji: "🙂",
                name: "Good",
                color: "#edcc12"
            },
            {
                emoji: "🤩",
                name: "Very Good",
                color: "rgb(81 141 14)"
            },
            {
                emoji: "🥰",
                name: "Excellent",
                color: "green"
            }
        ];

        const setRating = (index) => {
            stars.forEach((star) => star.classList.remove("selected"));
            if (index > 0 && index <= stars.length) {
                document.querySelector('[data-rate="' + index + '"]').classList.add("selected");
            }
            emojiEl.innerHTML = ratings[index].emoji;
            statusEl.innerHTML = ratings[index].name;
            statusEl.style.color = ratings[index].color;
        };

        stars.forEach((star) => {
            star.addEventListener("click", function() {
                const index = parseInt(star.getAttribute("data-rate"));
                currentRatingIndex = index;
                setRating(index);
            });

            star.addEventListener("mouseover", function() {
                const index = parseInt(star.getAttribute("data-rate"));
                setRating(index);
            });

            star.addEventListener("mouseout", function() {
                setRating(currentRatingIndex);
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            setRating(defaultRatingIndex);
        });
    </script>
    @if (App\Models\SystemAddons::where('unique_identifier', 'sales_notification')->first() != null &&
            App\Models\SystemAddons::where('unique_identifier', 'sales_notification')->first()->activated == 1)
        @if (helper::appdata($vdata)->fake_sales_notification == 1)
            <script>
                // Select the element with the ID 'sales-booster-popup'
                const popup = document.getElementById('sales-booster-popup');

                if (popup) {
                    let intervalId; // To hold the interval ID
                    let isMouseOver = false; // Track whether the mouse is over the popup

                    // Define a function to add and remove the 'loaded' class
                    const toggleLoadedClass = () => {
                        popup.classList.add('loaded');
                        // Remove the 'loaded' class after the specified time, unless the mouse is over the popup
                        setTimeout(() => {
                                if (!isMouseOver) {
                                    popup.classList.remove('loaded');
                                }
                            },
                            "{{ helper::appdata($vdata)->notification_display_time }}"
                        );
                    };

                    // Function to handle mouseover event
                    const handleMouseOver = () => {
                        isMouseOver = true;
                        // Clear the interval while the mouse is over the popup
                        clearInterval(intervalId);
                    };

                    // Function to handle mouseout event
                    const handleMouseOut = () => {
                        isMouseOver = false;
                        // Restart the interval when the mouse leaves the popup
                        startPopupInterval();
                    };

                    // Function to start the interval for fetching notification data
                    const startPopupInterval = () => {
                        intervalId = setInterval(() => {
                                $.ajax({
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    url: "{{ URL::to('get_notification_data') }}",
                                    type: "post",
                                    dataType: "json",
                                    data: {
                                        vendor_id: "{{ $vdata }}",
                                    },
                                    success: function(response) {
                                        toggleLoadedClass();
                                        $('#sales-booster-popup').show();
                                        $('#notification_body').html(response.output);
                                    },
                                });
                            },
                            "{{ helper::appdata($vdata)->notification_display_time + helper::appdata($vdata)->next_time_popup }}"
                        );
                    };

                    // Call the function initially
                    toggleLoadedClass();

                    // Start the interval initially
                    startPopupInterval();

                    // Add mouseover and mouseout event listeners to the popup
                    popup.addEventListener('mouseover', handleMouseOver);
                    popup.addEventListener('mouseout', handleMouseOut);

                    // Select the close button within the popup
                    const closeButton = popup.querySelector('.close'); // Close button selector

                    if (closeButton) {
                        // Add an event listener to the close button
                        closeButton.addEventListener('click', () => {
                            // Remove the 'loaded' class immediately
                            popup.classList.remove('loaded');
                            // Clear the interval when popup is closed
                            clearInterval(intervalId);
                        });
                    }
                }
            </script>
        @endif
    @endif
    <input type="hidden" id="addtocarturl" value="{{ url('/add-to-cart') }}" />
    <input type="hidden" id="showitemurl" value="{{ url('/product-details') }}" />
    <input type="hidden" id="showreviewsurl" value="{{ url('/product-reviews') }}" />
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ helper::appdata(1)->tracking_id }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', '{{ helper::appdata(1)->tracking_id }}');
    </script>
    <!--Start of Tawk.to Script-->
    @if (App\Models\SystemAddons::where('unique_identifier', 'tawk')->first() != null &&
            App\Models\SystemAddons::where('unique_identifier', 'tawk')->first()->activated == 1)
        @if (helper::appdata(@$vdata)->tawk_on_off == 1)
            {!! helper::appdata(@$vdata)->tawk_widget_id !!}
        @endif
    @endif
    <!--End of Tawk.to Script-->
    <!-- Wizz Chat -->
    @if (App\Models\SystemAddons::where('unique_identifier', 'wizz_chat')->first() != null &&
            App\Models\SystemAddons::where('unique_identifier', 'wizz_chat')->first()->activated == 1)
        @if (helper::appdata($vdata)->wizz_chat_on_off == 1)
            {!! helper::appdata($vdata)->wizz_chat_settings !!}
        @endif
    @endif

    <script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/jquery-3.6.3.min.js') }}"></script>
    <script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/custom.js') }}"></script>
    <script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/custom/product.js') }}"></script>
    <script src="{{ url('resources/js/age.js') }}"></script>
    <script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/custom/cart.js') }}" type="text/javascript"></script>

    <!-- Bootstrap Bundle Min Js -->

    <script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Owl Carousel Min Js -->

    <script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/owl.carousel.min.js') }}"></script>

    <script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/toastr/toastr.min.js') }}"></script><!-- Toastr JS -->

    <!-- Jquery DataTables Min Js -->

    <script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/jquery.dataTables.min.js') }}"></script>

    <!-- DataTables Bootstrap4 Min Js -->

    <script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Sweetalert2@11 Js -->

    <script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/sweetalert2@11.js') }}"></script>

    <!-- Aos Js -->

    <script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/unpkg.com_aos@2.3.1_dist_aos.js') }}"></script>

    <!-- Swiper Bundle Min Js -->

    <script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/cdn.jsdelivr.net_npm_swiper@9_swiper-bundle.min.js') }}">
    </script>

    <script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/jquery.number.min.js') }}"></script>
    <script>
        var are_you_sure = "{{ trans('messages.are_you_sure') }}";
        var yes = "{{ trans('messages.yes') }}";
        var no = "{{ trans('messages.no') }}";
        var cancel = "{{ trans('labels.cancel') }}";
        let wrong = "{{ trans('messages.wrong') }}";
        let env = "{{ env('Environment') }}";
        let whatsappnumber = "{{ @helper::appdata(@$vdata)->contact }}";
        let direction = "{{ session('direction') }}";
        var vendor_id = "{{ $vdata }}";
        var formate = "{{ helper::appdata($vdata)->currency_formate }}";
        toastr.options = {
            "closeButton": true,
            "positionClass": "toast-bottom-center",
        }
        @if (Session::has('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if (Session::has('error'))
            toastr.error("{{ session('error') }}");
        @endif

        // top deals parameter
        var start_date = "{{ @helper::top_deals($vdata)->start_date }}";
        var start_time = "{{ @helper::top_deals($vdata)->start_time }}";
        var end_date = "{{ @helper::top_deals($vdata)->end_date }}";
        var end_time = "{{ @helper::top_deals($vdata)->end_time }}";
        @if (App\Models\SystemAddons::where('unique_identifier', 'top_deals')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'top_deals')->first()->activated == 1)
            var enddate = "{{ @App\Models\TopDeals::where('vendor_id', $vdata)->first()->end_date }}";
            var endtime = "{{ @App\Models\TopDeals::where('vendor_id', $vdata)->first()->end_time }}";
        @else
            var enddate = null;
            var endtime = null;
        @endif
        var topdeals = "{{ !empty(@$topdealsproducts) ? 1 : 0 }}";
        var time_zone = "{{ helper::appdata($vdata)->timezone }}";
        var current_date = "{{ \Carbon\Carbon::now()->toDateString() }}";
        var deal_type = "{{ @helper::top_deals($vdata)->deal_type }}";
        var siteurl = "{{ URL::to($storeinfo->slug) }}";
        var darklogo = "{{ helper::image_path(helper::appdata($storeinfo->id)->darklogo) }}";
        var lightlogo = "{{ helper::image_path(helper::appdata($storeinfo->id)->logo) }}";
       

        function currency_formate(price) {
            var formate = {{ @helper::currencyinfo($vdata)->decimal_digit ?? 2 }};
            var price = parseFloat(price) * {{ @helper::currencyinfo($vdata)->exchange_rate }};

            var currency = "{{ @helper::currencyinfo($vdata)->currency }}";
            
            var position = "{{ @helper::currencyinfo($vdata)->currency_position }}";
            var space = "{{ @helper::currencyinfo($vdata)->currency_space }}";
            var decimal_sep = "{{ @helper::currencyinfo($vdata)->decimal_separator }}";

            var oldprice = decimal_sep == 1 ? $.number(price, formate) : $.number(price, formate, ',', '.');
            var newprice = '';

            if (position == "1") {
                newprice = space == "1" ? currency + ' ' + oldprice : currency + oldprice;
            } else {
                newprice = space == "1" ? oldprice + ' ' + currency : oldprice + currency;
            }

            return newprice;
        }

        $('.whatsapp_icon').on("click", function(event) {
            $(".wp_chat_box").toggleClass("d-none");
        });

        // Theme-1 owlCarousel js
        $('.categories-slider').owlCarousel({
            rtl: direction == '2' ? true : false,
            loop: false,
            nav: false,
            // margin: 15,
            dots: false,
            responsive: {
                0: {
                    items: 2
                },
                600: {
                    items: 5
                },
                1024: {
                    items: 6
                },
                1000: {
                    items: 8
                }
            }
        })
        $('.blogs-slider').owlCarousel({
            rtl: direction == '2' ? true : false,
            loop: false,
            nav: false,
            margin: 15,
            dots: false,
            responsive: {
                0: {
                    items: 1
                },
                768: {
                    items: 2
                },
                1024: {
                    items: 3

                },
                1440: {
                    items: 4

                },
                1660: {
                    items: 4

                }
            }
        })

        $('.banner-imges-slider').owlCarousel({
            rtl: direction == '2' ? true : false,
            loop: false,
            nav: true,
            margin: 15,
            dots: false,
            navText: ["<i class='fa-solid fa-arrow-left-long'></i>",
                "<i class='fa-solid fa-arrow-right-long'></i>"
            ],
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 2
                },
                1024: {
                    items: 2
                },
                1660: {
                    items: 2
                }
            }
        })

        @if (helper::appdata($storeinfo->id)->template_type == 1)
            $('.topdeals-slider').owlCarousel({
                rtl: direction == '2' ? true : false,
                loop: true,
                nav: false,
                autoplay: true,
                autoplayTimeout: 3000,
                autoplaySpeed: 2000,
                margin: 8,
                dots: false,
                responsive: {
                    0: {
                        items: @if (helper::appdata($storeinfo->id)->template == 1)
                            2
                        @elseif (helper::appdata($storeinfo->id)->template == 4)
                            2
                        @elseif (helper::appdata($storeinfo->id)->template == 6)
                            2
                        @elseif (helper::appdata($storeinfo->id)->template == 7)
                            2
                        @elseif (helper::appdata($storeinfo->id)->template == 8)
                            2
                        @elseif (helper::appdata($storeinfo->id)->template == 9)
                            2
                        @elseif (helper::appdata($storeinfo->id)->template == 10)
                            2
                        @elseif (helper::appdata($storeinfo->id)->template == 11)
                            2
                        @elseif (helper::appdata($storeinfo->id)->template == 12)
                            2
                        @elseif (helper::appdata($storeinfo->id)->template == 13)
                            2
                        @elseif (helper::appdata($storeinfo->id)->template == 14)
                            2
                        @elseif (helper::appdata($storeinfo->id)->template == 15)
                            2
                        @else
                            1
                        @endif ,
                    },
                    400: {
                        items: 2,
                    },
                    600: {
                        items: 2,
                    },
                    768: {
                        items: @if (helper::appdata($storeinfo->id)->template == 11)
                            3
                        @elseif (helper::appdata($storeinfo->id)->template == 12)
                            3
                        @elseif (helper::appdata($storeinfo->id)->template == 13)
                            3
                        @elseif (helper::appdata($storeinfo->id)->template == 15)
                            3
                        @else
                            2
                        @endif ,
                        margin: 15,
                    },
                    1024: {
                        items: @if (helper::appdata($storeinfo->id)->template == 11)
                            4
                        @elseif (helper::appdata($storeinfo->id)->template == 12)
                            4
                        @elseif (helper::appdata($storeinfo->id)->template == 13)
                            4
                        @elseif (helper::appdata($storeinfo->id)->template == 15)
                            4
                        @else
                            3
                        @endif ,
                        margin: 15,
                    },
                    1440: {
                        items: @if (helper::appdata($storeinfo->id)->template == 11)
                            4
                        @elseif (helper::appdata($storeinfo->id)->template == 12)
                            4
                        @elseif (helper::appdata($storeinfo->id)->template == 13)
                            4
                        @elseif (helper::appdata($storeinfo->id)->template == 15)
                            4
                        @else
                            4
                        @endif ,
                        margin: 15,
                    },
                    1660: {
                        items: @if (helper::appdata($storeinfo->id)->template == 11)
                            5
                        @elseif (helper::appdata($storeinfo->id)->template == 12)
                            5
                        @elseif (helper::appdata($storeinfo->id)->template == 13)
                            5
                        @elseif (helper::appdata($storeinfo->id)->template == 15)
                            5
                        @else
                            4
                        @endif ,
                        margin: 15,
                    }
                }
            })
        @else
            $('.topdeals-slider').owlCarousel({
                rtl: direction == '2' ? true : false,
                loop: true,
                nav: false,
                margin: 10,
                dots: false,
                autoplay: true,
                autoplayTimeout: 4000,
                autoplaySpeed: 4000,
                responsive: {
                    0: {
                        items: 1
                    },
                    768: {
                        items: @if (helper::appdata($storeinfo->id)->template == 1)
                            2
                        @elseif (helper::appdata($storeinfo->id)->template == 2)
                            2
                        @elseif (helper::appdata($storeinfo->id)->template == 7)
                            1
                        @elseif (helper::appdata($storeinfo->id)->template == 8)
                            1
                        @elseif (helper::appdata($storeinfo->id)->template == 10)
                            2
                        @elseif (helper::appdata($storeinfo->id)->template == 11)
                            2
                        @elseif (helper::appdata($storeinfo->id)->template == 12)
                            2
                        @elseif (helper::appdata($storeinfo->id)->template == 13)
                            2
                        @elseif (helper::appdata($storeinfo->id)->template == 15)
                            2
                        @else
                            2
                        @endif
                    },
                    1024: {
                        items: @if (helper::appdata($storeinfo->id)->template == 1)
                            2
                        @elseif (helper::appdata($storeinfo->id)->template == 2)
                            3
                        @elseif (helper::appdata($storeinfo->id)->template == 4)
                            2
                        @elseif (helper::appdata($storeinfo->id)->template == 8)
                            2
                        @elseif (helper::appdata($storeinfo->id)->template == 10)
                            2
                        @elseif (helper::appdata($storeinfo->id)->template == 11)
                            2
                        @elseif (helper::appdata($storeinfo->id)->template == 12)
                            2
                        @elseif (helper::appdata($storeinfo->id)->template == 13)
                            2
                        @elseif (helper::appdata($storeinfo->id)->template == 15)
                            2
                        @else
                            2
                        @endif
                    },
                    1440: {
                        items: @if (helper::appdata($storeinfo->id)->template == 1)
                            2
                        @elseif (helper::appdata($storeinfo->id)->template == 2)
                            3
                        @elseif (helper::appdata($storeinfo->id)->template == 4)
                            3
                        @elseif (helper::appdata($storeinfo->id)->template == 8)
                            3
                        @elseif (helper::appdata($storeinfo->id)->template == 10)
                            3
                        @elseif (helper::appdata($storeinfo->id)->template == 11)
                            3
                        @elseif (helper::appdata($storeinfo->id)->template == 12)
                            3
                        @elseif (helper::appdata($storeinfo->id)->template == 13)
                            3
                        @elseif (helper::appdata($storeinfo->id)->template == 15)
                            3
                        @else
                            2
                        @endif
                    },
                    1660: {
                        items: @if (helper::appdata($storeinfo->id)->template == 1)
                            2
                        @elseif (helper::appdata($storeinfo->id)->template == 2)
                            3
                        @elseif (helper::appdata($storeinfo->id)->template == 4)
                            3
                        @elseif (helper::appdata($storeinfo->id)->template == 8)
                            3
                        @elseif (helper::appdata($storeinfo->id)->template == 10)
                            3
                        @elseif (helper::appdata($storeinfo->id)->template == 11)
                            3
                        @elseif (helper::appdata($storeinfo->id)->template == 12)
                            3
                        @elseif (helper::appdata($storeinfo->id)->template == 13)
                            3
                        @elseif (helper::appdata($storeinfo->id)->template == 15)
                            3
                        @else
                            2
                        @endif

                    }
                }
            })
        @endif
        $('.testimonial-slider').owlCarousel({
            rtl: direction == '2' ? true : false,
            loop: true,
            nav: false,
            margin: 15,
            dots: false,
            autoHeight: true,
            responsive: {
                0: {
                    items: 1
                },
                768: {
                    items: 1
                },
                1024: {
                    items: 2
                },
                1440: {
                    items: 3

                },
                1660: {
                    items: 3

                }
            }
        })

        // Theme-2 owlCarousel js
        $('.theme-2-categories').owlCarousel({
            rtl: direction == '2' ? true : false,
            loop: false,
            nav: false,
            margin: 30,
            padding: 30,
            dots: false,
            responsive: {
                0: {
                    items: 2
                },
                600: {
                    items: 3
                },
                1000: {
                    items: 5
                }
            }
        })
        $('.theme-2blogs-slider').owlCarousel({
            rtl: direction == '2' ? true : false,
            loop: false,
            nav: false,
            margin: 15,
            dots: false,
            responsive: {
                0: {
                    items: 1
                },
                768: {
                    items: 2
                },
                1024: {
                    items: 3

                },
                1440: {
                    items: 4

                },
                1660: {
                    items: 4

                }
            }
        })
        $('.banner-imges-slider-2').owlCarousel({
            rtl: direction == '2' ? true : false,
            loop: false,
            nav: true,
            margin: 25,
            dots: false,
            navText: ["<i class='fa-solid fa-arrow-left-long'></i>",
                "<i class='fa-solid fa-arrow-right-long'></i>"
            ],
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 2
                },
                1024: {
                    items: 3
                },
                1660: {
                    items: 3
                }
            }
        })

        // Theme-4 owlCarousel js
        $('.testimonial-slider4').owlCarousel({
            rtl: direction == '2' ? true : false,
            loop: true,
            nav: false,
            margin: 15,
            dots: false,
            autoHeight: true,
            responsive: {
                0: {
                    items: 1
                },
                768: {
                    items: 2
                },
                1024: {
                    items: 3
                },
                1440: {
                    items: 4
                },
                1660: {
                    items: 4

                }
            }
        })

        // Theme-10 owlCarousel js
        $('.category-slider-theme-10').owlCarousel({
            loop: false,
            margin: 16,
            nav: true,
            dots: false,
            navText: ["<i class='fa-solid fa-arrow-left-long'></i>",
                "<i class='fa-solid fa-arrow-right-long'></i>"
            ],
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 3
                },
                1000: {
                    items: 4
                },
                1200: {
                    items: 4
                }
            }
        })

        // Theme-11 owlCarousel js
        $('.banner-imges-slider-11').owlCarousel({
            rtl: direction == '2' ? true : false,
            loop: true,
            nav: false,
            margin: 14,
            dots: false,
            autoplay: true,
            autoplayTimeout: 3000,
            autoplaySpeed: 2000,
            navText: ["<i class='fa-solid fa-arrow-left-long'></i>",
                "<i class='fa-solid fa-arrow-right-long'></i>"
            ],
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 2
                },
                1000: {
                    items: 2
                },
                1200: {
                    items: 3
                }
            }
        })
        $('#testimonial-slider-11').owlCarousel({
            loop: true,
            nav: false,
            margin: 10,
            dots: false,
            autoplay: true,
            autoplayTimeout: 2500,
            autoplaySpeed: 1000,
            rtl: direction == '2' ? true : false,
            responsive: {
                0: {
                    items: 1,
                },
                600: {
                    items: 2,
                },
                1000: {
                    items: 3,
                }
            }
        })

        // Theme-12 owlCarousel js
        $('#testimonial-slider-12').owlCarousel({
            rtl: direction == '2' ? true : false,
            loop: true,
            nav: false,
            navText: ["<i class='fa-solid fa-arrow-left-long'></i>",
                "<i class='fa-solid fa-arrow-right-long'></i>"
            ],
            dots: false,
            autoplay: true,
            autoHeight: true,
            autoplayTimeout: 5000,
            responsive: {
                0: {
                    items: 1,
                },
                700: {
                    items: 1,
                },
                1000: {
                    items: 2,
                },
                1200: {
                    items: 3,
                },
            }
        })

        // Theme-13 owlCarousel js
        $('.testimonial-slider-13').owlCarousel({
            loop: true,
            nav: false,
            margin: 10,
            dots: false,
            autoplay: true,
            autoplayTimeout: 2500,
            rtl: direction == '2' ? true : false,
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 2
                },
                1000: {
                    items: 3
                }
            }
        })

        // Theme-15 owlCarousel js
        $('.product-slider-15').owlCarousel({
            rtl: direction == '2' ? true : false,
            loop: false,
            nav: true,
            navText: ["<i class='fa-solid fa-arrow-left-long'></i>",
                "<i class='fa-solid fa-arrow-right-long'></i>"
            ],
            margin: 10,
            dots: false,
            responsive: {
                0: {
                    items: 2,
                },
                400: {
                    items: 2,
                },
                600: {
                    items: 2,
                },
                768: {
                    items: 3,
                },
                1000: {
                    items: 4,
                },
                1200: {
                    items: 5,
                }
            }
        })
        $('.product-list-slider-15').owlCarousel({
            rtl: direction == '2' ? true : false,
            loop: false,
            nav: true,
            navText: ["<i class='fa-solid fa-arrow-left-long'></i>",
                "<i class='fa-solid fa-arrow-right-long'></i>"
            ],
            margin: 10,
            dots: false,
            responsive: {
                0: {
                    items: 1,
                },
                400: {
                    items: 1,
                },
                600: {
                    items: 2,
                },
                768: {
                    items: 2,
                },
                1000: {
                    items: 2,
                },
                1200: {
                    items: 3,
                }
            }
        })


        $('.cart-modal').owlCarousel({
            rtl: direction == '2' ? true : false,
            loop: false,
            nav: false,
            margin: 25,
            dots: false,
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 1
                },
                1024: {
                    items: 2
                },
                1660: {
                    items: 2
                }
            }
        })


        // aos js important
        AOS.init();

        AOS.init({
            // Global settings:
            disable: false,
            startEvent: 'DOMContentLoaded',
            initClassName: 'aos-init',
            animatedClassName: 'aos-animate',
            useClassNames: false,
            disableMutationObserver: false,
            debounceDelay: 50,
            throttleDelay: 99,


            // Settings that can be overridden on per-element basis, by `data-aos-*` attributes:
            offset: 120,
            delay: 0,
            duration: 400,
            easing: 'ease',
            once: false,
            mirror: false,
            anchorPlacement: 'top-bottom',

        });
    </script>
    {{-- pwa js --}}
    @if (App\Models\SystemAddons::where('unique_identifier', 'subscription')->first() != null &&
            App\Models\SystemAddons::where('unique_identifier', 'subscription')->first()->activated == 1)
        @if (App\Models\SystemAddons::where('unique_identifier', 'pwa')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'pwa')->first()->activated == 1)
            @php
                $checkplan = App\Models\Transaction::where('vendor_id', $vdata)->orderByDesc('id')->first();
                $user = App\Models\User::where('id', $vdata)->first();
                if ($user->allow_without_subscription == 1) {
                    $pwa = 1;
                } else {
                    $pwa = @$checkplan->pwa;
                }
            @endphp
            @if ($pwa == 1)
                <script src="{{ url('storage/app/public/sw.js') }}"></script>
                <script>
                    if (!navigator.serviceWorker.controller) {
                        navigator.serviceWorker.register("{{ url('storage/app/public/sw.js') }}").then(function(reg) {
                            console.log("Service worker has been registered for scope: " + reg.scope);
                        });
                    }
                </script>
            @endif
        @endif
    @else
        @if (App\Models\SystemAddons::where('unique_identifier', 'pwa')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'pwa')->first()->activated == 1)
            <script src="{{ url('storage/app/public/sw.js') }}"></script>
            <script>
                if (!navigator.serviceWorker.controller) {
                    navigator.serviceWorker.register("{{ url('storage/app/public/sw.js') }}").then(function(reg) {
                        console.log("Service worker has been registered for scope: " + reg.scope);
                    });
                }
            </script>
        @endif
    @endif
    @if (App\Models\SystemAddons::where('unique_identifier', 'pixel')->first() != null &&
            App\Models\SystemAddons::where('unique_identifier', 'pixel')->first()->activated == 1)
        @include('front.pixel.pixel')
    @endif
    <script></script>
    @yield('model')
    @yield('script')
</body>

</html>
