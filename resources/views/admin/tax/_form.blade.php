{{-- Shared Add/Edit tax rule form.
     Layout follows the approved reference: Tax name · Applies to · Systems / Type · Value ·
     Price type / Registration number (read-only, from General Settings) · Status.
     Uses the existing admin card + form classes — no new styling. --}}
@php
    $ocTax = $tax ?? null;
    $ocCompany = \App\Helpers\Subscriptions::company();
    $ocVal = fn($field, $default = '') => old($field, $ocTax->{$field} ?? $default);
    $ocIsSubscription = $ocVal('applies_to', \App\Models\Tax::APPLIES_VENDOR_SALES) === \App\Models\Tax::APPLIES_SUBSCRIPTION;
    $ocAr = app()->getLocale() === 'ar';
@endphp

{{-- Do not charge tax until the registration and rate are confirmed. --}}
<div class="alert alert-warning d-flex align-items-start gap-2 mb-3">
    <i class="fa-solid fa-circle-exclamation mt-1"></i>
    <div>
        <div class="fw-600">{{ trans('labels.keep_inactive_until_confirmed') }}</div>
        <div class="fs-7">{{ trans('messages.tax_inactive_notice') }}</div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-xl-8">
        <div class="card border-0 box-shadow h-100">
            <div class="card-body">
                <h6 class="mb-3">{{ trans('labels.tax_settings') }}</h6>
                <form action="{{ $action }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-6 col-xl-4">
                            <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.name') }}<span class="text-danger"> *</span></label>
                            <input type="text" class="form-control" name="name" value="{{ $ocVal('name') }}"
                                placeholder="{{ $ocAr ? 'ضريبة اشتراك أوردر كليك' : 'Order Click Subscription Tax' }}" required>
                        </div>

                        <div class="col-12 col-md-6 col-xl-4">
                            <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.applies_to') }}</label>
                            <select name="applies_to" id="ocAppliesTo" class="form-select">
                                @foreach (\App\Models\Tax::appliesToOptions() as $k => $lbl)
                                    <option value="{{ $k }}" {{ $ocVal('applies_to', \App\Models\Tax::APPLIES_VENDOR_SALES) === $k ? 'selected' : '' }}>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6 col-xl-4">
                            <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.systems') }}</label>
                            <select name="systems" class="form-select">
                                @foreach (\App\Models\Tax::systemOptions() as $k => $lbl)
                                    <option value="{{ $k }}" {{ (string) $ocVal('systems', 'all') === (string) $k ? 'selected' : '' }}>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6 col-xl-4">
                            <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.type') }}<span class="text-danger"> *</span></label>
                            <select name="type" id="ocTaxType" class="form-select" required>
                                <option value="2" {{ (string) $ocVal('type', 2) === '2' ? 'selected' : '' }}>{{ trans('labels.percentage') }} (%)</option>
                                <option value="1" {{ (string) $ocVal('type', 2) === '1' ? 'selected' : '' }}>{{ trans('labels.fixed') }}</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-6 col-xl-4">
                            <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.tax_value') }}<span class="text-danger"> *</span></label>
                            <input type="number" step="0.01" min="0" class="form-control" name="tax" id="ocTaxValue"
                                value="{{ $ocVal('tax', '0.00') }}" required>
                        </div>

                        <div class="col-12 col-md-6 col-xl-4">
                            <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.price_type') }}</label>
                            <select name="price_type" class="form-select">
                                @foreach (\App\Models\Tax::priceTypeOptions() as $k => $lbl)
                                    <option value="{{ $k }}" {{ $ocVal('price_type', 'exclusive') === $k ? 'selected' : '' }}>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Entered once in General Settings, never per rule. --}}
                        <div class="col-12 col-xl-8">
                            <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.tax_registration_number_from_settings') }}</label>
                            <input type="text" class="form-control bg-light" readonly
                                value="{{ $ocCompany['tax_no'] ?: '' }}"
                                placeholder="{{ trans('labels.loaded_automatically') }}">
                            @if (!$ocCompany['tax_no'])
                                <small class="text-muted">
                                    <a href="{{ URL::to('admin/settings') }}">{{ trans('labels.settings') }}</a> —
                                    {{ trans('messages.tax_number_missing') }}
                                </small>
                            @endif
                        </div>

                        <div class="col-12 col-xl-4">
                            <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.status') }}</label>
                            <select name="is_available" id="ocTaxStatus" class="form-select">
                                <option value="2" {{ (string) $ocVal('is_available', 2) === '2' ? 'selected' : '' }}>{{ trans('labels.inactive') }}</option>
                                <option value="1" {{ (string) $ocVal('is_available', 2) === '1' ? 'selected' : '' }}>{{ trans('labels.active') }}</option>
                            </select>
                            <small class="text-muted" id="ocTaxStatusHint"></small>
                        </div>

                        <div class="col-12 d-flex gap-2 justify-content-end mt-2">
                            <a href="{{ URL::to('admin/tax') }}" class="btn btn-light px-4 rounded-start-5 rounded-end-5">{{ trans('labels.cancel') }}</a>
                            <button class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_tax', Auth::user()->role_id, $vendor_id, 'add') == 1 ? '' : 'd-none') : '' }}"
                                @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif>
                                {{ trans('labels.save') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Live invoice preview + what this rule actually touches. --}}
    <div class="col-12 col-xl-4">
        <div class="card border-0 box-shadow mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{ helper::image_path($ocCompany['logo']) }}" alt="" style="height:34px;width:auto;">
                        <div>
                            <div class="fw-600 color-changer">{{ $ocCompany['name'] }}</div>
                            @if ($ocCompany['legal'])
                                <div class="fs-7 text-muted">{{ $ocCompany['legal'] }}</div>
                            @endif
                        </div>
                    </div>
                    <span class="badge bg-success">{{ trans('labels.invoice_preview') }}</span>
                </div>

                @php $ocPreviewBase = 14.99; @endphp
                <div class="d-flex justify-content-between fs-7 py-2 border-bottom">
                    <span class="text-muted">{{ trans('labels.subtotal') }}</span>
                    <span class="fw-500">{{ number_format($ocPreviewBase, 2) }} USD</span>
                </div>
                <div class="d-flex justify-content-between fs-7 py-2 border-bottom">
                    <span class="text-muted">{{ trans('labels.discount') }}</span>
                    <span class="fw-500">0.00</span>
                </div>
                <div class="d-flex justify-content-between fs-7 py-2 border-bottom">
                    <span class="text-muted">{{ trans('labels.tax') }} (<span id="ocPrevRate">0.00</span>)</span>
                    <span class="fw-500" id="ocPrevTax">0.00</span>
                </div>
                <div class="d-flex justify-content-between py-2 fw-600">
                    <span>{{ trans('labels.grand_total') }}</span>
                    <span id="ocPrevTotal">{{ number_format($ocPreviewBase, 2) }} USD</span>
                </div>
            </div>
        </div>

        <div class="card border-0 box-shadow">
            <div class="card-body">
                <h6 class="mb-3">{{ trans('labels.tax_scope') }}</h6>
                <div class="d-flex gap-2 mb-3">
                    <i class="fa-solid fa-circle-check text-success mt-1"></i>
                    <div>
                        <div class="fw-500 fs-7">{{ trans('labels.subscription_invoices') }}</div>
                        <div class="fs-7 text-muted">{{ trans('messages.tax_scope_subscription') }}</div>
                    </div>
                </div>
                <div class="d-flex gap-2 mb-3">
                    <i class="fa-solid fa-minus text-muted mt-1"></i>
                    <div>
                        <div class="fw-500 fs-7">{{ trans('labels.vendor_customer_sales') }}</div>
                        <div class="fs-7 text-muted">{{ trans('messages.tax_scope_vendor') }}</div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <i class="fa-solid fa-circle-info text-muted mt-1"></i>
                    <div>
                        <div class="fw-500 fs-7">{{ trans('labels.company_information') }}</div>
                        <div class="fs-7 text-muted">{{ trans('messages.tax_scope_company') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Live invoice preview: shows what this rule would do to a sample subscription line.
    (function () {
        var base = {{ $ocPreviewBase }};
        var val = document.getElementById('ocTaxValue');
        var type = document.getElementById('ocTaxType');
        var status = document.getElementById('ocTaxStatus');
        var hint = document.getElementById('ocTaxStatusHint');
        if (!val || !type) return;

        function render() {
            var v = parseFloat(val.value) || 0;
            var isPct = type.value === '2';
            var tax = isPct ? (base * v / 100) : v;

            document.getElementById('ocPrevRate').textContent = isPct ? v.toFixed(2) + '%' : v.toFixed(2);
            document.getElementById('ocPrevTax').textContent = tax.toFixed(2);
            document.getElementById('ocPrevTotal').textContent = (base + tax).toFixed(2) + ' USD';

            // A rule with no rate cannot be switched on.
            if (status) {
                var noRate = v <= 0;
                status.disabled = noRate;
                if (noRate) status.value = '2';
                if (hint) hint.textContent = noRate
                    ? '{{ $ocAr ? "أدخل قيمة أكبر من صفر لتفعيل القاعدة." : "Enter a value above zero before this rule can be activated." }}'
                    : '';
            }
        }

        val.addEventListener('input', render);
        type.addEventListener('change', render);
        render();
    })();
</script>
