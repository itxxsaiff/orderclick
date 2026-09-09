@extends('front.template-6.layout')
@section('content')
    @include('front.template-6.partials._legal', [
        'legalTitle' => __('Terms & Conditions'),
        'legalContent' => optional($terms)->terms_content,
    ])
@endsection
