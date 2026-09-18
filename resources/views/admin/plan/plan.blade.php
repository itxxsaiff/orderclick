@extends('admin.layout.default')
@section('content')
    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-12 col-md-4">
            {{-- V2: a merchant only sees the plans of the system they signed up for, and the
                 heading tells them whether this is their first purchase or an upgrade. --}}
            <h5 class="pages-title color-changer fs-2">
                @if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1))
                    {{ !empty($isUpgrade) ? trans('labels.upgrade_plan') : trans('labels.pricing_plans') }}
                @else
                    {{ trans('labels.pricing_plans') }}
                @endif
            </h5>
            <div class="d-flex">
                @include('admin.layout.breadcrumb')
            </div>
        </div>
        @if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1))
            <div class="col-12 col-md-8">
                <div class="d-flex justify-content-end">
                    <a href="{{ URL::to('admin/plan/add') }}"
                        class="btn btn-secondary px-4 rounded-start-5 rounded-end-5 {{ Auth::user()->type == 4 ? (helper::check_access('role_pricing_plans', Auth::user()->role_id, Auth::user()->vendor_id, 'add') == 1 ? '' : 'd-none') : '' }}">
                        <i class="fa-regular fa-plus mx-1"></i>{{ trans('labels.add') }}
                    </a>
                </div>
            </div>
        @endif
    </div>

    {{-- Manual methods (Benefit, bank transfer, cash) are approved by an admin, so the merchant
         is told their receipt arrived and is still under review. Stripe never lands here: it
         confirms itself and the transaction is already paid. --}}
    @if (!empty($pendingPayment))
        @php
            $ocAr = app()->getLocale() === 'ar';
            $ocSupport = helper::appdata(1)->contact ?? '';
            $ocWa = preg_replace('/[^0-9]/', '', (string) $ocSupport);
        @endphp
        <div class="col-12 mb-3">
            <div class="alert alert-warning border-0 box-shadow d-flex flex-wrap align-items-center justify-content-between gap-3 mb-0">
                <div class="d-flex align-items-start gap-3">
                    <i class="fa-solid fa-clock fs-4 mt-1"></i>
                    <div>
                        <div class="fw-semibold">
                            {{ trans('labels.payment_pending_waiting_for_admin_approval') }}
                        </div>
                        <div class="fs-7">
                            {{ trans('labels.your_payment_has_been_submitted_and_is') }}
                        </div>
                        <div class="fs-7 text-muted mt-1">
                            {{ $pendingPayment->plan_name }}
                            · {{ \App\Helpers\Subscriptions::methodName($pendingPayment->payment_type) }}
                            · {{ helper::currency_formate($pendingPayment->grand_total ?: $pendingPayment->amount, '') }}
                            @if ($pendingPayment->transaction_number)
                                · {{ $pendingPayment->transaction_number }}
                            @endif
                        </div>
                    </div>
                </div>
                @if ($ocWa)
                    <a href="https://wa.me/{{ $ocWa }}" target="_blank"
                        class="btn btn-sm btn-secondary rounded-start-5 rounded-end-5">
                        <i class="fa-brands fa-whatsapp mx-1"></i>{{ trans('labels.contact_support_2') }}</a>
                @endif
            </div>
        </div>
    @endif

    @if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1))
        @php $ocSys = \App\Helpers\Systems::all()[$planSystem ?? 'orders']; @endphp
        <div class="col-12 mb-3">
            <div class="card border-0 box-shadow">
                <div class="card-body py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <span class="text-muted small d-block">{{ trans('labels.selected_system') }}</span>
                        <span class="fw-semibold">{{ $ocSys['icon'] }}
                            {{ app()->getLocale() === 'ar' ? $ocSys['name_ar'] : $ocSys['name'] }}</span>
                    </div>
                    @if (!empty($currentPlan))
                        <div>
                            <span class="text-muted small d-block">{{ trans('labels.current_plan') }}</span>
                            <span class="fw-semibold">{{ $currentPlan }}</span>
                            @if (!empty($pendingPayment))
                                <span class="badge bg-warning ms-1">
                                    {{ trans('labels.pending') }}</span>
                            @endif
                        </div>
                    @endif
                    <a href="{{ URL::to('admin/setup') }}"
                        class="btn btn-light rounded-start-5 rounded-end-5">{{ trans('labels.account_setup') }}</a>
                </div>
            </div>
        </div>
    @endif

    @if (Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1))
        <div class="col-12 mb-3">
            <div class="oc-plan-filter d-inline-flex gap-2 p-1 rounded-3" style="background:#eef2f0">
                <button type="button" class="btn btn-sm oc-planfilter-btn active" data-filter="all">{{ trans('labels.all') }}</button>
                <button type="button" class="btn btn-sm oc-planfilter-btn" data-filter="orders">Orders &amp; Stores</button>
                <button type="button" class="btn btn-sm oc-planfilter-btn" data-filter="booking">Booking</button>
                <button type="button" class="btn btn-sm oc-planfilter-btn" data-filter="service">Service Marketplace</button>
            </div>
        </div>
        <style>
            .oc-planfilter-btn { border: 0; background: transparent; color: #566; font-weight: 600; }
            .oc-planfilter-btn.active { background: #fff; color: #1f9d55; box-shadow: 0 4px 12px -6px rgba(20,40,30,.35); }
        </style>
    @endif
    <div class="col-12 mb-7">
        <div class="g-3 row {{ Auth::user()->type == 1 || (Auth::user()->type == 4 && Auth::user()->vendor_id == 1) ? 'sort_menu' : '' }}"
            id="carddetails" data-url="{{ url('admin/plan/reorder_plan') }}">
            @if (count($allplan) > 0)
                @foreach ($allplan as $plandata)
                    @php
                        if (Auth::user()->type == 4 && Auth::user()->vendor_id != 1) {
                            $vendor_id = Auth::user()->vendor_id;
                            $plan = helper::getplantransaction(Auth::user()->vendor_id);
                            $plan_id = $plan->plan_id;
                            $purchase_amount = $plan->amount;
                        } else {
                            $vendor_id = Auth::user()->id;
                            $plan_id = Auth::user()->plan_id;
                            $purchase_amount = Auth::user()->purchase_amount;
                        }
                        $check_vendorplan = helper::checkplan($vendor_id, '');
                        $data = json_decode(json_encode($check_vendorplan), true);
                    @endphp
                    @if (Auth::user()->type == 2 || (Auth::user()->type == 4 && Auth::user()->vendor_id != 1))
                        @if ($plandata->vendor_id != '' && $plandata->vendor_id != null)
                            @if (in_array($vendor_id, explode('|', $plandata->vendor_id)))
                                @include('admin.plan.plancommon')
                            @endif
                        @else
                            @include('admin.plan.plancommon')
                        @endif
                    @else
                        @include('admin.plan.plancommon')
                    @endif
                @endforeach
            @else
                @include('admin.layout.no_data')
            @endif
        </div>
    </div>

@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $('.sort_menu').sortable({
                handle: '.handle',
                cursor: 'move',
                placeholder: 'highlight',
                axis: "x,y",

                update: function(e, ui) {
                    var sortData = $('.sort_menu').sortable('toArray', {
                        attribute: 'data-id'
                    })
                    updateToDatabase(sortData.join('|'))
                }
            })

            function updateToDatabase(idString) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    dataType: "json",
                    url: $('#carddetails').attr('data-url'),
                    data: {
                        ids: idString,
                    },
                    success: function(response) {
                        if (response.status == 1) {
                            toastr.success(response.msg);
                        } else {
                            toastr.success(wrong);
                        }
                    }
                });
            }

        })
    </script>
    <script>
        function themeinfo(id, theme_id, plan_name) {

            let string = theme_id;
            let arr = string.split(',');
            $('#themeinfoLabel').text(plan_name);
            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                url: "{{ URL::to('admin/themeimages') }}",
                method: 'GET',
                data: {
                    theme_id: arr
                },
                dataType: 'json',
                success: function(data) {
                    $('#theme_modalbody').html(data.output);
                    $('#themeinfo').modal('show');
                }
            })
        }
    </script>
    <script>
        // Filter the plan cards by system (Orders & Stores / Booking / Service Marketplace).
        (function () {
            var btns = document.querySelectorAll('.oc-planfilter-btn');
            if (!btns.length) return;
            btns.forEach(function (b) {
                b.addEventListener('click', function () {
                    btns.forEach(function (x) { x.classList.remove('active'); });
                    b.classList.add('active');
                    var f = b.dataset.filter;
                    document.querySelectorAll('.oc-plan-card').forEach(function (card) {
                        card.style.display = (f === 'all' || card.dataset.system === f) ? '' : 'none';
                    });
                });
            });
        })();
    </script>
@endsection
