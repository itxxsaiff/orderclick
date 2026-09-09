@extends('front.template-10.layout')
@section('content')
    @include('front.template-10.partials._legal', [
        'legalTitle' => __('Privacy Policy'),
        'legalContent' => optional($privacy)->privacypolicy_content,
    ])
@endsection
