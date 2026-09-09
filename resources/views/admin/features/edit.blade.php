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
                <form action="{{ URL::to('/admin/features/update-' . $editfeature->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="form-group">
                            <label class="form-label">{{ trans('labels.title') }}<span class="text-danger"> *
                                </span></label>
                            <input type="text" class="form-control" name="title" value="{{ $editfeature->title }}"
                                placeholder="{{ trans('labels.title') }}" required>
                            @error('title')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ trans('labels.description') }}<span class="text-danger"> *
                                </span></label>
                            <textarea class="form-control" name="description" placeholder="{{ trans('labels.description') }}" rows="5"
                                required>{{ $editfeature->description }}</textarea>
                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        @include('admin.features._scope_fields', ['feature' => $editfeature])

                            <div class="form-group">
                            <label class="form-label">{{ trans('labels.image') }}
                                <span class="text-muted fs-7">({{ trans('labels.optional') }})</span></label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                            <small class="text-muted">{{ trans('messages.feature_image_optional') }}</small>
                            @error('image')
                                <span class="text-danger">{{ $message }} <br></span>
                            @enderror
                            <img src="{{ helper::image_path($editfeature->image) }}" class="img-fluid rounded hw-50"
                                alt="">
                        </div>
                    </div>
                    <div class="form-group m-0 mt-2 d-flex gap-2 justify-content-end">
                        <a href="{{ URL::to('admin/features') }}"
                            class="btn btn-danger px-4 rounded-start-5 rounded-end-5">{{ trans('labels.cancel') }}</a>
                        <button
                            @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif
                            class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_features', Auth::user()->role_id, Auth::user()->vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}">{{ trans('labels.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
