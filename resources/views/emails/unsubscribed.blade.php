<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>{{ trans('labels.email_preferences') }}</title>
    <style>
        body { margin:0; min-height:100vh; display:flex; align-items:center; justify-content:center;
            font:16px/1.6 -apple-system,"Segoe UI",Helvetica,Arial,sans-serif; color:#17201a;
            background:radial-gradient(120% 120% at 90% -10%, rgba(31,157,85,.12), transparent 55%), #f5f7f4; padding:24px; }
        .box { max-width:520px; background:#fff; border:1px solid #e5e9e2; border-radius:18px; padding:40px 34px;
            text-align:center; box-shadow:0 24px 60px -32px rgba(20,32,24,.4); }
        .dot { width:56px; height:56px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center;
            font-size:26px; margin-bottom:18px; }
        .ok { background:#eafaf0; color:#1f9d55; }
        .bad { background:#fdecec; color:#d64545; }
        h1 { font-size:21px; margin:0 0 10px; }
        p { color:#6b7669; margin:0 0 8px; }
        .mail { font-weight:600; color:#17201a; }
        a.btn { display:inline-block; margin-top:18px; padding:11px 22px; border-radius:10px; background:#1f9d55;
            color:#fff; text-decoration:none; font-weight:600; }
        a.link { color:#1f9d55; }
    </style>
</head>
<body>
    <div class="box">
        @if (!$ok)
            <div class="dot bad">&#10005;</div>
            <h1>{{ trans('messages.unsubscribe_link_invalid') }}</h1>
        @elseif (!empty($resubscribed))
            <div class="dot ok">&#10003;</div>
            <h1>{{ trans('messages.resubscribed_title') }}</h1>
            <p><span class="mail">{{ $email }}</span></p>
            <p>{{ trans('messages.resubscribed_body') }}</p>
        @else
            <div class="dot ok">&#10003;</div>
            <h1>{{ trans('messages.unsubscribed_title') }}</h1>
            <p><span class="mail">{{ $email }}</span></p>
            <p>{{ trans('messages.unsubscribed_body') }}</p>
            @if (!empty($resubUrl))
                <p style="margin-top:14px;font-size:14px;">
                    {{ trans('messages.unsubscribed_mistake') }}
                    <a class="link" href="{{ $resubUrl }}">{{ trans('labels.resubscribe') }}</a>
                </p>
            @endif
        @endif
        <a class="btn" href="{{ url('/') }}">{{ trans('labels.back_to_website') }}</a>
    </div>
</body>
</html>
