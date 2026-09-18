@extends('admin.layout.default')
@php
    if (Auth::user()->type == 4) {
        $vendor_id = Auth::user()->vendor_id;
    } else {
        $vendor_id = Auth::user()->id;
    }
@endphp
@section('content')
    <div class="row align-items-center mb-3">
        <div class="col-12">
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.payment_settings') }}</h5>
            @include('admin.layout.breadcrumb')
        </div>
   
    </div>
    <div class="row g-3 mb-7 sort_menu" id="carddetails" data-url="{{ url('admin/payment/reorder_payment') }}">

        @foreach ($getpayment as $key => $pmdata)
            @php
                // Check if the current $pmdata is a system addon and activated
                if ($pmdata->isIncludedGateway()) {
                    $systemAddonActivated = true;
                } else {
                    $systemAddonActivated = false;
                }
                $addon = App\Models\SystemAddons::where('unique_identifier', $pmdata->unique_identifier)->first();
                if ($addon != null && $addon->activated == 1) {
                    $systemAddonActivated = true;
                }
                $transaction_type = $pmdata->payment_type;
            @endphp
            @if ($systemAddonActivated)
                <div class="col-md-12 col-lg-12 col-xl-6" data-id="{{ $pmdata->id }}">
                    <form action="{{ URL::to('admin/payment/update') }}" method="POST" class=""
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="transaction_type" value="{{ $pmdata->id }}">
                        <input type="hidden" name="payment_type" value="{{ $transaction_type }}">
                        <div class="card h-100 box-shadow overflow-hidden handle">
                            <div class="card-header bg-secondary p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <img src="{{ helper::image_path($pmdata->image) }}" alt=""
                                            class="img-fluid rounded me-2" height="30" width="30">
                                        <b>
                                            {{ $pmdata->payment_name }}
                                            @if (
                                                $transaction_type == '2' ||
                                                    $transaction_type == '3' ||
                                                    $transaction_type == '4' ||
                                                    $transaction_type == '5' ||
                                                    $transaction_type == '6' ||
                                                    $transaction_type == '7' ||
                                                    $transaction_type == '8' ||
                                                    $transaction_type == '9' ||
                                                    $transaction_type == '10' ||
                                                    $transaction_type == '11' ||
                                                    $transaction_type == '12' ||
                                                    $transaction_type == '13' ||
                                                    $transaction_type == '14' ||
                                                    $transaction_type == '15')
                                                @if (env('Environment') == 'sendbox')
                                                    <span
                                                        class="badge badge bg-danger ms-2">{{ trans('labels.addon') }}</span>
                                                @endif
                                            @endif
                                        </b>
                                    </div>
                                    <div>
                                        <input id="checkbox-switch-{{ $transaction_type }}" type="checkbox"
                                            class="checkbox-switch" name="is_available" value="1"
                                            {{ $pmdata->is_available == 1 ? 'checked' : '' }}>
                                        <label for="checkbox-switch-{{ $transaction_type }}" class="switch">
                                            <span
                                                class="{{ session()->get('direction') == 2 ? 'switch__circle-rtl' : 'switch__circle' }}"><span
                                                    class="switch__circle-inner"></span></span>
                                            <span
                                                class="switch__left {{ session()->get('direction') == 2 ? 'pe-2' : 'ps-2' }}">{{ trans('labels.off') }}</span>
                                            <span
                                                class="switch__right {{ session()->get('direction') == 2 ? 'ps-2' : 'pe-2' }}">{{ trans('labels.on') }}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label class="form-label">
                                            {{ trans('labels.payment_name') }}
                                            <span class="text-danger">*</span> </label>
                                        <input type="text" class="form-control" name="name"
                                            placeholder="{{ trans('labels.payment_name') }}"
                                            value="{{ $pmdata->payment_name }}" required>
                                    </div>
                                    @if (!in_array($transaction_type, ['1', '6', '16', '17', '18', '19', '20', '21']))
                                        <div class="col-md-6">
                                            <label for="razorpaycurrency" class="form-label">
                                                {{ trans('labels.environment') }}
                                                <span class="text-danger"> *</span>
                                            </label>
                                            <div class="d-flex gap-3 align-items-center">
                                                <div class="form-check form-check-inline p-0 m-0">
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <input class="form-check-input p-0 m-0" type="radio"
                                                            name="environment" id="sandbox-{{ $transaction_type }}"
                                                            value="1"
                                                            {{ $pmdata->environment == 1 ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="sandbox-{{ $transaction_type }}">
                                                            {{ trans('labels.sandbox') }}
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-check form-check-inline p-0 m-0">
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <input class="form-check-input p-0 m-0" type="radio"
                                                            name="environment" id="production-{{ $transaction_type }}"
                                                            value="2"
                                                            {{ $pmdata->environment == 2 ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="production-{{ $transaction_type }}">
                                                            {{ trans('labels.production') }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="currency" class="form-label"> {{ trans('labels.currency') }}
                                                    <span class="text-danger"> *</span>
                                                </label>
                                                <input type="text" required="" id="currency" class="form-control"
                                                    name="currency" placeholder="Currency" value="{{ $pmdata->currency }}">
                                            </div>
                                        </div>


                                        @if ($transaction_type == '4')
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="form-label">
                                                        @if ($transaction_type == 'flutterwave')
                                                            {{ trans('labels.encryption_key') }}
                                                        @else
                                                            {{ trans('labels.merchant_user_id') }}
                                                        @endif
                                                        <span class="text-danger"> *</span>
                                                    </label>
                                                    <input type="text" id="encryption_key" class="form-control"
                                                        name="encryption_key"
                                                        placeholder="{{ !empty($pmdata->encryption_key) ? '•••••••• (leave blank to keep current)' : 'Encryption Key' }}"
                                                        value=""
                                                        {{ $transaction_type == 'flutterwave' && empty($pmdata->encryption_key) ? 'required' : '' }}>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($transaction_type == '12')
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="base_url_by_region" class="form-label">
                                                        {{ trans('labels.base_url_by_region') }}
                                                        <span class="text-danger"> *</span>
                                                    </label>
                                                    <input type="text" id="base_url_by_region" class="form-control"
                                                        name="base_url_by_region"
                                                        placeholder="{{ trans('labels.base_url_by_region') }}"
                                                        value="{{ $pmdata->base_url_by_region }}"
                                                        {{ $transaction_type == 'paytab' ? 'required' : '' }}>
                                                </div>
                                            </div>
                                        @endif

                                        <div
                                            class=" {{ $transaction_type == '7' || $transaction_type == '9' || $transaction_type == '13' || $transaction_type == '14' ? 'col-md-12' : 'col-md-6' }}">
                                            <div class="form-group">
                                                <label class="form-label">
                                                    @if ($transaction_type == '11')
                                                        {{ trans('labels.salt_key') }}
                                                    @else
                                                        {{ trans('labels.secret_key') }}
                                                    @endif
                                                    <span class="text-danger"> *</span>
                                                </label>
                                                <input type="text" id="secretkey" class="form-control"
                                                    name="secret_key"
                                                    placeholder="{{ !empty($pmdata->secret_key) ? '•••••••• (leave blank to keep current)' : 'Secret Key' }}"
                                                    value="" {{ empty($pmdata->secret_key) ? 'required' : '' }}>
                                            </div>
                                        </div>
                                        <div
                                            class="col-md-6 {{ $transaction_type == '7' || $transaction_type == '9' || $transaction_type == '13' || $transaction_type == '14' ? 'd-none' : '' }}">
                                            <div class="form-group">
                                                <label for="publickey" class="form-label">
                                                    @if ($transaction_type == '12')
                                                        {{ trans('labels.profile_key') }}
                                                    @elseif($transaction_type == '11')
                                                        {{ trans('labels.merchant_id') }}
                                                    @else
                                                        {{ trans('labels.public_key') }}
                                                    @endif
                                                    <span class="text-danger"> *</span>
                                                </label>
                                                <input type="text" id="publickey" class="form-control"
                                                    name="public_key" placeholder="Public Key"
                                                    value="{{ $pmdata->public_key }}"
                                                    {{ $transaction_type != '7' || $transaction_type != '9' || $transaction_type != '13' || $transaction_type != '14' ? '' : 'required' }}>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="image" class="form-label">
                                                {{ trans('labels.image') }}
                                            </label>
                                            <input type="file" class="form-control" name="image">
                                            <img src="{{ helper::image_path($pmdata->image) }}" alt=""
                                                class="img-fluid rounded hw-50">
                                        </div>
                                    </div>
                                    @if ($transaction_type == '6')
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label">
                                                    {{ trans('labels.payment_description') }} <span class="text-danger">
                                                        *</span></label>
                                                <textarea class="form-control" id="ckeditor" name="payment_description">{{ $pmdata->payment_description }}</textarea>

                                            </div>
                                        </div>
                                    @endif
                                    {{-- V2 manual/offline payment method config --}}
                                    @if (in_array($transaction_type, ['19', '20']))
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">
                                                    {{ trans('labels.payment_qr_code_image') }}
                                                </label>
                                                <input type="file" class="form-control" name="qr_image" accept="image/*">
                                                @if (!empty($pmdata->qr_image))
                                                    <img src="{{ helper::image_path($pmdata->qr_image) }}" alt="" class="img-fluid rounded hw-70 mt-2">
                                                @endif
                                                <small class="text-muted d-block mt-1">{{ trans('labels.customer_scans_this_to_pay') }}</small>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($transaction_type == '21')
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label">
                                                    {{ trans('labels.payment_link_url') }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="url" class="form-control" name="payment_link"
                                                    value="{{ @$pmdata->payment_link }}" placeholder="https://...">
                                            </div>
                                        </div>
                                    @endif
                                    @if (in_array($transaction_type, ['17', '18', '19', '20', '21']))
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label">
                                                    {{ trans('labels.payment_instructions_shown_to_the_customer') }}
                                                </label>
                                                <textarea class="form-control" name="payment_description" rows="2"
                                                    placeholder="{{ trans('labels.e_g_send_payment_to_973_xxxx') }}">{{ @$pmdata->payment_description }}</textarea>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-md-12">
                                        <div class="form-group m-0 mt-2 d-flex gap-2 justify-content-end">
                                            <button
                                                class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_payment_methods', Auth::user()->role_id, $vendor_id, 'add') == 1 ? '' : 'd-none') : '' }}"
                                                @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif>{{ trans('labels.save') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
        @endforeach

    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('.sort_menu').sortable({
                handle: '.handle',
                cursor: 'move',
                placeholder: 'highlight',
                axis: "x,y",

                update: function(e, ui) {
                    var sortData = $('.sort_menu').sortable('toArray', {
                        attribute: 'data-id'
                    })
                    updateToDatabase(sortData.join('|'))
                }
            })

            function updateToDatabase(idString) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    dataType: "json",
                    url: $('#carddetails').attr('data-url'),
                    data: {
                        ids: idString,
                    },
                    success: function(response) {
                        if (response.status == 1) {
                            toastr.success(response.msg);
                        } else {
                            toastr.success(wrong);
                        }
                    }
                });
            }

        })
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.12.1/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('ckeditor');
    </script>
    <script src="{{ url(env('ASSETSPATHURL') . 'admin-assets/js/payment.js') }}"></script>
@endsection
