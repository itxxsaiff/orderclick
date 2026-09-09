@extends('admin.layout.default')

@section('content')
    @php
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $i = 1;
    @endphp
    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-12 col-md-4">
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.theme_images') }}</h5>
            @include('admin.layout.breadcrumb')
        </div>
        <div class="col-12 col-md-8">
            <div class="d-flex justify-content-end">
                <a href="{{ URL::to('admin/themes/add') }}"
                    class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_theme_images', Auth::user()->role_id, Auth::user()->vendor_id, 'add') == 1 ? '' : 'd-none') : '' }}">
                    <i class="fa-regular fa-plus mx-1"></i>{{ trans('labels.add') }}
                </a>
            </div>
        </div>
    </div>

    <div class="row mt-3">

        <div class="col-12">

            <div class="card border-0 mb-3">

                    {{-- Filter by system — same pill treatment as Vendors, Locations and Store Categories. --}}
    @php
        $ocQ = request()->query();
        $ocTabUrl = fn($k) => URL::to('admin/themes') . '?' . http_build_query(array_merge($ocQ, ['tab' => $k]));
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
                        <form method="GET" action="{{ URL::to('admin/themes') }}">
                            <input type="hidden" name="tab" value="{{ $tab }}">
                            <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.search') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                                <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0"
                                    placeholder="{{ trans('labels.name') }}">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-striped table-bordered py-3 zero-configuration w-100 dataTable no-footer">

                            <thead>

                                <tr class="text-capitalize fw-500 fs-15">

                                    <td></td>

                                    <td>{{ trans('labels.srno') }}</td>

                                    <td>{{ trans('labels.image') }}</td>

                                    <td>{{ trans('labels.name') }}</td>

                                    <td>{{ trans('labels.system') }}</td>

                                    <td>{{ trans('labels.applicable_activity') }}</td>

                                    <td>{{ trans('labels.created_date') }}</td>

                                    <td>{{ trans('labels.updated_date') }}</td>

                                    <td>{{ trans('labels.action') }}</td>



                                </tr>

                            </thead>

                            <tbody id="tabledetails" data-url="{{ url('admin/themes/reorder_theme') }}">

                                @php

                                    $i = 1;

                                @endphp

                                @foreach ($themes as $theme)
                                    <tr class="fs-7 row1 align-middle" id="dataid{{ $theme->id }}"
                                        data-id="{{ $theme->id }}">

                                        <td><a tooltip="{{ trans('labels.move') }}"><i
                                                    class="fa-light fa-up-down-left-right mx-2"></i></a></td>

                                        <td>

                                            @php

                                                echo $i++;

                                            @endphp</td>

                                        <td>
                                            @if ($theme->hasPreview())
                                                <img src="{{ helper::image_path($theme->image) }}"
                                                    class="img-fluid rounded-3 hw-70 object-fit-cover" alt="">
                                            @else
                                                {{-- Screenshots are uploaded later from the admin panel. --}}
                                                <div class="hw-70 rounded-3 d-flex align-items-center justify-content-center bg-light text-muted"
                                                    tooltip="{{ trans('messages.preview_image_hint') }}">
                                                    <i class="fa-regular fa-image"></i>
                                                </div>
                                            @endif
                                        </td>

                                        <td>{{ $theme->name }}</td>

                                        <td class="fs-7">{{ $theme->systemLabel() }}</td>

                                        <td class="fs-7">{{ $theme->activityLabel() }}</td>

                                        <td>{{ helper::date_format($theme->created_at, $vendor_id) }}<br>
                                            {{ helper::time_format($theme->created_at, $vendor_id) }}
                                        </td>

                                        <td>{{ helper::date_format($theme->updated_at, $vendor_id) }}<br>
                                            {{ helper::time_format($theme->updated_at, $vendor_id) }}
                                        </td>

                                        <td>
                                            <div class="d-flex flex-wrap gap-1">

                                                <a href="{{ URL::to('/admin/themes/edit-' . $theme->id) }}"
                                                    class="btn btn-info btn-size btn-sm {{ Auth::user()->type == 4 ? (helper::check_access('role_theme_images', Auth::user()->role_id, Auth::user()->vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}"
                                                    tooltip="{{ trans('labels.edit') }}">
                                                    <i class="fa-regular fa-pen-to-square"></i>
                                                </a>

                                                <a href="javascript:void(0)" tooltip="{{ trans('labels.delete') }}"
                                                    @if (env('Environment') == 'sendbox') onclick="myFunction()" @else onclick="statusupdate('{{ URL::to('admin/themes/delete-' . $theme->id) }}')" @endif
                                                    class="btn btn-danger btn-size btn-sm {{ Auth::user()->type == 4 ? (helper::check_access('role_theme_images', Auth::user()->role_id, Auth::user()->vendor_id, 'delete') == 1 ? '' : 'd-none') : '' }}">
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

    </div>
@endsection
