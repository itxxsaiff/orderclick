@extends('admin.layout.default')
@section('content')
    @php $isAr = app()->getLocale() === 'ar'; @endphp
    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-12">
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.bookings') }}</h5>
            @include('admin.layout.breadcrumb')
        </div>
    </div>

    <div class="card border-0 box-shadow mb-7">
        <div class="card-body">
            @if (count($bookings) > 0)
                @php $badge = [1 => 'warning', 2 => 'info', 3 => 'success', 4 => 'danger']; @endphp
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr class="text-uppercase fs-8 text-muted">
                                <th>#</th>
                                <th>{{ trans('labels.customer_2') }}</th>
                                <th>{{ trans('labels.service') }}</th>
                                <th>{{ trans('labels.date_time') }}</th>
                                <th>{{ trans('labels.amount_payment') }}</th>
                                <th>{{ trans('labels.mobile') }}</th>
                                <th>{{ trans('labels.status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookings as $b)
                                <tr>
                                    <td class="fw-600">{{ $b->booking_number }}</td>
                                    <td>{{ $b->customer_name }}
                                        @if ($b->email)<br><small class="text-muted">{{ $b->email }}</small>@endif
                                    </td>
                                    <td>{{ $b->service_name }}
                                        @if ($b->staff)<br><small class="text-muted"><i class="fa-solid fa-user me-1"></i>{{ $b->staff }}</small>@endif
                                    </td>
                                    <td>{{ $b->booking_date }}
                                        @if ($b->booking_time)<br><small class="text-muted">{{ $b->booking_time }}</small>@endif
                                    </td>
                                    <td>
                                        @if ($b->amount > 0)
                                            <span class="fw-600">{{ helper::currency_formate($b->amount, Auth::user()->type == 4 ? Auth::user()->vendor_id : Auth::user()->id) }}</span><br>
                                        @endif
                                        <small class="text-muted">{{ $b->payment_method ?: '—' }}</small>
                                    </td>
                                    <td>
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $b->mobile) }}" target="_blank" class="text-success">
                                            <i class="fa-brands fa-whatsapp"></i> {{ $b->mobile }}
                                        </a>
                                    </td>
                                    <td>
                                        <form action="{{ URL::to('admin/bookings/status') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $b->id }}">
                                            <select name="status" class="form-select form-select-sm border-{{ $badge[$b->status] ?? 'secondary' }}" style="min-width:130px;" onchange="this.form.submit()">
                                                @foreach (\App\Models\Booking::STATUS as $k => $v)
                                                    <option value="{{ $k }}" {{ $b->status == $k ? 'selected' : '' }}>{{ $v }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                                @if ($b->notes)
                                    <tr class="bg-light">
                                        <td></td>
                                        <td colspan="6"><small class="text-muted"><i class="fa-solid fa-note-sticky me-1"></i>{{ $b->notes }}</small></td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="fa-solid fa-calendar-xmark fs-1 mb-3 d-block"></i>
                    {{ $isAr ? 'لا توجد حجوزات بعد. ستظهر هنا عندما يحجز العملاء.' : 'No bookings yet. They\'ll appear here when customers book.' }}
                </div>
            @endif
        </div>
    </div>
@endsection
