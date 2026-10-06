@extends('admin.layout.default')
@section('content')
    @php
        $styles = ['modern', 'luxury', 'bold', 'warm', 'minimal', 'playful', 'natural', 'professional'];
        $opt = \App\Services\StoreDesign::OPTIONS;
        $allSections = \App\Services\StoreDesign::SECTIONS[$flow];
        $ordered = array_values(array_unique(array_merge($design['sections'], $allSections)));
        $previewUrl = $storeUrl . '?design_preview=1';
    @endphp
    <style>
        .ocd-style { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; }
        .ocd-style label { border: 1px solid var(--bs-border-color, #e3e7e2); border-radius: 10px; padding: 9px 6px; text-align: center;
            font-size: 13px; font-weight: 600; cursor: pointer; transition: .15s; margin: 0; }
        .ocd-style input { display: none; }
        .ocd-style input:checked + label { border-color: #1f9d55; background: rgba(31,157,85,.08); color: #1f9d55; }
        .ocd-preview { position: sticky; top: 90px; }
        .ocd-frame-wrap { background: #eef1ee; border-radius: 14px; padding: 12px; display: flex; justify-content: center; }
        .ocd-frame { width: 100%; height: 72vh; border: 0; border-radius: 10px; background: #fff; box-shadow: 0 10px 30px -18px rgba(0,0,0,.45); transition: width .25s; }
        .ocd-frame.mobile { width: 390px; }
        .ocd-busy { position: fixed; inset: 0; background: rgba(255,255,255,.82); z-index: 3000; display: none; align-items: center; justify-content: center; flex-direction: column; gap: 14px; text-align: center; padding: 20px; }
        .ocd-busy.show { display: flex; }
        .ocd-busy .spinner-border { width: 3rem; height: 3rem; color: #1f9d55; }
        .ocd-swatch { display: flex; gap: 10px; flex-wrap: wrap; }
        .ocd-swatch label { font-size: 12px; display: flex; flex-direction: column; gap: 4px; }
        .ocd-swatch input[type=color] { width: 54px; height: 36px; border: 1px solid #ddd; border-radius: 8px; padding: 2px; background: #fff; }
        .ocd-sec { display: flex; align-items: center; gap: 8px; padding: 7px 10px; border: 1px solid var(--bs-border-color, #e3e7e2); border-radius: 9px; margin-bottom: 6px; font-size: 13.5px; }
        .ocd-sec .ocd-move { margin-inline-start: auto; display: flex; gap: 4px; }
        .ocd-sec .ocd-move button { border: 1px solid #ddd; background: #fff; border-radius: 6px; width: 28px; height: 26px; line-height: 1; }
    </style>

    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-12">
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.design_title') }}</h5>
            <p class="fs-7 text-muted mb-1">{{ trans('labels.design_sub') }}</p>
            @include('admin.layout.breadcrumb')
        </div>
    </div>

    @if (!$aiReady)
        <div class="alert alert-warning">{{ trans('messages.design_ai_missing') }}</div>
    @endif

    <div class="row">
        <div class="col-12 col-xl-5 mb-3">
            {{-- 1. Describe --}}
            <div class="card border-0 box-shadow mb-3">
                <div class="card-body">
                    <h6 class="fw-700 mb-1"><i class="fa-solid fa-wand-magic-sparkles text-success"></i> {{ trans('labels.design_step_describe') }}</h6>
                    <p class="fs-7 text-muted">{{ trans('labels.design_tip_products') }}</p>
                    <form id="ocdGenerate">
                        <label class="form-label fw-600">{{ trans('labels.design_style') }}</label>
                        <div class="ocd-style mb-3">
                            @foreach ($styles as $s)
                                <div>
                                    <input type="radio" name="style" id="ocdStyle{{ $s }}" value="{{ $s }}" {{ ($brief['style'] ?? '') === $s ? 'checked' : '' }}>
                                    <label for="ocdStyle{{ $s }}">{{ trans('labels.design_style_' . $s) }}</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-600">{{ trans('labels.design_colors') }}</label>
                            <input type="text" name="colors" class="form-control" maxlength="120" value="{{ $brief['colors'] ?? '' }}" placeholder="{{ trans('labels.design_colors_ph') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-600">{{ trans('labels.design_mode') }}</label>
                            <select name="mode" class="form-select">
                                @foreach (['auto', 'light', 'dark'] as $m)
                                    <option value="{{ $m }}" {{ ($brief['mode'] ?? 'auto') === $m ? 'selected' : '' }}>{{ trans('labels.design_mode_' . $m) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-600">{{ trans('labels.design_audience') }}</label>
                            <input type="text" name="audience" class="form-control" maxlength="160" value="{{ $brief['audience'] ?? '' }}" placeholder="{{ trans('labels.design_audience_ph') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-600">{{ trans('labels.design_describe') }}</label>
                            <textarea name="describe" class="form-control" rows="3" maxlength="800" placeholder="{{ trans('labels.design_describe_ph') }}">{{ $brief['describe'] ?? '' }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100" {{ $aiReady ? '' : 'disabled' }}>
                            <i class="fa-solid fa-wand-magic-sparkles"></i> {{ trans('labels.design_generate') }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- 2. Refine --}}
            <div class="card border-0 box-shadow mb-3">
                <div class="card-body">
                    <h6 class="fw-700 mb-2"><i class="fa-solid fa-comment-dots text-success"></i> {{ trans('labels.design_refine') }}</h6>
                    <form id="ocdRefine" class="d-flex gap-2">
                        <input type="text" name="refine" class="form-control" maxlength="400" required placeholder="{{ trans('labels.design_refine_ph') }}">
                        <button type="submit" class="btn btn-outline-success text-nowrap" {{ $aiReady ? '' : 'disabled' }}>{{ trans('labels.design_refine_btn') }}</button>
                    </form>
                </div>
            </div>

            {{-- 3. Fine-tune --}}
            <div class="card border-0 box-shadow mb-3">
                <div class="card-body">
                    <h6 class="fw-700 mb-3"><i class="fa-solid fa-sliders text-success"></i> {{ trans('labels.design_finetune') }}</h6>
                    <form id="ocdTweak">
                        <div class="ocd-swatch mb-3">
                            @foreach (['brand', 'accent', 'bg'] as $c)
                                <label>{{ trans('labels.design_color_' . $c) }}<input type="color" name="palette[{{ $c }}]" value="{{ $design['palette'][$c] }}"></label>
                            @endforeach
                        </div>
                        <div class="row g-2 mb-2">
                            @foreach (['heading', 'body'] as $f)
                                <div class="col-6">
                                    <label class="form-label fs-7 fw-600 mb-1">{{ trans('labels.design_font_' . $f) }}</label>
                                    <select name="fonts[{{ $f }}]" class="form-select form-select-sm">
                                        @foreach (\App\Services\StoreDesign::FONTS as $font)
                                            <option value="{{ $font }}" {{ $design['fonts'][$f] === $font ? 'selected' : '' }}>{{ $font }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach
                            @foreach ($opt as $key => $values)
                                <div class="col-6">
                                    <label class="form-label fs-7 fw-600 mb-1">{{ trans('labels.design_opt_' . $key) }}</label>
                                    <select name="{{ $key }}" class="form-select form-select-sm">
                                        @foreach ($values as $v)
                                            <option value="{{ $v }}" {{ $design[$key] === $v ? 'selected' : '' }}>{{ trans('labels.design_val_' . $v) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach
                        </div>
                        <label class="form-label fs-7 fw-600 mt-2">{{ trans('labels.design_sections') }}</label>
                        <div id="ocdSections">
                            @foreach ($ordered as $sec)
                                <div class="ocd-sec" data-sec="{{ $sec }}">
                                    <input type="checkbox" class="form-check-input m-0" {{ in_array($sec, $design['sections'], true) ? 'checked' : '' }}>
                                    <span>{{ trans('labels.design_sec_' . $sec) }}</span>
                                    <span class="ocd-move">
                                        <button type="button" data-move="-1" aria-label="Up">↑</button>
                                        <button type="button" data-move="1" aria-label="Down">↓</button>
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Preview --}}
        <div class="col-12 col-xl-7 mb-3">
            <div class="card border-0 box-shadow ocd-preview">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                        <div>
                            <h6 class="fw-700 mb-0">{{ trans('labels.design_preview') }}</h6>
                            <span class="badge {{ $hasDraft ? 'bg-warning' : ($published ? 'bg-success' : 'bg-secondary') }}">
                                {{ $hasDraft ? trans('labels.design_status_draft') : ($published ? trans('labels.design_status_live') : trans('labels.design_status_default')) }}
                            </span>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-secondary active" data-device="desktop"><i class="fa-solid fa-display"></i></button>
                                <button type="button" class="btn btn-outline-secondary" data-device="mobile"><i class="fa-solid fa-mobile-screen"></i></button>
                            </div>
                            <a href="{{ $previewUrl }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-up-right-from-square"></i> {{ trans('labels.design_open_new') }}</a>
                            @if ($hasDraft)
                                <form method="POST" action="{{ URL::to('admin/design/discard') }}">@csrf
                                    <button class="btn btn-sm btn-outline-danger">{{ trans('labels.design_discard') }}</button></form>
                                <form method="POST" action="{{ URL::to('admin/design/publish') }}">@csrf
                                    <button class="btn btn-sm btn-success"><i class="fa-solid fa-rocket"></i> {{ trans('labels.design_publish') }}</button></form>
                            @endif
                        </div>
                    </div>
                    <div class="ocd-frame-wrap">
                        <iframe id="ocdFrame" class="ocd-frame" src="{{ $previewUrl }}" title="{{ trans('labels.design_preview') }}"></iframe>
                    </div>
                    @if ($published || $hasDraft)
                        <form method="POST" action="{{ URL::to('admin/design/reset') }}" class="text-end mt-2"
                            onsubmit="return confirm(@js(trans('labels.design_reset_confirm')))">@csrf
                            <button class="btn btn-link btn-sm text-muted">{{ trans('labels.design_reset') }}</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="ocd-busy" id="ocdBusy">
        <div class="spinner-border" role="status"></div>
        <div class="fw-600">{{ trans('labels.design_generating') }}</div>
    </div>
@endsection

@section('scripts')
    <script>
        (function () {
            var token = @js(csrf_token());
            var frame = document.getElementById('ocdFrame');
            var busy = document.getElementById('ocdBusy');
            var previewUrl = @js($previewUrl);

            function reloadPreview() { frame.src = previewUrl + '&t=' + Date.now(); }
            function post(url, data) {
                return fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: data
                }).then(function (r) { return r.json(); });
            }
            function fail(msg) { if (window.toastr) toastr.error(msg); else alert(msg); }

            // Generate / refine with the AI: a new draft, then reload to show it everywhere.
            function aiRequest(form) {
                busy.classList.add('show');
                post(@js(URL::to('admin/design/generate')), new FormData(form)).then(function (res) {
                    if (res.success) { location.reload(); return; }
                    busy.classList.remove('show');
                    fail(res.message || @js(trans('messages.design_generate_failed')));
                }).catch(function () { busy.classList.remove('show'); fail(@js(trans('messages.design_generate_failed'))); });
            }
            document.getElementById('ocdGenerate').addEventListener('submit', function (e) { e.preventDefault(); aiRequest(this); });
            document.getElementById('ocdRefine').addEventListener('submit', function (e) { e.preventDefault(); aiRequest(this); });

            // Fine-tune: save the draft on every change and refresh only the preview.
            var tweakForm = document.getElementById('ocdTweak');
            var timer = null;
            function saveTweak() {
                clearTimeout(timer);
                timer = setTimeout(function () {
                    var data = new FormData(tweakForm);
                    document.querySelectorAll('#ocdSections .ocd-sec').forEach(function (row) {
                        if (row.querySelector('input').checked) data.append('sections[]', row.getAttribute('data-sec'));
                    });
                    post(@js(URL::to('admin/design/tweak')), data).then(function (res) {
                        if (res.success) reloadPreview(); else fail(@js(trans('messages.wrong')));
                    });
                }, 450);
            }
            tweakForm.addEventListener('change', saveTweak);
            document.getElementById('ocdSections').addEventListener('click', function (e) {
                var btn = e.target.closest('[data-move]');
                if (!btn) return;
                var row = btn.closest('.ocd-sec');
                if (btn.getAttribute('data-move') === '-1' && row.previousElementSibling) row.parentNode.insertBefore(row, row.previousElementSibling);
                if (btn.getAttribute('data-move') === '1' && row.nextElementSibling) row.parentNode.insertBefore(row.nextElementSibling, row);
                saveTweak();
            });

            // Desktop / mobile preview width.
            document.querySelectorAll('[data-device]').forEach(function (b) {
                b.addEventListener('click', function () {
                    document.querySelectorAll('[data-device]').forEach(function (x) { x.classList.remove('active'); });
                    b.classList.add('active');
                    frame.classList.toggle('mobile', b.getAttribute('data-device') === 'mobile');
                });
            });
        })();
    </script>
@endsection
