{{--
    Shared plan "system engine" fields — used by Add and Edit.
    Pass $plan (the model) for edit; leave null for add.
    Counts represent the MAXIMUM allowance per plan (not current usage).
--}}
@php
    $plan = $plan ?? null;
    $sys = old('system', $plan->system ?? 'orders');
    $lim = (array) (old('limits') ?: (is_array($plan->plan_limits ?? null) ? $plan->plan_limits : []));
    $off = (array) (old('offer') ?: (is_array($plan->plan_offer ?? null) ? $plan->plan_offer : []));
    $add = (array) (old('addon') ?: (is_array($plan->plan_addons ?? null) ? $plan->plan_addons : []));
    $xf  = (array) (is_array($plan->plan_extra_features ?? null) ? $plan->plan_extra_features : []);
    $limVal = fn($k, $f, $d = '') => $lim[$k][$f] ?? $d;
    $isAr = app()->getLocale() === 'ar';

    // Per-system entity names (the count is the MAXIMUM allowance). JS swaps these live.
    $L = [
        'orders'  => ['primary' => 'Products', 'secondary' => 'Monthly Orders', 'branch' => 'Branches', 'whatsapp' => 'WhatsApp Numbers', 'team' => 'Team Members', 'addonbranch' => 'Branch', 'addonteam' => ''],
        'booking' => ['primary' => 'Services', 'secondary' => 'Monthly Bookings', 'branch' => 'Branches', 'whatsapp' => 'WhatsApp Numbers', 'team' => 'Staff / Providers', 'addonbranch' => 'Branch', 'addonteam' => 'Staff / Provider'],
        'service' => ['primary' => 'Service Listings', 'secondary' => 'Monthly Service Requests', 'branch' => 'Service Areas', 'whatsapp' => 'WhatsApp Numbers', 'team' => 'Providers / Freelancers', 'addonbranch' => 'Service Area', 'addonteam' => 'Provider / Freelancer'],
    ];

    // Per-system feature toggles.
    $F = [
        'orders' => ['delivery' => 'Delivery', 'pickup' => 'Pickup', 'dine_in' => 'Dine-in', 'external_delivery' => 'External Delivery Providers', 'whatsapp_ordering' => 'WhatsApp Ordering', 'whatsapp_notifications' => 'WhatsApp Notifications', 'online_payments' => 'Online Payments', 'coupons' => 'Coupons', 'qr_code' => 'QR Code', 'basic_analytics' => 'Basic Analytics', 'advanced_analytics' => 'Advanced Analytics', 'priority_support' => 'Priority Support', 'custom_domain' => 'Custom Domain', 'sound_notifications' => 'Sound Notifications', 'blogs' => 'Blogs'],
        'booking' => ['online_booking' => 'Online Booking', 'staff_calendars' => 'Staff Calendars', 'whatsapp_reminders' => 'WhatsApp Reminders', 'reschedule_cancellation' => 'Reschedule & Cancellation', 'deposits_payments' => 'Deposits / Online Payments', 'resource_room_booking' => 'Resource / Room Booking', 'waitlist' => 'Waitlist', 'advanced_reports' => 'Advanced Reports', 'multi_branch_booking' => 'Multi-branch Booking', 'recurring_appointments' => 'Recurring Appointments', 'booking_confirmation' => 'Booking Confirmation Notifications', 'priority_support' => 'Priority Support'],
        'service' => ['public_profiles' => 'Public Service Profiles', 'service_listings' => 'Service Listings', 'portfolio_gallery' => 'Portfolio Gallery', 'requests_quotations' => 'Requests & Quotations', 'reviews_ratings' => 'Reviews & Ratings', 'direct_whatsapp' => 'Direct WhatsApp', 'online_payments' => 'Online Payments', 'multi_provider' => 'Multi-provider Management', 'service_area_coverage' => 'Location / Service Area Coverage', 'driver_profiles' => 'Driver / Delivery Service Profiles', 'advanced_reports' => 'Advanced Reports', 'priority_support' => 'Priority Support', 'custom_domain' => 'Custom Domain'],
    ];
@endphp

<style>
    .oc-systabs { display: inline-flex; gap: 6px; background: #eef2f0; padding: 5px; border-radius: 12px; flex-wrap: wrap; }
    .oc-systab { border: 0; background: transparent; padding: 9px 20px; border-radius: 9px; font-weight: 600; font-size: 14px; color: #566; cursor: pointer; transition: .15s; }
    .oc-systab.active { background: #fff; color: #1f9d55; box-shadow: 0 4px 12px -6px rgba(20,40,30,.35); }
    .oc-sec { border: 1px solid #e6ece9; border-radius: 14px; padding: 18px 20px; margin-top: 22px; }
    .oc-sec__h { font-weight: 700; font-size: 15px; margin: 0 0 14px; display: flex; align-items: center; gap: 8px; }
    .oc-sec__h .tag { font-size: 10px; font-weight: 700; color: #b02a37; background: #fde8ea; border-radius: 20px; padding: 2px 8px; text-transform: uppercase; }
    .oc-count-wrap.hide-count { display: none; }
</style>

{{-- Extended usage limits (branch / whatsapp / team) --}}
<div class="oc-sec">
    <div class="oc-sec__h"><span id="oc_limits_title">Orders &amp; Stores Usage Limits</span> <span class="tag">new</span></div>
    <div class="row g-3">
        @foreach (['branch', 'whatsapp', 'team'] as $row)
            <div class="col-md-3">
                <label class="form-label" data-limlabel="{{ $row }}">{{ ucfirst($row) }} Limit</label>
                <select class="form-select oc-limtype" name="limits[{{ $row }}][type]" data-target="lim_{{ $row }}" data-count="limcount_{{ $row }}">
                    <option value="1" {{ (string) $limVal($row, 'type', '1') === '1' ? 'selected' : '' }}>{{ trans('labels.limited') }}</option>
                    <option value="2" {{ (string) $limVal($row, 'type') === '2' ? 'selected' : '' }}>{{ trans('labels.unlimited') }}</option>
                </select>
            </div>
            <div class="col-md-3 oc-count-wrap {{ (string) $limVal($row, 'type', '1') === '2' ? 'hide-count' : '' }}" id="lim_{{ $row }}">
                <label class="form-label" data-countlabel="{{ $row }}">Maximum {{ ucfirst($row) }}</label>
                <input type="text" class="form-control numbers_only" id="limcount_{{ $row }}" name="limits[{{ $row }}][count]" value="{{ $limVal($row, 'count') }}" data-countinput="{{ $row }}" placeholder="0">
            </div>
        @endforeach
        <div class="col-12"><small class="text-muted">{{ $isAr ? 'الأرقام تمثّل الحد الأقصى المسموح لكل خطة (وليس استخدام التاجر الحالي).' : 'These numbers are the maximum allowance per plan (not the vendor\'s current usage). WhatsApp Numbers stay separate from Team/Staff.' }}</small></div>
    </div>
</div>

{{-- Subscription Plan Offer --}}
<div class="oc-sec">
    <div class="oc-sec__h">{{ trans('labels.subscription_plan_offer') }} <span class="tag">new</span></div>
    <div class="row g-3">
        <div class="col-md-3">
            <label class="form-label d-block">{{ trans('labels.enable_offer') }}</label>
            <input type="checkbox" class="form-check-input" name="offer[enabled]" id="oc_offer_enable" value="1" {{ !empty($off['enabled']) ? 'checked' : '' }}>
        </div>
        <div class="col-md-9 oc-offer-fields row g-3" id="oc_offer_fields">
            <div class="col-md-4">
                <label class="form-label">{{ trans('labels.offer_type_2') }}</label>
                <select class="form-select" name="offer[type]" id="oc_offer_type">
                    <option value="percentage" {{ ($off['type'] ?? '') === 'percentage' ? 'selected' : '' }}>Percentage Discount</option>
                    <option value="fixed" {{ ($off['type'] ?? '') === 'fixed' ? 'selected' : '' }}>Fixed Offer Price (Special Price)</option>
                    <option value="free_duration" {{ ($off['type'] ?? '') === 'free_duration' ? 'selected' : '' }}>Extra Free Duration</option>
                    <option value="pay_x_get_y" {{ ($off['type'] ?? 'pay_x_get_y') === 'pay_x_get_y' ? 'selected' : '' }}>Pay X Months, Get Y Months Free</option>
                </select>
            </div>
            <div class="col-md-4 oc-offv" data-for="percentage"><label class="form-label">Discount %</label><input type="text" class="form-control numbers_only" name="offer[discount_percentage]" value="{{ $off['discount_percentage'] ?? '' }}"></div>
            <div class="col-md-4 oc-offv" data-for="fixed"><label class="form-label">Offer Price</label><input type="text" class="form-control numbers_only" name="offer[offer_amount]" value="{{ $off['offer_amount'] ?? '' }}"></div>
            <div class="col-md-4 oc-offv" data-for="free_duration"><label class="form-label">{{ trans('labels.free_days') }}</label><input type="text" class="form-control numbers_only" name="offer[free_duration]" value="{{ $off['free_duration'] ?? '' }}"></div>
            <div class="col-md-2 oc-offv" data-for="pay_x_get_y"><label class="form-label">Paid Months</label><input type="text" class="form-control numbers_only" name="offer[paid_months]" value="{{ $off['paid_months'] ?? '' }}"></div>
            <div class="col-md-2 oc-offv" data-for="pay_x_get_y"><label class="form-label">Free Months</label><input type="text" class="form-control numbers_only" name="offer[free_months]" value="{{ $off['free_months'] ?? '' }}"></div>
            <div class="col-md-3 oc-offv" data-for="percentage fixed"><label class="form-label">Discounted Billing Cycles</label><input type="text" class="form-control numbers_only" name="offer[cycles]" value="{{ $off['cycles'] ?? '' }}" placeholder="{{ trans('labels.blank_all_cycles') }}"></div>
            <div class="col-md-3"><label class="form-label">Offer Starts At</label><input type="datetime-local" class="form-control" name="offer[starts_at]" value="{{ $off['starts_at'] ?? '' }}"></div>
            <div class="col-md-3"><label class="form-label">Offer Ends At</label><input type="datetime-local" class="form-control" name="offer[ends_at]" value="{{ $off['ends_at'] ?? '' }}"></div>
            <div class="col-md-3"><label class="form-label">Applies To</label>
                <select class="form-select" name="offer[applies_to]">
                    <option value="new" {{ ($off['applies_to'] ?? 'new') === 'new' ? 'selected' : '' }}>New Subscribers Only</option>
                    <option value="all" {{ ($off['applies_to'] ?? '') === 'all' ? 'selected' : '' }}>All Subscribers</option>
                </select>
            </div>
            <div class="col-md-3"><label class="form-label">After Offer</label>
                <select class="form-select" name="offer[after_offer]"><option value="regular" selected>Return to Regular Price</option></select>
            </div>
            <div class="col-12"><small class="text-muted">{{ trans('labels.the_offer_auto_expires_after_the_end') }}</small></div>
        </div>
    </div>
</div>

{{-- Optional Add-on Pricing --}}
<div class="oc-sec">
    <div class="oc-sec__h" id="oc_addon_title">Optional Add-on Pricing <span class="tag">new</span></div>
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label"><span data-addonlabel="branch">Allow Additional Branches</span>
                <input type="checkbox" class="form-check-input ms-2 oc-addon-allow" name="addon[branch][allow]" value="1" data-price="addonprice_branch" {{ !empty($add['branch']['allow']) ? 'checked' : '' }}></label>
            <input type="text" class="form-control numbers_only oc-addon-price" id="addonprice_branch" name="addon[branch][price]" value="{{ $add['branch']['price'] ?? '' }}" placeholder="Price / extra / month">
        </div>
        <div class="col-md-4">
            <label class="form-label">Allow Extra WhatsApp Numbers
                <input type="checkbox" class="form-check-input ms-2 oc-addon-allow" name="addon[whatsapp][allow]" value="1" data-price="addonprice_whatsapp" {{ !empty($add['whatsapp']['allow']) ? 'checked' : '' }}></label>
            <input type="text" class="form-control numbers_only oc-addon-price" id="addonprice_whatsapp" name="addon[whatsapp][price]" value="{{ $add['whatsapp']['price'] ?? '' }}" placeholder="Price / extra number / month">
        </div>
        <div class="col-md-4 oc-addon-team" id="oc_addon_team">
            <label class="form-label"><span data-addonlabel="team">Allow Extra Staff / Providers</span>
                <input type="checkbox" class="form-check-input ms-2 oc-addon-allow" name="addon[team][allow]" value="1" data-price="addonprice_team" {{ !empty($add['team']['allow']) ? 'checked' : '' }}></label>
            <input type="text" class="form-control numbers_only oc-addon-price" id="addonprice_team" name="addon[team][price]" value="{{ $add['team']['price'] ?? '' }}" placeholder="Price / extra / month">
        </div>
        <div class="col-12"><small class="text-muted">{{ trans('labels.total_base_plan_selected_add_ons_all') }}</small></div>
    </div>
</div>

{{-- Plan Display & Status --}}
<div class="oc-sec">
    <div class="oc-sec__h">{{ trans('labels.plan_display_status') }} <span class="tag">new</span></div>
    <div class="row g-3">
        <div class="col-md-3"><label class="form-label">Display Order</label>
            <select class="form-select" name="display_order">
                @for ($i = 1; $i <= 3; $i++)
                    <option value="{{ $i }}" {{ (string) old('display_order', $plan->reorder_id ?? 1) === (string) $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </div>
        <div class="col-md-3"><label class="form-label d-block">Recommended Plan</label>
            <input type="checkbox" class="form-check-input" name="recommended" value="1" {{ (int) old('recommended', $plan->recommended ?? 2) === 1 ? 'checked' : '' }}></div>
        <div class="col-md-3"><label class="form-label">Plan Status</label>
            <select class="form-select" name="plan_status">
                <option value="1" {{ (int) old('plan_status', $plan->is_available ?? 1) === 1 ? 'selected' : '' }}>Active</option>
                <option value="2" {{ (int) old('plan_status', $plan->is_available ?? 1) === 2 ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="col-md-3"><label class="form-label">Plan Visibility</label>
            <select class="form-select" name="visibility">
                <option value="1" {{ (int) old('visibility', $plan->visibility ?? 1) === 1 ? 'selected' : '' }}>Show</option>
                <option value="2" {{ (int) old('visibility', $plan->visibility ?? 1) === 2 ? 'selected' : '' }}>Hide</option>
            </select>
        </div>
    </div>
</div>

{{-- System-specific features --}}
<div class="oc-sec">
    <div class="oc-sec__h" id="oc_feat_title">Orders &amp; Stores Features</div>
    @foreach ($F as $skey => $feats)
        <div class="row g-2 oc-featgroup" data-system="{{ $skey }}" style="{{ $sys === $skey ? '' : 'display:none' }}">
            @foreach ($feats as $fk => $flabel)
                <div class="col-md-3 col-6">
                    <input class="form-check-input" type="checkbox" id="xf_{{ $skey }}_{{ $fk }}" name="xfeat_{{ $skey }}[]" value="{{ $fk }}"
                        {{ !empty($xf[$fk]) && $sys === $skey ? 'checked' : '' }}>
                    <label class="form-check-label" for="xf_{{ $skey }}_{{ $fk }}">{{ $flabel }}</label>
                </div>
            @endforeach
        </div>
    @endforeach
</div>

<script>
    (function () {
        var LBL = @json($L);
        var sysInput = document.getElementById('planSystem');

        function applySystem(sys) {
            // A plan row with an empty or unrecognised system used to fall through every lookup:
            // titles read "undefined", and ocFilterPlanThemes() matched no theme card, so it
            // disabled and unchecked all of them. Disabled inputs are not posted, which made the
            // plan save with no themes (and, before the controller guard, crash on implode).
            if (['orders', 'booking', 'service'].indexOf(sys) === -1) { sys = 'orders'; }
            if (sysInput) sysInput.value = sys;
            document.querySelectorAll('.oc-systab').forEach(function (b) { b.classList.toggle('active', b.dataset.system === sys); });
            var m = LBL[sys] || LBL.orders;
            // Dropdown label = "<Entity> Limit"; count label + placeholder = "Maximum <Entity>"
            document.querySelectorAll('[data-limlabel]').forEach(function (el) { el.textContent = m[el.dataset.limlabel] + ' Limit'; });
            document.querySelectorAll('[data-countlabel]').forEach(function (el) { el.textContent = 'Maximum ' + m[el.dataset.countlabel]; });
            document.querySelectorAll('[data-countinput]').forEach(function (el) { el.placeholder = 'Maximum ' + m[el.dataset.countinput]; });
            document.querySelectorAll('[data-addonlabel]').forEach(function (el) {
                el.textContent = (el.dataset.addonlabel === 'branch') ? ('Allow Additional ' + m.addonbranch) : ('Allow Extra ' + m.addonteam);
            });
            var teamAddon = document.getElementById('oc_addon_team');
            if (teamAddon) teamAddon.style.display = m.addonteam ? '' : 'none';
            var titleMap = { orders: 'Orders & Stores', booking: 'Booking', service: 'Service Marketplace' };
            var lt = document.getElementById('oc_limits_title'); if (lt) lt.textContent = titleMap[sys] + ' Usage Limits';
            var ft = document.getElementById('oc_feat_title'); if (ft) ft.textContent = titleMap[sys] + ' Features';
            var at = document.getElementById('oc_addon_title'); if (at) at.firstChild.textContent = titleMap[sys] + ' Optional Add-on Pricing ';
            document.querySelectorAll('.oc-featgroup').forEach(function (g) {
                var on = g.dataset.system === sys;
                g.style.display = on ? '' : 'none';
                g.querySelectorAll('input').forEach(function (i) { i.disabled = !on; });
            });
            // system-filtered themes (checkboxes rendered in add/edit with data-theme-system)
            if (window.ocFilterPlanThemes) window.ocFilterPlanThemes(sys);
        }

        document.querySelectorAll('.oc-systab').forEach(function (b) {
            b.addEventListener('click', function () { applySystem(b.dataset.system); });
        });

        // Limited/Unlimited: show+enable+require count on Limited; hide+disable+clear on Unlimited.
        document.querySelectorAll('.oc-limtype').forEach(function (sel) {
            function apply() {
                var wrap = document.getElementById(sel.dataset.target);
                var input = document.getElementById(sel.dataset.count);
                var unlimited = sel.value === '2';
                if (wrap) wrap.classList.toggle('hide-count', unlimited);
                if (input) { input.disabled = unlimited; if (unlimited) input.value = ''; input.required = !unlimited; }
            }
            sel.addEventListener('change', apply); apply();
        });

        // Add-on: price enabled only when Allow is checked; required + cleared accordingly.
        document.querySelectorAll('.oc-addon-allow').forEach(function (cb) {
            function apply() {
                var price = document.getElementById(cb.dataset.price);
                if (!price) return;
                price.disabled = !cb.checked; price.required = cb.checked;
                if (!cb.checked) price.value = '';
            }
            cb.addEventListener('change', apply); apply();
        });

        // Offer: enable toggle + type-based fields (a field is shown if its data-for contains the type)
        var offEnable = document.getElementById('oc_offer_enable'), offFields = document.getElementById('oc_offer_fields'), offType = document.getElementById('oc_offer_type');
        function offToggle() { if (offFields) offFields.style.display = (offEnable && offEnable.checked) ? '' : 'none'; }
        function offTypeToggle() {
            var t = offType ? offType.value : '';
            document.querySelectorAll('.oc-offv').forEach(function (el) {
                el.style.display = el.dataset.for.split(' ').indexOf(t) !== -1 ? '' : 'none';
            });
        }
        if (offEnable) offEnable.addEventListener('change', offToggle);
        if (offType) offType.addEventListener('change', offTypeToggle);
        offToggle(); offTypeToggle();

        applySystem(sysInput ? sysInput.value : 'orders');
    })();
</script>
