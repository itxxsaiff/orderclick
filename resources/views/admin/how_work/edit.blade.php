@extends('admin.layout.default')
@section('content')
    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-12">
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.edit') }}</h5>
            @include('admin.layout.breadcrumb')
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card border-0 box-shadow">
                <div class="card-body">
                    <form action="{{ URL::to('/admin/how_works/update-' . $editwork->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="form-label">{{ trans('labels.title') }}<span class="text-danger"> *
                                    </span></label>
                                <input type="text" class="form-control" name="title" value="{{ $editwork->title }}"
                                    placeholder="{{ trans('labels.title') }}" required>
                                @error('title')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label">{{ trans('labels.sub_title') }}<span class="text-danger"> *
                                    </span></label>
                                <input type="text" class="form-control" name="subtitle"
                                    value="{{ $editwork->sub_title }}" placeholder="{{ trans('labels.sub_title') }}"
                                    required>
                                @error('subtitle')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label">{{ trans('labels.title_arabic') }}
                                    <span class="text-muted fs-7">({{ trans('labels.optional') }})</span></label>
                                <input type="text" class="form-control input-group-rtl" dir="rtl"
                                    name="title_ar" value="{{ old('title_ar', $editwork->title_ar) }}"
                                    placeholder="{{ trans('labels.title_arabic') }}">
                                <small class="text-muted">{{ trans('messages.arabic_optional_note') }}</small>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label">{{ trans('labels.sub_title_arabic') }}
                                    <span class="text-muted fs-7">({{ trans('labels.optional') }})</span></label>
                                <input type="text" class="form-control input-group-rtl" dir="rtl"
                                    name="subtitle_ar" value="{{ old('subtitle_ar', $editwork->sub_title_ar) }}"
                                    placeholder="{{ trans('labels.sub_title_arabic') }}">
                                <small class="text-muted">{{ trans('messages.arabic_optional_note') }}</small>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label">{{ trans('labels.display_order') }}</label>
                                <input type="number" min="1" class="form-control" name="reorder_id"
                                    value="{{ old('reorder_id', $editwork->reorder_id) }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label">{{ trans('labels.image') }}
                                    <span class="text-muted fs-7">({{ trans('labels.optional') }})</span></label>
                                <input type="file" class="form-control" name="image" accept="image/*">
                                @error('image')
                                    <span class="text-danger">{{ $message }}</span> <br>
                                @enderror
                                <img src="{{ helper::image_path($editwork->image) }}" class="img-fluid rounded hw-70 mt-1"
                                    alt="">
                            </div>
                        </div>
                        <div class="form-group m-0 mt-2 d-flex gap-2 justify-content-end">
                            <a href="{{ URL::to('admin/how_works') }}"
                                class="btn btn-danger px-4 rounded-start-5 rounded-end-5">{{ trans('labels.cancel') }}</a>
                            <button
                                @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif
                                class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_how_works', Auth::user()->role_id, Auth::user()->vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}">{{ trans('labels.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
