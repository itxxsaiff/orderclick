{{-- Analytics & Engagement.
     The funnel keeps WhatsApp click, started and completed as three SEPARATE counts — a click is
     never treated as a completed transaction. Everything is computed from vendor_events and the
     real order/booking/request tables; nothing here is an editable counter. --}}
<div class="card border-0 box-shadow mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="tab" value="analytics">
            <div class="col-6 col-md-3">
                <label class="form-label fs-7 mb-1">{{ trans('labels.from_date') ?? 'From' }}</label>
                <input type="date" name="from" value="{{ $analytics['from'] }}" class="form-control">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label fs-7 mb-1">{{ trans('labels.to_date') ?? 'To' }}</label>
                <input type="date" name="to" value="{{ $analytics['to'] }}" class="form-control">
            </div>
            <div class="col-12 col-md-3">
                <button class="btn btn-secondary px-4 rounded-start-5 rounded-end-5">{{ trans('labels.filter') }}</button>
            </div>
        </form>
    </div>
</div>

@if (!$analytics['has_events'])
    <div class="alert alert-warning fs-7">{{ trans('messages.no_analytics_yet') }}</div>
@endif

<div class="row g-3">
    @php
        $metrics = [
            trans('labels.visitors')               => 'visitors',
            trans('labels.sessions')               => 'sessions',
            trans('labels.page_views')             => 'page_views',
            trans('labels.whatsapp_clicks')        => 'whatsapp_clicks',
            trans('labels.transactions_started')   => 'started',
            trans('labels.completed_transactions') => 'transactions',
        ];
    @endphp
    @foreach ($metrics as $label => $key)
        @php
            $now = $analytics['current'][$key];
            $was = $analytics['previous'][$key];
            $delta = $was > 0 ? round((($now - $was) / $was) * 100) : ($now > 0 ? 100 : 0);
        @endphp
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 box-shadow h-100">
                <div class="card-body py-3">
                    <div class="text-muted fs-7">{{ $label }}</div>
                    <div class="fs-4 fw-600 color-changer">{{ $now }}</div>
                    <div class="fs-7 {{ $delta > 0 ? 'text-success' : ($delta < 0 ? 'text-danger' : 'text-muted') }}">
                        {{ $delta > 0 ? '+' : '' }}{{ $delta }}% {{ trans('labels.vs_previous') }}
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <div class="col-6 col-md-4 col-xl-2">
        <div class="card border-0 box-shadow h-100">
            <div class="card-body py-3">
                <div class="text-muted fs-7">{{ trans('labels.conversion_rate') }}</div>
                <div class="fs-4 fw-600 color-changer">{{ $analytics['current']['conversion'] }}%</div>
                <div class="fs-7 text-muted">{{ trans('labels.completed_transactions') }} / {{ trans('labels.visitors') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- The funnel, stated explicitly so a click is never read as an order. --}}
<div class="card border-0 box-shadow mt-3">
    <div class="card-body">
        <h6 class="mb-3">{{ trans('labels.analytics_engagement') }}</h6>
        <div class="d-flex flex-wrap align-items-center gap-3 fs-7">
            <span class="badge bg-secondary px-3 py-2">{{ trans('labels.whatsapp_clicks') }}: {{ $analytics['current']['whatsapp_clicks'] }}</span>
            <i class="fa-solid fa-arrow-right text-muted"></i>
            <span class="badge bg-warning px-3 py-2">{{ trans('labels.transactions_started') }}: {{ $analytics['current']['started'] }}</span>
            <i class="fa-solid fa-arrow-right text-muted"></i>
            <span class="badge bg-success px-3 py-2">{{ trans('labels.completed_transactions') }}: {{ $analytics['current']['transactions'] }}</span>
        </div>
    </div>
</div>
