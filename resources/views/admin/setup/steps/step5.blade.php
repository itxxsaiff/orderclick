{{-- STEP 5 — Services & Availability. Links to the modules that already own this data rather
     than duplicating product/timing management inside the wizard. --}}
@php
    $itemCount = \App\Models\Item::where('vendor_id', $vendor->id)->count();
    $isBooking = $system === \App\Helpers\Systems::BOOKING;
    $isService = $system === \App\Helpers\Systems::SERVICE;
    $itemLabel = $isBooking ? trans('labels.services') : ($isService ? trans('labels.service_listings') : trans('labels.products'));
    $itemUrl = $isBooking ? 'admin/booking_services' : 'admin/products';
@endphp
<div class="card border-0 box-shadow">
    <div class="card-body">
        <div class="d-flex align-items-center gap-2 mb-4">
            <span class="step-badge">{{ trans('labels.step') }} 5</span>
            <h6 class="mb-0">{{ trans('labels.services_availability') }}</h6>
        </div>
        <p class="fs-7 text-muted">{{ trans('messages.services_step_hint') }}</p>

        <div class="row g-3">
            @php
                $tiles = [
                    ['icon' => 'fa-box', 'label' => $itemLabel, 'value' => $itemCount,
                     'done' => $itemCount > 0, 'url' => $itemUrl],
                    ['icon' => 'fa-whatsapp', 'style' => 'fa-brands', 'label' => trans('labels.whatsapp_number'),
                     'value' => optional($settings)->whatsapp_number ?: trans('labels.not_set'),
                     'done' => !empty(optional($settings)->whatsapp_number), 'url' => 'admin/settings'],
                    ['icon' => 'fa-clock', 'label' => trans('labels.working_hours'),
                     'value' => \App\Models\Timing::where('vendor_id', $vendor->id)->count() . ' ' . trans('labels.days'),
                     'done' => \App\Models\Timing::where('vendor_id', $vendor->id)->count() > 0, 'url' => 'admin/time'],
                    ['icon' => 'fa-location-dot', 'label' => trans('labels.branches'),
                     'value' => \App\Models\VendorBranch::where('vendor_id', $vendor->id)->count(),
                     'done' => true, 'url' => 'admin/branches'],
                ];
            @endphp
            @foreach ($tiles as $t)
                <div class="col-12 col-md-6">
                    <div class="oc-collapsed d-flex align-items-center justify-content-between gap-2 h-100">
                        <div class="d-flex align-items-center gap-3">
                            <i class="{{ $t['style'] ?? 'fa-solid' }} {{ $t['icon'] }} fs-5 {{ $t['done'] ? 'text-success' : 'text-muted' }}"></i>
                            <div>
                                <div class="fw-600 fs-7 color-changer">{{ $t['label'] }}</div>
                                <div class="fs-7 text-muted">{{ $t['value'] }}</div>
                            </div>
                        </div>
                        <a href="{{ URL::to($t['url']) }}" class="btn btn-sm btn-light rounded-start-5 rounded-end-5">
                            {{ $t['done'] ? trans('labels.edit') : trans('labels.add') }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
