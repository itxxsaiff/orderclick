@extends('front.template-7.layout')
@section('content')
    @include('front.template-7.partials._legal', [
        'legalTitle' => __('Privacy Policy'),
        'legalContent' => optional($privacy)->privacypolicy_content,
    ])
@endsection
