{{-- STEP 2 — Verification & Licensing --}}
<div class="card border-0 box-shadow">
    <div class="card-body">
        <div class="d-flex align-items-center gap-2 mb-4">
            <span class="step-badge">{{ trans('labels.step') }} 2</span>
            <h6 class="mb-0">{{ trans('labels.verification_licensing') }}</h6>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-4">
                @include('admin.setup._upload', ['type' => 'commercial_registration'])
            </div>
            <div class="col-12 col-md-4">
                @include('admin.setup._upload', ['type' => 'business_license'])
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">{{ trans('labels.registration_number') }}<span class="text-danger"> *</span></label>
                <input type="text" class="form-control mb-3" name="cr_number" value="{{ old('cr_number', $vendor->cr_number) }}"
                    placeholder="{{ trans('labels.registration_number') }}">

                <label class="form-label">{{ trans('labels.license_number') }}<span class="text-danger"> *</span></label>
                <input type="text" class="form-control mb-3" name="license_number" value="{{ old('license_number', $vendor->license_number) }}"
                    placeholder="{{ trans('labels.license_number') }}">

                <label class="form-label">{{ trans('labels.license_expiry_date') }}<span class="text-danger"> *</span></label>
                <input type="date" class="form-control" name="license_expiry_date"
                    value="{{ old('license_expiry_date', $vendor->license_expiry_date) }}">
            </div>

            @if (isset($documents['sector_license']))
                <div class="col-12 col-md-6">
                    @include('admin.setup._upload', ['type' => 'sector_license'])
                </div>
            @endif

            <div class="col-12">
                @include('admin.setup._upload', ['type' => 'additional'])
            </div>
        </div>
    </div>
</div>
