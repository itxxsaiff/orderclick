{{-- Business & Verification. Documents upload is Phase 2 of the registration spec; this tab
     already reviews whatever has been uploaded and drives the verification status axis. --}}
@php $ocAr = app()->getLocale() === 'ar'; @endphp
<div class="card border-0 box-shadow mb-3">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h6 class="mb-0">{{ trans('labels.verification') }}</h6>
            <form method="POST" action="{{ URL::to('admin/users/record-' . $vendor->id . '/status') }}" class="d-flex flex-wrap gap-2 align-items-center">
                @csrf
                <input type="hidden" name="field" value="verification_status">
                <select name="value" class="form-select form-select-sm" style="width:auto">
                    @foreach (\App\Helpers\Vendor360::VERIFICATION_STATUSES as $k => $m)
                        <option value="{{ $k }}" {{ $vendor->verification_status === $k ? 'selected' : '' }}>{{ $m['label'] }}</option>
                    @endforeach
                </select>
                <input type="text" name="reason" class="form-control form-control-sm" placeholder="{{ trans('labels.reason') }}" style="width:220px">
                <button class="btn btn-sm btn-secondary rounded-start-5 rounded-end-5"
                    @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif>
                    {{ trans('labels.change_status') }}</button>
                {{-- One-click decisions: the dropdown above still allows any state, but approving
                     or asking for a correction is what an admin does 99% of the time. --}}
                <button name="value" value="approved" class="btn btn-sm btn-success rounded-start-5 rounded-end-5"
                    @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif>
                    <i class="fa-solid fa-check mx-1"></i>{{ trans('labels.approve_verification') }}</button>
                <button name="value" value="changes_required" class="btn btn-sm btn-danger rounded-start-5 rounded-end-5"
                    @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif>
                    <i class="fa-solid fa-rotate-left mx-1"></i>{{ trans('labels.request_correction') }}</button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr class="fs-7 fw-500">
                        <td>{{ trans('labels.documents') }}</td>
                        <td>{{ trans('labels.status') }}</td>
                        <td>{{ trans('labels.created_date') }}</td>
                        <td>{{ trans('labels.expiry_date') }}</td>
                        <td>{{ trans('labels.actor') }}</td>
                        <td>{{ trans('labels.description') }}</td>
                        <td>{{ trans('labels.action') }}</td>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($documents as $doc)
                        <tr class="fs-7">
                            <td>{{ ucwords(str_replace('_', ' ', $doc->doc_type)) }}
                                @if ($doc->file)
                                    {{-- document_path(), not image_path(): these are PDFs/images in
                                         admin-assets/documents, which image_path() cannot resolve - it
                                         returned the placeholder picture instead of the file. --}}
                                    <a href="{{ helper::document_path($doc->file) }}" target="_blank" class="mx-1"
                                        tooltip="{{ trans('labels.view') }}"><i class="fa-regular fa-eye"></i></a>
                                    <a href="{{ helper::document_path($doc->file) }}" download class="mx-1"
                                        tooltip="{{ trans('labels.download') }}"><i class="fa-solid fa-download"></i></a>
                                    @if ($doc->original_name)
                                        <div class="text-muted">{{ \Illuminate\Support\Str::limit($doc->original_name, 28) }}</div>
                                    @endif
                                @endif
                            </td>
                            <td><span class="badge {{ $doc->status === 'approved' ? 'bg-success' : ($doc->status === 'expired' ? 'bg-danger' : 'bg-warning') }}">{{ ucwords(str_replace('_', ' ', $doc->status)) }}</span></td>
                            <td>{{ $doc->created_at ? $doc->created_at->format('d M Y') : '—' }}</td>
                            <td>{{ $doc->expiry_date ? date('d M Y', strtotime($doc->expiry_date)) : '—' }}</td>
                            <td>{{ $doc->reviewed_by ? optional(\App\Models\User::find($doc->reviewed_by))->name : '—' }}</td>
                            <td>{{ $doc->review_note ?: '—' }}</td>
                            <td>
                                <form method="POST" class="d-flex gap-1 align-items-center"
                                    action="{{ URL::to('admin/users/record-' . $vendor->id . '/document-' . $doc->id . '/review') }}">
                                    @csrf
                                    <input type="text" name="review_note" class="form-control form-control-sm"
                                        style="width:150px" placeholder="{{ trans('labels.reason_shown_to_the_vendor') }}"
                                        value="{{ $doc->review_note }}">
                                    <button name="decision" value="approve" class="btn btn-sm btn-success"
                                        tooltip="{{ trans('labels.approve') }}"
                                        @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif>
                                        <i class="fa-solid fa-check"></i></button>
                                    <button name="decision" value="reject" class="btn btn-sm btn-danger"
                                        tooltip="{{ trans('labels.reject') }}"
                                        @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif>
                                        <i class="fa-solid fa-xmark"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-muted fs-7">{{ trans('labels.no_records') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
