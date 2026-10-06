@extends('front.template-20.layout')
@section('content')
    @include('front.template-20.partials._legal', [
        'legalTitle' => __('Privacy Policy'),
        'legalContent' => optional($privacy)->privacypolicy_content,
    ])
@endsection
