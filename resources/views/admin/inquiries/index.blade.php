@extends('admin.layout.default')
@php
    if (Auth::user()->type == 4) {
        $vendor_id = Auth::user()->vendor_id;
    } else {
        $vendor_id = Auth::user()->id;
    }
@endphp
@section('content')
    <div class="row justify-content-between align-items-center">
        <div class="col-12">
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.inquiries') }}</h5>
            @include('admin.layout.breadcrumb')
        </div>
    </div>

    <div class="col-12 mb-7 mt-3">
        <div class="card border-0 box-shadow">
                {{-- Filters. There is deliberately no Add button: inquiries arrive automatically from the
         website's contact and support forms. --}}
    <div class="col-12 mb-3">
        <div class="card border-0 box-shadow">
            <div class="card-body">
                <form method="GET" action="{{ URL::to('admin/inquiries') }}" class="row g-3 align-items-end">
                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.search') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0"
                                placeholder="{{ trans('labels.name') }} / {{ trans('labels.email') }}">
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.inquiry_type') }}</label>
                        <select name="type" class="form-select">
                            <option value="">{{ trans('labels.all') }}</option>
                            @foreach (\App\Models\Contact::typeOptions() as $ocK => $ocL)
                                <option value="{{ $ocK }}" {{ request('type') === $ocK ? 'selected' : '' }}>{{ $ocL }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.related_system') }}</label>
                        <select name="system" class="form-select">
                            <option value="">{{ trans('labels.all') }}</option>
                            @foreach (\App\Helpers\Systems::all() as $ocS)
                                <option value="{{ $ocS['key'] }}" {{ request('system') === $ocS['key'] ? 'selected' : '' }}>{{ $ocS['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.status') }}</label>
                        <select name="status" class="form-select">
                            <option value="">{{ trans('labels.all') }}</option>
                            @foreach (\App\Models\Contact::statusOptions() as $ocK => $ocL)
                                <option value="{{ $ocK }}" {{ request('status') === $ocK ? 'selected' : '' }}>{{ $ocL }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-4 col-lg-1">
                        <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.archive') }}</label>
                        <select name="archived" class="form-select">
                            <option value="">{{ trans('labels.no') }}</option>
                            <option value="1" {{ request('archived') === '1' ? 'selected' : '' }}>{{ trans('labels.yes') }}</option>
                        </select>
                    </div>
                    <div class="col-12 col-lg-2 d-flex gap-2">
                        <button type="submit" class="btn btn-secondary px-4 rounded-start-5 rounded-end-5">
                            <i class="fa-solid fa-filter mx-1"></i>{{ trans('labels.filter') }}</button>
                        <a href="{{ URL::to('admin/inquiries') }}" class="btn btn-light px-4 rounded-start-5 rounded-end-5">
                            <i class="fa-solid fa-rotate-left mx-1"></i>{{ trans('labels.reset') }}</a>
                    </div>
                    <div class="col-12">
                        <span class="badge bg-secondary">{{ trans('labels.new') }}: {{ $counts['new'] }}</span>
                        <span class="badge bg-warning">{{ trans('labels.in_progress') }}: {{ $counts['in_progress'] }}</span>
                        <span class="badge bg-light text-dark">{{ trans('labels.archive') }}: {{ $counts['archived'] }}</span>
                    </div>
                </form>
            </div>
        </div>
    </div>

<div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered py-3 zero-configuration w-100">
                        <thead>
                            <tr class="fw-500 fs-15">
                                <td>{{ trans('labels.srno') }}</td>
                                <td>{{ trans('labels.name') }}</td>
                                <td>{{ trans('labels.email') }}</td>
                                <td>{{ trans('labels.mobile') }}</td>
                                <td>{{ trans('labels.message') }}</td>
                                <td>{{ trans('labels.inquiry_type') }}</td>
                                <td>{{ trans('labels.related_system') }}</td>
                                <td>{{ trans('labels.status') }}</td>
                                <td>{{ trans('labels.account') }}</td>
                                <td>{{ trans('labels.created_date') }}</td>
                                <td>{{ trans('labels.updated_date') }}</td>
                                <td>{{ trans('labels.action') }}</td>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i=1; @endphp
                            @foreach ($getinquiries as $inquiry)
                                <tr class="fs-7 align-middle">
                                    <td>@php echo $i++ @endphp</td>
                                    <td>{{ $inquiry->name }}</td>
                                    <td>{{ $inquiry->email }}</td>
                                    <td>{{ $inquiry->mobile }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($inquiry->message, 60) }}</td>
                                    <td class="fs-7">{{ $inquiry->typeLabel() }}</td>
                                    <td class="fs-7">{{ $inquiry->systemLabel() }}</td>
                                    <td><span class="badge {{ $inquiry->statusClass() }}">{{ $inquiry->statusLabel() }}</span></td>
                                    <td class="fs-7">
                                        @if ($inquiry->linked_user_id && $inquiry->linkedUser)
                                            {{-- Came from a registered account. --}}
                                            <a href="{{ URL::to('admin/users/record-' . $inquiry->linked_user_id) }}">
                                                {{ $inquiry->linkedUser->name }}</a>
                                        @else
                                            <span class="text-muted">{{ trans('labels.general') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ helper::date_format($inquiry->created_at, $vendor_id) }}<br>
                                        {{ helper::time_format($inquiry->created_at, $vendor_id) }}

                                    </td>
                                    <td>{{ helper::date_format($inquiry->updated_at, $vendor_id) }}<br>
                                        {{ helper::time_format($inquiry->updated_at, $vendor_id) }}

                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ URL::to('admin/inquiries/view-' . $inquiry->id) }}"
                                                class="btn btn-dark btn-sm btn-size" tooltip="{{ trans('labels.view') }}">
                                                <i class="fa-regular fa-eye"></i></a>

                                            <a href="{{ $inquiry->replyEmailUrl() }}"
                                                class="btn btn-info btn-sm btn-size" tooltip="{{ trans('labels.reply_by_email') }}">
                                                <i class="fa-regular fa-envelope"></i></a>

                                            @if ($ocWa = $inquiry->replyWhatsappUrl())
                                                <a href="{{ $ocWa }}" target="_blank"
                                                    class="btn btn-success btn-sm btn-size" tooltip="{{ trans('labels.reply_by_whatsapp') }}">
                                                    <i class="fa-brands fa-whatsapp"></i></a>
                                            @endif

                                            {{-- Archive, not delete — a support conversation is never lost. --}}
                                            <a @if (env('Environment') == 'sendbox') onclick="myFunction()" @else onclick="statusupdate('{{ URL::to('admin/inquiries/archive-' . $inquiry->id) }}')" @endif
                                                class="btn btn-warning btn-sm btn-size"
                                                tooltip="{{ $inquiry->isArchived() ? trans('labels.restore') : trans('labels.archive') }}">
                                                <i class="fa-regular {{ $inquiry->isArchived() ? 'fa-rotate-left' : 'fa-box-archive' }}"></i></a>

                                            <a @if (env('Environment') == 'sendbox') onclick="myFunction()" @else onclick="deletedata('{{ URL::to('admin/inquiries/delete-' . $inquiry->id) }}')" @endif
                                                class="btn btn-danger btn-sm btn-size {{ Auth::user()->type == 4 ? (helper::check_access('role_inquiries', Auth::user()->role_id, $vendor_id, 'delete') == 1 ? '' : 'd-none') : '' }}"
                                                tooltip="{{ trans('labels.delete') }}"> <i
                                                    class="fa-regular fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
