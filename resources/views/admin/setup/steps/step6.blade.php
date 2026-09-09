{{-- STEP 6 — Review & Submit --}}
<div class="card border-0 box-shadow">
    <div class="card-body">
        <div class="d-flex align-items-center gap-2 mb-4">
            <span class="step-badge">{{ trans('labels.step') }} 6</span>
            <h6 class="mb-0">{{ trans('labels.review_submit') }}</h6>
        </div>

        @php $allDone = collect($progress)->except(6)->every(fn($s) => $s['done']); @endphp

        <div class="row g-3">
            @foreach (\App\Helpers\Onboarding::STEPS as $n => $meta)
                @continue($n === 6)
                <div class="col-12">
                    <div class="oc-collapsed d-flex align-items-start justify-content-between gap-3">
                        <div class="d-flex gap-3">
                            <i class="fa-solid {{ $progress[$n]['done'] ? 'fa-circle-check text-success' : 'fa-circle-exclamation text-warning' }} mt-1"></i>
                            <div>
                                <div class="fw-600 fs-7 color-changer">{{ $meta['full'] }}</div>
                                @if ($progress[$n]['done'])
                                    <div class="fs-7 text-success">{{ trans('labels.complete') }}</div>
                                @else
                                    <div class="fs-7 text-muted">{{ trans('labels.missing') }}: {{ implode(', ', $progress[$n]['missing']) }}</div>
                                @endif
                            </div>
                        </div>
                        <a href="{{ URL::to('admin/setup') }}?step={{ $n }}" class="btn btn-sm btn-light rounded-start-5 rounded-end-5">
                            {{ trans('labels.edit') }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($vendor->document_submitted_date)
            <div class="alert alert-success mt-3 mb-0">
                <i class="fa-solid fa-circle-check mx-1"></i>
                {{ trans('messages.setup_submitted') }}
                <div class="fs-7 mt-1">
                    {{ trans('labels.submitted_on') }}: {{ date('d M Y H:i', strtotime($vendor->document_submitted_date)) }}
                    · {{ trans('labels.verification') }}:
                    <span class="badge {{ \App\Helpers\Vendor360::verificationStatus($vendor)['class'] }}">
                        {{ \App\Helpers\Vendor360::verificationStatus($vendor)['text'] }}</span>
                </div>
            </div>
        @else
            <div class="oc-warn mt-3">{{ trans('messages.review_submit_notice') }}</div>
        @endif
    </div>
</div>

@if (!$vendor->document_submitted_date)
    <div class="d-flex justify-content-end mt-3">
        <form method="POST" action="{{ URL::to('admin/setup/submit') }}">
            @csrf
            <button class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ $allDone ? '' : 'disabled' }}"
                @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif>
                <i class="fa-solid fa-paper-plane mx-1"></i>{{ trans('labels.submit_for_verification') }}
            </button>
        </form>
    </div>
@endif
