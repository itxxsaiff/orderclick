@extends('admin.layout.default')
@php
    if (Auth::user()->type == 4) {
        $vendor_id = Auth::user()->vendor_id;
    } else {
        $vendor_id = Auth::user()->id;
    }
@endphp
@section('content')
    @php $ocIsAdmin = Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1); @endphp

    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-12 col-md-8">
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.transaction') }}</h5>
            @include('admin.layout.breadcrumb')
        </div>
    </div>

    {{-- Filters: one aligned grid, each field labelled. Vendor / System / Method / Status /
         From / To / Search all submit together, so a filter never clears the others. --}}
    <div class="col-12 mb-3">
        <div class="card border-0 box-shadow">
            <div class="card-body">
                <form action="{{ URL::to('/admin/transaction') }}" method="get">
                    <div class="row g-3 align-items-end">
                        @if ($ocIsAdmin)
                            <div class="col-12 col-sm-6 col-lg-4 col-xl-2">
                                <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.name') }}</label>
                                <select class="form-select" name="vendor">
                                    <option value="">{{ trans('labels.all') }}</option>
                                    @foreach ($vendors as $vendor)
                                        <option value="{{ $vendor->id }}" {{ request('vendor') == $vendor->id ? 'selected' : '' }}>
                                            {{ $vendor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="col-12 col-sm-6 col-lg-4 col-xl-2">
                            <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.system') }}</label>
                            <select class="form-select" name="system">
                                <option value="">{{ trans('labels.all') }}</option>
                                @foreach (\App\Helpers\Systems::all() as $ocSys)
                                    <option value="{{ $ocSys['key'] }}" {{ request('system') === $ocSys['key'] ? 'selected' : '' }}>
                                        {{ app()->getLocale() === 'ar' ? $ocSys['name_ar'] : $ocSys['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-4 col-xl-2">
                            <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.payment_type') }}</label>
                            <select class="form-select" name="method">
                                <option value="">{{ trans('labels.all') }}</option>
                                @foreach (\App\Helpers\Subscriptions::filterMethods() as $ocM)
                                    <option value="{{ $ocM }}" {{ (string) request('method') === (string) $ocM ? 'selected' : '' }}>
                                        {{ \App\Helpers\Subscriptions::methodName($ocM) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-4 col-xl-2">
                            <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.status') }}</label>
                            <select class="form-select" name="status">
                                <option value="">{{ trans('labels.all') }}</option>
                                @foreach (\App\Helpers\Subscriptions::statuses() as $ocK => $ocLbl)
                                    <option value="{{ $ocK }}" {{ request('status') === $ocK ? 'selected' : '' }}>{{ $ocLbl }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-6 col-sm-6 col-lg-4 col-xl-2">
                            <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.from_date') }}</label>
                            <input type="date" class="form-control" name="startdate" value="{{ request('startdate') }}">
                        </div>

                        <div class="col-6 col-sm-6 col-lg-4 col-xl-2">
                            <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.to_date') }}</label>
                            <input type="date" class="form-control" name="enddate" value="{{ request('enddate') }}">
                        </div>

                        <div class="col-12 col-lg-8 {{ $ocIsAdmin ? 'col-xl-6' : 'col-xl-8' }}">
                            <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.search') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                                <input type="text" class="form-control border-start-0" name="q" value="{{ request('q') }}"
                                    placeholder="{{ trans('labels.transaction_search_placeholder') }}">
                            </div>
                        </div>

                        <div class="col-12 col-lg-4 d-flex gap-2">
                            <button type="submit" class="btn btn-secondary px-4 rounded-start-5 rounded-end-5">
                                <i class="fa-solid fa-filter mx-1"></i>{{ trans('labels.fetch') }}</button>
                            <a href="{{ URL::to('/admin/transaction') }}" class="btn btn-light px-4 rounded-start-5 rounded-end-5">
                                <i class="fa-solid fa-rotate-left mx-1"></i>{{ trans('labels.reset') }}</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 mb-7">
        <div class="card border-0 box-shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered py-3 zero-configuration w-100">
                        <thead>
                            <tr class="fw-500 fs-15">
                                <td>{{ trans('labels.srno') }}</td>
                                @if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1))
                                    <td>{{ trans('labels.name') }}</td>
                                @endif
                                <td>{{ trans('labels.system') }}</td>
                                <td>{{ trans('labels.plan_name') }}</td>
                                <td>{{ trans('labels.amount') }}</td>
                                <td>{{ trans('labels.payment_type') }}</td>
                                <td>{{ trans('labels.status') }}</td>
                                <td>{{ trans('labels.payment_date') }}</td>
                                <td>{{ trans('labels.activation_term') }}</td>
                                <td>{{ trans('labels.expire_date') }}</td>
                                <td>{{ trans('labels.addons') }}</td>
                                <td>{{ trans('labels.created_date') }}</td>
                                <td>{{ trans('labels.updated_date') }}</td>
                                <td>{{ trans('labels.action') }}</td>

                            </tr>
                        </thead>
                        <tbody>
                            @php

                                $i = 1;

                            @endphp
                            @foreach ($transaction as $transaction)
                                <tr class="fs-7 align-middle">
                                    <td>@php echo $i++; @endphp</td>
                                    @if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1))
                                        <td>{{ optional($transaction['vendor_info'])->name ?? '-' }}</td>
                                    @endif
                                    @php
                                        $ocPay  = \App\Helpers\Subscriptions::paymentStatus($transaction);
                                        $ocTerm = \App\Helpers\Subscriptions::term($transaction);
                                        $ocSnap = $transaction->planDetails();
                                        $ocAddons = (array) ($transaction->addons ?? []);
                                    @endphp
                                    <td>{{ \App\Helpers\Systems::label($transaction->system) }}</td>
                                    <td>
                                        {{ $ocSnap['name'] ?? (optional($transaction['plan_info'])->name ?? '-') }}
                                        <div class="text-muted">{{ $transaction->transaction_number }}</div>
                                    </td>
                                    <td>
                                        {{ number_format((float) $transaction->grand_total, 2) }}
                                        <span class="text-muted">{{ $transaction->currency ?: 'USD' }}</span>
                                    </td>
                                    <td>
                                        {{ \App\Helpers\Subscriptions::methodName($transaction->payment_type) }}
                                        @if ($transaction->payment_id)
                                            <div class="text-muted">{{ \Illuminate\Support\Str::limit($transaction->payment_id, 16) }}</div>
                                        @endif
                                        @if ($ocReceipt = \App\Helpers\Subscriptions::receiptUrl($transaction))
                                            <a href="{{ $ocReceipt }}" target="_blank" class="fs-7">
                                                <i class="fa-solid fa-receipt"></i> {{ trans('labels.receipt') }}</a>
                                        @endif
                                    </td>
                                    <td><span class="badge {{ $ocPay['class'] }}">{{ app()->getLocale() === 'ar' ? $ocPay['label_ar'] : $ocPay['label'] }}</span></td>
                                    {{-- Payment date, activation term and expiry are three separate facts. --}}
                                    <td>{{ $transaction->purchase_date ? helper::date_format($transaction->purchase_date, $vendor_id) : '-' }}</td>
                                    <td>
                                        <span class="{{ $ocTerm['class'] }} fw-500">{{ $ocTerm['label'] }}</span>
                                        <div class="text-muted">{{ $ocTerm['note'] }}</div>
                                    </td>
                                    <td>{{ $transaction->expire_date ? helper::date_format($transaction->expire_date, $vendor_id) : '-' }}</td>
                                    <td>
                                        @if (!empty($ocAddons))
                                            @foreach ($ocAddons as $ocKey => $ocVal)
                                                <span class="badge bg-secondary">+{{ is_numeric($ocVal) ? $ocVal : 1 }}
                                                    {{ ucwords(str_replace('_', ' ', is_numeric($ocKey) ? $ocVal : $ocKey)) }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">{{ trans('labels.none') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ helper::date_format($transaction->created_at, $vendor_id) }}<br>
                                        {{ helper::time_format($transaction->created_at, $vendor_id) }}

                                    </td>
                                    <td>{{ helper::date_format($transaction->updated_at, $vendor_id) }}<br>
                                        {{ helper::time_format($transaction->updated_at, $vendor_id) }}

                                    </td>

                                    <td>
                                        <div class="d-flex gap-2">
                                            @if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1))
                                                {{-- Approve / reject belongs to every offline method (Benefit, Al Salam
                                                     Bank QR, payment link, cash), not only bank transfer and COD. --}}
                                                @if (\App\Helpers\Subscriptions::isManual($transaction->payment_type))
                                                    @if ($transaction->status == 1)
                                                        <a class="btn btn-sm btn-success btn-size"
                                                            tooltip="{{ trans('labels.accept') }}"
                                                            onclick="statusupdate('{{ URL::to('admin/transaction-' . $transaction->id . '-2') }}')"><i
                                                                class="fas fa-check"></i></a>

                                                        <a class="btn btn-sm btn-danger btn-size"
                                                            tooltip="{{ trans('labels.cancel') }}"
                                                            onclick="statusupdate('{{ URL::to('admin/transaction-' . $transaction->id . '-3') }}')"><i
                                                                class="fas fa-close"></i></a>
                                                    @endif
                                                @endif
                                            @endif
                                            <a class="btn btn-sm btn-dark btn-size" tooltip="{{ trans('labels.view') }}"
                                                href="{{ URL::to('admin/transaction/plandetails-' . $transaction->id) }}"><i
                                                    class="fa-regular fa-eye"></i></a>
                                            <a href="{{ URL::to('/admin/transaction/generatepdf-' . $transaction->id) }}"
                                                tooltip="{{ trans('labels.downloadpdf') }}"
                                                class="btn btn-danger btn-sm btn-size">
                                                <i class="fa-solid fa-file-pdf"></i>
                                            </a>
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
