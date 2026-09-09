<!DOCTYPE html>
<html lang="{{ session()->get('locale', app()->getLocale()) }}" dir="{{ session()->get('direction') == 2 ? 'rtl' : 'ltr' }}" class="light">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script>
        const theme = localStorage.getItem('theme');
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.add('light');
        }
    </script>

    {{-- <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">  --}}
    <title>{{ helper::appdata($storeinfo->id)->website_title }}</title>
    <link rel="icon" href="{{ @helper::image_path(@helper::appdata(@$storeinfo->id)->favicon) }}" type="image"
        sizes="16x16">

    <!-- font-family -->

    <link href="{{ url(env('ASSETSPATHURL') . 'web-assets/font/css2.css') }}" rel="stylesheet">

    <!-- font awesome -->

    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'admin-assets/css/fontawesome/all.min.css') }}">

    <!-- carousel css -->

    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'web-assets/css/owl.carousel.min.css') }}">

    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'web-assets/css/toastr/toastr.min.css') }}">

    <!-- carousel css -->

    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'web-assets/css/owl.theme.default.css') }}">

    <!-- bootstrap min css -->

    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'web-assets/css/bootstrap.min.css') }}">

    <!-- style css -->

    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'web-assets/css/style.css') }}">

    <!-- responsive css -->

    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'web-assets/css/responsive.css') }}">

    <style>
        :root {
            --bs-primary: #ce6a19;
            --bs-secondary: #5a0bee;

            @if (helper::appdata($storeinfo->id)->primary_color != null)
                --bs-primary: {{ helper::appdata($storeinfo->id)->primary_color }};
            @endif

            @if (helper::appdata($storeinfo->id)->secondary_color != null)
                --bs-secondary: {{ helper::appdata($storeinfo->id)->secondary_color }};
            @endif

            --secondary-color: #000;
            --font-family: 'Outfit',
            sans-serif;
        }
    </style>
</head>

<body>
    <section class="mt-0 extra-margins">
        <div class="row m-0 vh-100">
            <div class="col-xl-4 col-lg-5 col-12 d-flex justify-content-center align-items-center bg-change-mode bg-light">
                <div class="right-side row justify-content-center align-items-center g-0">
                    <div class="card overflow-hidden border-0 w-100 card-bg bg-transparent">
                        <div class="card-body">
                            <form class="row align-items-center justify-content-center m-auto py-md-3 py-lg-0 py-xxl-3"
                                method="POST" action="{{ URL::to($slug . '/send_password') }}">
                                @csrf
                                <div class="col-md-10">
                                    <div class="mb-2 login-form-logo">
                                         <script>
                                            document.addEventListener("DOMContentLoaded", function(event) {
                                                if (localStorage.getItem('theme') === 'dark') {
                                                    var logo = "{{ helper::image_path(helper::appdata($storeinfo->id)->darklogo) }}";
                                                } else {
                                                    var logo = "{{ helper::image_path(helper::appdata($storeinfo->id)->logo) }}";
                                                }
                                                $('#logoimage').attr('src', logo);
                                            });
                                        </script>
                                        <a href="{{ URL::to($storeinfo->slug) }}">
                                            <img src="" alt="" id="logoimage">
                                        </a>
                                    </div>
                                    <h2 class="form-title color-changer">{{ trans('labels.forgot_password') }}</h2>
                                    <p class="page-subtitle line-limit-3 mb-3">{{ trans('labels.password_desc') }}</p>
                                </div>
                                <div class="col-md-10 mb-3">
                                    <label for="email" class="form-label">{{ trans('labels.email') }}<span
                                            class="text-danger"> * </span></label>
                                    <input type="email" class="form-control input-h" name="email"
                                        placeholder="{{ trans('labels.email') }}" id="email" required>
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <input type="hidden" class="form-control" name="type" value="user">
                                <div class="d-flex justify-content-center col-md-10 mb-3">
                                    <input class="btn-primary btn fs-15 fw-500 input-h w-100 text-center" type="submit"
                                        value="{{ trans('labels.submit') }}" />
                                </div>
                                <p class="page-subtitle text-center mt-3">Remember Password?
                                    <a href="{{ URL::to($slug . '/login') }}"
                                        class="text-primary fw-semibold text-dark color-changer">{{ trans('labels.login') }}</a>
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-8 col-lg-7 d-none d-lg-block p-0">
                <div class="left-side h-100 m-auto">
                    <img src="{{ helper::image_path(helper::appdata($vdata)->auth_page_image) }}" alt=""
                        class="w-100 h-100">
                </div>
            </div>
        </div>
    </section>
    <!--------------- mobile menu Section start ------------------>
    <div class="mobile-menu-footer d-lg-none">
        <ul class="d-flex align-items-center mobile-menu-active p-0 m-0">
            <li class="nav-link position-relative">
                <a class="{{ request()->is(@$storeinfo->slug) ? 'active' : '' }} {{ request()->is('/') ? 'active' : '' }}"
                    href="{{ URL::to(@$storeinfo->slug) }}">
                    <i class="fs-7 fa-light fa-house"></i>
                    <span class="act-8">{{ trans('labels.home') }}</span>
                </a>
            </li>
            <li class="nav-link position-relative">
                <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#searchModal">
                    <i class="fs-7 fa-light fa-search"></i>
                    <span class="act-8">{{ trans('labels.search') }}</span>
                </a>
            </li>
            @if (request()->route()->getName() == 'front.home')
                <li class="nav-link position-relative">
                    <a href="javascript:void(0)" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvasBottom" aria-controls="offcanvasBottom">
                        <i class="fs-7 fa-light fa-box-archive"></i>
                        <span class="act-8">{{ trans('labels.menu') }}</span>
                    </a>
                </li>
            @endif
            <li class="nav-link position-relative">
                <a href="{{ URL::to(@$storeinfo->slug . '/cart') }}"
                    class="{{ request()->is(@$storeinfo->slug . '/cart') ? 'active' : '' }}">
                    <i class="fs-7 fa-light fa-bag-shopping position-relative">
                        <div class="cart-3 mx-2 d-lg-none " id="cartcount_mobile">
                            {{ helper::getcartcount($storeinfo->id, @Auth::user()->id) }}</div>
                    </i>
                    <span>{{ trans('labels.menu_cart') }}</span>
                </a>
            </li>
            @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                    App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                <li class="nav-link position-relative">
                    @if (Auth::user() && Auth::user()->type == 3)
                        <a class="text-dark" href="{{ URL::to($storeinfo->slug . '/profile/') }}">
                            <i class="fs-7 fa-light fa-user"></i>
                            <span>{{ trans('labels.account') }}</span>
                        </a>
                    @else
                        @if (helper::appdata(@$storeinfo->id)->checkout_login_required == 1)
                            <a href="{{ URL::to($storeinfo->slug . '/login/') }}" class="text-dark">
                                <i class="fs-7 fa-light fa-user"></i>
                                <span>{{ trans('labels.account') }}</span></a>
                        @endif
                    @endif
                </li>
            @endif

        </ul>
    </div>
    <!--------------- mobile menu Section End ------------------>
</body>
<script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/jquery-3.6.3.min.js') }}"></script>
<script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/custom.js') }}"></script>
<script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/owl.carousel.min.js') }}"></script>

<script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/toastr/toastr.min.js') }}"></script><!-- Toastr JS -->

<script>
    function setLightMode() {
    document.documentElement.classList.remove('dark');
    document.documentElement.classList.add('light');
    localStorage.setItem('theme', 'light');
    }

    function setDarkMode() {
    document.documentElement.classList.remove('light');
    document.documentElement.classList.add('dark');
    localStorage.setItem('theme', 'dark');
    }
</script>

<script>
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
</script>

</html>
