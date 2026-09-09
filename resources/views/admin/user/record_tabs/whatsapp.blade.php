{{-- WhatsApp routing: allowed by plan vs used vs remaining, and per-number click counts. --}}
@php
    $plan = $vendor->plan_id ? \App\Models\PricingPlan::find($vendor->plan_id) : null;
    $limits = $plan && $plan->plan_limits ? (is_array($plan->plan_limits) ? $plan->plan_limits : json_decode($plan->plan_limits, true)) : [];
    $waLimit = ($limits['whatsapp']['type'] ?? '1') == '2' ? -1 : ($limits['whatsapp']['count'] ?? null);
    $waUsed = $numbers->count();
@endphp
<div class="row g-3 mb-3">
    @php
        $tiles = [
            ($ar ?? false) ? 'المسموح' : 'Allowed by plan' => ($waLimit === -1 ? trans('labels.unlimited') : ($waLimit ?? '—')),
            trans('labels.used') => $waUsed,
            trans('labels.remaining') => ($waLimit === null || $waLimit === -1 ? trans('labels.unlimited') : max(0, $waLimit - $waUsed)),
        ];
    @endphp
    @foreach ($tiles as $lbl => $val)
        <div class="col-6 col-md-4">
            <div class="card border-0 box-shadow"><div class="card-body py-3">
                <div class="text-muted fs-7">{{ $lbl }}</div>
                <div class="fs-4 fw-600 color-changer">{{ $val }}</div>
            </div></div>
        </div>
    @endforeach
</div>

<div class="card border-0 box-shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr class="fs-7 fw-500">
                        <td>{{ trans('labels.mobile') }}</td>
                        <td>{{ trans('labels.description') }}</td>
                        <td>{{ trans('labels.branches') }}</td>
                        <td>{{ trans('labels.time') }}</td>
                        <td>{{ trans('labels.whatsapp_clicks') }}</td>
                        <td>{{ trans('labels.status') }}</td>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($numbers as $n)
                        <tr class="fs-7">
                            <td>{{ $n->number }} @if ($n->is_primary == 1)<span class="badge bg-success">1</span>@endif</td>
                            <td>{{ $n->label ?: '—' }}{{ $n->assigned_to ? ' · ' . $n->assigned_to : '' }}</td>
                            <td>{{ optional($n->branch)->name ?: '—' }}</td>
                            <td>{{ $n->start_time && $n->end_time ? $n->start_time . ' – ' . $n->end_time : '—' }}</td>
                            <td>{{ $n->click_count }}</td>
                            <td><span class="badge {{ $n->is_available == 1 ? 'bg-success' : 'bg-danger' }}">
                                {{ $n->is_available == 1 ? trans('labels.active') : trans('labels.inactive') }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-muted fs-7">
                                {{ trans('labels.no_records') }}
                                @if (optional($settings)->whatsapp_number)
                                    — {{ app()->getLocale() === 'ar' ? 'الرقم الرئيسي:' : 'primary number:' }}
                                    <span class="fw-500">{{ $settings->whatsapp_number }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
