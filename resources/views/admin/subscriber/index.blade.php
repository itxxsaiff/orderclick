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
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.subscribers') }}</h5>
            @include('admin.layout.breadcrumb')
        </div>
    </div>
    <div class="col-12 mb-7 mt-3">
        <div class="card border-0 box-shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered py-3 zero-configuration w-100">
                        <thead>
                            <tr class="fw-500 fs-15">
                                <td>{{ trans('labels.srno') }}</td>
                                <td>{{ trans('labels.email') }}</td>
                                <td>{{ trans('labels.status') }}</td>
                                <td>{{ trans('labels.created_date') }}</td>
                                <td>{{ trans('labels.updated_date') }}</td>
                                <td>{{ trans('labels.action') }}</td>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i=1; @endphp
                            @foreach ($getsubscribers as $subscriber)
                                <tr class="fs-7 align-middle">
                                    <td>@php echo $i++ @endphp</td>
                                    <td>{{ $subscriber->email }}</td>
                                    <td>
                                        <span class="badge {{ $subscriber->statusClass() }}">{{ $subscriber->statusLabel() }}</span>
                                        @if (!$subscriber->isSubscribed() && $subscriber->unsubscribed_at)
                                            <div class="fs-7 text-muted">{{ date('d M Y', strtotime($subscriber->unsubscribed_at)) }}</div>
                                        @endif
                                    </td>
                                    <td>{{ helper::date_format($subscriber->created_at, $vendor_id) }}<br>
                                        {{ helper::time_format($subscriber->created_at, $vendor_id) }}

                                    </td>
                                    <td>{{ helper::date_format($subscriber->updated_at, $vendor_id) }}<br>
                                        {{ helper::time_format($subscriber->updated_at, $vendor_id) }}

                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            {{-- Copy the subscriber's one-click unsubscribe URL — the same
                                                 link that goes in a marketing email footer. Useful for
                                                 support and for testing without digging in the database. --}}
                                            <a href="javascript:void(0)" tooltip="{{ trans('labels.copy_unsubscribe_link') }}"
                                                onclick="ocCopyUnsub(this, '{{ $subscriber->unsubscribeUrl() }}')"
                                                class="btn btn-light btn-sm btn-size">
                                                <i class="fa-regular fa-link"></i>
                                            </a>
                                            {{-- Unsubscribing keeps the address on record so no future
                                                 marketing email can be sent to it. --}}
                                            @if ($subscriber->isSubscribed())
                                                <a @if (env('Environment') == 'sendbox') onclick="myFunction()" @else onclick="statusupdate('{{ URL::to('admin/subscribers/status-' . $subscriber->id . '/unsubscribed') }}')" @endif
                                                    tooltip="{{ trans('labels.unsubscribe') }}"
                                                    class="btn btn-warning btn-sm btn-size {{ Auth::user()->type == 4 ? (helper::check_access('role_subscribers', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}">
                                                    <i class="fa-regular fa-bell-slash"></i>
                                                </a>
                                            @else
                                                <a @if (env('Environment') == 'sendbox') onclick="myFunction()" @else onclick="statusupdate('{{ URL::to('admin/subscribers/status-' . $subscriber->id . '/subscribed') }}')" @endif
                                                    tooltip="{{ trans('labels.reactivate') }}"
                                                    class="btn btn-success btn-sm btn-size {{ Auth::user()->type == 4 ? (helper::check_access('role_subscribers', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}">
                                                    <i class="fa-regular fa-bell"></i>
                                                </a>
                                            @endif
                                            <a @if (env('Environment') == 'sendbox') onclick="myFunction()" @else onclick="deletedata('{{ URL::to('admin/subscribers/delete-' . $subscriber->id) }}')" @endif
                                                class="btn btn-danger btn-sm  btn-size {{ Auth::user()->type == 4 ? (helper::check_access('role_subscribers', Auth::user()->role_id, $vendor_id, 'delete') == 1 ? '' : 'd-none') : '' }}">
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
@endsection

@section('scripts')
    <script>
        function ocCopyUnsub(el, url) {
            var done = function () {
                var icon = el.querySelector('i');
                var old = icon.className;
                icon.className = 'fa-solid fa-check text-success';
                setTimeout(function () { icon.className = old; }, 1500);
            };
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(url).then(done).catch(function () { window.prompt('Unsubscribe link', url); });
            } else {
                // http:// (no secure context) — fall back to a selectable prompt.
                window.prompt('Unsubscribe link', url);
            }
        }
    </script>
@endsection
