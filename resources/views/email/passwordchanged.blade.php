@extends('email.layouts.oc')
@section('content')
    <h1 style="margin:0 0 14px;font-size:22px;line-height:1.3;color:#17201a;">{{ trans('labels.changed_email_title') }}</h1>
    <p style="margin:0 0 14px;">{{ trans('labels.email_hello', ['name' => $name]) }}</p>
    <p style="margin:0 0 22px;">{{ trans('labels.changed_email_body', ['brand' => $brand, 'date' => $when]) }}</p>

    <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px;">
        <tr>
            <td align="center" bgcolor="#1f9d55" style="border-radius:10px;">
                <a href="{{ $loginUrl }}" target="_blank"
                    style="display:inline-block;padding:13px 28px;font-family:Arial,Helvetica,sans-serif;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;border-radius:10px;">
                    {{ trans('labels.login') }}
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:0;color:#56635a;font-size:14px;">{{ trans('labels.changed_email_warning') }}</p>
@endsection
