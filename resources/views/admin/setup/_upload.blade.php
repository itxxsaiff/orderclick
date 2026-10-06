{{-- One document slot. Re-uploading keeps the previous version rather than replacing it, so the
     admin's correction history stays intact. --}}
@php
    $existing = ($uploaded[$type] ?? collect());
    $latest = $existing->first();
    $meta = $documents[$type] ?? ['label' => ucwords(str_replace('_', ' ', $type)), 'hint' => '', 'required' => false];
    $multiple = !empty($meta['multiple']);
@endphp
<div class="oc-upload">
    <div class="fw-600 fs-7 color-changer">
        {{ $meta['label'] }}@if ($meta['required'])<span class="text-danger"> *</span>@endif
    </div>
    @if (!empty($meta['hint']))
        <div class="fs-7 text-muted mb-2">{{ $meta['hint'] }}</div>
    @endif

    <label class="btn-up d-inline-flex align-items-center justify-content-center gap-2 mt-1" style="cursor:pointer;">
        <i class="fa-solid fa-cloud-arrow-up"></i>{{ trans('labels.upload_document') }}
        <input type="file" name="{{ $type }}{{ $multiple ? '[]' : '' }}" {{ $multiple ? 'multiple' : '' }}
            accept=".pdf,.jpg,.jpeg,.png" data-name-target="ocName{{ $type }}">
    </label>
    <div class="fs-7 text-muted mt-1">PDF, JPG, PNG ({{ trans('labels.max') }} 5MB)</div>
    <div class="oc-filename mt-1" id="ocName{{ $type }}"></div>

    @foreach ($existing as $file)
        <div class="d-flex align-items-center justify-content-between gap-2 mt-2 pt-2 border-top">
            <a href="{{ asset('storage/app/public/admin-assets/documents/' . $file->file) }}" target="_blank" class="fs-7 text-truncate">
                <i class="fa-regular fa-file"></i>
                {{ \Illuminate\Support\Str::limit($file->original_name ?: $file->file, 26) }}
                <span class="text-muted">v{{ $file->version }}</span>
            </a>
            <div class="d-flex align-items-center gap-1">
                <span class="badge {{ $file->status === 'approved' ? 'bg-success' : ($file->status === 'changes_required' ? 'bg-warning' : 'bg-secondary') }}">
                    {{ \Illuminate\Support\Facades\Lang::has('labels.doc_status_' . $file->status) ? trans('labels.doc_status_' . $file->status) : ucwords(str_replace('_', ' ', $file->status)) }}
                </span>
                <a href="{{ URL::to('admin/setup/document/delete-' . $file->id) }}" class="text-danger"
                    tooltip="{{ trans('labels.delete') }}"><i class="fa-regular fa-trash"></i></a>
            </div>
        </div>
        @if ($file->review_note)
            <div class="fs-7 text-danger mt-1">{{ $file->review_note }}</div>
        @endif
    @endforeach
</div>
