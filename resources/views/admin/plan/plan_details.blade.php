@extends('admin.layout.default')

@php

    if (Auth::user()->type == 4) {
        $vendor_id = Auth::user()->vendor_id;
    } else {
        $vendor_id = Auth::user()->id;
    }

@endphp

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="col-12">
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.plan_details') }}</h5>
            @include('admin.layout.breadcrumb')
        </div>
    </div>
    {{-- Transaction details: vendor & system, payment, subscription timeline and the plan
         snapshot frozen at purchase. Added above the existing plan cards; nothing below changed. --}}
    @php
        $ocPay    = \App\Helpers\Subscriptions::paymentStatus($plan);
        $ocTerm   = \App\Helpers\Subscriptions::term($plan);
        $ocSnap   = $plan->planDetails();
        $ocReceipt = \App\Helpers\Subscriptions::receiptUrl($plan);
        $ocCur    = $plan->currency ?: ($ocSnap['currency'] ?? 'USD');
        $ocAddons = (array) ($plan->addons ?? []);
        $ocOffer  = $plan->offer_code ?: ($ocSnap['offer_code'] ?? null);
    @endphp
    {{-- Subscription invoice: company header from General Settings, then the four totals. --}}
    @php $ocInv = \App\Helpers\Subscriptions::invoice($plan); $ocCo = $ocInv['company']; @endphp
    <div class="col-12 mt-3">
        <div class="card border-0 box-shadow">
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-12 col-lg-6">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            @if (!empty($ocCo['logo']))
                                <img src="{{ helper::image_path($ocCo['logo']) }}" alt="" style="height:44px;width:auto;">
                            @endif
                            <div>
                                <div class="fw-600 fs-5 color-changer">{{ $ocCo['name'] }}</div>
                                @if ($ocCo['legal'])
                                    <div class="fs-7 text-muted">{{ $ocCo['legal'] }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="fs-7 text-muted">
                            @if ($ocCo['address'])<div>{{ $ocCo['address'] }}</div>@endif
                            @if ($ocCo['email'])<div>{{ $ocCo['email'] }}</div>@endif
                            @if ($ocCo['phone'])<div>{{ $ocCo['phone'] }}</div>@endif
                            @if ($ocCo['tax_no'])
                                <div class="mt-1">{{ trans('labels.tax_registration_number') }}: <span class="fw-500">{{ $ocCo['tax_no'] }}</span></div>
                            @endif
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">{{ trans('labels.subtotal') }}</span>
                            <span class="fw-500">{{ number_format($ocInv['subtotal'], 2) }} {{ $ocInv['currency'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">{{ trans('labels.discount') }}</span>
                            <span class="fw-500">{{ $ocInv['discount'] > 0 ? '-' : '' }}{{ number_format($ocInv['discount'], 2) }}</span>
                        </div>
                        @forelse ($ocInv['tax_lines'] as $ocLine)
                            <div class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">{{ $ocLine['name'] }}</span>
                                <span class="fw-500">{{ number_format($ocLine['amount'], 2) }}</span>
                            </div>
                        @empty
                            <div class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">{{ trans('labels.tax') }}</span>
                                <span class="fw-500">0.00</span>
                            </div>
                        @endforelse
                        <div class="d-flex justify-content-between py-2 fw-600 fs-5">
                            <span>{{ trans('labels.grand_total') }}</span>
                            <span>{{ number_format($ocInv['grand_total'], 2) }} {{ $ocInv['currency'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 mt-3">
        <div class="card border-0 box-shadow">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <h6 class="mb-0">{{ trans('labels.transaction_details') }}</h6>
                    <span class="badge bg-success">{{ \App\Helpers\Systems::label($plan->system) }}</span>
                    <span class="text-muted fs-7">{{ $plan->transaction_number }} ·
                        {{ optional($plan->vendor_info)->name ?? '-' }}</span>
                </div>

                <div class="row g-4 fs-7 color-changer">
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="fw-600 border-bottom border-2 border-success pb-1 mb-2">{{ trans('labels.vendor_and_system') }}</div>
                        <div class="d-flex justify-content-between"><span class="text-muted">{{ trans('labels.name') }}</span>
                            <span class="fw-500">{{ optional($plan->vendor_info)->name ?? '-' }}</span></div>
                        <div class="d-flex justify-content-between"><span class="text-muted">{{ trans('labels.system') }}</span>
                            <span class="fw-500">{{ \App\Helpers\Systems::label($plan->system) }}</span></div>
                        <div class="d-flex justify-content-between"><span class="text-muted">{{ trans('labels.plan') }}</span>
                            <span class="fw-500">{{ $ocSnap['name'] ?? $plan->plan_name }}</span></div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="fw-600 border-bottom border-2 border-success pb-1 mb-2">{{ trans('labels.payment') }}</div>
                        <div class="d-flex justify-content-between"><span class="text-muted">{{ trans('labels.payment_type') }}</span>
                            <span class="fw-500">{{ \App\Helpers\Subscriptions::methodName($plan->payment_type) }}</span></div>
                        <div class="d-flex justify-content-between"><span class="text-muted">{{ trans('labels.gateway_reference') }}</span>
                            <span class="fw-500">{{ $plan->payment_id ?: '—' }}</span></div>
                        <div class="d-flex justify-content-between"><span class="text-muted">{{ trans('labels.total') }}</span>
                            <span class="fw-500">{{ number_format((float) $plan->grand_total, 2) }} {{ $ocCur }}</span></div>
                        <div class="d-flex justify-content-between"><span class="text-muted">{{ trans('labels.status') }}</span>
                            <span class="badge {{ $ocPay['class'] }}">{{ app()->getLocale() === 'ar' ? $ocPay['label_ar'] : $ocPay['label'] }}</span></div>
                        @if ($ocReceipt)
                            <div class="mt-1"><a href="{{ $ocReceipt }}" target="_blank">
                                <i class="fa-solid fa-receipt"></i> {{ trans('labels.bank_transfer_receipt') }}</a></div>
                        @endif
                    </div>

                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="fw-600 border-bottom border-2 border-success pb-1 mb-2">{{ trans('labels.subscription_timeline') }}</div>
                        <div class="d-flex justify-content-between"><span class="text-muted">{{ trans('labels.payment_date') }}</span>
                            <span class="fw-500">{{ $plan->purchase_date ? date('d M Y', strtotime($plan->purchase_date)) : '—' }}</span></div>
                        <div class="d-flex justify-content-between"><span class="text-muted">{{ trans('labels.activation_date') }}</span>
                            <span class="fw-500">{{ $plan->activated_at ? date('d M Y', strtotime($plan->activated_at)) : trans('labels.not_activated') }}</span></div>
                        <div class="d-flex justify-content-between"><span class="text-muted">{{ trans('labels.expire_date') }}</span>
                            <span class="fw-500">{{ $plan->expire_date ? date('d M Y', strtotime($plan->expire_date)) : trans('labels.starts_on_activation') }}</span></div>
                        <div class="d-flex justify-content-between"><span class="text-muted">{{ trans('labels.activation_term') }}</span>
                            <span class="{{ $ocTerm['class'] }} fw-500">{{ $ocTerm['note'] }}</span></div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="fw-600 border-bottom border-2 border-success pb-1 mb-2">{{ trans('labels.plan_snapshot') }}</div>
                        <div class="d-flex justify-content-between"><span class="text-muted">{{ trans('labels.offer') }}</span>
                            <span class="fw-500">{{ $ocOffer ?: trans('labels.none') }}
                                @if ((float) $plan->offer_amount > 0)(-{{ number_format((float) $plan->offer_amount, 2) }} {{ $ocCur }})@endif
                            </span></div>
                        <div class="d-flex justify-content-between"><span class="text-muted">{{ trans('labels.addons') }}</span>
                            <span class="fw-500">
                                @if (!empty($ocAddons))
                                    @foreach ($ocAddons as $ocKey => $ocVal)
                                        +{{ is_numeric($ocVal) ? $ocVal : 1 }} {{ ucwords(str_replace('_', ' ', is_numeric($ocKey) ? $ocVal : $ocKey)) }}@if (!$loop->last), @endif
                                    @endforeach
                                @else
                                    {{ trans('labels.none') }}
                                @endif
                            </span></div>
                        <div class="d-flex justify-content-between"><span class="text-muted">{{ trans('labels.price') }}</span>
                            <span class="fw-500">{{ number_format((float) ($ocSnap['price'] ?? $plan->amount), 2) }} {{ $ocCur }}</span></div>
                        <div class="d-flex justify-content-between"><span class="text-muted">{{ trans('labels.invoice') }}</span>
                            <a href="{{ URL::to('/admin/transaction/generatepdf-' . $plan->id) }}">{{ trans('labels.downloadpdf') }}</a></div>
                        @if (empty($plan->plan_snapshot))
                            <small class="text-muted d-block mt-1">{{ trans('messages.snapshot_not_captured') }}</small>
                        @endif
                    </div>
                </div>

                <div class="alert alert-success fs-7 mt-3 mb-0">
                    <i class="fa-solid fa-circle-check mx-1"></i>{{ trans('messages.subscription_rule_notice') }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 mt-3 mb-7">
        <div class="row g-3">
            <div class="col-md-4 col-sm-6">

                <div class="card border-0 box-shadow">
                    <div class="card-header rounded-top-4 p-3 bg-secondary">
                        <h5 class="text-white text-capitalize">
                            {{ $plan->plan_name }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h4 class="fw-600 text-dark color-changer mb-2">{{ helper::currency_formate($plan->amount, '') }}
                                <span class="fs-7 text-muted">/
                                    @if ($plan->duration != null || $plan->duration != '')
                                        @if ($plan->duration == 1)
                                            {{ trans('labels.one_month') }}
                                        @elseif($plan->duration == 2)
                                            {{ trans('labels.three_month') }}
                                        @elseif($plan->duration == 3)
                                            {{ trans('labels.six_month') }}
                                        @elseif($plan->duration == 4)
                                            {{ trans('labels.one_year') }}
                                        @elseif($plan->duration == 5)
                                            {{ trans('labels.lifetime') }}
                                        @endif
                                    @else
                                        {{ $plan->days }}
                                        {{ $plan->days > 1 ? trans('labels.days') : trans('labels.day') }}
                                    @endif
                                </span>
                            </h4>
                            @if ($plan->tax != null && $plan->tax != '')
                                <small class="text-danger">{{ trans('labels.exclusive_taxes') }}</small><br>
                            @else
                                <small class="text-success">{{ trans('labels.inclusive_taxes') }}</small> <br>
                            @endif
                            <small class="text-muted text-center">
                                {{ Str::limit($plan->description, 150) }}
                            </small>
                        </div>

                        <ul class="pb-5">

                            @php $features = ($plan->features == null ? null : explode('|', $plan->features));@endphp

                            <li class="mb-2 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary "></i>

                                <span class="mx-2 fs-7">

                                    {{ $plan->service_limit == -1 ? trans('labels.unlimited') : $plan->service_limit }}

                                    {{ \App\Helpers\Systems::entityLabel($plan->system, 'primary', $plan->service_limit) }}

                                </span>

                            </li>

                            <li class="mb-2 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary "></i>

                                <span class="mx-2 fs-7">

                                    {{ $plan->appoinment_limit == -1 ? trans('labels.unlimited') : $plan->appoinment_limit }}

                                    {{ \App\Helpers\Systems::entityLabel($plan->system, 'secondary', $plan->appoinment_limit) }}

                                </span>

                            </li>


                            @if (App\Models\SystemAddons::where('unique_identifier', 'coupon')->first() != null &&
                                    App\Models\SystemAddons::where('unique_identifier', 'coupon')->first()->activated == 1)
                                @if ($plan->coupons == 1)
                                    <li class="mb-2 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary "></i>

                                        <span class="mx-2 fs-7">{{ trans('labels.coupons') }}</span>

                                    </li>
                                @endif
                            @endif

                            @if (App\Models\SystemAddons::where('unique_identifier', 'custom_domain')->first() != null &&
                                    App\Models\SystemAddons::where('unique_identifier', 'custom_domain')->first()->activated == 1)
                                @if ($plan->custom_domain == 1)
                                    <li class="mb-2 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary "></i>

                                        <span class="mx-2 fs-7">{{ trans('labels.custome_domain') }}</span>

                                    </li>
                                @endif
                            @endif

                            @if (App\Models\SystemAddons::where('unique_identifier', 'google_analytics')->first() != null &&
                                    App\Models\SystemAddons::where('unique_identifier', 'google_analytics')->first()->activated == 1)
                                @if ($plan->google_analytics == 1)
                                    <li class="mb-2 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary "></i>

                                        <span class="mx-2 fs-7">{{ trans('labels.google_analytics') }}</span>

                                    </li>
                                @endif
                            @endif

                            @if (App\Models\SystemAddons::where('unique_identifier', 'blog')->first() != null &&
                                    App\Models\SystemAddons::where('unique_identifier', 'blog')->first()->activated == 1)
                                @if ($plan->blogs == 1)
                                    <li class="mb-2 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary "></i>

                                        <span class="mx-2 fs-7">{{ trans('labels.blogs') }}</span>

                                    </li>
                                @endif
                            @endif

                            @if (App\Models\SystemAddons::where('unique_identifier', 'google_login')->first() != null &&
                                    App\Models\SystemAddons::where('unique_identifier', 'google_login')->first()->activated == 1)
                                @if ($plan->google_login == 1)
                                    <li class="mb-2 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary "></i>

                                        <span class="mx-2 fs-7">{{ trans('labels.google_login') }}</span>

                                    </li>
                                @endif
                            @endif
                            @if (App\Models\SystemAddons::where('unique_identifier', 'facebook_login')->first() != null &&
                                    App\Models\SystemAddons::where('unique_identifier', 'facebook_login')->first()->activated == 1)
                                @if ($plan->facebook_login == 1)
                                    <li class="mb-2 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary "></i>

                                        <span class="mx-2 fs-7">{{ trans('labels.facebook_login') }}</span>

                                    </li>
                                @endif
                            @endif

                            @if (App\Models\SystemAddons::where('unique_identifier', 'notification')->first() != null &&
                                    App\Models\SystemAddons::where('unique_identifier', 'notification')->first()->activated == 1)
                                @if ($plan->sound_notification == 1)
                                    <li class="mb-2 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary "></i>

                                        <span class="mx-2 fs-7">{{ trans('labels.sound_notification') }}</span>

                                    </li>
                                @endif
                            @endif

                            @if (App\Models\SystemAddons::where('unique_identifier', 'whatsapp_message')->first() != null &&
                                    App\Models\SystemAddons::where('unique_identifier', 'whatsapp_message')->first()->activated == 1)
                                @if ($plan->whatsapp_message == 1)
                                    <li class="mb-2 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary "></i>

                                        <span class="mx-2 fs-7">{{ trans('labels.whatsapp_message') }}</span>

                                    </li>
                                @endif
                            @endif

                            @if (App\Models\SystemAddons::where('unique_identifier', 'telegram_message')->first() != null &&
                                    App\Models\SystemAddons::where('unique_identifier', 'telegram_message')->first()->activated == 1)
                                @if ($plan->telegram_message == 1)
                                    <li class="mb-2 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary "></i>

                                        <span class="mx-2 fs-7">{{ trans('labels.telegram_message') }}</span>

                                    </li>
                                @endif
                            @endif

                            @if (App\Models\SystemAddons::where('unique_identifier', 'vendor_app')->first() != null &&
                                    App\Models\SystemAddons::where('unique_identifier', 'vendor_app')->first()->activated == 1)
                                @if ($plan->vendor_app == 1)
                                    <li class="mb-2 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary "></i>

                                        <span class="mx-2 fs-7">{{ trans('labels.vendor_app_available') }}</span>

                                    </li>
                                @endif
                            @endif

                            @if (App\Models\SystemAddons::where('unique_identifier', 'user_app')->first() != null &&
                                    App\Models\SystemAddons::where('unique_identifier', 'user_app')->first()->activated == 1)
                                @if ($plan->customer_app == 1)
                                    <li class="mb-2 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary "></i>

                                        <span class="mx-2 fs-7">{{ trans('labels.customer_app') }}</span>

                                    </li>
                                @endif
                            @endif

                            @if (App\Models\SystemAddons::where('unique_identifier', 'pos')->first() != null &&
                                    App\Models\SystemAddons::where('unique_identifier', 'pos')->first()->activated == 1)
                                @if ($plan->pos == 1)
                                    <li class="mb-2 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary "></i>

                                        <span class="mx-2 fs-7">{{ trans('labels.pos') }}</span>

                                    </li>
                                @endif
                            @endif

                            @if (App\Models\SystemAddons::where('unique_identifier', 'pwa')->first() != null &&
                                    App\Models\SystemAddons::where('unique_identifier', 'pwa')->first()->activated == 1)
                                @if ($plan->pwa == 1)
                                    <li class="mb-2 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary "></i>

                                        <span class="mx-2 fs-7">{{ trans('labels.pwa') }}</span>

                                    </li>
                                @endif
                            @endif

                            @if (App\Models\SystemAddons::where('unique_identifier', 'employee')->first() != null &&
                                    App\Models\SystemAddons::where('unique_identifier', 'employee')->first()->activated == 1)
                                @if ($plan->role_management == 1)
                                    <li class="mb-2 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary "></i>

                                        <span class="mx-2 fs-7">{{ trans('labels.role_management') }}</span>

                                    </li>
                                @endif
                            @endif

                            @if (App\Models\SystemAddons::where('unique_identifier', 'pixel')->first() != null &&
                                    App\Models\SystemAddons::where('unique_identifier', 'pixel')->first()->activated == 1)
                                @if ($plan->pixel == 1)
                                    <li class="mb-2 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary "></i>

                                        <span class="mx-2 fs-7">{{ trans('labels.pixel') }}</span>

                                    </li>
                                @endif
                            @endif

                            @if ($features != '')
                                @foreach ($features as $feature)
                                    @if ($feature != '' && $feature != null)
                                        <li class="mb-2 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary "></i>

                                            <span class="mx-2 fs-7"> {{ $feature }} </span>

                                        </li>
                                    @endif
                                @endforeach
                            @endif



                        </ul>

                    </div>

                </div>

            </div>
            <div class="col-md-8 col-sm-6 payments">
                <div class="row g-3 flex-column">
                    @if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1))
                        <div class="col-12">
                            <div class="card border-0 box-shadow">
                                <div class="card-header rounded-top-4 border-bottom bg-transparent p-3">
                                    <h5 class="text-dark color-changer">{{ trans('labels.vendor_info') }}</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between px-0">

                                            <p class="fw-500 fs-15 color-changer">{{ trans('labels.transaction_number') }}</p>

                                            <p class="fw-600 text-dark color-changer">{{ $plan->transaction_number }}</p>

                                        </li>

                                        <li class="list-group-item d-flex justify-content-between px-0">

                                            <p class="fw-500 fs-15 color-changer">{{ trans('labels.name') }}</p>

                                            <p class="fw-500 fs-15 color-changer">{{ $plan['vendor_info']->name }}</p>

                                        </li>

                                        <li class="list-group-item d-flex justify-content-between px-0">

                                            <p class="fw-500 fs-15 color-changer">{{ trans('labels.email') }}</p>

                                            <p class="fw-500 fs-15 color-changer">{{ $plan['vendor_info']->email }}</p>

                                        </li>

                                        <li class="list-group-item d-flex justify-content-between px-0 border-bottom-0">

                                            <p class="fw-500 fs-15 color-changer">{{ trans('labels.mobile') }}</p>

                                            <p class="fw-500 fs-15 color-changer">{{ $plan['vendor_info']->mobile }}</p>

                                        </li>

                                    </ul>

                                </div>

                            </div>
                        </div>
                    @endif
                    <div class="col-12">
                        <div class="card border-0 box-shadow">
                            <div class="card-header border-bottom rounded-top-4 bg-transparent p-3">
                                <h5 class="text-dark color-changer">{{ trans('labels.plan_information') }}</h5>
                            </div>
                            <div class="card-body">

                                <ul class="list-group list-group-flush">

                                    <li class="list-group-item d-flex justify-content-between px-0">

                                        <p class="fw-500 fs-15 color-changer">{{ trans('labels.payment_type') }}</p>

                                        <p class="fw-500 fs-15 color-changer">

                                            @if (\App\Helpers\Subscriptions::isManual($plan->payment_type))
                                                {{-- Offline methods all settle with an uploaded receipt. This used to
                                                     match type 6 only, so a Benefit or Al Salam Bank payment fell through
                                                     to the "-" branch and its receipt was unreachable from the panel. --}}
                                                {{ \App\Helpers\Subscriptions::methodName($plan->payment_type) }}
                                                @if ($ocReceipt = \App\Helpers\Subscriptions::receiptUrl($plan))
                                                    : <small>
                                                        <a href="{{ $ocReceipt }}" target="_blank"
                                                            class="text-danger">{{ trans('labels.click_here') }}</a>
                                                    </small>
                                                @endif
                                            @elseif(in_array($plan->payment_type, [2, 3, 4, 5, 7, 8, 9, 10, 11, 12, 13, 14, 15]))
                                                {{ helper::getpayment($plan->payment_type, 1)->payment_name }} :
                                                {{ $plan->payment_id }}
                                            @elseif($plan->payment_type == 0)
                                                {{ trans('labels.manual') }}
                                            @elseif($plan->amount == 0)
                                                -
                                            @else
                                                -
                                            @endif

                                        </p>

                                    </li>

                                    <li class="list-group-item d-flex justify-content-between px-0">

                                        <p class="fw-500 fs-15 color-changer">{{ trans('labels.purchase_date') }}</p>

                                        <p class="fw-500 fs-15 color-changer">
                                            {{ helper::date_format($plan->purchase_date, $vendor_id) }}
                                        </p>

                                    </li>

                                    <li class="list-group-item d-flex justify-content-between px-0 border-bottom-2">

                                        <p class="fw-500 fs-15 color-changer">{{ trans('labels.expire_date') }}</p>

                                        <p class="fw-500 fs-15 color-changer">

                                            {{ $plan->expire_date != '' ? helper::date_format($plan->expire_date, $vendor_id) : '-' }}
                                        </p>

                                    </li>

                                </ul>

                            </div>

                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card border-0 box-shadow">
                            <div class="card-header border-bottom rounded-top-4 bg-transparent p-3">
                                <h5 class="text-dark color-changer">{{ trans('labels.payment_information') }}</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <p class="fw-500 fs-15 color-changer">{{ trans('labels.sub_total') }}</p>
                                        <p class="fw-500 fs-15 color-changer">{{ helper::currency_formate($plan->amount, '') }}</p>
                                    </li>
                                    @if ($plan->amount != 0)
                                        @if ($plan->tax != null && $plan->tax != '')
                                            @php
                                                $tax = explode('|', $plan->tax);
                                                $tax_name = explode('|', $plan->tax_name);
                                            @endphp
                                            @foreach ($tax as $key => $tax_value)
                                                @if ($tax_value != 0)
                                                    <li
                                                        class="list-group-item d-flex justify-content-between px-0 border-bottom-2">
                                                        <p class="fw-500 fs-15 color-changer">{{ $tax_name[$key] }}</p>
                                                        <p class="fw-500 fs-15 color-changer">
                                                            {{ helper::currency_formate(@$tax[$key], '') }}
                                                        </p>
                                                    </li>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endif
                                    @if ($plan->offer_code != null && $plan->offer_amount != null)
                                        <li class="list-group-item d-flex justify-content-between px-0 border-bottom-2">
                                            <p class="fw-500 fs-15 color-changer">{{ trans('labels.discount') }}
                                                ({{ $plan->offer_code }})</p>
                                            <p class="fw-500 fs-15 color-changer">
                                                -{{ helper::currency_formate($plan->offer_amount, '') }}
                                            </p>
                                        </li>
                                    @endif

                                    <li class="list-group-item d-flex justify-content-between px-0">

                                        <p class="fw-600 text-success">{{ trans('labels.grand_total') }}</p>

                                        <p class="fw-600 text-success">

                                            {{ helper::currency_formate($plan->grand_total, '') }}

                                        </p>

                                    </li>
                                </ul>
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

    </script>
@endsection
