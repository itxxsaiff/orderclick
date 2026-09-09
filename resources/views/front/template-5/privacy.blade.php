@extends('front.template-5.layout')
@section('content')
    @include('front.template-5.partials._legal', [
        'legalTitle' => __('Privacy Policy'),
        'legalContent' => optional($privacy)->privacypolicy_content,
    ])
@endsection
