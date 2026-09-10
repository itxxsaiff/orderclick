@extends('admin.layout.default')
@section('content')
    @php $ar = app()->getLocale() === 'ar'; @endphp

    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-12">
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.whatsapp_integration') }}</h5>
            <p class="fs-7 text-muted mb-1">{{ trans('messages.wa_settings_sub') }}</p>
            @include('admin.layout.breadcrumb')
        </div>
    </div>

    <div class="row">
        {{-- These two values are generated here and pasted into Meta. --}}
        <div class="col-12 mb-3">
            <div class="card border-0 box-shadow">
                <div class="card-body">
                    <h6 class="mb-3">{{ trans('labels.meta_webhook_details') }}</h6>
                    <div class="alert alert-success fs-7">{{ trans('messages.wa_give_meta_these') }}</div>

                    <div class="row g-3">
                        <div class="col-12 col-lg-6">
                            <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.callback_url') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control bg-light" readonly id="ocWaUrl" value="{{ $settings->callbackUrl() }}">
                                <button class="btn btn-light" type="button" onclick="ocCopy('ocWaUrl', this)">
                                    <i class="fa-regular fa-copy"></i></button>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.verify_token') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control bg-light" readonly id="ocWaToken" value="{{ $settings->verify_token }}">
                                <button class="btn btn-light" type="button" onclick="ocCopy('ocWaToken', this)">
                                    <i class="fa-regular fa-copy"></i></button>
                                <a href="{{ URL::to('admin/whatsapp/regenerate-token') }}" class="btn btn-light"
                                    tooltip="{{ trans('labels.regenerate') }}"
                                    onclick="return confirm('{{ trans('messages.wa_regenerate_confirm') }}')">
                                    <i class="fa-solid fa-rotate"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-8 mb-3">
            <div class="card border-0 box-shadow h-100">
                <div class="card-body">
                    <h6 class="mb-3">{{ trans('labels.connection') }}</h6>
                    <form method="POST" action="{{ URL::to('admin/whatsapp/settings') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">{{ trans('labels.whatsapp_number') }}</label>
                                <input type="text" class="form-control" name="display_number"
                                    value="{{ old('display_number', $settings->display_number) }}" placeholder="+973 3307 0341">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">{{ trans('labels.phone_number_id') }}<span class="text-danger"> *</span></label>
                                <input type="text" class="form-control" name="phone_number_id"
                                    value="{{ old('phone_number_id', $settings->phone_number_id) }}" placeholder="1224986210707917">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">{{ trans('labels.waba_id') }}</label>
                                <input type="text" class="form-control" name="waba_id"
                                    value="{{ old('waba_id', $settings->waba_id) }}" placeholder="1365929632371697">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">{{ trans('labels.api_version') }}</label>
                                <input type="text" class="form-control" name="api_version"
                                    value="{{ old('api_version', $settings->api_version) }}" placeholder="v21.0">
                            </div>

                            {{-- Secrets are stored encrypted and never echoed back to the browser. --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label">{{ trans('labels.access_token') }}</label>
                                <input type="password" class="form-control" name="access_token" autocomplete="new-password"
                                    placeholder="{{ $settings->access_token ? trans('labels.saved_leave_blank') : 'EAAT…' }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">{{ trans('labels.app_secret') }}</label>
                                <input type="password" class="form-control" name="app_secret" autocomplete="new-password"
                                    placeholder="{{ $settings->app_secret ? trans('labels.saved_leave_blank') : '' }}">
                                @if ($settings->app_secret)
                                    <div class="form-check mt-1">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="clear_app_secret" id="clear_app_secret">
                                        <label class="form-check-label text-muted fs-7" for="clear_app_secret">
                                            {{ trans('labels.remove_app_secret') }}
                                        </label>
                                    </div>
                                @endif
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">{{ trans('labels.status') }}</label>
                                <select name="is_active" class="form-select">
                                    <option value="1" {{ $settings->is_active == 1 ? 'selected' : '' }}>{{ trans('labels.active') }}</option>
                                    <option value="2" {{ $settings->is_active == 2 ? 'selected' : '' }}>{{ trans('labels.inactive') }}</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">{{ trans('labels.ai_auto_reply') }}</label>
                                <select name="ai_enabled" class="form-select">
                                    <option value="1" {{ $settings->ai_enabled == 1 ? 'selected' : '' }}>{{ trans('labels.active') }}</option>
                                    <option value="2" {{ $settings->ai_enabled == 2 ? 'selected' : '' }}>{{ trans('labels.inactive') }}</option>
                                </select>
                                <small class="text-muted">{{ trans('messages.wa_ai_toggle_hint') }}</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ trans('labels.ai_instructions') }}</label>
                                <textarea name="ai_instructions" rows="3" class="form-control"
                                    placeholder="{{ trans('messages.wa_ai_instructions_hint') }}">{{ old('ai_instructions', $settings->ai_instructions) }}</textarea>
                            </div>

                            <div class="col-12 d-flex gap-2 justify-content-end">
                                <button class="btn btn-secondary px-4 rounded-start-5 rounded-end-5"
                                    @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif>
                                    {{ trans('labels.save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4 mb-3">
            <div class="card border-0 box-shadow">
                <div class="card-body">
                    <h6 class="mb-2">{{ trans('labels.status') }}</h6>
                    @php $ocAi = \App\Services\AiAssistant::enabled(); @endphp
                    <div class="fs-7">
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">{{ trans('labels.connection') }}</span>
                            <span class="badge {{ $settings->isReady() ? 'bg-success' : 'bg-warning' }}">
                                {{ $settings->isReady() ? trans('labels.active') : trans('labels.incomplete') }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">{{ trans('labels.ai_auto_reply') }}</span>
                            <span class="badge {{ $ocAi && $settings->ai_enabled == 1 ? 'bg-success' : 'bg-warning' }}">
                                {{ $ocAi ? ($settings->ai_enabled == 1 ? trans('labels.active') : trans('labels.inactive')) : trans('labels.not_set') }}</span>
                        </div>
                    </div>
                    @unless ($ocAi)
                        <div class="alert alert-warning fs-7 mt-2 mb-0">{{ trans('messages.wa_ai_key_missing') }}</div>
                    @endunless
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function ocCopy(id, btn) {
            var el = document.getElementById(id);
            el.select(); el.setSelectionRange(0, 99999);
            var done = function () {
                var i = btn.querySelector('i'), old = i.className;
                i.className = 'fa-solid fa-check text-success';
                setTimeout(function () { i.className = old; }, 1500);
            };
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(el.value).then(done).catch(function () { document.execCommand('copy'); done(); });
            } else { document.execCommand('copy'); done(); }
        }
    </script>
@endsection
