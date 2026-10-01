@extends('admin.layout.auth_default')
@section('content')

    @include('admin.auth._auth_styles')
    <style>
        .ocl-inp[readonly] { background: #f5f7f4; color: #56635a; }
    </style>

    <div class="ocl-auth">
        <div class="ocl-auth__card">
            @if (!$valid)
                <div class="ocl-auth__icon" style="background:rgba(214,69,69,.10);color:#d64545;"><i class="fa-solid fa-link-slash"></i></div>
                <h1 class="ocl-auth__title">{{ trans('labels.reset_link_expired_title') }}</h1>
                <p class="ocl-auth__sub">{{ trans('messages.reset_link_invalid') }}</p>
                <a class="ocl-submit d-flex align-items-center justify-content-center text-decoration-none"
                    href="{{ URL::to('admin/forgot_password') }}">{{ trans('labels.send_reset_link') }}</a>
                <a class="ocl-auth__back" href="{{ URL::to('/admin') }}">
                    <i class="fa-solid fa-arrow-left"></i> {{ trans('labels.back_to_login') }}
                </a>
            @else
                <div class="ocl-auth__icon"><i class="fa-solid fa-lock"></i></div>
                <h1 class="ocl-auth__title">{{ trans('labels.set_new_password') }}</h1>
                <p class="ocl-auth__sub">{{ trans('labels.set_new_password_hint') }}</p>

                <form method="POST" action="{{ URL::to('admin/reset-password') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <div class="ocl-fld">
                        <label for="email">{{ trans('labels.email') }}</label>
                        <input type="email" class="ocl-inp" name="email" id="email" value="{{ $email }}" readonly>
                    </div>
                    <div class="ocl-fld">
                        <label for="password">{{ trans('labels.new_password') }} <span class="req">*</span></label>
                        <div class="ocl-pw">
                            <input type="password" class="ocl-inp" name="password" id="password" minlength="6"
                                placeholder="{{ trans('labels.new_password') }}" autocomplete="new-password" required autofocus>
                            <button type="button" class="ocl-eye" data-target="password" aria-label="{{ trans('labels.show_password') }}">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                        @error('password')<span class="ocl-err">{{ $message }}</span>@enderror
                    </div>
                    <div class="ocl-fld">
                        <label for="password_confirmation">{{ trans('labels.confirm_password_2') }} <span class="req">*</span></label>
                        <div class="ocl-pw">
                            <input type="password" class="ocl-inp" name="password_confirmation" id="password_confirmation" minlength="6"
                                placeholder="{{ trans('labels.confirm_password_2') }}" autocomplete="new-password" required>
                            <button type="button" class="ocl-eye" data-target="password_confirmation" aria-label="{{ trans('labels.show_password') }}">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                        <span class="ocl-err d-none" id="oclMismatch">{{ trans('messages.new_confirm_password_inccorect') }}</span>
                    </div>
                    <button class="ocl-submit" type="submit">{{ trans('labels.update_password') }}</button>
                </form>

                <a class="ocl-auth__back" href="{{ URL::to('/admin') }}">
                    <i class="fa-solid fa-arrow-left"></i> {{ trans('labels.back_to_login') }}
                </a>
            @endif
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        (function () {
            document.querySelectorAll('.ocl-eye').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var input = document.getElementById(btn.getAttribute('data-target'));
                    var show = input.type === 'password';
                    input.type = show ? 'text' : 'password';
                    btn.querySelector('i').className = show ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
                });
            });
            // Catch a mismatch before submitting; the server checks it again.
            var form = document.querySelector('form[action$="reset-password"]');
            if (!form) return;
            form.addEventListener('submit', function (e) {
                var same = form.password.value === form.password_confirmation.value;
                document.getElementById('oclMismatch').classList.toggle('d-none', same);
                if (!same) e.preventDefault();
            });
        })();
    </script>
@endsection
