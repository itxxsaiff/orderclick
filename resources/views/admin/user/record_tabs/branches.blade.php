{{-- Locations & Branches. Every GPS/address correction is written to the audit log. --}}
<div class="card border-0 box-shadow">
    <div class="card-body">
        <h6 class="mb-3">{{ trans('labels.locations_branches') }}</h6>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr class="fs-7 fw-500">
                        <td>{{ trans('labels.name') }}</td>
                        <td>{{ trans('labels.city') }} / {{ trans('labels.area') }}</td>
                        <td>{{ trans('labels.address') }}</td>
                        <td>GPS</td>
                        <td>{{ trans('labels.mobile') }}</td>
                        <td>{{ trans('labels.status') }}</td>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($branches as $b)
                        <tr class="fs-7">
                            <td>{{ $b->name }} @if ($b->is_primary == 1)<span class="badge bg-success">1</span>@endif</td>
                            <td>{{ trim(($b->city ?: '—') . ' / ' . ($b->area ?: '—'), ' /') }}</td>
                            <td>{{ $b->address ?: '—' }}</td>
                            <td>
                                @if ($b->latitude && $b->longitude)
                                    <a href="https://maps.google.com/?q={{ $b->latitude }},{{ $b->longitude }}" target="_blank">
                                        {{ $b->latitude }}, {{ $b->longitude }}</a>
                                @else
                                    <span class="text-danger">{{ trans('labels.no_records') }}</span>
                                @endif
                            </td>
                            <td>{{ $b->phone ?: '—' }}</td>
                            <td><span class="badge {{ $b->is_available == 1 ? 'bg-success' : 'bg-danger' }}">
                                {{ $b->is_available == 1 ? trans('labels.active') : trans('labels.inactive') }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-muted fs-7">
                                {{ trans('labels.no_records') }} —
                                {{ trans('labels.this_vendor_is_still_on_the_single') }}
                                @if (optional($settings)->address)
                                    <div class="mt-2">{{ $settings->address }}</div>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
