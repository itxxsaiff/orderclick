@extends('admin.layout.default')
@section('content')
    <div class="row mb-3">
        <div class="col-12">
            <h5 class="pages-title color-changer fs-2">Edit</h5>
            @include('admin.layout.breadcrumb')
        </div>
    </div>
    <div class="col-12 mb-7 mt-3">
        <div class="card border-0 box-shadow">
            <div class="card-body">
                <form action="{{ URL::to('/admin/language-settings/update-' . $getlanguage->id) }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-sm-3 col-md-12">
                            <div class="form-group mb-3">
                                <label for="layout" class="col-form-label">{{ trans('labels.layout') }} <span
                                        class="text-danger"> *
                                    </span></label>
                                <select name="layout" class="form-control layout-dropdown" id="layout" required>
                                    <option value="" selected>{{ trans('labels.select') }}</option>
                                    <option value="1"{{ $getlanguage->layout == '1' ? 'selected' : '' }}>
                                        {{ trans('labels.ltr') }}</option>
                                    <option value="2"{{ $getlanguage->layout == '2' ? 'selected' : '' }}>
                                        {{ trans('labels.rtl') }}</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="layout" class="col-form-label">{{ trans('labels.image') }}</label>
                                <input type="file" class="form-control" name="image">
                                <img src="{{ helper::image_path($getlanguage->image) }}"
                                    class="img-fluid rounded hw-50 mt-1" alt="">
                            </div>

                        </div>
                    </div>
                    <div class="form-group m-0 mt-2 d-flex gap-2 justify-content-end">
                        <a href="{{ URL::to('admin/language-settings') }}"
                            class="btn btn-danger px-4 rounded-start-5 rounded-end-5">{{ trans('labels.cancel') }}</a>
                        <button
                            @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif
                            class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_language_settings', Auth::user()->role_id, Auth::user()->vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}">{{ trans('labels.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
