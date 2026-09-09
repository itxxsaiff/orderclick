@extends('front.theme.default')
@section('recaptcha_script')
    <!-- IF VERSION 2  -->
    @if (helper::adminappdata('')->recaptcha_version == 'v2')
        <script src='https://www.google.com/recaptcha/api.js'></script>
    @endif
    <!-- IF VERSION 3  -->
    @if (helper::adminappdata('')->recaptcha_version == 'v3')
        {!! RecaptchaV3::initJs() !!}
    @endif
@endsection
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
                        {{ trans('labels.contact_us') }}
                    </li>
                </ol>
            </nav>
        </div>
    </section>
    <!-- breadcrumb end -->
    <!-- contact section start -->
    <section class="my-5">
        <div class="container">
            <div class="row gx-sm-3 gx-0">
                <div class="col-12 col-lg-8 col-sm-12 col-auto mb-4 mb-lg-0">
                    <div class="card rounded h-100 select-delivery py-3 px-sm-2 px-0">
                        <div class="card-body">
                            <form action="{{ URL::To(@$storeinfo->slug . '/submit') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <h2 class="page-title mb-1 px-2"> {{ trans('labels.contact_us') }}</h2>
                                    <p class="page-subtitle px-2 mb-3 md-mb-5">
                                    </p>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6 mb-4">
                                        <label for="validationDefault"
                                            class="form-label">{{ trans('labels.frist_name') }}<span class="text-danger"> *
                                            </span></label>
                                        <input type="text" class="form-control input-h" name="first_name"
                                            placeholder="{{ trans('labels.frist_name') }}" required>
                                        <input type="hidden" name="vendor_id" value="{{ $vdata }}">
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label for="validationDefault"
                                            class="form-label">{{ trans('labels.last_name') }}<span class="text-danger"> *
                                            </span></label>
                                        <input type="text" class="form-control input-h" name="last_name"
                                            placeholder="{{ trans('labels.last_name') }}" required>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label for="validationDefault" class="form-label">{{ trans('labels.email') }}<span
                                                class="text-danger"> * </span></label>
                                        <input type="text" class="form-control input-h" name="email"
                                            placeholder="{{ trans('labels.email') }}" required>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label for="validationDefault" class="form-label">{{ trans('labels.mobile') }}<span
                                                class="text-danger"> * </span></label>
                                        <input type="number" class="form-control input-h" name="mobile"
                                            placeholder="{{ trans('labels.mobile') }}" required>
                                    </div>
                                    <div class="col-md-12 mb-4">
                                        <label class="form-label">{{ trans('labels.message') }}<span class="text-danger"> *
                                            </span></label>
                                        <textarea class="form-control input-h" rows="5" aria-label="With textarea" name="message"
                                            placeholder="{{ trans('labels.message') }}" required></textarea>
                                    </div>
                                    @include('landing.layout.recaptcha')
                                    <div class="col d-flex justify-content-end mt-2">
                                        <button type="submit"
                                            class="btn-primary btn rounded px-sm-4 px-3 py-2 fw-500 fs-15 mobile-viwe-btn">{{ trans('labels.submit') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 col-sm-12 col-auto">
                    <div class="card contact-details rounded">
                        <div class="card-body mx-3">
                            <h4 class="page-title mb-4">{{ trans('labels.contact_details') }}</h4>
                            <ul class="contact-right-side">
                                <li>
                                    <i class="fa-solid fa-location-dot"></i>
                                    <span>
                                        <a href="#" class="px-2"> {{ helper::appdata($vdata)->address }}</a>
                                    </span>
                                </li>
                                <li>
                                    <i class="fa-solid fa-headphones"></i>
                                    <span class="px-2">{{ trans('labels.call_us') }}<a
                                            href="tel:{{ helper::appdata($vdata)->contact }}">
                                            +{{ helper::appdata($vdata)->contact }}</a>
                                    </span>
                                </li>
                                <li>
                                    <i class="fa-regular fa-envelope"></i>
                                    <span class="px-2">
                                        {{ trans('labels.email') }}
                                        <a href="mailto:{{ helper::appdata($vdata)->email }}">
                                            {{ helper::appdata($vdata)->email }}</a>
                                    </span>
                                </li>
                                <li>
                                    <i class="fa-regular fa-circle-question"></i>
                                    <span class="px-2">
                                        {{ trans('labels.hours') }}
                                        @foreach (@helper::timings($vdata) as $timing)
                                            <br>
                                            <a href="#">
                                                {{ Str::upper($timing->day) }} --
                                                @if ($timing->is_always_close == 1)
                                                    ({{ trans('labels.closed') }})
                                                @else
                                                    ({{ $timing->open_time }} to {{ $timing->close_time }})
                                                @endif
                                            </a>
                                        @endforeach

                                    </span>
                                </li>
                            </ul>
                            <hr class="my-4">
                            <h5 class="title mb-2">{{ trans('labels.social_share') }}</h5>
                            <div class="social-share d-flex flex-wrap gap-2">
                                @foreach (@helper::getsociallinks($vdata) as $links)
                                    <a class="btn btn-outline-light btn-social" role="button"
                                        href="{{ $links->link }}">{!! $links->icon !!}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- contact section start -->

    <!-- Subscription Section Start -->
    <section class="theme-1-margin-top my-5 extras-margin">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="subscription-main position-relative w-100 overflow-hidden">
                        <div class="overflow-hidden rounded">
                            <img src="{{ helper::image_path(helper::appdata($vdata)->subscribe_background) }}"
                                class="img-fluid subscription-image rounded-2">
                            <div class="caption-subscription col-md-7 col-lg-6">
                                <div class="subscription-text">
                                    <h3>{{ trans('labels.subscribe_title') }}</h3>
                                    <p>{{ trans('labels.subscribe_description') }}</p>
                                    <form action="{{ URL::to($storeinfo->slug . '/subscribe') }}" method="post">
                                        @csrf
                                        <div class="subscribe-input form-control col-md-6">
                                            <input type="hidden" value="{{ $vdata }}" name="id">
                                            <input type="email" name="email" class="form-control border-0"
                                                placeholder="{{ trans('labels.enter_email') }}" required>
                                            <button type="submit"
                                                class="btn btn-primary m-0 fs-15 fw-500">{{ trans('labels.subscribe') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Subscription Section End -->
@endsection
