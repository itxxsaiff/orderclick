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
                        {{ trans('labels.our_latest_blogs') }}
                    </li>
                </ol>
            </nav>
        </div>
    </section>
    <!-- breadcrumb end -->
    @if (count($blogs) > 0)
        <!-- Our Lestes Blogs section start -->
        <section class="theme-1-margin-top">
            <div class="container">
                <div class="row blogs-card pt-0 g-3">
                    <h3 class="page-title mb-1">{{ trans('labels.blogs') }}</h3>
                    <p class="page-subtitle line-limit-2 mt-0">
                        {{ trans('labels.blog_desc') }}
                    </p>
                    @foreach ($blogs as $blog)
                        <div class="col-lg-3">
                            <a href="{{ URL::to(@$storeinfo->slug . '/blog-details-' . $blog->slug) }}">
                                <div class="card h-100 rounded">
                                    <img src="{{ helper::image_path($blog->image) }}" alt="" class="rounded">
                                    <div class="card-body py-4">
                                        <p class="title mt-2 blog-title color-changer">{{ $blog->title }}</p>
                                        <span class="blog-description text-muted">{!! $blog->description !!}</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-center align-items-center m-auto mb-3">
                    {{ $blogs->links() }}
                </div>
            </div>
        </section>
    @else
        @include('front.nodata')
    @endif

    @include('front.sum_qusction')
    <!-- Our Lestes Blogs section end -->
@endsection
