{{-- STEP 3 — Account Authorization --}}
<div class="card border-0 box-shadow">
    <div class="card-body">
        <div class="d-flex align-items-center gap-2 mb-4">
            <span class="step-badge">{{ trans('labels.step') }} 3</span>
            <h6 class="mb-0">{{ trans('labels.account_authorization') }}</h6>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label">{{ trans('labels.authorized_person') }}<span class="text-danger"> *</span></label>
                <input type="text" class="form-control" name="authorized_person"
                    value="{{ old('authorized_person', $vendor->authorized_person) }}" placeholder="{{ trans('labels.full_name') }}">
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">{{ trans('labels.position_title') }}<span class="text-danger"> *</span></label>
                <input type="text" class="form-control" name="authorized_position"
                    value="{{ old('authorized_position', $vendor->authorized_position) }}"
                    placeholder="{{ trans('labels.position_placeholder') }}">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">{{ trans('labels.id_passport_number') }}<span class="text-danger"> *</span></label>
                <input type="text" class="form-control" name="authorized_id_number"
                    value="{{ old('authorized_id_number', $vendor->authorized_id_number) }}"
                    placeholder="{{ trans('labels.id_passport_number') }}">
            </div>
            <div class="col-12 col-md-4">
                @include('admin.setup._upload', ['type' => 'id_document'])
            </div>
            <div class="col-12 col-md-4">
                @include('admin.setup._upload', ['type' => 'authorization_letter'])
            </div>
        </div>

        <div class="oc-warn mt-3 d-flex gap-2">
            <i class="fa-solid fa-circle-info mt-1"></i>
            <span>{{ trans('messages.authorization_letter_notice') }}</span>
        </div>
    </div>
</div>
