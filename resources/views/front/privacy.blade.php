@extends('front.theme.default')

@section('content')
    <!-- breadcrumb start -->
    <section class="breadcrumb-sec bg-change-mode">
        <div class="container">
            <nav>
                <ol class="breadcrumb d-flex m-0 text-capitalize">
                    <li class="breadcrumb-item"><a href="{{ URL::to(@$storeinfo->slug) }}"
                            class="text-dark color-changer">{{ trans('labels.home') }}</a></li>

                    <li
                        class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-item-right' : 'breadcrumb-item-left' }}">
                        {{ trans('labels.privacy_policy') }}
                    </li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- breadcrumb end -->

    <!-- Privacy Policy section end -->

    @if ($privacy != null)
        <section class="theme-1-margin-top">

            <div class="container">

                <div class="details cms-section row">

                    {!! @$privacy->privacypolicy_content !!}

                </div>

            </div>

        </section>
    @else
        @include('front.nodata')
    @endif

    @include('front.sum_qusction')
    <!-- Privacy Policy section end -->
@endsection
