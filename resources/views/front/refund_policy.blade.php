@extends('front.theme.default')

@section('content')

<!-- breadcrumb start -->
<section class="breadcrumb-sec bg-change-mode">
    <div class="container">
        <nav>
            <ol class="breadcrumb d-flex m-0 text-capitalize">
                <li class="breadcrumb-item"><a href="{{ URL::to(@$storeinfo->slug) }}"
                        class="text-dark color-changer">{{ trans('labels.home') }}</a></li>
                        
                <li class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-item-right' : 'breadcrumb-item-left' }}">
                    {{trans('labels.refund_policy')}}</li>
            </ol>
        </nav>
    </div>
</section>

<!-- breadcrumb end -->

<!-- refund Policy section end -->

@if($refund_policy != null)

<section class="theme-1-margin-top">

    <div class="container">

        <div class="details cms-section row">

            {!!@$refund_policy->refund_policy_content!!}

        </div>

    </div>

</section>

@else

    @include('front.nodata')

@endif
@include('front.sum_qusction')
<!-- refund Policy section end -->

@endsection