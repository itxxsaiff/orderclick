{{-- STEP 4 — Agreement --}}
<div class="card border-0 box-shadow">
    <div class="card-body">
        <div class="d-flex align-items-center gap-2 mb-4">
            <span class="step-badge">{{ trans('labels.step') }} 4</span>
            <h6 class="mb-0">{{ trans('labels.agreement') }}</h6>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <div class="oc-upload h-100">
                    <div class="fw-600 fs-7 color-changer">{{ trans('labels.service_agreement') }}</div>
                    <div class="fs-7 text-muted mb-3">{{ trans('messages.download_agreement_hint') }}</div>
                    <a href="{{ URL::to('admin/setup/agreement') }}" target="_blank"
                        class="btn-up d-inline-flex align-items-center justify-content-center gap-2 text-decoration-none">
                        <i class="fa-solid fa-download"></i>{{ trans('labels.download_agreement') }}
                    </a>
                </div>
            </div>
            <div class="col-12 col-md-6">
                @include('admin.setup._upload', ['type' => 'signed_agreement'])
            </div>
        </div>

        <div class="oc-warn mt-3 d-flex gap-2">
            <i class="fa-solid fa-shield-halved mt-1"></i>
            <div>
                <div class="fw-600">{{ trans('labels.why_do_we_need_this') }}</div>
                <span>{{ trans('messages.agreement_reason') }}</span>
            </div>
        </div>
    </div>
</div>
