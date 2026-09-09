@php $tActive = 'menu'; @endphp
@extends('front.template-10.layout')

@section('content')
    <section style="padding:clamp(48px,8vw,110px) 0">
      <div class="container" style="max-width:560px">
        <div class="panel" style="text-align:center;padding:clamp(28px,5vw,48px)">
          <span style="width:88px;height:88px;border-radius:50%;background:var(--brand-soft,#f1ecf7);color:var(--brand);display:grid;place-items:center;margin:0 auto 22px">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="width:40px;height:40px"><path d="M20 6L9 17l-5-5"/></svg>
          </span>
          <h1 style="font-size:clamp(1.6rem,3.6vw,2.1rem)">{{ __('Booking Confirmed!') }}</h1>
          <p class="muted mt-1">{{ __('Booking') }} <strong>#{{ optional($booking)->booking_number ?? optional($booking)->id }}</strong></p>
          <p class="muted" style="margin-top:10px">{{ __('Send your booking to us on WhatsApp so we can confirm it quickly.') }}</p>
          <div style="display:grid;gap:12px;margin-top:26px">
            @if (!empty($booking_whatsapp_url))
              <a href="{{ $booking_whatsapp_url }}" target="_blank" rel="noopener" class="btn btn-primary btn-lg btn-block">
                <svg viewBox="0 0 24 24" fill="currentColor" style="width:20px;height:20px"><path d="M12 2a10 10 0 00-8.6 15L2 22l5.2-1.4A10 10 0 1012 2z"/></svg>
                {{ __('Send booking on WhatsApp') }}
              </a>
            @endif
            <a href="{{ URL::to($tSlug . '/booking') }}" class="btn btn-dark btn-lg btn-block">{{ __('Make another booking') }}</a>
            <a href="{{ $tBase }}" class="link-arrow" style="justify-content:center;margin-top:6px">{{ __('Back to home') }}</a>
          </div>
        </div>
      </div>
    </section>
@endsection
