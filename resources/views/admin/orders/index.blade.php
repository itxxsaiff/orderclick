@extends('admin.layout.default')
@section('content')
    @php
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }

    @endphp
    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-12 col-md-4">
            <h5 class="pages-title fs-2 align-items-center">
                {{ request()->is('admin/report*') ? trans('labels.report') : trans('labels.orders') }}
            </h5>
            <div class="d-flex">
                @include('admin.layout.breadcrumb')
            </div>

        </div>
        <div class="col-12 col-md-8">
            @if (request()->is('admin/report*'))
                <form action="{{ URL::to('/admin/report') }}" class="mb-">
                    <div class="input-group col-md-12 ps-0 justify-content-end">
                        @if ($getcustomerslist->count() > 0)
                            <div class="input-group-append col-auto px-1">
                                <select name="customer_id" class="form-select">
                                    <option value="">{{ trans('labels.select_customer') }}</option>
                                    @foreach ($getcustomerslist as $getcustomer)
                                        <option value="{{ $getcustomer->id }}"
                                            {{ $getcustomer->id == @$_GET['customer_id'] ? 'selected' : '' }}>
                                            {{ $getcustomer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="input-group-append col-auto px-1 pb-2 pb-xl-0">
                            <input type="date" class="form-control rounded-5 px-4 bg-white" name="startdate"
                                @isset($_GET['startdate']) value="{{ $_GET['startdate'] }}" @endisset required>
                        </div>
                        <div class="input-group-append col-auto px-1 pb-2 pb-xl-0">
                            <input type="date" class="form-control rounded-5 px-4 bg-white" name="enddate"
                                @isset($_GET['enddate']) value="{{ $_GET['enddate'] }}" @endisset required>
                        </div>
                        <div class="input-group-append pb-2 pb-xl-0 px-1">
                            <button class="btn btn-primary rounded-5 px-4"
                                type="submit">{{ trans('labels.fetch') }}</button>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>

    @include('admin.orders.statistics')
    <div class="col-12 mb-7 mt-3">
        <div class="card border-0 box-shadow">
            <div class="card-body">
                <div class="table-responsive">
                    @include('admin.orders.orderstable')
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">{{ trans('labels.record_payment') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action=" {{ URL::to('admin/orders/payment_status-' . '2') }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="order_number" name="order_number" value="">
                        <label for="modal_total_amount" class="form-label">
                            {{ trans('labels.total') }} {{ trans('labels.amount') }}
                        </label>
                        <input type="text" class="form-control numbers_only" name="modal_total_amount"
                            id="modal_total_amount" disabled value="">
                        <div id="cod_payment">
                            <label for="modal_amount" class="form-label mt-2">
                                {{ trans('labels.cash_received') }}
                            </label>
                            <input type="text" class="form-control numbers_only" name="modal_amount" id="modal_amount"
                                value="" onkeyup="validation($(this).val())" required>
                            <label for="modal_amount" class="form-label mt-2">
                                {{ trans('labels.change_amount') }}
                            </label>
                            <input type="number" class="form-control" name="ramin_amount" id="ramin_amount" value=""
                                readonly>
                        </div>
                        <div id="pos_payment">
                            <p class="m-0 mt-2 mb-1 fs-7 text-dark fw-medium"> {{ trans('labels.payment_information') }}
                            </p>
                            <div class="col-12 d-flex gap-4">
                                <div class="form-check form-check-inline m-0 p-0 d-flex align-items-center gap-2">
                                    <input class="form-check-input m-0 p-0" type="radio" name="payment_type"
                                        id="inlineRadio1" value="1">
                                    <label class="form-check-label m-0 p-0 modal-price fw-500"
                                        for="inlineRadio1">{{ trans('labels.cash') }}</label>
                                </div>
                                <div class="form-check form-check-inline m-0 p-0 d-flex align-items-center gap-2">
                                    <input class="form-check-input m-0 p-0" type="radio" name="payment_type"
                                        id="inlineRadio2" value="0">
                                    <label class="form-check-label m-0 p-0 modal-price fw-500"
                                        for="inlineRadio2">{{ trans('labels.online') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-secondary">{{ trans('labels.submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function codpayment(order_number, grand_total, order_type) {
            $('#modal_total_amount').val(grand_total);
            $('#order_number').val(order_number);
            if (order_type == 4) {
                $('#cod_payment').addClass('d-none');
                $('#pos_payment').removeClass('d-none');
                $('#modal_amount').prop('required', false);
                $("input[name=payment_type]").prop('required', true);
            } else {
                $('#cod_payment').removeClass('d-none');
                $('#pos_payment').addClass('d-none');
                $('#modal_amount').prop('required', true);
                $("input[name=payment_type]").prop('required', false);
            }
            $('#paymentModal').modal('show');
        }

        function validation(value) {
            var remaining = $('#modal_total_amount').val() - value;
            $('#ramin_amount').val(remaining.toFixed(2));
        }
    </script>
@endsection
