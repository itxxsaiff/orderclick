{{--
    Branded layout for account emails. Table-based with inline styles only: Gmail, Outlook and
    Apple Mail ignore most <style> rules. Expects $brand, $logo, $preheader; pages fill 'content'.
--}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>{{ $subject ?? $brand }}</title>
</head>
<body style="margin:0;padding:0;background:#f3f6f2;-webkit-text-size-adjust:100%;">
    {{-- Inbox preview line, hidden in the message itself. --}}
    <div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;">{{ $preheader ?? '' }}</div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f3f6f2;">
        <tr>
            <td align="center" style="padding:32px 16px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:560px;">
                    <tr>
                        <td align="center" style="padding:0 0 22px;">
                            @if (!empty($logo))
                                <img src="{{ $logo }}" alt="{{ $brand }}" height="48" style="display:block;height:48px;width:auto;border:0;">
                            @else
                                <span style="font-family:Arial,Helvetica,sans-serif;font-size:22px;font-weight:700;color:#1f9d55;">{{ $brand }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#ffffff;border:1px solid #e3e9e1;border-radius:16px;padding:36px 36px 32px;font-family:Arial,Helvetica,sans-serif;color:#1d2a21;font-size:15px;line-height:1.6;">
                            @yield('content')
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:22px 12px 0;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:1.6;color:#8a968c;">
                            {{ trans('labels.email_footer_automated', ['brand' => $brand]) }}<br>
                            &copy; {{ date('Y') }} {{ $brand }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
