@extends('admin.layout.auth_default')
@section('content')

    @include('admin.auth._auth_styles')

    <div class="ocl-auth">
        <div class="ocl-auth__card">
            @if (session('reset_link_sent'))
                {{-- Same message whether or not the email has an account. --}}
                <div class="ocl-auth__icon"><i class="fa-solid fa-envelope-circle-check"></i></div>
                <h1 class="ocl-auth__title">{{ trans('labels.check_your_email') }}</h1>
                <p class="ocl-auth__sub">
                    {{ trans('labels.reset_link_sent_to') }} <strong>{{ session('reset_link_sent') }}</strong>.
                    {{ trans('labels.reset_link_sent_hint') }}
                </p>
                <a class="ocl-submit d-flex align-items-center justify-content-center text-decoration-none"
                    href="{{ URL::to('/admin') }}">{{ trans('labels.back_to_login') }}</a>
                <a class="ocl-auth__back" href="{{ URL::to('admin/forgot_password') }}">
                    {{ trans('labels.reset_use_another_email') }}
                </a>
            @else
                <div class="ocl-auth__icon"><i class="fa-solid fa-key"></i></div>
                <h1 class="ocl-auth__title">{{ trans('labels.forgot_password') }}</h1>
                <p class="ocl-auth__sub">{{ trans('labels.forgot_password_hint') }}</p>

                <form method="POST" action="{{ URL::to('admin/send_password') }}">
                    @csrf
                    <div class="ocl-fld">
                        <label for="email">{{ trans('labels.email') }} <span class="req">*</span></label>
                        <input type="email" class="ocl-inp" name="email" id="email" value="{{ old('email') }}"
                            placeholder="{{ trans('labels.email') }}" required autofocus>
                        @error('email')<span class="ocl-err">{{ $message }}</span>@enderror
                    </div>
                    <button class="ocl-submit"
                        @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif>
                        {{ trans('labels.send_reset_link') }}
                    </button>
                </form>

                <a class="ocl-auth__back" href="{{ URL::to('/admin') }}">
                    <i class="fa-solid fa-arrow-left"></i> {{ trans('labels.remember_password') }} {{ trans('labels.login') }}
                </a>
            @endif
        </div>
    </div>
@endsection
