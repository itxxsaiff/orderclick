{{-- Printable order invoice (signed public link). Standalone page: prints cleanly and can be saved as PDF. --}}
@php
    $ocName = \App\Services\StoreKnowledge::businessName($vdata);
    $ocBrand = $design['palette']['brand'];
    $ocCur = fn($amt) => trim(strip_tags(helper::currency_formate($amt, $vdata)));
    $ocTypes = [1 => trans('labels.wa_type_delivery'), 2 => trans('labels.wa_type_pickup'), 3 => trans('labels.wa_type_dinein'), 4 => 'POS'];
    $ocRtl = session()->get('direction') == 2;
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $ocRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ trans('labels.invoice') }} #{{ $order->order_number }} — {{ $ocName }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --brand: {{ $ocBrand }}; }
        * { box-sizing: border-box; }
        body { margin: 0; background: #f3f4f1; font-family: Inter, Cairo, -apple-system, "Segoe UI", sans-serif; color: #1d2421; font-size: 14px; }
        .inv { max-width: 760px; margin: 28px auto; background: #fff; border-radius: 16px; box-shadow: 0 18px 50px -30px rgba(0,0,0,.35); overflow: hidden; }
        .inv-head { background: var(--brand); color: #fff; padding: 26px 30px; display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; }
        .inv-brand { display: flex; align-items: center; gap: 12px; }
        .inv-brand img { width: 52px; height: 52px; border-radius: 12px; object-fit: cover; background: #fff; }
        .inv-brand h1 { margin: 0; font-size: 20px; }
        .inv-brand p { margin: 2px 0 0; opacity: .85; font-size: 12.5px; }
        .inv-no { text-align: end; }
        .inv-no strong { display: block; font-size: 18px; letter-spacing: .02em; }
        .inv-no span { opacity: .85; font-size: 12.5px; }
        .inv-body { padding: 26px 30px; }
        .inv-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 22px; }
        .inv-box h4 { margin: 0 0 6px; font-size: 11.5px; text-transform: uppercase; letter-spacing: .08em; color: #7a857e; }
        .inv-box p { margin: 2px 0; line-height: 1.5; }
        .inv-box a { color: var(--brand); font-weight: 600; text-decoration: none; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: start; font-size: 11.5px; text-transform: uppercase; letter-spacing: .06em; color: #7a857e; border-bottom: 2px solid #eef0ed; padding: 10px 6px; }
        td { padding: 11px 6px; border-bottom: 1px solid #f0f2ef; vertical-align: top; }
        td small { color: #7a857e; display: block; margin-top: 2px; }
        .num { text-align: end; white-space: nowrap; }
        .inv-totals { margin-top: 16px; margin-inline-start: auto; width: min(100%, 320px); }
        .inv-totals div { display: flex; justify-content: space-between; padding: 5px 0; }
        .inv-totals .grand { border-top: 2px solid #1d2421; margin-top: 6px; padding-top: 10px; font-size: 17px; font-weight: 800; }
        .inv-foot { padding: 0 30px 26px; color: #7a857e; font-size: 12.5px; }
        .inv-actions { max-width: 760px; margin: 0 auto 30px; display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
        .inv-actions a, .inv-actions button { border: 0; border-radius: 999px; padding: 11px 20px; font: inherit; font-weight: 700; cursor: pointer; text-decoration: none; }
        .inv-print { background: var(--brand); color: #fff; }
        .inv-back { background: #fff; color: #1d2421; border: 1px solid #dfe3dd !important; }
        @media (max-width: 600px) { .inv-grid { grid-template-columns: 1fr; } .inv-head, .inv-body { padding: 20px; } }
        @media print { body { background: #fff; } .inv { box-shadow: none; margin: 0; max-width: none; border-radius: 0; } .inv-actions { display: none; }
            .inv-head { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }
    </style>
</head>
<body>
    <div class="inv">
        <div class="inv-head">
            <div class="inv-brand">
                @if (!empty($settings->logo))<img src="{{ helper::image_path($settings->logo) }}" alt="">@endif
                <div>
                    <h1>{{ $ocName }}</h1>
                    <p>{{ trim(implode(' · ', array_filter([$settings->contact ?? null, $settings->email ?? null]))) }}</p>
                </div>
            </div>
            <div class="inv-no">
                <span>{{ trans('labels.invoice') }}</span>
                <strong>#{{ $order->order_number }}</strong>
                <span>{{ date('d M Y, h:i A', strtotime($order->created_at)) }}</span>
            </div>
        </div>

        <div class="inv-body">
            <div class="inv-grid">
                <div class="inv-box">
                    <h4>{{ trans('labels.wa_customer') }}</h4>
                    <p><strong>{{ $order->customer_name }}</strong></p>
                    @if ($order->mobile)<p>{{ $order->mobile }}</p>@endif
                    @if ($order->customer_email)<p>{{ $order->customer_email }}</p>@endif
                </div>
                <div class="inv-box">
                    <h4>{{ $ocTypes[$order->order_type] ?? trans('labels.wa_type') }}</h4>
                    @if ($order->order_type == 1)
                        @php $ocAddr = trim(implode(', ', array_filter([$order->address, $order->building, $order->landmark, $order->delivery_area]))); @endphp
                        @if ($ocAddr !== '')<p>{{ $ocAddr }}</p>@endif
                        @if (!empty($order->latitude) && !empty($order->longitude))
                            <p><a href="https://www.google.com/maps?q={{ $order->latitude }},{{ $order->longitude }}" target="_blank" rel="noopener">{{ trans('labels.open_in_maps') }}</a></p>
                        @endif
                    @endif
                    @if ($order->delivery_date)<p>{{ trim($order->delivery_date . ' ' . $order->delivery_time) }}</p>@endif
                    @if ($payment)<p>{{ trans('labels.wa_payment') }}: {{ $payment }}</p>@endif
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>{{ trans('labels.wa_items') }}</th>
                        <th class="num">{{ trans('labels.qty') }}</th>
                        <th class="num">{{ trans('labels.wa_total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($details as $d)
                        <tr>
                            <td>{{ $d->item_name }}
                                @php $ocEx = array_filter([$d->variants_name, $d->extras_name]); @endphp
                                @if ($ocEx)<small>{{ implode(', ', $ocEx) }}</small>@endif
                            </td>
                            <td class="num">{{ $d->qty }}</td>
                            <td class="num">{{ $ocCur($d->price) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="inv-totals">
                <div><span>{{ trans('labels.wa_subtotal') }}</span><span>{{ $ocCur($order->sub_total) }}</span></div>
                @if ((float) $order->delivery_charge > 0)<div><span>{{ trans('labels.wa_delivery_charge') }}</span><span>{{ $ocCur($order->delivery_charge) }}</span></div>@endif
                @if ((float) $order->discount_amount > 0)<div><span>{{ trans('labels.wa_discount') }}</span><span>-{{ $ocCur($order->discount_amount) }}</span></div>@endif
                @if ((float) $order->tax > 0)<div><span>{{ $order->tax_name ?: trans('labels.wa_tax') }}</span><span>{{ $ocCur($order->tax) }}</span></div>@endif
                <div class="grand"><span>{{ trans('labels.wa_total') }}</span><span>{{ $ocCur($order->grand_total) }}</span></div>
            </div>
        </div>
        @if ($order->order_notes)
            <div class="inv-foot"><strong>{{ trans('labels.wa_notes') }}:</strong> {{ $order->order_notes }}</div>
        @endif
    </div>
    <div class="inv-actions">
        <button class="inv-print" onclick="window.print()">{{ trans('labels.print_save_pdf') }}</button>
        <a class="inv-back" href="{{ URL::to($storeinfo->slug ?? '') }}">{{ $ocName }}</a>
    </div>
</body>
</html>
