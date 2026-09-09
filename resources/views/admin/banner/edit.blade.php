@extends('admin.layout.default')
@php
    if (Auth::user()->type == 4) {
        $vendor_id = Auth::user()->vendor_id;
    } else {
        $vendor_id = Auth::user()->id;
    }
@endphp
@section('content')
    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-12">
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.edit') }}</h5>
        </div>
        @include('admin.layout.breadcrumb')
    </div>

    <div class="col-12 mb-7 mt-3">
        <div class="card border-0 box-shadow">
            <div class="card-body">
                <form action="{{ URL::to('admin/banner/update-' . $getbannerdata->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="form-group">
                            <label class="form-label">{{ trans('labels.image') }} <span class="text-danger"> *
                                </span></label>
                            <input type="file" class="form-control" name="image" required>
                            @error('image')
                                <span class="text-danger">{{ $message }}</span><br>
                            @enderror
                            <img src="{{ helper::image_path($getbannerdata->banner_image) }}"
                                class="img-fluid rounded-3 hight-50 mt-2 object-fit-cover" alt="">
                        </div>
                    </div>
                    <div class="form-group m-0 mt-2 d-flex gap-2 justify-content-end">
                        <a href="{{ URL::to('admin/banner') }}"
                            class="btn btn-danger px-4 rounded-start-5 rounded-end-5">{{ trans('labels.cancel') }}</a>
                        <button
                            class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_banner', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}"
                            @if (env('Environment') == 'sendbox') type="button"
                                    onclick="myFunction()" @else type="submit" @endif>
                            {{ trans('labels.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
