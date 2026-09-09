<html>
<head><meta charset="utf-8"><title>Service Agreement</title></head>
<style>
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #222; line-height: 1.6; }
    h1 { font-size: 18px; text-align: center; margin-bottom: 4px; }
    h2 { font-size: 13px; margin: 18px 0 4px; }
    .muted { color: #666; }
    .sign { margin-top: 40px; }
    .sign td { padding-top: 34px; border-top: 1px solid #999; width: 45%; font-size: 11px; }
    table { width: 100%; border-collapse: collapse; }
</style>
<body>
    <h1>{{ $company['name'] }} — Service Agreement</h1>
    <p class="muted" style="text-align:center;margin-top:0;">
        {{ $company['legal'] }}@if ($company['tax_no']) · Tax Registration: {{ $company['tax_no'] }}@endif
    </p>

    <h2>1. Parties</h2>
    <p>
        This agreement is made between <strong>{{ $company['legal'] ?: $company['name'] }}</strong> ("the Platform")
        and <strong>{{ $vendor->trade_name ?: $vendor->name }}</strong> (Vendor ID {{ $vendor->vendor_code }}) ("the Vendor").
    </p>

    <h2>2. Scope</h2>
    <p>
        The Platform provides the Vendor with access to the Order Click
        {{ \App\Helpers\Systems::label($vendor->system) }} system, including a public business page,
        order/booking/request management and the associated tools included in the Vendor's subscription plan.
    </p>

    <h2>3. Verification</h2>
    <p>
        The Vendor confirms that all registration details and uploaded documents are accurate and current.
        The Platform may request corrections or additional documents, and may restrict the public page where
        there is a legal, fraudulent or safety concern.
    </p>

    <h2>4. Subscription</h2>
    <p>
        The subscription period begins on the date the Vendor's public website is activated, not on the payment
        date. Fees are payable in advance and are non-refundable once the website is activated.
    </p>

    <h2>5. Responsibilities</h2>
    <p>
        The Vendor is responsible for the accuracy of its listings, prices, availability and the fulfilment of
        its own customer orders, bookings or service requests. The Platform provides the software; it is not a
        party to transactions between the Vendor and its customers.
    </p>

    <h2>6. Data</h2>
    <p>
        Each party will handle customer data in line with applicable law. The Platform does not sell Vendor or
        customer data to third parties.
    </p>

    <h2>7. Termination</h2>
    <p>
        Either party may terminate with written notice. The Vendor's saved setup and data remain available for
        export for a reasonable period following termination.
    </p>

    <p class="muted" style="margin-top:24px;">
        Please print, sign and stamp this agreement, then upload it in Step 4 of your account setup.
    </p>

    <table class="sign">
        <tr>
            <td>Vendor signature &amp; stamp<br><span class="muted">{{ $vendor->trade_name ?: $vendor->name }}</span></td>
            <td style="width:10%;border:0;"></td>
            <td>Date</td>
        </tr>
    </table>
</body>
</html>
