@extends('email.layouts.oc')
@section('content')
    <h1 style="margin:0 0 14px;font-size:22px;line-height:1.3;color:#17201a;">{{ trans('labels.reset_email_title') }}</h1>
    <p style="margin:0 0 14px;">{{ trans('labels.email_hello', ['name' => $name]) }}</p>
    <p style="margin:0 0 26px;">{{ trans('labels.reset_email_intro', ['brand' => $brand]) }}</p>

    <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 26px;">
        <tr>
            <td align="center" bgcolor="#1f9d55" style="border-radius:10px;">
                <a href="{{ $url }}" target="_blank"
                    style="display:inline-block;padding:14px 30px;font-family:Arial,Helvetica,sans-serif;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;border-radius:10px;">
                    {{ trans('labels.reset_email_button') }}
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 14px;color:#56635a;font-size:14px;">{{ trans('labels.reset_email_expiry', ['minutes' => $minutes]) }}</p>
    <p style="margin:0 0 22px;color:#56635a;font-size:14px;">{{ trans('labels.reset_email_ignore') }}</p>

    <p style="margin:0;padding-top:18px;border-top:1px solid #e9eee7;color:#8a968c;font-size:12.5px;word-break:break-all;">
        {{ trans('labels.email_button_fallback') }}<br>
        <a href="{{ $url }}" style="color:#1f9d55;">{{ $url }}</a>
    </p>
@endsection
