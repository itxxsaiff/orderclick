@php
    if (Auth::user()->type == 4) {
        $vendor_id = Auth::user()->vendor_id;
    } else {
        $vendor_id = Auth::user()->id;
    }
@endphp
@extends('admin.layout.default')
@section('content')
    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-12 col-md-4">
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.store_categories') }}</h5>
            <div class="d-flex">
                @include('admin.layout.breadcrumb')
            </div>
        </div>
        <div class="col-12 col-md-8">
            <div class="d-flex justify-content-end">
                <a href="{{ URL::to('admin/store_categories/add') }}"
                    class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_store_categories', Auth::user()->role_id, Auth::user()->vendor_id, 'add') == 1 ? '' : 'd-none') : '' }}">
                    <i class="fa-regular fa-plus mx-1"></i>{{ trans('labels.add') }}
                </a>
            </div>
        </div>
    </div>
    {{-- Filter by system. Same pill treatment as the Vendors and Locations pages. --}}
    @php
        $ocQ = request()->query();
        $ocTabUrl = fn($k) => URL::to('admin/store_categories') . '?' . http_build_query(array_merge($ocQ, ['tab' => $k]));
        $ocTabs = ['all' => trans('labels.all')] + collect(\App\Helpers\Systems::all())
            ->mapWithKeys(fn($x) => [$x['key'] => app()->getLocale() === 'ar' ? $x['name_ar'] : $x['name']])->all();
    @endphp
    <div class="col-12 mb-3">
        <div class="card border-0 box-shadow">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-lg-8">
                        <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.filter_by_system') }}</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($ocTabs as $ocKey => $ocLabel)
                                <a href="{{ $ocTabUrl($ocKey) }}"
                                    class="btn btn-sm rounded-start-5 rounded-end-5 px-3 {{ $tab === $ocKey ? 'btn-secondary' : 'btn-light' }}">
                                    {{ $ocLabel }}
                                    <span class="badge {{ $tab === $ocKey ? 'bg-light text-dark' : 'bg-secondary' }} ms-1">{{ $tabCounts[$ocKey] ?? 0 }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <form method="GET" action="{{ URL::to('admin/store_categories') }}">
                            <input type="hidden" name="tab" value="{{ $tab }}">
                            <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.search') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                                <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0"
                                    placeholder="{{ trans('labels.category') }}">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 mb-7">
        <div class="card border-0 box-shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered py-3 zero-configuration w-100">
                        <thead>
                            <tr class="text-capitalize fs-15 fw-500">
                                <td></td>
                                <td>{{ trans('labels.srno') }}</td>
                                <td>{{ trans('labels.category') }}</td>
                                <td>{{ trans('labels.system') }}</td>
                                <td>{{ trans('labels.image') }}</td>
                                <td>{{ trans('labels.status') }}</td>
                                <td>{{ trans('labels.created_date') }}</td>
                                <td>{{ trans('labels.updated_date') }}</td>
                                <td>{{ trans('labels.action') }}</td>
                            </tr>
                        </thead>

                        <tbody id="tabledetails" data-url="{{ url('admin/store_categories/reorder_category') }}">

                            @php $i=1; @endphp

                            @foreach ($allcategories as $category)
                                <tr class="fs-7 align-middle row1" id="dataid{{ $category->id }}"
                                    data-id="{{ $category->id }}">

                                    <td><a tooltip="{{ trans('labels.move') }}">
                                            <i class="fa-light fa-up-down-left-right mx-2"></i>
                                        </a>
                                    </td>

                                    <td>@php echo $i++ @endphp</td>

                                    <td>
                                        {{ $category->name }}
                                        @if ($category->is_other == 1)
                                            <span class="badge bg-secondary">{{ trans('labels.other') }}</span>
                                        @endif
                                    </td>

                                    <td class="fs-7">{{ $category->systemLabel() }}</td>

                                    <td>
                                        @if (!empty($category->image))
                                            <img src="{{ helper::image_path($category->image) }}"
                                                class="img-fluid rounded-3 hw-70 object-fit-cover" alt="">
                                        @else
                                            {{-- Images are uploaded later; show a neutral placeholder until then. --}}
                                            <div class="hw-70 rounded-3 d-flex align-items-center justify-content-center bg-light text-muted"
                                                tooltip="{{ trans('messages.category_image_placeholder') }}">
                                                <i class="fa-regular fa-image fs-4"></i>
                                            </div>
                                        @endif
                                    </td>

                                    <td>

                                        @if ($category->is_available == '1')
                                            <a tooltip="{{ trans('labels.active') }}"
                                                @if (env('Environment') == 'sendbox') onclick="myFunction()" @else onclick="statusupdate('{{ URL::to('admin/store_categories/change_status-' . $category->id . '/2') }}')" @endif
                                                class="btn btn-sm btn-success btn-size {{ Auth::user()->type == 4 ? (helper::check_access('role_store_categories', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        @else
                                            <a tooltip="{{ trans('labels.inactive') }}"
                                                @if (env('Environment') == 'sendbox') onclick="myFunction()" @else onclick="statusupdate('{{ URL::to('admin/store_categories/change_status-' . $category->id . '/1') }}')" @endif
                                                class="btn btn-sm btn-danger btn-size {{ Auth::user()->type == 4 ? (helper::check_access('role_store_categories', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}">
                                                <i class="fas fa-close"></i>
                                            </a>
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
                                            <a href="{{ URL::to('admin/store_categories/edit-' . $category->id) }}"
                                                tooltip="{{ trans('labels.edit') }}"
                                                class="btn btn-info btn-sm btn-size {{ Auth::user()->type == 4 ? (helper::check_access('role_store_categories', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </a>

                                            <a tooltip="{{ trans('labels.delete') }}"
                                                @if (env('Environment') == 'sendbox') onclick="myFunction()" @else onclick="statusupdate('{{ URL::to('admin/store_categories/delete-' . $category->id) }}')" @endif
                                                class="btn btn-danger btn-sm btn-size {{ Auth::user()->type == 4 ? (helper::check_access('role_store_categories', Auth::user()->role_id, $vendor_id, 'delete') == 1 ? '' : 'd-none') : '' }}">
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
