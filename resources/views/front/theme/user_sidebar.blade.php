<div class="d-lg-none mb-2">

    <div class="setting-page-profile-mobile border d-flex gap-3 align-items-center bg-white bg-change-mode rounded p-2 mb-2">

        <img src="{{ helper::image_path(@Auth::user()->image) }}" alt="">

        <div class="">

            <h5 class="mb-1 color-changer">{{ @Auth::user()->name }}</h5>

            <a class="color-changer">{{ @Auth::user()->email }}</a>

        </div>

    </div>

    <div class="accordion accordion-flush d-lg-none" id="mobileaccountmenu">

        <div class="accordion-item  bg-transparent border rounded overflow-hidden my-0">

            <h2 class="accordion-header">

                <button
                    class="accordion-button fw-500 bg-white bg-change-mode accordion_button d-flex gap-2 align-items-center collapsed {{ session()->get('direction') == 2 ? 'rtl' : '' }}"
                    type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false"
                    aria-controls="flush-collapseOne">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-light fa-bars-staggered"></i>

                        <p class="fw-600">Dashboard Navigation</p>
                    </div>

                </button>

            </h2>

            <div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#mobileaccountmenu">

                <div class="accordion-body border-top">

                    <!--------- ACCOUNT MENU --------->

                    <div class="account_menu">

                        <p class="setting-left-sidetitle mt-0">{{ trans('labels.account') }}</p>

                        <ul class="setting-left-sidebar mt-0">

                            <li>

                                <a href="{{ URL::to($storeinfo->slug . '/profile/') }}">

                                    <i class="fa-regular fa-user color-changer"></i>

                                    <span class="px-3 color-changer">{{ trans('labels.profile') }}</span>

                                </a>

                            </li>

                            <li>

                                @if (@Auth::user()->google_id == '' && @Auth::user()->facebook_id == '')
                                    <a href="{{ URL::to($storeinfo->slug . '/change-password/') }}">

                                        <i class="fa-solid fa-lock color-changer"></i>

                                        <span class="px-3 color-changer">{{ trans('labels.change_password') }}</span>

                                    </a>
                                @endif

                            </li>

                        </ul>

                        <p class="setting-left-sidetitle mt-0">{{ trans('labels.dashboard') }}</p>

                        <ul class="setting-left-sidebar mt-0">

                            <li>

                                <a href="{{ URL::to($storeinfo->slug . '/orders/') }}">

                                    <i class="fa-solid fa-cart-shopping color-changer"></i>

                                    <span class="px-3 color-changer">{{ trans('labels.orders') }}</span>

                                </a>

                            </li>

                            @if (App\Models\SystemAddons::where('unique_identifier', 'loyalty_program')->first() != null &&
                                    App\Models\SystemAddons::where('unique_identifier', 'loyalty_program')->first()->activated == 1)
                                <li>

                                    <a href="{{ URL::to($storeinfo->slug . '/loyality/') }}">

                                        <i class="fa-solid fa-trophy color-changer"></i>

                                        <span class="px-3 color-changer">{{ trans('labels.loyalty_program') }}</span>

                                    </a>

                                </li>
                            @endif

                            <li>

                                <a href="{{ URL::to($storeinfo->slug . '/favorites/') }}">

                                    <i class="fa-regular fa-heart color-changer"></i>

                                    <span class="px-3 color-changer">{{ trans('labels.favourites') }}</span>

                                </a>

                            </li>

                            <li>

                                <a href="{{ URL::to($storeinfo->slug . '/wallet/') }}">

                                    <i class="fa-solid fa-wallet color-changer"></i>

                                    <span class="px-3 color-changer">{{ trans('labels.wallet') }}</span>

                                </a>

                            </li>
                            <li>

                                <a href="{{ URL::to($storeinfo->slug . '/delete-password/') }}">

                                    <i class="fa-light fa-trash color-changer"></i>

                                    <span class="px-3 color-changer">{{ trans('labels.delete_profile') }}</span>

                                </a>

                            </li>

                            <li class="cursor-pointer">

                                <a onclick="statusupdate('{{ URL::to($storeinfo->slug . '/logout/') }}')">

                                    <i class="fa-solid fa-right-from-bracket color-changer"></i>

                                    <span class="px-3 color-changer">{{ trans('labels.logout') }}</span>

                                </a>

                            </li>

                        </ul>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>



<div class="col-md-3 d-lg-block d-none">
    <div class="card rounded">
        <div class="card-body">
            <div class="setting-page-profile pb-3 border-bottom text-center">

                <img src="{{ helper::image_path(@Auth::user()->image) }}" alt="" class="mb-3 mx-auto">

                <h3 class="mb-1 color-changer">{{ @Auth::user()->name }}</h3>

                <a>{{ @Auth::user()->email }}</a>

            </div>
            <p class="setting-left-sidetitle">{{ trans('labels.account') }}</p>
            <ul class="setting-left-sidebar">

                <li>

                    <a class="d-flex gap-2 align-items-center m-0 fs-15"
                        href="{{ URL::to($storeinfo->slug . '/profile/') }}">

                        <i class="fa-solid fa-user color-changer"></i>

                        <span class="fs-7 text-dark color-changer">{{ trans('labels.profile') }}</span>

                    </a>

                </li>

                <li>

                    @if (@Auth::user()->google_id == '' && @Auth::user()->facebook_id == '')
                        <a class="d-flex gap-2 align-items-center m-0 fs-15"
                            href="{{ URL::to($storeinfo->slug . '/change-password/') }}">

                            <i class="fa-solid fa-lock color-changer"></i>

                            <span class="fs-7 text-dark color-changer">{{ trans('labels.change_password') }}</span>

                        </a>
                    @endif

                </li>

            </ul>
            <p class="setting-left-sidetitle">{{ trans('labels.dashboard') }}</p>
            <ul class="setting-left-sidebar">

                <li>

                    <a class="d-flex gap-2 align-items-center m-0 fs-15"
                        href="{{ URL::to($storeinfo->slug . '/orders/') }}">

                        <i class="fa-solid fa-cart-shopping color-changer"></i>

                        <span class="fs-7 text-dark color-changer">{{ trans('labels.orders') }}</span>

                    </a>

                </li>

                @if (App\Models\SystemAddons::where('unique_identifier', 'loyalty_program')->first() != null &&
                        App\Models\SystemAddons::where('unique_identifier', 'loyalty_program')->first()->activated == 1)
                    <li>

                        <a class="d-flex gap-2 align-items-center m-0 fs-15"
                            href="{{ URL::to($storeinfo->slug . '/loyality/') }}">

                            <i class="fa-solid fa-trophy color-changer"></i>

                            <span class="fs-7 text-dark color-changer">{{ trans('labels.loyalty_program') }}</span>

                        </a>

                    </li>
                @endif

                <li>

                    <a class="d-flex gap-2 align-items-center m-0 fs-15"
                        href="{{ URL::to($storeinfo->slug . '/favorites/') }}">

                        <i class="fa-solid fa-heart color-changer"></i>

                        <span class="fs-7 text-dark color-changer">{{ trans('labels.favourites') }}</span>

                    </a>

                </li>
                <li>

                    <a class="d-flex gap-2 align-items-center m-0 fs-15"
                        href="{{ URL::to($storeinfo->slug . '/wallet/') }}">

                        <i class="fa-solid fa-wallet color-changer"></i>

                        <span class="fs-7 text-dark color-changer">{{ trans('labels.wallet') }}</span>

                    </a>

                </li>
                <li>

                    <a class="d-flex gap-2 align-items-center m-0 fs-15"
                        href="{{ URL::to($storeinfo->slug . '/delete-password/') }}">

                        <i class="fa-solid fa-trash color-changer"></i>

                        <span class="fs-7 text-dark color-changer">{{ trans('labels.delete_profile') }}</span>

                    </a>

                </li>
                <li class="cursor-pointer">

                    <a class="d-flex gap-2 align-items-center m-0 fs-15"
                        onclick="statusupdate('{{ URL::to($storeinfo->slug . '/logout/') }}')">

                        <i class="fa-solid fa-right-from-bracket color-changer"></i>

                        <span class="fs-7 text-dark color-changer">{{ trans('labels.logout') }}</span>

                    </a>

                </li>

            </ul>
        </div>
    </div>
</div>
