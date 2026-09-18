@extends('admin.layout.default')
@section('content')
    @php
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $user = App\Models\User::where('id', $vendor_id)->where('is_available', 1)->where('is_deleted', 2)->first();
    @endphp
    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-12">
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.settings') }}</h5>
            @include('admin.layout.breadcrumb')
        </div>
    </div>
    <div class="col-12 settings mt-3 mb-7">
        <div class="col-xl-12 mb-4">
            <div class="card card-sticky-top border-0 box-shadow">
                <div class="card-body">
                    <nav class="scrolling-wrapper">
                        <ul class="d-flex align-items-center flex-md-wrap general_settings list-options gap-2">
                            <li>
                                <a data_attribute="basicinfo"
                                    class="list-group-item basicinfo p-2 px-3 list-item-secondary d-flex align-items-baseline color-changer active"
                                    aria-current="true">
                                    <i class="fa-solid fa-circle-info"></i>
                                    <p class="px-2">{{ trans('labels.basic_info') }}</p>
                                </a>
                            </li>
                            @if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1))
                                <li>
                                    <a data_attribute="themesettings"
                                        class="list-group-item basicinfo p-2 px-3 list-item-secondary d-flex align-items-baseline color-changer"
                                        aria-current="true">
                                        <i class="fa-solid fa-screwdriver-wrench"></i>
                                        <p class="px-2">{{ trans('labels.theme_settings') }}</p>
                                    </a>
                                </li>
                            @endif
                            <li>
                                <a data_attribute="editprofile"
                                    class="list-group-item basicinfo p-2 px-3 list-item-secondary d-flex align-items-baseline color-changer"
                                    aria-current="true">
                                    <i class="fa-solid fa-user-pen"></i>
                                    <p class="px-2">{{ trans('labels.edit_profile') }}</p>
                                </a>
                            </li>
                            <li>
                                <a data_attribute="changepasssword"
                                    class="list-group-item basicinfo p-2 px-3 color-changer list-item-secondary d-flex justify-content-between align-items-baseline"
                                    aria-current="true">
                                    <i class="fa-solid fa-unlock"></i>
                                    <p class="px-2">{{ trans('labels.change_password') }}</p>
                                </a>
                            </li>
                            <li>
                                <a data_attribute="seo"
                                    class="list-group-item basicinfo p-2 px-3 list-item-secondary d-flex align-items-baseline color-changer w-100"
                                    aria-current="true">
                                    <i class="fa-solid fa-chart-line"></i>
                                    <p class="px-2">{{ trans('labels.seo') }}</p>
                                </a>
                            </li>
                            @if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1))
                                <li>
                                    <a data_attribute="landing_page"
                                        class="list-group-item basicinfo p-2 px-3 list-item-secondary d-flex align-items-baseline color-changer w-100"
                                        aria-current="true">
                                        <i class="fa-solid fa-clipboard"></i>
                                        <p class="px-2">{{ trans('labels.landing_page') }}</p>
                                    </a>
                                </li>
                                @if (App\Models\SystemAddons::where('unique_identifier', 'whatsapp_message')->first() != null &&
                                        App\Models\SystemAddons::where('unique_identifier', 'whatsapp_message')->first()->activated == 1)
                                    <li>
                                        <a data_attribute="whatsappmessagesettings"
                                            class="list-group-item basicinfo p-2 color-changer px-3 list-item-secondary d-flex justify-content-between align-items-baseline"
                                            aria-current="true">
                                            <i class="fa-brands fa-whatsapp"></i>
                                            <p class="px-2">
                                                {{ trans('labels.whatsapp_message_settings') }}
                                                @if (env('Environment') == 'sendbox')
                                                    <span class="badge badge bg-danger">{{ trans('labels.addon') }}</span>
                                                @endif
                                            </p>
                                        </a>
                                    </li>
                                @endif
                            @endif

                            @if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1))
                                @if (App\Models\SystemAddons::where('unique_identifier', 'google_login')->first() != null &&
                                        App\Models\SystemAddons::where('unique_identifier', 'google_login')->first()->activated == 1)
                                    <li>
                                        <a data_attribute="google_login_config"
                                            class="list-group-item basicinfo p-2 px-3 list-item-secondary d-flex align-items-baseline color-changer w-100"
                                            aria-current="true">
                                            <i class="fa-brands fa-google"></i>
                                            <p class="px-2">
                                                {{ trans('labels.google_login_config') }}
                                                @if (env('Environment') == 'sendbox')
                                                    <span class="badge badge bg-danger">{{ trans('labels.addon') }}</span>
                                                @endif
                                            </p>
                                        </a>
                                    </li>
                                @endif
                                @if (App\Models\SystemAddons::where('unique_identifier', 'facebook_login')->first() != null &&
                                        App\Models\SystemAddons::where('unique_identifier', 'facebook_login')->first()->activated == 1)
                                    <li>
                                        <a data_attribute="facebook_login_config"
                                            class="list-group-item basicinfo p-2 px-3 list-item-secondary d-flex align-items-baseline color-changer w-100"
                                            aria-current="true">
                                            <i class="fa-brands fa-facebook-f"></i>
                                            <p class="px-2">
                                                {{ trans('labels.facebook_login_config') }}
                                                @if (env('Environment') == 'sendbox')
                                                    <span class="badge badge bg-danger">{{ trans('labels.addon') }}</span>
                                                @endif
                                            </p>
                                        </a>
                                    </li>
                                @endif
                                @if (App\Models\SystemAddons::where('unique_identifier', 'trusted_badges')->first() != null &&
                                        App\Models\SystemAddons::where('unique_identifier', 'trusted_badges')->first()->activated == 1)
                                    <li>
                                        <a data_attribute="trusted_badges"
                                            class="list-group-item basicinfo p-2 px-3 list-item-secondary d-flex align-items-baseline color-changer w-100"
                                            aria-current="true">
                                            <i class="fa-sharp fa-solid fa-badge-check"></i>
                                            <p class="px-2">
                                                {{ trans('labels.trusted_badges') }}
                                                @if (env('Environment') == 'sendbox')
                                                    <span class="badge badge bg-danger">{{ trans('labels.addon') }}</span>
                                                @endif
                                            </p>
                                        </a>
                                    </li>
                                @endif
                                @if (App\Models\SystemAddons::where('unique_identifier', 'safe_secure_checkout')->first() != null &&
                                        App\Models\SystemAddons::where('unique_identifier', 'safe_secure_checkout')->first()->activated == 1)
                                    <li>
                                        <a data_attribute="safe_secure"
                                            class="list-group-item basicinfo p-2 px-3 list-item-secondary d-flex align-items-baseline color-changer w-100"
                                            aria-current="true">
                                            <i class="fa-solid fa-shield-check"></i>
                                            <p class="px-2">
                                                {{ trans('labels.safe_secure') }}
                                                @if (env('Environment') == 'sendbox')
                                                    <span class="badge badge bg-danger">{{ trans('labels.addon') }}</span>
                                                @endif
                                            </p>
                                        </a>
                                    </li>
                                @endif
                            @endif
                            @if (App\Models\SystemAddons::where('unique_identifier', 'email_settings')->first() != null &&
                                    App\Models\SystemAddons::where('unique_identifier', 'email_settings')->first()->activated == 1)
                                <li>
                                    <a data_attribute="email_smtp_configuration"
                                        class="list-group-item basicinfo p-2 px-3 list-item-secondary d-flex align-items-baseline color-changer w-100"
                                        aria-current="true">
                                        <i class="fa-solid fa-envelope"></i>
                                        <p class="px-2">
                                            {{ trans('labels.email_smtp_configuration') }}
                                            @if (env('Environment') == 'sendbox')
                                                <span class="badge badge bg-danger">{{ trans('labels.addon') }}</span>
                                            @endif
                                        </p>
                                    </a>
                                </li>
                                <li>
                                    <a data_attribute="email_template"
                                        class="list-group-item basicinfo p-2 px-3 list-item-secondary d-flex align-items-baseline color-changer w-100"
                                        aria-current="true">
                                        <i class="fa-sharp fa-solid fa-envelopes"></i>
                                        <p class="px-2">
                                            {{ trans('labels.email_message_settings') }}
                                            @if (env('Environment') == 'sendbox')
                                                <span class="badge badge bg-danger">{{ trans('labels.addon') }}</span>
                                            @endif
                                        </p>
                                    </a>
                                </li>
                            @endif
                            @if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1))
                                @if (App\Models\SystemAddons::where('unique_identifier', 'subscription')->first() != null &&
                                        App\Models\SystemAddons::where('unique_identifier', 'subscription')->first()->activated == 1)
                                    @if (App\Models\SystemAddons::where('unique_identifier', 'pwa')->first() != null &&
                                            App\Models\SystemAddons::where('unique_identifier', 'pwa')->first()->activated == 1)
                                        @php
                                            $checkplan = App\Models\Transaction::where('vendor_id', $vendor_id)
                                                ->orderByDesc('id')
                                                ->first();

                                            if ($user->allow_without_subscription == 1) {
                                                $pwa = 1;
                                            } else {
                                                $pwa = @$checkplan->pwa;
                                            }
                                        @endphp
                                        @if ($pwa == 1)
                                            <li>
                                                <a data_attribute="pwa"
                                                    class="list-group-item basicinfo p-2 px-3 list-item-secondary d-flex align-items-baseline color-changer w-100"
                                                    aria-current="true">
                                                    <i class="fa-regular fa-tablet-button"></i>
                                                    <p class="px-2">{{ trans('labels.pwa') }}
                                                        @if (env('Environment') == 'sendbox')
                                                            <span
                                                                class="badge badge bg-danger">{{ trans('labels.addon') }}</span>
                                                        @endif
                                                    </p>
                                                </a>
                                            </li>
                                        @endif
                                    @endif
                                @else
                                    @if (App\Models\SystemAddons::where('unique_identifier', 'pwa')->first() != null &&
                                            App\Models\SystemAddons::where('unique_identifier', 'pwa')->first()->activated == 1)
                                        <li>
                                            <a data_attribute="pwa"
                                                class="list-group-item basicinfo p-2 px-3 list-item-secondary d-flex align-items-baseline color-changer w-100"
                                                aria-current="true">
                                                <i class="fa-regular fa-tablet-button"></i>
                                                <p class="px-2">{{ trans('labels.pwa') }}
                                                    @if (env('Environment') == 'sendbox')
                                                        <span
                                                            class="badge badge bg-danger">{{ trans('labels.addon') }}</span>
                                                    @endif
                                                </p>
                                            </a>

                                        </li>
                                    @endif
                                @endif
                                @if (App\Models\SystemAddons::where('unique_identifier', 'pixel')->first() != null &&
                                        App\Models\SystemAddons::where('unique_identifier', 'pixel')->first()->activated == 1)
                                    <li>
                                        <a data_attribute="pixel_settings"
                                            class="list-group-item basicinfo p-2 px-3 list-item-secondary d-flex align-items-baseline color-changer w-100"
                                            aria-current="true">
                                            <i class="fa-regular fa-tablet-button"></i>
                                            <p class="px-2">{{ trans('labels.pixel_settings') }}
                                                @if (env('Environment') == 'sendbox')
                                                    <span class="badge badge bg-danger">{{ trans('labels.addon') }}</span>
                                                @endif
                                            </p>
                                        </a>

                                    </li>
                                @endif
                                @if (App\Models\SystemAddons::where('unique_identifier', 'subscription')->first() != null &&
                                        App\Models\SystemAddons::where('unique_identifier', 'subscription')->first()->activated == 1)
                                    @if (App\Models\SystemAddons::where('unique_identifier', 'loyalty_program')->first() != null &&
                                            App\Models\SystemAddons::where('unique_identifier', 'loyalty_program')->first()->activated == 1)
                                        <li>
                                            <a data_attribute="loyalty_program"
                                                class="list-group-item basicinfo p-2 px-3 list-item-secondary d-flex align-items-baseline color-changer w-100"
                                                aria-current="true">
                                                <i class="fa-solid fa-trophy"></i>
                                                <p class="px-2">{{ trans('labels.loyalty_program') }}
                                                    @if (env('Environment') == 'sendbox')
                                                        <span
                                                            class="badge badge bg-danger">{{ trans('labels.addon') }}</span>
                                                    @endif
                                                </p>
                                            </a>
                                        </li>
                                    @endif
                                @else
                                    @if (App\Models\SystemAddons::where('unique_identifier', 'loyalty_program')->first() != null &&
                                            App\Models\SystemAddons::where('unique_identifier', 'loyalty_program')->first()->activated == 1)
                                        <li>
                                            <a data_attribute="loyalty_program"
                                                class="list-group-item basicinfo p-2 px-3 list-item-secondary d-flex align-items-baseline color-changer w-100"
                                                aria-current="true">
                                                <i class="fa-solid fa-trophy"></i>
                                                <p class="px-2">{{ trans('labels.loyalty_program') }}
                                                    @if (env('Environment') == 'sendbox')
                                                        <span
                                                            class="badge badge bg-danger">{{ trans('labels.addon') }}</span>
                                                    @endif
                                                </p>
                                            </a>

                                        </li>
                                    @endif
                                @endif
                            @endif
                            @if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1))
                                @if (App\Models\SystemAddons::where('unique_identifier', 'custom_domain')->first() != null &&
                                        App\Models\SystemAddons::where('unique_identifier', 'custom_domain')->first()->activated == 1)
                                    <li>
                                        <a data_attribute="custom_domain"
                                            class="list-group-item basicinfo color-changer p-2 px-3 list-item-secondary d-flex justify-content-between align-items-baseline"
                                            aria-current="true">
                                            <i class="fa-solid fa-globe"></i>
                                            <p class="px-2">
                                                {{ trans('labels.custom_domain') }}
                                                @if (env('Environment') == 'sendbox')
                                                    <span class="badge badge bg-danger">{{ trans('labels.addon') }}</span>
                                                @endif
                                            </p>
                                        </a>
                                    </li>
                                @endif
                                @if (App\Models\SystemAddons::where('unique_identifier', 'google_recaptcha')->first() != null &&
                                        App\Models\SystemAddons::where('unique_identifier', 'google_recaptcha')->first()->activated == 1)
                                    <li>
                                        <a data_attribute="recaptcha"
                                            class="list-group-item basicinfo p-2 px-3 list-item-secondary d-flex align-items-baseline color-changer w-100"
                                            aria-current="true">
                                            <i class="fa-solid fa-gears"></i>
                                            <p class="px-2">
                                                {{ trans('labels.google_recaptcha') }}
                                                @if (env('Environment') == 'sendbox')
                                                    <span class="badge badge bg-danger">{{ trans('labels.addon') }}</span>
                                                @endif
                                            </p>
                                        </a>
                                    </li>
                                @endif
                                <li>
                                    <a data_attribute="fun_fact"
                                        class="list-group-item basicinfo p-2 px-3 list-item-secondary d-flex align-items-baseline color-changer"
                                        aria-current="true">
                                        <i class="fa-solid fa-rocket"></i>
                                        <p class="px-2">
                                            {{ trans('landing.fun_fact') }}
                                        </p>
                                    </a>
                                </li>
                            @endif
                            @if (App\Models\SystemAddons::where('unique_identifier', 'tawk')->first() != null &&
                                    App\Models\SystemAddons::where('unique_identifier', 'tawk')->first()->activated == 1)
                                <li>
                                    <a data_attribute="tawk_config"
                                        class="list-group-item basicinfo p-2 px-3 list-item-secondary d-flex align-items-baseline color-changer w-100"
                                        aria-current="true">
                                        <i class="fa-regular fa-message"></i>
                                        <p class="px-2">{{ trans('labels.tawk_config') }}
                                            @if (env('Environment') == 'sendbox')
                                                <span class="badge badge bg-danger">{{ trans('labels.addon') }}</span>
                                            @endif
                                        </p>
                                    </a>
                                </li>
                            @endif
                            @if (App\Models\SystemAddons::where('unique_identifier', 'wizz_chat')->first() != null &&
                                    App\Models\SystemAddons::where('unique_identifier', 'wizz_chat')->first()->activated == 1)
                                <li>
                                    <a data_attribute="wizz_chat_settings"
                                        class="list-group-item basicinfo p-2 px-3 list-item-secondary d-flex align-items-baseline color-changer"
                                        aria-current="true">
                                        <i class="fa-regular fa-message"></i>
                                        <p class="px-2">{{ trans('labels.wizz_chat_settings') }}
                                            @if (env('Environment') == 'sendbox')
                                                <span class="badge badge bg-danger">{{ trans('labels.addon') }}</span>
                                            @endif
                                        </p>
                                    </a>
                                </li>
                            @endif
                            @if (App\Models\SystemAddons::where('unique_identifier', 'quick_call')->first() != null &&
                                    App\Models\SystemAddons::where('unique_identifier', 'quick_call')->first()->activated == 1)
                                <li>
                                    <a data_attribute="quick_call"
                                        class="list-group-item basicinfo p-2 px-3 list-item-secondary d-flex align-items-baseline color-changer"
                                        aria-current="true">
                                        <i class="fa-duotone fa-solid fa-phone"></i>
                                        <p class="px-2">{{ trans('labels.quick_call') }}
                                            @if (env('Environment') == 'sendbox')
                                                <span class="badge badge bg-danger">{{ trans('labels.addon') }}</span>
                                            @endif
                                        </p>
                                    </a>
                                </li>
                            @endif
                            @if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1))
                                @if (App\Models\SystemAddons::where('unique_identifier', 'vendor_tip')->first() != null &&
                                        App\Models\SystemAddons::where('unique_identifier', 'vendor_tip')->first()->activated == 1)
                                    <li>
                                        <a data_attribute="tips_settings"
                                            class="list-group-item basicinfo p-2 px-3 list-item-secondary d-flex align-items-baseline color-changer"
                                            aria-current="true">
                                            <i class="fa-solid fa-coins"></i>
                                            <p class="px-2">
                                                {{ trans('labels.tips_settings') }}
                                                @if (env('Environment') == 'sendbox')
                                                    <span class="badge badge bg-danger">{{ trans('labels.addon') }}</span>
                                                @endif
                                            </p>
                                        </a>
                                    </li>
                                @endif
                                @if (App\Models\SystemAddons::where('unique_identifier', 'sales_notification')->first() != null &&
                                        App\Models\SystemAddons::where('unique_identifier', 'sales_notification')->first()->activated == 1)
                                    <li>
                                        <a data_attribute="fake_sales_notification"
                                            class="list-group-item basicinfo p-2 px-3 list-item-secondary d-flex align-items-baseline color-changer"
                                            aria-current="true">
                                            <i class="fa-regular fa-bell"></i>
                                            <p class="px-2">{{ trans('labels.fake_sales_notification') }}
                                                @if (env('Environment') == 'sendbox')
                                                    <span class="badge badge bg-danger">{{ trans('labels.addon') }}</span>
                                                @endif
                                            </p>
                                        </a>
                                    </li>
                                @endif
                                @if (App\Models\SystemAddons::where('unique_identifier', 'fake_view')->first() != null &&
                                        App\Models\SystemAddons::where('unique_identifier', 'fake_view')->first()->activated == 1)
                                    <li>
                                        <a data_attribute="product_fake_view"
                                            class="list-group-item basicinfo p-2 px-3 list-item-secondary d-flex align-items-baseline color-changer"
                                            aria-current="true">
                                            <i class="fa-sharp fa-regular fa-eye"></i>
                                            <p class="px-2">{{ trans('labels.product_fake_view') }}
                                                @if (env('Environment') == 'sendbox')
                                                    <span class="badge badge bg-danger">{{ trans('labels.addon') }}</span>
                                                @endif
                                            </p>
                                        </a>
                                    </li>
                                @endif
                                @if (App\Models\SystemAddons::where('unique_identifier', 'age_verification')->first() != null &&
                                        App\Models\SystemAddons::where('unique_identifier', 'age_verification')->first()->activated == 1)
                                    <li>
                                        <a data_attribute="age_verification"
                                            class="list-group-item basicinfo p-2 px-3 list-item-secondary d-flex align-items-baseline color-changer"
                                            aria-current="true">
                                            <i class="fa-sharp fa-regular fa-badge-check"></i>
                                            <p class="px-2">{{ trans('labels.age_verification') }}
                                                @if (env('Environment') == 'sendbox')
                                                    <span class="badge badge bg-danger">{{ trans('labels.addon') }}</span>
                                                @endif
                                            </p>
                                        </a>
                                    </li>
                                @endif
                                @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                                        App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                                    <li>
                                        <a data_attribute="review_settings"
                                            class="list-group-item basicinfo p-2 px-3 list-item-secondary d-flex align-items-baseline color-changer"
                                            aria-current="true">
                                            <i class="fa-regular fa-star"></i>
                                            <p class="px-2">{{ trans('labels.review_settings') }}
                                                @if (env('Environment') == 'sendbox')
                                                    <span class="badge badge bg-danger">{{ trans('labels.addon') }}</span>
                                                @endif
                                            </p>
                                        </a>
                                    </li>
                                @endif
                                @if (App\Models\SystemAddons::where('unique_identifier', 'user_app')->first() != null &&
                                        App\Models\SystemAddons::where('unique_identifier', 'user_app')->first()->activated == 1)
                                    <li>
                                        <a data_attribute="app_section"
                                            class="list-group-item basicinfo p-2 px-3 list-item-secondary d-flex align-items-baseline color-changer"
                                            aria-current="true">
                                            <i class="fa-solid fa-circle-info"></i>
                                            <p class="px-2">{{ trans('labels.mobile_app_section') }}
                                                @if (env('Environment') == 'sendbox')
                                                    <span class="badge badge bg-danger">{{ trans('labels.addon') }}</span>
                                                @endif
                                            </p>
                                        </a>
                                    </li>
                                @endif
                                @if (App\Models\SystemAddons::where('unique_identifier', 'cart_checkout_countdown')->first() != null &&
                                        App\Models\SystemAddons::where('unique_identifier', 'cart_checkout_countdown')->first()->activated == 1)
                                    <li>
                                        <a data_attribute="cart_checkout_countdown"
                                            class="list-group-item basicinfo p-2 px-3 list-item-secondary d-flex align-items-baseline color-changer"
                                            aria-current="true">
                                            <i class="fa-solid fa-clock"></i>
                                            <p class="px-2">{{ trans('labels.cart_checkout_countdown') }}
                                                @if (env('Environment') == 'sendbox')
                                                    <span class="badge badge bg-danger">{{ trans('labels.addon') }}</span>
                                                @endif
                                            </p>
                                        </a>
                                    </li>
                                @endif
                                @if (App\Models\SystemAddons::where('unique_identifier', 'cart_checkout_progressbar')->first() != null &&
                                        App\Models\SystemAddons::where('unique_identifier', 'cart_checkout_progressbar')->first()->activated == 1)
                                    <li>
                                        <a data_attribute="cart_checkout_progressbar"
                                            class="list-group-item basicinfo p-2 px-3 list-item-secondary d-flex align-items-baseline color-changer"
                                            aria-current="true">
                                            <i class="fa-regular fa-bars-progress"></i>
                                            <p class="px-2">{{ trans('labels.cart_checkout_progressbar') }}
                                                @if (env('Environment') == 'sendbox')
                                                    <span class="badge badge bg-danger">{{ trans('labels.addon') }}</span>
                                                @endif
                                            </p>
                                        </a>
                                    </li>
                                @endif
                                <li>
                                    <a data_attribute="estimated_delivery"
                                        class="list-group-item basicinfo p-2 px-3 list-item-secondary d-flex align-items-baseline color-changer"
                                        aria-current="true">
                                        <i class="fa-regular fa-angle"></i>
                                        <p class="px-2">{{ trans('labels.estimated_delivery') }}</p>
                                    </a>
                                </li>
                            @endif
                            <li>
                                <a data_attribute="social_links"
                                    class="list-group-item basicinfo p-2 px-3 list-item-secondary d-flex align-items-baseline color-changer"
                                    aria-current="true">
                                    <i class="fa-solid fa-link"></i>
                                    <p class="px-2">{{ trans('labels.social_links') }}</p>
                                </a>
                            </li>
                            <li>
                                <a data_attribute="maintenance_mode"
                                    class="list-group-item basicinfo p-2 px-3 list-item-secondary d-flex align-items-baseline color-changer"
                                    aria-current="true">
                                    <i class="fa-regular fa-angle"></i>
                                    <p class="px-2">{{ trans('labels.maintenance_mode') }}</p>
                                </a>
                            </li>
                            <li>
                                <a data_attribute="other"
                                    class="list-group-item basicinfo p-2 px-3 list-item-secondary d-flex align-items-baseline color-changer"
                                    aria-current="true">
                                    <i class="fa-solid fa-gears"></i>
                                    <p class="px-2">{{ trans('labels.other') }}</p>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        <div class="col-xl-12">
            <div id="settingmenuContent">
                <div id="basicinfo" class="hidechild">
                    <div class="col-12">
                        <div class="card overflow-hidden border-0 box-shadow">
                            <div class="card-header bg-secondary py-3 d-flex align-items-center text-white">
                                <i class="fa-solid fa-circle-info fs-5"></i>
                                <h5 class="px-2">{{ trans('labels.basic_info') }}</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ URL::to('admin/settings/update') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="row">
                                                

                                                    @if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1))
                                                        <div
                                                            class="d-flex justify-content-start mt-2 mt-md-0  {{ env('Environment') == 'sendbox' ? 'col-md-6 ' : 'col-md-6' }}">
                                                            <div>
                                                                <label class="form-label"
                                                                    for="">{{ trans('labels.vendor_register') }}
                                                                </label>
                                                                <input id="vendor_register-switch" type="checkbox"
                                                                    class="checkbox-switch" name="vendor_register"
                                                                    value="1"
                                                                    {{ $settingdata->vendor_register == 1 ? 'checked' : '' }}>
                                                                <label for="vendor_register-switch" class="switch">
                                                                    <span
                                                                        class="{{ session()->get('direction') == 2 ? 'switch__circle-rtl' : 'switch__circle' }}"><span
                                                                            class="switch__circle-inner"></span></span>
                                                                    <span
                                                                        class="switch__left {{ session()->get('direction') == 2 ? 'pe-2' : 'ps-2' }}">{{ trans('labels.off') }}</span>
                                                                    <span
                                                                        class="switch__right {{ session()->get('direction') == 2 ? 'ps-2' : 'pe-2' }}">{{ trans('labels.on') }}</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 form-group">
                                                            <label class="form-label"
                                                                for="">{{ trans('labels.image_size') }}<span
                                                                    class="text-danger"> * </span>
                                                            </label>
                                                            <input type="text" step="any"
                                                                class="form-control numbers_only" name="image_size"
                                                                value="{{ @$settingdata->image_size }}"
                                                                placeholder="{{ trans('labels.image_size') }}" required>

                                                        </div>
                                                    @endif

                                                    @if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1))
                                                        @if (App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first() != null &&
                                                                App\Models\SystemAddons::where('unique_identifier', 'customer_login')->first()->activated == 1)
                                                            <div class="col-md-2" id="checkout_login_required">
                                                                <label class="form-label"
                                                                    for="">{{ trans('labels.checkout_login_required') }}
                                                                </label>
                                                                @if (env('Environment') == 'sendbox')
                                                                    <span
                                                                        class="badge badge bg-danger ms-2 mb-0">{{ trans('labels.addon') }}</span>
                                                                @endif
                                                                <input id="checkout_login_required-switch" type="checkbox"
                                                                    class="checkbox-switch" name="checkout_login_required"
                                                                    value="1"
                                                                    {{ $settingdata->checkout_login_required == 1 ? 'checked' : '' }}>
                                                                <label for="checkout_login_required-switch"
                                                                    class="switch">
                                                                    <span
                                                                        class="{{ session()->get('direction') == 2 ? 'switch__circle-rtl' : 'switch__circle' }}"><span
                                                                            class="switch__circle-inner"></span></span>
                                                                    <span
                                                                        class="switch__left {{ session()->get('direction') == 2 ? 'pe-2' : 'ps-2' }}">{{ trans('labels.off') }}</span>
                                                                    <span
                                                                        class="switch__right {{ session()->get('direction') == 2 ? 'ps-2' : 'pe-2' }}">{{ trans('labels.on') }}</span>
                                                                </label>
                                                            </div>
                                                            <div class="col-md-2 {{ $settingdata->checkout_login_required == 1 ? '' : 'd-none' }}"
                                                                id="is_checkout_login_required">
                                                                <label class="form-label"
                                                                    for="">{{ trans('labels.is_checkout_login_required') }}
                                                                </label>
                                                                @if (env('Environment') == 'sendbox')
                                                                    <span
                                                                        class="badge badge bg-danger ms-2 mb-0">{{ trans('labels.addon') }}</span>
                                                                @endif
                                                                <input id="is_checkout_login_required-switch"
                                                                    type="checkbox" class="checkbox-switch"
                                                                    name="is_checkout_login_required" value="1"
                                                                    {{ $settingdata->is_checkout_login_required == 1 ? 'checked' : '' }}>
                                                                <label for="is_checkout_login_required-switch"
                                                                    class="switch">
                                                                    <span
                                                                        class="{{ session()->get('direction') == 2 ? 'switch__circle-rtl' : 'switch__circle' }}"><span
                                                                            class="switch__circle-inner"></span></span>
                                                                    <span
                                                                        class="switch__left {{ session()->get('direction') == 2 ? 'pe-2' : 'ps-2' }}">{{ trans('labels.off') }}</span>
                                                                    <span
                                                                        class="switch__right {{ session()->get('direction') == 2 ? 'ps-2' : 'pe-2' }}">{{ trans('labels.on') }}</span>
                                                                </label>
                                                            </div>
                                                        @endif
                                                    @endif

                                                    @if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1))
                                                        <div class="col-md-4 form-group">
                                                            <label
                                                                class="form-label">{{ trans('labels.order_prefix_number') }}<span
                                                                    class="text-danger"> * </span></label>
                                                            <input type="text" class="form-control w-10"
                                                                name="order_prefix"
                                                                value="{{ @$settingdata->order_prefix }}"
                                                                placeholder="{{ trans('labels.order_prefix_number') }}"
                                                                onkeyup="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')"
                                                                required>
                                                        </div>
                                                        @if ($order->count() == 0)
                                                            <div class="col-md-4 form-group">
                                                                <label
                                                                    class="form-label">{{ trans('labels.order_number_start') }}<span
                                                                        class="text-danger"> * </span></label>
                                                                <input type="text" class="form-control numbers_only"
                                                                    name="order_number_start"
                                                                    value="{{ @$settingdata->order_number_start }}"
                                                                    placeholder="{{ trans('labels.order_number_start') }}"
                                                                    onkeyup="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')"
                                                                    required>
                                                            </div>
                                                        @endif
                                                        <div class="col-md-4 form-group">
                                                            <label
                                                                class="form-label">{{ trans('labels.min_order_amount') }}</label>
                                                            <input type="text" class="form-control w-10 numbers_only"
                                                                name="min_order_amount"
                                                                value="{{ @$settingdata->min_order_amount }}"
                                                                placeholder="{{ trans('labels.min_order_amount') }}"
                                                                onkeyup="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')">
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="{{ env('Environment') == 'sendbox' ? 'col-md-6' : 'col-md-6' }} form-group">
                                            <label class="form-label" for="">{{ trans('labels.time_format') }}
                                            </label>
                                            <select class="form-select" name="time_format">
                                                <option value="2"
                                                    {{ $settingdata->time_format == 2 ? 'selected' : '' }}>12
                                                    {{ trans('labels.hour') }}
                                                </option>
                                                <option value="1"
                                                    {{ $settingdata->time_format == 1 ? 'selected' : '' }}>24
                                                    {{ trans('labels.hour') }}
                                                </option>
                                            </select>
                                        </div>
                                        <div
                                            class="{{ env('Environment') == 'sendbox' ? 'col-md-6' : 'col-md-6' }} form-group">
                                            <label class="form-label" for="">{{ trans('labels.date_format') }}
                                            </label>

                                            <select class="form-select" name="date_format">
                                                <option value="d M, Y"
                                                    {{ $settingdata->date_format == 'd M, Y' ? 'selected' : '' }}>dd
                                                    MMM, yyyy</option>
                                                <option value="M d, Y"
                                                    {{ $settingdata->date_format == 'M d, Y' ? 'selected' : '' }}>MMM
                                                    dd, yyyy</option>
                                                <option value="d-m-Y"
                                                    {{ $settingdata->date_format == 'd-m-Y' ? 'selected' : '' }}>
                                                    dd-MM-yyyy</option>
                                                <option value="m-d-Y"
                                                    {{ $settingdata->date_format == 'm-d-Y' ? 'selected' : '' }}>
                                                    MM-dd-yyyy</option>
                                                <option value="d/m/Y"
                                                    {{ $settingdata->date_format == 'd/m/Y' ? 'selected' : '' }}>
                                                    dd/MM/yyyy</option>
                                                <option value="m/d/Y"
                                                    {{ $settingdata->date_format == 'm/d/Y' ? 'selected' : '' }}>
                                                    MM/dd/yyyy</option>
                                                <option value="Y/m/d"
                                                    {{ $settingdata->date_format == 'Y/m/d' ? 'selected' : '' }}>
                                                    yyyy/MM/dd</option>
                                                <option value="Y-m-d"
                                                    {{ $settingdata->date_format == 'Y-m-d' ? 'selected' : '' }}>
                                                    yyyy-MM-dd</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label">{{ trans('labels.time_zone') }}</label>
                                                <select class="form-select" name="timezone">
                                                    <option
                                                        {{ @$settingdata->timezone == 'Pacific/Midway' ? 'selected' : '' }}
                                                        value="Pacific/Midway">(GMT-11:00) Midway Island, Samoa
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'America/Adak' ? 'selected' : '' }}
                                                        value="America/Adak">(GMT-10:00) Hawaii-Aleutian
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Etc/GMT+10' ? 'selected' : '' }}
                                                        value="Etc/GMT+10">(GMT-10:00) Hawaii</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Pacific/Marquesas' ? 'selected' : '' }}
                                                        value="Pacific/Marquesas">(GMT-09:30) Marquesas Islands
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Pacific/Gambier' ? 'selected' : '' }}
                                                        value="Pacific/Gambier">(GMT-09:00) Gambier Islands
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'America/Anchorage' ? 'selected' : '' }}
                                                        value="America/Anchorage">(GMT-09:00) Alaska</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'America/Ensenada' ? 'selected' : '' }}
                                                        value="America/Ensenada">(GMT-08:00) Tijuana, Baja
                                                        California </option>
                                                    <option {{ @$settingdata->timezone == 'Etc/GMT+8' ? 'selected' : '' }}
                                                        value="Etc/GMT+8">(GMT-08:00) Pitcairn Islands</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'America/Los_Angeles' ? 'selected' : '' }}
                                                        value="America/Los_Angeles">(GMT-08:00) Pacific Time
                                                        (US
                                                        &amp; Canada) </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'America/Denver' ? 'selected' : '' }}
                                                        value="America/Denver">(GMT-07:00) Mountain Time (US
                                                        &amp;
                                                        Canada) </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'America/Chihuahua' ? 'selected' : '' }}
                                                        value="America/Chihuahua">(GMT-07:00) Chihuahua, La
                                                        Paz,
                                                        Mazatlan </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'America/Dawson_Creek' ? 'selected' : '' }}
                                                        value="America/Dawson_Creek">(GMT-07:00) Arizona
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'America/Belize' ? 'selected' : '' }}
                                                        value="America/Belize">(GMT-06:00) Saskatchewan,
                                                        Central
                                                        America </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'America/Cancun' ? 'selected' : '' }}
                                                        value="America/Cancun">(GMT-06:00) Guadalajara, Mexico
                                                        City,
                                                        Monterrey </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Chile/EasterIsland' ? 'selected' : '' }}
                                                        value="Chile/EasterIsland">(GMT-06:00) Easter Island
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'America/Chicago' ? 'selected' : '' }}
                                                        value="America/Chicago">(GMT-06:00) Central Time (US
                                                        &amp;
                                                        Canada) </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'America/New_York' ? 'selected' : '' }}
                                                        value="America/New_York">(GMT-05:00) Eastern Time (US
                                                        &amp;
                                                        Canada) </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'America/Havana' ? 'selected' : '' }}
                                                        value="America/Havana">(GMT-05:00) Cuba</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'America/Bogota' ? 'selected' : '' }}
                                                        value="America/Bogota">(GMT-05:00) Bogota, Lima, Quito,
                                                        Rio
                                                        Branco </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'America/Caracas' ? 'selected' : '' }}
                                                        value="America/Caracas">(GMT-04:30) Caracas</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'America/Santiago' ? 'selected' : '' }}
                                                        value="America/Santiago">(GMT-04:00) Santiago</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'America/La_Paz' ? 'selected' : '' }}
                                                        value="America/La_Paz">(GMT-04:00) La Paz</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Atlantic/Stanley' ? 'selected' : '' }}
                                                        value="Atlantic/Stanley">(GMT-04:00) Faukland Islands
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'America/Campo_Grande' ? 'selected' : '' }}
                                                        value="America/Campo_Grande">(GMT-04:00) Brazil
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'America/Goose_Bay' ? 'selected' : '' }}
                                                        value="America/Goose_Bay">(GMT-04:00) Atlantic Time
                                                        (Goose
                                                        Bay) </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'America/Glace_Bay' ? 'selected' : '' }}
                                                        value="America/Glace_Bay">(GMT-04:00) Atlantic Time
                                                        (Canada) </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'America/St_Johns' ? 'selected' : '' }}
                                                        value="America/St_Johns">(GMT-03:30) Newfoundland
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'America/Araguaina' ? 'selected' : '' }}
                                                        value="America/Araguaina">(GMT-03:00) UTC-3</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'America/Montevideo' ? 'selected' : '' }}
                                                        value="America/Montevideo">(GMT-03:00) Montevideo
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'America/Miquelon' ? 'selected' : '' }}
                                                        value="America/Miquelon">(GMT-03:00) Miquelon, St.
                                                        Pierre
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'America/Godthab' ? 'selected' : '' }}
                                                        value="America/Godthab">(GMT-03:00) Greenland</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'America/Argentina' ? 'selected' : '' }}
                                                        value="America/Argentina/Buenos_Aires">(GMT-03:00)
                                                        Buenos
                                                        Aires </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'America/Sao_Paulo' ? 'selected' : '' }}
                                                        value="America/Sao_Paulo">(GMT-03:00) Brasilia</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'America/Noronha' ? 'selected' : '' }}
                                                        value="America/Noronha">(GMT-02:00) Mid-Atlantic
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Atlantic/Cape_Verde' ? 'selected' : '' }}
                                                        value="Atlantic/Cape_Verde">(GMT-01:00) Cape Verde Is.
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Atlantic/Azores' ? 'selected' : '' }}
                                                        value="Atlantic/Azores">(GMT-01:00) Azores</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Europe/Belfast' ? 'selected' : '' }}
                                                        value="Europe/Belfast">(GMT) Greenwich Mean Time :
                                                        Belfast
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Europe/Dublin' ? 'selected' : '' }}
                                                        value="Europe/Dublin">(GMT) Greenwich Mean Time :
                                                        Dublin
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Europe/Lisbon' ? 'selected' : '' }}
                                                        value="Europe/Lisbon">(GMT) Greenwich Mean Time :
                                                        Lisbon
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Europe/London' ? 'selected' : '' }}
                                                        value="Europe/London">(GMT) Greenwich Mean Time :
                                                        London
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Africa/Abidjan' ? 'selected' : '' }}
                                                        value="Africa/Abidjan">(GMT) Monrovia, Reykjavik
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Europe/Amsterdam' ? 'selected' : '' }}
                                                        value="Europe/Amsterdam">(GMT+01:00) Amsterdam, Berlin,
                                                        Bern, Rome, Stockholm, Vienna</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Europe/Belgrade' ? 'selected' : '' }}
                                                        value="Europe/Belgrade">(GMT+01:00) Belgrade,
                                                        Bratislava,
                                                        Budapest, Ljubljana, Prague</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Europe/Brussels' ? 'selected' : '' }}
                                                        value="Europe/Brussels">(GMT+01:00) Brussels,
                                                        Copenhagen,
                                                        Madrid, Paris </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Africa/Algiers' ? 'selected' : '' }}
                                                        value="Africa/Algiers">(GMT+01:00) West Central Africa
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Africa/Windhoek' ? 'selected' : '' }}
                                                        value="Africa/Windhoek">(GMT+01:00) Windhoek</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Asia/Beirut' ? 'selected' : '' }}
                                                        value="Asia/Beirut">(GMT+02:00) Beirut</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Africa/Cairo' ? 'selected' : '' }}
                                                        value="Africa/Cairo">(GMT+02:00) Cairo</option>
                                                    <option {{ @$settingdata->timezone == 'Asia/Gaza' ? 'selected' : '' }}
                                                        value="Asia/Gaza">(GMT+02:00) Gaza</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Africa/Blantyre' ? 'selected' : '' }}
                                                        value="Africa/Blantyre">(GMT+02:00) Harare, Pretoria
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Asia/Jerusalem' ? 'selected' : '' }}
                                                        value="Asia/Jerusalem">(GMT+02:00) Jerusalem</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Europe/Minsk' ? 'selected' : '' }}
                                                        value="Europe/Minsk">(GMT+02:00) Minsk</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Asia/Damascus' ? 'selected' : '' }}
                                                        value="Asia/Damascus">(GMT+02:00) Syria</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Europe/Moscow' ? 'selected' : '' }}
                                                        value="Europe/Moscow">(GMT+03:00) Moscow, St.
                                                        Petersburg,
                                                        Volgograd </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Africa/Addis_Ababa' ? 'selected' : '' }}
                                                        value="Africa/Addis_Ababa">(GMT+03:00) Nairobi</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Asia/Tehran' ? 'selected' : '' }}
                                                        value="Asia/Tehran">(GMT+03:30) Tehran</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Asia/Dubai' ? 'selected' : '' }}
                                                        value="Asia/Dubai">(GMT+04:00) Abu Dhabi, Muscat
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Asia/Yerevan' ? 'selected' : '' }}
                                                        value="Asia/Yerevan">(GMT+04:00) Yerevan</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Asia/Kabul' ? 'selected' : '' }}
                                                        value="Asia/Kabul">(GMT+04:30) Kabul</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Asia/Yekaterinburg' ? 'selected' : '' }}
                                                        value="Asia/Yekaterinburg">(GMT+05:00) Ekaterinburg
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Asia/Tashkent' ? 'selected' : '' }}
                                                        value="Asia/Tashkent"> (GMT+05:00) Tashkent</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Asia/Kolkata' ? 'selected' : '' }}
                                                        value="Asia/Kolkata"> (GMT+05:30) Chennai, Kolkata,
                                                        Mumbai,
                                                        New Delhi</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Asia/Katmandu' ? 'selected' : '' }}
                                                        value="Asia/Katmandu">(GMT+05:45) Kathmandu</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Asia/Dhaka' ? 'selected' : '' }}
                                                        value="Asia/Dhaka">(GMT+06:00) Astana, Dhaka</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Asia/Novosibirsk' ? 'selected' : '' }}
                                                        value="Asia/Novosibirsk">(GMT+06:00) Novosibirsk
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Asia/Rangoon' ? 'selected' : '' }}
                                                        value="Asia/Rangoon">(GMT+06:30) Yangon (Rangoon)
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Asia/Bangkok' ? 'selected' : '' }}
                                                        value="Asia/Bangkok">(GMT+07:00) Bangkok, Hanoi,
                                                        Jakarta
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Asia/Kuala_Lumpur' ? 'selected' : '' }}
                                                        value="Asia/Kuala_Lumpur">(GMT+08:00) Kuala Lumpur
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Asia/Krasnoyarsk' ? 'selected' : '' }}
                                                        value="Asia/Krasnoyarsk">(GMT+07:00) Krasnoyarsk
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Asia/Hong_Kong' ? 'selected' : '' }}
                                                        value="Asia/Hong_Kong">(GMT+08:00) Beijing, Chongqing,
                                                        Hong
                                                        Kong, Urumqi</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Asia/Irkutsk' ? 'selected' : '' }}
                                                        value="Asia/Irkutsk">(GMT+08:00) Irkutsk, Ulaan Bataar
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Australia/Perth' ? 'selected' : '' }}
                                                        value="Australia/Perth">(GMT+08:00) Perth</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Australia/Eucla' ? 'selected' : '' }}
                                                        value="Australia/Eucla">(GMT+08:45) Eucla</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Asia/Tokyo' ? 'selected' : '' }}
                                                        value="Asia/Tokyo">(GMT+09:00) Osaka, Sapporo, Tokyo
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Asia/Seoul' ? 'selected' : '' }}
                                                        value="Asia/Seoul">(GMT+09:00) Seoul</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Asia/Yakutsk' ? 'selected' : '' }}
                                                        value="Asia/Yakutsk">(GMT+09:00) Yakutsk</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Australia/Adelaide' ? 'selected' : '' }}
                                                        value="Australia/Adelaide">(GMT+09:30) Adelaide
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Australia/Darwin' ? 'selected' : '' }}
                                                        value="Australia/Darwin">(GMT+09:30) Darwin</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Australia/Brisbane' ? 'selected' : '' }}
                                                        value="Australia/Brisbane">(GMT+10:00) Brisbane
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Australia/Hobart' ? 'selected' : '' }}
                                                        value="Australia/Hobart">(GMT+10:00) Hobart</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Asia/Vladivostok' ? 'selected' : '' }}
                                                        value="Asia/Vladivostok">(GMT+10:00) Vladivostok
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Australia/Lord_Howe' ? 'selected' : '' }}
                                                        value="Australia/Lord_Howe">(GMT+10:30) Lord Howe
                                                        Island
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Etc/GMT-11' ? 'selected' : '' }}
                                                        value="Etc/GMT-11">(GMT+11:00) Solomon Is., New
                                                        Caledonia
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Asia/Magadan' ? 'selected' : '' }}
                                                        value="Asia/Magadan">(GMT+11:00) Magadan</option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Pacific/Norfolk' ? 'selected' : '' }}
                                                        value="Pacific/Norfolk">(GMT+11:30) Norfolk Island
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Asia/Anadyr' ? 'selected' : '' }}
                                                        value="Asia/Anadyr">(GMT+12:00) Anadyr, Kamchatka
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Pacific/Auckland' ? 'selected' : '' }}
                                                        value="Pacific/Auckland">(GMT+12:00) Auckland,
                                                        Wellington
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Etc/GMT-12' ? 'selected' : '' }}
                                                        value="Etc/GMT-12">(GMT+12:00) Fiji, Kamchatka,
                                                        Marshall
                                                        Is. </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Pacific/Chatham' ? 'selected' : '' }}
                                                        value="Pacific/Chatham">(GMT+12:45) Chatham Islands
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Pacific/Tongatapu' ? 'selected' : '' }}
                                                        value="Pacific/Tongatapu">(GMT+13:00) Nuku'alofa
                                                    </option>
                                                    <option
                                                        {{ @$settingdata->timezone == 'Pacific/Kiritimati' ? 'selected' : '' }}
                                                        value="Pacific/Kiritimati">(GMT+14:00) Kiritimati
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label d-flex align-items-center justify-content-between">
                                                    <span>{{ trans('labels.website_title') }}<span class="text-danger"> * </span></span>
                                                    @include('admin.partials.ai_assist', ['target' => '#oc_website_title', 'field' => 'store name / website title'])
                                                </label>
                                                <input type="text" id="oc_website_title" class="form-control" name="website_title"
                                                    value="{{ @$settingdata->website_title }}"
                                                    placeholder="{{ trans('labels.website_title') }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">{{ trans('labels.copyright') }}<span
                                                        class="text-danger"> * </span></label>
                                                <input type="text" class="form-control" name="copyright"
                                                    value="{{ @$settingdata->copyright }}"
                                                    placeholder="{{ trans('labels.copyright') }}" required>
                                            </div>
                                        </div>
                                        @if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1))
                                            @if (App\Models\SystemAddons::where('unique_identifier', 'unique_slug')->first() != null &&
                                                    App\Models\SystemAddons::where('unique_identifier', 'unique_slug')->first()->activated == 1)
                                                @if (helper::checkcustomdomain($vendor_id) == null)
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label
                                                                class="form-label">{{ trans('labels.personlized_link') }}<span
                                                                    class="text-danger"> * </span></label>
                                                            <div class="input-group mb-2">
                                                                <span
                                                                    class="input-group-text fs-7 overflow-x-scroll 
                                                                {{ session()->get('direction') == 2 ? 'rounded-start-0 rounded-end-5' : 'rounded-start-5 rounded-end-0' }}">{{ URL::to('/') }}</span>
                                                                <input type="text"
                                                                    class="form-control mb-0  {{ session()->get('direction') == 2 ? 'rounded-start-5 rounded-end-0' : 'rounded-start-0 rounded-end-5' }}"
                                                                    id="slug" name="slug"
                                                                    value="{{ $user->slug }}" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">{{ trans('labels.contact_email') }}<span
                                                            class="text-danger"> * </span></label>
                                                    <input type="email" class="form-control" name="email"
                                                        value="{{ @$settingdata->email }}"
                                                        placeholder="{{ trans('labels.contact_email') }}" required>
                                                    @error('email')
                                                        <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">
                                                        <i class="fa-brands fa-whatsapp text-success"></i>
                                                        {{ trans('labels.whatsapp_number_for_orders_2') }}
                                                    </label>
                                                    <input type="text" class="form-control" name="whatsapp_number"
                                                        value="{{ @$settingdata->whatsapp_number }}"
                                                        placeholder="+973 3XXXXXXX">
                                                    <small class="text-muted">{{ trans('labels.customer_orders_are_sent_here_on_whatsapp') }}</small>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="form-label">{{ trans('labels.address') }}<span
                                                            class="text-danger"> * </span></label>
                                                    <textarea class="form-control" name="address" rows="3" placeholder="{{ trans('labels.address') }}" required>{{ @$settingdata->address }}</textarea>
                                                    @error('address')
                                                        <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                                {{-- V2: Store Google Map location --}}
                                                {{-- Company identity for subscription invoices. Entered once here; every
                                                     tax rule and invoice reads it from this one place. --}}
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">{{ trans('labels.company_legal_name') }}</label>
                                                    <input type="text" class="form-control" name="company_legal_name"
                                                        value="{{ @$settingdata->company_legal_name }}"
                                                        placeholder="Click Kick Digital W.L.L.">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">{{ trans('labels.tax_registration_number') }}</label>
                                                    <input type="text" class="form-control" name="tax_registration_number"
                                                        value="{{ @$settingdata->tax_registration_number }}"
                                                        placeholder="{{ trans('labels.tax_registration_number') }}">
                                                    <small class="text-muted">{{ trans('messages.tax_number_hint') }}</small>
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <label class="form-label">
                                                        <i class="fa-solid fa-location-dot text-danger"></i>
                                                        {{ trans('labels.store_location_google_maps') }}
                                                    </label>
                                                    <input type="text" class="form-control" name="map_link" id="oc_map_link"
                                                        value="{{ @$settingdata->map_link }}" placeholder="https://maps.google.com/...">
                                                    <div class="d-flex align-items-center gap-2 mt-2 flex-wrap">
                                                        <button type="button" class="btn btn-sm btn-outline-success" id="oc_gps_btn">
                                                            <i class="fa-solid fa-location-crosshairs"></i>
                                                            {{ trans('labels.detect_my_location') }}
                                                        </button>
                                                        <span class="small text-muted" id="oc_gps_status">
                                                            @if (@$user->latitude && @$user->longitude)
                                                                <a href="https://maps.google.com/?q={{ $user->latitude }},{{ $user->longitude }}" target="_blank" class="text-success fw-600">
                                                                    <i class="fa-solid fa-map-pin"></i> {{ trans('labels.saved') }} ({{ $user->latitude }}, {{ $user->longitude }})
                                                                </a>
                                                            @else
                                                                {{ trans('labels.optional_paste_a_google_maps_link_or') }}
                                                            @endif
                                                        </span>
                                                    </div>
                                                    <input type="hidden" name="latitude" id="oc_lat" value="{{ @$user->latitude }}">
                                                    <input type="hidden" name="longitude" id="oc_lng" value="{{ @$user->longitude }}">
                                                    <script>
                                                        (function () {
                                                            var b = document.getElementById('oc_gps_btn');
                                                            if (!b) return;
                                                            b.addEventListener('click', function () {
                                                                var s = document.getElementById('oc_gps_status');
                                                                if (!navigator.geolocation) { s.textContent = 'GPS not supported'; return; }
                                                                s.textContent = 'Detecting…';
                                                                navigator.geolocation.getCurrentPosition(function (p) {
                                                                    var la = p.coords.latitude.toFixed(6), lo = p.coords.longitude.toFixed(6);
                                                                    document.getElementById('oc_lat').value = la;
                                                                    document.getElementById('oc_lng').value = lo;
                                                                    if (!document.getElementById('oc_map_link').value) {
                                                                        document.getElementById('oc_map_link').value = 'https://maps.google.com/?q=' + la + ',' + lo;
                                                                    }
                                                                    s.innerHTML = '<span class="text-success fw-600"><i class="fa-solid fa-map-pin"></i> Location detected (' + la + ', ' + lo + ') — save to keep</span>';
                                                                }, function (e) {
                                                                    s.textContent = (e.code === 1) ? 'Permission denied' : 'Could not get location';
                                                                }, { enableHighAccuracy: true, timeout: 10000 });
                                                            });
                                                        })();
                                                    </script>
                                                </div>
                                                @php
                                                    $delivery_type = explode(',', $settingdata->delivery_type);
                                                @endphp
                                                <div class="col-md-12 mb-2">
                                                    <label class="form-label">{{ trans('labels.delivery_option') }}<span
                                                            class="text-danger"> * </span></label>
                                                    <div class="form-group">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="delivery_type[]" value="1" id="delivery"
                                                            {{ in_array(1, $delivery_type) ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="delivery">{{ trans('labels.delivery') }}</label>
                                                    </div>
                                                    <div class="form-group">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="delivery_type[]" value="2" id="pickup"
                                                            {{ in_array(2, $delivery_type) ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="pickup">{{ trans('labels.pickup') }}</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="form-label d-flex align-items-center justify-content-between">
                                                            <span>{{ trans('labels.description') }}</span>
                                                            @include('admin.partials.ai_assist', ['target' => '#oc_store_description', 'field' => 'store description'])
                                                        </label>
                                                        <textarea id="oc_store_description" class="form-control" name="description" rows="3" placeholder="{{ trans('labels.description') }}">{{ @$settingdata->description }}</textarea>
                                                        @error('description')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <div class="row justify-content-between align-items-center mb-3">
                                                        <label class="col-auto col-form-label"
                                                            for="">{{ trans('labels.footer_features') }}
                                                            <span class="" data-bs-toggle="tooltip"
                                                                data-bs-placement="top"
                                                                title="Ex. <i class='fa-solid fa-truck-fast'></i> Visit https://fontawesome.com/ for more info">
                                                            </span>
                                                        </label>
                                                        @if (count($getfooterfeatures) > 0)
                                                            <span class="col-auto">
                                                                <button
                                                                    class="btn btn-secondary px-4 rounded-start-5 rounded-end-5"
                                                                    type="button"
                                                                    onclick="add_features('{{ trans('labels.icon') }}','{{ trans('labels.title') }}','{{ trans('labels.description') }}')">
                                                                    <i class="fa-sharp fa-solid fa-plus"></i>
                                                                    {{ trans('labels.add_new') }}
                                                                    {{ trans('labels.footer_features') }}
                                                                </button>
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="row mb-2">
                                                        @forelse ($getfooterfeatures as $key => $features)
                                                            <input type="hidden" name="edit_icon_key[]"
                                                                value="{{ $features->id }}">
                                                            <div class="col-md-4 form-group">
                                                                <div class="input-group">
                                                                    <input type="text"
                                                                        class="form-control mb-0 {{ session()->get('direction') == 2 ? 'rounded-start-0 rounded-end-5' : 'rounded-start-5 rounded-end-0' }}"
                                                                        onkeyup="show_feature_icon(this)"
                                                                        name="edi_feature_icon[{{ $features->id }}]"
                                                                        placeholder="{{ trans('labels.icon') }}"
                                                                        value="{{ $features->icon }}" required>
                                                                    <p
                                                                        class="input-group-text {{ session()->get('direction') == 2 ? 'rounded-start-5 rounded-end-0 border-end-0' : 'rounded-start-0 rounded-end-5' }}">
                                                                        {!! $features->icon !!}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <input type="text" class="form-control"
                                                                    name="edi_feature_title[{{ $features->id }}]"
                                                                    placeholder="{{ trans('labels.title') }}"
                                                                    value="{{ $features->title }}" required>
                                                            </div>
                                                            <div class="col-md-4 d-flex gap-2 form-group">
                                                                <input type="text" class="form-control mb-0"
                                                                    name="edi_feature_description[{{ $features->id }}]"
                                                                    placeholder="{{ trans('labels.description') }}"
                                                                    value="{{ $features->description }}" required>
                                                                <button
                                                                    class="btn btn-danger btn-sm hov rounded-5 pricebtn"
                                                                    type="button"
                                                                    onclick="statusupdate('{{ URL::to('admin/settings/delete-feature-' . $features->id) }}')">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        @empty
                                                            <div class="col-md-4 form-group">
                                                                <div class="input-group">
                                                                    <input type="text"
                                                                        class="form-control mb-0 {{ session()->get('direction') == 2 ? 'input-group-rtl' : '' }}"
                                                                        onkeyup="show_feature_icon(this)"
                                                                        name="feature_icon[]"
                                                                        placeholder="{{ trans('labels.icon') }}">
                                                                    <p
                                                                        class="input-group-text {{ session()->get('direction') == 2 ? 'input-group-icon-rtl' : '' }}">
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <input type="text" class="form-control"
                                                                    name="feature_title[]"
                                                                    placeholder="{{ trans('labels.title') }}">
                                                            </div>
                                                            <div class="col-md-4 form-group d-flex gap-2">
                                                                <input type="text" class="form-control mb-0"
                                                                    name="feature_description[]"
                                                                    placeholder="{{ trans('labels.description') }}">
                                                                <button class="btn btn-dark btn-sm rounded-5 hov pricebtn"
                                                                    type="button"
                                                                    onclick="add_features('{{ trans('labels.icon') }}','{{ trans('labels.title') }}','{{ trans('labels.description') }}')">
                                                                    <i class="fa-sharp fa-solid fa-plus"></i>
                                                                </button>
                                                            </div>
                                                        @endforelse
                                                        <span class="extra_footer_features"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1))
                                            @if (App\Models\SystemAddons::where('unique_identifier', 'notification')->first() != null &&
                                                    App\Models\SystemAddons::where('unique_identifier', 'notification')->first()->activated == 1)
                                                <div class="form-group col-md-6">
                                                    <label
                                                        class="form-label">{{ trans('labels.notification_sound') }}</label>
                                                    <input type="file" class="form-control" name="notification_sound">
                                                    @error('notification_sound')
                                                        <small class="text-danger">{{ $message }}</small><br>
                                                    @enderror
                                                    @if (!empty($settingdata->notification_sound) && $settingdata->notification_sound != null)
                                                        <audio controls class="mt-3">
                                                            <source
                                                                src="{{ url(env('ASSETSPATHURL') . 'admin-assets/notification/' . $settingdata->notification_sound) }}"
                                                                type="audio/mpeg">
                                                        </audio>
                                                    @endif
                                                </div>
                                            @endif
                                        @endif
                                        <div class="form-group m-0 mt-2 d-flex gap-2 justify-content-end">
                                            <button
                                                class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_settings', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}"
                                                @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" name="updatebasicinfo" value="1" @endif>{{ trans('labels.save') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1))
                    <div id="themesettings" class="hidechild">
                        <div class="col-12 mb-7 mt-3">
                            <div class="card border-0 box-shadow">
                                <div
                                    class="card-header rounded-top-4 bg-secondary py-3 d-flex align-items-center text-white">
                                    <i class="fa-solid fa-screwdriver-wrench fs-5"></i>
                                    <h5 class="px-2">{{ trans('labels.theme_settings') }}</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="{{ URL::to('admin/settings/updatetheme') }}"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label">{{ trans('labels.logo') }}
                                                            </label>
                                                            <input type="file" class="form-control" name="logo">
                                                            @error('logo')
                                                                <small class="text-danger">{{ $message }}</small>
                                                                <br>
                                                            @enderror
                                                            <img class="img-fluid rounded-3 h-70px my-2 object-fit-contain"
                                                                src="{{ helper::image_path(@$settingdata->logo) }}"
                                                                alt="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label">{{ trans('labels.darklogo') }}
                                                            </label>
                                                            <input type="file" class="form-control" name="darklogo">
                                                            @error('logo')
                                                                <small class="text-danger">{{ $message }}</small>
                                                                <br>
                                                            @enderror
                                                            <img class="img-fluid rounded-3 h-70px my-2 object-fit-contain"
                                                                src="{{ helper::image_path(@$settingdata->darklogo) }}"
                                                                alt="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label
                                                                class="form-label">{{ trans('labels.favicon') }}</label>
                                                            <input type="file" class="form-control" name="favicon">
                                                            @error('favicon')
                                                                <small class="text-danger">{{ $message }}</small>
                                                                <br>
                                                            @enderror
                                                            <img class="img-fluid rounded-3 h-70px my-2 object-fit-contain"
                                                                src="{{ helper::image_path(@$settingdata->favicon) }}"
                                                                alt="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Super-admin uses the fixed Order Click theme — colours are locked. Vendors keep their pickers. --}}
                                            @if (Auth::user()->type != 1)
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="form-group col-sm-6">
                                                        <label
                                                            class="form-label">{{ trans('labels.primary_color') }}</label>
                                                        <input name="primary_color"
                                                            class="form-control form-control-color w-100 border-0"
                                                            type="color" value="{{ @$settingdata->primary_color }}">
                                                    </div>
                                                    <div class="form-group col-sm-6">
                                                        <label
                                                            class="form-label">{{ trans('labels.secondary_color') }}</label>
                                                        <input name="secondary_color"
                                                            class="form-control form-control-color w-100 border-0"
                                                            type="color" value="{{ @$settingdata->secondary_color }}">
                                                    </div>
                                                </div>
                                            </div>
                                            @else
                                                <input type="hidden" name="primary_color" value="{{ @$settingdata->primary_color ?: '#1f9d55' }}">
                                                <input type="hidden" name="secondary_color" value="{{ @$settingdata->secondary_color ?: '#137a40' }}">
                                            @endif
                                            @php
                                                // Design section: only templates that match the vendor's PURCHASED
                                                // system and selected activity. Classification lives on the theme
                                                // row (system + activity_ids + template), so there is no name map
                                                // to keep in sync any more.
                                                $ocVendor = \App\Models\User::find($vendor_id);
                                                $pickerThemes = \App\Models\Theme::forVendor($ocVendor)
                                                    ->filter(fn($t) => $t->hasPreview())
                                                    ->values();
                                            @endphp
                                            <div class="col-md-12 selectimg">
                                                <div class="form-group">
                                                    <label class="form-label mt-4">{{ trans('labels.theme') }}
                                                        <span class="text-danger"> * </span> </label>
                                                    @if (env('Environment') == 'sendbox')
                                                        <span
                                                            class="badge badge bg-danger ms-2">{{ trans('labels.addon') }}</span>
                                                    @endif
                                                    <div class="row">
                                                        @foreach ($pickerThemes as $theme)
                                                            @php $tpl = $theme->template ?: 2; @endphp
                                                            <div class="col-12 col-md-4 col-lg-4 col-xl-3">
                                                                <label for="template{{ $tpl }}"
                                                                    class="radio-card position-relative">
                                                                    <input type="radio" name="template"
                                                                        id="template{{ $tpl }}"
                                                                        value="{{ $tpl }}"
                                                                        {{ @$settingdata->template == $tpl ? 'checked' : '' }}>
                                                                    <div class="card-content-wrapper border rounded-2">
                                                                        <span class="check-icon position-absolute"></span>
                                                                        <div class="selecimg">
                                                                            <img
                                                                                src="{{ helper::image_path($theme->image) }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="text-center fw-600 color-changer mt-2 mb-1"
                                                                        style="font-size:13.5px;">
                                                                        {{ $theme->name }}
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 selectimg">
                                                <div class="form-group">
                                                    <label class="form-label mt-4">{{ trans('labels.theme_type') }}
                                                        <span class="text-danger"> * </span> </label>
                                                    @if (env('Environment') == 'sendbox')
                                                        <span
                                                            class="badge badge bg-danger ms-2">{{ trans('labels.addon') }}</span>
                                                    @endif
                                                    <div class="row">
                                                        <div class="col-12 col-md-6">
                                                            <label for="template_type_1"
                                                                class="radio-card position-relative">
                                                                <input type="radio" name="template_type"
                                                                    id="template_type_1" value="1"
                                                                    {{ @$settingdata->template_type == 1 ? 'checked' : '' }}>
                                                                <div class="card-content-wrapper border rounded-2">
                                                                    <span class="check-icon position-absolute m-2"></span>
                                                                    <div class="selecimg">
                                                                        <img src="{{ helper::image_path('theme-grid.png') }}"
                                                                            class="w-100 h-100">
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <label for="template_type_2"
                                                                class="radio-card position-relative">
                                                                <input type="radio" name="template_type"
                                                                    id="template_type_2" value="2"
                                                                    {{ @$settingdata->template_type == 2 ? 'checked' : '' }}>
                                                                <div class="card-content-wrapper border rounded-2">
                                                                    <span class="check-icon position-absolute m-2"></span>
                                                                    <div class="selecimg">
                                                                        <img src="{{ helper::image_path('theme-list.png') }}"
                                                                            class="w-100 h-100">
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group m-0 d-flex gap-2 justify-content-end">
                                                <button
                                                    class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_settings', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}"
                                                    @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif>{{ trans('labels.save') }}</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div id="editprofile" class="hidechild">
                    <div class="col-12">
                        <div class="card overflow-hidden border-0 box-shadow">
                            <div class="card-header bg-secondary py-3 d-flex align-items-center text-white">
                                <i class="fa-solid fa-user-pen fs-5"></i>
                                <h5 class="px-2">{{ trans('labels.edit_profile') }}</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST"
                                    action="{{ URL::to('admin/settings/update-profile-' . Auth::user()->id) }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="form-group col-sm-6">
                                            <label class="form-label">{{ trans('labels.name') }}<span
                                                    class="text-danger"> *
                                                </span></label>
                                            <input type="text" class="form-control" name="name"
                                                value="{{ Auth::user()->name }}"
                                                placeholder="{{ trans('labels.name') }}" required>
                                            @error('name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <label class="form-label">{{ trans('labels.email') }}<span
                                                    class="text-danger">
                                                    * </span></label>
                                            <input type="email" class="form-control" name="email"
                                                value="{{ Auth::user()->email }}"
                                                placeholder="{{ trans('labels.email') }}" required>
                                            @error('email')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <label class="form-label" for="mobile">{{ trans('labels.mobile') }}<span
                                                    class="text-danger"> * </span></label>
                                            <input type="number" class="form-control" name="mobile" id="mobile"
                                                value="{{ Auth::user()->mobile }}"
                                                placeholder="{{ trans('labels.mobile') }}" required>
                                            @error('mobile')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <label class="form-label">{{ trans('labels.image') }}</label>
                                            <input type="file" class="form-control" name="profile">
                                            @error('profile')
                                                <span class="text-danger">{{ $message }}</span> <br>
                                            @enderror
                                            <img class="img-fluid rounded-3 hw-70 object-fit-cover my-2"
                                                src="{{ helper::image_Path(Auth::user()->image) }}" alt="">
                                        </div>
                                        @if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1))
                                            <div class="form-group col-md-6">
                                                <label for="city" class="form-label">{{ trans('labels.city') }}<span
                                                        class="text-danger"> * </span></label>
                                                <select name="city" class="form-select" id="city" required>
                                                    <option value="">{{ trans('labels.select') }}</option>
                                                    @foreach ($city as $city)
                                                        <option value="{{ $city->id }}"
                                                            {{ $city->id == Auth::user()->city_id ? 'selected' : '' }}>
                                                            {{ $city->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="area" class="form-label">{{ trans('labels.area') }}<span
                                                        class="text-danger">
                                                        * </span></label>
                                                <select name="area" class="form-select" id="area" required>
                                                    <option value="">{{ trans('labels.select') }}</option>
                                                </select>
                                            </div>
                                        @endif
                                        <div class="form-group m-0 mt-2 d-flex gap-2 justify-content-end">
                                            <button
                                                class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_settings', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}"
                                                @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" name="updateprofile" value="1" @endif>{{ trans('labels.save') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="changepasssword" class="hidechild">
                    <div class="col-12">
                        <div class="card overflow-hidden border-0 box-shadow">
                            <div class="card-header bg-secondary py-3 d-flex align-items-center text-white">
                                <i class="fa-solid fa-unlock fs-5"></i>
                                <h5 class="px-2">{{ trans('labels.change_password') }}</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ URL::to('admin/settings/change-password') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="form-group col-sm-12">
                                            <label class="form-label">{{ trans('labels.current_password') }}<span
                                                    class="text-danger"> * </span></label>
                                            <input type="password" class="form-control" name="current_password"
                                                value="{{ old('current_password') }}"
                                                placeholder="{{ trans('labels.current_password') }}" autocomplete="on"
                                                required>
                                            @error('current_password')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <label class="form-label">{{ trans('labels.new_password') }}<span
                                                    class="text-danger"> * </span></label>
                                            <input type="password" class="form-control" name="new_password"
                                                value="{{ old('new_password') }}"
                                                placeholder="{{ trans('labels.new_password') }}" autocomplete="on"
                                                required>
                                            @error('new_password')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <label class="form-label">{{ trans('labels.confirm_password') }}<span
                                                    class="text-danger"> * </span></label>
                                            <input type="password" class="form-control" name="confirm_password"
                                                value="{{ old('confirm_password') }}"
                                                placeholder="{{ trans('labels.confirm_password') }}" autocomplete="on"
                                                required>
                                            @error('confirm_password')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group m-0 mt-2 d-flex gap-2 justify-content-end">
                                            <button
                                                class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_settings', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}"
                                                @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif>{{ trans('labels.save') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="seo" class="hidechild">
                    <div class="col-12">
                        <div class="card border-0 overflow-hidden box-shadow">
                            <div class="card-header bg-secondary py-3 d-flex align-items-center text-white">
                                <i class="fa-solid fa-chart-line fs-5"></i>
                                <h5 class="px-2">{{ trans('labels.seo') }}</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ URL::to('admin/settings/updateseo') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="form-group">
                                            <label class="form-label">{{ trans('labels.meta_title') }}<span
                                                    class="text-danger"> * </span></label>
                                            <input type="text" class="form-control" name="meta_title"
                                                value="{{ @$settingdata->meta_title }}"
                                                placeholder="{{ trans('labels.meta_title') }}" required>
                                            @error('meta_title')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">{{ trans('labels.meta_description') }}<span
                                                    class="text-danger"> * </span></label>
                                            <textarea class="form-control" name="meta_description" rows="3"
                                                placeholder="{{ trans('labels.meta_description') }}" required>{{ @$settingdata->meta_description }}</textarea>
                                            @error('meta_description')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">{{ trans('labels.og_image') }}<span
                                                    class="text-danger"> * </span></label>
                                            <input type="file" class="form-control" name="og_image">
                                            @error('og_image')
                                                <span class="text-danger">{{ $message }}</span> <br>
                                            @enderror
                                            <img class="img-fluid rounded-3 hw-70 object-fit-cover my-2"
                                                src="{{ helper::image_path(@$settingdata->og_image) }}" alt="">
                                        </div>
                                        <div class="form-group m-0 mt-2 d-flex gap-2 justify-content-end">
                                            <button
                                                class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_settings', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}"
                                                @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif>{{ trans('labels.save') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1))
                    <div id="landing_page" class="hidechild">
                        <div class="col-12">
                            <div class="card border-0 overflow-hidden box-shadow">
                                <div class="card-header bg-secondary py-3 d-flex align-items-center text-white">
                                    <i class="fa-solid fa-clipboard"></i>
                                    <h5 class="px-2">{{ trans('labels.landing_page') }}</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ URL::to('/admin/landingsettings') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="form-group col-sm-6">
                                                <label class="form-label">{{ trans('labels.logo') }}
                                                    <small></small></label>
                                                <input type="file" class="form-control mb-0" name="logo">
                                                @error('logo')
                                                    <small class="text-danger">{{ $message }}</small> <br>
                                                @enderror
                                                <img class="img-fluid rounded-3 h-70px object-fit-cover my-2"
                                                    src="{{ helper::image_path(@$settingdata->logo) }}" alt="">
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label class="form-label">{{ trans('labels.favicon') }} </label>
                                                <input type="file" class="form-control mb-0" name="favicon">
                                                @error('favicon')
                                                    <small class="text-danger">{{ $message }}</small> <br>
                                                @enderror
                                                <img class="img-fluid rounded-3 h-70px object-fit-cover my-2"
                                                    src="{{ helper::image_path(@$settingdata->favicon) }}"
                                                    alt="">
                                            </div>
                                            {{-- Landing uses the fixed Order Click theme — colours are locked. --}}
                                            <input type="hidden" name="landing_primary_color" value="{{ @$settingdata->primary_color ?: '#1f9d55' }}">
                                            <input type="hidden" name="landing_secondary_color" value="{{ @$settingdata->secondary_color ?: '#137a40' }}">
                                            <div class="form-group">
                                                <label class="form-label">{{ trans('labels.website_title') }}<span
                                                        class="text-danger"> * </span></label>
                                                <input type="text" class="form-control" name="landing_website_title"
                                                    value="{{ @$settingdata->landing_website_title }}"
                                                    placeholder="{{ trans('labels.website_title') }}" required>
                                                @error('landing_website_title')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label class="form-label">{{ trans('labels.contact_email') }}<span
                                                        class="text-danger"> * </span></label>
                                                <input type="email" class="form-control" name="landing_email"
                                                    value="{{ @$settingdata->email }}"
                                                    placeholder="{{ trans('labels.contact_email') }}" required>
                                                @error('landing_email')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label class="form-label">{{ trans('labels.contact_mobile') }}<span
                                                        class="text-danger"> * </span></label>
                                                <input type="text" class="form-control" name="landing_mobile"
                                                    value="{{ @$settingdata->contact }}"
                                                    placeholder="{{ trans('labels.contact_mobile') }}" required>
                                                @error('contact_mobile')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">{{ trans('labels.address') }}<span
                                                        class="text-danger"> * </span></label>
                                                <textarea class="form-control" name="landing_address" rows="3" placeholder="{{ trans('labels.address') }}"
                                                    required>{{ @$settingdata->address }}</textarea>
                                                @error('address')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="m-0 mt-2 d-flex gap-2 justify-content-end">
                                            <button
                                                @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif
                                                class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_settings', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}">{{ trans('labels.save') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if (App\Models\SystemAddons::where('unique_identifier', 'whatsapp_message')->first() != null &&
                            App\Models\SystemAddons::where('unique_identifier', 'whatsapp_message')->first()->activated == 1)
                        @include('admin.included.whatsapp_message.admin_setting_form')
                    @endif
                @endif


                @if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1))
                    @if (App\Models\SystemAddons::where('unique_identifier', 'google_login')->first() != null &&
                            App\Models\SystemAddons::where('unique_identifier', 'google_login')->first()->activated == 1)
                        @include('admin.sociallogin.google_login')
                    @endif
                    @if (App\Models\SystemAddons::where('unique_identifier', 'facebook_login')->first() != null &&
                            App\Models\SystemAddons::where('unique_identifier', 'facebook_login')->first()->activated == 1)
                        @include('admin.sociallogin.facebook_login')
                    @endif

                    @if (App\Models\SystemAddons::where('unique_identifier', 'trusted_badges')->first() != null &&
                            App\Models\SystemAddons::where('unique_identifier', 'trusted_badges')->first()->activated == 1)
                        <div id="trusted_badges" class="hidechild">
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="card border-0 box-shadow">
                                        <div
                                            class="card-header rounded-top-4 py-3 bg-secondary d-flex align-items-center">
                                            <i class="fa-sharp fa-solid fa-badge-check fs-5"></i>
                                            <h5 class="px-2">
                                                {{ trans('labels.trusted_badges') }}
                                            </h5>
                                        </div>
                                        <form action="{{ URL::to('admin/settings/safe-secure-store') }}"
                                            method="post" enctype="multipart/form-data">
                                            @csrf
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div
                                                            class="row row-cols-xxl-4 row-cols-xl-2 row-cols-lg-2 row-cols-md-2 row-cols-1">
                                                            <div class="form-group col">
                                                                <label
                                                                    class="form-label">{{ trans('labels.trusted_badge_image_1') }}
                                                                </label>
                                                                <input type="file" class="form-control"
                                                                    name="trusted_badge_image_1">
                                                                <img class="img-fluid rounded h-40 mt-1"
                                                                    src="{{ @helper::image_Path($othersettingdata->trusted_badge_image_1) }}"
                                                                    alt="">
                                                            </div>
                                                            <div class="form-group col">
                                                                <label
                                                                    class="form-label">{{ trans('labels.trusted_badge_image_2') }}
                                                                </label>
                                                                <input type="file" class="form-control"
                                                                    name="trusted_badge_image_2">
                                                                <img class="img-fluid rounded h-40 mt-1"
                                                                    src="{{ @helper::image_Path($othersettingdata->trusted_badge_image_2) }}"
                                                                    alt="">
                                                            </div>
                                                            <div class="form-group col">
                                                                <label
                                                                    class="form-label">{{ trans('labels.trusted_badge_image_3') }}
                                                                </label>
                                                                <input type="file" class="form-control"
                                                                    name="trusted_badge_image_3">
                                                                <img class="img-fluid rounded h-40 mt-1"
                                                                    src="{{ @helper::image_Path($othersettingdata->trusted_badge_image_3) }}"
                                                                    alt="">
                                                            </div>
                                                            <div class="form-group col">
                                                                <label
                                                                    class="form-label">{{ trans('labels.trusted_badge_image_4') }}
                                                                </label>
                                                                <input type="file" class="form-control"
                                                                    name="trusted_badge_image_4">
                                                                <img class="img-fluid rounded h-40 mt-1"
                                                                    src="{{ @helper::image_Path($othersettingdata->trusted_badge_image_4) }}"
                                                                    alt="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group m-0 mt-2 d-flex gap-2 justify-content-end">
                                                    <button
                                                        @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" name="trusted_badges" value="1" @endif
                                                        class="btn btn-primary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_settings', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}">{{ trans('labels.save') }}</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if (App\Models\SystemAddons::where('unique_identifier', 'safe_secure_checkout')->first() != null &&
                            App\Models\SystemAddons::where('unique_identifier', 'safe_secure_checkout')->first()->activated == 1)
                        <div id="safe_secure" class="hidechild">
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="card border-0 box-shadow">
                                        <div
                                            class="card-header rounded-top-4 py-3 bg-secondary d-flex align-items-center">
                                            <i class="fa-solid fa-shield-check fs-5"></i>
                                            <h5 class="px-2">
                                                {{ trans('labels.safe_secure') }}
                                            </h5>
                                        </div>
                                        <form action="{{ URL::to('admin/settings/safe-secure-store') }}"
                                            method="post" enctype="multipart/form-data">
                                            @csrf
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="form-group col-sm-12">
                                                        <label
                                                            class="form-label">{{ trans('labels.safe_secure_checkout_payment_selection') }}
                                                        </label>
                                                        <div class="row">
                                                            @foreach ($getpayment as $payment)
                                                                @php
                                                                    // Check if the current $payment is a system addon and activated
                                                                    if (
                                                                        $payment->payment_type == '1' ||
                                                                        $payment->payment_type == '16'
                                                                    ) {
                                                                        $systemAddonActivated = true;
                                                                    } else {
                                                                        $systemAddonActivated = false;
                                                                    }
                                                                    $addon = App\Models\SystemAddons::where(
                                                                        'unique_identifier',
                                                                        $payment->unique_identifier,
                                                                    )->first();
                                                                    if ($addon != null && $addon->activated == 1) {
                                                                        $systemAddonActivated = true;
                                                                    }
                                                                @endphp
                                                                @if ($systemAddonActivated)
                                                                    <div class="form-group col-auto">
                                                                        <div class="form-check">
                                                                            <input
                                                                                class="form-check-input payment-checkbox"
                                                                                type="checkbox"
                                                                                name="safe_secure_checkout_payment_selection[]"
                                                                                {{ @in_array($payment->payment_type, explode(',', $othersettingdata->safe_secure_checkout_payment_selection)) ? 'checked' : '' }}
                                                                                id="{{ $payment->payment_type }}"
                                                                                value="{{ $payment->payment_type }}">
                                                                            <label class="form-check-label fw-bolder"
                                                                                for="{{ $payment->payment_type }}">
                                                                                {{ $payment->payment_name }}
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-sm-6">
                                                        <label
                                                            class="form-label">{{ trans('labels.safe_secure_checkout_text') }}
                                                        </label>
                                                        <input type="text" class="form-control"
                                                            name="safe_secure_checkout_text"
                                                            value="{{ @$othersettingdata->safe_secure_checkout_text }}">
                                                    </div>
                                                    <div class="form-group col-sm-6">
                                                        <label
                                                            class="form-label">{{ trans('labels.safe_secure_checkout_text_color') }}
                                                        </label>
                                                        <input type="color"
                                                            class="form-control form-control-color w-100 border-0"
                                                            name="safe_secure_checkout_text_color"
                                                            value="{{ @$othersettingdata->safe_secure_checkout_text_color }}">
                                                    </div>
                                                </div>
                                                <div class="form-group m-0 mt-2 d-flex gap-2 justify-content-end">
                                                    <button
                                                        @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" name="safe_secure" value="1" @endif
                                                        class="btn btn-primary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_settings', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}">{{ trans('labels.save') }}</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if (App\Models\SystemAddons::where('unique_identifier', 'vendor_tip')->first() != null &&
                            App\Models\SystemAddons::where('unique_identifier', 'vendor_tip')->first()->activated == 1)
                        @include('admin.tips_settings.tips_settings')
                    @endif
                @endif
                @if (App\Models\SystemAddons::where('unique_identifier', 'email_settings')->first() != null &&
                        App\Models\SystemAddons::where('unique_identifier', 'email_settings')->first()->activated == 1)
                    @include('admin.emailsettings.email_setting')
                    @include('admin.email_template.setting_form')
                @endif
                @if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1))
                    @if (App\Models\SystemAddons::where('unique_identifier', 'subscription')->first() != null &&
                            App\Models\SystemAddons::where('unique_identifier', 'subscription')->first()->activated == 1)
                        @if (App\Models\SystemAddons::where('unique_identifier', 'pwa')->first() != null &&
                                App\Models\SystemAddons::where('unique_identifier', 'pwa')->first()->activated == 1)
                            @php
                                $checkplan = App\Models\Transaction::where('vendor_id', $vendor_id)
                                    ->orderByDesc('id')
                                    ->first();

                                if ($user->allow_without_subscription == 1) {
                                    $pwa = 1;
                                } else {
                                    $pwa = @$checkplan->pwa;
                                }
                            @endphp
                            @if ($pwa == 1)
                                @include('admin.pwa.pwa_settings')
                            @endif
                        @endif
                    @else
                        @if (App\Models\SystemAddons::where('unique_identifier', 'pwa')->first() != null &&
                                App\Models\SystemAddons::where('unique_identifier', 'pwa')->first()->activated == 1)
                            @include('admin.pwa.pwa_settings')
                        @endif
                    @endif
                    @if (App\Models\SystemAddons::where('unique_identifier', 'pixel')->first() != null &&
                            App\Models\SystemAddons::where('unique_identifier', 'pixel')->first()->activated == 1)
                        @include('admin.pixel.pixel_setting')
                    @endif
                    @if (App\Models\SystemAddons::where('unique_identifier', 'subscription')->first() != null &&
                            App\Models\SystemAddons::where('unique_identifier', 'subscription')->first()->activated == 1)
                        @if (App\Models\SystemAddons::where('unique_identifier', 'loyalty_program')->first() != null &&
                                App\Models\SystemAddons::where('unique_identifier', 'loyalty_program')->first()->activated == 1)
                            @include('admin.loyalty_program.loyalty_program_settings')
                        @endif
                    @else
                        @if (App\Models\SystemAddons::where('unique_identifier', 'loyalty_program')->first() != null &&
                                App\Models\SystemAddons::where('unique_identifier', 'loyalty_program')->first()->activated == 1)
                            @include('admin.loyalty_program.loyalty_program_settings')
                        @endif
                    @endif
                @endif
                @if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1))
                    @if (App\Models\SystemAddons::where('unique_identifier', 'custom_domain')->first() != null &&
                            App\Models\SystemAddons::where('unique_identifier', 'custom_domain')->first()->activated == 1)
                        @include('admin.customdomain.setting_form')
                    @endif
                    @if (App\Models\SystemAddons::where('unique_identifier', 'google_recaptcha')->first() != null &&
                            App\Models\SystemAddons::where('unique_identifier', 'google_recaptcha')->first()->activated == 1)
                        @include('admin.google_recaptcha.setting_form')
                    @endif
                    <div id="fun_fact" class="hidechild">
                        <div class="col-12">
                            <div class="card border-0 box-shadow">
                                <div
                                    class="card-header rounded-top-4 bg-secondary py-3 d-flex justify-content-between align-items-center text-white">
                                    <div class="d-flex align-items-center">
                                        <i class="fa-solid fa-rocket fs-5"></i>
                                        <h5 class="px-2">{{ trans('landing.fun_fact') }}</h5>
                                    </div>
                                    @if (count($getfooterfeatures) > 0)
                                        <button class="btn btn-dark btn-sm rounded-circle hov" type="button"
                                            tooltip="{{ trans('labels.add') }}"
                                            onclick="add_fun_fact('{{ trans('labels.title') }}','{{ trans('labels.description') }}')">
                                            <i class="fa-sharp fa-solid fa-plus"></i>
                                        </button>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <form action="{{ URL::to('/admin/fun_fact/update') }}" method="POST">
                                        @csrf
                                        <div class="row mb-2">
                                            @forelse ($getfooterfeatures as $key => $features)
                                                <input type="hidden" name="edit_icon_key[]"
                                                    value="{{ $features->id }}">
                                                <div class="col-md-6 form-group">
                                                    <input type="number" class="form-control"
                                                        name="edit_fun_fact_title[{{ $features->id }}]"
                                                        placeholder="{{ trans('labels.title') }}"
                                                        value="{{ $features->title }}" required>
                                                </div>
                                                <div class="col-md-6 d-flex gap-2 form-group">
                                                    <input type="text" class="form-control mb-0"
                                                        name="edit_fun_fact_description[{{ $features->id }}]"
                                                        placeholder="{{ trans('labels.description') }}"
                                                        value="{{ $features->description }}" required>
                                                    <button class="btn btn-danger btn-sm hov rounded-5 pricebtn"
                                                        type="button"
                                                        onclick="statusupdate('{{ URL::to('admin/settings/delete-feature-' . $features->id) }}')">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            @empty
                                                <div class="col-md-6 form-group">
                                                    <input type="text" class="form-control" name="fun_fact_title[]"
                                                        placeholder="{{ trans('labels.title') }}" required>
                                                </div>
                                                <div class="col-md-6 form-group d-flex gap-2">
                                                    <input type="number" class="form-control mb-0"
                                                        name="fun_fact_description[]"
                                                        placeholder="{{ trans('labels.description') }}" required>
                                                    <button class="btn btn-dark btn-sm rounded-5 hov pricebtn"
                                                        type="button"
                                                        onclick="add_fun_fact('{{ trans('labels.title') }}','{{ trans('labels.description') }}')">
                                                        <i class="fa-sharp fa-solid fa-plus"></i>
                                                    </button>
                                                </div>
                                            @endforelse
                                            <span class="extra_fun_fact"></span>
                                        </div>
                                        <div class="m-0 mt-2 d-flex gap-2 justify-content-end">
                                            <button
                                                @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif
                                                class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_settings', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}">{{ trans('labels.save') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @if (App\Models\SystemAddons::where('unique_identifier', 'tawk')->first() != null &&
                        App\Models\SystemAddons::where('unique_identifier', 'tawk')->first()->activated == 1)
                    @include('admin.tawk.setting_form')
                @endif
                @if (App\Models\SystemAddons::where('unique_identifier', 'wizz_chat')->first() != null &&
                        App\Models\SystemAddons::where('unique_identifier', 'wizz_chat')->first()->activated == 1)
                    @include('admin.wizz_chat_settings.index')
                @endif
                @if (App\Models\SystemAddons::where('unique_identifier', 'quick_call')->first() != null &&
                        App\Models\SystemAddons::where('unique_identifier', 'quick_call')->first()->activated == 1)
                    @include('admin.quick_call.index')
                @endif
                @if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1))
                    @if (App\Models\SystemAddons::where('unique_identifier', 'sales_notification')->first() != null &&
                            App\Models\SystemAddons::where('unique_identifier', 'sales_notification')->first()->activated == 1)
                        @include('admin.fake_sales_notification.index')
                    @endif
                    @if (App\Models\SystemAddons::where('unique_identifier', 'fake_view')->first() != null &&
                            App\Models\SystemAddons::where('unique_identifier', 'fake_view')->first()->activated == 1)
                        @include('admin.product_fake_view.index')
                    @endif
                    @if (App\Models\SystemAddons::where('unique_identifier', 'age_verification')->first() != null &&
                            App\Models\SystemAddons::where('unique_identifier', 'age_verification')->first()->activated == 1)
                        @include('admin.age_verification.index')
                    @endif
                    @if (App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first() != null &&
                            App\Models\SystemAddons::where('unique_identifier', 'product_reviews')->first()->activated == 1)
                        @include('admin.review.review_setting')
                    @endif
                    @if (App\Models\SystemAddons::where('unique_identifier', 'user_app')->first() != null &&
                            App\Models\SystemAddons::where('unique_identifier', 'user_app')->first()->activated == 1)
                        @include('admin.mobile_app.app_section')
                    @endif
                    @if (App\Models\SystemAddons::where('unique_identifier', 'cart_checkout_countdown')->first() != null &&
                            App\Models\SystemAddons::where('unique_identifier', 'cart_checkout_countdown')->first()->activated == 1)
                        @include('admin.cart_checkout_countdown.index')
                    @endif
                    @if (App\Models\SystemAddons::where('unique_identifier', 'cart_checkout_progressbar')->first() != null &&
                            App\Models\SystemAddons::where('unique_identifier', 'cart_checkout_progressbar')->first()->activated == 1)
                        @include('admin.cart_checkout_progressbar.index')
                    @endif

                    <div id="estimated_delivery" class="hidechild">
                        <div class="col-12">
                            <div class="card border-0 box-shadow">
                                <form action="{{ URL::to('admin/settings/estimated_delivery') }}" method="POST">
                                    
                                    @csrf
                                    <div class="card-header p-3 bg-secondary">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h5 class="text-capitalize px-2">
                                                {{ trans('labels.estimated_delivery') }}
                                            </h5>
                                            <div class="text-center">
                                                <input id="estimated_delivery_on_off" type="checkbox"
                                                    class="checkbox-switch" name="estimated_delivery_on_off"
                                                    value="1"
                                                    {{ @$othersettingdata->estimated_delivery_on_off == 1 ? 'checked' : '' }}>
                                                <label for="estimated_delivery_on_off" class="switch">
                                                    <span
                                                        class="{{ session()->get('direction') == 2 ? 'switch__circle-rtl' : 'switch__circle' }}">
                                                        <span class="switch__circle-inner"></span>
                                                    </span>
                                                    <span
                                                        class="switch__left {{ session()->get('direction') == 2 ? 'pe-2' : 'ps-2' }}">
                                                        {{ trans('labels.off') }}
                                                    </span>
                                                    <span
                                                        class="switch__right {{ session()->get('direction') == 2 ? 'ps-2' : 'pe-2' }}">
                                                        {{ trans('labels.on') }}
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="form-group col-sm-12">
                                                <label for="days_of_estimated_delivery" class="form-label">
                                                    {{ trans('labels.number_of_day') }}
                                                    <span class="text-danger"> * </span>
                                                </label>
                                                <input type="number" class="form-control"
                                                    id="days_of_estimated_delivery" name="days_of_estimated_delivery"
                                                    value="{{ @$othersettingdata->days_of_estimated_delivery }}"
                                                    required placeholder="{{ trans('labels.number_of_day') }}">
                                            </div>
                                        </div>
                                        <div class="text-{{ session()->get('direction') == '2' ? 'start' : 'end' }}">
                                            <button
                                                @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" name="estimated_delivery" value="1" @endif
                                                class="btn btn-primary px-sm-4 {{ Auth::user()->type == 4 ? (helper::check_access('role_setting', Auth::user()->role_id, Auth::user()->vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}">
                                                {{ trans('labels.save') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
                <div id="social_links" class="hidechild">
                    <div class="col-12">
                        <div class="card border-0 box-shadow">
                            <div
                                class="card-header rounded-top-4 bg-secondary py-3 d-flex align-items-center justify-content-between text-white">
                                <div class="d-flex align-items-center">
                                    <i class="fa-solid fa-link fs-5"></i>
                                    <h5 class="text-capitalize px-2">{{ trans('labels.social_links') }} <span
                                            class="" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Ex. <i class='fa-solid fa-truck-fast'></i> Visit https://fontawesome.com/ for more info">
                                        </span>
                                    </h5>
                                </div>
                                <button class="btn btn-dark btn-sm rounded-circle hov" type="button"
                                    tooltip="{{ trans('labels.add') }}"
                                    onclick="add_social_links('{{ trans('labels.icon') }}','{{ trans('labels.link') }}')">
                                    <i class="fa-sharp fa-solid fa-plus"></i> </button>
                            </div>
                            <div class="card-body">
                                <form action="{{ URL::to('admin/social_links/update') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">

                                        @foreach ($getsociallinks as $key => $links)
                                            <div class="col-12">
                                                <div class="row">
                                                    <input type="hidden" name="edit_icon_key[]"
                                                        value="{{ $links->id }}">
                                                    <div class="col-md-6 form-group">
                                                        <div class="input-group">
                                                            <input type="text"
                                                                class="form-control soaciallink_required  {{ session()->get('direction') == 2 ? 'input-group-rtl' : '' }}"
                                                                onkeyup="show_feature_icon(this)"
                                                                name="edi_sociallink_icon[{{ $links->id }}]"
                                                                placeholder="{{ trans('labels.icon') }}"
                                                                value="{{ $links->icon }}" required>
                                                            <p
                                                                class="input-group-text {{ session()->get('direction') == 2 ? 'input-group-icon-rtl' : '' }}">
                                                                {!! $links->icon !!}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 d-flex gap-2 align-items-center form-group">
                                                        <input type="text" class="form-control"
                                                            name="edi_sociallink_link[{{ $links->id }}]"
                                                            placeholder="{{ trans('labels.link') }}"
                                                            value="{{ $links->link }}" required>
                                                        <button class="btn btn-danger hov btn-sm rounded-5"
                                                            type="button" tooltip="{{ trans('labels.delete') }}"
                                                            onclick="statusupdate('{{ URL::to('admin/settings/delete-sociallinks-' . $links->id) }}')">
                                                            <i class="fa fa-trash"></i> </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                        <span class="extra_social_links"></span>
                                        <div class="form-group m-0 mt-2 d-flex gap-2 justify-content-end">
                                            <button
                                                class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_settings', Auth::user()->role_id, Auth::user()->vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}"
                                                @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif>{{ trans('labels.save') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="maintenance_mode" class="hidechild">
                    <div class="col-12">
                        <div class="card overflow-hidden border-0 box-shadow">
                            <div class="card-header bg-secondary py-3 d-flex align-items-center text-white">
                                <i class="fa-solid fa-gears fs-5"></i>
                                <h5 class="px-2">{{ trans('labels.maintenance_mode') }}</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ URL::to('admin/settings/maintenance_update') }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="form-group col-sm-3">
                                            <label class="form-label"
                                                for="">{{ trans('labels.maintenance_mode') }}
                                            </label>
                                            <input id="maintenance_on_off" type="checkbox" class="checkbox-switch"
                                                name="maintenance_on_off" value="1"
                                                {{ @$othersettingdata->maintenance_on_off == 1 ? 'checked' : '' }}>
                                            <label for="maintenance_on_off" class="switch">
                                                <span
                                                    class="{{ session()->get('direction') == 2 ? 'switch__circle-rtl' : 'switch__circle' }}">
                                                    <span class="switch__circle-inner"></span>
                                                </span>
                                                <span
                                                    class="switch__left {{ session()->get('direction') == 2 ? 'pe-2' : 'ps-2' }}">
                                                    {{ trans('labels.off') }}
                                                </span>
                                                <span
                                                    class="switch__right {{ session()->get('direction') == 2 ? 'ps-2' : 'pe-2' }}">
                                                    {{ trans('labels.on') }}
                                                </span>
                                            </label>
                                        </div>
                                        <div class="form-group col-sm-12">
                                            <label for="maintenance_title" class="form-label">
                                                {{ trans('labels.title') }}
                                                <span class="text-danger"> * </span>
                                            </label>
                                            <input type="text" class="form-control" id="maintenance_title"
                                                name="maintenance_title" placeholder=" {{ trans('labels.title') }}"
                                                required="" value="{{ @$othersettingdata->maintenance_title }}">
                                        </div>
                                        <div class="form-group col-sm-12">
                                            <label for="maintenance_description" class="form-label">
                                                {{ trans('labels.description') }}
                                                <span class="text-danger"> * </span>
                                            </label>
                                            <textarea name="maintenance_description" class="form-control" rows="4"
                                                placeholder=" {{ trans('labels.description') }}" required="">{{ @$othersettingdata->maintenance_description }}</textarea>
                                        </div>

                                        <div class="form-group col-sm-6">
                                            <label class="form-label">
                                                {{ trans('labels.image') }}<span class="text-danger">
                                                    *
                                                </span>
                                            </label>
                                            <input type="file" class="form-control" name="maintenance_image">

                                            <img class="img-fluid rounded hw-70 mt-1"
                                                src="{{ @helper::image_Path($othersettingdata->maintenance_image) }}"
                                                alt="">
                                        </div>
                                    </div>
                                    <div class="form-group m-0 mt-2 d-flex gap-2 justify-content-end">
                                        <button
                                            class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_settings', Auth::user()->role_id, Auth::user()->vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}"
                                            @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif>{{ trans('labels.save') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="other" class="hidechild">
                    <div class="col-12">
                        <div class="card overflow-hidden border-0 box-shadow">
                            <div class="card-header bg-secondary py-3 d-flex align-items-center text-white">
                                <i class="fa-solid fa-gears fs-5"></i>
                                <h5 class="px-2">{{ trans('labels.other') }}</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ URL::to('admin/settings/otherdata/update') }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        @if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1))
                                            <div class="form-group col-sm-6">
                                                <label class="form-label">{{ trans('labels.google_review') }}</label>
                                                <input type="text" class="form-control"
                                                    placeholder="{{ trans('labels.google_review') }}"
                                                    name="google_review" value="{{ $settingdata->google_review }}">
                                            </div>
                                        @endif
                                        @if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1))
                                            <div class="form-group col-sm-6">
                                                <label
                                                    class="form-label">{{ trans('labels.landing_home_banner') }}</label>
                                                <input type="file" class="form-control" name="landing_home_banner">
                                                <img class="img-fluid rounded-3 hw-70 object-fit-cover my-2"
                                                    src="{{ helper::image_Path($settingdata->landing_home_banner) }}"
                                                    alt="">
                                                <small class="text-muted d-block">{{ trans('labels.recommended') }}: 1200 × 900 px (4:3) — {{ trans('labels.shown_in_the_landing_hero_desktop') }}</small>
                                            </div>

                                            <div class="form-group col-sm-6">
                                                <label
                                                    class="form-label">{{ trans('labels.store_unavailable_image') }}</label>
                                                <input type="file" class="form-control"
                                                    name="store_unavailable_image">
                                                <img class="img-fluid rounded hw-70 mt-2 object-fit-cover"
                                                    src="{{ helper::image_path(@$settingdata->store_unavailable_image) }}"
                                                    alt="">
                                                <small class="text-muted d-block">{{ trans('labels.recommended') }}: 600 × 500 px — {{ trans('labels.transparent_png_illustration_shown_on_the_store') }}</small>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label
                                                    class="form-label">{{ trans('labels.subscribe_newsletter_image') }}</label>
                                                <input type="file" class="form-control"
                                                    name="subscribe_newsletter_image">
                                                <img class="img-fluid rounded hw-70 mt-2 object-fit-cover"
                                                    src="{{ helper::image_path(@$settingdata->subscribe_newsletter_image) }}"
                                                    alt="">
                                                <small class="text-muted d-block">{{ trans('labels.recommended') }}: 1600 × 500 px — {{ trans('labels.wide_background_for_the_newsletter_section_a') }}</small>
                                            </div>
                                        @endif
                                        {{-- Auth Page Image removed: the new login/register are clean card pages with no background image. --}}
                                        @if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1))
                                            <div class="form-group col-sm-6">
                                                <label class="form-label">{{ trans('labels.banner') }}</label>
                                                <input type="file" class="form-control" name="banner">
                                                <img class="img-fluid rounded-3 hw-70 my-2 object-fit-cover"
                                                    src="{{ helper::image_path(@$settingdata->banner) }}"
                                                    alt="">
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label class="form-label">{{ trans('labels.landing_page_cover_image') }}
                                                </label>
                                                <input type="file" class="form-control"
                                                    name="landin_page_cover_image">
                                                <img class="img-fluid rounded-3 hw-70 my-2 object-fit-cover"
                                                    src="{{ helper::image_path($settingdata->cover_image) }}"
                                                    alt="">
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label class="form-label">{{ trans('labels.subscribe_background') }}
                                                </label>
                                                <input type="file" class="form-control"
                                                    name="subscribe_background">
                                                <img class="img-fluid rounded-3 hw-70 my-2"
                                                    src="{{ helper::image_path($settingdata->subscribe_background) }}"
                                                    alt="">
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label class="form-label">{{ trans('labels.faq_image') }}</label>
                                                <input type="file" class="form-control" name="faq_image">
                                                <img class="img-fluid rounded-3 hw-70 object-fit-cover my-2"
                                                    src="{{ helper::image_Path($settingdata->faq_image) }}"
                                                    alt="">
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label class="form-label">{{ trans('labels.no_data_image') }}</label>
                                                <input type="file" class="form-control" name="no_data_image">
                                                <img class="img-fluid rounded-3 hw-70 object-fit-cover my-2"
                                                    src="{{ helper::image_Path($settingdata->no_data_image) }}"
                                                    alt="">
                                            </div>
                                        @endif
                                        <div class="form-group m-0 mt-2 d-flex gap-2 justify-content-end">
                                            <button
                                                class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_settings', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}"
                                                @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" name="updateprofile" value="1" @endif>{{ trans('labels.save') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        var layout = "{{ session()->get('direction') }}";
        var areaurl = "{{ URL::to('admin/getarea') }}";
        var select = "{{ trans('labels.select') }}";
        var areaid = "{{ Auth::user()->area_id != null ? Auth::user()->area_id : '0' }}";
        $(document).ready(function() {
            $('#recaptcha_version').on('change', function() {
                var recaptcha_version = $(this).val();
                if (recaptcha_version == 'v3') {
                    $("#score_threshold").show();
                } else {
                    $("#score_threshold").hide();
                }
            });
        });
        $('#review_approved_status-switch').on('change', function() {
            if ($(this).is(':checked')) {
                $(this).val(1);
                document.querySelector('#checkbox5').disabled = false;
                document.querySelector('#checkbox4').disabled = false;
                document.querySelector('#checkbox3').disabled = false;
                document.querySelector('#checkbox2').disabled = false;
                document.querySelector('#checkbox1').disabled = false;
                document.querySelector('#review_setting_update_btn').disabled = false;
            } else {
                $(this).val(2);
                document.querySelector('#checkbox5').disabled = true;
                document.querySelector('#checkbox4').disabled = true;
                document.querySelector('#checkbox3').disabled = true;
                document.querySelector('#checkbox2').disabled = true;
                document.querySelector('#checkbox1').disabled = true;
            }
        });
    </script>
    <script src="{{ url('storage/app/public/admin-assets/js/user.js') }}"></script>
    @if ($vendor_id == 1)
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.12.1/ckeditor.js"></script>
        <script>
            CKEDITOR.replace('cname_text');
        </script>
    @endif
    <script src="{{ url(env('ASSETSPATHURL') . 'admin-assets/js/settings.js') }}"></script>
@endsection
