{{-- Audit & security: old value, new value, actor, actor type, reason, timestamp. --}}
<div class="card border-0 box-shadow">
    <div class="card-body">
        <h6 class="mb-3">{{ trans('labels.audit_security') }}</h6>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr class="fs-7 fw-500">
                        <td>{{ trans('labels.created_date') }}</td>
                        <td>{{ trans('labels.type') }}</td>
                        <td>{{ trans('labels.old_value') }}</td>
                        <td>{{ trans('labels.new_value') }}</td>
                        <td>{{ trans('labels.actor') }}</td>
                        <td>{{ trans('labels.reason') }}</td>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr class="fs-7">
                            <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $log->field)) }}</td>
                            <td class="text-muted">{{ \Illuminate\Support\Str::limit($log->old_value, 40) ?: '—' }}</td>
                            <td class="fw-500">{{ \Illuminate\Support\Str::limit($log->new_value, 40) ?: '—' }}</td>
                            <td>{{ $log->actor_name }} <span class="badge bg-secondary">{{ $log->actor_type }}</span></td>
                            <td>{{ $log->reason ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-muted fs-7">{{ trans('labels.no_records') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
