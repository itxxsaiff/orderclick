@extends('landing.layout.default')

@section('content')
<div class="ocl ocl-page">
    <div class="ocl-content">
        <div class="wrap">
            <div class="crumbs"><a href="{{ URL::to('/') }}">{{ trans('labels.home') }}</a> <span class="sep">/</span> {{ trans('landing.privacy_policy') }}</div>
            <div class="ocl-doc">
                {!! $privacy_policy->privacypolicy_content !!}
            </div>
        </div>
    </div>
</div>
@endsection
