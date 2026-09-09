@extends('front.theme.default')
@section('content')
    <!------ breadcrumb ------>
    <section class="breadcrumb-sec bg-change-mode">
        <div class="container">
            <nav>
                <ol class="breadcrumb d-flex m-0 text-capitalize">
                    <li class="breadcrumb-item">
                        <a href="{{ URL::to(@$storeinfo->slug . '/') }}" class="text-dark color-changer">
                            {{ trans('labels.home') }}
                        </a>
                    </li>
                    <li
                        class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-item-right' : 'breadcrumb-item-left' }}">
                        {{ trans('labels.wallet') }}
                    </li>
                </ol>
            </nav>
        </div>
    </section>
    <section class="py-sm-5 py-4">
        <div class="container">
            <div class="row gx-sm-3 gx-2">
                @include('front.theme.user_sidebar')
                <div class="col-lg-9 col-md-12">
                    <div class="card rounded user-form">
                        <div class="settings-box card-body py-4">
                            <div class="settings-box-header gap-3 flex-wrap mb-4">
                                <div class="col-sm-8">
                                    <div class="d-flex gap-3 mb-1 flex-wrap align-items-center">
                                        <h2 class="page-title m-0">
                                            {{ trans('labels.wallet_balance') }}
                                        </h2>
                                        <p class="text-success fs-5 fw-600">
                                            {{ helper::currency_formate(Auth::user()->wallet, $vdata) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-sm-auto col-12">
                                    <a href="{{ URL::to($storeinfo->slug . '/addmoney') }}"
                                        class="w-100 btn-primary rounded px-sm-4 px-3 py-2 btn align-items-center fs-15 fw-500 justify-content-center d-flex gap-2">
                                        <i class="fa-regular fa-plus"></i>
                                        {{ trans('labels.add_money') }}
                                    </a>
                                </div>
                            </div>

                            @if (count($gettransactions) > 0)
                                <div class="settings-box-body dashboard-section">
                                    <div class="table-responsive rounded">
                                        <table class="table table-striped align-middle table-hover m-0">
                                            <thead class="table-light">
                                                <tr class="fs-7 fw-600">
                                                    <th scope="col">{{ trans('labels.date') }}</th>
                                                    <th scope="col"> {{ trans('labels.amount') }} </th>
                                                    <th scope="col">{{ trans('labels.remark') }}</th>
                                                    <th scope="col">{{ trans('labels.status') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($gettransactions as $row)
                                                    <tr class="fs-7">
                                                        <td>{{ helper::date_format($row->created_at, $vdata) }}<br>
                                                            {{ helper::time_format($row->created_at, $vdata) }}
                                                        </td>
                                                        <td>
                                                            @if ($row->tips > 0)
                                                                ({{ helper::currency_formate($row->amount, $vdata) }}
                                                                +
                                                                {{ trans('labels.tips') . ' : ' . helper::currency_formate($row->tips, $vdata) }})
                                                            @else
                                                                {{ helper::currency_formate($row->amount, $vdata) }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($row->transaction_type == 2)
                                                                {{ trans('labels.order_placed') }}
                                                                <a class="fw-600"
                                                                    href="{{ URL::to($storeinfo->slug . '/track-order/' . $row->order_number) }}">
                                                                    {{ $row->order_number }}
                                                                </a>
                                                            @elseif ($row->transaction_type == 3)
                                                                {{ trans('labels.order_cancelled') }}
                                                                <a class="fw-600"
                                                                    href="{{ URL::to($storeinfo->slug . '/track-order/' . $row->order_number) }}">
                                                                    {{ $row->order_number }}
                                                                </a>
                                                            @else
                                                                {{ trans('labels.wallet_recharge') }}
                                                                <span>{{ @helper::getpayment($row->payment_type, $vdata)->payment_name }}</span>
                                                                <span>{{ $row->payment_id }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($row->transaction_type == 2)
                                                                <div
                                                                    class="badge bg-debit custom-badge bg-cancelled rounded-0">
                                                                    <span> {{ trans('labels.debit') }}</span>
                                                                </div>
                                                            @else
                                                                <div
                                                                    class="badge bg-debit custom-badge rounded-0 bg-completed">
                                                                    <span> {{ trans('labels.credit') }}</span>
                                                                </div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        {{ $gettransactions->links() }}
                                    </div>
                                </div>
                            @else
                                @include('front.nodata')
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('front.sum_qusction')
@endsection
