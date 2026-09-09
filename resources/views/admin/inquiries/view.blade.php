@extends('admin.layout.default')
@section('content')
    @php $vendor_id = Auth::user()->type == 4 ? Auth::user()->vendor_id : Auth::user()->id; @endphp

    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-12 col-md-6">
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.inquiry') }}</h5>
            @include('admin.layout.breadcrumb')
        </div>
        <div class="col-12 col-md-6">
            <div class="d-flex justify-content-end gap-2 flex-wrap">
                <a href="{{ URL::to('admin/inquiries') }}" class="btn btn-light px-4 rounded-start-5 rounded-end-5">
                    <i class="fa-solid fa-arrow-left mx-1"></i>{{ trans('labels.inquiries') }}</a>
                <a href="{{ $inquiry->replyEmailUrl() }}" class="btn btn-info px-4 rounded-start-5 rounded-end-5">
                    <i class="fa-regular fa-envelope mx-1"></i>{{ trans('labels.reply_by_email') }}</a>
                @if ($wa = $inquiry->replyWhatsappUrl())
                    <a href="{{ $wa }}" target="_blank" class="btn btn-success px-4 rounded-start-5 rounded-end-5">
                        <i class="fa-brands fa-whatsapp mx-1"></i>{{ trans('labels.reply_by_whatsapp') }}</a>
                @endif
                <a href="javascript:void(0)" onclick="statusupdate('{{ URL::to('admin/inquiries/archive-' . $inquiry->id) }}')"
                    class="btn btn-warning px-4 rounded-start-5 rounded-end-5">
                    <i class="fa-regular fa-box-archive mx-1"></i>{{ $inquiry->isArchived() ? trans('labels.restore') : trans('labels.archive') }}</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-7 mb-4">
            <div class="card border-0 box-shadow h-100">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                        <h6 class="mb-0">{{ $inquiry->name }}</h6>
                        <span class="badge {{ $inquiry->statusClass() }}">{{ $inquiry->statusLabel() }}</span>
                        @if ($inquiry->isArchived())
                            <span class="badge bg-secondary">{{ trans('labels.archive') }}</span>
                        @endif
                    </div>

                    <div class="row g-2 fs-7 color-changer mb-3">
                        <div class="col-12 col-md-6"><span class="text-muted">{{ trans('labels.email') }}:</span> {{ $inquiry->email }}</div>
                        <div class="col-12 col-md-6"><span class="text-muted">{{ trans('labels.mobile') }}:</span> {{ $inquiry->mobile ?: '—' }}</div>
                        <div class="col-12 col-md-6"><span class="text-muted">{{ trans('labels.inquiry_type') }}:</span> {{ $inquiry->typeLabel() }}</div>
                        <div class="col-12 col-md-6"><span class="text-muted">{{ trans('labels.related_system') }}:</span> {{ $inquiry->systemLabel() }}</div>
                        <div class="col-12 col-md-6"><span class="text-muted">{{ trans('labels.created_date') }}:</span>
                            {{ helper::date_format($inquiry->created_at, $vendor_id) }} {{ helper::time_format($inquiry->created_at, $vendor_id) }}</div>
                        <div class="col-12 col-md-6"><span class="text-muted">{{ trans('labels.account') }}:</span>
                            @if ($inquiry->linked_user_id && $inquiry->linkedUser)
                                <a href="{{ URL::to('admin/users/record-' . $inquiry->linked_user_id) }}">{{ $inquiry->linkedUser->name }}</a>
                            @else
                                {{ trans('labels.general') }}
                            @endif
                        </div>
                    </div>

                    {{-- The complete message, not the truncated list preview. --}}
                    <div class="p-3" style="background:#f5f7f4;border-radius:12px;white-space:pre-wrap;">{{ $inquiry->message }}</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5 mb-4">
            <div class="card border-0 box-shadow h-100">
                <div class="card-body">
                    <h6 class="mb-3">{{ trans('labels.classification') }}</h6>
                    <form method="POST" action="{{ URL::to('admin/inquiries/update-' . $inquiry->id) }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ trans('labels.inquiry_type') }}</label>
                            <select name="inquiry_type" class="form-select">
                                @foreach (\App\Models\Contact::typeOptions() as $k => $l)
                                    <option value="{{ $k }}" {{ ($inquiry->inquiry_type ?: 'general') === $k ? 'selected' : '' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ trans('labels.related_system') }}</label>
                            <select name="related_system" class="form-select">
                                @foreach (\App\Models\Contact::systemOptions() as $k => $l)
                                    <option value="{{ $k }}" {{ (string) $inquiry->related_system === (string) $k ? 'selected' : '' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">{{ trans('messages.related_system_hint') }}</small>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ trans('labels.status') }}</label>
                            <select name="status" class="form-select">
                                @foreach (\App\Models\Contact::statusOptions() as $k => $l)
                                    <option value="{{ $k }}" {{ ($inquiry->status ?: 'new') === $k ? 'selected' : '' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ trans('labels.admin_notes') }}</label>
                            <textarea name="admin_note" rows="4" class="form-control">{{ $inquiry->admin_note }}</textarea>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-secondary px-4 rounded-start-5 rounded-end-5"
                                @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif>
                                {{ trans('labels.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
