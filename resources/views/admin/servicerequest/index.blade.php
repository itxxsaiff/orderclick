@extends('admin.layout.default')
@section('content')
    @php $isAr = app()->getLocale() === 'ar'; @endphp
    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-12">
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.service_requests') }}</h5>
            @include('admin.layout.breadcrumb')
        </div>
    </div>

    <div class="card border-0 box-shadow mb-7">
        <div class="card-body">
            @if (count($requests) > 0)
                @php $badge = [1 => 'warning', 2 => 'info', 3 => 'primary', 4 => 'success', 5 => 'danger']; @endphp
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr class="text-uppercase fs-8 text-muted">
                                <th>#</th>
                                <th>{{ trans('labels.customer_2') }}</th>
                                <th>{{ trans('labels.service') }}</th>
                                <th>{{ trans('labels.address') }}</th>
                                <th>{{ trans('labels.preferred') }}</th>
                                <th>{{ trans('labels.mobile') }}</th>
                                <th>{{ trans('labels.status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($requests as $r)
                                <tr>
                                    <td class="fw-600">{{ $r->request_number }}</td>
                                    <td>{{ $r->customer_name }}
                                        @if ($r->email)<br><small class="text-muted">{{ $r->email }}</small>@endif
                                    </td>
                                    <td>{{ $r->service_name }}</td>
                                    <td><small>{{ $r->address }}</small></td>
                                    <td>{{ $r->preferred_date }}
                                        @if ($r->preferred_time)<br><small class="text-muted">{{ $r->preferred_time }}</small>@endif
                                    </td>
                                    <td>
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $r->mobile) }}" target="_blank" class="text-success">
                                            <i class="fa-brands fa-whatsapp"></i> {{ $r->mobile }}
                                        </a>
                                    </td>
                                    <td>
                                        <form action="{{ URL::to('admin/service-requests/status') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $r->id }}">
                                            <select name="status" class="form-select form-select-sm border-{{ $badge[$r->status] ?? 'secondary' }}" style="min-width:140px;" onchange="this.form.submit()">
                                                @foreach (\App\Models\ServiceRequest::STATUS as $k => $v)
                                                    <option value="{{ $k }}" {{ $r->status == $k ? 'selected' : '' }}>{{ $v }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                                @if ($r->notes)
                                    <tr class="bg-light">
                                        <td></td>
                                        <td colspan="6"><small class="text-muted"><i class="fa-solid fa-note-sticky me-1"></i>{{ $r->notes }}</small></td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="fa-solid fa-clipboard-list fs-1 mb-3 d-block"></i>
                    {{ $isAr ? 'لا توجد طلبات بعد. ستظهر هنا عندما يرسل العملاء طلباً.' : 'No requests yet. They\'ll appear here when customers send one.' }}
                </div>
            @endif
        </div>
    </div>
@endsection
