@extends('landing.layout.default')
@section('content')
    <section class="breadcrumb-sec bg-change-mode">
        <div class="container">
            <nav>
                <ol class="breadcrumb d-flex m-0 text-capitalize">
                    <li class="breadcrumb-item">
                        <a href="{{ URL::to('/#home') }}" class="text-dark color-changer">
                            {{ trans('labels.home') }}
                        </a>
                    </li>
                    <li
                        class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-item-right' : 'breadcrumb-item-left' }}">
                        {{ trans('landing.our_partners') }}
                    </li>
                </ol>
            </nav>
        </div>
    </section>
    <section id="our-stores">
        <div class="owl-carousel owl_our_stores owl-theme position-relative">
            @foreach ($banners as $banner)
                <a href="{{ URL::to('/' . @$banner['vendor_info']->slug) }}" target="_blank">
                    <div class="item">
                        <div class="leyer">
                        </div>
                        <img src="{{ helper::image_path($banner->image) }}" alt="">
                    </div>
                </a>
            @endforeach
        </div>
        @if (count($stores) > 0)
            <div class="container">
                <div class="row row-cols-1 mt-2 row-cols-sm-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-3 py-5">
                    @foreach ($stores as $store)
                        <div class="col">
                            <a href="{{ URL::to($store->slug . '/') }}" target="_blank">
                                <div class="post-slide h-100 card card-bg border-0">
                                    <div class="box-post">
                                        <div class="box-img">
                                            <img src="{{ @helper::image_path(helper::appdata($store->id)->cover_image) }}"
                                                alt="">
                                            <span class="over-layer"></span>
                                        </div>
                                    </div>
                                    <div class="card-body pt-3 p-0">
                                        <h3 class="fs-6 post-title color-changer text-capitalize fw-600 line-2 mb-2">
                                            {{ @helper::appdata($store->id)->website_title }}
                                        </h3>
                                        <p class="hotel-subtitle text-muted fs-7 line-2 mb-0">
                                            {{ @helper::appdata($store->id)->description }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-center align-items-center mb-4">
                    {{ $stores->links() }}
                </div>
            </div>
        @else
            @include('landing.nodata')
        @endif
    </section>
@endsection
