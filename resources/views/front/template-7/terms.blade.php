@extends('front.template-7.layout')
@section('content')
    @include('front.template-7.partials._legal', [
        'legalTitle' => __('Terms & Conditions'),
        'legalContent' => optional($terms)->terms_content,
    ])
@endsection
