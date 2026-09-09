@extends('front.template-8.layout')
@section('content')
    @include('front.template-8.partials._legal', [
        'legalTitle' => __('Terms & Conditions'),
        'legalContent' => optional($terms)->terms_content,
    ])
@endsection
