@extends('admin.layout.default')
@section('content')
    @php
        $ar = app()->getLocale() === 'ar';
        $action = $branch
            ? URL::to('admin/branches/save-' . $branch->id)
            : URL::to('admin/branches/save');
        $ful = (array) ($branch->fulfilment ?? ['delivery', 'pickup']);
    @endphp

    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-12">
            <h5 class="pages-title color-changer fs-2">
                {{ $branch ? trans('labels.edit') : trans('labels.add_new') }} — {{ trans('labels.locations_branches') }}
            </h5>
            @include('admin.layout.breadcrumb')
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12 mb-7">
            <div class="card border-0 box-shadow">
                <div class="card-body">
                    <form action="{{ $action }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-6 mb-3">
                                <label class="form-label">{{ trans('labels.name') }}<span class="text-danger"> *</span></label>
                                <input type="text" class="form-control" name="name"
                                    value="{{ old('name', $branch->name ?? '') }}" required
                                    placeholder="{{ $ar ? 'مثال: فرع المنامة' : 'e.g. Manama Branch' }}">
                            </div>
                            <div class="form-group col-md-3 mb-3">
                                <label class="form-label">{{ trans('labels.mobile') }}</label>
                                <input type="text" class="form-control" name="phone" value="{{ old('phone', $branch->phone ?? '') }}">
                            </div>
                            <div class="form-group col-md-3 mb-3">
                                <label class="form-label">{{ trans('labels.email') }}</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email', $branch->email ?? '') }}">
                            </div>
                        </div>

                        {{-- Online / remote providers stay searchable without a pin. --}}
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_remote" value="1" id="ocRemote"
                                {{ old('is_remote', $branch->is_remote ?? 2) == 1 ? 'checked' : '' }}>
                            <label class="form-check-label" for="ocRemote">
                                {{ trans('labels.online_remote_service') }}
                                <span class="text-muted fs-7">— {{ $ar ? 'يبقى ظاهراً في السوق بدون تحديد موقع' : 'stays searchable in the Marketplace without GPS' }}</span>
                            </label>
                        </div>

                        <h6 class="mb-2">{{ trans('labels.location') }}</h6>
                        @include('admin.partials.map_picker', [
                            'lat' => old('latitude', $branch->latitude ?? null),
                            'lng' => old('longitude', $branch->longitude ?? null),
                        ])

                        {{-- Location behaviour changes with the purchased system. --}}
                        <hr class="my-4">
                        <h6 class="mb-2">{{ \App\Helpers\Systems::label($system) }}</h6>
                        <div class="row">
                            @if ($system === \App\Helpers\Systems::ORDERS)
                                <div class="form-group col-md-4 mb-3">
                                    <label class="form-label">{{ trans('labels.delivery_coverage_km') }}</label>
                                    <input type="number" step="0.5" min="0" class="form-control" name="coverage_km"
                                        value="{{ old('coverage_km', $branch->coverage_km ?? '') }}" placeholder="8">
                                </div>
                                <div class="form-group col-md-8 mb-3">
                                    <label class="form-label d-block">{{ trans('labels.fulfilment') }}</label>
                                    @foreach (['delivery' => trans('labels.delivery'), 'pickup' => trans('labels.pickup'), 'dine_in' => trans('labels.dine_in')] as $k => $lbl)
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="fulfilment[]" value="{{ $k }}"
                                                id="ocFul{{ $k }}" {{ in_array($k, $ful) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="ocFul{{ $k }}">{{ $lbl }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif ($system === \App\Helpers\Systems::SERVICE)
                                <div class="form-group col-md-4 mb-3">
                                    <label class="form-label">{{ trans('labels.service_coverage_km') }}</label>
                                    <input type="number" step="0.5" min="0" class="form-control" name="coverage_km"
                                        value="{{ old('coverage_km', $branch->coverage_km ?? '') }}" placeholder="20">
                                    <small class="text-muted">{{ $ar ? 'نطاق تغطية المزود أو السائق.' : 'How far this provider or driver travels.' }}</small>
                                </div>
                            @else
                                <div class="col-12 mb-3">
                                    <p class="fs-7 text-muted mb-0">
                                        {{ $ar ? 'يتم استقبال العملاء في هذا الفرع — لا حاجة لنطاق تغطية.' : 'Customers are seen at this branch — no coverage radius needed.' }}
                                    </p>
                                </div>
                            @endif

                            <div class="form-group col-md-4 mb-3">
                                <label class="form-label">{{ trans('labels.status') }}</label>
                                <select name="is_available" class="form-select">
                                    <option value="1" {{ old('is_available', $branch->is_available ?? 1) == 1 ? 'selected' : '' }}>{{ trans('labels.active') }}</option>
                                    <option value="0" {{ old('is_available', $branch->is_available ?? 1) == 2 ? 'selected' : '' }}>{{ trans('labels.inactive') }}</option>
                                </select>
                            </div>
                            @if ($branch)
                                <div class="form-group col-md-4 mb-3">
                                    <label class="form-label">{{ trans('labels.reason') }}</label>
                                    <input type="text" class="form-control" name="change_reason"
                                        placeholder="{{ $ar ? 'سبب تغيير الموقع (اختياري)' : 'Reason for moving the pin (optional)' }}">
                                </div>
                            @endif
                        </div>

                        <div class="alert alert-warning fs-7 mb-3">
                            {{ trans('messages.location_review_notice') }}
                        </div>

                        <div class="form-group m-0 mt-2 d-flex gap-2 justify-content-end">
                            <a href="{{ URL::to('admin/branches') }}" class="btn btn-danger px-4 rounded-start-5 rounded-end-5">{{ trans('labels.cancel') }}</a>
                            <button class="btn btn-secondary px-4 rounded-start-5 rounded-end-5"
                                @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif>
                                {{ trans('labels.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
