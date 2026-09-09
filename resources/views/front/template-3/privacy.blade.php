@extends('front.template-3.layout')
@section('content')
    @include('front.template-3.partials._legal', [
        'legalTitle' => __('Privacy Policy'),
        'legalContent' => optional($privacy)->privacypolicy_content,
    ])
@endsection
