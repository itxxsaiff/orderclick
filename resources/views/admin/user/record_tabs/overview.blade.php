{{-- Overview: the live totals for this vendor plus the system-specific section. --}}
<div class="row g-3">
    @php
        $tiles = [
            trans('labels.branches')         => $summary['branches'],
            trans('labels.whatsapp_numbers') => $summary['whatsapp'],
            trans('labels.customers')        => $summary['customers'],
            trans('labels.' . $summary['volume_label']) => $summary['volume'],
            trans('labels.visitors')         => $summary['visitors'],
            trans('labels.whatsapp_clicks')  => $summary['whatsapp_clicks'],
        ];
    @endphp
    @foreach ($tiles as $lbl => $val)
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 box-shadow h-100">
                <div class="card-body py-3">
                    <div class="text-muted fs-7">{{ $lbl }}</div>
                    <div class="fs-4 fw-600 color-changer">{{ $val }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-3 mt-1">
    <div class="col-12 col-lg-7">
        <div class="card border-0 box-shadow h-100">
            <div class="card-body">
                <h6 class="mb-3">{{ trans('labels.business_information') }}</h6>
                <div class="row g-2 fs-7 color-changer">
                    @php
                        $rows = [
                            trans('labels.name')            => $vendor->trade_name ?: $vendor->name,
                            trans('labels.email')           => $vendor->email,
                            trans('labels.mobile')          => $vendor->mobile,
                            'WhatsApp'                      => optional($settings)->whatsapp_number ?: '—',
                            trans('labels.legal_status')    => $vendor->legal_status ? trans('labels.' . $vendor->legal_status) : '—',
                            'CR / Licence'                  => $vendor->cr_number ?: '—',
                            trans('labels.country')         => $vendor->country ?: '—',
                            trans('labels.city')            => $vendor->city_name ?: '—',
                            trans('labels.personlized_link') => URL::to('/' . $vendor->slug),
                        ];
                    @endphp
                    @foreach ($rows as $lbl => $val)
                        <div class="col-12 col-md-6">
                            <span class="text-muted">{{ $lbl }}:</span> <span class="fw-500">{{ $val }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-5">
        <div class="card border-0 box-shadow h-100">
            <div class="card-body">
                {{-- The same layout for all three systems; only the fields differ. --}}
                <h6 class="mb-3">{{ $summary['system_label'] }}</h6>
                @php
                    $sys = $summary['system'];
                    $fields = [
                        'orders'  => ['Catalogue / products / menu', 'Delivery · Pickup · Dine-in', 'Prep time · coverage · order prefix', 'Inventory & fulfilment'],
                        'booking' => ['Services · providers / doctors', 'Schedules · rooms / resources', 'Confirmation & cancellation policies', 'Availability & no-show settings'],
                        'service' => ['Freelancer / company / driver', 'Skills · portfolio · service areas', 'Requests · quotes · assignment', 'Team members & ratings'],
                    ][$sys];
                @endphp
                <ul class="fs-7 color-changer mb-3">
                    @foreach ($fields as $f)
                        <li>{{ $f }}</li>
                    @endforeach
                </ul>
                <span class="badge bg-secondary">{{ trans('labels.locked_after_payment') }}</span>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card border-0 box-shadow">
            <div class="card-body">
                <h6 class="mb-3">{{ trans('labels.admin_notes') }}</h6>
                <form method="POST" action="{{ URL::to('admin/users/record-' . $vendor->id . '/note') }}">
                    @csrf
                    <textarea name="admin_notes" rows="3" class="form-control"
                        placeholder="{{ trans('labels.admin_notes') }}">{{ $vendor->admin_notes }}</textarea>
                    <div class="d-flex justify-content-end mt-2">
                        <button class="btn btn-secondary px-4 rounded-start-5 rounded-end-5"
                            @if (env('Environment') == 'sendbox') type="button" onclick="myFunction()" @else type="submit" @endif>
                            {{ trans('labels.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
