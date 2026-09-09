@extends('front.theme.default')

@section('content')
    <!-- breadcrumb start -->

    <section class="breadcrumb-sec bg-change-mode">
        <div class="container">
            <nav>
                <ol class="breadcrumb d-flex m-0 text-capitalize">
                    <li class="breadcrumb-item"><a href="{{ URL::to(@$storeinfo->slug) }}"
                            class="text-dark color-changer">{{ trans('labels.home') }}</a></li>

                    <li
                        class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-item-right' : 'breadcrumb-item-left' }}">
                        {{ trans('labels.settings') }}
                    </li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- breadcrumb end -->

    <!-- Setting section end -->

    <section class="py-sm-5 py-4">

        <div class="container">

            <div class="row gx-sm-3 gx-2">

                @include('front.theme.user_sidebar')

                <div class="col-lg-9 col-md-12">

                    <div class="card rounded">

                        <div class="card-body py-4">

                            <h2 class="page-title mb-2 px-sm-2">{{ trans('labels.profile') }}</h2>

                            <p class="page-subtitle px-sm-2 mb-4 line-limit-2">{{ trans('labels.profile_desc') }}</p>

                            <form action="{{ URL::to($storeinfo->slug . '/updateprofile/') }}" method="POST"
                                enctype="multipart/form-data">

                                @csrf

                                <div class="row gx-sm-3 gx-0">

                                    <div class="col-md-6 mb-4">

                                        <input type="hidden" value="{{ Auth::user()->id }}" name="id">

                                        <label for="Name" class="form-label">{{ trans('labels.name') }} <span
                                                class="text-danger"> * </span></label>

                                        <input type="text" class="form-control input-h" id="validationDefault"
                                            name="name" value="{{ Auth::user()->name }}"
                                            placeholder="{{ trans('labels.name') }} " required>

                                    </div>

                                    <div class="col-md-6 mb-4">

                                        <label for="Name" class="form-label">{{ trans('labels.email') }}<span
                                                class="text-danger"> * </span></label>

                                        <input type="email" class="form-control input-h" id="validationDefault"
                                            name="email" value="{{ Auth::user()->email }}"
                                            placeholder="{{ trans('labels.email') }}" required>

                                    </div>

                                    <div class="col-md-6 mb-4">

                                        <label for="Name" class="form-label">{{ trans('labels.mobile') }}<span
                                                class="text-danger"> * </span></label>

                                        <input type="tel" class="form-control input-h" id="validationDefault"
                                            name="mobile" value="{{ Auth::user()->mobile }}"
                                            placeholder="{{ trans('labels.mobile') }}" required>

                                    </div>

                                    <div class="col-md-12 mb-4">

                                        <label for="Name" class="form-label">{{ trans('labels.image') }}</label>

                                        <input class="form-control input-h" type="file" id="formFile" name="profile" />
                                        <img class="rounded-circle object-fit-cover mt-3"
                                            src="{{ helper::image_path(Auth::user()->image) }}" alt=""
                                            style="width:65px;">
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

    <!-- Setting section end -->

    @include('front.sum_qusction')
@endsection
