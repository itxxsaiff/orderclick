@extends('admin.layout.default')
@php
    if (Auth::user()->type == 4) {
        $vendor_id = Auth::user()->vendor_id;
    } else {
        $vendor_id = Auth::user()->id;
    }
    $module = 'role_currency_settings';
@endphp
@section('content')
    <div class="row justify-content-between align-items-center">
        <div class="col-12 col-md-4">
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.currency-settings') }}</h5>
            @include('admin.layout.breadcrumb')
        </div>
        <div class="col-12 col-md-8">
            <div class="d-flex justify-content-end">
                @if (App\Models\SystemAddons::where('unique_identifier', 'currency_settigns')->first() != null &&
                        App\Models\SystemAddons::where('unique_identifier', 'currency_settigns')->first()->activated == 1)
                    <a href="{{ URL::to('admin/currency-settings/add') }}"
                        class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_currency_settings', Auth::user()->role_id, $vendor_id, 'add') == 1 ? '' : 'd-none') : '' }}">
                        <i class="fa-regular fa-plus mx-1"></i>{{ trans('labels.add') }}
                    </a>
                @endif
            </div>
        </div>
    </div>
    <div class="col-12 mt-3 mb-7">
        <div class="card border-0 box-shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered py-3 zero-configuration w-100">
                        <thead>
                            <tr class="text-capitalize fw-500 fs-15">
                                <td></td>
                                <td>{{ trans('labels.srno') }}</td>
                                <td>{{ trans('labels.name') }}</td>
                                <td>{{ trans('labels.currency') }}</td>
                                <td>{{ trans('labels.exchange_rate') }}</td>
                                <td>{{ trans('labels.status') }}</td>
                                <td>{{ trans('labels.is_default') }}</td>
                                <td>{{ trans('labels.created_date') }}</td>
                                <td>{{ trans('labels.updated_date') }}</td>
                                @if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1))
                                    <td>{{ trans('labels.action') }}</td>
                                @endif

                            </tr>
                        </thead>
                        <tbody id="tabledetails" data-url="">
                            @php
                                $i = 1;
                            @endphp
                            @foreach ($getcurrency as $currency)
                                <tr class="fs-7 row1 align-middle" id="dataid{{ $currency->id }}"
                                    data-id="{{ $currency->id }}">
                                    <td><a tooltip="{{ trans('labels.move') }}">
                                            <i class="fa-light fa-up-down-left-right mx-2"></i>
                                        </a>
                                    </td>
                                    <td>@php
                                        echo $i++;
                                    @endphp </td>
                                    <td>{{ $currency->name }}</td>

                                    <td>
                                        {{ $currency->currency }}
                                    </td>
                                    <td>
                                        {{ $currency->exchange_rate }}
                                    </td>

                                    <td>
                                        @if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1))
                                            @if ($currency->is_available == '1')
                                                <a @if (env('Environment') == 'sendbox') onclick="myFunction()" @else onclick="statusupdate('{{ URL::to('admin/currency-settings/changestatus-' . $currency->code . '/2') }}')" @endif
                                                    class="btn btn-sm btn-success btn-size {{ Auth::user()->type == 4 ? (helper::check_access('role_currency_settings', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}"
                                                    tooltip="{{ trans('labels.active') }}"><i class="fas fa-check"></i></a>
                                            @else
                                                <a @if (env('Environment') == 'sendbox') onclick="myFunction()" @else onclick="statusupdate('{{ URL::to('admin/currency-settings/changestatus-' . $currency->code . '/1') }}')" @endif
                                                    class="btn btn-sm btn-danger btn-size {{ Auth::user()->type == 4 ? (helper::check_access('role_currency_settings', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}"
                                                    tooltip="{{ trans('labels.inactive') }}"><i
                                                        class="fas fa-close"></i></a>
                                            @endif
                                        @endif
                                        @if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1))
                                            @if (in_array($currency->code, explode('|', helper::appdata($vendor_id)->currencies)))
                                                <a @if (env('Environment') == 'sendbox') onclick="myFunction()" @else onclick="statusupdate('{{ URL::to('admin/currency-settings/currencystatus-' . $currency->code . '/2') }}')" @endif
                                                    class="btn btn-sm btn-success btn-size {{ Auth::user()->type == 4 ? (helper::check_access('role_currency_settings', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}"
                                                    tooltip="{{ trans('labels.active') }}"><i class="fas fa-check"></i></a>
                                            @else
                                                <a @if (env('Environment') == 'sendbox') onclick="myFunction()" @else onclick="statusupdate('{{ URL::to('admin/currency-settings/currencystatus-' . $currency->code . '/1') }}')" @endif
                                                    class="btn btn-sm btn-danger btn-size {{ Auth::user()->type == 4 ? (helper::check_access('role_currency_settings', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}"
                                                    tooltip="{{ trans('labels.inactive') }}"><i
                                                        class="fas fa-close"></i></a>
                                            @endif
                                        @endif


                                    </td>
                                    <td>
                                        @if (helper::appdata($vendor_id)->default_currency == $currency->code)
                                            <a @if (env('Environment') == 'sendbox') onclick="myFunction()" @else onclick="statusupdate('{{ URL::to('admin/currency-settings/setdefault-' . $currency->code . '/2') }}')" @endif
                                                class="btn btn-sm btn-success btn-size {{ Auth::user()->type == 4 ? (helper::check_access('role_currency_settings', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}"
                                                tooltip="{{ trans('labels.active') }}"><i class="fas fa-check"></i></a>
                                        @else
                                            <a @if (env('Environment') == 'sendbox') onclick="myFunction()" @else onclick="statusupdate('{{ URL::to('admin/currency-settings/setdefault-' . $currency->code . '/1') }}')" @endif
                                                class="btn btn-sm btn-danger btn-size {{ Auth::user()->type == 4 ? (helper::check_access('role_currency_settings', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}"
                                                tooltip="{{ trans('labels.inactive') }}"><i class="fas fa-close"></i></a>
                                        @endif
                                    </td>
                                    <td>{{ helper::date_format($currency->created_at, $vendor_id) }}<br>
                                        {{ helper::time_format($currency->created_at, $vendor_id) }}
                                    </td>
                                    <td>{{ helper::date_format($currency->updated_at, $vendor_id) }}<br>
                                        {{ helper::time_format($currency->updated_at, $vendor_id) }}
                                    </td>

                                    @if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1))
                                        <td>
                                            <div class="d-flex flex-wrap gap-2">

                                                <a href="{{ URL::to('admin/currency-settings/currency/edit-' . $currency->id) }}"
                                                    class="btn btn-info btn-sm btn-size {{ Auth::user()->type == 4 ? (helper::check_access('role_currency_settings', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}"
                                                    tooltip="{{ trans('labels.edit') }}"> <i
                                                        class="fa-regular fa-pen-to-square"></i></a>
                                                @if (Strtoupper($currency->name) != 'USD')
                                                    <a @if (env('Environment') == 'sendbox') onclick="myFunction()" @else onclick="statusupdate('{{ URL::to('admin/currency-settings/delete-' . $currency->id . '/1') }}')" @endif
                                                        class="btn btn-danger btn-size btn-sm {{ Auth::user()->type == 4 ? (helper::check_access('role_currency_settings', Auth::user()->role_id, $vendor_id, 'delete') == 1 ? '' : 'd-none') : '' }}"
                                                        tooltip="{{ trans('labels.delete') }}"> <i
                                                            class="fa-regular fa-trash"></i></a>
                                                @endif
                                            </div>
                                        </td>
                                    @endif

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
