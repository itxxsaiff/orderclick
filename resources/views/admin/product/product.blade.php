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
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.products') }}</h5>
            @include('admin.layout.breadcrumb')
        </div>
        <div class="col-12 col-md-8">
            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ URL::to('admin/products/add') }}"
                    class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_products', Auth::user()->role_id, $vendor_id, 'add') == 1 ? '' : 'd-none') : '' }}">
                    <i class="fa-regular fa-plus mx-1"></i>{{ trans('labels.add') }}
                </a>
                @if ($getproductslist->count() > 0)
                    <a href="{{ URL::to('/admin/exportproduct') }}"
                        class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_products', Auth::user()->role_id, $vendor_id, 'add') == 1 ? '' : 'd-none') : '' }} ">{{ trans('labels.export') }}</a>
                @endif
            </div>
        </div>
    </div>
    <div class="col-12 mb-7 mt-3">
        <div class="card border-0 box-shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered py-3 zero-configuration w-100">
                        <thead>
                            <tr class="fw-500">
                                <td></td>
                                <td>{{ trans('labels.srno') }}</td>
                                <td>{{ trans('labels.image') }}</td>
                                <td>{{ trans('labels.category') }}</td>
                                <td>{{ trans('labels.name') }}</td>
                                <td>{{ trans('labels.price') }}</td>
                                <td>{{ trans('labels.stock') }}</td>
                                <td>{{ trans('labels.status') }}</td>
                                <td>{{ trans('labels.created_date') }}</td>
                                <td>{{ trans('labels.updated_date') }}</td>
                                <td>{{ trans('labels.action') }}</td>
                            </tr>
                        </thead>
                        <tbody id="tabledetails" data-url="{{ url('admin/products/reorder_product') }}">
                            @php $i = 1; @endphp
                            @foreach ($getproductslist as $product)
                                <tr class="fs-7 align-middle row1" id="dataid{{ $product->id }}"
                                    data-id="{{ $product->id }}">
                                    <td><a tooltip="{{ trans('labels.move') }}"><i
                                                class="fa-light fa-up-down-left-right mx-2"></i></a></td>
                                    <td>@php echo $i++; @endphp</td>
                                    <td><img src="@if (@$product['item_image']->image_url != null) {{ @$product['item_image']->image_url }} @else {{ helper::image_path($product->image) }} @endif"
                                            class="img-fluid rounded hw-50 object-fit-cover" alt=""> </td>
                                    <td>{{ @$product['category_info']->name }}</td>
                                    <td>{{ $product->item_name }}
                                    </td>
                                    <td>
                                        @if ($product->has_variants == 1)
                                            <span class="badge bg-info">{{ trans('labels.in_variants') }}</span><br>
                                        @else
                                            {{ helper::currency_formate($product->item_price, $vendor_id) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($product->has_variants == 1)
                                            <span class="badge bg-info">{{ trans('labels.in_variants') }}</span><br>
                                            @if (helper::checklowqty($product->id, $product->vendor_id) == 1)
                                                <span class="badge bg-warning">{{ trans('labels.low_qty') }}</span>
                                            @endif
                                        @else
                                            @if ($product->stock_management == 1)
                                                @if (helper::checklowqty($product->id, $product->vendor_id) == 1)
                                                    <span
                                                        class="badge bg-success">{{ trans('labels.in_stock') }}</span><br>
                                                    <span class="badge bg-warning">{{ trans('labels.low_qty') }}</span>
                                                @elseif(helper::checklowqty($product->id, $product->vendor_id) == 2)
                                                    <span class="badge bg-danger">{{ trans('labels.out_of_stock') }}</span>
                                                @elseif(helper::checklowqty($product->id, $product->vendor_id) == 3)
                                                    -
                                                @else
                                                    <span class="badge bg-success">{{ trans('labels.in_stock') }}</span>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        @endif

                                    </td>

                                    <td>
                                        @if ($product->is_available == '1')
                                            <a @if (env('Environment') == 'sendbox') onclick="myFunction()" @else onclick="statusupdate('{{ URL::to('admin/products/status-' . $product->slug . '/2') }}')" @endif
                                                class="btn btn-sm btn-success btn-size {{ Auth::user()->type == 4 ? (helper::check_access('role_products', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}"
                                                tooltip="{{ trans('labels.active') }}"><i class="fas fa-check"></i></a>
                                        @else
                                            <a @if (env('Environment') == 'sendbox') onclick="myFunction()" @else onclick="statusupdate('{{ URL::to('admin/products/status-' . $product->slug . '/1') }}')" @endif
                                                class="btn btn-sm btn-danger btn-xmark {{ Auth::user()->type == 4 ? (helper::check_access('role_products', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}"
                                                tooltip="{{ trans('labels.in_active') }}"><i class="fas fa-close"></i></a>
                                        @endif
                                    </td>
                                    <td>{{ helper::date_format($product->created_at, $vendor_id) }}<br>
                                        {{ helper::time_format($product->created_at, $vendor_id) }}

                                    </td>
                                    <td>{{ helper::date_format($product->updated_at, $vendor_id) }}<br>
                                        {{ helper::time_format($product->updated_at, $vendor_id) }}

                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a class="btn btn-sm btn-info btn-size {{ Auth::user()->type == 4 ? (helper::check_access('role_products', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}"
                                                tooltip="{{ trans('labels.edit') }}"
                                                href="{{ URL::to('admin/products/edit-' . $product->slug) }}">
                                                <i class="fa-regular fa-pen-to-square"></i></a>
                                            <a class="btn btn-sm btn-danger btn-size {{ Auth::user()->type == 4 ? (helper::check_access('role_products', Auth::user()->role_id, $vendor_id, 'delete') == 1 ? '' : 'd-none') : '' }}"
                                                tooltip="{{ trans('labels.delete') }}"
                                                @if (env('Environment') == 'sendbox') onclick="myFunction()" @else onclick="statusupdate('{{ URL::to('admin/products/delete-' . $product->slug) }}')" @endif>
                                                <i class="fa-regular fa-trash"></i></a>
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
