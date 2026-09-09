@extends('front.theme.default')

@section('content')
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
                        {{ trans('labels.review') }}
                    </li>
                </ol>
            </nav>
        </div>
    </section>
    <!-- breadcrumb end -->

    <!-- Review Section Start -->
    <!-- Testimonials -->
    @if (count($storereview) > 0)
        <section class="testimonials py-5">
            <div class="container">
                <div class="row g-3">
                    <h3 class="page-title mb-1"> {{ trans('labels.review') }}</h3>
                    <p class="page-subtitle line-limit-2 mt-0">
                        {{ trans('labels.review_note') }}
                    </p>
                    <!-- Title -->
                    @foreach ($storereview as $review)
                        <div class="col-xl-4 col-lg-6 col-12">
                            <div class="card rounded h-100">
                                <div class="card-body p-sm-4 p-3">
                                    <div class="d-flex gap-2 align-items-center">
                                        <div class="client-img">
                                            <img src="{{ helper::image_path($review->image) }}" alt=""
                                                class="object">
                                        </div>
                                        <div>
                                            <p class="fs-15 fw-600 color-changer d-flex flex-wrap gap-1 align-items-center">
                                                {{ $review->name }} - <span class="fs-8">{{ $review->position }}</span>
                                            </p>
                                            <ul class="d-flex mt-1 gap-1 m-0">
                                                @if ($review->star == 1)
                                                    <li class="fs-15 text-warning">
                                                        <i class="fa-solid fa-star"></i>
                                                    </li>
                                                    <li class="fs-15 text-warning">
                                                        <i class="fa-regular fa-star"></i>
                                                    </li>
                                                    <li class="fs-15 text-warning">
                                                        <i class="fa-regular fa-star"></i>
                                                    </li>
                                                    <li class="fs-15 text-warning">
                                                        <i class="fa-regular fa-star"></i>
                                                    </li>
                                                    <li class="fs-15 text-warning">
                                                        <i class="fa-regular fa-star"></i>
                                                    </li>
                                                @elseif ($review->star == 2)
                                                    <li class="fs-15 text-warning">
                                                        <i class="fa-solid fa-star"></i>
                                                    </li>
                                                    <li class="fs-15 text-warning">
                                                        <i class="fa-solid fa-star"></i>
                                                    </li>
                                                    <li class="fs-15 text-warning">
                                                        <i class="fa-regular fa-star"></i>
                                                    </li>
                                                    <li class="fs-15 text-warning">
                                                        <i class="fa-regular fa-star"></i>
                                                    </li>
                                                    <li class="fs-15 text-warning">
                                                        <i class="fa-regular fa-star"></i>
                                                    </li>
                                                @elseif ($review->star == 3)
                                                    <li class="fs-15 text-warning">
                                                        <i class="fa-solid fa-star"></i>
                                                    </li>
                                                    <li class="fs-15 text-warning">
                                                        <i class="fa-solid fa-star"></i>
                                                    </li>
                                                    <li class="fs-15 text-warning">
                                                        <i class="fa-solid fa-star"></i>
                                                    </li>
                                                    <li class="fs-15 text-warning">
                                                        <i class="fa-regular fa-star"></i>
                                                    </li>
                                                    <li class="fs-15 text-warning">
                                                        <i class="fa-regular fa-star"></i>
                                                    </li>
                                                @elseif ($review->star == 4)
                                                    <li class="fs-15 text-warning">
                                                        <i class="fa-solid fa-star"></i>
                                                    </li>
                                                    <li class="fs-15 text-warning">
                                                        <i class="fa-solid fa-star"></i>
                                                    </li>
                                                    <li class="fs-15 text-warning">
                                                        <i class="fa-solid fa-star"></i>
                                                    </li>
                                                    <li class="fs-15 text-warning">
                                                        <i class="fa-solid fa-star"></i>
                                                    </li>
                                                    <li class="fs-15 text-warning">
                                                        <i class="fa-regular fa-star"></i>
                                                    </li>
                                                @elseif ($review->star == 5)
                                                    <li class="fs-15 text-warning">
                                                        <i class="fa-solid fa-star"></i>
                                                    </li>
                                                    <li class="fs-15 text-warning">
                                                        <i class="fa-solid fa-star"></i>
                                                    </li>
                                                    <li class="fs-15 text-warning">
                                                        <i class="fa-solid fa-star"></i>
                                                    </li>
                                                    <li class="fs-15 text-warning">
                                                        <i class="fa-solid fa-star"></i>
                                                    </li>
                                                    <li class="fs-15 text-warning">
                                                        <i class="fa-solid fa-star"></i>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="review-client mt-2">
                                        <p class="fs-7 text-muted">
                                            {{ Str::limit($review->description, 180) }}
                                        </p>

                                        <div class="d-flex flex-wrap justify-content-between mt-3">
                                            <div class="d-flex gap-1 align-items-center color-changer text-dark">
                                                <i class="fa-solid fa-clock fs-8"></i>
                                                <p class="fs-8">{{ date('l', strtotime($review->created_at)) }},
                                                    {{ helper::time_format($review->created_at, $vdata) }}</p>
                                            </div>
                                            <div class="d-flex gap-1 align-items-center color-changer text-dark">
                                                <i class="fa-solid fa-calendar-days fs-8"></i>
                                                <p class="fs-8">{{ helper::date_format($review->created_at, $vdata) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @else
        @include('front.nodata')
    @endif
    <!-- Testimonials -->
    @include('front.sum_qusction')

    <!-- About Us Section End -->
@endsection
