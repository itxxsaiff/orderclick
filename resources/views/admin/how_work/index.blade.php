@extends('admin.layout.default')

@section('content')
    @php
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
    @endphp

    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-12 col-md-4">
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.how_works') }}</h5>
            @include('admin.layout.breadcrumb')
        </div>
    </div>

    <div class="row mt-3">

        <div class="col-12">

            <form action="{{ URL::to('admin/how_works/savecontent') }}" method="POST">

                @csrf

                <div class="card border-0 mb-3 p-3">

                    <div class="row">

                        <div class="col-md-6 mb-lg-0">

                            <div class="form-group">

                                <label class="form-label">{{ trans('labels.title') }}<span class="text-danger"> *

                                    </span></label>

                                <input type="text"
                                    class="form-control {{ session()->get('direction') == 2 ? 'input-group-rtl' : '' }}"
                                    name="title" placeholder="{{ trans('labels.title') }}"
                                    value="{{ @$content->work_title }}" required>

                                @error('title')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror

                            </div>

                        </div>

                        <div class="col-md-6 mb-lg-0">

                            <div class="form-group">

                                <label class="form-label">{{ trans('labels.sub_title') }}<span class="text-danger"> *

                                    </span></label>

                                <input type="text"
                                    class="form-control {{ session()->get('direction') == 2 ? 'input-group-rtl' : '' }}"
                                    name="subtitle" placeholder="{{ trans('labels.sub_title') }}"
                                    value="{{ @$content->work_subtitle }}" required>

                                @error('subtitle')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 mb-lg-0">
                            <div class="form-group">
                                <label class="form-label">{{ trans('labels.title_arabic') }}
                                    <span class="text-muted fs-7">({{ trans('labels.optional') }})</span></label>
                                <input type="text" class="form-control input-group-rtl" dir="rtl" name="title_ar"
                                    placeholder="{{ trans('labels.title_arabic') }}"
                                    value="{{ @$content->work_title_ar }}">
                                <small class="text-muted">{{ trans('messages.arabic_optional_note') }}</small>
                            </div>
                        </div>

                        <div class="col-md-6 mb-lg-0">
                            <div class="form-group">
                                <label class="form-label">{{ trans('labels.sub_title_arabic') }}
                                    <span class="text-muted fs-7">({{ trans('labels.optional') }})</span></label>
                                <input type="text" class="form-control input-group-rtl" dir="rtl" name="subtitle_ar"
                                    placeholder="{{ trans('labels.sub_title_arabic') }}"
                                    value="{{ @$content->work_subtitle_ar }}">
                                <small class="text-muted">{{ trans('messages.arabic_optional_note') }}</small>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit"
                                class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_how_works', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}">{{ trans('labels.save') }}</button>
                        </div>
                    </div>
                </div>
            </form>
            <div class="card border-0 mb-3">
                <div class="d-flex justify-content-end">
                    <a href="{{ URL::to('admin/how_works/add') }}"
                        class="btn btn-secondary px-4 mx-3 mt-3 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_how_works', Auth::user()->role_id, $vendor_id, 'add') == 1 ? '' : 'd-none') : '' }}">
                        <i class="fa-regular fa-plus mx-1"></i>{{ trans('labels.add') }}
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered py-3 zero-configuration w-100 dataTable no-footer">
                            <thead>
                                <tr class="text-uppercase fw-500">
                                    <td></td>
                                    <td>{{ trans('labels.step') }}</td>
                                    <td>{{ trans('labels.title') }}</td>
                                    <td>{{ trans('labels.sub_title') }}</td>
                                    <td>{{ trans('labels.image') }}</td>
                                    <td>{{ trans('labels.created_date') }}</td>
                                    <td>{{ trans('labels.updated_date') }}</td>
                                    <td>{{ trans('labels.action') }}</td>
                                </tr>
                            </thead>
                            <tbody id="tabledetails" data-url="{{ url('admin/how_works/reorder_status') }}">
                                @php
                                    $i = 1;
                                @endphp
                                @foreach ($allworkcontent as $content)
                                    <tr class="fs-7 row1" id="dataid{{ $content->id }}" data-id="{{ $content->id }}">
                                        <td><a tooltip="{{ trans('labels.move') }}">
                                                <i class="fa-light fa-up-down-left-right mx-2"></i>
                                            </a>
                                        </td>
                                        <td>
                                            {{-- Display Order, not a running count, so the number
                                                 matches what the landing page renders. --}}
                                            <span class="badge bg-success rounded-circle p-2">{{ $content->reorder_id ?: $i }}</span>
                                            @php $i++; @endphp
                                        </td>
                                        <td>{{ $content->title }}</td>
                                        <td>{{ $content->sub_title }}</td>
                                        <td>
                                            @if (!empty($content->image) && file_exists(storage_path('app/public/landing/images/png/' . $content->image)))
                                                <img src="{{ helper::image_path($content->image) }}"
                                                    class="img-fluid rounded-3 hw-50 object-fit-cover" alt="">
                                            @else
                                                {{-- Images are uploaded later; a default icon stands in until then. --}}
                                                <div class="hw-50 rounded-3 d-flex align-items-center justify-content-center bg-light text-muted"
                                                    tooltip="{{ trans('messages.step_image_optional') }}">
                                                    <i class="fa-regular fa-image"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ helper::date_format($content->created_at, $vendor_id) }}<br>
                                            {{ helper::time_format($content->created_at, $vendor_id) }}
                                        </td>
                                        <td>{{ helper::date_format($content->updated_at, $vendor_id) }}<br>
                                            {{ helper::time_format($content->updated_at, $vendor_id) }}
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ URL::to('/admin/how_works/edit-' . $content->id) }}"
                                                    class="btn btn-info btn-sm btn-size {{ Auth::user()->type == 4 ? (helper::check_access('role_how_works', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}"
                                                    tooltip="{{ trans('labels.edit') }}">
                                                    <i class="fa-regular fa-pen-to-square"></i>
                                                </a>
                                                <a href="javascript:void(0)"
                                                    @if (env('Environment') == 'sendbox') onclick="myFunction()" @else onclick="statusupdate('{{ URL::to('/admin/how_works/delete-' . $content->id) }}')" @endif
                                                    class="btn btn-danger btn-sm btn-size {{ Auth::user()->type == 4 ? (helper::check_access('role_how_works', Auth::user()->role_id, $vendor_id, 'delete') == 1 ? '' : 'd-none') : '' }}"
                                                    tooltip="{{ trans('labels.delete') }}">
                                                    <i class="fa-regular fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
