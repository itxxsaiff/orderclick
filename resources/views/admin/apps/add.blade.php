@extends('admin.layout.default')
@section('content')
<div class="row justify-content-between align-items-center mb-3">
    <div class="col-12 col-md-12">
        <h5 class="pages-title color-changer fs-2">{{ trans('labels.add_new') }}</h5>
        @include('admin.layout.breadcrumb')
    </div>
</div>
<div class="row mb-7">
    <div class="col-12">
        <div class="card border-0 mb-3 box-shadow">
            <div class="card-body">
                <form method="post" action="{{ URL::to('admin/systemaddons/store') }}" name="about" id="about" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-sm-3 col-md-12">
                            <div class="form-group">
                                <label for="addon_zip" class="col-form-label">{{ trans('labels.zip_file') }}<span class="text-danger"> * </span></label>
                                <input type="file" class="form-control" name="addon_zip" id="addon_zip" required="">
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-end">
                        @if (env('Environment') == 'sendbox')
                        <button type="button" class="btn btn-primary px-4 rounded-start-5 rounded-end-5 mt-3" onclick="myFunction()">
                            {{ trans('labels.install') }}
                        </button>
                        @else
                        <button type="submit" class="btn btn-primary px-4 rounded-start-5 rounded-end-5 mt-3">{{ trans('labels.install') }}</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection