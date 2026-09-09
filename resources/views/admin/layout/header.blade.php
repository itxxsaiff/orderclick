@php
if (Auth::user()->type == 4) {
$vendor_id = Auth::user()->vendor_id;
} else {
$vendor_id = Auth::user()->id;
}
$user = App\Models\User::where('id', $vendor_id)->first();
@endphp
<header class="page-topbar border-bottom">
    <div class="navbar-header">
        <button class="navbar-toggler d-lg-none d-md-block px-2 px-md-3" data-bs-toggle="offcanvas"
            href="#offcanvasExample" role="button" aria-controls="offcanvasExample">
            <i class="fa-regular fa-bars fs-4"></i>
        </button>

        <div class="d-flex align-items-center gap-2">
            @if (session('vendor_login'))
            <a href="{{ URL::to('/admin/admin_back') }}" class="header_icon_box bg-success">
                <i class="fa-solid fa-desktop text-white"></i>
            </a>
            @endif
            @if (Auth::user()->type == 2)
            <a class="header_icon_box bg-warning"
                href="@if (helper::checkcustomdomain($vendor_id) == null) {{ URL::to('/' . $user->slug) }} @else {{ 'https://' . helper::checkcustomdomain($vendor_id) }} @endif"
                target="_blank">
                <i class="fa-solid fa-link text-white"></i>
            </a>
            @endif

            <!-- dekstop-tablet-mobile-language-dropdown-button-start-->
            @if (App\Models\SystemAddons::where('unique_identifier', 'language')->first() != null &&
            App\Models\SystemAddons::where('unique_identifier', 'language')->first()->activated == 1)

            <div class="position-relative">
                <div class="dropdown lag-btn">
                    <a class="btn-sm border-0" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <img src="{{ helper::image_path(session()->get('flag')) }}" alt=""
                            class="language-dropdown">
                    </a>
                    <ul
                        class="dropdown-menu shadow mt-2 border-0 p-0 bg-body-secondary overflow-hidden {{ session()->get('direction') == 2 ? 'min-dropdown-rtl' : 'min-dropdown-ltr' }}">
                        @foreach (helper::listoflanguage() as $languagelist)
                        <li>
                            <a class="dropdown-item d-flex p-2 gap-2 align-items-center"
                                href="{{ URL::to('/lang/change?lang=' . $languagelist->code) }}">
                                <img src="{{ helper::image_path($languagelist->image) }}" alt=""
                                    class="img-fluid">
                                <span>{{ $languagelist->name }}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
            <div class="lag-btn btn-group">
                <a class="nav-link d-flex align-items-center" href="#" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <p class="fs-5 m-0 text-white d-flex align-items-center language-dropdown justify-content-center bg-secondary">
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
            @if (App\Models\SystemAddons::where('unique_identifier', 'currency_settigns')->first() != null &&
            App\Models\SystemAddons::where('unique_identifier', 'currency_settigns')->first()->activated == 1)

            <div class="position-relative">
                <div class="dropdown lag-btn">
                    <a class="btn-sm border-0" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <p class="fs-5 m-0 text-white d-flex align-items-center language-dropdown justify-content-center bg-secondary">
                            {{ session()->get('currency') }}

                        </p>
                    </a>
                    <ul
                        class="dropdown-menu shadow mt-2 border-0 p-0 bg-body-secondary overflow-hidden {{ session()->get('direction') == 2 ? 'min-dropdown-rtl' : 'min-dropdown-ltr' }}">
                        @foreach (helper::available_currency() as $currencylist)
                        <li>
                            <a class="dropdown-item d-flex p-2 gap-2 align-items-center"
                                href="{{ URL::to('/currency/change?currency=' . $currencylist['code']) }}">
                                <span>
                                    {{ $currencylist['currency'] . '  ' . $currencylist['name'] }}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
            <!-- dekstop-tablet-mobile-language-dropdown-button-end-->

            <div class="dropwdown lag-btn d-inline-block">
                <button class="btn p-0 header-item" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ helper::image_path(Auth::user()->image) }}">
                    <span class="d-none d-xxl-inline-block d-xl-inline-block ms-1 color-changer">{{ Auth::user()->name }}</span>
                    <i class="fa-regular fa-angle-down d-none d-xxl-inline-block color-changer d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu shadow border-0 p-0 bg-body-secondary overflow-hidden">
                    <a href="{{ URL::to('admin/settings') }}"
                        class="dropdown-item d-flex align-items-center p-2 gap-2">
                        <i class="fa-solid fa-gear fs-7"></i>
                        <span>{{ trans('labels.setting') }}</span>
                    </a>

                    <a href="javascript:void(0)" onclick="statusupdate('{{ URL::to('/admin/logout') }}')"
                        class="dropdown-item d-flex align-items-center p-2 gap-2">
                        <i class="fa-solid fa-right-from-bracket fs-7"></i>
                        <span>{{ trans('labels.logout') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>