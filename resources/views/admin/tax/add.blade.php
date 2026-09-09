@extends('admin.layout.default')
@section('content')
    @php
        $vendor_id = Auth::user()->type == 4 ? Auth::user()->vendor_id : Auth::user()->id;
    @endphp
    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-12">
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.add_tax_rule') }}</h5>
            <p class="fs-7 text-muted mb-1">{{ trans('messages.tax_page_subtitle') }}</p>
            @include('admin.layout.breadcrumb')
        </div>
    </div>

    <div class="col-12 mb-7">
        @include('admin.tax._form', ['tax' => null, 'action' => URL::to('admin/tax/save')])
    </div>
@endsection
