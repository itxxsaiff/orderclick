@extends('front.theme.default')

@section('content')

<!-- breadcrumb start -->
<section class="breadcrumb-sec bg-change-mode">
    <div class="container">
        <nav>
            <ol class="breadcrumb d-flex m-0 text-capitalize">
                <li class="breadcrumb-item"><a href="{{URL::to(@$storeinfo->slug)}}"
                        class="text-dark color-changer">{{trans('labels.home')}}</a></li>
                        
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-item-right' : 'breadcrumb-item-left' }}">
                    {{trans('labels.terms')}}
                </li>
            </ol>
        </nav>
    </div>
</section>
<!-- breadcrumb end -->

<!-- terms-and-condition section start -->
@if($terms != null)

<section class="theme-1-margin-top">

    <div class="container">

        <div class="details cms-section row">

            {!!@$terms->terms_content!!}

        </div>

    </div>

</section>

@else

    @include('front.nodata')

@endif

@include('front.sum_qusction')

<!-- terms-and-condition section end -->

@endsection