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
                        {{ trans('landing.blog_details') }}
                    </li>
                </ol>
            </nav>
        </div>
    </section>
    <section id="blog" class="blog py-5">
        <div class="container">
            <div class="sec-title mb-5" data-aos="zoom-in" data-aos-easing="ease-out-cubic" data-aos-duration="2000">
                <h2 class="text-capitalize color-changer">{{ trans('landing.blog_details') }}</h2>
                <h5 class="sub-title">{{ trans('landing.blog_details_desc') }}</h5>
            </div>
            <div class="card h-100 rounded-3 p-3">
                <img class="card-img-top blog-image blog_img rounded-3"
                    src="{{ url(env('ASSETSPATHURL') . 'admin-assets/images/blog/' . $blog->image) }}" alt="">
                <div class="card-body px-0">
                    <div class="d-flex align-items-start">
                        <div>
                            <h4 class="card-title text-truncate-2 color-changer">{{ $blog->title }}</h4>
                            <p class="card-text text-truncate-3">{!! @$blog->description !!}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sec-title my-5" data-aos="zoom-in" data-aos-easing="ease-out-cubic" data-aos-duration="2000">
                <h2 class="text-capitalize color-changer">{{ trans('landing.related_blogs') }}</h2>
                <h5 class="sub-title">{{ trans('landing.related_blogs_desc') }}</h5>
            </div>

            <div id="blog-owl" class="owl-carousel owl-theme">
                @foreach ($blogdata as $blog)
                    <div class="item" data-aos="zoom-in" data-aos-easing="ease-out-cubic" data-aos-duration="2000">
                        <a href="{{ URL::to('blog_details-' . $blog->id) }}">
                            <div class="card rounded-3 p-3">
                                <div class="overflow-hidden rounded-3">
                                    <a href="{{ URL::to('blog_details-' . $blog->id) }}">
                                        <img src="{{ url(env('ASSETSPATHURL') . 'admin-assets/images/blog/' . $blog->image) }}"
                                            class="card-img-top blog-card-top-img rounded-3 blog-card-hover" height="260"
                                            alt="...">
                                    </a>
                                </div>
                                <div class="card-body p-0 pt-3">
                                    <a href="{{ URL::to('blog_details-' . $blog->id) }}">
                                        <h6 class="fw-500 text-secondary">
                                            {{ $blog->title }}
                                        </h6>
                                    </a>
                                    <p class="fs-7 m-0 text_truncation2">
                                        {!! Str::limit(@$blog->description, 100) !!}
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
            <div class="d-flex justify-content-center align-items-center mt-5">
                <a href="{{ URL::to('blog_list') }}"
                    class="btn btn-secondary d-flex align-items-center gap-2 py-2 fs-15 fw-500 px-3">{{ trans('landing.see_all') }}<i
                        class="fa-solid fa-arrow-right"></i></a>
            </div>

        </div>
    </section>
@endsection
