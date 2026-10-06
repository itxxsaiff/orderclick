@php
    if (Auth::user()->type == 4) {
        $role_id = Auth::user()->role_id;
        $vendor_id = Auth::user()->vendor_id;
    } else {
        $role_id = '';
        $vendor_id = Auth::user()->id;
    }
    $user = App\Models\User::where('id', $vendor_id)->first();
    // V2: current store's business type — drives which "order channel" menu shows.
    $oc_btype = optional(App\Models\Settings::where('vendor_id', $vendor_id)->first())->business_type;
    $oc_is_clinic = $oc_btype === 'clinic';
    $oc_is_salon = $oc_btype === 'salon';
    // Clinics & salons use the appointment (booking) channel too, plus a specialists manager.
    $oc_is_booking = $oc_btype === 'booking' || $oc_is_clinic || $oc_is_salon;
    $oc_is_service = $oc_btype === 'service';
    // Orders is the default channel for product stores (food/grocery/retail/pharmacy) + super-admin.
    $oc_is_orders = !$oc_is_booking && !$oc_is_service;
@endphp
<ul class="navbar-nav mx-xl-3 mx-lg-2">
    <li class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_dashboard') == 1 ? 'd-block' : 'd-none' }}">
        <a class="nav-link d-flex align-items-center  {{ request()->is('admin/dashboard') ? 'active' : '' }}"
            aria-current="page" href="{{ URL::to('admin/dashboard') }}">
            <span class="{{ request()->is('admin/dashboard') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                <i class="fa-solid fa-desktop"></i>
            </span>
            <span class="px-2">{{ trans('labels.dashboard') }}</span>
        </a>
    </li>
    {{-- V2: Account setup — System/Activity/Specialization and website activation. Vendors only. --}}
    @if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1))
        @php $oc_setup_vendor = \App\Models\User::find(Auth::user()->type == 4 ? Auth::user()->vendor_id : Auth::user()->id); @endphp
        <li class="nav-item mb-2 fs-7">
            <a class="nav-link d-flex align-items-center justify-content-between {{ request()->is('admin/setup*') ? 'active' : '' }}"
                aria-current="page" href="{{ URL::to('admin/setup') }}">
                <div class="d-flex align-items-center">
                    <span class="{{ request()->is('admin/setup*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                        <i class="fa-solid fa-circle-check"></i>
                    </span>
                    <span class="px-2">{{ trans('labels.account_setup') }}</span>
                </div>
                @if (!\App\Helpers\Systems::isLive($oc_setup_vendor))
                    <span class="badge bg-warning">!</span>
                @endif
            </a>
        </li>
    @endif
    {{-- WhatsApp Cloud API: connection, inbox and the shared AI knowledge base.
         Super admin always; merchants only when WHATSAPP_TOOLS=true (config/services.php). --}}
    @if (\App\Models\WhatsappSetting::toolsEnabledFor($vendor_id))
    <li class="nav-item mb-2 fs-7 dropdown multimenu">
        <a class="nav-link collapsed d-flex align-items-center justify-content-between dropdown-toggle mb-1 {{ request()->is('admin/whatsapp*') ? 'active' : '' }}"
            href="#ocwa" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="ocwa">
            <div class="d-flex align-items-center">
                <span class="{{ request()->is('admin/whatsapp*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                    <i class="fa-brands fa-whatsapp"></i>
                </span>
                <span class="multimenu-title px-2">WhatsApp</span>
            </div>
        </a>
        <ul class="collapse" id="ocwa">
            @php $ocWaUnread = \App\Models\WhatsappConversation::where('vendor_id', $vendor_id)->sum('unread'); @endphp
            <li class="nav-item ps-4 mb-1">
                <a class="nav-link {{ request()->is('admin/whatsapp/conversations*') ? 'active' : '' }}"
                    href="{{ URL::to('admin/whatsapp/conversations') }}">
                    <span class="d-flex align-items-center multimenu-menu-indicator">
                        <i class="fa-solid fa-circle-small"></i>{{ trans('labels.whatsapp_inbox') }}
                        @if ($ocWaUnread)<span class="badge bg-danger ms-1">{{ $ocWaUnread }}</span>@endif
                    </span>
                </a>
            </li>
            <li class="nav-item ps-4 mb-1">
                <a class="nav-link {{ request()->is('admin/whatsapp/knowledge*') ? 'active' : '' }}"
                    href="{{ URL::to('admin/whatsapp/knowledge') }}">
                    <span class="d-flex align-items-center multimenu-menu-indicator">
                        <i class="fa-solid fa-circle-small"></i>{{ trans('labels.ai_knowledge_base') }}</span>
                </a>
            </li>
            <li class="nav-item ps-4 mb-1">
                <a class="nav-link {{ request()->is('admin/whatsapp/settings*') ? 'active' : '' }}"
                    href="{{ URL::to('admin/whatsapp/settings') }}">
                    <span class="d-flex align-items-center multimenu-menu-indicator">
                        <i class="fa-solid fa-circle-small"></i>{{ trans('labels.whatsapp_integration') }}</span>
                </a>
            </li>
        </ul>
    </li>
    @endif

    {{-- V2: vendor Locations & Branches — GPS/map driven. --}}
    @if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1))
        <li class="nav-item mb-2 fs-7">
            <a class="nav-link d-flex align-items-center {{ request()->is('admin/branches*') ? 'active' : '' }}"
                aria-current="page" href="{{ URL::to('admin/branches') }}">
                <span class="{{ request()->is('admin/branches*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                    <i class="fa-solid fa-location-dot"></i>
                </span>
                <span class="px-2">{{ trans('labels.locations_branches') }}</span>
            </a>
        </li>
    @endif
    @if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1))
        <li
            class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_addons_manager') == 1 ? 'd-block' : 'd-none' }}">
            <a class="nav-link d-flex align-items-center justify-content-between {{ request()->is('admin/apps*') ? 'active' : '' }}"
                aria-current="page" href="{{ URL::to('/admin/apps') }}">
                <div class="d-flex align-items-center">
                    <span class="{{ request()->is('admin/apps*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                        <i class="fa-solid fa-puzzle-piece"></i>
                    </span>
                    <span class="px-2">{{ trans('labels.addons_manager') }}</span>
                </div>
                <span class="rainbowText float-right">Premium</span>
            </a>
        </li>
    @endif
    @if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1))
        <li
            class="nav-item mt-3 {{ helper::check_menu($role_id, 'role_orders') == 1 || helper::check_menu($role_id, 'role_report') == 1 || false && helper::check_menu($role_id, 'role_pos') == 1 ? 'd-block' : 'd-none' }}">
            <h6 class="text-dark color-changer fw-500 mb-2 fs-7 text-uppercase mx-3">{{ trans('labels.orders_management') }}</h6>
        </li>

        @if (App\Models\SystemAddons::where('unique_identifier', 'subscription')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'subscription')->first()->activated == 1)
            @if (App\Models\SystemAddons::where('unique_identifier', 'pos')->first() != null &&
                    App\Models\SystemAddons::where('unique_identifier', 'pos')->first()->activated == 1)
                @php
                    if ($user->allow_without_subscription == 1) {
                        $pos = 1;
                    } else {
                        $pos = @helper::get_plan($vendor_id)->pos;
                    }
                @endphp

                @if ($pos == 1)
                    <li
                        class="nav-item mb-2 fs-7 {{ false && helper::check_menu($role_id, 'role_pos') == 1 ? 'd-block' : 'd-none' }}">
                        <a class="nav-link d-flex align-items-center justify-content-between {{ request()->is('admin/pos*') ? 'active' : '' }}"
                            aria-current="page" href="{{ URL::to('admin/pos') }}">
                            <div class="d-flex align-items-center">
                                <span class="{{ request()->is('admin/pos*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                                    <!-- <i class="fa-solid fa-users"></i> -->
                                    <i class="fa-solid fa-cash-register"></i>
                                </span>
                                <span class="nav-text px-2">{{ trans('labels.pos') }}</span>
                            </div>
                            @if (env('Environment') == 'sendbox')
                                <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                            @endif
                        </a>
                    </li>
                @endif
            @endif
        @else
            @if (App\Models\SystemAddons::where('unique_identifier', 'pos')->first() != null &&
                    App\Models\SystemAddons::where('unique_identifier', 'pos')->first()->activated == 1)
                <li
                    class="nav-item mb-2 fs-7 {{ false && helper::check_menu($role_id, 'role_pos') == 1 ? 'd-block' : 'd-none' }}">
                    <a class="nav-link d-flex align-items-center justify-content-between {{ request()->is('admin/pos*') ? 'active' : '' }}"
                        aria-current="page" href="{{ URL::to('admin/pos') }}">
                        <div class="d-flex align-items-center">
                            <span class="{{ request()->is('admin/pos*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                                <i class="fa-solid fa-cash-register"></i>
                            </span>
                            <span class="nav-text px-2">{{ trans('labels.pos') }}</span>
                        </div>
                        @if (env('Environment') == 'sendbox')
                            <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                        @endif
                    </a>
                </li>
            @endif
        @endif
        {{-- Orders: only for product stores (food/grocery/retail) + super-admin --}}
        @if ($oc_is_orders)
            <li class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_orders') == 1 ? 'd-block' : 'd-none' }}">
                <a class="nav-link d-flex d-flex align-items-center {{ request()->is('admin/orders*') ? 'active' : '' }}"
                    href="{{ URL::to('/admin/orders') }}" aria-expanded="false">
                    <span class="{{ request()->is('admin/orders*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                        <i class="fa-solid fa-list-check"></i>
                    </span>
                    <span class="nav-text px-2">{{ trans('labels.orders') }}</span>
                </a>
            </li>
        @endif
        {{-- Bookings: only for booking-type stores (clinics/salons/gyms) --}}
        @if ($oc_is_booking)
            <li class="nav-item mb-2 fs-7">
                <a class="nav-link d-flex align-items-center {{ request()->is('admin/bookings*') ? 'active' : '' }}"
                    href="{{ URL::to('/admin/bookings') }}" aria-expanded="false">
                    <span class="{{ request()->is('admin/bookings*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                        <i class="fa-solid fa-calendar-check"></i>
                    </span>
                    <span class="nav-text px-2">{{ trans('labels.bookings') }}</span>
                </a>
            </li>
        @endif
        {{-- Service Requests: only for service-type stores (cleaning/maintenance/plumbing) --}}
        @if ($oc_is_service)
            <li class="nav-item mb-2 fs-7">
                <a class="nav-link d-flex align-items-center {{ request()->is('admin/service-requests*') ? 'active' : '' }}"
                    href="{{ URL::to('/admin/service-requests') }}" aria-expanded="false">
                    <span class="{{ request()->is('admin/service-requests*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                        <i class="fa-solid fa-clipboard-list"></i>
                    </span>
                    <span class="nav-text px-2">{{ trans('labels.service_requests') }}</span>
                </a>
            </li>
        @endif
        <li class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_report') == 1 ? 'd-block' : 'd-none' }}">
            <a class="nav-link d-flex d-flex align-items-center {{ request()->is('admin/report*') ? 'active' : '' }}"
                href="{{ URL::to('/admin/report') }}" aria-expanded="false">
                <span class="{{ request()->is('admin/report*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                    <i class="fa-solid fa-chart-pie"></i>
                </span>
                <span class="nav-text px-2">{{ trans('labels.report') }}</span>
            </a>
        </li>
    @endif
    @if (Auth::user()->type == 1 ||
            (Auth::user()->type == 4 && Auth::user()->vendor_id == 1) ||
            ((Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1)) &&
                (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                    App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)))
        <li
            class="nav-item mt-3 {{ helper::check_menu($role_id, 'role_vendors') == 1 || helper::check_menu($role_id, 'role_customers') == 1 ? 'd-block' : 'd-none' }}">
            <h6 class="text-dark color-changer fw-500 mb-2 fs-7 text-uppercase mx-3">{{ trans('labels.user_management') }}</h6>
        </li>

        @if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1))
            <li
                class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_vendors') == 1 ? 'd-block' : 'd-none' }}">
                <a class="nav-link d-flex align-items-center  {{ request()->is('admin/users*') ? 'active' : '' }}"
                    aria-current="page" href="{{ URL::to('admin/users') }}">
                    <span class="{{ request()->is('admin/users*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                        <i class="fa-solid fa-user-plus"></i>
                    </span>
                    <span class="px-2">
                        {{ trans('labels.users') }}
                    </span>
                </a>
            </li>
        @endif
        @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
            <li
                class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_customers') == 1 ? 'd-block' : 'd-none' }}">
                <a class="nav-link d-flex align-items-center justify-content-between {{ request()->is('admin/customers*') ? 'active' : '' }}"
                    aria-current="page" href="{{ URL::to('admin/customers') }}">
                    <div class="d-flex align-items-center">
                        <div class="{{ request()->is('admin/customers') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <span class="nav-text px-2">{{ trans('labels.customers') }}</span>
                    </div>
                    @if (env('Environment') == 'sendbox')
                        <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                    @endif
                </a>
            </li>
        @endif
    @endif

    @if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1))
        @if ($oc_is_booking)
            {{-- Booking stores: a Services catalog instead of Products --}}
            <li class="nav-item mt-3">
                <h6 class="text-dark color-changer fw-500 mb-2 fs-7 text-uppercase mx-3">{{ trans('labels.booking_management') }}</h6>
            </li>
            <li class="nav-item mb-2 fs-7">
                <a class="nav-link d-flex align-items-center {{ request()->is('admin/booking-services*') ? 'active' : '' }}"
                    href="{{ URL::to('admin/booking-services') }}" aria-current="page">
                    <span class="{{ request()->is('admin/booking-services*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                        <i class="fa-solid fa-hand-holding-medical"></i>
                    </span>
                    <span class="px-2">{{ trans('labels.services') }}</span>
                </a>
            </li>
            {{-- Clinics: Doctors manager · Salons: Team/specialists manager (same doctors module) --}}
            @if ($oc_is_clinic || $oc_is_salon)
                <li class="nav-item mb-2 fs-7">
                    <a class="nav-link d-flex align-items-center {{ request()->is('admin/doctors*') ? 'active' : '' }}"
                        href="{{ URL::to('admin/doctors') }}" aria-current="page">
                        <span class="{{ request()->is('admin/doctors*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                            <i class="fa-solid {{ $oc_is_salon ? 'fa-scissors' : 'fa-user-doctor' }}"></i>
                        </span>
                        <span class="px-2">
                            @if ($oc_is_salon)
                                {{ trans('labels.team') }}
                            @else
                                {{ trans('labels.doctors') }}
                            @endif
                        </span>
                    </a>
                </li>
            @endif
        @else
            <li
                class="nav-item mt-3 {{ helper::check_menu($role_id, 'role_categories') == 1 || helper::check_menu($role_id, 'role_products') == 1 || false && helper::check_menu($role_id, 'role_global_extras') == 1 || false && helper::check_menu($role_id, 'role_import_product') == 1 || false && helper::check_menu($role_id, 'role_product_review') == 1 || helper::check_menu($role_id, 'role_shipping_management') == 1 || false && helper::check_menu($role_id, 'role_question_answer') == 1 ? 'd-block' : 'd-none' }}">
                <h6 class="text-dark color-changer fw-500 mb-2 fs-7 text-uppercase mx-3">{{ trans('labels.product_managment') }}</h6>
            </li>
        @endif
        {{-- AI store builder: no longer forced on the merchant after login, so it needs a
             permanent home in the menu. --}}
        {{-- AI store designer: the store's whole look comes from here (no template picking). --}}
        @if (Auth::user()->type != 1 && $vendor_id != 1)
            <li class="nav-item mb-2 fs-7">
                <a class="nav-link d-flex align-items-center {{ request()->is('admin/design*') ? 'active' : '' }}"
                    aria-current="page" href="{{ URL::to('admin/design') }}">
                    <span class="{{ request()->is('admin/design*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                        <i class="fa-solid fa-palette"></i>
                    </span>
                    <span class="mx-2">{{ trans('labels.design_menu') }}</span>
                </a>
            </li>
        @endif
        @if (Auth::user()->type != 1 && $vendor_id != 1 && \App\Services\AiAssistant::enabled())
            <li class="nav-item mb-2 fs-7">
                <a class="nav-link d-flex align-items-center {{ request()->is('admin/store-setup*') ? 'active' : '' }}"
                    aria-current="page" href="{{ URL::to('admin/store-setup') }}">
                    <span class="{{ request()->is('admin/store-setup*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </span>
                    <span class="mx-2">{{ trans('labels.ai_assistant') }}</span>
                </a>
            </li>
            {{-- What the AI assistant on the merchant's store page answers from. --}}
            <li class="nav-item mb-2 fs-7">
                <a class="nav-link d-flex align-items-center {{ request()->is('admin/whatsapp/knowledge*') ? 'active' : '' }}"
                    aria-current="page" href="{{ URL::to('admin/whatsapp/knowledge') }}">
                    <span class="{{ request()->is('admin/whatsapp/knowledge*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                        <i class="fa-solid fa-book-open"></i>
                    </span>
                    <span class="mx-2">{{ trans('labels.ai_knowledge_base') }}</span>
                </a>
            </li>
        @endif
        <li
            class="nav-item mb-2 fs-7 {{ !$oc_is_booking && helper::check_menu($role_id, 'role_categories') == 1 ? 'd-block' : 'd-none' }}">
            <a class="nav-link d-flex align-items-center {{ request()->is('admin/categories*') ? 'active' : '' }}"
                aria-current="page" href="{{ URL::to('admin/categories') }}">
                <span class="{{ request()->is('admin/categories*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                    <i class="fa-sharp fa-solid fa-list"></i>
                </span>
                <span class="px-2">{{ trans('labels.categories') }}</span>
            </a>
        </li>
        <li class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_tax') == 1 ? 'd-block' : 'd-none' }}">
            <a class="nav-link d-flex align-items-center {{ request()->is('admin/tax*') ? 'active' : '' }}"
                aria-current="page" href="{{ URL::to('admin/tax') }}">
                <span class="{{ request()->is('admin/tax*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                    <i class="fa-solid fa-money-check-dollar"></i>
                </span>
                <span class="px-2">{{ trans('labels.tax') }}</span>
            </a>
        </li>
        <li
            class="nav-item mb-2 fs-7 {{ false && helper::check_menu($role_id, 'role_global_extras') == 1 ? 'd-block' : 'd-none' }}">
            <a class="nav-link d-flex align-items-center {{ request()->is('admin/extras*') ? 'active' : '' }}"
                aria-current="page" href="{{ URL::to('admin/extras') }}">
                <span class="{{ request()->is('admin/extras*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                    <i class="fa-sharp fa-solid fa-planet-moon"></i>
                </span>
                <span class="px-2">{{ trans('labels.global_extras') }}</span>
            </a>
        </li>

        <li class="nav-item mb-2 fs-7 {{ !$oc_is_booking && helper::check_menu($role_id, 'role_products') == 1 ? 'd-block' : 'd-none' }}">
            <a class="nav-link d-flex align-items-center {{ request()->is('admin/products*') && !request()->is('admin/products/import') ? 'active' : '' }}"
                aria-current="page" href="{{ URL::to('admin/products') }}">
                <span
                    class="{{ request()->is('admin/products*') && !request()->is('admin/products/import') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                    <i class="fa-solid fa-layer-group"></i>
                </span>
                <span class="px-2">{{ trans('labels.products') }}</span>
            </a>
        </li>
        @if (App\Models\SystemAddons::where('unique_identifier', 'product_import')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'product_import')->first()->activated == 1)
            <li
                class="nav-item mb-2 fs-7 {{ false && helper::check_menu($role_id, 'role_import_product') == 1 ? 'd-block' : 'd-none' }}">
                <a class="nav-link d-flex align-items-center justify-content-between {{ request()->is('admin/products/import') ? 'active' : '' }}"
                    aria-current="page" href="{{ URL::to('/admin/products/import') }}">
                    <div class="d-flex align-items-center">
                        <span
                            class="{{ request()->is('admin/products/import') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </span>
                        <span class="px-2">{{ trans('labels.product_upload') }}</span>
                    </div>
                    @if (env('Environment') == 'sendbox')
                        <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                    @endif
                </a>
            </li>
        @endif
        @if (App\Models\SystemAddons::where('unique_identifier', 'question_answer')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'question_answer')->first()->activated == 1)
            <li
                class="nav-item mb-2 fs-7 {{ false && helper::check_menu($role_id, 'role_question_answer') == 1 ? 'd-block' : 'd-none' }}">
                <a class="nav-link d-flex align-items-center justify-content-between {{ request()->is('admin/question_answer') ? 'active' : '' }}"
                    aria-current="page" href="{{ URL::to('/admin/question_answer') }}">
                    <div class="d-flex align-items-center">
                        <span
                            class="{{ request()->is('/admin/question_answer') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                            <i class="fa-regular fa-question"></i>
                        </span>
                        <span class="px-2">{{ trans('labels.product_question_answer') }}</span>
                    </div>
                    @if (env('Environment') == 'sendbox')
                        <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                    @endif
                </a>
            </li>
        @endif
        @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
            <li
                class="nav-item mb-2 fs-7 {{ false && helper::check_menu($role_id, 'role_product_review') == 1 ? 'd-block' : 'd-none' }}">
                <a class="nav-link d-flex align-items-center justify-content-between {{ request()->is('admin/reviews') ? 'active' : '' }}"
                    aria-current="page" href="{{ URL::to('/admin/reviews') }}">
                    <div class="d-flex align-items-center">
                        <span class="{{ request()->is('/admin/reviews') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                            <i class="fa-solid fa-star"></i>
                        </span>
                        <span class="px-2">{{ trans('labels.product_reviews') }}</span>
                    </div>
                    @if (env('Environment') == 'sendbox')
                        <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                    @endif
                </a>
            </li>
        @endif
        @if (App\Models\SystemAddons::where('unique_identifier', 'shipping_area')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'shipping_area')->first()->activated == 1)
        <li
            class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_shipping_management') == 1 ? 'd-block' : 'd-none' }}">
            <a class="nav-link d-flex align-items-center {{ request()->is('admin/shipping') ? 'active' : '' }}"
                aria-current="page" href="{{ URL::to('admin/shipping') }}">
                <span class="{{ request()->is('admin/shipping*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                    <i class="fa-solid fa-truck-fast"></i>
                </span>
                <span class="px-2">{{ trans('labels.shipping_management') }}</span>
            </a>
        </li>
        @endif

        <li
            class="nav-item mt-3 {{ helper::check_menu($role_id, 'role_banner') == 1 || helper::check_menu($role_id, 'role_coupons') == 1 || false && helper::check_menu($role_id, 'role_top_deals') == 1 || false && helper::check_menu($role_id, 'role_firebase_notification') == 1 ? 'd-block' : 'd-none' }}">
            <h6 class="text-dark color-changer fw-500 mb-2 fs-7 text-uppercase mx-3">{{ trans('labels.promotions') }}</h6>
        </li>
        <li class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_banner') == 1 ? 'd-block' : 'd-none' }}">
            <a class="nav-link d-flex align-items-center {{ request()->is('admin/banner*') ? 'active' : '' }}"
                href="{{ URL::to('/admin/banner') }}" aria-expanded="false">
                <span class="{{ request()->is('admin/banner*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                    <i class="fa-solid fa-image"></i>
                </span>
                <span class="nav-text px-2">{{ trans('labels.banner') }}</span>
            </a>
        </li>
        @if (App\Models\SystemAddons::where('unique_identifier', 'subscription')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'subscription')->first()->activated == 1)
            @if (App\Models\SystemAddons::where('unique_identifier', 'coupon')->first() != null &&
                    App\Models\SystemAddons::where('unique_identifier', 'coupon')->first()->activated == 1)
                @php
                    $checkplan = App\Models\Transaction::where('vendor_id', $vendor_id)->orderByDesc('id')->first();
                    if ($user->allow_without_subscription == 1) {
                        $coupons = 1;
                    } else {
                        $coupons = @helper::get_plan($vendor_id)->coupons;
                    }
                @endphp
                @if ($coupons == 1)
                    <li
                        class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_coupons') == 1 ? 'd-block' : 'd-none' }}">
                        <a class="nav-link d-flex align-items-center justify-content-between {{ request()->is('admin/coupons*') ? 'active' : '' }}"
                            href="{{ URL::to('/admin/coupons') }}" aria-expanded="false">
                            <div class="d-flex align-items-center">
                                <span
                                    class="{{ request()->is('admin/coupons*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                                    <i class="fa-solid fa-ticket"></i>
                                </span>
                                <span class="nav-text px-2">{{ trans('labels.coupons') }}</span>
                            </div>
                            @if (env('Environment') == 'sendbox')
                                <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                            @endif
                        </a>
                    </li>
                @endif
            @endif
        @else
            @if (App\Models\SystemAddons::where('unique_identifier', 'coupon')->first() != null &&
                    App\Models\SystemAddons::where('unique_identifier', 'coupon')->first()->activated == 1)
                <li
                    class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_coupons') == 1 ? 'd-block' : 'd-none' }}">
                    <a class="nav-link d-flex align-items-center justify-content-between {{ request()->is('admin/coupons*') ? 'active' : '' }}"
                        href="{{ URL::to('/admin/coupons') }}" aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <span
                                class="{{ request()->is('admin/coupons*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                                <i class="fa-solid fa-ticket"></i>
                            </span>
                            <span class="nav-text px-2">{{ trans('labels.coupons') }}</span>
                        </div>
                        @if (env('Environment') == 'sendbox')
                            <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                        @endif
                    </a>
                </li>
            @endif
        @endif

        @if (App\Models\SystemAddons::where('unique_identifier', 'top_deals')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'top_deals')->first()->activated == 1)
            <li
                class="nav-item mb-2 fs-7 {{ false && helper::check_menu($role_id, 'role_top_deals') == 1 ? 'd-block' : 'd-none' }}">
                <a class="nav-link d-flex align-items-center justify-content-between {{ request()->is('admin/top_deals*') ? 'active' : '' }}"
                    href="{{ URL::to('/admin/top_deals') }}" aria-expanded="false">
                    <div class="d-flex align-items-center">
                        <span class="{{ request()->is('admin/top_deals*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                            <i class="fa-solid fa-badge-percent"></i>
                        </span>
                        <span class="nav-text px-2">{{ trans('labels.top_deals') }}</span>
                    </div>
                    @if (env('Environment') == 'sendbox')
                        <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                    @endif
                </a>
            </li>


        @endif
        @if (App\Models\SystemAddons::where('unique_identifier', 'firebase_notification')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'firebase_notification')->first()->activated == 1)
            <li
                class="nav-item mb-2 fs-7 {{ false && helper::check_menu($role_id, 'role_firebase_notification') == 1 ? 'd-block' : 'd-none' }}">
                <a class="nav-link d-flex align-items-center justify-content-between {{ request()->is('admin/notification*') ? 'active' : '' }}"
                    href="{{ URL::to('/admin/notification') }}" aria-expanded="false">
                    <div class="d-flex align-items-center">
                        <span
                            class="{{ request()->is('admin/notification*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                            <i class="fa-solid fa-tags"></i>
                        </span>
                        <span class="nav-text px-2">{{ trans('labels.firebase_notification') }}</span>
                    </div>
                    @if (env('Environment') == 'sendbox')
                        <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                    @endif
                </a>
            </li>

        @endif
        @if (App\Models\SystemAddons::where('unique_identifier', 'subscription')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'subscription')->first()->activated == 1)

            @if (App\Models\SystemAddons::where('unique_identifier', 'tableqr')->first() != null &&
                    App\Models\SystemAddons::where('unique_identifier', 'tableqr')->first()->activated == 1)
                @php
                    if ($user->allow_without_subscription == 1) {
                        $tableqr = 1;
                    } else {
                        $tableqr = @helper::get_plan($vendor_id)->tableqr;
                    }
                @endphp
                @if ($tableqr == 1)
                    <li
                        class="nav-item mt-3 {{ false && helper::check_menu($role_id, 'role_tableqr') == 1 || false && helper::check_menu($role_id, 'role_area') == 1 ? 'd-block' : 'd-none' }}">
                        <h6 class="text-dark color-changer fw-500 mb-2 fs-7 text-uppercase mx-3">
                            {{ trans('labels.table_management') }}</h6>
                    </li>
                    <li
                        class="nav-item mb-2 fs-7 {{ false && helper::check_menu($role_id, 'role_tableqr') == 1 ? 'd-block' : 'd-none' }}">
                        <a class="nav-link d-flex align-items-center justify-content-between {{ request()->is('admin/tableqr*') ? 'active' : '' }}"
                            href="{{ URL::to('/admin/tableqr') }}" aria-expanded="false">
                            <div class="d-flex align-items-center">
                                <span
                                    class="{{ request()->is('admin/tableqr*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                                    <i class="fa-solid fa-qrcode"></i>
                                </span>
                                <span class="nav-text px-2">{{ trans('labels.tableqr') }}</span>
                            </div>
                            @if (env('Environment') == 'sendbox')
                                <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                            @endif
                        </a>
                    </li>

                    <li
                        class="nav-item mb-2 fs-7 {{ false && helper::check_menu($role_id, 'role_area') == 1 ? 'd-block' : 'd-none' }}">
                        <a class="nav-link d-flex align-items-center {{ request()->is('admin/area*') ? 'active' : '' }}"
                            href="{{ URL::to('/admin/area') }}" aria-expanded="false">
                            <span class="{{ request()->is('admin/area*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                                <i class="fa-solid fa-location-dot"></i>
                            </span>
                            <span class="nav-text px-2">{{ trans('labels.area') }}</span>
                        </a>
                    </li>

                @endif
            @endif
        @else
            @if (App\Models\SystemAddons::where('unique_identifier', 'tableqr')->first() != null &&
                    App\Models\SystemAddons::where('unique_identifier', 'tableqr')->first()->activated == 1)
                <li
                    class="nav-item mt-3 {{ false && helper::check_menu($role_id, 'role_tableqr') == 1 || false && helper::check_menu($role_id, 'role_area') == 1 ? 'd-block' : 'd-none' }}">
                    <h6 class="text-dark color-changer fw-500 mb-2 fs-7 text-uppercase mx-3">{{ trans('labels.table_management') }}
                    </h6>
                </li>

                <li
                    class="nav-item mb-2 fs-7 {{ false && helper::check_menu($role_id, 'role_tableqr') == 1 ? 'd-block' : 'd-none' }}">
                    <a class="nav-link d-flex align-items-center justify-content-between {{ request()->is('admin/tableqr*') ? 'active' : '' }}"
                        href="{{ URL::to('/admin/tableqr') }}" aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <span
                                class="{{ request()->is('admin/tableqr*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                                <i class="fa-solid fa-qrcode"></i>
                            </span>
                            <span class="nav-text px-2">{{ trans('labels.tableqr') }}</span>
                        </div>
                        @if (env('Environment') == 'sendbox')
                            <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                        @endif
                    </a>
                </li>

                <li
                    class="nav-item mb-2 fs-7 {{ false && helper::check_menu($role_id, 'role_area') == 1 ? 'd-block' : 'd-none' }}">
                    <a class="nav-link d-flex align-items-center justify-content-between {{ request()->is('admin/area*') ? 'active' : '' }}"
                        href="{{ URL::to('/admin/area') }}" aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <span class="{{ request()->is('admin/area*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                                <i class="fa-solid fa-location-dot"></i>
                            </span>
                            <span class="nav-text px-2">{{ trans('labels.area') }}</span>
                        </div>
                        @if (env('Environment') == 'sendbox')
                            <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                        @endif
                    </a>
                </li>

            @endif
        @endif
    @endif
    @if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1))
        <li class="nav-item mt-3 {{ helper::check_menu($role_id, 'role_coupons') == 1 ? 'd-block' : 'd-none' }}">
            <h6 class="text-dark color-changer fw-500 mb-2 fs-7 text-uppercase mx-3">{{ trans('labels.promotions') }}</h6>
        </li>
        @if (App\Models\SystemAddons::where('unique_identifier', 'coupon')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'coupon')->first()->activated == 1)
            <li
                class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_coupons') == 1 ? 'd-block' : 'd-none' }}">
                <a class="nav-link d-flex align-items-center justify-content-between {{ request()->is('admin/coupons*') ? 'active' : '' }}"
                    href="{{ URL::to('/admin/coupons') }}" aria-expanded="false">
                    <div class="d-flex align-items-center">
                        <span class="{{ request()->is('admin/coupons*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                            <i class="fa-solid fa-ticket"></i>
                        </span>
                        <span class="nav-text px-2">{{ trans('labels.coupons') }}</span>
                    </div>
                    @if (env('Environment') == 'sendbox')
                        <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                    @endif
                </a>
            </li>
        @endif
    @endif
    <li
        class="nav-item mt-3 {{ helper::check_menu($role_id, 'role_tax') == 1 || helper::check_menu($role_id, 'role_pricing_plans') == 1 || helper::check_menu($role_id, 'role_transaction') == 1 || helper::check_menu($role_id, 'role_payment_methods') == 1 || helper::check_menu($role_id, 'role_working_hours') == 1 || helper::check_menu($role_id, 'role_cities') == 1 || helper::check_menu($role_id, 'role_areas') == 1 || helper::check_menu($role_id, 'role_store_categories') == 1 || helper::check_menu($role_id, 'role_custom_domains') == 1 || helper::check_menu($role_id, 'role_google_analytics') == 1 ? 'd-block' : 'd-none' }}">
        <h6 class="text-dark color-changer fw-500 mb-2 fs-7 text-uppercase mx-3">{{ trans('labels.business_management') }}</h6>
    </li>

    @if (App\Models\SystemAddons::where('unique_identifier', 'subscription')->first() != null &&
            App\Models\SystemAddons::where('unique_identifier', 'subscription')->first()->activated == 1)

        @if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1))
            <li
                class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_tax') == 1 ? 'd-block' : 'd-none' }}">
                <a class="nav-link d-flex align-items-center {{ request()->is('admin/tax*') ? 'active' : '' }}"
                    aria-current="page" href="{{ URL::to('/admin/tax') }}">

                    <span class="{{ request()->is('admin/tax') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                        <i class="fa-solid fa-money-check-dollar"></i>
                    </span>
                    <span class="px-2">{{ trans('labels.tax') }}</span>
                </a>
            </li>
        @endif
        @if ($user->allow_without_subscription != 1)
            <li
                class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_pricing_plans') == 1 ? 'd-block' : 'd-none' }}">
                <a class="nav-link d-flex align-items-center  {{ request()->is('admin/plan*') ? 'active' : '' }}"
                    aria-current="page" href="{{ URL::to('admin/plan') }}">

                    <span class="{{ request()->is('admin/plan') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                        <i class="fa-solid fa-bell"></i>
                    </span>
                    <span class="px-2">{{ trans('labels.pricing_plans') }}</span>
                </a>
            </li>
        @endif
        <li
            class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_transaction') == 1 ? 'd-block' : 'd-none' }}">
            <a class="nav-link d-flex align-items-center {{ request()->is('admin/transaction') ? 'active' : '' }}"
                aria-current="page" href="{{ URL::to('admin/transaction') }}">
                <span class="{{ request()->is('admin/transaction') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </span>
                <span class="px-2">{{ trans('labels.transaction') }}</span>
            </a>
        </li>
        <li
            class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_payment_methods') == 1 ? 'd-block' : 'd-none' }}">
            <a class="nav-link d-flex align-items-center  {{ request()->is('admin/payment') ? 'active' : '' }}"
                aria-current="page" href="{{ URL::to('admin/payment') }}">

                <span class="{{ request()->is('admin/payment') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                    <i class="fa-solid fa-hand-holding-dollar"></i>

                </span>
                <span class="px-2">{{ trans('labels.payment_methods') }}</span>
            </a>
        </li>
    @else
        @if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1))
            <li
                class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_payment_methods') == 1 ? 'd-block' : 'd-none' }}">
                <a class="nav-link d-flex align-items-center  {{ request()->is('admin/payment') ? 'active' : '' }}"
                    aria-current="page" href="{{ URL::to('admin/payment') }}">

                    <span class="{{ request()->is('admin/payment') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </span>
                    <span class="px-2">{{ trans('labels.payment_methods') }}</span>
                </a>
            </li>
        @endif
    @endif
    @if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1))
        <li
            class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_working_hours') == 1 ? 'd-block' : 'd-none' }}">
            <a class="nav-link d-flex align-items-center {{ request()->is('admin/time*') ? 'active' : '' }}"
                href="{{ URL::to('/admin/time') }}" aria-expanded="false">
                <span class="{{ request()->is('admin/time*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                    <i class="fa-solid fa-business-time"></i>
                </span>
                <span class="nav-text px-2">{{ trans('labels.working_hours') }}</span>
            </a>
        </li>
    @endif

    @if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1))
        <li
            class="nav-item mb-2 fs-7 dropdown multimenu {{ helper::check_menu($role_id, 'role_cities') == 1 || helper::check_menu($role_id, 'role_areas') == 1 ? 'd-block' : 'd-none' }}">
            <a class="nav-link collapsed d-flex align-items-center  justify-content-between dropdown-toggle mb-1 {{ request()->is('admin/cities*') || request()->is('admin/areas*') || request()->is('admin/locations*') ? 'active' : '' }}"
                href="#location" data-bs-toggle="collapse" role="button" aria-expanded="false"
                aria-controls="location">
                <div class="d-flex align-items-center">
                    <span
                        class=" {{ request()->is('admin/cities*') || request()->is('admin/areas*') || request()->is('admin/locations*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                        <i class="fa-solid fa-location-crosshairs"></i>
                    </span>
                    <span class="multimenu-title px-2">{{ trans('labels.location') }}</span>
                </div>
            </a>
            <ul class="collapse" id="location">
                <li class="nav-item ps-4 mb-1">
                    <a class="nav-link {{ request()->is('admin/locations*') ? 'active' : '' }}" aria-current="page"
                        href="{{ URL::to('/admin/locations') }}">
                        <span class="d-flex align-items-center multimenu-menu-indicator">
                            <i class="fa-solid fa-circle-small"></i>{{ trans('labels.marketplace_locations') }}</span>
                    </a>
                </li>
                <li
                    class="nav-item ps-4 mb-1 {{ helper::check_menu($role_id, 'role_cities') == 1 ? 'd-block' : 'd-none' }}">
                    <a class="nav-link  {{ request()->is('admin/cities*') ? 'active' : '' }}" aria-current="page"
                        href="{{ URL::to('/admin/cities') }}">
                        <span class="d-flex align-items-center multimenu-menu-indicator">
                            <i class="fa-solid fa-circle-small"></i>
                            {{ trans('labels.cities') }}</span>
                    </a>
                </li>
                <li
                    class="nav-item ps-4 mb-1 {{ helper::check_menu($role_id, 'role_areas') == 1 ? 'd-block' : 'd-none' }}">
                    <a class="nav-link {{ request()->is('admin/areas*') ? 'active' : '' }}" aria-current="page"
                        href="{{ URL::to('/admin/areas') }}">
                        <span class="d-flex align-items-center multimenu-menu-indicator">
                            <i class="fa-solid fa-circle-small"></i>{{ trans('labels.areas') }}</span>
                    </a>
                </li>
            </ul>
        </li>

        <li
            class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_store_categories') == 1 ? 'd-block' : 'd-none' }}">
            <a class="nav-link d-flex align-items-center {{ request()->is('admin/store_categories*') ? 'active' : '' }}"
                aria-current="page" href="{{ URL::to('admin/store_categories') }}">
                <span class="{{ request()->is('admin/store_categories*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                    <i class="fa-sharp fa-solid fa-list"></i>
                </span>
                <span class="px-2">{{ trans('labels.store_categories') }}</span>
            </a>
        </li>
        @if (App\Models\SystemAddons::where('unique_identifier', 'custom_domain')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'custom_domain')->first()->activated == 1)
            <li
                class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_custom_domains') == 1 ? 'd-block' : 'd-none' }}">
                <a class="nav-link d-flex align-items-center justify-content-between {{ request()->is('admin/custom_domain*') ? 'active' : '' }}"
                    aria-current="page" href="{{ URL::to('/admin/custom_domain') }}">
                    <div class="d-flex align-items-center">
                        <span
                            class="{{ request()->is('admin/custom_domain') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                            <i class="fa-solid fa-globe"></i>
                        </span>

                        <span class="nav-text px-2">{{ trans('labels.custom_domains') }}</span>
                    </div>
                    @if (env('Environment') == 'sendbox')
                        <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                    @endif

                </a>
            </li>
        @endif
    @endif

    @if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1))
        @if (App\Models\SystemAddons::where('unique_identifier', 'subscription')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'subscription')->first()->activated == 1)
            @if (App\Models\SystemAddons::where('unique_identifier', 'custom_domain')->first() != null &&
                    App\Models\SystemAddons::where('unique_identifier', 'custom_domain')->first()->activated == 1)
                @php
                    $checkplan = App\Models\Transaction::where('vendor_id', $vendor_id)->orderByDesc('id')->first();
                    if ($user->allow_without_subscription == 1) {
                        $custom_domain = 1;
                    } else {
                        $custom_domain = @$checkplan->custom_domain;
                    }
                @endphp
                @if ($custom_domain == 1)
                    <li
                        class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_custom_domains') == 1 ? 'd-block' : 'd-none' }}">
                        <a class="nav-link d-flex align-items-center justify-content-between {{ request()->is('admin/custom_domain*') ? 'active' : '' }}"
                            aria-current="page" href="{{ URL::to('/admin/custom_domain') }}">
                            <div class="d-flex align-items-center">
                                <span
                                    class="{{ request()->is('admin/custom_domain*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                                    <i class="fa-solid fa-globe"></i>
                                </span>
                                <span class="nav-text px-2">{{ trans('labels.custom_domains') }}</span>
                            </div>
                            @if (env('Environment') == 'sendbox')
                                <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                            @endif
                        </a>
                    </li>
                @endif
            @endif
        @else
            @if (App\Models\SystemAddons::where('unique_identifier', 'custom_domain')->first() != null &&
                    App\Models\SystemAddons::where('unique_identifier', 'custom_domain')->first()->activated == 1)
                <li
                    class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_custom_domains') == 1 ? 'd-block' : 'd-none' }}">
                    <a class="nav-link d-flex align-items-center justify-content-between {{ request()->is('admin/custom_domain*') ? 'active' : '' }}"
                        aria-current="page" href="{{ URL::to('/admin/custom_domain') }}">
                        <div class="d-flex align-items-center">
                            <span
                                class="{{ request()->is('admin/custom_domain*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                                <i class="fa-solid fa-globe"></i>
                                <!-- <i class="fa-solid fa-link"></i> -->
                            </span>
                            <span class="nav-text px-2">{{ trans('labels.custom_domains') }}</span>
                        </div>
                        @if (env('Environment') == 'sendbox')
                            <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                        @endif
                    </a>
                </li>
            @endif
        @endif

        @if (App\Models\SystemAddons::where('unique_identifier', 'custom_status')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'custom_status')->first()->activated == 1)
            <li
                class="nav-item mb-2 fs-7 {{ false && helper::check_menu($role_id, 'role_custom_status') == 1 ? 'd-block' : 'd-none' }}">
                <a class="nav-link d-flex align-items-center justify-content-between {{ request()->is('admin/custom_status*') ? 'active' : '' }}"
                    href="{{ URL::to('/admin/custom_status') }}" aria-expanded="false">
                    <div class="d-flex align-items-center">
                        <span
                            class="{{ request()->is('admin/custom_status*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                            <i class="fa-regular fa-clipboard-list-check"></i>
                        </span>
                        <span class="nav-text px-2">{{ trans('labels.custom_status') }}</span>
                    </div>
                    @if (env('Environment') == 'sendbox')
                        <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                    @endif
                </a>
            </li>

        @endif
        @if (App\Models\SystemAddons::where('unique_identifier', 'subscription')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'subscription')->first()->activated == 1)

            @if (App\Models\SystemAddons::where('unique_identifier', 'employee')->first() != null &&
                    App\Models\SystemAddons::where('unique_identifier', 'employee')->first()->activated == 1)
                @php

                    $checkplan = App\Models\Transaction::where('vendor_id', $vendor_id)->orderByDesc('id')->first();

                    if ($user->allow_without_subscription == 1) {
                        $role_management = 1;
                    } else {
                        $role_management = @$checkplan->role_management;
                    }

                @endphp
                @if ($role_management == 1)
                    <li
                        class="nav-item mt-3 {{ helper::check_menu($role_id, 'role_employees') == 1 || helper::check_menu($role_id, 'role_roles') == 1 ? 'd-block' : 'd-none' }}">
                        <h6 class="text-dark color-changer fw-500 mb-2 fs-7 text-uppercase mx-3">
                            {{ trans('labels.employee_management') }}
                        </h6>
                    </li>

                    <li
                        class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_roles') == 1 ? 'd-block' : 'd-none' }}">
                        <a class="nav-link d-flex align-items-center justify-content-between {{ request()->is('admin/roles*') ? 'active' : '' }}"
                            href="{{ URL::to('/admin/roles') }}" aria-expanded="false">
                            <div class="d-flex align-items-center">
                                <span
                                    class="{{ request()->is('admin/roles*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                                    <i class="fa-solid fa-user-secret"></i>
                                </span>
                                <span class="nav-text px-2">{{ trans('labels.roles') }}</span>
                            </div>
                            @if (env('Environment') == 'sendbox')
                                <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                            @endif
                        </a>
                    </li>
                    <li
                        class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_employees') == 1 ? 'd-block' : 'd-none' }}">
                        <a class="nav-link d-flex align-items-center justify-content-between {{ request()->is('admin/employees*') ? 'active' : '' }}"
                            href="{{ URL::to('/admin/employees') }}" aria-expanded="false">
                            <div class="d-flex align-items-center">
                                <span
                                    class="{{ request()->is('admin/employees*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                                    <i class="fa fa-users"></i>
                                </span>
                                <span class="nav-text px-2">{{ trans('labels.employee') }}</span>
                            </div>
                            @if (env('Environment') == 'sendbox')
                                <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                            @endif
                        </a>
                    </li>
                @endif
            @endif
        @else
            @if (App\Models\SystemAddons::where('unique_identifier', 'employee')->first() != null &&
                    App\Models\SystemAddons::where('unique_identifier', 'employee')->first()->activated == 1)
                <li
                    class="nav-item mt-3 {{ helper::check_menu($role_id, 'role_employees') == 1 || helper::check_menu($role_id, 'role_roles') == 1 ? 'd-block' : 'd-none' }}">
                    <h6 class="text-dark color-changer fw-500 mb-2 fs-7 text-uppercase mx-3">
                        {{ trans('labels.employee_management') }}
                    </h6>
                </li>

                <li
                    class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_roles') == 1 ? 'd-block' : 'd-none' }}">
                    <a class="nav-link d-flex align-items-center justify-content-between {{ request()->is('admin/roles*') ? 'active' : '' }}"
                        href="{{ URL::to('/admin/roles') }}" aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <span class="{{ request()->is('admin/roles*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                                <i class="fa-solid fa-user-secret"></i>
                            </span>
                            <span class="nav-text px-2">{{ trans('labels.roles') }}</span>
                        </div>
                        @if (env('Environment') == 'sendbox')
                            <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                        @endif
                    </a>
                </li>
                <li
                    class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_employees') == 1 ? 'd-block' : 'd-none' }}">
                    <a class="nav-link d-flex align-items-center justify-content-between {{ request()->is('admin/employees*') ? 'active' : '' }}"
                        href="{{ URL::to('/admin/employees') }}" aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <span
                                class="{{ request()->is('admin/employees*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                                <i class="fa fa-users"></i>
                            </span>
                            <span class="nav-text px-2">{{ trans('labels.employee') }}</span>
                        </div>
                        @if (env('Environment') == 'sendbox')
                            <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                        @endif
                    </a>
                </li>
            @endif
        @endif
    @endif



    @if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1))
        @if (App\Models\SystemAddons::where('unique_identifier', 'employee')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'employee')->first()->activated == 1)
            <li
                class="nav-item mt-3 {{ helper::check_menu($role_id, 'role_employees') == 1 || helper::check_menu($role_id, 'role_roles') == 1 ? 'd-block' : 'd-none' }}">
                <h6 class="text-dark color-changer fw-500 mb-2 fs-7 text-uppercase mx-3">{{ trans('labels.employee_management') }}
                </h6>
            </li>

            <li
                class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_roles') == 1 ? 'd-block' : 'd-none' }}">
                <a class="nav-link d-flex align-items-center justify-content-between {{ request()->is('admin/roles*') ? 'active' : '' }}"
                    href="{{ URL::to('/admin/roles') }}" aria-expanded="false">
                    <div class="d-flex align-items-center">
                        <span class="{{ request()->is('admin/roles*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                            <i class="fa-solid fa-user-secret"></i>
                        </span>
                        <span class="nav-text px-2">{{ trans('labels.roles') }}</span>
                    </div>
                    @if (env('Environment') == 'sendbox')
                        <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                    @endif
                </a>
            </li>
            <li
                class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_employees') == 1 ? 'd-block' : 'd-none' }}">
                <a class="nav-link d-flex align-items-center justify-content-between {{ request()->is('admin/employees*') ? 'active' : '' }}"
                    href="{{ URL::to('/admin/employees') }}" aria-expanded="false">
                    <div class="d-flex align-items-center">
                        <span class="{{ request()->is('admin/employees*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                            <i class="fa fa-users"></i>
                        </span>
                        <span class="nav-text px-2">{{ trans('labels.employee') }}</span>
                    </div>
                    @if (env('Environment') == 'sendbox')
                        <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                    @endif
                </a>
            </li>
        @endif
        {{-- landing Page --}}
        <li
            class="nav-item mt-3 {{ helper::check_menu($role_id, 'role_how_works') == 1 || helper::check_menu($role_id, 'role_theme_images') == 1 || helper::check_menu($role_id, 'role_features') == 1 || helper::check_menu($role_id, 'role_promotional_banners') == 1 || helper::check_menu($role_id, 'role_blogs') == 1 || helper::check_menu($role_id, 'role_faqs') == 1 || helper::check_menu($role_id, 'role_testimonials') == 1 || helper::check_menu($role_id, 'role_subscribers') == 1 || helper::check_menu($role_id, 'role_inquiries') == 1 || helper::check_menu($role_id, 'role_cms_pages') == 1 ? 'd-block' : 'd-none' }}">
            <h6 class="text-dark color-changer fw-500 mb-2 fs-7 text-uppercase mx-3">{{ trans('labels.landing_page') }}</h6>
        </li>
        <li
            class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_how_works') == 1 ? 'd-block' : 'd-none' }}">
            <a class="nav-link d-flex align-items-center {{ request()->is('admin/how_works*') ? 'active' : '' }}"
                aria-current="page" href="{{ URL::to('/admin/how_works') }}">
                <span class="{{ request()->is('admin/how_works') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                    <i class="fa-solid fa-hourglass"></i>
                </span>
                <span class="px-2">{{ trans('labels.how_works') }}</span>
            </a>
        </li>
        <li
            {{-- Template Images retired: stores are designed by the AI designer, nothing picks a template any more. --}}
            class="nav-item mb-2 fs-7 d-none">
            <a class="nav-link d-flex align-items-center {{ request()->is('admin/themes*') ? 'active' : '' }}"
                aria-current="page" href="{{ URL::to('/admin/themes') }}">
                <span class="{{ request()->is('admin/themes') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                    <i class="fa-solid fa-palette"></i>
                </span>
                <span class="px-2">{{ trans('labels.theme_images') }}</span>
            </a>
        </li>
        <li
            class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_features') == 1 ? 'd-block' : 'd-none' }}">
            <a class="nav-link d-flex align-items-center {{ request()->is('admin/features*') ? 'active' : '' }}"
                aria-current="page" href="{{ URL::to('/admin/features') }}">
                <span class="{{ request()->is('admin/features') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                    <i class="fa-solid fa-lightbulb"></i>
                </span>
                <span class="px-2">{{ trans('labels.features') }}</span>
            </a>
        </li>
        <li
            class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_promotional_banners') == 1 ? 'd-block' : 'd-none' }}">
            <a class="nav-link d-flex align-items-center {{ request()->is('admin/promotionalbanners*') ? 'active' : '' }}"
                aria-current="page" href="{{ URL::to('/admin/promotionalbanners') }}">
                <span class="{{ request()->is('admin/promotionalbanners') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                    <i class="fa-solid fa-bullhorn"></i>
                </span>
                <span class="px-2">{{ trans('labels.promotional_banners') }}</span>
            </a>
        </li>

        @if (App\Models\SystemAddons::where('unique_identifier', 'blog')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'blog')->first()->activated == 1)
            <li
                class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_blogs') == 1 ? 'd-block' : 'd-none' }}">
                <a class="nav-link d-flex align-items-center justify-content-between {{ request()->is('admin/blogs*') ? 'active' : '' }}"
                    aria-current="page" href="{{ URL::to('/admin/blogs') }}">
                    <div class="d-flex align-items-center">
                        <span class="{{ request()->is('admin/blogs*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                            <i class="fa-solid fa-blog"></i>
                        </span>
                        <span class="nav-text px-2">{{ trans('labels.blogs') }}</span>
                    </div>
                    @if (env('Environment') == 'sendbox')
                        <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                    @endif
                </a>
            </li>
        @endif
        <li class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_faqs') == 1 ? 'd-block' : 'd-none' }}">
            <a class="nav-link d-flex align-items-center {{ request()->is('admin/faqs*') ? 'active' : '' }}"
                aria-current="page" href="{{ URL::to('/admin/faqs') }}">
                <span class="{{ request()->is('admin/faqs*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                    <i class="fa-solid fa-question"></i>
                </span>
                <span class="px-2">{{ trans('labels.faqs') }}</span>
            </a>
        </li>
        @if (App\Models\SystemAddons::where('unique_identifier', 'store_reviews')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'store_reviews')->first()->activated == 1)
            <li
                class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_testimonials') == 1 ? 'd-block' : 'd-none' }}">
                <a class="nav-link d-flex align-items-center {{ request()->is('admin/testimonials*') ? 'active' : '' }}"
                    aria-current="page" href="{{ URL::to('/admin/testimonials') }}">
                    <span class="{{ request()->is('admin/testimonials*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                        <i class="fa-solid fa-comments"></i>
                    </span>
                    <span class="px-2">{{ trans('labels.testimonials') }}</span>
                </a>
            </li>
        @endif
        <li
            class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_subscribers') == 1 ? 'd-block' : 'd-none' }}">
            <a class="nav-link d-flex align-items-center {{ request()->is('admin/subscribers*') ? 'active' : '' }}"
                aria-current="page" href="{{ URL::to('admin/subscribers') }}">
                <span class="{{ request()->is('admin/subscribers*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                    <i class="fa-solid fa-envelope-open-text"></i>
                </span>
                <span class="px-2">{{ trans('labels.subscribers') }}</span>
            </a>
        </li>
        <li
            class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_inquiries') == 1 ? 'd-block' : 'd-none' }}">
            <a class="nav-link d-flex align-items-center {{ request()->is('admin/inquiries*') ? 'active' : '' }}"
                aria-current="page" href="{{ URL::to('admin/inquiries') }}">

                <span class="{{ request()->is('admin/inquiries*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                    <i class="fa-solid fa-id-badge"></i>
                </span>
                <span class="px-2">{{ trans('labels.inquiries') }}</span>
            </a>
        </li>
        <li
            class="nav-item mb-2 fs-7 dropdown multimenu {{ helper::check_menu($role_id, 'role_cms_pages') == 1 ? 'd-block' : 'd-none' }}">
            <a class="nav-link collapsed d-flex align-items-center justify-content-between dropdown-toggle mb-1 {{ request()->is('admin/privacy-policy*') || request()->is('admin/refund-policy*') || request()->is('admin/terms-conditions*') || request()->is('admin/aboutus*') ? 'active' : '' }}"
                href="#pages" data-bs-toggle="collapse" role="button" aria-expanded="false"
                aria-controls="pages">
                <div class="d-flex align-items-center">
                    <span
                        class="{{ request()->is('admin/privacy-policy*') || request()->is('admin/refund-policy*') || request()->is('admin/terms-conditions*') || request()->is('admin/aboutus*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                        <i class="fa-regular fa-file-lines"></i>
                    </span>
                    <span class="multimenu-title px-2">{{ trans('labels.cms_pages') }}</span>
                </div>
            </a>
            <ul class="collapse" id="pages">
                <li class="nav-item ps-4 mb-1">
                    <a class="nav-link {{ request()->is('admin/privacy-policy*') ? 'active' : '' }}"
                        aria-current="page" href="{{ URL::to('/admin/privacy-policy') }}">
                        <span class="d-flex align-items-center multimenu-menu-indicator">
                            <i class="fa-solid fa-circle-small"></i>{{ trans('labels.privacypolicy') }}</span>
                    </a>
                </li>
                <li class="nav-item ps-4 mb-1">
                    <a class="nav-link {{ request()->is('admin/refund-policy*') ? 'active' : '' }}"
                        aria-current="page" href="{{ URL::to('/admin/refund-policy') }}">
                        <span class="d-flex align-items-center multimenu-menu-indicator">
                            <i class="fa-solid fa-circle-small"></i>{{ trans('labels.refund_policy') }}</span>
                    </a>
                </li>
                <li class="nav-item ps-4 mb-1">
                    <a class="nav-link {{ request()->is('admin/terms-conditions*') ? 'active' : '' }}"
                        aria-current="page" href="{{ URL::to('/admin/terms-conditions') }}">
                        <span class="d-flex align-items-center multimenu-menu-indicator">
                            <i class="fa-solid fa-circle-small"></i>{{ trans('labels.terms') }}</span>
                    </a>
                </li>
                <li class="nav-item ps-4 mb-1">
                    <a class="nav-link {{ request()->is('admin/aboutus*') ? 'active' : '' }}" aria-current="page"
                        href="{{ URL::to('/admin/aboutus') }}">
                        <span class="d-flex align-items-center multimenu-menu-indicator">
                            <i class="fa-solid fa-circle-small"></i>{{ trans('labels.about') }}</span>
                    </a>
                </li>
            </ul>
        </li>
    @endif
    @if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1))
        <li
            class="nav-item mt-3 {{ helper::check_menu($role_id, 'role_who_we_are') == 1 || helper::check_menu($role_id, 'role_blogs') == 1 || helper::check_menu($role_id, 'role_faqs') == 1 || helper::check_menu($role_id, 'role_testimonials') == 1 || helper::check_menu($role_id, 'role_subscribers') == 1 || helper::check_menu($role_id, 'role_inquiries') == 1 || false && helper::check_menu($role_id, 'role_product_inquiry') == 1 || helper::check_menu($role_id, 'role_cms_pages') == 1 || helper::check_menu($role_id, 'role_whatsapp_settings') == 1 || helper::check_menu($role_id, 'role_telegram_settings') == 1 || helper::check_menu($role_id, 'role_share') == 1 || helper::check_menu($role_id, 'role_language_settings') == 1 || helper::check_menu($role_id, 'role_currency_settings') == 1 || helper::check_menu($role_id, 'role_settings') == 1 ? 'd-block' : 'd-none' }}">
            <h6 class="text-dark color-changer fw-500 mb-2 fs-7 text-uppercase mx-3">{{ trans('labels.other') }}</h6>
        </li>
    @else
        <li
            class="nav-item mt-3 {{ helper::check_menu($role_id, 'role_language_settings') == 1 || helper::check_menu($role_id, 'role_settings') == 1 ? 'd-block' : 'd-none' }}">
            <h6 class="text-dark color-changer fw-500 mb-2 fs-7 text-uppercase mx-3">{{ trans('labels.other') }}</h6>
        </li>
    @endif

    @if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1))
        <li
            class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_who_we_are') == 1 ? 'd-block' : 'd-none' }}">
            <a class="nav-link d-flex align-items-center {{ request()->is('admin/whoweare*') ? 'active' : '' }}"
                aria-current="page" href="{{ URL::to('/admin/whoweare') }}">
                <span class="{{ request()->is('admin/whoweare*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                    <i class="fa-solid fa-question"></i>
                </span>
                <span class="px-2">{{ trans('labels.who_we_are') }}</span>
            </a>
        </li>
        @if (App\Models\SystemAddons::where('unique_identifier', 'subscription')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'subscription')->first()->activated == 1)
            @if (App\Models\SystemAddons::where('unique_identifier', 'blog')->first() != null &&
                    App\Models\SystemAddons::where('unique_identifier', 'blog')->first()->activated == 1)
                @php
                    if ($user->allow_without_subscription == 1) {
                        $blogs = 1;
                    } else {
                        $blogs = @helper::get_plan($vendor_id)->blogs;
                    }
                @endphp

                @if ($blogs == 1)
                    <li
                        class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_blogs') == 1 ? 'd-block' : 'd-none' }}">
                        <a class="nav-link d-flex align-items-center justify-content-between {{ request()->is('admin/blogs*') ? 'active' : '' }}"
                            aria-current="page" href="{{ URL::to('/admin/blogs') }}">
                            <div class="d-flex align-items-center">
                                <span
                                    class="{{ request()->is('admin/blogs*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                                    <i class="fa-solid fa-blog"></i>
                                </span>
                                <span class="nav-text px-2">{{ trans('labels.blogs') }}</span>
                            </div>
                            @if (env('Environment') == 'sendbox')
                                <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                            @endif
                        </a>
                    </li>
                @endif

            @endif
        @else
            @if (App\Models\SystemAddons::where('unique_identifier', 'blog')->first() != null &&
                    App\Models\SystemAddons::where('unique_identifier', 'blog')->first()->activated == 1)
                <li
                    class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_blogs') == 1 ? 'd-block' : 'd-none' }}">
                    <a class="nav-link d-flex  align-items-center justify-content-between {{ request()->is('admin/blogs*') ? 'active' : '' }}"
                        aria-current="page" href="{{ URL::to('/admin/blogs') }}">
                        <div class="d-flex align-items-center">
                            <span class="{{ request()->is('admin/blogs*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                                <i class="fa-solid fa-blog"></i>
                            </span>
                            <span class="nav-text px-2">{{ trans('labels.blogs') }}</span>
                        </div>
                        @if (env('Environment') == 'sendbox')
                            <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                        @endif
                    </a>
                </li>
            @endif
        @endif
        <li class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_faqs') == 1 ? 'd-block' : 'd-none' }}">
            <a class="nav-link d-flex align-items-center {{ request()->is('admin/faqs*') ? 'active' : '' }}"
                aria-current="page" href="{{ URL::to('/admin/faqs') }}">
                <span class="{{ request()->is('admin/faqs*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                    <i class="fa-solid fa-question"></i>
                </span>
                <span class="px-2">{{ trans('labels.faqs') }}</span>
            </a>
        </li>
        @if (App\Models\SystemAddons::where('unique_identifier', 'store_reviews')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'store_reviews')->first()->activated == 1)
            <li
                class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_testimonials') == 1 ? 'd-block' : 'd-none' }}">
                <a class="nav-link d-flex justify-content-between align-items-center {{ request()->is('admin/testimonials*') ? 'active' : '' }}"
                    aria-current="page" href="{{ URL::to('/admin/testimonials') }}">
                    <div class="d-flex align-items-center">
                        <span
                            class="{{ request()->is('admin/testimonials*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                            <i class="fa-solid fa-comments"></i>
                        </span>
                        <span class="px-2">{{ trans('labels.testimonials') }}</span>
                    </div>
                    @if (env('Environment') == 'sendbox')
                        <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                    @endif
                </a>
            </li>
        @endif
        <li
            class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_subscribers') == 1 ? 'd-block' : 'd-none' }}">
            <a class="nav-link d-flex align-items-center {{ request()->is('admin/subscribers*') ? 'active' : '' }}"
                aria-current="page" href="{{ URL::to('admin/subscribers') }}">
                <span class="{{ request()->is('admin/subscribers*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                    <i class="fa-solid fa-envelope"></i>
                </span>
                <span class="px-2">{{ trans('labels.subscribers') }}</span>
            </a>
        </li>

        <li
            class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_inquiries') == 1 ? 'd-block' : 'd-none' }}">
            <a class="nav-link d-flex align-items-center {{ request()->is('admin/inquiries*') ? 'active' : '' }}"
                aria-current="page" href="{{ URL::to('admin/inquiries') }}">
                <span class="{{ request()->is('admin/inquiries*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                    <i class="fa-solid fa-solid fa-address-book"></i>
                </span>
                <span class="px-2">{{ trans('labels.inquiries') }}</span>
            </a>
        </li>
        @if (App\Models\SystemAddons::where('unique_identifier', 'product_inquiry')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'product_inquiry')->first()->activated == 1)
            <li
                class="nav-item mb-2 fs-7 {{ false && helper::check_menu($role_id, 'role_product_inquiry') == 1 ? 'd-block' : 'd-none' }}">
                <a class="nav-link d-flex justify-content-between align-items-center {{ request()->is('admin/product_inquiry*') ? 'active' : '' }}"
                    aria-current="page" href="{{ URL::to('admin/product_inquiry') }}">
                    <div class="d-flex align-items-center">
                        <span
                            class="{{ request()->is('admin/product_inquiry*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                            <i class="fa-solid fa-id-badge"></i>
                        </span>
                        <span class="px-2">{{ trans('labels.product_inquiry') }}</span>
                    </div>
                    @if (env('Environment') == 'sendbox')
                        <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                    @endif
                </a>
            </li>
        @endif
        <li
            class="nav-item mb-2 fs-7 dropdown multimenu {{ helper::check_menu($role_id, 'role_cms_pages') == 1 ? 'd-block' : 'd-none' }}">
            <a class="nav-link collapsed d-flex align-items-center justify-content-between dropdown-toggle mb-1 {{ request()->is('admin/privacy-policy*') || request()->is('admin/refund-policy*') || request()->is('admin/terms-conditions*') || request()->is('admin/aboutus*') ? 'active' : '' }}"
                href="#pages" data-bs-toggle="collapse" role="button" aria-expanded="false"
                aria-controls="pages">
                <div class="d-flex align-items-center">
                    <span
                        class="{{ request()->is('admin/privacy-policy*') || request()->is('admin/refund-policy*') || request()->is('admin/terms-conditions*') || request()->is('admin/aboutus*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                        <i class="fa-regular fa-file-lines"></i>
                    </span>
                    <span class="multimenu-title px-2">{{ trans('labels.cms_pages') }}</span>
                </div>
            </a>
            <ul class="collapse" id="pages">
                <li class="nav-item ps-4 mb-1">
                    <a class="nav-link {{ request()->is('admin/privacy-policy*') ? 'active' : '' }}"
                        aria-current="page" href="{{ URL::to('/admin/privacy-policy') }}">
                        <span class="d-flex align-items-center multimenu-menu-indicator"><i
                                class="fa-solid fa-circle-small"></i>{{ trans('labels.privacypolicy') }}</span>
                    </a>
                </li>
                <li class="nav-item ps-4 mb-1">
                    <a class="nav-link {{ request()->is('admin/refund-policy*') ? 'active' : '' }}"
                        aria-current="page" href="{{ URL::to('/admin/refund-policy') }}">
                        <span class="d-flex align-items-center multimenu-menu-indicator"><i
                                class="fa-solid fa-circle-small"></i>{{ trans('labels.refund_policy') }}</span>
                    </a>
                </li>
                <li class="nav-item ps-4 mb-1">
                    <a class="nav-link {{ request()->is('admin/terms-conditions*') ? 'active' : '' }}"
                        aria-current="page" href="{{ URL::to('/admin/terms-conditions') }}">
                        <span class="d-flex align-items-center multimenu-menu-indicator"><i
                                class="fa-solid fa-circle-small"></i>{{ trans('labels.terms') }}</span>
                    </a>
                </li>
                <li class="nav-item ps-4 mb-1">
                    <a class="nav-link {{ request()->is('admin/aboutus*') ? 'active' : '' }}" aria-current="page"
                        href="{{ URL::to('/admin/aboutus') }}">
                        <span class="d-flex align-items-center multimenu-menu-indicator"><i
                                class="fa-solid fa-circle-small"></i>{{ trans('labels.about') }}</span>
                    </a>
                </li>
            </ul>
        </li>

        @if (App\Models\SystemAddons::where('unique_identifier', 'subscription')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'subscription')->first()->activated == 1)
            @if (App\Models\SystemAddons::where('unique_identifier', 'whatsapp_message')->first() != null &&
                    App\Models\SystemAddons::where('unique_identifier', 'whatsapp_message')->first()->activated == 1)
                @php
                    if ($user->allow_without_subscription == 1) {
                        $whatsapp_message = 1;
                    } else {
                        $whatsapp_message = @helper::get_plan($vendor_id)->whatsapp_message;
                    }
                @endphp

                @if ($whatsapp_message == 1)
                    <li
                        class="nav-item mb-2 fs-7 d-none"> {{-- V2: WhatsApp API-keys config removed (keys-free wa.me flow) --}}
                        <a class="nav-link d-flex align-items-center justify-content-between {{ request()->is('admin/whatsapp_settings*') ? 'active' : '' }}"
                            aria-current="page" href="{{ URL::to('/admin/whatsapp_settings') }}">
                            <div class="d-flex align-items-center">
                                <span
                                    class="{{ request()->is('admin/whatsapp_settings*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </span>
                                <span class="nav-text px-2">{{ trans('labels.whatsapp_settings') }}</span>
                            </div>
                            @if (env('Environment') == 'sendbox')
                                <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                            @endif
                        </a>
                    </li>
                @endif
            @endif
        @else
            @if (App\Models\SystemAddons::where('unique_identifier', 'whatsapp_message')->first() != null &&
                    App\Models\SystemAddons::where('unique_identifier', 'whatsapp_message')->first()->activated == 1)
                <li
                    class="nav-item mb-2 fs-7 d-none"> {{-- V2: WhatsApp API-keys config removed (keys-free wa.me flow) --}}
                    <a class="nav-link d-flex  align-items-center justify-content-between {{ request()->is('admin/whatsapp_settings*') ? 'active' : '' }}"
                        aria-current="page" href="{{ URL::to('/admin/whatsapp_settings') }}">
                        <div class="d-flex align-items-center">
                            <span
                                class="{{ request()->is('admin/whatsapp_settings*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                                <i class="fa-brands fa-whatsapp"></i>
                            </span>
                            <span class="nav-text px-2">{{ trans('labels.whatsapp_settings') }}</span>
                        </div>
                        @if (env('Environment') == 'sendbox')
                            <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                        @endif
                    </a>
                </li>
            @endif
        @endif
        @if (App\Models\SystemAddons::where('unique_identifier', 'subscription')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'subscription')->first()->activated == 1)
            @if (App\Models\SystemAddons::where('unique_identifier', 'telegram_message')->first() != null &&
                    App\Models\SystemAddons::where('unique_identifier', 'telegram_message')->first()->activated == 1)
                @php
                    if ($user->allow_without_subscription == 1) {
                        $telegram_message = 1;
                    } else {
                        $telegram_message = @helper::get_plan($vendor_id)->telegram_message;
                    }
                @endphp

                @if ($telegram_message == 1)
                    <li
                        class="nav-item mb-2 fs-7 d-none"> {{-- V2: Telegram config removed --}}
                        <a class="nav-link d-flex align-items-center justify-content-between {{ request()->is('admin/telegram_settings*') ? 'active' : '' }}"
                            aria-current="page" href="{{ URL::to('/admin/telegram_settings') }}">
                            <div class="d-flex align-items-center">
                                <span
                                    class="{{ request()->is('admin/telegram_settings*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                                    <i class="fa-brands fa-telegram"></i>
                                </span>
                                <span class="nav-text px-2">{{ trans('labels.telegram_settings') }}</span>
                            </div>
                            @if (env('Environment') == 'sendbox')
                                <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                            @endif
                        </a>
                    </li>
                @endif
            @endif
        @else
            @if (App\Models\SystemAddons::where('unique_identifier', 'telegram_message')->first() != null &&
                    App\Models\SystemAddons::where('unique_identifier', 'telegram_message')->first()->activated == 1)
                <li
                    class="nav-item mb-2 fs-7 d-none"> {{-- V2: Telegram config removed --}}
                    <a class="nav-link d-flex  align-items-center justify-content-between {{ request()->is('admin/telegram_settings*') ? 'active' : '' }}"
                        aria-current="page" href="{{ URL::to('/admin/telegram_settings') }}">
                        <div class="d-flex align-items-center">
                            <span
                                class="{{ request()->is('admin/telegram_settings*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                                <i class="fa-brands fa-telegram"></i>
                            </span>
                            <span class="nav-text px-2">{{ trans('labels.telegram_settings') }}</span>
                        </div>
                        @if (env('Environment') == 'sendbox')
                            <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                        @endif
                    </a>
                </li>
            @endif
        @endif
        <li class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_share') == 1 ? 'd-block' : 'd-none' }}">
            <a class="nav-link d-flex align-items-center {{ request()->is('admin/share*') ? 'active' : '' }}"
                aria-current="page" href="{{ URL::to('admin/share') }}">
                <span class="{{ request()->is('admin/share*') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                    <i class="fa-solid fa-share-from-square"></i>
                </span>
                <span class="px-2">{{ trans('labels.share') }}</span>
            </a>
        </li>
        @if (App\Models\SystemAddons::where('unique_identifier', 'language')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'language')->first()->activated == 1)
            @if (helper::listoflanguage()->count() > 1)
                <li
                    class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_language_settings') == 1 ? 'd-block' : 'd-none' }}">
                    <a class="nav-link d-flex align-items-center justify-content-between {{ request()->is('admin/language-settings') ? 'active' : '' }}"
                        aria-current="page" href="{{ URL::to('admin/language-settings') }}">
                        <div class="d-flex align-items-center">
                            <span
                                class="{{ request()->is('admin/language-settings') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                                <i class="fa-solid fa-language"></i>
                            </span>
                            <span class="px-2">{{ trans('labels.language-settings') }}</span>
                        </div>
                        @if (env('Environment') == 'sendbox')
                            <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                        @endif
                    </a>
                </li>
            @endif
        @endif
        @if (App\Models\SystemAddons::where('unique_identifier', 'currency_settigns')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'currency_settigns')->first()->activated == 1)
            @if (helper::listoflanguage()->count() > 1)
                <li
                    class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_currency_settings') == 1 ? 'd-block' : 'd-none' }}">
                    <a class="nav-link d-flex align-items-center justify-content-between {{ request()->is('admin/currency-settings') ? 'active' : '' }}"
                        aria-current="page" href="{{ URL::to('admin/currency-settings') }}">
                        <div class="d-flex align-items-center">
                            <span
                                class="{{ request()->is('admin/currency-settings') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                                <i class="fa-solid fa-dollar-sign"></i>
                            </span>
                            <span class="px-2">{{ trans('labels.currency-settings') }}</span>
                        </div>
                        @if (env('Environment') == 'sendbox')
                            <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                        @endif
                    </a>
                </li>
            @endif
        @endif

    @endif

    @if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1))
        @if (App\Models\SystemAddons::where('unique_identifier', 'language')->first() != null &&
                App\Models\SystemAddons::where('unique_identifier', 'language')->first()->activated == 1)
            <li
                class="nav-item mb-3 fs-7 {{ helper::check_menu($role_id, 'role_language_settings') == 1 ? 'd-block' : 'd-none' }}">
                <a class="nav-link d-flex align-items-center justify-content-between {{ request()->is('admin/language-settings*') ? 'active' : '' }}"
                    aria-current="page" href="{{ URL::to('admin/language-settings') }}">
                    <div class="d-flex align-items-center">
                        <span
                            class="{{ request()->is('admin/language-settings') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                            <i class="fa-solid fa-language"></i>
                        </span>
                        <span class="px-2">{{ trans('labels.language-settings') }}</span>
                    </div>
                    @if (env('Environment') == 'sendbox')
                        <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                    @endif
                </a>
            </li>
        @endif
        <li
            class="nav-item mb-3 fs-7 {{ helper::check_menu($role_id, 'role_currency_settings') == 1 ? 'd-block' : 'd-none' }}">
            <a class="nav-link collapsed d-flex align-items-center justify-content-between dropdown-toggle mb-1"
                href="#currency_setting" data-bs-toggle="collapse" role="button" aria-expanded="false"
                aria-controls="currency_setting">
               
                <div class="d-flex align-items-center">
                    <span
                        class="{{ request()->is('admin/language-settings') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                        <i class="fa-solid fa-dollar-sign"></i>
                    </span>
                    <span class="px-2">{{ trans('labels.currency-settings') }}</span>
                </div>
                @if (env('Environment') == 'sendbox')
                    <span class="badge badge bg-danger float-right">{{ trans('labels.addon') }}</span>
                @endif
            </a>
            <ul class="collapse" id="currency_setting">
                @if (App\Models\SystemAddons::where('unique_identifier', 'currency_settigns')->first() != null &&
                        App\Models\SystemAddons::where('unique_identifier', 'currency_settigns')->first()->activated == 1)
                    <li class="nav-item ps-4 mb-1">
                        <a class="nav-link {{ request()->is('admin/currencys*') ? 'active' : '' }}"
                            aria-current="page" href="{{ URL::to('/admin/currencys') }}">
                            <span class="d-flex align-items-center multimenu-menu-indicator"><i
                                    class="fa-solid fa-circle-small"></i>{{ trans('labels.currency') }}</span>
                        </a>
                    </li>
                @endif
                <li class="nav-item ps-4 mb-1">
                    <a class="nav-link {{ request()->is('admin/currency-settings*') ? 'active' : '' }}"
                        aria-current="page" href="{{ URL::to('/admin/currency-settings') }}">
                        <span class="d-flex align-items-center multimenu-menu-indicator"><i
                                class="fa-solid fa-circle-small"></i>{{ trans('labels.currency-settings') }}</span>
                    </a>
                </li>

            </ul>
        </li>
    @endif
    <li class="nav-item mb-2 fs-7 {{ helper::check_menu($role_id, 'role_settings') == 1 ? 'd-block' : 'd-none' }}">
        <a class="nav-link d-flex align-items-center {{ request()->is('admin/settings') ? 'active' : '' }}"
            aria-current="page" href="{{ URL::to('admin/settings') }}">
            <span class="{{ request()->is('admin/settings') ? 'sidebariconbox' : 'sidebariconbox1' }}">
                <i class="fa-solid fa-gears"></i>
            </span>
            <span class="px-2">{{ trans('labels.settings') }}</span>
        </a>
    </li>

</ul>
