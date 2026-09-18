@extends('admin.layout.default')
@section('content')
    @php $ocEff = method_exists($plan, 'effectivePrice') ? $plan->effectivePrice() : $plan->price; @endphp
@php
if (Auth::user()->type == 4) {
$vendor_id = Auth::user()->vendor_id;
} else {
$vendor_id = Auth::user()->id;
}
$user = App\Models\User::where('id', $vendor_id)->where('is_available', 1)->where('is_deleted', 2)->first();
@endphp
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="col-12">
        <h5 class="pages-title color-changer fs-2">{{ trans('labels.pricing_plan') }}</h5>
        @include('admin.layout.breadcrumb')
    </div>
</div>
<div class="col-12 mt-3 mb-7">
    <div class="row g-3">
        <div class="col-12 col-md-12 col-lg-12 col-xl-8 payments">
            @if (App\Models\SystemAddons::where('unique_identifier', 'coupon')->first() != null &&
            App\Models\SystemAddons::where('unique_identifier', 'coupon')->first()->activated == 1)
            <div class="card border-0 box-shadow">
                <div class="card-header border-bottom rounded-top-4 bg-transparent p-3">
                    <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
                        <h5 class="text-dark color-changer">{{ trans('labels.apply_coupon') }}</h5>
                        <p class="text-secondary cursor-pointer {{ session()->has('discount_data') ? 'd-none' : '' }}"
                            data-bs-toggle="modal" data-bs-target="#couponmodal">
                            {{ trans('labels.select_promocode') }}
                        </p>
                    </div>
                </div>
                <div class="card-body">
                    @php
                    $count = App\Models\Promocode::where('vendor_id', 1)->count();
                    $coupons = App\Models\Promocode::where('vendor_id', 1)->orderBy('reorder_id')->get();
                    @endphp
                    @if (session()->has('discount_data'))
                    <div class="input-group">
                        <input type="text"
                            class="form-control {{ session()->get('direction') == 2 ? 'rounded-start-0 rounded-end-5' : 'rounded-start-5 rounded-end-0' }}"
                            id="promocode" name="promocode"
                            value="{{ session()->get('discount_data')['offer_code'] }}" readonly
                            placeholder="{{ trans('labels.enter_coupon_code') }}">
                        <button type="button" onclick="removecoupon()"
                            class="btn btn-secondary px-4 {{ session()->get('direction') == 2 ? 'rounded-start-5 rounded-end-0 border-end-0' : 'rounded-start-0 rounded-end-5' }}">{{ trans('labels.remove') }}</button>
                    </div>
                    @else
                    <div class="input-group">
                        <input type="text"
                            class="form-control {{ session()->get('direction') == 2 ? 'rounded-start-0 rounded-end-5' : 'rounded-start-5 rounded-end-0' }}"
                            id="promocode" name="promocode" readonly
                            placeholder="{{ trans('labels.enter_coupon_code') }}">
                        <button type="button" onclick="applyCopon()"
                            class="btn btn-secondary px-4 {{ session()->get('direction') == 2 ? 'rounded-start-5 rounded-end-0 border-end-0' : 'rounded-start-0 rounded-end-5' }}">{{ trans('labels.apply') }}</button>
                    </div>
                    @endif
                </div>
            </div>
            @endif
            <div class="card border-0 box-shadow mt-3">
                <div class="card-header bg-transparent border-bottom rounded-top-4 p-3">
                    <h5 class="text-dark color-changer m-0">{{ trans('labels.payment_details') }}</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <p class="fw-600 color-changer fs-15">{{ trans('labels.sub_total') }}</p>
                            <p class="fw-600 color-changer fs-15">{{ helper::currency_formate($ocEff, '') }}</p>
                        </li>
                        @php
                            $ocSys = $plan->system ?? 'orders';
                            $ocPlanAddons = is_array($plan->plan_addons ?? null) ? $plan->plan_addons : [];
                            $ocAddonLabels = [
                                'orders'  => ['branch' => 'Extra Branch', 'whatsapp' => 'Extra WhatsApp Number', 'team' => 'Extra Team Member'],
                                'booking' => ['branch' => 'Extra Branch', 'whatsapp' => 'Extra WhatsApp Number', 'team' => 'Extra Staff / Provider'],
                                'service' => ['branch' => 'Extra Service Area', 'whatsapp' => 'Extra WhatsApp Number', 'team' => 'Extra Provider / Freelancer'],
                            ][$ocSys] ?? [];
                        @endphp
                        @foreach (['branch', 'whatsapp', 'team'] as $ak)
                            @if (!empty($ocPlanAddons[$ak]['allow']) && (float) ($ocPlanAddons[$ak]['price'] ?? 0) > 0)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="fs-15 fw-600 color-changer">{{ $ocAddonLabels[$ak] ?? ucfirst($ak) }}
                                        <small class="text-muted">({{ helper::currency_formate($ocPlanAddons[$ak]['price'], '') }})</small></span>
                                    <input type="number" min="0" value="0" class="form-control oc-addon-qty text-center"
                                        style="max-width:88px" data-price="{{ $ocPlanAddons[$ak]['price'] }}" data-key="{{ $ak }}">
                                </li>
                            @endif
                        @endforeach
                        <li class="list-group-item d-flex justify-content-between px-0 oc-addon-total-row" style="display:none">
                            <p class="fw-600 color-changer fs-15">{{ trans('labels.add_ons') }}</p>
                            <p class="fw-600 color-changer fs-15" id="oc_addon_total">—</p>
                        </li>
                        @if (session()->has('discount_data'))
                        @php
                        $discount = session()->get('discount_data')['offer_amount'];
                        @endphp
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <p class="fw-600 color-changer fs-15">{{ trans('labels.discount') }} <span
                                    class="text-dark color-changer">({{ session()->get('discount_data')['offer_code'] }})</span>
                            </p>
                            <p class="fw-600 fs-15 color-changer">
                                -{{ helper::currency_formate(session()->get('discount_data')['offer_amount'], '') }}
                            </p>
                        </li>
                        @else
                        @php
                        $discount = 0;
                        @endphp
                        @endif
                        @php
                        $taxlist = helper::gettax($plan->tax);
                        $newtax = [];
                        $totaltax = 0;
                        @endphp
                        @if ($plan->tax != null && $plan->tax != '')
                        @foreach ($taxlist as $tax)
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <p class="fw-600 color-changer fs-15">
                                {{ @$tax->name }}
                            </p>
                            <p class="fw-600 color-changer fs-15">
                                {{ @$tax->type == 1 ? helper::currency_formate(@$tax->tax, '') : helper::currency_formate($ocEff * (@$tax->tax / 100), '') }}
                            </p>
                            @php
                            if (@$tax->type == 1) {
                            $newtax[] = @$tax->tax;
                            } else {
                            $newtax[] = $ocEff * (@$tax->tax / 100);
                            }
                            @endphp
                        </li>
                        @endforeach
                        @endif
                        @foreach ($newtax as $item)
                        @php
                        $totaltax += (float) $item;
                        @endphp
                        @endforeach
                        <li class="list-group-item d-flex justify-content-between px-0">
                            @php
                            $grand_total = $ocEff - $discount + $totaltax;
                            @endphp
                            <p class="fw-bolder text-success">{{ trans('labels.grand_total') }}</p>
                            <input type="hidden" name="grand_total" id="grand_total" value="{{ $grand_total }}" data-base="{{ $grand_total }}">
                            <input type="hidden" name="selected_addons" id="selected_addons" value="">
                            <p class="fw-bolder text-success" id="oc_grand_display">{{ helper::currency_formate($grand_total, '') }}</p>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card border-0 box-shadow mt-3">
                <div class="card-header bg-transparent border-bottom rounded-top-4 p-3">
                    <h5 class="text-dark color-changer">{{ trans('labels.payment_methods') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-2 g-3">
                        @foreach ($paymentmethod as $pmdata)
                        @php
                        // Check if the current $pmdata is a system addon and activated
                        if ($pmdata->isIncludedGateway()) {
                        $systemAddonActivated = true;
                        } else {
                        $systemAddonActivated = false;
                        }
                        $addon = App\Models\SystemAddons::where(
                        'unique_identifier',
                        $pmdata->unique_identifier,
                        )->first();
                        if ($addon != null && $addon->activated == 1) {
                        $systemAddonActivated = true;
                        }
                        $payment_type = $pmdata->payment_type;

                        // Instructions shown inside the confirmation modal.
                        // CKEditor methods (bank transfer) store real HTML; the simple textarea
                        // methods (Benefit / Al Salam QR / payment link / cash) store plain text,
                        // so newlines are converted here. The value is printed with a single
                        // Blade escape: jQuery decodes it once when reading the attribute, so the
                        // markup arrives intact instead of showing raw <p> / <strong> tags.
                        $pmInstructions = trim((string) ($pmdata->payment_description ?? ''));
                        if ($pmInstructions !== '' && strip_tags($pmInstructions) === $pmInstructions) {
                            $pmInstructions = nl2br(e($pmInstructions));
                        }
                        if ($pmInstructions === '' && $payment_type == '6') {
                            $pmInstructions = collect([
                                $pmdata->bank_name ? trans('labels.bank_name') . ': ' . $pmdata->bank_name : null,
                                $pmdata->account_holder_name ? trans('labels.account_holder_name') . ': ' . $pmdata->account_holder_name : null,
                                $pmdata->account_number ? trans('labels.account_number') . ': ' . $pmdata->account_number : null,
                                $pmdata->bank_ifsc_code ? trans('labels.bank_ifsc_code') . ': ' . $pmdata->bank_ifsc_code : null,
                            ])->filter()->map(fn($l) => e($l))->implode('<br>');
                        }
                        // helper::image_path() silently falls back to the item placeholder when a
                        // file is missing, which would render an unrelated image as the "QR code".
                        $pmQr = !empty($pmdata->qr_image)
                            && file_exists(storage_path('app/public/admin-assets/images/about/payment/' . $pmdata->qr_image))
                                ? helper::image_path($pmdata->qr_image)
                                : '';
                        $pmLink = $payment_type == '21' ? (string) ($pmdata->payment_link ?? '') : '';
                        @endphp
                        @if ($systemAddonActivated)
                        <div class="col">
                            <label for="{{ $payment_type }}"
                                class="form-control border mb-0 px-2 py-1 cursor-pointer">
                                <div class="card card-bg h-100 border-0">
                                    <div class="card-body">
                                        <div class="input-group">
                                            <div class="input-group-text bg-transparent border-0 p-0">
                                                <input class="form-check-input mt-0" type="radio"
                                                    value="{{ $pmdata->public_key }}" id="{{ $payment_type }}"
                                                    data-transaction-type="{{ $payment_type }}"
                                                    data-currency="{{ $pmdata->currency }}"
                                                    @if ($payment_type=='6' ) data-bank-name="{{ $pmdata->bank_name }}" data-account-holder-name="{{ $pmdata->account_holder_name }}" data-account-number="{{ $pmdata->account_number }}" data-bank-ifsc-code="{{ $pmdata->bank_ifsc_code }}" @endif
                                                    @if (\App\Helpers\Subscriptions::isManual($payment_type))
                                                    data-method-name="{{ \App\Helpers\Subscriptions::methodName($payment_type) }}"
                                                    data-instructions="{{ $pmInstructions }}"
                                                    data-qr="{{ $pmQr }}"
                                                    data-link="{{ $pmLink }}"
                                                    @endif
                                                    name="paymentmode">
                                            </div>
                                            <div class="mx-3">
                                                <img src="{{ helper::image_path($pmdata->image) }}"
                                                    class="hw-20 rounded-0" alt="" srcset="">
                                                <span class="px-1 color-changer">{{ \App\Helpers\Subscriptions::methodName($pmdata->payment_type) }}</span>
                                            </div>
                                        </div>
                                        @if ($payment_type == '6')
                                        <input type="hidden" value="{{ $pmdata->payment_description }}"
                                            id="bank_payment">
                                        @endif
                                        @if ($payment_type == '3')
                                        <input type="hidden" name="stripe_public_key"
                                            id="stripe_public_key" value="{{ $pmdata->public_key }}">
                                        @endif
                                    </div>
                                </div>
                            </label>
                        </div>
                        @endif
                        @endforeach
                        <span
                            class="text-danger payment_error d-none">{{ trans('messages.select_atleast_one') }}</span>
                    </div>
                    <div class="form-group m-0 mt-3 d-flex justify-content-end">
                        <button
                            @if (env('Environment')=='sendbox' ) type="button" onclick="myFunction()" @else type="button" @endif
                            class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ env('Environment') == 'sendbox' ? '' : 'buy_now' }} ">
                            {{ trans('labels.checkout') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-6 col-xl-4">
            <div class="card box-shadow border-0 h-100 text-dark">
                <div class="card-header rounded-top-4 p-3 bg-secondary">
                    <h5 class="text-white text-capitalize">
                        {{ $plan->name }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h4 class="fw-600 text-dark color-changer mb-2">{{ helper::currency_formate($ocEff, '') }}
                            <span class="per-month fw-normal">/
                                @if ($plan->plan_type == 1)
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
                                @endif
                                @if ($plan->plan_type == 2)
                                {{ $plan->days }}
                                {{ $plan->days > 1 ? trans('labels.days') : trans('labels.day') }}
                                @endif

                            </span>
                        </h4>
                        {{-- <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4>{{ $plan->name }}</h4>
                    </div> --}}
                    @if ($plan->tax != null && $plan->tax != '')
                    <small class="text-danger">{{ trans('labels.exclusive_taxes') }}</small><br>
                    @else
                    <small class="text-success">{{ trans('labels.inclusive_taxes') }}</small> <br>
                    @endif
                    <small class="text-muted line-limit-3">{{ Str::limit($plan->description, 150) }}</small>
                </div>
                <ul>
                    @php $features = explode('|', $plan->features); @endphp
                    <li class="mb-3 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary  "></i>
                        <span class="mx-2 fs-7">
                            {{ $plan->order_limit == -1 ? trans('labels.unlimited') : $plan->order_limit }}
                            {{ \App\Helpers\Systems::entityLabel($plan->system, 'primary', $plan->order_limit) }}
                        </span>
                    </li>
                    <li class="mb-3 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary  "></i>
                        <span class="mx-2 fs-7">
                            {{ $plan->appointment_limit == -1 ? trans('labels.unlimited') : $plan->appointment_limit }}
                            {{ \App\Helpers\Systems::entityLabel($plan->system, 'secondary', $plan->appointment_limit) }}
                        </span>
                    </li>
                    @php
                    $themes = [];
                    if ($plan->themes_id != '' && $plan->themes_id != null) {
                    $themes = explode(',', $plan->themes_id);
                    } @endphp
                    <li class="mb-3 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary  "></i>
                        <span class="mx-2 fs-7">{{ count($themes) }}
                            {{ count($themes) > 1 ? trans('labels.themes') : trans('labels.theme') }}</span>
                    </li>
                    @if ($plan->coupons == 1)
                    <li class="mb-3 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary  "></i>
                        <span class="mx-2 fs-7">{{ trans('labels.coupons') }}</span>
                    </li>
                    @endif
                    @if ($plan->custom_domain == 1)
                    <li class="mb-3 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary  "></i>
                        <span class="mx-2 fs-7">{{ trans('labels.custome_domain_available') }}</span>
                    </li>
                    @endif
                    @if ($plan->vendor_app == 1)
                    <li class="mb-3 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary  "></i>
                        <span class="mx-2 fs-7">{{ trans('labels.vendor_app_available') }}</span>
                    </li>
                    @endif
                    @if ($plan->google_analytics == 1)
                    <li class="mb-3 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary  "></i>
                        <span class="mx-2 fs-7">{{ trans('labels.google_analytics_available') }}</span>
                    </li>
                    @endif

                    @if ($plan->blogs == 1)
                    <li class="mb-3 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary  "></i>
                        <span class="mx-2 fs-7">{{ trans('labels.blogs') }}</span>
                    </li>
                    @endif
                    @if ($plan->google_login == 1)
                    <li class="mb-3 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary  "></i>
                        <span class="mx-2 fs-7">{{ trans('labels.google_login') }}</span>
                    </li>
                    @endif
                    @if ($plan->facebook_login == 1)
                    <li class="mb-3 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary  "></i>
                        <span class="mx-2 fs-7">{{ trans('labels.facebook_login') }}</span>
                    </li>
                    @endif
                    @if ($plan->sound_notification == 1)
                    <li class="mb-3 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary  "></i>
                        <span class="mx-2 fs-7">{{ trans('labels.sound_notification') }}</span>
                    </li>
                    @endif
                    @if ($plan->whatsapp_message == 1)
                    <li class="mb-3 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary  "></i>
                        <span class="mx-2 fs-7">{{ trans('labels.whatsapp_message') }}</span>
                    </li>
                    @endif
                    @if ($plan->telegram_message == 1)
                    <li class="mb-3 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary  "></i>
                        <span class="mx-2 fs-7">{{ trans('labels.telegram_message') }}</span>
                    </li>
                    @endif
                    @if ($plan->pos == 1)
                    <li class="mb-3 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary  "></i>
                        <span class="mx-2 fs-7">{{ trans('labels.pos') }}</span>
                    </li>
                    @endif
                    @if ($plan->pwa == 1)
                    <li class="mb-3 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary  "></i>
                        <span class="mx-2 fs-7">{{ trans('labels.pwa') }}</span>
                    </li>
                    @endif
                    @if ($plan->tableqr == 1)
                    <li class="mb-3 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary  "></i>
                        <span class="mx-2 fs-7">{{ trans('labels.tableqr') }}</span>
                    </li>
                    @endif
                    @if ($plan->role_management == 1)
                    <li class="mb-3 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary  "></i>
                        <span class="mx-2 fs-7">{{ trans('labels.role_management') }}</span>
                    </li>
                    @endif

                    @foreach ($features as $feature)
                    <li class="mb-3 d-flex color-changer"> <i class="fa-regular fa-circle-check text-secondary  "></i>
                        <span class="mx-2 fs-7"> {{ $feature }} </span>
                    </li>
                    @endforeach
                </ul>

            </div>
        </div>
    </div>
</div>
</div>
<!-- Modal -->
<div class="modal fade" id="modalbankdetails" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="modalbankdetailsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header justify-content-between">
                <h5 class="modal-title text-dark color-changer" id="modalbankdetailsLabel">{{ trans('labels.banktransfer') }}</h5>
                <button type="button" class="bg-transparent border-0 m-0" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="fa-regular fa-xmark color-changer fs-4"></i>
                </button>
            </div>
            <form enctype="multipart/form-data" action="{{ URL::to('admin/plan/buyplan') }}" method="POST">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" name="payment_type" id="modal_payment_type" class="form-control"
                        value="">
                    <input type="hidden" name="plan_id" id="modal_plan_id" class="form-control" value="">
                    <input type="hidden" name="amount" id="modal_amount" class="form-control" value="">
                    <input type="hidden" name="modal_offer_code" id="modal_offer_code" class="form-control"
                        value="">
                    <input type="hidden" name="modal_discount" id="modal_discount" class="form-control"
                        value="">
                    <input type="hidden" name="selected_addons" id="modal_selected_addons" value="">
                    <p class="color-changer">{{ trans('labels.payment_description') }}</p>
                    <div class="my-3 border-bottom"></div>
                    <div class="text-center mb-3 d-none" id="payment_qr_wrap">
                        <img src="" alt="QR" id="payment_qr"
                            style="max-width:190px;width:100%;border-radius:12px;border:1px solid #e5e7eb;padding:6px;background:#fff;">
                    </div>
                    <div class="text-center mb-3 d-none" id="payment_link_wrap">
                        <a href="#" target="_blank" rel="noopener" id="payment_link"
                            class="btn btn-secondary px-4 rounded-start-5 rounded-end-5">
                            <i class="fa-solid fa-up-right-from-square"></i>
                            {{ trans('labels.pay_via_link') }}
                        </a>
                    </div>
                    <p class="payment_description color-changer" id="payment_description"></p>
                    <div class="my-3 border-bottom"></div>
                    <div class="form-group col-md-12">
                        <label for="screenshot" class="form-label"> {{ trans('labels.screenshot') }} </label>
                        <div class="controls">
                            {{-- Not `required`: on a phone the picked photo often does not attach
                                 (HEIC, camera capture), and the browser then blocks the submit with a
                                 validation bubble the user cannot see inside the modal - the button
                                 simply appeared dead. The receipt is optional server-side too. --}}
                            <input type="file" name="screenshot" id="screenshot" accept="image/*"
                                class="form-control  @error('screenshot') is-invalid @enderror">
                            @error('screenshot')
                            <span class="text-danger"> {{ $message }} </span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer gap-2">
                    <button type="button" class="btn btn-danger px-4 rounded-start-5 rounded-end-5 m-0"
                        data-bs-dismiss="modal">{{ trans('labels.close') }}</button>
                    <button id="ocBankSave"
                        @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif
                        class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 m-0"> {{ trans('labels.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if (env('Environment') != 'sendbox')
    <script>
        // Some mobile browsers do not fire the form's default submit from inside a modal footer.
        // Submitting explicitly makes the button behave the same on phone and desktop.
        document.addEventListener('DOMContentLoaded', function () {
            var btn = document.getElementById('ocBankSave');
            if (!btn) return;
            btn.addEventListener('click', function (e) {
                var form = btn.closest('form');
                if (!form) return;
                e.preventDefault();
                btn.disabled = true;
                if (typeof form.requestSubmit === 'function') form.requestSubmit(); else form.submit();
            });
        });
    </script>
@endif

<!-- Modal -->
<div class="modal fade" id="couponmodal" tabindex="-1" aria-labelledby="couponmodalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header justify-content-between">
                <h5 class="modal-title text-dark color-changer" id="couponmodalLabel">{{ trans('labels.coupons_offers') }}</h5>
                <button type="button" class="bg-transparent border-0 m-0" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="fa-regular fa-xmark color-changer fs-4"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="available-cuppon {{ session()->get('direction') == '2' ? 'text-right' : '' }}">
                    <p class="available-title text-dark color-changer" id="exampleModalLabel">
                        {{ trans('labels.available_coupons') }}
                    </p>
                </div>
                @foreach ($coupons as $coupon)
                <div class="card my-3 border-0 bg-white rgb-dark-light box-shadow">
                    <div
                        class="card-body p-0 overflow-hidden {{ session()->get('direction') == '2' ? 'pe-3' : 'ps-3' }}">
                        <div class="coupon rounded d-flex justify-content-between align-items-center">
                            <div
                                class="{{ session()->get('direction') == '2' ? 'right-side' : 'left-side' }} py-3 d-flex w-100 justify-content-start align-items-center">
                                <div>
                                    <h6 class="fw-600 text-dark color-changer">{{ $coupon->offer_name }}</h6>
                                    <p class="dark_color mb-0 fw-500 fs-15 color-changer mt-1">
                                        {{ trans('labels.coupons') }} :
                                        <span
                                            class="fw-normal text-decoration-underline text-uppercase text-secondary">
                                            {{ $coupon->offer_code }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="right-side border-0">
                                <div class="info m-3 d-flex align-items-center">
                                    <span
                                        class="{{ session()->get('direction') == '2' ? 'coupn-circle-up-right' : 'coupn-circle-up-left' }}"></span>
                                    <div class="w-100 d-flex justify-content-center">
                                        <button class="btn btn-success px-sm-4 fs-7 rounded-start-5 rounded-end-5"
                                            onclick="copy('{{ $coupon->offer_code }}')">
                                            {{ trans('labels.copy') }}
                                        </button>
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
</div>
<input type="hidden" name="price" id="price" value="{{ $ocEff }}">
<input type="hidden" name="plan_id" id="plan_id" value="{{ $plan->id }}">
<input type="hidden" name="user_name" id="user_name" value="{{ $user->name }}">
<input type="hidden" name="user_email" id="user_email" value="{{ $user->email }}">
<input type="hidden" name="user_mobile" id="user_mobile" value="{{ $user->mobile }}">

<form action="{{ url('admin/plan/buyplan/paypalrequest') }}" method="post" class="d-none">
    {{ csrf_field() }}
    <input type="hidden" name="return" value="2">
    <input type="submit" class="callpaypal" name="submit">
</form>
@endsection
@section('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="https://js.paystack.co/v1/inline.js"></script>
<script src="https://checkout.flutterwave.com/v3.js"></script>
<script>
    var SITEURL = "{{ URL::to('') }}";
    var planlisturl = "{{ URL::to('admin/plan') }}";
    var buyurl = "{{ URL::to('admin/plan/buyplan') }}";
    var plan_name = "{{ $plan->name }}";
    var plan_description = "{{ preg_replace('/\s+/', ' ', $plan->description) }}";
    var title = "{{ Str::limit(helper::appdata('')->website_title, 50) }}";
    var description = "Plan Subscription";
    var applycouponurl = "{{ URL::to('/admin/applycoupon') }}";
    var removecouponurl = "{{ URL::to('/admin/removecoupon') }}";
    var offer_code = "{{ session()->has('discount_data') ? session()->get('discount_data')['offer_code'] : 0 }}";
    var discount = "{{ session()->has('discount_data') ? session()->get('discount_data')['offer_amount'] : 0 }}";
    var sub_total = "{{ $ocEff }}";
</script>
<script src="{{ url(env('ASSETSPATHURL') . 'admin-assets/js/plan_payment.js') }}"></script>
<script>
    // Paid add-ons: adjust the charged total live. plan_payment.js reads the global `grand_total`
    // at load, so we reassign it here (same page scope) whenever an add-on quantity changes.
    (function () {
        var qtys = document.querySelectorAll('.oc-addon-qty');
        if (!qtys.length) return;
        var gtField = document.getElementById('grand_total');
        var base = parseFloat(gtField.getAttribute('data-base')) || 0;
        var curTpl = @json(helper::currency_formate(0, ''));
        function fmt(n) { return curTpl.replace(/[\d.,]+/, Number(n).toFixed(2)); }

        function recompute() {
            var addonTotal = 0, sel = {};
            qtys.forEach(function (q) {
                var n = parseInt(q.value, 10) || 0; if (n < 0) { n = 0; q.value = 0; }
                var p = parseFloat(q.getAttribute('data-price')) || 0;
                if (n > 0) { addonTotal += n * p; sel[q.getAttribute('data-key')] = { qty: n, price: p }; }
            });
            var newGrand = +(base + addonTotal).toFixed(2);
            var row = document.querySelector('.oc-addon-total-row');
            if (row) { row.style.display = addonTotal > 0 ? '' : 'none'; document.getElementById('oc_addon_total').textContent = fmt(addonTotal); }
            gtField.value = newGrand;
            var disp = document.getElementById('oc_grand_display'); if (disp) disp.textContent = fmt(newGrand);
            var ma = document.getElementById('modal_amount'); if (ma) ma.value = newGrand;
            var jsonStr = JSON.stringify(sel);
            var sa = document.getElementById('selected_addons'); if (sa) sa.value = jsonStr;
            var msa = document.getElementById('modal_selected_addons'); if (msa) msa.value = jsonStr;
            // reassign the global the payment gateways read
            try { if (typeof grand_total !== 'undefined') grand_total = newGrand; } catch (e) {}
            try { window.grand_total = newGrand; } catch (e) {}
        }
        qtys.forEach(function (q) { q.addEventListener('input', recompute); q.addEventListener('change', recompute); });
        recompute();
    })();
</script>
@endsection
