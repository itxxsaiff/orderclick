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
                    {{trans('labels.about_us')}}
                </li>
            </ol>
        </nav>
    </div>
</section>
<!-- breadcrumb end -->

<!-- About Us Section Start -->

<section class="theme-1-margin-top">

    <div class="container">

        <div class="details row">

            

            @if (!empty($aboutus->about_content))

                <div class="cms-section my-3">



                    {!! $aboutus->about_content !!}



                </div>

            @else

                @include('front.nodata')

            @endif

        </div>

    </div>

</section>

@include('front.sum_qusction')

<!-- About Us Section End -->

@endsection