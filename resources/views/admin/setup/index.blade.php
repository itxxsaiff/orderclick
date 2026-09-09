@extends('admin.layout.default')
@section('content')
    @php
        $ar = app()->getLocale() === 'ar';
        $steps = \App\Helpers\Onboarding::STEPS;
        $vendorId = $vendor->id;
        $doc = fn($type) => ($uploaded[$type] ?? collect())->first();
        $docsFor = fn($n) => collect($documents)->filter(fn($d) => $d['step'] === $n);
        $stepUrl = fn($n) => URL::to('admin/setup') . '?step=' . $n;
    @endphp

    <style>
        /* Scoped to the setup wizard — reuses the admin green, adds no global rules. */
        .oc-wiz .oc-stepbar { display: flex; align-items: flex-start; gap: 0; overflow-x: auto; padding-bottom: 4px; }
        .oc-wiz .oc-stepbar .st { flex: 1 1 0; min-width: 96px; text-align: center; position: relative; }
        .oc-wiz .oc-stepbar .st .dot { width: 38px; height: 38px; border-radius: 50%; margin: 0 auto 8px;
            display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 15px;
            background: #eef2ec; color: #8a978d; border: 2px solid transparent; }
        .oc-wiz .oc-stepbar .st.done .dot { background: #d4ede0; color: #137a40; }
        .oc-wiz .oc-stepbar .st.active .dot { background: #1f9d55; color: #fff; }
        .oc-wiz .oc-stepbar .st .lbl { font-size: 12.5px; font-weight: 600; color: #8a978d; line-height: 1.25; display: block; padding: 0 4px; }
        .oc-wiz .oc-stepbar .st.active .lbl, .oc-wiz .oc-stepbar .st.done .lbl { color: #17201a; }
        .oc-wiz .oc-stepbar .st::after { content: ''; position: absolute; top: 19px; left: 50%; width: 100%; height: 2px; background: #eef2ec; z-index: -1; }
        .oc-wiz .oc-stepbar .st:last-child::after { display: none; }
        .oc-wiz .oc-stepbar { position: relative; z-index: 0; }
        .oc-wiz .step-badge { background: #1f9d55; color: #fff; font-size: 11.5px; font-weight: 700; letter-spacing: .04em;
            padding: 5px 11px; border-radius: 7px; text-transform: uppercase; }
        .oc-wiz .oc-upload { border: 1px solid #d9e0d4; border-radius: 12px; padding: 14px; height: 100%; }
        .oc-wiz .oc-upload .btn-up { border: 1px solid #1f9d55; color: #137a40; background: #f2fbf6; font-weight: 600;
            border-radius: 9px; padding: 8px 14px; font-size: 13.5px; width: 100%; }
        .oc-wiz .oc-upload input[type=file] { display: none; }
        .oc-wiz .oc-filename { font-size: 12.5px; color: #137a40; font-weight: 600; word-break: break-all; }
        .oc-wiz .oc-ring { width: 132px; height: 132px; border-radius: 50%; margin: 0 auto; display: flex;
            align-items: center; justify-content: center; }
        .oc-wiz .oc-ring .inner { width: 104px; height: 104px; border-radius: 50%; background: #fff; display: flex;
            align-items: center; justify-content: center; font-size: 26px; font-weight: 700; color: #17201a; }
        .oc-wiz .oc-side-step { display: flex; align-items: center; gap: 10px; padding: 9px 0; }
        .oc-wiz .oc-side-step .n { width: 26px; height: 26px; border-radius: 50%; background: #eef2ec; color: #8a978d;
            font-size: 12.5px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex: 0 0 auto; }
        .oc-wiz .oc-side-step.done .n { background: #d4ede0; color: #137a40; }
        .oc-wiz .oc-side-step.active .n { background: #1f9d55; color: #fff; }
        .oc-wiz .oc-note { background: #f2fbf6; border-radius: 12px; padding: 16px; }
        .oc-wiz .oc-warn { background: #fdf6e7; border-radius: 12px; padding: 14px; font-size: 13px; color: #6b5a2a; }
        .oc-wiz .oc-collapsed { border: 1px solid #e5e9e2; border-radius: 12px; padding: 14px 16px; }
    </style>

    <div class="oc-wiz">
        <div class="row justify-content-between align-items-center mb-3">
            <div class="col-12">
                <h5 class="pages-title color-changer fs-2">
                    {{ $ar ? 'إعداد حسابك' : 'Set up your ' . \App\Helpers\Systems::label($system) . ' business' }}
                </h5>
                <p class="fs-7 text-muted mb-1">{{ trans('messages.setup_wizard_subtitle') }}</p>
                @include('admin.layout.breadcrumb')
            </div>
        </div>

        {{-- Step bar --}}
        <div class="col-12 mb-3">
            <div class="card border-0 box-shadow">
                <div class="card-body">
                    <div class="oc-stepbar">
                        @foreach ($steps as $n => $meta)
                            <a href="{{ $stepUrl($n) }}" class="st text-decoration-none {{ $n === $step ? 'active' : '' }} {{ $progress[$n]['done'] ? 'done' : '' }}">
                                <span class="dot">
                                    @if ($progress[$n]['done'] && $n !== $step)<i class="fa-solid fa-check"></i>@else{{ $n }}@endif
                                </span>
                                <span class="lbl">{{ $meta['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- ============ MAIN COLUMN ============ --}}
            <div class="col-12 col-xl-8 mb-4">
                <form method="POST" action="{{ URL::to('admin/setup/save-' . $step) }}" enctype="multipart/form-data">
                    @csrf
                    @include('admin.setup.steps.step' . $step)

                    @if ($step < 6)
                        <div class="d-flex flex-wrap gap-2 justify-content-between mt-3">
                            <button type="submit" name="action" value="draft" class="btn btn-light px-4 rounded-start-5 rounded-end-5">
                                <i class="fa-regular fa-floppy-disk mx-1"></i>{{ trans('labels.save_as_draft') }}
                            </button>
                            <button type="submit" name="action" value="continue" class="btn btn-secondary px-4 rounded-start-5 rounded-end-5"
                                @if (env('Environment') == 'sendbox') formaction="javascript:void(0)" onclick="myFunction(); return false;" @endif>
                                {{ trans('labels.save_and_continue') }} <i class="fa-solid fa-arrow-right mx-1"></i>
                            </button>
                        </div>
                    @endif
                </form>
            </div>

            {{-- ============ SIDE COLUMN ============ --}}
            <div class="col-12 col-xl-4 mb-4">
                {{-- Setup progress --}}
                <div class="card border-0 box-shadow mb-3">
                    <div class="card-body text-center">
                        <h6 class="mb-3 text-start">{{ trans('labels.setup_progress') }}</h6>
                        <div class="oc-ring" style="background: conic-gradient(#1f9d55 {{ $percent * 3.6 }}deg, #eef2ec 0deg);">
                            <div class="inner">{{ $percent }}%</div>
                        </div>
                        <p class="fs-7 text-muted mt-3 mb-3">{{ trans('messages.setup_progress_hint') }}</p>
                        <div class="text-start">
                            @foreach ($steps as $n => $meta)
                                <a href="{{ $stepUrl($n) }}"
                                    class="oc-side-step text-decoration-none color-changer {{ $n === $step ? 'active' : '' }} {{ $progress[$n]['done'] ? 'done' : '' }}">
                                    <span class="n">@if ($progress[$n]['done'])<i class="fa-solid fa-check"></i>@else{{ $n }}@endif</span>
                                    <span class="fs-7 fw-500">{{ $meta['full'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Why verification --}}
                <div class="card border-0 box-shadow mb-3">
                    <div class="card-body oc-note">
                        <h6 class="mb-3">{{ trans('labels.why_verification') }}</h6>
                        @foreach ([
                            'fa-shield-halved' => trans('messages.why_verification_1'),
                            'fa-lock'          => trans('messages.why_verification_2'),
                            'fa-scale-balanced'=> trans('messages.why_verification_3'),
                            'fa-user-shield'   => trans('messages.why_verification_4'),
                        ] as $icon => $text)
                            <div class="d-flex gap-2 mb-2">
                                <i class="fa-solid {{ $icon }} text-success mt-1"></i>
                                <span class="fs-7 color-changer">{{ $text }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Support --}}
                <div class="card border-0 box-shadow">
                    <div class="card-body">
                        <h6 class="mb-2">{{ trans('labels.need_help') }}</h6>
                        <p class="fs-7 text-muted">{{ trans('messages.support_available') }}</p>
                        <a href="{{ URL::to('/') }}#contact" class="btn btn-light rounded-start-5 rounded-end-5 w-100">
                            <i class="fa-solid fa-headset mx-1"></i>{{ trans('labels.contact_support') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Show the chosen file name next to each upload button.
        document.querySelectorAll('.oc-wiz input[type=file]').forEach(function (input) {
            input.addEventListener('change', function () {
                var target = document.getElementById(this.dataset.nameTarget);
                if (!target) return;
                target.textContent = this.files.length
                    ? (this.files.length > 1 ? this.files.length + ' files selected' : this.files[0].name)
                    : '';
            });
        });
    </script>
@endsection
