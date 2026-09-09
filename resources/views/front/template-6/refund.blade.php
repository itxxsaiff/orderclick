@extends('front.template-6.layout')
@section('content')
    @include('front.template-6.partials._legal', [
        'legalTitle' => __('Refund Policy'),
        'legalContent' => optional($refund_policy)->refund_policy_content,
    ])
@endsection
