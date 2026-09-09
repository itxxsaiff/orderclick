{{-- Add-ons & services: every extra Order Click service this vendor bought or was given. --}}
<div class="card border-0 box-shadow">
    <div class="card-body">
        <h6 class="mb-3">{{ trans('labels.addons_services') }}</h6>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr class="fs-7 fw-500">
                        <td>{{ trans('labels.name') }}</td>
                        <td>{{ trans('labels.type') }}</td>
                        <td>{{ trans('labels.used') }} / {{ trans('labels.quantity') }}</td>
                        <td>{{ trans('labels.price') }}</td>
                        <td>{{ trans('labels.status') }}</td>
                        <td>{{ trans('labels.actor') }}</td>
                        <td>{{ trans('labels.created_date') }}</td>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($addons as $a)
                        <tr class="fs-7">
                            <td>{{ $a->name }}
                                @if ($a->deliverable_link)
                                    <a href="{{ $a->deliverable_link }}" target="_blank" class="mx-1"><i class="fa-solid fa-link"></i></a>
                                @endif
                            </td>
                            <td><span class="badge {{ $a->source === 'plan' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($a->source) }}</span></td>
                            <td>{{ rtrim(rtrim(number_format($a->used, 2), '0'), '.') }} / {{ rtrim(rtrim(number_format($a->quantity, 2), '0'), '.') }}</td>
                            <td>{{ $a->currency }} {{ number_format($a->price, 2) }}</td>
                            <td>
                                <span class="badge {{ $a->status === 'delivered' ? 'bg-success' : ($a->status === 'cancelled' ? 'bg-danger' : 'bg-warning') }}">
                                    {{ ucwords(str_replace('_', ' ', $a->status)) }}</span>
                            </td>
                            <td>{{ $a->assigned_to ?: '—' }}</td>
                            <td>{{ $a->ordered_at ? date('d M Y', strtotime($a->ordered_at)) : '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-muted fs-7">{{ trans('labels.no_records') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
