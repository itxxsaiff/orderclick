<header>
    <div id="header1">
        <div class="header-main sticky-top">
            @if (env('Environment') == 'sendbox')
                <div class="sale">
                    <div class="container">
                        <div class="d-block d-md-flex justify-content-center align-items-center">
                            <p class="text-center"> <a href="https://1.envato.market/XxMgjX" target="_blank">This is a demo
                                    website - Buy genuine Restro SaaS using our official link! Click Now >>> Buy Now</a>
                            </p>
                        </div>
                    </div>
                </div>
            @endif
            <nav class="navbar navbar-expand-lg py-2">
                <div class="container">
                    <div class="d-flex gap-3 align-items-center">
                        <div class="d-lg-none">
                            <button class="btn bg-transparent btn-group border-0 text-white p-0 m-0" type="button"
                                data-bs-toggle="offcanvas" data-bs-target="#footersiderbar"
                                aria-controls="footersiderbar">
                                <i class="fa-solid fa-bars fs-3"></i>
                            </button>
                        </div>
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
                        <a href="{{ URL::to(@$storeinfo->slug) }}" class="logo">
                            <img src="" alt="" id="logoimage">
                        </a>
                    </div>
                    <div class="collapse navbar-collapse header-menu-items justify-content-end gap-3"
                        id="navbarSupportedContent">
                        <ul class="navbar-nav">
                            <li class="nav-item px-xl-4 px-lg-3">
                                <a class="nav-link {{ request()->is(@$storeinfo->slug) ? 'active' : '' }} {{ request()->is('/') ? 'active' : '' }}"
                                    href="{{ URL::to(@$storeinfo->slug) }}">
                                    {{ trans('labels.home') }}
                                </a>
                            </li>
                            <li class="nav-item px-xl-4 px-lg-3">
                                <a class="nav-link {{ request()->is(@$storeinfo->slug . '/aboutus') ? 'active' : '' }} {{ request()->is('aboutus') ? 'active' : '' }}"
                                    href="{{ URL::to(@$storeinfo->slug . '/aboutus') }}">
                                    {{ trans('labels.about_us') }}
                                </a>
                            </li>
                            <li class="nav-item px-xl-4 px-lg-3">
                                <a class="nav-link {{ request()->is(@$storeinfo->slug . '/contact') ? 'active' : '' }} {{ request()->is('contact') ? 'active' : '' }}"
                                    href="{{ URL::to(@$storeinfo->slug . '/contact') }}">
                                    {{ trans('labels.contact_us') }}
                                </a>
                            </li>
                            <li class="nav-item px-xl-4 px-lg-3">
                                <a href="javascript:void(0)" class="nav-link" data-bs-toggle="modal"
                                    data-bs-target="#searchModal">
                                    {{ trans('labels.search') }}
                                </a>
                            </li>
                            <li class="nav-item px-xl-4 px-lg-3">
                                <div class="d-flex align-items-center">
                                    <a class="nav-link position-relative {{ request()->is(@$storeinfo->slug . '/cart') ? 'active' : '' }} {{ request()->is('cart') ? 'active' : '' }}"
                                        href="{{ URL::to(@$storeinfo->slug . '/cart') }}">
                                        <span>
                                            {{ trans('labels.my_cart') }}
                                        </span>
                                        <a class="cart-counting"
                                            id="cartcount">{{ helper::getcartcount($storeinfo->id, @Auth::user()->id) }}</a>
                                    </a>
                                </div>
                            </li>
                        </ul>
                        <div class="d-flex gap-3">
                            @php
                                $languages = explode('|', helper::appdata(@$storeinfo->id)->languages);
                                $currency = explode('|', helper::appdata(@$storeinfo->id)->currencies);
                            @endphp
                            @if (App\Models\SystemAddons::where('unique_identifier', 'language')->first() != null &&
                                    App\Models\SystemAddons::where('unique_identifier', 'language')->first()->activated == 1)
                                @if (count($languages) > 1)
                                    <div class="btn-group lag-btn">
                                        <a class="nav-link d-flex align-items-center" href="#" role="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <img src="{{ helper::image_path(session()->get('flag')) }}" alt=""
                                                class="language-dropdown-image">
                                        </a>
                                        <ul
                                            class="dropdown-menu bg-body-secondary p-0 mt-2 shadow overflow-hidden border-0 {{ session()->get('direction') == 2 ? 'min-dropdown-rtl' : 'min-dropdown-ltr' }}">

                                            @foreach (helper::available_language(@$storeinfo->id) as $languagelist)
                                                @if (in_array($languagelist->code, explode('|', helper::appdata(@$storeinfo->id)->languages)))
                                                    <li>
                                                        <a class="dropdown-item language-items p-2 d-flex align-items-center gap-2"
                                                            href="{{ URL::to('/lang/change?lang=' . $languagelist->code) }}">
                                                            <img src="{{ helper::image_path($languagelist->image) }}"
                                                                alt="" class="language-items-img">
                                                            <span>{{ $languagelist->name }}</span>
                                                        </a>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            @endif
                            @if (App\Models\SystemAddons::where('unique_identifier', 'currency_settigns')->first() != null &&
                                    App\Models\SystemAddons::where('unique_identifier', 'currency_settigns')->first()->activated == 1)
                                @if (count($currency) > 1)
                                    <div class="btn-group lag-btn">
                                        <a class="color-changer d-flex align-items-center" href="#" role="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <p class="fs-5 m-0 text-white language-dropdown-image d-flex justify-content-center align-items-center bg-secondary">
                                                {{ session()->get('currency') }}
                                            </p>
                                        </a>
                                        <ul
                                            class="dropdown-menu bg-body-secondary p-0 mt-2 shadow overflow-hidden border-0 {{ session()->get('direction') == 2 ? 'min-dropdown-rtl' : 'min-dropdown-ltr' }}">
                                            @foreach (helper::available_currency() as $currencylist)
                                                @if (in_array($currencylist->code, explode('|', helper::appdata($storeinfo->id)->currencies)))
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center gap-2 p-2"
                                                            href="{{ URL::to('/currency/change?currency=' . $currencylist['code']) }}">
                                                            <p>{{ $currencylist['currency'] }}</p>
                                                            <p>{{ $currencylist['name'] }}</p>
                                                        </a>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            @endif

                            <div class="lag-btn btn-group">
                                <a class="d-flex align-items-center color-changer" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <p class="fs-5 m-0 text-white d-flex align-items-center language-dropdown-image justify-content-center bg-secondary">
                                        <i class="fa-regular fa-circle-half-stroke"></i>
                                    </p>
                                </a>
                                <ul
                                    class="dropdown-menu p-0 bg-body-secondary border-0 shadow mt-2 {{ session()->get('direction') == 2 ? 'min-dropdown-rtl' : 'min-dropdown-ltr' }}">
                                    <li>
                                        <a class="dropdown-item d-flex cursor-pointer align-items-center p-2 gap-2"
                                            onclick="setLightMode()">
                                            <i class="fa-light fa-lightbulb"></i>
                                            <span class="fs-7">{{ trans('labels.light') }}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex cursor-pointer align-items-center p-2 gap-2"
                                            onclick="setDarkMode()">
                                            <i class="fa-solid fa-moon"></i>
                                            <span class="fs-7">{{ trans('labels.dark') }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                    App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                @if (Auth::user() && Auth::user()->type == 3)
                                    <a class="d-flex align-items-center d-none d-lg-block text-white"
                                        href="{{ URL::to($storeinfo->slug . '/profile/') }}">
                                        <img src="{{ helper::image_path(@Auth::user()->image) }}" alt=""
                                            class="profile_image">
                                    </a>
                                @else
                                    @if (helper::appdata(@$storeinfo->id)->checkout_login_required == 1)
                                        <a href="{{ URL::to($storeinfo->slug . '/login/') }}"
                                            class="login-buuton px-sm-4 px-3 py-2 fs-15 fw-500 d-none m-0 d-lg-block">{{ trans('labels.login') }}</a>
                                    @endif
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="d-lg-none">
                        <div class="d-flex gap-3">
                            @php
                                $languages = explode('|', helper::appdata(@$storeinfo->id)->languages);
                                $currency = explode('|', helper::appdata(@$storeinfo->id)->currencies);
                            @endphp
                            @if (App\Models\SystemAddons::where('unique_identifier', 'language')->first() != null &&
                                    App\Models\SystemAddons::where('unique_identifier', 'language')->first()->activated == 1)
                                @if (count($languages) > 1)
                                    <div class="btn-group lag-btn">
                                        <a class="nav-link d-flex align-items-center" href="#" role="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <img src="{{ helper::image_path(session()->get('flag')) }}" alt=""
                                                class="language-dropdown-image">
                                        </a>
                                        <ul
                                            class="dropdown-menu bg-body-secondary p-0 mt-2 shadow border-0 {{ session()->get('direction') == 2 ? 'min-dropdown-rtl' : 'min-dropdown-ltr' }}">

                                            @foreach (helper::available_language(@$storeinfo->id) as $languagelist)
                                                @if (in_array($languagelist->code, explode('|', helper::appdata(@$storeinfo->id)->languages)))
                                                    <li>
                                                        <a class="dropdown-item language-items p-2 d-flex align-items-center gap-2"
                                                            href="{{ URL::to('/lang/change?lang=' . $languagelist->code) }}">
                                                            <img src="{{ helper::image_path($languagelist->image) }}"
                                                                alt="" class="language-items-img">
                                                            <span>{{ $languagelist->name }}</span>
                                                        </a>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            @endif
                            @if (App\Models\SystemAddons::where('unique_identifier', 'currency_settigns')->first() != null &&
                                    App\Models\SystemAddons::where('unique_identifier', 'currency_settigns')->first()->activated == 1)
                                @if (count($currency) > 1)
                                    <div class="btn-group lag-btn">

                                        <a class="nav-link d-flex align-items-center" href="#" role="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <p class="fs-5 m-0 color-changer language-dropdown-image d-flex justify-content-center align-items-center bg-secondary">
                                                {{ session()->get('currency') }}
                                            </p>
                                        </a>
                                        <ul
                                            class="dropdown-menu bg-body-secondary p-0 mt-2 shadow border-0 {{ session()->get('direction') == 2 ? 'min-dropdown-rtl' : 'min-dropdown-ltr' }}">
                                            @foreach (helper::available_currency() as $currencylist)
                                                @if (in_array($currencylist->code, explode('|', helper::appdata($storeinfo->id)->currencies)))
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center gap-2 p-2"
                                                            href="{{ URL::to('/currency/change?currency=' . $currencylist['code']) }}">
                                                            <p>{{ $currencylist['currency'] }}</p>
                                                            <p>{{ $currencylist['name'] }}</p>
                                                        </a>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</header>



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
