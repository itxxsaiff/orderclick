@extends('admin.layout.auth_default')
@section('content')

    @include('admin.auth._auth_styles')

    <div class="ocl-auth">
        <div class="ocl-auth__card">
            <h1 class="ocl-auth__title">{{ trans('labels.welcome_back') }}</h1>
            @if (helper::appdata('')->vendor_register == 1)
                <p class="ocl-auth__sub">{{ trans('labels.dont_have_account') }}
                    <a href="{{ URL::to('admin/register') }}">{{ trans('labels.register') }}</a>
                </p>
            @else
                <p class="ocl-auth__sub">{{ trans('labels.sign_in_to_continue_to_your_dashboard') }}</p>
            @endif

            <form method="POST" action="{{ URL::to('admin/checklogin-normal') }}">
                @csrf
                <div class="ocl-fld">
                    <label>{{ trans('labels.email') }} <span class="req">*</span></label>
                    <input type="email" class="ocl-inp" name="email" id="email" placeholder="{{ trans('labels.email') }}" required>
                    @error('email')<span class="ocl-err">{{ $message }}</span>@enderror
                </div>
                <div class="ocl-fld">
                    <label>{{ trans('labels.password') }} <span class="req">*</span></label>
                    <div class="ocl-pw">
                        <input type="password" class="ocl-inp" name="password" id="password" placeholder="{{ trans('labels.password') }}" required>
                        <button type="button" class="ocl-eye" id="oclEye" aria-label="{{ trans('labels.show_password') }}">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                    @error('password')<span class="ocl-err">{{ $message }}</span>@enderror
                </div>
                <div class="ocl-forgot">
                    <a href="{{ URL::to('admin/forgot_password?redirect=admin') }}">
                        <i class="fa-solid fa-lock-keyhole"></i> {{ trans('labels.forgot_password') }}?
                    </a>
                </div>
                <button class="ocl-submit" type="submit">{{ trans('labels.login') }}</button>
            </form>

            @if (env('Environment') == 'sendbox')
                <div class="ocl-demo">
                    <table>
                        <tbody>
                            <tr><td>Admin<br>admin@gmail.com</td><td>123456</td><td><button class="btn btn-info btn-sm" onclick="fillData('admin@gmail.com','123456')">{{ trans('labels.copy') }}</button></td></tr>
                            <tr><td>Vendor<br>theme1@yopmail.com</td><td>123456</td><td><button class="btn btn-info btn-sm" onclick="fillData('theme1@yopmail.com','123456')">{{ trans('labels.copy') }}</button></td></tr>
                        </tbody>
                    </table>
                    <div class="themes">
                        <a href="{{ URL::to('/theme-1') }}" target="_blank">Chicken Shop</a>
                        <a href="{{ URL::to('/theme-2') }}" target="_blank">The Pizza</a>
                        <a href="{{ URL::to('/theme-3') }}" target="_blank">Burger Shop</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        // Show / hide the password, same behaviour as the registration wizard.
        (function () {
            var btn = document.getElementById('oclEye');
            var input = document.getElementById('password');
            if (!btn || !input) return;
            btn.addEventListener('click', function () {
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                btn.querySelector('i').className = show ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
                btn.setAttribute('aria-label', show ? '{{ trans('labels.hide_password') }}'
                                                    : '{{ trans('labels.show_password') }}');
                input.focus();
            });
        })();
        function fillData(email, password) {
            "use strict";
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
@endsection
