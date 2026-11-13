{{-- Printable invoice for a booking or a service request (signed public link). Same look as front/invoice (orders). --}}
@php
    $ocName = \App\Services\StoreKnowledge::businessName($vdata);
    $ocBrand = $design['palette']['brand'];
    $ocCur = fn($amt) => trim(strip_tags(helper::currency_formate($amt, $vdata)));
    $ocRtl = session()->get('direction') == 2;
    [$ocCustName, $ocCustPhone, $ocCustEmail] = $doc['customer'];
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $ocRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ trans('labels.invoice') }} #{{ $doc['number'] }} — {{ $ocName }}</title>
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
                <strong>#{{ $doc['number'] }}</strong>
                @if ($doc['created'])<span>{{ date('d M Y, h:i A', strtotime($doc['created'])) }}</span>@endif
            </div>
        </div>

        <div class="inv-body">
            <div class="inv-grid">
                <div class="inv-box">
                    <h4>{{ trans('labels.wa_customer') }}</h4>
                    <p><strong>{{ $ocCustName }}</strong></p>
                    @if ($ocCustPhone)<p>{{ $ocCustPhone }}</p>@endif
                    @if ($ocCustEmail)<p>{{ $ocCustEmail }}</p>@endif
                    @if ($doc['address'])<p>{{ $doc['address'] }}</p>@endif
                </div>
                <div class="inv-box">
                    <h4>{{ trans('labels.wa_date') }}</h4>
                    @if ($doc['when'] !== '')<p><strong>{{ $doc['when'] }}</strong></p>@endif
                    @if ($doc['payment'])<p>{{ trans('labels.wa_payment') }}: {{ $doc['payment'] }}</p>@endif
                    @if ($doc['status'])<p>{{ trans('labels.status') }}: {{ $doc['status'] }}</p>@endif
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>{{ trans('labels.wa_service') }}</th>
                        <th class="num">{{ trans('labels.wa_total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $doc['service'] }}
                            @if ($doc['staff'])<small>{{ trans('labels.wa_staff') }}: {{ $doc['staff'] }}</small>@endif
                        </td>
                        <td class="num">{{ $doc['amount'] > 0 ? $ocCur($doc['amount']) : '—' }}</td>
                    </tr>
                </tbody>
            </table>

            @if ($doc['amount'] > 0)
                <div class="inv-totals">
                    <div class="grand"><span>{{ trans('labels.wa_total') }}</span><span>{{ $ocCur($doc['amount']) }}</span></div>
                </div>
            @endif
        </div>
        @if ($doc['notes'])
            <div class="inv-foot"><strong>{{ trans('labels.wa_notes') }}:</strong> {{ $doc['notes'] }}</div>
        @endif
    </div>
    <div class="inv-actions">
        <button class="inv-print" onclick="window.print()">{{ trans('labels.print_save_pdf') }}</button>
        <a class="inv-back" href="{{ URL::to($storeinfo->slug ?? '') }}">{{ $ocName }}</a>
    </div>
</body>
</html>
