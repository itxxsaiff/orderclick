@extends('front.template-20.layout')
@section('content')
    @include('front.template-20.partials._legal', [
        'legalTitle' => __('Terms & Conditions'),
        'legalContent' => optional($terms)->terms_content,
    ])
@endsection
