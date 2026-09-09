{{-- Plan, billing, limits & usage. Warnings at 80% and 100%, with Upgrade always reachable. --}}
<div class="card border-0 box-shadow mb-3">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h6 class="mb-0">{{ trans('labels.entitlement') }}</h6>
            <a href="{{ URL::to('admin/plan') }}" class="btn btn-sm btn-secondary rounded-start-5 rounded-end-5">
                {{ trans('labels.upgrade_plan') }}</a>
        </div>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr class="fs-7 fw-500">
                        <td>{{ trans('labels.entitlement') }}</td>
                        <td>{{ trans('labels.used') }}</td>
                        <td>{{ trans('labels.remaining') }}</td>
                        <td style="width:35%">{{ trans('labels.plan_usage') }}</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($entitlements as $e)
                        <tr class="fs-7">
                            <td>{{ $e['label'] }}</td>
                            <td>{{ $e['used'] }}</td>
                            <td>{{ $e['remaining'] === null ? trans('labels.unlimited') : $e['remaining'] }}</td>
                            <td>
                                @if ($e['percent'] === null)
                                    <span class="text-muted">{{ trans('labels.unlimited') }}</span>
                                @else
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height:6px;">
                                            <div class="progress-bar {{ $e['percent'] >= 100 ? 'bg-danger' : ($e['percent'] >= 80 ? 'bg-warning' : 'bg-success') }}"
                                                style="width: {{ $e['percent'] }}%"></div>
                                        </div>
                                        <span class="fw-500">{{ $e['percent'] }}%</span>
                                    </div>
                                    @if ($e['percent'] >= 80)
                                        <span class="badge {{ $e['percent'] >= 100 ? 'bg-danger' : 'bg-warning' }} mt-1">
                                            {{ $e['percent'] >= 100 ? 'Limit reached' : 'Nearing limit' }}</span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card border-0 box-shadow">
    <div class="card-body">
        <h6 class="mb-3">{{ trans('labels.transaction') }}</h6>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr class="fs-7 fw-500">
                        <td>#</td>
                        <td>{{ trans('labels.plan') }}</td>
                        <td>{{ trans('labels.amount') }}</td>
                        <td>{{ trans('labels.payment_date') }}</td>
                        <td>{{ trans('labels.subscription_start_date') }}</td>
                        <td>{{ trans('labels.expiry_date') }}</td>
                        <td>{{ trans('labels.status') }}</td>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $t)
                        <tr class="fs-7">
                            <td>{{ $t->transaction_number }}</td>
                            <td>{{ $t->plan_name }}</td>
                            <td>{!! helper::currency_formate($t->grand_total, 1) !!}</td>
                            <td>{{ $t->purchase_date ? date('d M Y', strtotime($t->purchase_date)) : '—' }}</td>
                            <td>{{ $t->start_date ? date('d M Y', strtotime($t->start_date)) : trans('labels.not_started_yet') }}</td>
                            <td>{{ $t->expire_date ? date('d M Y', strtotime($t->expire_date)) : '—' }}</td>
                            <td>
                                <span class="badge {{ $t->status == 2 ? 'bg-success' : ($t->status == 3 ? 'bg-danger' : 'bg-warning') }}">
                                    {{ $t->status == 2 ? 'Paid' : ($t->status == 3 ? 'Rejected' : 'Pending') }}</span>
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
