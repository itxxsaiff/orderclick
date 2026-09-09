@extends('admin.layout.default')
@php
    if (Auth::user()->type == 4) {
        $vendor_id = Auth::user()->vendor_id;
    } else {
        $vendor_id = Auth::user()->id;
    }
@endphp
@section('content')
    <div class="row justify-content-between align-items-center">
        <div class="col-12">
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.edit') }}</h5>
            @include('admin.layout.breadcrumb')
        </div>
    </div>

    <div class="col-12 mt-3 mb-7">
        <div class="card border-0 box-shadow">
            <div class="card-body">
                <form action="{{ URL::to('/admin/faqs/update-' . $getfaq->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="form-group">
                            <label class="form-label">{{ trans('labels.question') }}<span class="text-danger"> *
                                </span></label>
                            <input type="text" class="form-control" name="question" value="{{ $getfaq->question }}"
                                placeholder="{{ trans('labels.question') }}" required>
                            @error('question')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ trans('labels.answer') }}<span class="text-danger"> *
                                </span></label>
                            <textarea class="form-control" name="answer" placeholder="{{ trans('labels.answer') }}" rows="5" required>{{ $getfaq->answer }}</textarea>
                            @error('answer')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group m-0 mt-2 d-flex gap-2 justify-content-end">
                        <a href="{{ URL::to('admin/faqs') }}"
                            class="btn btn-danger px-4 rounded-start-5 rounded-end-5">{{ trans('labels.cancel') }}</a>
                        <button
                            @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif
                            class="btn btn-secondary px-4 rounded-start-5 rounded-end-5">{{ trans('labels.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
