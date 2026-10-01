{{-- The four fields added to the Coupons form.
     Coupon CODES only — automatic offers stay in Subscription Plans and are not duplicated here.
     Shown to the platform admin only; a vendor's own storefront coupon has no system or plan. --}}
@php
    $ocPlatform = Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1);
    $ocC = $coupon ?? null;
    $ocSystem = old('applicable_system', $ocC->applicable_system ?? 'all');
    $ocSelectedPlans = old('applicable_plans', $ocC ? $ocC->planIds() : []);
    $ocSelectedPlans = array_map('strval', (array) $ocSelectedPlans);
@endphp

@if ($ocPlatform)
    <div class="form-group">
        <label class="form-label">{{ trans('labels.applicable_system') }}<span class="text-danger">*</span></label>
        <select class="form-select" name="applicable_system" id="ocCouponSystem" required>
            <option value="all" {{ $ocSystem === 'all' ? 'selected' : '' }}>{{ trans('labels.all_systems') }}</option>
            @foreach (\App\Helpers\Systems::all() as $ocS)
                <option value="{{ $ocS['key'] }}" {{ $ocSystem === $ocS['key'] ? 'selected' : '' }}>
                    {{ $ocS['name'] }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label class="form-label">{{ trans('labels.applicable_plans') }}</label>
        <select class="form-select" name="applicable_plans[]" id="ocCouponPlans" multiple size="5">
            @foreach ($plans as $ocP)
                <option value="{{ $ocP->id }}" data-system="{{ $ocP->system ?: 'orders' }}"
                    {{ in_array((string) $ocP->id, $ocSelectedPlans, true) ? 'selected' : '' }}>
                    {{ $ocP->name }} — {{ number_format((float) $ocP->price, 2) }} {{ $ocP->currency ?: 'USD' }}
                </option>
            @endforeach
        </select>
        <small class="text-muted">{{ trans('messages.applicable_plans_hint') }}</small>
    </div>

    <div class="form-group">
        <label class="form-label">{{ trans('labels.max_total_uses') }}</label>
        <input type="number" min="1" class="form-control" name="max_total_uses"
            value="{{ old('max_total_uses', $ocC->max_total_uses ?? '') }}"
            placeholder="{{ trans('labels.unlimited') }}">
        <small class="text-muted">{{ trans('messages.max_total_uses_hint') }}</small>
    </div>

    <div class="form-group">
        <label class="form-label">{{ trans('labels.status') }}<span class="text-danger">*</span></label>
        <select class="form-select" name="is_available" required>
            <option value="1" {{ (string) old('is_available', $ocC->is_available ?? 1) === '1' ? 'selected' : '' }}>{{ trans('labels.active') }}</option>
            <option value="2" {{ (string) old('is_available', $ocC->is_available ?? 1) === '2' ? 'selected' : '' }}>{{ trans('labels.inactive') }}</option>
        </select>
    </div>

    <script>
        // Applicable plans follow the selected system, so a coupon can never be attached to a plan
        // from a system it does not cover.
        (function () {
            var sys = document.getElementById('ocCouponSystem');
            var plans = document.getElementById('ocCouponPlans');
            if (!sys || !plans) return;

            function filter() {
                var want = sys.value;
                Array.prototype.forEach.call(plans.options, function (o) {
                    var show = want === 'all' || o.getAttribute('data-system') === want;
                    o.hidden = !show;
                    o.disabled = !show;
                    if (!show) o.selected = false;
                });
            }

            sys.addEventListener('change', filter);
            filter();
        })();
    </script>
@endif
