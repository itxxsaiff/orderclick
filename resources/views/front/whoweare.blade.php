@extends('front.theme.default')
@section('content')
    <section class="breadcrumb-sec bg-change-mode">
        <div class="container">
            <nav>
                <ol class="breadcrumb d-flex m-0 text-capitalize">
                    <li class="breadcrumb-item">
                        <a href="{{ URL::to(@$storeinfo->slug) }}" class="text-dark color-changer">{{ trans('labels.home') }}</a>
                    </li>
                    <li
                        class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-item-right' : 'breadcrumb-item-left' }}">
                        {{ trans('labels.who_we_are') }}
                    </li>
                </ol>
            </nav>
        </div>
    </section>
    @if (count($allwhoweare) > 0)
        <section class="theme-1-margin-top">
            <div class="container Who_We_Are">
                <div class="row my-md-4 my-3 g-3">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 d-flex justify-content-center align-items-center">
                        <div>
                            <div class="menu-heading">
                                <h3 class="page-title mb-1 text-capitalize">
                                    {{ helper::appdata($storeinfo->id)->whoweare_title }}</h3>
                                <p class="page-subtitle line-limit-2 mt-0 fs-7">
                                    {{ helper::appdata($storeinfo->id)->whoweare_subtitle }}
                                </p>
                            </div>
                            <div class="row g-xl-4 g-3">
                                @foreach ($allwhoweare as $key => $whoweare)
                                    <div class="col-lg-6 col-12 d-flex gap-2">
                                        <div class="icon rounded-2">
                                            <img src="{{ helper::image_path($whoweare->image) }}"
                                                class="w-100 h-100 rounded-2">
                                        </div>
                                        <div class="text-content col">
                                            <h6 class="mb-2 fs-15 color-changer text-capitalize fw-600 line-2">{{ $whoweare->title }}
                                            </h6>
                                            <p class="fs-7 text-muted m-0 line-3">{{ $whoweare->sub_title }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="image1 rounded overflow-hidden">
                            <img src="{{ helper::image_path(helper::appdata($storeinfo->id)->whoweare_image) }}"
                                alt="" class="object rounded">
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @else
        @include('front.nodata')
    @endif

    @include('front.sum_qusction')
@endsection
