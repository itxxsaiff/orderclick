@extends('admin.layout.auth_default')
@section('content')

    <style>
        .ocl-auth { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 28px 16px;
            background: radial-gradient(120% 120% at 90% -10%, rgba(31,157,85,.10), transparent 55%), #f5f7f4; }
        .ocl-auth__card { width: 100%; max-width: 440px; background: #fff; border: 1px solid #e5e9e2; border-radius: 18px;
            box-shadow: 0 24px 60px -32px rgba(20,32,24,.4); padding: 34px 32px; }
        .ocl-auth__title { font-size: 26px; font-weight: 700; color: #17201a; margin: 0; }
        .ocl-auth__sub { color: #6b7669; margin: 6px 0 26px; font-size: 14.5px; }
        .ocl-auth__sub a { color: #1f9d55; font-weight: 600; text-decoration: none; }
        .ocl-fld { margin-bottom: 16px; }
        .ocl-fld label { display: block; font-size: 13.5px; font-weight: 600; color: #39443c; margin-bottom: 6px; }
        .ocl-fld label .req { color: #d64545; }
        .ocl-inp { width: 100%; height: 48px; border: 1px solid #d9e0d4; border-radius: 10px; padding: 0 14px;
            font-size: 15px; color: #17201a; background: #fff; transition: .15s; }
        .ocl-inp:focus { outline: none; border-color: #1f9d55; box-shadow: 0 0 0 3px rgba(31,157,85,.12); }
        .ocl-pw { position: relative; }
        .ocl-pw .ocl-inp { padding-inline-end: 46px; }
        .ocl-eye { position: absolute; inset-inline-end: 2px; top: 0; height: 48px; width: 44px; border: 0;
            background: none; color: #8a978d; cursor: pointer; display: flex; align-items: center;
            justify-content: center; border-radius: 10px; }
        .ocl-eye:hover { color: #1f9d55; }
        .ocl-forgot { text-align: end; margin: -4px 0 18px; }
        .ocl-forgot a { font-size: 13px; font-weight: 600; color: #6b7669; text-decoration: none; }
        .ocl-forgot a:hover { color: #1f9d55; }
        .ocl-submit { width: 100%; height: 50px; border: 0; border-radius: 11px; background: #1f9d55; color: #fff;
            font-weight: 650; font-size: 15.5px; cursor: pointer; transition: .15s; }
        .ocl-submit:hover { background: #198a49; }
        .ocl-err { color: #d64545; font-size: 12.5px; margin-top: 5px; display: block; }
        .ocl-demo { margin-top: 22px; }
        .ocl-demo table { width: 100%; font-size: 12.5px; border-collapse: collapse; }
        .ocl-demo td { border: 1px solid #e5e9e2; padding: 6px 8px; }
        .ocl-demo .themes { display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; margin-top: 10px; }
        .ocl-demo .themes a { font-size: 11px; text-align: center; padding: 7px 4px; border-radius: 8px; background: #17201a; color: #fff; text-decoration: none; }
    </style>

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
