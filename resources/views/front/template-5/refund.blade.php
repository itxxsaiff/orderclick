@extends('front.template-5.layout')
@section('content')
    @include('front.template-5.partials._legal', [
        'legalTitle' => __('Refund Policy'),
        'legalContent' => optional($refund_policy)->refund_policy_content,
    ])
@endsection
