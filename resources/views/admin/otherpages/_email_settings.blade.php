{{-- Platform SMTP. Every email the platform sends (password resets, subscriptions, store order emails) uses it. --}}
@php $mailSettings = App\Models\Settings::where('vendor_id', 1)->first(); @endphp
<div id="email_settings" class="hidechild">
    <div class="col-12">
        <div class="card border-0 overflow-hidden box-shadow">
            <div class="card-header bg-secondary py-3 d-flex align-items-center text-white">
                <i class="fa-solid fa-envelope fs-5"></i>
                <h5 class="px-2">{{ trans('labels.email_settings') }}</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-light border small mb-4">
                    <i class="fa-solid fa-circle-info text-secondary"></i>
                    {{ trans('labels.smtp_platform_note') }}<br>
                    <i class="fa-brands fa-google text-secondary"></i> {{ trans('labels.smtp_gmail_hint') }}
                </div>
                <form action="{{ URL::to('admin/settings/email-update') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ trans('labels.mail_host') }}<span class="text-danger"> * </span></label>
                            <input type="text" class="form-control" name="mail_host" placeholder="smtp.gmail.com"
                                value="{{ old('mail_host', @$mailSettings->mail_host) }}" required>
                            @error('mail_host')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group col-md-3">
                            <label class="form-label">{{ trans('labels.mail_port') }}<span class="text-danger"> * </span></label>
                            <input type="number" class="form-control" name="mail_port" placeholder="587"
                                value="{{ old('mail_port', @$mailSettings->mail_port) }}" required>
                            @error('mail_port')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group col-md-3">
                            <label class="form-label">{{ trans('labels.mail_encryption') }}</label>
                            @php $enc = old('mail_encryption', @$mailSettings->mail_encryption); @endphp
                            <select class="form-select" name="mail_encryption">
                                <option value="tls" {{ $enc == 'tls' ? 'selected' : '' }}>TLS (587)</option>
                                <option value="ssl" {{ $enc == 'ssl' ? 'selected' : '' }}>SSL (465)</option>
                                <option value="" {{ $enc == '' ? 'selected' : '' }}>—</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ trans('labels.mail_username') }}<span class="text-danger"> * </span></label>
                            <input type="text" class="form-control" name="mail_username" placeholder="you@gmail.com"
                                value="{{ old('mail_username', @$mailSettings->mail_username) }}" autocomplete="off" required>
                            @error('mail_username')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ trans('labels.mail_password') }}</label>
                            {{-- Never print the saved password into the page; blank keeps it. --}}
                            <input type="password" class="form-control" name="mail_password" value=""
                                placeholder="{{ trans('labels.smtp_password_keep') }}" autocomplete="new-password">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ trans('labels.mail_fromaddress') }}<span class="text-danger"> * </span></label>
                            <input type="email" class="form-control" name="mail_fromaddress" placeholder="you@gmail.com"
                                value="{{ old('mail_fromaddress', @$mailSettings->mail_fromaddress) }}" required>
                            @error('mail_fromaddress')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">{{ trans('labels.mail_fromname') }}<span class="text-danger"> * </span></label>
                            <input type="text" class="form-control" name="mail_fromname" placeholder="Order Click"
                                value="{{ old('mail_fromname', @$mailSettings->mail_fromname) }}" required>
                            @error('mail_fromname')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group m-0 mt-2 d-flex gap-2 justify-content-end">
                            <button class="btn btn-secondary px-4 rounded-start-5 rounded-end-5"
                                @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif>{{ trans('labels.save') }}</button>
                        </div>
                    </div>
                </form>

                <hr class="my-4">
                <form action="{{ URL::to('admin/settings/email-test') }}" method="POST">
                    @csrf
                    <label class="form-label">{{ trans('labels.send_test_email') }}</label>
                    <div class="d-flex flex-wrap gap-2">
                        <input type="email" class="form-control flex-grow-1 w-auto" name="test_email" required
                            value="{{ old('test_email', Auth::user()->email) }}" placeholder="{{ trans('labels.email') }}">
                        <button class="btn btn-outline-secondary px-4 rounded-start-5 rounded-end-5" type="submit">
                            <i class="fa-solid fa-paper-plane"></i> {{ trans('labels.send_test_email') }}
                        </button>
                    </div>
                    @error('test_email')<span class="text-danger">{{ $message }}</span>@enderror
                    <small class="text-muted d-block mt-2">{{ trans('labels.smtp_test_hint') }}</small>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    // Reopen a settings tab named in the URL hash (e.g. after saving: admin/settings#email_settings).
    window.addEventListener('load', function () {
        var tab = location.hash ? document.querySelector('.basicinfo[data_attribute="' + location.hash.slice(1) + '"]') : null;
        if (tab) tab.click();
    });
</script>
