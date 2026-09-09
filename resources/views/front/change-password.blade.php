@extends('front.theme.default')
@section('content')
    <!-- breadcrumb start -->
    <section class="breadcrumb-sec bg-change-mode">
        <div class="container">
            <nav>
                <ol class="breadcrumb d-flex m-0 text-capitalize">
                    <li class="breadcrumb-item">
                        <a href="{{ URL::to(@$storeinfo->slug) }}" class="text-dark color-changer">
                            {{ trans('labels.home') }}
                        </a>
                    </li>
                    <li
                        class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-item-right' : 'breadcrumb-item-left' }}">
                        {{ trans('labels.change_password') }}
                    </li>
                </ol>
            </nav>
        </div>
    </section>
    <!-- breadcrumb end -->
    <!-- Change Password section end -->
    <section class="mt-0 py-sm-5 py-4">
        <div class="container">
            <div class="row gx-sm-3 gx-0">
                @include('front.theme.user_sidebar')
                <div class="col-md-12 col-lg-9">
                    <div class="card rounded">
                        <div class="card-body py-4">
                            <h2 class="page-title mb-2 px-sm-2">{{ trans('labels.change_password') }}</h2>
                            <p class="page-subtitle px-sm-2 mb-4 line-limit-2">{{ trans('labels.change_password_desc') }}
                            </p>
                            <form action="{{ URL::to($storeinfo->slug . '/change_password/') }}" method="POST">
                                @csrf
                                <div class="row gx-sm-3 gx-0">
                                    <div class="col-md-12 mb-4">
                                        <label for="Name" class="form-label">{{ trans('labels.current_password') }}
                                            <span class="text-danger"> * </span></label>
                                        <input type="password" class="form-control input-h" name="current_password"
                                            id="validationDefault" value=""
                                            placeholder="{{ trans('labels.current_password') }} " required>
                                        @error('current_password')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-12 mb-4">
                                        <label for="Name" class="form-label">{{ trans('labels.new_password') }}<span
                                                class="text-danger"> * </span></label>
                                        <input type="password" class="form-control input-h" name="new_password"
                                            id="validationDefault" value=""
                                            placeholder="{{ trans('labels.new_password') }}" required>
                                        @error('new_password')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-12 mb-4">
                                        <label for="Name"
                                            class="form-label">{{ trans('labels.confirm_password') }}<span
                                                class="text-danger"> * </span></label>
                                        <input type="password" class="form-control input-h" name="confirm_password"
                                            id="validationDefault" value=""
                                            placeholder="{{ trans('labels.confirm_password') }}" required>
                                        @error('confirm_password')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-12 d-flex justify-content-end">
                                        <button type="submit"
                                            class="btn-primary btn fs-15 fw-500 rounded px-sm-4 px-3 py-2">{{ trans('labels.save') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Change Password section end -->

    @include('front.sum_qusction')
@endsection
