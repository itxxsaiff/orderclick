<html lang="{{ session()->get('locale', app()->getLocale()) }}" dir="{{ session()->get('direction') == 2 ? 'rtl' : 'ltr' }}" class="light">

<head>
    <meta charset="UTF-8">
    @include('partials.google_tag')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ helper::appdata($vdata)->website_title }}</title>
    <link rel="icon" href="{{ helper::image_path(helper::appdata(@$vdata)->favicon) }}" type="image" sizes="16x16">
    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'web-assets/font-awesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'web-assets/css/bootstrap.min.css') }}">
    <style>
        :root {
            --bs-primary: #1f9d55;
            @if (helper::appdata($vdata)->primary_color != null) --bs-primary: {{ helper::appdata($vdata)->primary_color }}; @endif
        }
        body { background: #f6f8f5; min-height: 100vh; margin: 0; font-family: 'Outfit', system-ui, -apple-system, sans-serif; }
        .ocs-wrap { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px 16px; box-sizing: border-box; }
        .ocs-card { background: #fff; border: 1px solid #e7ece4; border-radius: 24px; max-width: 520px; width: 100%; padding: 50px 40px; text-align: center; box-sizing: border-box; box-shadow: 0 34px 80px -46px rgba(20,40,28,.4); }
        .ocs-check { width: 96px; height: 96px; border-radius: 50%; background: rgba(31,157,85,.10); display: flex; align-items: center; justify-content: center; margin: 0 auto 26px; animation: ocsPop .5s cubic-bezier(.2,.7,.3,1.4); }
        .ocs-check i { font-size: 44px; color: var(--bs-primary); }
        @keyframes ocsPop { 0% { transform: scale(.3); opacity: 0; } 60% { transform: scale(1.1); } 100% { transform: scale(1); opacity: 1; } }
        .ocs-card h1 { font-size: 26px; font-weight: 800; color: #17201a; margin: 0 0 12px; }
        .ocs-num { color: #67736a; font-size: 15.5px; margin: 0 0 18px; }
        .ocs-num b { color: var(--bs-primary); }
        .ocs-detail { text-align: left; background: #f7faf7; border: 1px solid #e7ece4; border-radius: 12px; padding: 16px 18px; margin: 0 0 24px; }
        .ocs-detail div { display: flex; justify-content: space-between; padding: 5px 0; font-size: 14.5px; color: #39443c; }
        .ocs-detail div span:first-child { color: #8a978d; }
        .ocs-detail div b { color: #17201a; }
        .ocs-actions { display: grid; gap: 12px; margin-bottom: 18px; }
        .ocs-btn { display: flex; align-items: center; justify-content: center; gap: 10px; height: 54px; border-radius: 12px; font-weight: 600; font-size: 15.5px; text-decoration: none; }
        .ocs-btn--wa { background: #25d366; color: #fff; }
        .ocs-btn--wa:hover { background: #1eb457; color: #fff; }
        .ocs-continue { display: inline-flex; align-items: center; gap: 8px; color: #67736a; font-size: 14.5px; text-decoration: none; font-weight: 500; }
        .ocs-continue:hover { color: var(--bs-primary); }
    </style>
</head>

<body>
    @php
        $host = $_SERVER['HTTP_HOST'];
        $ocsShop = URL::to($host == env('WEBSITE_HOST') ? $storeinfo->slug : '');
        $ocsAr = app()->getLocale() === 'ar';
    @endphp
    <div class="ocs-wrap">
        <div class="ocs-card">
            <div class="ocs-check"><i class="fa-solid fa-calendar-check"></i></div>
            <h1>{{ $ocsAr ? 'تم تأكيد حجزك!' : 'Booking Confirmed!' }}</h1>
            <p class="ocs-num">{{ $ocsAr ? 'رقم الحجز' : 'Booking' }} <b>#{{ $booking->booking_number }}</b></p>

            <div class="ocs-detail">
                <div><span>{{ $ocsAr ? 'الخدمة' : 'Service' }}</span><b>{{ $booking->service_name }}</b></div>
                <div><span>{{ $ocsAr ? 'التاريخ' : 'Date' }}</span><b>{{ $booking->booking_date }}{{ $booking->booking_time ? ' — ' . $booking->booking_time : '' }}</b></div>
                @if ($booking->amount > 0)
                    <div><span>{{ $ocsAr ? 'المبلغ' : 'Amount' }}</span><b>{{ helper::currency_formate($booking->amount, $vdata) }}</b></div>
                @endif
                @if ($booking->payment_method)
                    <div><span>{{ $ocsAr ? 'الدفع' : 'Payment' }}</span><b>{{ $booking->payment_method }}</b></div>
                @endif
                <div><span>{{ $ocsAr ? 'الاسم' : 'Name' }}</span><b>{{ $booking->customer_name }}</b></div>
            </div>

            <div class="ocs-actions">
                @if (!empty($booking_whatsapp_url))
                    <a href="{{ $booking_whatsapp_url }}" target="_blank" rel="noopener" class="ocs-btn ocs-btn--wa">
                        <i class="fa-brands fa-whatsapp"></i>
                        {{ $ocsAr ? 'إرسال الحجز عبر واتساب' : 'Send booking on WhatsApp' }}
                    </a>
                @endif
            </div>

            <a href="{{ $ocsShop }}" class="ocs-continue">
                <i class="fa-solid fa-arrow-left"></i>
                {{ $ocsAr ? 'العودة للمتجر' : 'Back to store' }}
            </a>
        </div>
    </div>
</body>

</html>
