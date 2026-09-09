<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>{{ trans('messages.store_not_activated') }}</title>
    <style>
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center;
            font: 16px/1.6 -apple-system, "Segoe UI", Helvetica, Arial, sans-serif; color: #17201a;
            background: radial-gradient(120% 120% at 90% -10%, rgba(31,157,85,.12), transparent 55%), #f5f7f4; padding: 24px; }
        .box { max-width: 520px; background: #fff; border: 1px solid #e5e9e2; border-radius: 18px; padding: 40px 34px;
            text-align: center; box-shadow: 0 24px 60px -32px rgba(20,32,24,.4); }
        .dot { width: 56px; height: 56px; border-radius: 50%; background: #fdf3e0; color: #d98a0b;
            display: inline-flex; align-items: center; justify-content: center; font-size: 26px; margin-bottom: 18px; }
        h1 { font-size: 22px; margin: 0 0 10px; }
        p { color: #6b7669; margin: 0; }
    </style>
</head>
<body>
    <div class="box">
        <div class="dot">&#9203;</div>
        <h1>{{ @$storeinfo->name ?: trans('messages.store_not_activated') }}</h1>
        <p>{{ trans('messages.store_not_activated') }}</p>
    </div>
</body>
</html>
