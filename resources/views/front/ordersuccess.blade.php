<html lang="{{ session()->get('locale', app()->getLocale()) }}" dir="{{ session()->get('direction') == 2 ? 'rtl' : 'ltr' }}" class="light">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script>
        const theme = localStorage.getItem('theme');
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.add('light');
        }
    </script>
    
    <title>{{ helper::appdata($vdata)->website_title }}</title>
    <!-- font-family -->
    <link rel="icon" href="{{ helper::image_path(helper::appdata(@$vdata)->favicon) }}" type="image" sizes="16x16">
    <link href="assets/font/css2.css" rel="stylesheet">
    <!-- font awesome -->
    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'web-assets/font-awesome/css/all.min.css') }}">
    <!-- dataTables -->
    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'web-assets/css/dataTables.bootstrap4.min.css') }}">
    <!-- carousel css -->
    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'web-assets/css/owl.carousel.min.css') }}">
    <!-- carousel css -->
    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'web-assets/css/owl.theme.default.css') }}">
    <!-- bootstrap min css -->
    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'web-assets/css/bootstrap.min.css') }}">
    <!-- style css -->
    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'web-assets/css/style.css') }}">
    <!-- responsive css -->
    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'web-assets/css/responsive.css') }}">
    <style>
        :root {
            --bs-primary: #ce6a19;
            --bs-secondary: #5a0bee;

            @if (helper::appdata($vdata)->primary_color != null)
                --bs-primary: {{ helper::appdata($vdata)->primary_color }};
            @endif
            @if (helper::appdata($vdata)->secondary_color != null)
                --bs-secondary: {{ helper::appdata($vdata)->secondary_color }};
            @endif
            --secondary-color: #000;
            --font-family: 'Outfit',
            sans-serif;
        }
    </style>
    <style>
        .ocs-body { background: #f6f8f5 !important; min-height: 100vh; margin: 0; font-family: 'Outfit', system-ui, -apple-system, sans-serif; }
        .ocs-wrap { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px 16px; box-sizing: border-box; }
        .ocs-card { background: #fff; border: 1px solid #e7ece4; border-radius: 24px; max-width: 520px; width: 100%; padding: 50px 40px; text-align: center; box-sizing: border-box; box-shadow: 0 34px 80px -46px rgba(20,40,28,.4); }
        .ocs-check { width: 96px; height: 96px; border-radius: 50%; background: rgba(31,157,85,.10); display: flex; align-items: center; justify-content: center; margin: 0 auto 26px; animation: ocsPop .5s cubic-bezier(.2,.7,.3,1.4); }
        .ocs-check i { font-size: 44px; color: var(--bs-primary, #1f9d55); }
        @keyframes ocsPop { 0% { transform: scale(.3); opacity: 0; } 60% { transform: scale(1.1); } 100% { transform: scale(1); opacity: 1; } }
        .ocs-card h1 { font-size: 26px; font-weight: 800; color: #17201a; margin: 0 0 12px; letter-spacing: -.01em; }
        .ocs-num { color: #67736a; font-size: 15.5px; margin: 0 0 6px; }
        .ocs-num b { color: var(--bs-primary, #1f9d55); }
        .ocs-sub { color: #8a978d; font-size: 14px; margin: 0 0 30px; line-height: 1.5; }
        .ocs-actions { display: grid; gap: 12px; margin-bottom: 20px; }
        .ocs-btn { display: flex; align-items: center; justify-content: center; gap: 10px; height: 54px; border-radius: 12px; font-weight: 600; font-size: 15.5px; text-decoration: none; transition: .15s; border: 0; }
        .ocs-btn--wa { background: #25d366; color: #fff; }
        .ocs-btn--wa:hover { background: #1eb457; color: #fff; }
        .ocs-btn--track { background: var(--bs-primary, #1f9d55); color: #fff; }
        .ocs-btn--track:hover { filter: brightness(.93); color: #fff; }
        .ocs-continue { display: inline-flex; align-items: center; gap: 8px; color: #67736a; font-size: 14.5px; text-decoration: none; font-weight: 500; }
        .ocs-continue:hover { color: var(--bs-primary, #1f9d55); }
    </style>
</head>

<body class="ocs-body">
    @php
        $host = $_SERVER['HTTP_HOST'];
        $ocsTrack = URL::to($host == env('WEBSITE_HOST') ? $storeinfo->slug . '/track-order/' . $order_number : '/track-order/' . $order_number);
        $ocsShop = URL::to($host == env('WEBSITE_HOST') ? $storeinfo->slug : '');
        $ocsAr = app()->getLocale() === 'ar';
    @endphp
    <div class="ocs-wrap">
        <div class="ocs-card">
            <div class="ocs-check"><i class="fa-solid fa-check"></i></div>
            <h1>{{ $ocsAr ? 'تم استلام طلبك بنجاح!' : 'Order Placed Successfully!' }}</h1>
            <p class="ocs-num">{{ $ocsAr ? 'رقم الطلب' : 'Order' }} <b>#{{ $order_number }}</b></p>
            <p class="ocs-sub">{{ $ocsAr ? 'أرسل طلبك إلى المتجر عبر واتساب لتأكيده بسرعة.' : 'Send your order to the store on WhatsApp so they can confirm it quickly.' }}</p>

            <div class="ocs-actions">
                @if (!empty($order_whatsapp_url))
                    <a href="{{ $order_whatsapp_url }}" id="ocSendWhatsapp" class="ocs-btn ocs-btn--wa" target="_blank" rel="noopener">
                        <i class="fa-brands fa-whatsapp"></i>
                        {{ $ocsAr ? 'إرسال الطلب عبر واتساب' : 'Send order on WhatsApp' }}
                    </a>
                @endif
                <a href="{{ $ocsTrack }}" class="ocs-btn ocs-btn--track" target="_blank">
                    <i class="fa-solid fa-location-arrow"></i>
                    {{ trans('labels.track_order') }}
                </a>
            </div>

            <a href="{{ $ocsShop }}" class="ocs-continue">
                <i class="fa-solid fa-arrow-left"></i>
                {{ trans('labels.continue_shop') }}
            </a>
        </div>
    </div>
</body>
<script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/jquery-3.6.3.min.js') }}"></script>
<script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/custom.js') }}"></script>
<script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/owl.carousel.min.js') }}"></script>
<script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ url(env('ASSETSPATHURL') . 'web-assets/js/sweetalert2@11.js') }}"></script>
<script>
    function setLightMode() {
        document.documentElement.classList.remove('dark');
        document.documentElement.classList.add('light');
       localStorage.setItem('theme', 'light');
    }

        function setDarkMode() {
            document.documentElement.classList.remove('light');
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        }
</script>
<script>
    $(document).ready(function() {
        $('#example').DataTable();
    });
</script>
<script>
    function copytext(copied) {
        "use strict";
        var copyText = document.getElementById("data");
        copyText.select();
        document.execCommand("copy");
        document.getElementById("tool").innerHTML = copied;
    }
</script>

</html>
