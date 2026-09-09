@extends('front.template-5.layout')
@section('content')
    @include('front.template-5.partials._legal', [
        'legalTitle' => __('Terms & Conditions'),
        'legalContent' => optional($terms)->terms_content,
    ])
@endsection
