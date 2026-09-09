@extends('admin.layout.default')
@section('content')
    @php
        $ar = app()->getLocale() === 'ar';
        $recordUrl = fn($t) => URL::to('admin/users/record-' . $vendor->id) . '?tab=' . $t;
        $tabs = [
            'overview'     => trans('labels.overview'),
            'verification' => trans('labels.business_verification'),
            'branches'     => trans('labels.locations_branches'),
            'whatsapp'     => 'WhatsApp',
            'billing'      => trans('labels.plan_billing'),
            'addons'       => trans('labels.addons_services'),
            'analytics'    => trans('labels.analytics_engagement'),
            'audit'        => trans('labels.audit_security'),
        ];
    @endphp

    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-12 col-md-6">
            <h5 class="pages-title color-changer fs-2">{{ $vendor->trade_name ?: $vendor->name }}</h5>
            @include('admin.layout.breadcrumb')
        </div>
        <div class="col-12 col-md-6">
            <div class="d-flex justify-content-end gap-2 flex-wrap">
                <a href="{{ URL::to('admin/users') }}" class="btn btn-light rounded-start-5 rounded-end-5">
                    <i class="fa-solid fa-arrow-left mx-1"></i>{{ trans('labels.users') }}</a>
                <a href="{{ URL::to('admin/users/edit-' . $vendor->id) }}" class="btn btn-info rounded-start-5 rounded-end-5">
                    <i class="fa fa-pen-to-square mx-1"></i>{{ trans('labels.edit') }}</a>
                <button type="button" class="btn btn-dark rounded-start-5 rounded-end-5" data-bs-toggle="modal" data-bs-target="#ocImpersonate">
                    <i class="fa-regular fa-arrow-right-to-bracket mx-1"></i>{{ trans('labels.login_as_vendor') }}</button>
                <a href="{{ URL::to('/' . $vendor->slug) }}" target="_blank" class="btn btn-primary rounded-start-5 rounded-end-5">
                    <i class="fa-regular fa-eye mx-1"></i>{{ trans('labels.view') }}</a>
                @if ($vendor->archived_at)
                    <a href="javascript:void(0)" onclick="statusupdate('{{ URL::to('admin/users/restore-' . $vendor->id) }}')"
                        class="btn btn-success rounded-start-5 rounded-end-5">{{ trans('labels.restore') }}</a>
                @else
                    <a href="javascript:void(0)" onclick="statusupdate('{{ URL::to('admin/users/archive-' . $vendor->id) }}')"
                        class="btn btn-danger rounded-start-5 rounded-end-5">{{ trans('labels.archive') }}</a>
                @endif
            </div>
        </div>
    </div>

    {{-- Header: identity + the four independent status axes. --}}
    <div class="row">
        <div class="col-12 mb-3">
            <div class="card border-0 box-shadow">
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-3 align-items-center mb-3">
                        <img src="{{ helper::image_path($vendor->image) }}" class="img-fluid rounded hw-50" alt="">
                        <div class="flex-grow-1">
                            <div class="fs-6 fw-600 color-changer">{{ $vendor->trade_name ?: $vendor->name }}</div>
                            <div class="fs-7 color-changer">
                                <span class="text-muted">{{ trans('labels.vendor_id') }}: {{ $vendor->vendor_code }}</span>
                                · {{ $summary['system_label'] }}
                                <span class="badge bg-secondary">{{ trans('labels.locked_after_payment') }}</span>
                                @if ($activity) · {{ $activity->display_name }} @endif
                                @if ($specialization) · {{ $specialization->display_name }} @endif
                                @if ($vendor->country) · {{ $vendor->country }} @endif
                            </div>
                        </div>
                        @if ($vendor->is_sandbox == 1)
                            <span class="badge bg-secondary">{{ trans('labels.sandbox') }}</span>
                        @endif
                        <span class="badge {{ $alert['class'] }}"><i class="fa-solid fa-bell mx-1"></i>{{ $alert['text'] }}</span>
                    </div>

                    <div class="row g-3">
                        @php
                            $axes = [
                                trans('labels.account_status') => $statuses['account'],
                                trans('labels.verification')   => $statuses['verification'],
                                trans('labels.subscription')   => $statuses['subscription'],
                                trans('labels.page_status')    => $statuses['page'],
                            ];
                        @endphp
                        @foreach ($axes as $lbl => $meta)
                            <div class="col-6 col-md-3">
                                <div class="text-muted fs-7">{{ $lbl }}</div>
                                <span class="badge {{ $meta['class'] }} px-3 py-2">{{ $meta['text'] }}</span>
                            </div>
                        @endforeach

                        <div class="col-6 col-md-3">
                            <div class="text-muted fs-7">{{ trans('labels.current_plan') }}</div>
                            <div class="fw-semibold">{{ $summary['plan_name'] }}
                                <a href="{{ URL::to('admin/plan') }}" class="fs-7">{{ trans('labels.upgrade_plan') }}</a>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="text-muted fs-7">{{ trans('labels.subscription_start_date') }}</div>
                            <div class="fw-semibold">{{ $vendor->subscription_start_date ? date('d M Y', strtotime($vendor->subscription_start_date)) : trans('labels.not_started_yet') }}</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="text-muted fs-7">{{ trans('labels.subscription_end_date') }}</div>
                            <div class="fw-semibold">{{ $vendor->subscription_end_date ? date('d M Y', strtotime($vendor->subscription_end_date)) : '—' }}</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="text-muted fs-7">{{ trans('labels.payment_date') }}</div>
                            <div class="fw-semibold">{{ $vendor->payment_date ? date('d M Y', strtotime($vendor->payment_date)) : '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="col-12 mb-3">
            <div class="d-flex flex-wrap gap-2">
                @foreach ($tabs as $key => $label)
                    <a href="{{ $recordUrl($key) }}"
                        class="btn btn-sm rounded-start-5 rounded-end-5 px-3 {{ $tab === $key ? 'btn-secondary' : 'btn-light' }}">{{ $label }}</a>
                @endforeach
            </div>
        </div>

        <div class="col-12 mb-7">
            @include('admin.user.record_tabs.' . $tab)
        </div>
    </div>

    {{-- Login-as-vendor needs a reason; it is written to the audit log. --}}
    <div class="modal fade" id="ocImpersonate" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="GET" action="{{ URL::to('admin/users/login-' . $vendor->id) }}">
                <div class="modal-header">
                    <h6 class="modal-title">{{ trans('labels.login_as_vendor') }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">{{ trans('labels.impersonation_reason') }}<span class="text-danger"> *</span></label>
                    <textarea name="reason" class="form-control" rows="3" required></textarea>
                    <small class="text-muted d-block mt-2">
                        {{ $ar ? 'سيتم تسجيل اسم المشرف ووقت الدخول والخروج في سجل التدقيق.' : 'The admin name, reason and login/logout times are recorded in the audit log.' }}
                    </small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ trans('labels.cancel') }}</button>
                    <button type="submit" class="btn btn-dark rounded-start-5 rounded-end-5">{{ trans('labels.login') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
