@extends('admin.layout.default')
@section('content')
    @php
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
    @endphp
    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-12 col-md-4">
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.features') }}</h5>
            @include('admin.layout.breadcrumb')
        </div>
        <div class="col-12 col-md-8">
            <div class="d-flex justify-content-end">
                <a href="{{ URL::to('admin/features/add') }}"
                    class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_features', Auth::user()->role_id, Auth::user()->vendor_id, 'add') == 1 ? '' : 'd-none') : '' }}">
                    <i class="fa-regular fa-plus mx-1"></i>{{ trans('labels.add') }}
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 mb-7">
        <div class="card border-0 box-shadow">
            <div class="card-body">
                <div class="table-responsive" id="table-display">
                    <table class="table table-striped table-bordered py-3 zero-configuration w-100">
                        <thead>
                            <tr class=" fw-500">
                                <td></td>
                                <td>{{ trans('labels.srno') }}</td>
                                <td>{{ trans('labels.image') }}</td>
                                <td>{{ trans('labels.title') }}</td>
                                <td>{{ trans('labels.applies_to') }}</td>
                                <td>{{ trans('labels.display_order') }}</td>
                                <td>{{ trans('labels.description') }}</td>
                                <td>{{ trans('labels.created_date') }}</td>
                                <td>{{ trans('labels.updated_date') }}</td>
                                <td>{{ trans('labels.action') }}</td>
                            </tr>
                        </thead>
                        <tbody id="tabledetails" data-url="{{ url('admin/features/reorder_features') }}">
                            @php
                                $i = 1;
                            @endphp
                            @foreach ($features as $feature)
                                <tr class="fs-7 row1" id="dataid{{ $feature->id }}" data-id="{{ $feature->id }}">
                                    <td><a tooltip="{{ trans('labels.move') }}"><i
                                                class="fa-light fa-up-down-left-right mx-2"></i></a></td>
                                    <td>@php
                                        echo $i++;
                                    @endphp</td>
                                    <td>
                                        @if ($feature->hasImage())
                                            <img src="{{ helper::image_path($feature->image) }}"
                                                class="img-fluid rounded-3 hw-50 object-fit-cover" alt="">
                                        @else
                                            {{-- Images are uploaded later; a default icon stands in. --}}
                                            <div class="hw-50 rounded-circle d-flex align-items-center justify-content-center bg-light text-success"
                                                tooltip="{{ trans('messages.feature_image_optional') }}">
                                                <i class="fa-solid fa-star"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $feature->title }}</td>
                                    <td><span class="badge {{ $feature->appliesToClass() }}">{{ $feature->appliesToLabel() }}</span></td>
                                    <td>{{ $feature->reorder_id }}</td>
                                    <td>{{ $feature->description }}</td>
                                    <td>{{ helper::date_format($feature->created_at, $vendor_id) }}<br>
                                        {{ helper::time_format($feature->created_at, $vendor_id) }}

                                    </td>
                                    <td>{{ helper::date_format($feature->updated_at, $vendor_id) }}<br>
                                        {{ helper::time_format($feature->updated_at, $vendor_id) }}

                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ URL::to('/admin/features/edit-' . $feature->id) }}"
                                                class="btn btn-sm btn-info btn-size {{ Auth::user()->type == 4 ? (helper::check_access('role_features', Auth::user()->role_id, Auth::user()->vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}"
                                                tooltip="{{ trans('labels.edit') }}">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </a>
                                            <a href="javascript:void(0)"
                                                @if (env('Environment') == 'sendbox') onclick="myFunction()" @else onclick="statusupdate('{{ URL::to('admin/features/delete-' . $feature->id) }}')" @endif
                                                class="btn btn-sm btn-danger btn-size {{ Auth::user()->type == 4 ? (helper::check_access('role_features', Auth::user()->role_id, Auth::user()->vendor_id, 'delete') == 1 ? '' : 'd-none') : '' }}"
                                                tooltip="{{ trans('labels.delete') }}">
                                                <i class="fa-regular fa-trash"></i>
                                            </a>
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
