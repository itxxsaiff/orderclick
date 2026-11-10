@extends('admin.layout.default')
@section('content')
    @php
        $fieldLabel = [
            'name' => trans('labels.tr_f_name'), 'item_name' => trans('labels.tr_f_name'),
            'category' => trans('labels.tr_f_category'), 'description' => trans('labels.tr_f_description'),
            'question' => trans('labels.tr_f_question'), 'answer' => trans('labels.tr_f_answer'),
        ];
        $long = ['description', 'answer'];
    @endphp
    <style>
        .octr-row { border: 1px solid var(--bs-border-color, #e3e7e2); border-radius: 10px; margin-bottom: 8px; background: var(--bs-body-bg, #fff); }
        .octr-row > summary { list-style: none; cursor: pointer; display: flex; align-items: center; gap: 10px; padding: 10px 14px; }
        .octr-row > summary::-webkit-details-marker { display: none; }
        .octr-row > summary .octr-name { font-weight: 600; flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; unicode-bidi: plaintext; }
        .octr-row[open] > summary { border-bottom: 1px solid var(--bs-border-color, #e3e7e2); }
        .octr-body { padding: 14px; }
        .octr-langs { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 14px; }
        .octr-lang h6 { font-size: 13px; font-weight: 700; margin-bottom: 8px; }
        .octr-lang label { font-size: 12px; color: #6b7280; margin: 6px 0 3px; display: block; }
        .octr-lang .form-control { font-size: 13.5px; unicode-bidi: plaintext; }
        .octr-badge { font-size: 11.5px; font-weight: 600; border-radius: 99px; padding: 3px 9px; white-space: nowrap; }
        .octr-ok { background: rgba(31,157,85,.1); color: #1f9d55; }
        .octr-wait { background: rgba(245,158,11,.12); color: #b45309; }
        .octr-src { background: #f1f3f5; color: #555; }
    </style>

    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-12">
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.tr_title') }}</h5>
            <p class="fs-7 text-muted mb-1">{{ trans('labels.tr_sub') }}</p>
            @include('admin.layout.breadcrumb')
        </div>
    </div>

    <div class="card border-0 box-shadow mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <div class="flex-grow-1">
                    <div class="fw-semibold mb-1">{{ implode(' · ', $langs) }}</div>
                    <div class="fs-7 text-muted" id="octrStatus">
                        @if ($pending['total'] > 0)
                            {{ trans('labels.tr_pending', ['count' => $pending['total']]) }}
                        @else
                            {{ trans('labels.tr_all_done') }}
                        @endif
                    </div>
                </div>
                @if ($aiReady)
                    <button type="button" class="btn btn-primary" id="octrRun" @if ($pending['total'] == 0) disabled @endif>
                        <i class="fa-solid fa-wand-magic-sparkles"></i> {{ trans('labels.tr_translate_all') }}
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="octrAgain">{{ trans('labels.tr_translate_again') }}</button>
                @else
                    <span class="text-danger fs-7">{{ trans('messages.assistant_unavailable') }}</span>
                @endif
            </div>
            <div class="progress mt-3 d-none" id="octrBar" style="height:8px"><div class="progress-bar bg-success" style="width:0%"></div></div>
            <p class="fs-7 text-muted mt-3 mb-0">{{ trans('labels.tr_hint') }}</p>
        </div>
    </div>

    @forelse ($groups as $type => $records)
        <div class="card border-0 box-shadow mb-3">
            <div class="card-body">
                <h6 class="fw-bold mb-3">{{ trans('labels.tr_t_' . $type) }} <span class="text-muted fw-normal">({{ $records->count() }})</span></h6>
                @foreach ($records as $r)
                    @php
                        $src = $r->translatableSource();
                        $current = $r->translationIsCurrent();
                        $t = $current ? (array) $r->translations : [];
                        $srcLang = null; // every language is editable (the original stays on its own page)
                        $first = reset($src);
                    @endphp
                    <details class="octr-row">
                        <summary>
                            <span class="octr-name">{{ \Illuminate\Support\Str::limit(strip_tags($first), 90) }}</span>
                            @if ($srcLang)<span class="octr-badge octr-src">{{ strtoupper($srcLang) }}</span>@endif
                            @if ($current && empty($t['_missing']))
                                <span class="octr-badge octr-ok">{{ trans('labels.tr_done') }}</span>
                            @else
                                <span class="octr-badge octr-wait">{{ trans('labels.tr_waiting') }}</span>
                            @endif
                        </summary>
                        <form class="octr-body" data-octr-save>
                            <input type="hidden" name="type" value="{{ $type }}">
                            <input type="hidden" name="id" value="{{ $r->id }}">
                            <div class="octr-langs">
                                @foreach ($langs as $code => $langName)
                                    <div class="octr-lang">
                                        <h6>{{ $langName }}@if ($code === $srcLang) <span class="octr-badge octr-src">{{ trans('labels.tr_original') }}</span>@endif</h6>
                                        @foreach ($src as $f => $orig)
                                            <label>{{ $fieldLabel[$f] ?? $f }}</label>
                                            @if ($code === $srcLang)
                                                <div class="form-control bg-light" style="white-space:pre-line;min-height:38px">{{ $orig }}</div>
                                            @elseif (in_array($f, $long, true))
                                                <textarea class="form-control" rows="4" name="t[{{ $code }}][{{ $f }}]" dir="auto" placeholder="{{ \Illuminate\Support\Str::limit($orig, 120) }}">{{ $t[$code][$f] ?? '' }}</textarea>
                                            @else
                                                <input class="form-control" name="t[{{ $code }}][{{ $f }}]" dir="auto" value="{{ $t[$code][$f] ?? '' }}" placeholder="{{ $orig }}">
                                            @endif
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-3"><button type="submit" class="btn btn-success btn-sm">{{ trans('labels.save') }}</button></div>
                        </form>
                    </details>
                @endforeach
            </div>
        </div>
    @empty
        <div class="card border-0 box-shadow"><div class="card-body text-muted">{{ trans('labels.tr_nothing') }}</div></div>
    @endforelse
@endsection

@section('scripts')
    <script>
        (function () {
            var token = @js(csrf_token());
            var urls = { step: @js(URL::to('admin/translations/step')), reset: @js(URL::to('admin/translations/reset')), save: @js(URL::to('admin/translations/save')) };
            var txt = { left: @js(trans('labels.tr_left')), again: @js(trans('labels.tr_again_confirm')), failed: @js(trans('messages.translate_failed')) };
            function post(url, body) {
                return fetch(url, { method: 'POST', body: body || new FormData(),
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function (r) { return r.json(); });
            }
            function toast(ok, msg) { if (window.toastr) { ok ? toastr.success(msg) : toastr.error(msg); } else { alert(msg); } }

            var run = document.getElementById('octrRun'), again = document.getElementById('octrAgain');
            var bar = document.getElementById('octrBar'), status = document.getElementById('octrStatus');
            var start = 0, stuck = 0, last = null;

            function busy(on) { [run, again].forEach(function (b) { if (b) b.disabled = on; }); bar.classList.toggle('d-none', !on); }
            function loop() {
                post(urls.step).then(function (res) {
                    var left = res.remaining || 0;
                    if (!start) start = Math.max(left + (res.done || 0), 1);
                    bar.firstElementChild.style.width = Math.round(100 * (start - left) / start) + '%';
                    status.textContent = txt.left.replace(':count', left);
                    if (!res.success) { busy(false); toast(false, res.error || txt.failed); return; }
                    stuck = (left === last) ? stuck + 1 : 0; last = left;
                    if (left > 0 && stuck < 3) { loop(); } else { location.reload(); }
                }).catch(function () { busy(false); toast(false, txt.failed); });
            }
            if (run) run.addEventListener('click', function () { busy(true); start = 0; loop(); });
            if (again) again.addEventListener('click', function () {
                if (!confirm(txt.again)) return;
                busy(true);
                post(urls.reset).then(function () { start = 0; loop(); });
            });

            document.querySelectorAll('[data-octr-save]').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    var btn = form.querySelector('button[type=submit]'); btn.disabled = true;
                    post(urls.save, new FormData(form)).then(function (res) {
                        btn.disabled = false; toast(!!res.success, res.message || txt.failed);
                    }).catch(function () { btn.disabled = false; toast(false, txt.failed); });
                });
            });
        })();
    </script>
@endsection
