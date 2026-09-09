@extends('admin.layout.default')

@section('content')
    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-12">
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.edit') }}</h5>
            @include('admin.layout.breadcrumb')
        </div>
    </div>

    <div class="row my-3">

        <div class="col-12">

            <div class="card border-0 box-shadow">

                <div class="card-body">

                    <form action="{{ URL::to('admin/coupons/update-' . $promocode->id) }}" method="POST"
                        enctype="multipart/form-data">

                        @csrf

                        <div class="row">

                            <div class="col-md-6 form-group">

                                <label class="form-label">{{ trans('labels.offer_name') }}<span class="text-danger">

                                        * </span></label>

                                <input type="text" class="form-control" name="offer_name"
                                    value="{{ $promocode->offer_name }}" placeholder="{{ trans('labels.offer_name') }}"
                                    required>

                                @error('offer_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror

                            </div>

                            <div class="col-md-6 form-group">

                                <label class="form-label">{{ trans('labels.offer_code') }}<span
                                        class="text-danger">*</span></label>

                                <input type="text" class="form-control" name="offer_code"
                                    value="{{ $promocode->offer_code }}" placeholder="{{ trans('labels.offer_code') }}"
                                    required>

                                @error('offer_code')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror

                            </div>

                            <div class="col-md-6 form-group">

                                <div class="row">

                                    <div class="col-md-6">

                                        <label class="form-label">{{ trans('labels.offer_type') }}<span
                                                class="text-danger">* </span></label>

                                        <select class="form-select" name="offer_type" required>

                                            <option value="">{{ trans('labels.select') }}</option>

                                            <option value="1" {{ $promocode->offer_type == '1' ? 'selected' : '' }}>
                                                {{ trans('labels.fixed') }}</option>

                                            <option value="2" {{ $promocode->offer_type == '2' ? 'selected' : '' }}>
                                                {{ trans('labels.percentage') }}</option>

                                        </select>

                                        @error('offer_type')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror

                                    </div>

                                    <div class="col-md-6">

                                        <label class="form-label">{{ trans('labels.discount') }}<span class="text-danger">
                                                * </span></label>

                                        <input type="text" class="form-control numbers_only" name="amount"
                                            value="{{ $promocode->offer_amount }}"
                                            placeholder="{{ trans('labels.discount') }}" required>

                                        @error('amount')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror

                                    </div>

                                </div>

                            </div>

                            <div class="col-md-6 form-group">

                                <label class="form-label">
                                    @if (Auth::user()->type == 1)
                                        {{ trans('labels.min_plan_amount') }}
                                    @else
                                        {{ trans('labels.min_order_amount') }}
                                    @endif
                                    <span class="text-danger"> * </span>
                                </label>

                                <input type="text" class="form-control numbers_only" name="order_amount"
                                    value="{{ $promocode->min_amount }}"
                                    @if (Auth::user()->type == 1) placeholder="{{ trans('labels.min_plan_amount') }}" 
                                    @else
                                    placeholder="{{ trans('labels.min_order_amount') }}" @endif
                                    required>
                                @error('order_amount')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror

                            </div>

                            <div class="col-md-6">

                                <div class="form-group">

                                    <div class="row">

                                        <div class="col-md-6">

                                            <label class="form-label">{{ trans('labels.start_date') }}<span
                                                    class="text-danger"> * </span></label>

                                            <input type="date" class="form-control" name="start_date"
                                                value="{{ $promocode->start_date }}" id="start_date"
                                                placeholder="{{ trans('labels.start_date') }}" required>

                                            @error('start_date')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror

                                        </div>

                                        <div class="col-md-6">

                                            <label class="form-label">{{ trans('labels.end_date') }}<span
                                                    class="text-danger"> * </span></label>

                                            <input type="date" class="form-control" name="end_date"
                                                value="{{ $promocode->exp_date }}" id="end_date"
                                                placeholder="{{ trans('labels.end_date') }}" required>

                                            @error('end_date')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror

                                        </div>

                                    </div>

                                </div>

                                <div class=" form-group">

                                    <label class="form-label">{{ trans('labels.usage_type') }}<span class="text-danger"> *
                                        </span></label>

                                    <select class="form-select type" name="usage_type">

                                        <option value="">{{ trans('labels.select') }}</option>

                                        <option value="1" {{ $promocode->usage_type == '1' ? 'selected' : '' }}>
                                            {{ trans('labels.limited_time') }}</option>

                                        <option value="2" {{ $promocode->usage_type == '2' ? 'selected' : '' }}>
                                            {{ trans('labels.multiple_time') }}</option>

                                    </select>

                                    @error('usage_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                </div>

@include('admin.included.promocode._scope_fields', ['coupon' => $promocode])

                                <div class="form-group" id="usage_limit_input">

                                    <label class="form-label">{{ trans('labels.usage_limit') }}<span class="text-danger">*
                                        </span></label>

                                    <input type="text" class="form-control" name="usage_limit"
                                        value="{{ $promocode->usage_limit }}"
                                        placeholder="{{ trans('labels.usage_limit') }}">

                                    @error('usage_limit')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                </div>

                            </div>

                            <div class="col-md-6 form-group">

                                <label class="form-label">{{ trans('labels.description') }}<span class="text-danger"> *
                                    </span></label>

                                <textarea name="description" class="form-control" rows="4" placeholder="{{ trans('labels.description') }}"
                                    required>{{ $promocode->description }}</textarea>

                                @error('description')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror

                            </div>

                            <div class="form-group mt-2 m-0 d-flex gap-2 justify-content-end">
                                <a type="button" class="btn btn-danger px-4 rounded-start-5 rounded-end-5"
                                    href="{{ route('coupons') }}">
                                    {{ trans('labels.cancel') }}
                                </a>
                                <button
                                    class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_coupons', Auth::user()->role_id, $vendor_id, 'add') == 1 ? '' : 'd-none') : '' }}"
                                    @if (env('Environment') == 'sendbox') type="button"
                                        onclick="myFunction()" @else type="submit" @endif>{{ trans('labels.save') }}
                                </button>
                            </div>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>
@endsection

@section('scripts')
    <script src="{{ url(env('ASSETSPATHURL') . 'admin-assets/js/promocode.js') }}"></script>
@endsection
