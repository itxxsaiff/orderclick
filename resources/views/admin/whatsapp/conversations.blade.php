@extends('admin.layout.default')
@section('content')
    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-12">
            <h5 class="pages-title color-changer fs-2">{{ trans('labels.whatsapp_inbox') }}</h5>
            @include('admin.layout.breadcrumb')
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-4 mb-3">
            <div class="card border-0 box-shadow h-100">
                <div class="card-body">
                    <form method="GET" class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0"
                                placeholder="{{ trans('labels.search') }}">
                        </div>
                    </form>

                    @forelse ($conversations as $c)
                        <a href="{{ URL::to('admin/whatsapp/conversations') }}?id={{ $c->id }}"
                            class="d-flex align-items-center justify-content-between gap-2 p-2 rounded-3 text-decoration-none mb-1
                                   {{ $active && $active->id === $c->id ? 'bg-light' : '' }}">
                            <div>
                                <div class="fw-600 fs-7 color-changer">{{ $c->profile_name ?: $c->wa_id }}</div>
                                <div class="fs-7 text-muted">{{ $c->wa_id }}</div>
                            </div>
                            <div class="text-end">
                                <span class="badge {{ $c->isBotHandled() ? 'bg-success' : 'bg-warning' }}">
                                    {{ $c->isBotHandled() ? 'AI' : trans('labels.human') }}</span>
                                @if ($c->unread)<div><span class="badge bg-danger">{{ $c->unread }}</span></div>@endif
                            </div>
                        </a>
                    @empty
                        <p class="text-muted fs-7 mb-0">{{ trans('messages.wa_no_conversations') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8 mb-7">
            <div class="card border-0 box-shadow h-100">
                @if (!$active)
                    <div class="card-body text-muted fs-7">{{ trans('messages.wa_no_conversations') }}</div>
                @else
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom">
                            <div>
                                <div class="fw-600 color-changer">{{ $active->profile_name ?: $active->wa_id }}</div>
                                <div class="fs-7 text-muted">{{ $active->wa_id }}</div>
                            </div>
                            {{-- Human takeover: while a person owns the thread the AI stays silent. --}}
                            <a href="{{ URL::to('admin/whatsapp/conversations/takeover-' . $active->id) }}"
                                class="btn btn-sm {{ $active->isBotHandled() ? 'btn-warning' : 'btn-success' }} rounded-start-5 rounded-end-5">
                                {{ $active->isBotHandled() ? trans('labels.take_over') : trans('labels.hand_back_to_ai') }}
                            </a>
                        </div>

                        <div style="max-height:420px;overflow-y:auto;" id="ocWaThread">
                            @foreach ($messages as $m)
                                <div class="d-flex mb-2 {{ $m->isIncoming() ? '' : 'justify-content-end' }}">
                                    <div class="p-2 px-3 rounded-4 fs-7"
                                        style="max-width:78%;background:{{ $m->isIncoming() ? '#f1f3f0' : '#e6f4ec' }};">
                                        <div style="white-space:pre-wrap;">{{ $m->body }}</div>
                                        <div class="text-muted" style="font-size:11px;">
                                            {{ $m->created_at?->format('d M H:i') }}
                                            @if ($m->sent_by) · {{ strtoupper($m->sent_by) }} @endif
                                            @if ($m->status === 'failed') · <span class="text-danger">{{ $m->error }}</span> @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <form method="POST" action="{{ URL::to('admin/whatsapp/conversations/reply-' . $active->id) }}" class="mt-3">
                            @csrf
                            <div class="input-group">
                                <input type="text" name="body" class="form-control" placeholder="{{ trans('labels.message') }}">
                                <button class="btn btn-secondary px-4"
                                    @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif>
                                    <i class="fa-solid fa-paper-plane"></i></button>
                            </div>
                            <small class="text-muted">{{ trans('messages.wa_reply_takes_over') }}</small>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var t = document.getElementById('ocWaThread');
        if (t) t.scrollTop = t.scrollHeight;
    </script>
@endsection
