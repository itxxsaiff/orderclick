@extends('admin.layout.default')
@section('content')
    @php
        $ar = app()->getLocale() === 'ar';
        $q = request()->query();
        $tabUrl = fn($k) => URL::to('admin/locations') . '?' . http_build_query(array_merge($q, ['tab' => $k]));
    @endphp

    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-12 col-md-7">
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.marketplace_locations') }}</h5>
            @include('admin.layout.breadcrumb')
        </div>
        @if ($pendingReview > 0)
            <div class="col-12 col-md-5">
                <div class="d-flex justify-content-end">
                    <a href="{{ URL::to('admin/locations') }}?review=pending" class="btn btn-warning px-4 rounded-start-5 rounded-end-5">
                        <i class="fa-solid fa-bell mx-1"></i>{{ trans('labels.locations_to_review') }}
                        <span class="badge bg-light text-dark ms-1">{{ $pendingReview }}</span>
                    </a>
                </div>
            </div>
        @endif
    </div>

    {{-- Vendor selects GPS or map -> address fills automatically -> admin reviews -> account
         approved -> the business appears in the Marketplace on its own. --}}
    <div class="row">
        <div class="col-12 mb-3">
            <div class="alert alert-success fs-7 mb-0">
                <i class="fa-solid fa-circle-info mx-1"></i>
                {{ trans('messages.location_workflow_notice') }}
            </div>
        </div>

        <div class="col-12 mb-3">
            <div class="d-flex flex-wrap gap-2">
                @php
                    $tabs = ['all' => trans('labels.all')] + collect(\App\Helpers\Systems::all())
                        ->mapWithKeys(fn($s) => [$s['key'] => $s['name']])->all();
                @endphp
                @foreach ($tabs as $key => $label)
                    <a href="{{ $tabUrl($key) }}"
                        class="btn btn-sm rounded-start-5 rounded-end-5 px-3 {{ $tab === $key ? 'btn-secondary' : 'btn-light' }}">
                        {{ $label }} <span class="badge {{ $tab === $key ? 'bg-light text-dark' : 'bg-secondary' }} ms-1">{{ $tabCounts[$key] ?? 0 }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="col-12 mb-3">
            <div class="card border-0 box-shadow">
                <div class="card-body">
                    <form method="GET" class="row g-2 align-items-end">
                        <input type="hidden" name="tab" value="{{ $tab }}">
                        <div class="col-12 col-md-5">
                            <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.search') }}</label>
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                                placeholder="{{ trans('labels.search_business_or_location') }}">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label fs-7 text-muted text-uppercase mb-1">GPS</label>
                            <select name="gps" class="form-select">
                                <option value="">{{ trans('labels.all') }}</option>
                                <option value="set" {{ request('gps') === 'set' ? 'selected' : '' }}>{{ trans('labels.gps_set') }}</option>
                                <option value="missing" {{ request('gps') === 'missing' ? 'selected' : '' }}>{{ trans('labels.gps_missing') }}</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.review') }}</label>
                            <select name="review" class="form-select">
                                <option value="">{{ trans('labels.all') }}</option>
                                <option value="pending" {{ request('review') === 'pending' ? 'selected' : '' }}>{{ trans('labels.pending') }}</option>
                                <option value="verified" {{ request('review') === 'verified' ? 'selected' : '' }}>{{ trans('labels.verified') }}</option>
                                <option value="rejected" {{ request('review') === 'rejected' ? 'selected' : '' }}>{{ trans('labels.rejected') }}</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-secondary px-4 rounded-start-5 rounded-end-5">
                                <i class="fa-solid fa-filter mx-1"></i>{{ trans('labels.filter') }}</button>
                            <a href="{{ URL::to('admin/locations') }}" class="btn btn-light px-4 rounded-start-5 rounded-end-5">
                                <i class="fa-solid fa-rotate-left mx-1"></i>{{ trans('labels.reset') }}</a>
                        </div>
                    </form>
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
                                    <td>{{ trans('labels.business') }}</td>
                                    <td>{{ trans('labels.system') }}</td>
                                    <td>{{ trans('labels.location') }}</td>
                                    <td>{{ trans('labels.branches') }} / {{ trans('labels.plan') }}</td>
                                    <td>{{ trans('labels.coverage') }}</td>
                                    <td>GPS</td>
                                    <td>{{ trans('labels.marketplace') }}</td>
                                    <td>{{ trans('labels.action') }}</td>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($branches as $b)
                                    @php
                                        $v = $vendors[$b->vendor_id] ?? null;
                                        $u = $usage[$b->vendor_id] ?? ['limit' => 1, 'used' => 1];
                                        $gps = $b->gpsState();
                                        $mk = $b->marketplaceState($v);
                                    @endphp
                                    <tr class="fs-7">
                                        <td>
                                            <a href="{{ URL::to('admin/users/record-' . $b->vendor_id) }}" class="fw-500 text-decoration-none">
                                                {{ $b->trade_name ?: $b->vendor_name }}</a>
                                            <div class="text-muted">{{ $b->vendor_code }} · {{ $b->name }}
                                                @if ($b->is_primary == 1)<span class="badge bg-success">{{ trans('labels.primary') }}</span>@endif
                                                @if ($b->is_sandbox == 1)<span class="badge bg-secondary">{{ trans('labels.sandbox') }}</span>@endif
                                            </div>
                                        </td>
                                        <td>{{ \App\Helpers\Systems::label($b->system) }}</td>
                                        <td>
                                            {{ trim(($b->city ?: '—') . ' · ' . ($b->area ?: '—'), ' ·') }}
                                            <div class="text-muted">{{ \Illuminate\Support\Str::limit($b->address, 38) ?: ($b->country ?: '—') }}</div>
                                        </td>
                                        <td>{{ $u['used'] }} {{ trans('labels.of_2') }} {{ $u['limit'] === null ? '∞' : $u['limit'] }}</td>
                                        <td>{{ $b->coverageLabel($b->system) }}</td>
                                        <td>
                                            <span class="{{ $gps['class'] }} fw-500">{{ $gps['text'] }}</span>
                                            @if ($b->latitude && $b->longitude)
                                                <a href="https://maps.google.com/?q={{ $b->latitude }},{{ $b->longitude }}" target="_blank" class="mx-1">
                                                    <i class="fa-solid fa-map-pin"></i></a>
                                            @endif
                                        </td>
                                        <td><span class="badge {{ $mk['class'] }}">{{ $mk['text'] }}</span></td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a class="btn btn-sm btn-primary btn-size" tooltip="{{ trans('labels.view') }}"
                                                    href="{{ URL::to('/' . $b->slug) }}" target="_blank"><i class="fa-regular fa-eye"></i></a>
                                                @if ($b->review_status !== 'verified')
                                                    <form method="POST" action="{{ URL::to('admin/locations/review-' . $b->id) }}" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="status" value="verified">
                                                        <button class="btn btn-sm btn-success btn-size" tooltip="{{ trans('labels.approve_location') }}"
                                                            @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif>
                                                            <i class="fa-solid fa-check"></i></button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ URL::to('admin/locations/review-' . $b->id) }}" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="status" value="rejected">
                                                        <button class="btn btn-sm btn-danger btn-size" tooltip="{{ trans('labels.reject_location') }}"
                                                            @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif>
                                                            <i class="fa-solid fa-xmark"></i></button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-muted fs-7">{{ trans('labels.no_records') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
