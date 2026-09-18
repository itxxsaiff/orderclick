@extends('front.theme.default')
@section('content')
    @php $isAr = app()->getLocale() === 'ar'; @endphp
    <style>
        .ocb-wrap { background: #f6f8f5; padding: 40px 0 64px; }
        .ocb-card { max-width: 660px; margin: 0 auto; background: #fff; border: 1px solid #e7ece4; border-radius: 20px;
            padding: 34px 32px; box-shadow: 0 30px 64px -42px rgba(20,40,28,.4); }
        .ocb-card h1 { font-size: 26px; font-weight: 800; color: #17201a; margin: 0 0 6px; }
        .ocb-card .ocb-sub { color: #6a756c; margin: 0 0 24px; font-size: 15px; }
        .ocb-field { margin-bottom: 16px; }
        .ocb-field label { display: block; font-weight: 600; font-size: 13.5px; color: #39443c; margin-bottom: 6px; }
        .ocb-field label .req { color: #d64545; }
        .ocb-field .ocb-in { width: 100%; height: 48px; border: 1px solid #d9e0d4; border-radius: 10px; padding: 0 14px; font-size: 15px; color: #17201a; background: #fff; box-sizing: border-box; }
        .ocb-field textarea.ocb-in { height: auto; padding: 12px 14px; }
        .ocb-field .ocb-in:focus { outline: none; border-color: var(--bs-primary); box-shadow: 0 0 0 3px rgba(0,0,0,.06); }
        .ocb-grid2 { display: grid; grid-template-columns: minmax(0,1fr) minmax(0,1fr); gap: 14px; }
        .ocb-sec { font-weight: 750; font-size: 15px; color: #17201a; margin: 24px 0 12px; padding-top: 20px; border-top: 1px solid #eef2ec; }
        .ocb-pay { display: grid; gap: 10px; }
        .ocb-pay .ocb-opt { border: 1px solid #d9e0d4; border-radius: 12px; padding: 12px 14px; display: flex; align-items: center; gap: 11px; cursor: pointer; }
        .ocb-pay .ocb-opt input { accent-color: var(--bs-primary); width: 18px; height: 18px; }
        .ocb-pay .ocb-opt img { height: 26px; width: auto; margin-left: auto; }
        .ocb-pay .ocb-opt.sel { border-color: var(--bs-primary); background: color-mix(in srgb, var(--bs-primary) 6%, #fff); }
        .ocb-payinfo { font-size: 13.5px; color: #39443c; background: #f7faf7; border: 1px solid #e7ece4; border-radius: 10px; padding: 12px 14px; margin-top: 4px; }
        .ocb-total { display: flex; justify-content: space-between; align-items: center; font-weight: 750; font-size: 17px; margin: 18px 0 6px; }
        .ocb-total span:last-child { color: var(--bs-primary); }
        .ocb-submit { width: 100%; height: 52px; border: 0; border-radius: 12px; background: var(--bs-primary); color: #fff; font-weight: 650; font-size: 16px; cursor: pointer; margin-top: 12px; }
        .ocb-submit:hover { filter: brightness(.93); }
        @media (max-width: 560px) { .ocb-grid2 { grid-template-columns: 1fr; } }
    </style>

    <div class="ocb-wrap">
        <div class="container">
            <div class="ocb-card">
                <h1>{{ trans('labels.book_an_appointment') }}</h1>
                <p class="ocb-sub">{{ trans('labels.choose_a_service_and_a_time_that') }}</p>

                <form action="{{ URL::to(@$storeinfo->slug . '/save-booking') }}" method="POST">
                    @csrf
                    <div class="ocb-field">
                        <label>{{ trans('labels.service') }} <span class="req">*</span></label>
                        @if (count($services) > 0)
                            <select name="service_name" id="ocb_service" class="ocb-in" required onchange="ocbService(this)">
                                <option value="">{{ trans('labels.select_a_service') }}</option>
                                @foreach ($services as $s)
                                    <option value="{{ $s->name }}" data-id="{{ $s->id }}" data-price="{{ $s->price }}"
                                        {{ (string) $selected === (string) $s->id ? 'selected' : '' }}>
                                        {{ $s->name }}@if ($s->price > 0) — {{ helper::currency_formate($s->price, $vdata) }}@endif
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="service_id" id="ocb_service_id" value="{{ $selected }}">
                        @else
                            <input type="text" name="service_name" class="ocb-in" required placeholder="{{ trans('labels.e_g_consultation_room_service') }}">
                        @endif
                    </div>

                    <div class="ocb-grid2">
                        <div class="ocb-field">
                            <label>{{ trans('labels.date') }} <span class="req">*</span></label>
                            <input type="date" name="booking_date" class="ocb-in" required min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="ocb-field">
                            <label>{{ trans('labels.time') }}</label>
                            <input type="time" name="booking_time" class="ocb-in">
                        </div>
                    </div>

                    <div class="ocb-grid2">
                        <div class="ocb-field">
                            <label>{{ trans('labels.your_name') }} <span class="req">*</span></label>
                            <input type="text" name="customer_name" class="ocb-in" required>
                        </div>
                        <div class="ocb-field">
                            <label>{{ trans('labels.mobile') }} <span class="req">*</span></label>
                            <input type="text" name="mobile" class="ocb-in" required>
                        </div>
                    </div>

                    <div class="ocb-field">
                        <label>{{ trans('labels.email_optional') }}</label>
                        <input type="email" name="email" class="ocb-in">
                    </div>
                    <div class="ocb-field">
                        <label>{{ trans('labels.notes') }}</label>
                        <textarea name="notes" rows="2" class="ocb-in" placeholder="{{ trans('labels.anything_the_provider_should_know') }}"></textarea>
                    </div>

                    {{-- Payment method (reuses the vendor's enabled methods; pay at location or online) --}}
                    @php
                        $bkPayments = collect($paymentlist)->filter(function ($p) {
                            if ($p->isIncludedGateway()) return true;
                            $addon = App\Models\SystemAddons::where('unique_identifier', $p->unique_identifier)->first();
                            return $addon != null && $addon->activated == 1;
                        })->values();
                    @endphp
                    @if ($bkPayments->count() > 0)
                        <div class="ocb-sec">{{ trans('labels.payment_method') }}</div>
                        <div class="ocb-pay">
                            @foreach ($bkPayments as $i => $payment)
                                <label class="ocb-opt {{ $i == 0 ? 'sel' : '' }}" data-opt="{{ $payment->payment_type }}">
                                    <input type="radio" name="payment_type" value="{{ $payment->payment_type }}"
                                        data-name="{{ $payment->payment_name }}" {{ $i == 0 ? 'checked' : '' }}>
                                    <span>{{ $payment->payment_name }}</span>
                                    <img src="{{ helper::image_path($payment->image) }}" alt="">
                                </label>
                                @if (in_array($payment->payment_type, ['6', '17', '18', '19', '20', '21']))
                                    <div class="ocb-payinfo" data-for="{{ $payment->payment_type }}" style="display:none;">
                                        @if (in_array($payment->payment_type, ['19', '20']) && !empty($payment->qr_image))
                                            <img src="{{ helper::image_path($payment->qr_image) }}" alt="QR" style="max-width:160px;border-radius:8px;display:block;margin-bottom:8px;">
                                        @endif
                                        @if ($payment->payment_type == '21' && !empty($payment->payment_link))
                                            <a href="{{ $payment->payment_link }}" target="_blank" class="btn btn-sm btn-primary mb-2">
                                                <i class="fa-solid fa-up-right-from-square"></i> {{ trans('labels.pay_via_link') }}
                                            </a><br>
                                        @endif
                                        @if (!empty($payment->payment_description))
                                            {!! nl2br(e($payment->payment_description)) !!}
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    <div class="ocb-total" id="ocb_total_row" style="display:none;">
                        <span>{{ trans('labels.total') }}</span>
                        <span id="ocb_total">—</span>
                    </div>

                    <button type="submit" class="ocb-submit">
                        <i class="fa-solid fa-calendar-check"></i> {{ trans('labels.confirm_booking') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        var ocbCur = @json(helper::currency_formate(0, $vdata));
        function ocbFmt(v) {
            // reuse the server currency symbol wrapper by replacing the number
            return ocbCur.replace(/[\d.,]+/, (v).toFixed(2));
        }
        function ocbService(sel) {
            var opt = sel.options[sel.selectedIndex];
            var id = opt.getAttribute('data-id') || '';
            var price = parseFloat(opt.getAttribute('data-price') || '0');
            var f = document.getElementById('ocb_service_id');
            if (f) f.value = id;
            var row = document.getElementById('ocb_total_row');
            if (price > 0) {
                document.getElementById('ocb_total').innerText = ocbFmt(price);
                row.style.display = 'flex';
            } else {
                row.style.display = 'none';
            }
        }
        // payment option selection + info toggle
        (function () {
            function refresh() {
                var sel = document.querySelector('input[name="payment_type"]:checked');
                document.querySelectorAll('.ocb-opt').forEach(function (o) { o.classList.remove('sel'); });
                document.querySelectorAll('.ocb-payinfo').forEach(function (b) { b.style.display = 'none'; });
                if (sel) {
                    sel.closest('.ocb-opt').classList.add('sel');
                    var box = document.querySelector('.ocb-payinfo[data-for="' + sel.value + '"]');
                    if (box) box.style.display = 'block';
                }
            }
            document.querySelectorAll('input[name="payment_type"]').forEach(function (r) { r.addEventListener('change', refresh); });
            refresh();
        })();
        // preselect total if a service came in from the storefront
        (function () {
            var sel = document.getElementById('ocb_service');
            if (sel && sel.value) ocbService(sel);
        })();
    </script>
@endsection
