@extends('admin.layout.default')
@section('content')
<div class="row justify-content-between align-items-center">
    <div class="col-12">
        <h5 class="pages-title color-changer fs-2">{{trans('labels.edit')}}</h5>
        @include('admin.layout.breadcrumb')
    </div>
</div>
    <div class="col-12 mb-7 mt-3">
        <div class="card border-0 box-shadow">
            <div class="card-body">
                <form action="{{URL::to('admin/areas/update-'.$editarea->id)}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="form-label">{{trans('labels.city')}}<span class="text-danger"> * </span></label>
                            <select name="city" class="form-select" required>
                                <option value="">{{trans('labels.select')}}</option>
                                @foreach($allcity as $city)
                                <option value="{{$city->id}}" {{$city->id == $editarea->city_id ? 'selected' : ''}}>{{$city->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">{{trans('labels.area')}}<span class="text-danger"> * </span></label>
                            <input type="text" class="form-control" name="name" value="{{$editarea->area}}" placeholder="{{trans('labels.area')}}" required>
                            @error('name')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group m-0 mt-2 d-flex gap-2 justify-content-end">
                            <a href="{{ URL::to('admin/areas') }}" class="btn btn-danger px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_areas', Auth::user()->role_id, $vendor_id, 'edit') == 1  ? '' : 'd-none') : '' }}">{{ trans('labels.cancel') }}</a>
                            <button class="btn btn-secondary px-4 rounded-start-5 rounded-end-5" @if(env('Environment')=='sendbox' ) type="button" onclick="myFunction()" @else type="submit" @endif>{{ trans('labels.save') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection