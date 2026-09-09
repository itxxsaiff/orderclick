@extends('admin.layout.default')
@php
    if(Auth::user()->type == 4)
    {
        $vendor_id = Auth::user()->vendor_id;
    }else{
        $vendor_id = Auth::user()->id;
    }
    
@endphp
@section('content')
    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-12 col-md-4">
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.edit') }}</h5>
            <div class="d-flex">
                @include('admin.layout.breadcrumb')
            </div>
        </div>
       
    </div>

        <div class="col-12 mb-7 mt-3">
            <div class="card border-0 box-shadow">
                <div class="card-body">
                    <form action="{{ URL::to('admin/categories/update-' . $editcategory->slug) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="form-group row ">

                                <div class="col-12 col-md-6">
                                    <label class="form-label">{{ trans('labels.name') }}<span class="text-danger"> *
                                        </span></label>
                                    <input type="text" class="form-control" name="category_name"
                                        value="{{ $editcategory->name }}" placeholder="{{ trans('labels.name') }}" required>
                                </div>                                        
                                @error('category_name')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror


                                <div class="col-12 col-md-6">
                                    <label class="form-label">{{ trans('labels.image') }}<span class="text-danger"> *
                                        </span></label>
                                    <input type="file" class="form-control" name="category_image"
                                        value="{{ old('category_image') }}" placeholder="{{ trans('labels.image') }}">
                                    <img src="{{url(env('ASSETSPATHURL').'admin-assets/images/category/'.$editcategory->image)}}" class="img-fluid rounded-3 hw-70 mt-2 object-fit-cover" alt="">
                                </div>
                                @error('category_image')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror


                            </div>
                            <div class="form-group m-0 mt-2 d-flex gap-2 justify-content-end">
                                <a href="{{ URL::to('admin/categories') }}" class="btn btn-danger px-4 rounded-start-5 rounded-end-5">{{
                                    trans('labels.cancel') }}</a>
                                <button class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_categories', Auth::user()->role_id, $vendor_id, 'edit') == 1  ? '' : 'd-none') : '' }}" @if (env('Environment') == 'sendbox') type="button"
                                    onclick="myFunction()" @else type="submit" @endif>{{ trans('labels.save')
                                    }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
@endsection