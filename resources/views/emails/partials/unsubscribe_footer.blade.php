{{-- Unsubscribe footer for MARKETING emails.
     Include it in any promotional send:
        @include('emails.partials.unsubscribe_footer', ['subscriber' => $subscriber])
     Do NOT add it to transactional mail (orders, invoices, password resets) — those are not
     marketing and must still reach an unsubscribed address. --}}
@if (!empty($subscriber) && !empty($subscriber->token))
    <div style="margin-top:28px;padding-top:16px;border-top:1px solid #e5e9e2;text-align:center;
                font:12px/1.6 Arial,Helvetica,sans-serif;color:#8a978d;">
        {{ trans('messages.email_footer_reason') }}<br>
        <a href="{{ $subscriber->unsubscribeUrl() }}" style="color:#1f9d55;text-decoration:underline;">
            {{ trans('labels.unsubscribe') }}
        </a>
    </div>
@endif
