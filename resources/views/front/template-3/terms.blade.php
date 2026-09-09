@extends('front.template-3.layout')
@section('content')
    @include('front.template-3.partials._legal', [
        'legalTitle' => __('Terms & Conditions'),
        'legalContent' => optional($terms)->terms_content,
    ])
@endsection
