@extends('front.template-9.layout')
@section('content')
    @include('front.template-9.partials._legal', [
        'legalTitle' => __('Privacy Policy'),
        'legalContent' => optional($privacy)->privacypolicy_content,
    ])
@endsection
