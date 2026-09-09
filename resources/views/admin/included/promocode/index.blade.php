@extends('admin.layout.default')
@php
    if (Auth::user()->type == 4) {
        $vendor_id = Auth::user()->vendor_id;
    } else {
        $vendor_id = Auth::user()->id;
    }
@endphp
@section('content')
    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-12 col-md-4">
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.coupons') }}</h5>
            @include('admin.layout.breadcrumb')
        </div>
        <div class="col-12 col-md-4">
            <div class="d-flex justify-content-end">
                <a href="{{ URL::to('admin/coupons/add') }}"
                    class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_coupons', Auth::user()->role_id, $vendor_id, 'add') == 1 ? '' : 'd-none') : '' }}">
                    <i class="fa-regular fa-plus mx-1"></i>{{ trans('labels.add') }}
                </a>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-12">

            <div class="card border-0 my-3">

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-striped table-bordered py-3 zero-configuration w-100">

                            <thead>

                                <tr class="text-capitalize fs-15 fw-500">
                                    <td></td>
                                    <td>{{ trans('labels.srno') }}</td>

                                    <td>{{ trans('labels.offer_name') }}</td>

                                    <td>{{ trans('labels.offer_code') }}</td>

                                    <td>{{ trans('labels.discount') }}</td>
                                    @if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1))
                                        <td>{{ trans('labels.applicable_system') }}</td>
                                        <td>{{ trans('labels.applicable_plans') }}</td>
                                        <td>{{ trans('labels.uses') }}</td>
                                    @endif

                                    <td>{{ trans('labels.start_date') }}</td>

                                    <td>{{ trans('labels.end_date') }}</td>

                                    <td>{{ trans('labels.status') }}</td>
                                    <td>{{ trans('labels.created_date') }}</td>
                                    <td>{{ trans('labels.updated_date') }}</td>

                                    <td>{{ trans('labels.action') }}</td>

                                </tr>

                            </thead>

                            <tbody id="tabledetails" data-url="{{ url('admin/coupons/reorder_coupon') }}">

                                @php $i = 1; @endphp

                                @foreach ($getpromocodeslist as $promocode)
                                    <tr class="fs-7 align-middle row1" id="dataid{{ $promocode->id }}"
                                        data-id="{{ $promocode->id }}">
                                        <td><a tooltip="{{ trans('labels.move') }}"><i
                                                    class="fa-light fa-up-down-left-right mx-2"></i></a></td>
                                        <td>@php echo $i++; @endphp</td>

                                        <td>{{ $promocode->offer_name }}</td>

                                        <td>{{ $promocode->offer_code }}</td>

                                        <td>{{ $promocode->offer_type == 1 ? helper::currency_formate($promocode->offer_amount, $promocode->vendor_id) : $promocode->offer_amount . '%' }}
                                        </td>

                                        @if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1))
                                            <td class="fs-7">{{ $promocode->systemLabel() }}</td>
                                            <td class="fs-7">
                                                @php $ocIds = $promocode->planIds(); @endphp
                                                @if (empty($ocIds))
                                                    <span class="text-muted">{{ trans('labels.all_plans') }}</span>
                                                @else
                                                    {{ \App\Models\PricingPlan::whereIn('id', $ocIds)->pluck('name')->implode(', ') }}
                                                @endif
                                            </td>
                                            <td class="fs-7">
                                                {{ $promocode->timesUsed() }}
                                                / {{ $promocode->max_total_uses ?: '∞' }}
                                            </td>
                                        @endif

                                        <td><span
                                                class="badge bg-success">{{ helper::date_format($promocode->start_date, $promocode->vendor_id) }}</span>
                                        </td>

                                        <td><span
                                                class="badge bg-danger">{{ helper::date_format($promocode->exp_date, $promocode->vendor_id) }}</span>
                                        </td>

                                        <td>
                                            <div class="d-flex gap-2">
                                                @if ($promocode->is_available == '1')
                                                    <a href="javascript:void(0)" tooltip="{{ trans('labels.active') }}"
                                                        @if (env('Environment') == 'sendbox') onclick="myFunction()" @else  onclick="statusupdate('{{ URL::to('admin/coupons/status-' . $promocode->id . '/2') }}')" @endif
                                                        class="btn btn-sm btn-size btn-success {{ Auth::user()->type == 4 ? (helper::check_access('role_coupons', Auth::user()->role_id, Auth::user()->vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}">
                                                        <i class="fa-regular fa-check"></i> </a>
                                                @else
                                                    <a href="javascript:void(0)" tooltip="{{ trans('labels.inactive') }}"
                                                        @if (env('Environment') == 'sendbox') onclick="myFunction()" @else  onclick="statusupdate('{{ URL::to('admin/coupons/status-' . $promocode->id . '/1') }}')" @endif
                                                        class="btn btn-sm btn-size btn-danger  {{ Auth::user()->type == 4 ? (helper::check_access('role_coupons', Auth::user()->role_id, Auth::user()->vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}">
                                                        <i class="fa-regular fa-xmark"></i> </a>
                                                @endif
                                            </div>

                                        </td>
                                        <td>{{ helper::date_format($promocode->created_at, $promocode->vendor_id) }}<br>
                                            {{ helper::time_format($promocode->created_at, $promocode->vendor_id) }}
                                        </td>
                                        <td>{{ helper::date_format($promocode->updated_at, $promocode->vendor_id) }}<br>
                                            {{ helper::time_format($promocode->updated_at, $promocode->vendor_id) }}
                                        </td>

                                        <td>
                                            <div class="d-flex gap-2">
                                                <a class="btn btn-sm btn-info btn-size  {{ Auth::user()->type == 4 ? (helper::check_access('role_coupons', Auth::user()->role_id, Auth::user()->vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}"
                                                    tooltip="{{ trans('labels.edit') }}"
                                                    href="{{ URL::to('admin/coupons/edit-' . $promocode->id) }}">
                                                    <i class="fa-regular fa-pen-to-square"></i> </a>
                                                <a class="btn btn-sm btn-danger btn-size  {{ Auth::user()->type == 4 ? (helper::check_access('role_coupons', Auth::user()->role_id, Auth::user()->vendor_id, 'delete') == 1 ? '' : 'd-none') : '' }}"
                                                    tooltip="{{ trans('labels.delete') }}"
                                                    @if (env('Environment') == 'sendbox') onclick="myFunction()" @else onclick="deletedata('{{ URL::to('admin/coupons/delete-' . $promocode->id) }}')" @endif>
                                                    <i class="fa-regular fa-trash"></i> </a>
                                            </div>

                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>
@endsection
