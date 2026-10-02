@extends('admin.layout.default')
@php
if (Auth::user()->type == 4) {
$vendor_id = Auth::user()->vendor_id;
} else {
$vendor_id = Auth::user()->id;
}
@endphp
@section('content')
<div class="row justify-content-between align-items-center mb-3">
    <div class="col-12 col-md-4">
        <h5 class="pages-title color-changer fs-2">{{ trans('labels.users') }}</h5>
        @include('admin.layout.breadcrumb')
    </div>
    <div class="col-12 col-md-8">
        <div class="d-flex justify-content-end gap-2">
            @if (App\Models\SystemAddons::where('unique_identifier', 'vendor_import')->first() != null &&
            App\Models\SystemAddons::where('unique_identifier', 'vendor_import')->first()->activated == 1)
            <a href="{{ URL::to('admin/users/import') }}"
                class="btn btn-secondary px-4 rounded-start-5 rounded-end-5">
                <i class="fa-solid fa-file-import mx-1"></i>{{ trans('labels.import') }}</a>
            @endif

            @if ($getuserslist->count() > 0)
            <a href="{{ URL::to('admin/users/exportvendor') }}"
                class="btn btn-secondary px-4 rounded-start-5 rounded-end-5">
                <i class="fa-solid fa-file-export mx-1"></i>{{ trans('labels.export') }}</a>
            @endif
            <a href="{{ URL::to('admin/users/add') }}"
                class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_vendors', Auth::user()->role_id, $vendor_id, 'add') == 1 ? '' : 'd-none') : '' }}">
                <i class="fa-regular fa-plus mx-1"></i>
                {{ trans('labels.add') }}
            </a>
        </div>
    </div>
</div>
{{-- Vendor 360 · step 2: system tabs, global search, filters and admin queues.
     The card design below is unchanged — only the data inside it grew. --}}
@php
    $ocQ = request()->query();
    $ocTabUrl = function ($key) use ($ocQ) {
        $p = array_merge($ocQ, ['tab' => $key]);
        unset($p['queue']);
        return URL::to('admin/users') . '?' . http_build_query($p);
    };
    $ocQueueUrl = function ($key) use ($ocQ) {
        $p = array_merge($ocQ, ['queue' => $key]);
        if ((request('queue') ?? '') === $key) unset($p['queue']);
        return URL::to('admin/users') . '?' . http_build_query($p);
    };
@endphp

<div class="row">
    <div class="col-12 mb-3">
        <div class="d-flex flex-wrap gap-2">
            @php
                $ocTabs = ['all' => trans('labels.all_vendors')] + collect(\App\Helpers\Systems::all())
                    ->mapWithKeys(fn($s) => [$s['key'] => $s['name']])->all();
            @endphp
            @foreach ($ocTabs as $key => $label)
                <a href="{{ $ocTabUrl($key) }}"
                    class="btn btn-sm rounded-start-5 rounded-end-5 px-3 {{ $tab === $key ? 'btn-secondary' : 'btn-light' }}">
                    {{ $label }}
                    <span class="badge {{ $tab === $key ? 'bg-light text-dark' : 'bg-secondary' }} ms-1">{{ $tabCounts[$key] ?? 0 }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <div class="col-12 mb-3">
        <div class="card border-0 box-shadow">
            <div class="card-body">
                <form method="GET" action="{{ URL::to('admin/users') }}">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <div class="row g-2">
                        <div class="col-12 col-lg-4">
                            <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.search') }}</label>
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                                placeholder="{{ trans('labels.vendor_search_placeholder') }}">
                        </div>
                        <div class="col-6 col-md-4 col-lg-2">
                            <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.plan') }}</label>
                            <select name="plan" class="form-select">
                                <option value="">{{ trans('labels.all') }}</option>
                                @foreach ($filters['plans'] as $p)
                                    <option value="{{ $p->id }}" {{ request('plan') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-4 col-lg-2">
                            <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.account_status') }}</label>
                            <select name="account_status" class="form-select">
                                <option value="">{{ trans('labels.all') }}</option>
                                @foreach (\App\Helpers\Vendor360::ACCOUNT_STATUSES as $k => $m)
                                    <option value="{{ $k }}" {{ request('account_status') === $k ? 'selected' : '' }}>{{ $m['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-4 col-lg-2">
                            <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.verification') }}</label>
                            <select name="verification" class="form-select">
                                <option value="">{{ trans('labels.all') }}</option>
                                @foreach (\App\Helpers\Vendor360::VERIFICATION_STATUSES as $k => $m)
                                    <option value="{{ $k }}" {{ request('verification') === $k ? 'selected' : '' }}>{{ $m['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-4 col-lg-2">
                            <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.subscription') }}</label>
                            <select name="subscription" class="form-select">
                                <option value="">{{ trans('labels.all') }}</option>
                                @foreach (\App\Helpers\Vendor360::SUBSCRIPTION_STATUSES as $k => $m)
                                    <option value="{{ $k }}" {{ request('subscription') === $k ? 'selected' : '' }}>{{ $m['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-4 col-lg-2">
                            <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.page_status') }}</label>
                            <select name="page_status" class="form-select">
                                <option value="">{{ trans('labels.all') }}</option>
                                @foreach (\App\Helpers\Vendor360::PAGE_STATUSES as $k => $m)
                                    <option value="{{ $k }}" {{ request('page_status') === $k ? 'selected' : '' }}>{{ $m['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-4 col-lg-2">
                            <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.activity') }}</label>
                            <select name="activity" class="form-select">
                                <option value="">{{ trans('labels.all') }}</option>
                                @foreach ($filters['activities'] as $a)
                                    <option value="{{ $a->id }}" {{ request('activity') == $a->id ? 'selected' : '' }}>{{ $a->display_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-4 col-lg-2">
                            <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.country') }}</label>
                            <select name="country" class="form-select">
                                <option value="">{{ trans('labels.all') }}</option>
                                @foreach ($filters['countries'] as $c)
                                    <option value="{{ $c }}" {{ request('country') === $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-4 col-lg-2">
                            <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.legal_status') }}</label>
                            <select name="legal_status" class="form-select">
                                <option value="">{{ trans('labels.all') }}</option>
                                <option value="company" {{ request('legal_status') === 'company' ? 'selected' : '' }}>{{ trans('labels.company') }}</option>
                                <option value="freelancer" {{ request('legal_status') === 'freelancer' ? 'selected' : '' }}>{{ trans('labels.freelancer') }}</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-4 col-lg-2">
                            <label class="form-label fs-7 text-muted text-uppercase mb-1">{{ trans('labels.environment') }}</label>
                            <select name="env" class="form-select">
                                <option value="production" {{ $env === 'production' ? 'selected' : '' }}>{{ trans('labels.production') }}</option>
                                <option value="sandbox" {{ $env === 'sandbox' ? 'selected' : '' }}>{{ trans('labels.sandbox') }}</option>
                                <option value="all" {{ $env === 'all' ? 'selected' : '' }}>{{ trans('labels.all') }}</option>
                            </select>
                        </div>
                        <div class="col-12 col-lg-2 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-secondary px-4 rounded-start-5 rounded-end-5">
                                <i class="fa-solid fa-filter mx-1"></i>{{ trans('labels.filter') }}</button>
                            <a href="{{ URL::to('admin/users') }}" class="btn btn-light px-4 rounded-start-5 rounded-end-5">
                                <i class="fa-solid fa-rotate-left mx-1"></i>{{ trans('labels.reset') }}</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Admin queues — each chip filters to the vendors whose top alert matches. --}}
    <div class="col-12 mb-3">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <span class="fs-7 color-changer me-1">{{ trans('labels.quick_review') }}:</span>
            @foreach (\App\Helpers\Vendor360::queues() as $key => $label)
                @php $n = collect($alerts)->where('key', $key)->count(); @endphp
                <a href="{{ $ocQueueUrl($key) }}"
                    class="btn btn-sm rounded-start-5 rounded-end-5 {{ request('queue') === $key ? 'btn-secondary' : 'btn-light' }}">
                    {{ $label }} <span class="badge bg-warning ms-1">{{ $n }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <div class="col-12 mb-7">
        <!-- <div class="card border-0 box-shadow">
            <div class="card-body">
                <div class="table-responsive" id="table-display">
                    <table class="table table-striped table-bordered py-3 zero-configuration w-100">
                        <thead>
                            <tr class="fw-500 fs-15">
                                <td>{{ trans('labels.srno') }}</td>
                                <td>{{ trans('labels.image') }}</td>
                                <td>{{ trans('labels.name') }}</td>
                                <td>{{ trans('labels.email') }}</td>
                                <td>{{ trans('labels.mobile') }}</td>
                                <td>{{ trans('labels.status') }}</td>
                                <td>{{ trans('labels.created_date') }}</td>
                                <td>{{ trans('labels.updated_date') }}</td>
                                <td>{{ trans('labels.action') }}</td>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i = 1; @endphp
                            @foreach ($getuserslist as $user)
                            <tr class="fs-7 align-middle">
                                <td>@php echo $i++; @endphp</td>
                                <td> <img src="{{ helper::image_path($user->image) }}"
                                        class="img-fluid rounded hw-50" alt="" srcset=""> </td>
                                <td> {{ $user->name }} </td>
                                <td> {{ $user->email }} </td>
                                <td> {{ $user->mobile }} </td>
                                <td>
                                    @if ($user->is_available == 1)
                                    <a class="btn btn-sm btn-success btn-size {{ Auth::user()->type == 4 ? (helper::check_access('role_vendors', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}"
                                        tooltip="{{ trans('labels.active') }}"
                                        @if (env('Environment')=='sendbox' ) onclick="myFunction()" @else onclick="statusupdate('{{ URL::to('admin/users/status-' . $user->slug . '/2') }}')" @endif>
                                        <i class="fa-sharp fa-solid fa-check"></i>
                                    </a>
                                    @else
                                    <a class="btn btn-sm btn-danger btn-xmark {{ Auth::user()->type == 4 ? (helper::check_access('role_vendors', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}"
                                        tooltip="{{ trans('labels.in_active') }}"
                                        @if (env('Environment')=='sendbox' ) onclick="myFunction()" @else onclick="statusupdate('{{ URL::to('admin/users/status-' . $user->slug . '/1') }}')" @endif>
                                        <i class="fa-sharp fa-solid fa-xmark"></i>
                                    </a>
                                    @endif
                                </td>
                                <td>{{ helper::date_format($user->created_at, $vendor_id) }}<br>
                                    {{ helper::time_format($user->created_at, $vendor_id) }}

                                </td>
                                <td>{{ helper::date_format($user->updated_at, $vendor_id) }}<br>
                                    {{ helper::time_format($user->updated_at, $vendor_id) }}

                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a class="btn btn-sm btn-info btn-size {{ Auth::user()->type == 4 ? (helper::check_access('role_vendors', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}"
                                            tooltip="{{ trans('labels.edit') }}"
                                            href="{{ URL::to('admin/users/edit-' . $user->id) }}">
                                            <i class="fa fa-pen-to-square"></i>
                                        </a>
                                        <a class="btn btn-sm btn-dark btn-size"
                                            tooltip="{{ trans('labels.login') }}"
                                            href="{{ URL::to('admin/users/login-' . $user->id) }}">
                                            <i class="fa-regular fa-arrow-right-to-bracket"></i>
                                        </a>
                                        @php
                                        if (helper::checkcustomdomain($user->id) == null) {
                                        $url = URL::to($user->slug);
                                        } else {
                                        $url = 'https://' . helper::checkcustomdomain($user->id);
                                        }
                                        @endphp
                                        <a class="btn btn-sm btn-primary btn-size"
                                            tooltip="{{ trans('labels.view') }}" href="{{ $url }}"
                                            target="_blank">
                                            <i class="fa-regular fa-eye"></i>
                                        </a>

                                        <a href="javascript:void(0)" tooltip="{{ trans('labels.delete') }}"
                                            @if (env('Environment')=='sendbox' ) onclick="myFunction()" @else onclick="statusupdate('{{ URL::to('admin/users/delete-' . $user->id) }}')" @endif
                                            class="btn btn-sm btn-danger btn-size {{ Auth::user()->type == 4 ? (helper::check_access('role_vendors', Auth::user()->role_id, $vendor_id, 'delete') == 1 ? '' : 'd-none') : '' }}">
                                            <i class="fa-regular fa-trash"></i>
                                        </a>
                                        @if (App\Models\SystemAddons::where('unique_identifier', 'store_clone')->first() != null &&
                                        App\Models\SystemAddons::where('unique_identifier', 'store_clone')->first()->activated == 1)
                                        <a href="{{ URL::to('admin/users/add-' . $user->id) }}"
                                            tooltip="{{ trans('labels.clone') }}"
                                            class="btn btn-warning btn-size btn-sm {{ Auth::user()->type == 4 ? (helper::check_access('role_vendors', Auth::user()->role_id, $vendor_id, 'add') == 1 ? '' : 'd-none') : '' }}">
                                            <i class="fa-regular fa-clone"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div> -->
        <div class="row row-cols-xxl-5 row-cols-xl-4 row-cols-lg-2 row-cols-md-2 row-cols-sm-1 row-cols-1 g-3 py-3">
            @foreach ($getuserslist as $user)
            <div class="col">
                <div class="vendor_card card border bg-change rounded-4 h-100 box-shadow">
                    <div class="card-body p-3">
                        @php
                            $sum = $summaries[$user->id];
                            $alert = $alerts[$user->id];
                            $acct = \App\Helpers\Vendor360::accountStatus($user);
                            $verif = \App\Helpers\Vendor360::verificationStatus($user);
                            $subs = \App\Helpers\Vendor360::subscriptionStatus($user);
                            $page = \App\Helpers\Vendor360::pageStatus($user);
                            $activity = $user->activity_id ? \App\Models\Activity::find($user->activity_id) : null;
                            $spec = $user->specialization_id ? \App\Models\Specialization::find($user->specialization_id) : null;
                        @endphp
                        <div class="d-flex gap-3 mb-2 align-items-center border-bottom pb-2">
                            <div class="col-auto">
                                <img src="{{ helper::image_path($user->image) }}"
                                    class="img-fluid rounded hw-50" alt="" srcset="">
                            </div>
                            <div class="flex-grow-1">
                                <a href="{{ URL::to('admin/users/record-' . $user->id) }}"
                                    class="fs-6 fw-600 mt-1 color-changer text-decoration-none d-block">
                                    {{ $user->trade_name ?: $user->name }}
                                </a>
                                <p class="fs-7 mt-1 color-changer mb-0">
                                    <span class="text-muted">{{ $user->vendor_code }}</span> · {{ $user->mobile }}
                                </p>
                                <p class="fs-7 mt-1 color-changer mb-0">
                                    {{ $sum['system_label'] }}@if ($activity) · {{ $activity->display_name }}@endif
                                    @if ($spec) · {{ $spec->display_name }} @endif
                                </p>
                            </div>
                            @if ($user->is_sandbox == 1)
                                <span class="badge bg-secondary align-self-start">{{ trans('labels.sandbox') }}</span>
                            @endif
                        </div>

                        {{-- Four independent status axes. Green / amber / red only. --}}
                        <div class="d-flex flex-wrap gap-1 mb-2">
                            <span class="badge {{ $acct['class'] }}" tooltip="{{ trans('labels.account_status') }}">{{ $acct['text'] }}</span>
                            <span class="badge {{ $verif['class'] }}" tooltip="{{ trans('labels.verification') }}">{{ $verif['text'] }}</span>
                            <span class="badge {{ $subs['class'] }}" tooltip="{{ trans('labels.subscription') }}">{{ $subs['text'] }}</span>
                            <span class="badge {{ $page['class'] }}" tooltip="{{ trans('labels.page_status') }}">{{ $page['text'] }}</span>
                        </div>
                        <div class="row g-1 fs-7 color-changer">
                            <div class="col-6">{{ trans('labels.plan') }}: <span class="fw-500">{{ $sum['plan_name'] }}</span></div>
                            <div class="col-6">{{ trans('labels.expiry_date') }}:
                                <span class="fw-500">{{ $sum['expiry'] ? date('d M Y', strtotime($sum['expiry'])) : '—' }}</span>
                            </div>
                            <div class="col-6">{{ trans('labels.branches') }}: <span class="fw-500">{{ $sum['branches'] }}</span></div>
                            <div class="col-6">{{ trans('labels.whatsapp_numbers') }}: <span class="fw-500">{{ $sum['whatsapp'] }}</span></div>
                            <div class="col-6">{{ trans('labels.customers') }}: <span class="fw-500">{{ $sum['customers'] }}</span></div>
                            <div class="col-6">{{ trans('labels.' . $sum['volume_label']) }}: <span class="fw-500">{{ $sum['volume'] }}</span></div>
                            <div class="col-6">{{ trans('labels.visitors') }}: <span class="fw-500">{{ $sum['visitors'] }}</span></div>
                            <div class="col-6">{{ trans('labels.whatsapp_clicks') }}: <span class="fw-500">{{ $sum['whatsapp_clicks'] }}</span></div>
                        </div>

                        {{-- Usage against the plan's product/service limit. 80% amber, 100% red. --}}
                        <div class="mt-2">
                            <div class="d-flex justify-content-between fs-7 color-changer">
                                <span>{{ trans('labels.plan_usage') }}</span>
                                <span class="fw-500">
                                    {{ $sum['products'] }}@if ($sum['product_limit'] !== null && (int) $sum['product_limit'] !== -1)/{{ $sum['product_limit'] }}@else/∞ @endif
                                </span>
                            </div>
                            @if ($sum['usage_percent'] !== null)
                                <div class="progress mt-1" style="height:5px;">
                                    <div class="progress-bar {{ $sum['usage_percent'] >= 100 ? 'bg-danger' : ($sum['usage_percent'] >= 80 ? 'bg-warning' : 'bg-success') }}"
                                        style="width: {{ $sum['usage_percent'] }}%"></div>
                                </div>
                            @endif
                        </div>

                        @if ($sum['outstanding'] > 0)
                            <p class="fs-7 mt-2 mb-0 text-danger fw-500">
                                {{ trans('labels.outstanding') }}: {!! helper::currency_formate($sum['outstanding'], 1) !!}
                            </p>
                        @endif

                        <p class="fs-7 mt-2 mb-0 color-changer">{{ $user->email }}</p>

                        {{-- The single most important thing an admin should act on. --}}
                        <div class="mt-2">
                            <span class="badge {{ $alert['class'] }}">
                                <i class="fa-solid fa-bell mx-1"></i>{{ $alert['text'] }}
                            </span>
                        </div>

                        {{-- Marketplace listing, from the same checks the Marketplace page applies. --}}
                        @php $mpReason = \App\Http\Controllers\landing\HomeController::marketplaceBlocker($user); @endphp
                        <p class="fs-7 mt-2 mb-0 fw-500" style="color: {{ $mpReason ? '#b26a00' : '#1f9d55' }}">
                            <i class="fa-solid fa-store"></i> {{ trans('labels.marketplace') }}:
                            {{ $mpReason ? trans('labels.mp_hidden') . ' — ' . $mpReason : trans('labels.mp_listed') }}
                        </p>

                        <div class="d-flex flex-wrap justify-content-center mt-3 gap-2">
                            <a class="btn btn-sm btn-info btn-size {{ Auth::user()->type == 4 ? (helper::check_access('role_vendors', Auth::user()->role_id, $vendor_id, 'edit') == 1 ? '' : 'd-none') : '' }}"
                                tooltip="{{ trans('labels.edit') }}"
                                href="{{ URL::to('admin/users/edit-' . $user->id) }}">
                                <i class="fa fa-pen-to-square"></i>
                            </a>
                            <a class="btn btn-sm btn-dark btn-size"
                                tooltip="{{ trans('labels.login') }}"
                                href="{{ URL::to('admin/users/login-' . $user->id) }}">
                                <i class="fa-regular fa-arrow-right-to-bracket"></i>
                            </a>
                            @php
                            if (helper::checkcustomdomain($user->id) == null) {
                            $url = URL::to($user->slug);
                            } else {
                            $url = 'https://' . helper::checkcustomdomain($user->id);
                            }
                            @endphp
                            <a class="btn btn-sm btn-primary btn-size"
                                tooltip="{{ trans('labels.view') }}" href="{{ $url }}"
                                target="_blank">
                                <i class="fa-regular fa-eye"></i>
                            </a>

                            @if ($user->archived_at)
                                <a href="javascript:void(0)" tooltip="{{ trans('labels.restore') }}"
                                    @if (env('Environment')=='sendbox' ) onclick="myFunction()" @else onclick="statusupdate('{{ URL::to('admin/users/restore-' . $user->id) }}')" @endif
                                    class="btn btn-sm btn-success btn-size {{ Auth::user()->type == 4 ? (helper::check_access('role_vendors', Auth::user()->role_id, $vendor_id, 'delete') == 1 ? '' : 'd-none') : '' }}">
                                    <i class="fa-regular fa-rotate-left"></i>
                                </a>
                            @else
                                {{-- Archive, not delete. Permanent removal lives on the vendor record
                                     behind a second confirmation. --}}
                                <a href="javascript:void(0)" tooltip="{{ trans('labels.archive') }}"
                                    @if (env('Environment')=='sendbox' ) onclick="myFunction()" @else onclick="statusupdate('{{ URL::to('admin/users/archive-' . $user->id) }}')" @endif
                                    class="btn btn-sm btn-danger btn-size {{ Auth::user()->type == 4 ? (helper::check_access('role_vendors', Auth::user()->role_id, $vendor_id, 'delete') == 1 ? '' : 'd-none') : '' }}">
                                    <i class="fa-regular fa-box-archive"></i>
                                </a>
                            @endif
                            {{-- Permanent delete: every row of this vendor is removed. --}}
                            <a href="javascript:void(0)" tooltip="{{ trans('labels.delete_permanently') }}"
                                @if (env('Environment')=='sendbox' ) onclick="myFunction()" @else onclick="ocDeleteVendor('{{ URL::to('admin/users/delete-' . $user->id) }}', @js($user->trade_name ?: $user->name))" @endif
                                class="btn btn-sm btn-outline-danger btn-size {{ Auth::user()->type == 4 ? (helper::check_access('role_vendors', Auth::user()->role_id, $vendor_id, 'delete') == 1 ? '' : 'd-none') : '' }}">
                                <i class="fa-regular fa-trash"></i>
                            </a>
                            <a class="btn btn-sm btn-secondary btn-size" tooltip="{{ trans('labels.vendor_record') }}"
                                href="{{ URL::to('admin/users/record-' . $user->id) }}">
                                <i class="fa-regular fa-id-card"></i>
                            </a>
                            @if (App\Models\SystemAddons::where('unique_identifier', 'store_clone')->first() != null &&
                            App\Models\SystemAddons::where('unique_identifier', 'store_clone')->first()->activated == 1)
                            <a href="{{ URL::to('admin/users/add-' . $user->id) }}"
                                tooltip="{{ trans('labels.clone') }}"
                                class="btn btn-warning btn-size btn-sm {{ Auth::user()->type == 4 ? (helper::check_access('role_vendors', Auth::user()->role_id, $vendor_id, 'add') == 1 ? '' : 'd-none') : '' }}">
                                <i class="fa-regular fa-clone"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
    <form id="ocDeleteVendorForm" method="POST" class="d-none">@csrf</form>
@endsection
@section('scripts')
    <script>
        // Second, explicit confirmation: this cannot be undone.
        function ocDeleteVendor(url, name) {
            Swal.fire({
                title: @js(trans('labels.delete_vendor_title')),
                html: @js(trans('labels.delete_vendor_text')).replace(':name', '<b>' + $('<div>').text(name).html() + '</b>'),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: @js(trans('labels.delete_permanently')),
                cancelButtonText: no,
                confirmButtonColor: '#d64545',
                reverseButtons: true,
                focusCancel: true
            }).then(function (result) {
                if (result.isConfirmed) {
                    var form = document.getElementById('ocDeleteVendorForm');
                    form.action = url;
                    form.submit();
                }
            });
        }
    </script>
@endsection
