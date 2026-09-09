@extends('admin.layout.default')
@section('content')
    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-12 col-md-4">
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.edit') }}</h5>
            <div class="d-flex">
                @include('admin.layout.breadcrumb')
            </div>
        </div>

    </div>
    <div class="row mt-3">
        @php
            if (Auth::user()->type == 4) {
                $vendor_id = Auth::user()->vendor_id;
            } else {
                $vendor_id = Auth::user()->id;
            }
        @endphp
        <div class="col-12 mb-7">
            <div class="card border-0 box-shadow">
                <div class="card-body">
                    <form action="{{ URL::to('admin/currencys/currency_update-' . $editcurrency->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-6">
                                <input type="hidden" name="code" id="code">
                                <label class="form-label">{{ trans('labels.currency') }}<span class="text-danger"> *
                                    </span></label>
                                <input type="text" class="form-control" name="currency" id="currency"
                                    value="{{ $editcurrency->currency }}" placeholder="{{ trans('labels.currency') }}"
                                    required>

                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label">{{ trans('labels.currency_symbol') }}<span class="text-danger"> *
                                    </span></label>
                                <input type="text" class="form-control" name="currency_symbol"
                                    value="{{ $editcurrency->currency_symbol }}"
                                    placeholder="{{ trans('labels.currency_symbol') }}" required>

                            </div>
                            <div class="form-group m-0 mt-2 d-flex gap-2 justify-content-end">
                                <a href="{{ URL::to('admin/currencys') }}"
                                    class="btn btn-danger px-4 rounded-start-5 rounded-end-5">{{ trans('labels.cancel') }}</a>
                                <button
                                    class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_currency_settings', Auth::user()->role_id, $vendor_id, 'add') == 1 ? '' : 'd-none') : '' }}"
                                    @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif>{{ trans('labels.save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
