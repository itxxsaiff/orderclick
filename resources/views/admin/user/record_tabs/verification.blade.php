{{-- Business & Verification. Documents upload is Phase 2 of the registration spec; this tab
     already reviews whatever has been uploaded and drives the verification status axis. --}}
<div class="card border-0 box-shadow mb-3">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h6 class="mb-0">{{ trans('labels.verification') }}</h6>
            <form method="POST" action="{{ URL::to('admin/users/record-' . $vendor->id . '/status') }}" class="d-flex gap-2">
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
                    </tr>
                </thead>
                <tbody>
                    @forelse ($documents as $doc)
                        <tr class="fs-7">
                            <td>{{ ucwords(str_replace('_', ' ', $doc->doc_type)) }}
                                @if ($doc->file)
                                    <a href="{{ helper::image_path($doc->file) }}" target="_blank" class="mx-1"><i class="fa-regular fa-eye"></i></a>
                                @endif
                            </td>
                            <td><span class="badge {{ $doc->status === 'approved' ? 'bg-success' : ($doc->status === 'expired' ? 'bg-danger' : 'bg-warning') }}">{{ ucwords(str_replace('_', ' ', $doc->status)) }}</span></td>
                            <td>{{ $doc->created_at ? $doc->created_at->format('d M Y') : '—' }}</td>
                            <td>{{ $doc->expiry_date ? date('d M Y', strtotime($doc->expiry_date)) : '—' }}</td>
                            <td>{{ $doc->reviewed_by ? optional(\App\Models\User::find($doc->reviewed_by))->name : '—' }}</td>
                            <td>{{ $doc->review_note ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-muted fs-7">{{ trans('labels.no_records') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
