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
                        class="breadcrumb-item {{ session()->get('direction') == 2 ? 'breadcrumb-item-right' : 'breadcrumb-item-left' }}">
                        <a href="{{ URL::to(@$storeinfo->slug . '/blog-list') }}" class="text-dark color-changer">
                            {{ trans('labels.blogs') }}
                        </a>
                    </li>
                    <li
                        class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-item-right' : 'breadcrumb-item-left' }}">
                        {{ trans('labels.blogs_details') }}
                    </li>
                </ol>
            </nav>
        </div>
    </section>
    <!-- breadcrumb end -->
    <!-- blog detail section start -->
    @if ($blogdetail != null)
        <section class="mt-5">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="card h-100 rounded-5 overflow-hidden p-3">
                            <img src="{{ helper::image_path($blogdetail->image) }}" alt="blog-img" class="h-75 rounded-5">
                            <div class="card-body blog-detalis-body">
                                <h2 class="blog-details-title color-changer">{{ $blogdetail->title }}</h2>
                                <div class="blog-se text-muted">
                                    <p class="blog-details text-muted">
                                        {!! $blogdetail->description !!}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- blog detail section end -->
        <!-- related blog section start -->
        @if (count($getblog) > 0)
            <section class="theme-1-margin-top">
                <div class="container">
                    <div class="row blogs-card pt-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="page-title">{{ trans('labels.related_blogs') }}</h3>
                            <a href="{{ URL::to(@$storeinfo->slug . '/blog-list') }}"
                                class="border btn btn-secondary rounded-3 extra-paddings">
                                {{ trans('labels.view_all') }}
                            </a>
                        </div>
                        <div class="col">
                            <div class="owl-carousel blogs-slider owl-theme">
                                @foreach ($getblog as $blog)
                                    <a href="{{ URL::to(@$storeinfo->slug . '/blog-details-' . $blog->slug) }}">
                                        <div class="item pb-3">
                                            <div class="card h-100 rounded-5">
                                                <img src="{{ helper::image_path($blog->image) }}" alt=""
                                                    class="rounded-5">
                                                <div class="card-body py-4">
                                                    <p class="title mt-2 color-changer blog-title">{{ $blog->title }}</p>
                                                    <span class="blog-description text-muted">{!! $blog->description !!}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif
    @else
        @include('front.nodata')
    @endif
    <!-- related blog section end -->
    @include('front.sum_qusction')
@endsection
@section('script')
    <script>
        $('.blogs-slider').owlCarousel({
            loop: false,
            nav: false,
            margin: 15,
            dots: false,
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 5
                },
                1660: {
                    items: 5
                }
            }
        })
    </script>
@endsection
