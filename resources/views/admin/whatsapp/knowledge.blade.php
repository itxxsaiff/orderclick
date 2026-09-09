@extends('admin.layout.default')
@section('content')
    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-12">
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.ai_knowledge_base') }}</h5>
            <p class="fs-7 text-muted mb-1">{{ trans('messages.kb_sub') }}</p>
            @include('admin.layout.breadcrumb')
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-5 mb-3">
            <div class="card border-0 box-shadow">
                <div class="card-body">
                    <h6 class="mb-3">{{ trans('labels.add') }}</h6>
                    <form method="POST" action="{{ URL::to('admin/whatsapp/knowledge/save') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">{{ trans('labels.question') }}<span class="text-danger"> *</span></label>
                            <input type="text" class="form-control" name="question" required
                                placeholder="{{ trans('messages.kb_question_example') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ trans('labels.answer') }}<span class="text-danger"> *</span></label>
                            <textarea class="form-control" name="answer" rows="4" required
                                placeholder="{{ trans('messages.kb_answer_example') }}"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ trans('labels.status') }}</label>
                            <select name="is_available" class="form-select">
                                <option value="1">{{ trans('labels.active') }}</option>
                                <option value="2">{{ trans('labels.inactive') }}</option>
                            </select>
                        </div>
                        <button class="btn btn-secondary w-100 rounded-start-5 rounded-end-5"
                            @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif>
                            {{ trans('labels.save') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-7 mb-7">
            <div class="card border-0 box-shadow">
                <div class="card-body">
                    <div class="alert alert-success fs-7">{{ trans('messages.kb_shared_note') }}</div>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr class="fs-7 fw-500">
                                    <td>{{ trans('labels.question') }}</td>
                                    <td>{{ trans('labels.answer') }}</td>
                                    <td>{{ trans('labels.status') }}</td>
                                    <td>{{ trans('labels.action') }}</td>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($entries as $e)
                                    <tr class="fs-7">
                                        <td>{{ $e->question }}</td>
                                        <td>{{ \Illuminate\Support\Str::limit($e->answer, 70) }}</td>
                                        <td>
                                            <a href="{{ URL::to('admin/whatsapp/knowledge/status-' . $e->id . '/' . ($e->is_available == 1 ? 2 : 1)) }}"
                                                class="badge {{ $e->is_available == 1 ? 'bg-success' : 'bg-secondary' }} text-decoration-none">
                                                {{ $e->is_available == 1 ? trans('labels.active') : trans('labels.inactive') }}</a>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <button class="btn btn-sm btn-info btn-size" data-bs-toggle="modal"
                                                    data-bs-target="#kb{{ $e->id }}"><i class="fa fa-pen-to-square"></i></button>
                                                <a href="javascript:void(0)" class="btn btn-sm btn-danger btn-size"
                                                    onclick="statusupdate('{{ URL::to('admin/whatsapp/knowledge/delete-' . $e->id) }}')">
                                                    <i class="fa-regular fa-trash"></i></a>
                                            </div>

                                            <div class="modal fade" id="kb{{ $e->id }}" tabindex="-1">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <form class="modal-content" method="POST"
                                                        action="{{ URL::to('admin/whatsapp/knowledge/save-' . $e->id) }}">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h6 class="modal-title">{{ trans('labels.edit') }}</h6>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body text-start">
                                                            <label class="form-label">{{ trans('labels.question') }}</label>
                                                            <input type="text" class="form-control mb-3" name="question" value="{{ $e->question }}" required>
                                                            <label class="form-label">{{ trans('labels.answer') }}</label>
                                                            <textarea class="form-control mb-3" name="answer" rows="4" required>{{ $e->answer }}</textarea>
                                                            <label class="form-label">{{ trans('labels.display_order') }}</label>
                                                            <input type="number" min="1" class="form-control" name="reorder_id" value="{{ $e->reorder_id }}">
                                                            <input type="hidden" name="is_available" value="{{ $e->is_available }}">
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ trans('labels.cancel') }}</button>
                                                            <button class="btn btn-secondary rounded-start-5 rounded-end-5"
                                                                @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif>
                                                                {{ trans('labels.save') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-muted fs-7">{{ trans('labels.no_records') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
