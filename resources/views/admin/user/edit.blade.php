@extends('admin.layout.default')

@section('content')
    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-12">
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.edit') }}</h5>
            @include('admin.layout.breadcrumb')
        </div>
    </div>

    <div class="col-12 mb-7">

        <div class="card border-0 box-shadow">

            <div class="card-body">

                <form action="{{ URL::to('admin/users/update-' . $getuserdata->id) }}" method="post"
                    enctype="multipart/form-data">

                    @csrf
                    <div class="row">

                        @if (Auth::user()->type == 1)
                            {{-- Super admin: move the account to another System / Activity. --}}
                            @php $ocUserSystem = \App\Helpers\Systems::normalise($getuserdata->system); @endphp
                            <div class="form-group col-md-4">
                                <label class="form-label">{{ trans('labels.system') }}</label>
                                <select name="system" id="ocSystem" class="form-select" data-orig="{{ $ocUserSystem }}">
                                    @foreach (\App\Helpers\Systems::all() as $ocSys)
                                        <option value="{{ $ocSys['key'] }}" {{ $ocUserSystem === $ocSys['key'] ? 'selected' : '' }}>{{ $ocSys['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-label">{{ trans('labels.activity') }}</label>
                                <select name="activity_id" id="ocActivity" class="form-select">
                                    <option value="">{{ trans('labels.select') }}</option>
                                    @foreach ($activities as $ocAct)
                                        <option value="{{ $ocAct->id }}" data-system="{{ $ocAct->system }}"
                                            {{ (string) $getuserdata->activity_id === (string) $ocAct->id ? 'selected' : '' }}>{{ $ocAct->display_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-label">{{ trans('labels.specialization') }}</label>
                                <select name="specialization_id" id="ocSpecialization" class="form-select">
                                    <option value="">{{ trans('labels.select') }}</option>
                                    @foreach ($specializations as $ocSpec)
                                        <option value="{{ $ocSpec->id }}" data-activity="{{ $ocSpec->activity_id }}"
                                            {{ (string) $getuserdata->specialization_id === (string) $ocSpec->id ? 'selected' : '' }}>{{ $ocSpec->display_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <small class="text-muted">{{ trans('messages.vendor_system_change_hint') }}</small>
                            </div>
                        @endif

                        <div class="form-group col-md-6">
                            <label for="store" class="form-label">{{ trans('labels.store_categories') }}<span
                                    class="text-danger">
                                    * </span></label>
                            <select name="store" id="ocStore" class="form-select" required>
                                <option value="">{{ trans('labels.select') }}</option>
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}" data-system="{{ \App\Helpers\Systems::normalise($store->system ?: null) }}"
                                        {{ $store->id == $getuserdata->store_id ? 'selected' : '' }}>
                                        {{ $store->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6 form-group">
                            <input type="hidden" value="{{ $getuserdata->id }}" name="id">
                            <label class="form-label">{{ trans('labels.name') }}<span class="text-danger"> *
                                </span></label>
                            <input type="text" class="form-control" name="name" value="{{ $getuserdata->name }}"
                                placeholder="{{ trans('labels.name') }}" required>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-sm-6 form-group">
                            <label class="form-label">{{ trans('labels.email') }}<span class="text-danger"> *
                                </span></label>
                            <input type="email" class="form-control" dir="ltr" name="email" value="{{ $getuserdata->email }}"
                                placeholder="{{ trans('labels.email') }}" required>
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-sm-6 form-group">
                            <div class="form-group">
                                <label class="form-label">{{ trans('labels.mobile') }}<span class="text-danger"> *
                                    </span></label>
                                <input type="number" class="form-control" dir="ltr" name="mobile"
                                    value="{{ $getuserdata->mobile }}" placeholder="{{ trans('labels.mobile') }}"
                                    required>
                                @error('mobile')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label class="form-label">{{ trans('labels.image') }} (250 x 250) </label>
                            <input type="file" class="form-control" name="profile">
                            <img class="rounded-circle mb-2 mt-1" src="{{ helper::image_path($getuserdata->image) }}"
                                alt="" width="70" height="70">
                            @error('profile')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- City / area as the merchant typed them at signup (the old fixed city list is not used for new stores). --}}
                        <div class="form-group col-md-6">
                            <label for="city_name" class="form-label">{{ trans('labels.city') }}</label>
                            <input type="text" class="form-control" name="city_name" id="city_name" maxlength="120"
                                value="{{ $getuserdata->city_name ?: optional($cities->firstWhere('id', $getuserdata->city_id))->name }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="area_name" class="form-label">{{ trans('labels.area') }}</label>
                            <input type="text" class="form-control" name="area_name" id="area_name" maxlength="120"
                                value="{{ $getuserdata->area_name }}">
                        </div>
                        @if (App\Models\SystemAddons::where('unique_identifier', 'unique_slug')->first() != null &&
                                App\Models\SystemAddons::where('unique_identifier', 'unique_slug')->first()->activated == 1)
                            @if (helper::checkcustomdomain($getuserdata->id) == null)
                                <div class="form-group">
                                    <label for="basic-url" class="form-label">{{ trans('labels.personlized_link') }}<span
                                            class="text-danger"> * </span></label>
                                    @if (env('Environment') == 'sendbox')
                                        <span class="badge badge bg-danger ms-2 mb-0">{{ trans('labels.addon') }}</span>
                                    @endif
                                    <div class="input-group" dir="ltr">
                                        <span
                                            class="input-group-text col-5 col-lg-auto overflow-x-auto {{ session()->get('direction') == 2 ? 'rounded-start-0 rounded-end-5' : 'rounded-start-5 rounded-end-0' }}">{{ URL::to('/') }}/</span>
                                        <input type="text"
                                            class="form-control {{ session()->get('direction') == 2 ? 'rounded-end-0 rounded-start-5' : 'rounded-start-0 rounded-end-5' }}"
                                            id="slug" name="slug" value="{{ $getuserdata->slug }}" required>
                                    </div>
                                </div>
                            @endif
                        @endif
                        <div class="col-sm-6">
                            @if (App\Models\SystemAddons::where('unique_identifier', 'allow_without_subscription')->first() != null &&
                                    App\Models\SystemAddons::where('unique_identifier', 'allow_without_subscription')->first()->activated == 1)
                                <div class="form-group" id="plan">
                                    @php
                                        $plan = helper::plandetail(@$getuserdata->plan_id);
                                    @endphp
                                    <div class="d-flex">
                                        <input class="form-check-input mx-1" type="checkbox" name="plan_checkbox"
                                            id="plan_checkbox">
                                        <div>
                                            <label for="plan_checkbox"
                                                class="form-label">{{ trans('labels.assign_plan') }}</label>&nbsp;<span>({{ trans('labels.current_plan') }}&nbsp;:&nbsp;</span>
                                            <span class="fw-500"> {{ !empty($plan) ? $plan->name : '-' }})</span>
                                            @if (env('Environment') == 'sendbox')
                                                <span
                                                    class="badge badge bg-danger ms-2 mb-0">{{ trans('labels.addon') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <select name="plan" id="selectplan" class="form-select" disabled>
                                        <option value="">{{ trans('labels.select') }}</option>
                                        @foreach ($getplanlist as $plan)
                                            @if ($plan->vendor_id != '' && $plan->vendor_id != null)
                                                @if (in_array($getuserdata->id, explode('|', $plan->vendor_id)))
                                                    <option value="{{ $plan->id }}"
                                                        {{ $getuserdata->plan_id == $plan->id ? 'selected' : '' }}>
                                                        {{ $plan->name }}
                                                    </option>
                                                @endif
                                            @else
                                                <option value="{{ $plan->id }}"
                                                    {{ $getuserdata->plan_id == $plan->id ? 'selected' : '' }}>
                                                    {{ $plan->name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>

                                </div>


                                <div class="form-group d-flex">
                                    <input class="form-check-input mx-1" type="checkbox" name="allow_store_subscription"
                                        id="allow_store_subscription" @if ($getuserdata->allow_without_subscription == '1') checked @endif>
                                    <div>
                                        <label class="form-check-label"
                                            for="allow_store_subscription">{{ trans('labels.allow_store_without_subscription') }}
                                        </label>
                                        @if (env('Environment') == 'sendbox')
                                            <span class="badge badge bg-danger ms-2">{{ trans('labels.addon') }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            <div class="form-group d-flex">
                                <input class="form-check-input mx-1" type="checkbox" name="show_landing_page"
                                    id="show_landing_page" @if ($getuserdata->available_on_landing == '1') checked @endif><label
                                    class="form-check-label"
                                    for="show_landing_page">{{ trans('labels.display_store_on_landing') }}</label>

                            </div>
                        </div>
                        <div class="form-group m-0 mt-2 d-flex gap-2 justify-content-end">
                            <a href="{{ URL::to('admin/users') }}"
                                class="btn btn-danger px-4 rounded-start-5 rounded-end-5">{{ trans('labels.cancel') }}</a>
                            <button
                                @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif
                                class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_vendors', Auth::user()->role_id, Auth::user()->vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}">{{ trans('labels.save') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection
@section('scripts')
    <script>
        var areaurl = "{{ URL::to('admin/getarea') }}";
        var select = "{{ trans('labels.select') }}";
        var areaid = "{{ $getuserdata->area_id }}";
    </script>
    <script src="{{ url('storage/app/public/admin-assets/js/user.js') }}"></script>
    <script>
        // Super admin: Activity, Specialization and Category lists follow the chosen System.
        (function () {
            var sys = document.getElementById('ocSystem');
            if (!sys) return;
            var act = document.getElementById('ocActivity'), spec = document.getElementById('ocSpecialization'),
                store = document.getElementById('ocStore');
            function filter(select, attr, value) {
                Array.prototype.forEach.call(select.options, function (o) {
                    if (!o.value) return;
                    var show = o.getAttribute(attr) === value;
                    o.hidden = !show; o.disabled = !show;
                    if (!show && o.selected) select.value = '';
                });
            }
            function refresh() {
                filter(act, 'data-system', sys.value);
                filter(spec, 'data-activity', act.value);
                if (store) filter(store, 'data-system', sys.value);
                // Moving to another system needs an activity in that system.
                act.required = sys.value !== sys.getAttribute('data-orig');
            }
            sys.addEventListener('change', refresh);
            act.addEventListener('change', function () { filter(spec, 'data-activity', act.value); });
            refresh();
        })();
    </script>
@endsection
