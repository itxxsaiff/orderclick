@extends('front.template-9.layout')
@section('content')
    @include('front.template-9.partials._legal', [
        'legalTitle' => __('Terms & Conditions'),
        'legalContent' => optional($terms)->terms_content,
    ])
@endsection
