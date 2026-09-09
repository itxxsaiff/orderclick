{{-- STEP 1 — Business Information --}}
<div class="card border-0 box-shadow">
    <div class="card-body">
        <div class="d-flex align-items-center gap-2 mb-4">
            <span class="step-badge">{{ trans('labels.step') }} 1</span>
            <h6 class="mb-0">{{ trans('labels.business_information') }}</h6>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-4">
                <label class="form-label">{{ trans('labels.business_name') }}<span class="text-danger"> *</span></label>
                <input type="text" class="form-control" name="business_name"
                    value="{{ old('business_name', $vendor->trade_name ?: optional($settings)->website_title) }}"
                    placeholder="{{ trans('labels.business_name') }}">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">{{ trans('labels.business_type') }}<span class="text-danger"> *</span></label>
                <select class="form-select" name="activity_id" id="ocSetupActivity">
                    <option value="">{{ trans('labels.select') }}</option>
                    @foreach ($activities as $a)
                        <option value="{{ $a->id }}" {{ (string) old('activity_id', $vendor->activity_id) === (string) $a->id ? 'selected' : '' }}>
                            {{ $a->display_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">{{ trans('labels.specialization') }}</label>
                <select class="form-select" name="specialization_id" id="ocSetupSpecialization">
                    <option value="">{{ trans('labels.select') }}</option>
                    @foreach (\App\Helpers\Systems::specializations($vendor->activity_id) as $sp)
                        <option value="{{ $sp->id }}" {{ (string) $vendor->specialization_id === (string) $sp->id ? 'selected' : '' }}>
                            {{ $sp->display_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label">{{ trans('labels.category') }}<span class="text-danger"> *</span></label>
                <select class="form-select" name="store_id">
                    <option value="">{{ trans('labels.select') }}</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}" {{ (string) $vendor->store_id === (string) $c->id ? 'selected' : '' }}>
                            {{ $c->name }}</option>
                    @endforeach
                </select>
                <small class="text-muted">{{ trans('messages.category_auto_hint') }}</small>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">{{ trans('labels.mobile') }}<span class="text-danger"> *</span></label>
                <div class="input-group">
                    <select name="country_code" class="form-select" style="max-width:130px">
                        @foreach (helper::countries() as $c)
                            <option value="{{ $c['dial'] }}" {{ $vendor->country_code === $c['dial'] ? 'selected' : '' }}>
                                {{ $c['flag'] }} {{ $c['dial'] }}</option>
                        @endforeach
                    </select>
                    <input type="tel" class="form-control @error('mobile') is-invalid @enderror"
                        name="mobile" value="{{ old('mobile', $vendor->mobile) }}">
                </div>
                @error('mobile')<span class="text-danger fs-7">{{ $message }}</span>@enderror
            </div>
            <div class="col-12 col-md-8">
                <label class="form-label">{{ trans('labels.personlized_link') }}</label>
                <div class="input-group">
                    <span class="input-group-text bg-light">{{ URL::to('/') }}/</span>
                    <input type="text" class="form-control" name="slug" id="ocSetupSlug"
                        value="{{ old('slug', $vendor->slug) }}" placeholder="my-store">
                </div>
                <small class="text-muted">{{ trans('messages.slug_from_business_name') }}</small>
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label">{{ trans('labels.email') }}<span class="text-danger"> *</span></label>
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                    name="email" value="{{ old('email', $vendor->email) }}" placeholder="example@yourbusiness.com">
                @error('email')<span class="text-danger fs-7">{{ $message }}</span>@enderror
            </div>
        </div>

        {{-- GPS is required for bookings and navigation, so the picker is part of this step. --}}
        <div class="mt-4 p-3" style="background:#f2fbf6;border-radius:12px;">
            <div class="fw-600 color-changer mb-1">{{ trans('labels.business_location_gps') }}<span class="text-danger"> *</span></div>
            <p class="fs-7 text-muted">{{ trans('messages.gps_used_for') }}</p>
            @include('admin.partials.map_picker', [
                'lat' => old('latitude', $branch->latitude),
                'lng' => old('longitude', $branch->longitude),
                'branch' => $branch,
            ])
        </div>
    </div>
</div>

<script>
    // The public link follows the BUSINESS name until the merchant edits it by hand.
    (function () {
        var biz = document.querySelector('input[name="business_name"]');
        var slug = document.getElementById('ocSetupSlug');
        if (!biz || !slug) return;
        var touched = slug.value !== '';
        slug.addEventListener('input', function () { touched = true; });
        biz.addEventListener('input', function () {
            if (touched) return;
            slug.value = this.value.toLowerCase().trim()
                .replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');
        });
    })();

    // Specialization follows the chosen business type.
    (function () {
        var act = document.getElementById('ocSetupActivity');
        var spec = document.getElementById('ocSetupSpecialization');
        if (!act || !spec) return;
        act.addEventListener('change', function () {
            spec.innerHTML = '<option value="">{{ trans('labels.select') }}</option>';
            if (!this.value) return;
            fetch('{{ URL::to('admin/setup/specializations') }}?activity_id=' + encodeURIComponent(this.value),
                { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (r) { return r.json(); })
                .then(function (d) {
                    (d.specializations || []).forEach(function (s) {
                        var o = document.createElement('option');
                        o.value = s.id; o.textContent = s.name; spec.appendChild(o);
                    });
                });
        });
    })();
</script>
