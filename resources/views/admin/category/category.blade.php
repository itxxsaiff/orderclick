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
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.category') }}</h5>
            <div class="d-flex">
                @include('admin.layout.breadcrumb')
            </div>
        </div>
        <div class="col-12 col-md-8">
            <div class="d-flex justify-content-end">
                <a href="{{ URL::to('admin/categories/add') }}"
                    class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_categories', Auth::user()->role_id, $vendor_id, 'add') == 1 ? '' : 'd-none') : '' }}"><i
                        class="fa-regular fa-plus mx-1"></i>{{ trans('labels.add') }}</a>
            </div>
        </div>
    </div>

    <div class="col-12 mt-3 mb-7">
        <div class="card border-0 my-3 box-shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered py-3 zero-configuration w-100">
                        <thead>
                            <tr class="fw-500">
                                <td></td>
                                <td>{{ trans('labels.srno') }}</td>
                                <td>{{ trans('labels.image') }}</td>
                                <td>{{ trans('labels.category') }}</td>
                                <td>{{ trans('labels.status') }}</td>
                                <td>{{ trans('labels.created_date') }}</td>
                                <td>{{ trans('labels.updated_date') }}</td>
                                <td>{{ trans('labels.action') }}</td>
                            </tr>
                        </thead>
                        <tbody id="tabledetails" data-url="{{ url('admin/categories/reorder_category') }}">
                            @php $i=1; @endphp
                            @foreach ($allcategories as $category)
                                <tr class="fs-7 align-middle row1" id="dataid{{ $category->id }}"
                                    data-id="{{ $category->id }}">
                                    <td><a tooltip="{{ trans('labels.move') }}"><i
                                                class="fa-light fa-up-down-left-right mx-2"></i></a></td>
                                    <td>@php echo $i++ @endphp</td>
                                    <td><img src="{{ url(env('ASSETSPATHURL') . 'admin-assets/images/category/' . $category->image) }}"
                                            alt="" width="50" class="img-fluid rounded hw-50 object-fit-cover">
                                    </td>
                                    <td>{{ $category->name }}</td>

                                    <td>
                                        @if ($category->is_available == '1')
                                            <a @if (env('Environment') == 'sendbox') onclick="myFunction()" @else onclick="statusupdate('{{ URL::to('admin/categories/change_status-' . $category->slug . '/2') }}')" @endif
                                                class="btn btn-sm btn-success btn-size {{ Auth::user()->type == 4 ? (helper::check_access('role_categories', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}"
                                                tooltip="{{ trans('labels.active') }}"><i class="fas fa-check"></i></a>
                                        @else
                                            <a @if (env('Environment') == 'sendbox') onclick="myFunction()" @else onclick="statusupdate('{{ URL::to('admin/categories/change_status-' . $category->slug . '/1') }}')" @endif
                                                class="btn btn-sm btn-danger btn-xmark {{ Auth::user()->type == 4 ? (helper::check_access('role_categories', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}"
                                                tooltip="{{ trans('labels.in_active') }}"><i class="fas fa-close"></i></a>
                                        @endif
                                    </td>
                                    <td>{{ helper::date_format($category->created_at, $vendor_id) }}<br>
                                        {{ helper::time_format($category->created_at, $vendor_id) }}
                                    </td>
                                    <td>{{ helper::date_format($category->updated_at, $vendor_id) }}<br>
                                        {{ helper::time_format($category->updated_at, $vendor_id) }}

                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ URL::to('admin/categories/edit-' . $category->slug) }}"
                                                class="btn btn-sm btn-info btn-size {{ Auth::user()->type == 4 ? (helper::check_access('role_categories', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}"
                                                tooltip="{{ trans('labels.edit') }}">
                                                <i class="fa-regular fa-pen-to-square"></i></a>
                                            <a @if (env('Environment') == 'sendbox') onclick="myFunction()" @else onclick="statusupdate('{{ URL::to('admin/categories/delete-' . $category->slug) }}')" @endif
                                                class="btn btn-sm btn-danger btn-size {{ Auth::user()->type == 4 ? (helper::check_access('role_categories', Auth::user()->role_id, $vendor_id, 'delete') == 1 ? '' : 'd-none') : '' }}"
                                                tooltip="{{ trans('labels.delete') }}"> <i
                                                    class="fa-regular fa-trash"></i></a>
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
@endsection
