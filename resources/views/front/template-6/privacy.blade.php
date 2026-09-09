@extends('front.template-6.layout')
@section('content')
    @include('front.template-6.partials._legal', [
        'legalTitle' => __('Privacy Policy'),
        'legalContent' => optional($privacy)->privacypolicy_content,
    ])
@endsection
