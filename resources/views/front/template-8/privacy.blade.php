@extends('front.template-8.layout')
@section('content')
    @include('front.template-8.partials._legal', [
        'legalTitle' => __('Privacy Policy'),
        'legalContent' => optional($privacy)->privacypolicy_content,
    ])
@endsection
