@extends('front.template-10.layout')
@section('content')
    @include('front.template-10.partials._legal', [
        'legalTitle' => __('Terms & Conditions'),
        'legalContent' => optional($terms)->terms_content,
    ])
@endsection
