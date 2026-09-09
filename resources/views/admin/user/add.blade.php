@extends('admin.layout.default')
@section('content')
    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-12">
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.add_new') }}</h5>
            @include('admin.layout.breadcrumb')
        </div>
    </div>
    <div class="col-12 mb-7">
        <div class="card border-0 box-shadow">
            <div class="card-body">
                @if (isset($id))
                    <form action="{{ URL::to('admin/clonevendor') }}" method="POST">
                        <input type="hidden" class="form-control" name="clone_vendor_id" value="{{ @$id }} "
                            required>
                    @else
                        <form action="{{ URL::to('admin/register_vendor') }}" method="POST">
                @endif
                @csrf
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="store" class="form-label">{{ trans('labels.store_categories') }}<span
                                class="text-danger">
                                * </span></label>
                        <select name="store" class="form-select" required>
                            <option value="">{{ trans('labels.select') }}</option>
                            @foreach ($stores as $store)
                                <option value="{{ $store->id }}" {{ old('store') == $store->id ? 'selected' : '' }}>
                                    {{ $store->name }}
                                </option>
                            @endforeach
                        </select>

                    </div>
                    <div class="form-group col-md-6">
                        <label for="name" class="form-label">{{ trans('labels.name') }}<span class="text-danger">
                                * </span></label>
                        <input type="text" class="form-control" name="name" value="{{ old('name') }}"
                            id="name" placeholder="{{ trans('labels.name') }}" required>
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="email" class="form-label">{{ trans('labels.email') }}<span class="text-danger">
                                * </span></label>
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}"
                            id="email" placeholder="{{ trans('labels.email') }}" required>
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="mobile" class="form-label">{{ trans('labels.mobile') }}<span class="text-danger">
                                * </span></label>
                        <input type="number" class="form-control" name="mobile" value="{{ old('mobile') }}"
                            id="mobile" placeholder="{{ trans('labels.mobile') }}" required>
                        @error('mobile')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="password" class="form-label">{{ trans('labels.password') }}<span class="text-danger"> *
                            </span></label>
                        <input type="password" class="form-control" name="password" value="{{ old('password') }}"
                            id="password" placeholder="{{ trans('labels.password') }}" required>
                        @error('password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="city" class="form-label">{{ trans('labels.city') }}<span class="text-danger"> *
                            </span></label>
                        <select name="city" class="form-select" id="city" required>
                            <option value="">{{ trans('labels.select') }}</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>

                    </div>
                    <div class="form-group col-md-6">
                        <label for="area" class="form-label">{{ trans('labels.area') }}<span class="text-danger"> *
                            </span></label>
                        <select name="area" class="form-select" id="area" required>
                            <option value="">{{ trans('labels.select') }}</option>
                        </select>

                    </div>
                    @if (App\Models\SystemAddons::where('unique_identifier', 'unique_slug')->first() != null &&
                            App\Models\SystemAddons::where('unique_identifier', 'unique_slug')->first()->activated == 1)
                        <div class="form-group col-md-6">
                            <label for="basic-url" class="form-label">{{ trans('labels.personlized_link') }}<span
                                    class="text-danger"> * </span></label>
                            @if (env('Environment') == 'sendbox')
                                <span class="badge badge bg-danger ms-2 mb-0">{{ trans('labels.addon') }}</span>
                            @endif
                            <div class="input-group ">
                                <span
                                    class="input-group-text col-5 col-lg-auto overflow-x-auto {{ session()->get('direction') == 2 ? 'rounded-start-0 rounded-end-5' : 'rounded-start-5 rounded-end-0' }}">{{ URL::to('/') }}/</span>
                                <input type="text"
                                    class="form-control {{ session()->get('direction') == 2 ? 'rounded-end-0 rounded-start-5' : 'rounded-start-0 rounded-end-5' }}"
                                    id="slug" name="slug" value="{{ old('slug') }}" required>
                            </div>

                        </div>
                    @endif
                </div>

                <div class="form-group mt-2 m-0 d-flex gap-2 justify-content-end">

                    <a href="{{ URL::to('admin/users') }}"
                        class="btn btn-danger px-4 rounded-start-5 rounded-end-5">{{ trans('labels.cancel') }}</a>

                    <button
                        class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_vendors', Auth::user()->role_id, Auth::user()->vendor_id, 'add') == 1 ? '' : 'd-none') : '' }}"
                        @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif>{{ trans('labels.save') }}
                    </button>

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
        var areaid = '0';
        $('#name').on('blur', function() {
            "use strict";
            $('#slug').val($('#name').val().split(" ").join("-").toLowerCase());
        });
    </script>
    <script src="{{ url(env('ASSETSPATHURL') . '/admin-assets/js/user.js') }}"></script>
@endsection
