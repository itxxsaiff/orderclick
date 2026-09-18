@extends('admin.layout.default')
@section('content')
    @php $ar = app()->getLocale() === 'ar'; @endphp
    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-12 col-md-6">
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.locations_branches') }}</h5>
            @include('admin.layout.breadcrumb')
        </div>
        <div class="col-12 col-md-6">
            <div class="d-flex justify-content-end gap-2">
                {{-- The allowance comes from the paid plan. At the limit the Add button is replaced
                     by Upgrade rather than failing after the fact. --}}
                @if ($canAdd)
                    <a href="{{ URL::to('admin/branches/add') }}" class="btn btn-secondary px-4 rounded-start-5 rounded-end-5">
                        <i class="fa-regular fa-plus mx-1"></i>{{ trans('labels.add') }}
                    </a>
                @else
                    <a href="{{ URL::to('admin/plan') }}" class="btn btn-warning px-4 rounded-start-5 rounded-end-5">
                        <i class="fa-solid fa-arrow-up mx-1"></i>{{ trans('labels.upgrade_plan') }}
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-3">
            <div class="card border-0 box-shadow">
                <div class="card-body py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <span class="text-muted fs-7 d-block">{{ trans('labels.branches') }}</span>
                        <span class="fw-600 fs-5 color-changer">
                            {{ $used }} / {{ $limit === null ? trans('labels.unlimited') : $limit }}
                        </span>
                    </div>
                    @if ($limit !== null)
                        <div class="flex-grow-1" style="max-width:340px">
                            @php $pct = \App\Helpers\Vendor360::usagePercent($used, $limit) ?? 0; @endphp
                            <div class="progress" style="height:6px;">
                                <div class="progress-bar {{ $pct >= 100 ? 'bg-danger' : ($pct >= 80 ? 'bg-warning' : 'bg-success') }}"
                                    style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endif
                    <span class="fs-7 text-muted">
                        {{ trans('labels.country_city_and_area_are_filled_automatically') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-12 mb-7">
            <div class="card border-0 box-shadow">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr class="fs-7 fw-500">
                                    <td>{{ trans('labels.name') }}</td>
                                    <td>{{ trans('labels.city') }} / {{ trans('labels.area') }}</td>
                                    <td>{{ trans('labels.written_address') }}</td>
                                    <td>GPS</td>
                                    <td>{{ trans('labels.coverage') }}</td>
                                    <td>{{ trans('labels.marketplace') }}</td>
                                    <td>{{ trans('labels.action') }}</td>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($branches as $b)
                                    @php $gps = $b->gpsState(); $mk = $b->marketplaceState($vendor); @endphp
                                    <tr class="fs-7">
                                        <td>
                                            {{ $b->name }}
                                            @if ($b->is_primary == 1)<span class="badge bg-success">{{ trans('labels.primary') }}</span>@endif
                                        </td>
                                        <td>{{ trim(($b->city ?: '—') . ' · ' . ($b->area ?: '—'), ' ·') }}</td>
                                        <td>{{ \Illuminate\Support\Str::limit($b->address, 40) ?: '—' }}</td>
                                        <td>
                                            <span class="{{ $gps['class'] }} fw-500">{{ $gps['text'] }}</span>
                                            @if ($b->latitude && $b->longitude)
                                                <a href="https://maps.google.com/?q={{ $b->latitude }},{{ $b->longitude }}" target="_blank" class="mx-1">
                                                    <i class="fa-solid fa-map-pin"></i></a>
                                            @endif
                                        </td>
                                        <td>{{ $b->coverageLabel($system) }}</td>
                                        <td><span class="badge {{ $mk['class'] }}">{{ $mk['text'] }}</span></td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a class="btn btn-sm btn-info btn-size" tooltip="{{ trans('labels.edit') }}"
                                                    href="{{ URL::to('admin/branches/edit-' . $b->id) }}"><i class="fa fa-pen-to-square"></i></a>
                                                @if ($b->is_primary != 1)
                                                    <a href="javascript:void(0)" tooltip="{{ trans('labels.delete') }}"
                                                        @if (env('Environment') == 'sendbox') onclick="myFunction()" @else onclick="statusupdate('{{ URL::to('admin/branches/delete-' . $b->id) }}')" @endif
                                                        class="btn btn-sm btn-danger btn-size"><i class="fa-regular fa-trash"></i></a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-muted fs-7">{{ trans('labels.no_records') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
