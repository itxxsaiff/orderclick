@php $tActive = 'cart'; @endphp
@extends('front.template-20.layout')

@section('content')
    @php
        $ocsTrack = URL::to($tSlug . '/track-order/' . $order_number);
    @endphp
    <section style="padding:clamp(48px,8vw,110px) 0">
      <div class="container" style="max-width:560px">
        <div class="panel" style="text-align:center;padding:clamp(28px,5vw,48px)">
          <span style="width:88px;height:88px;border-radius:50%;background:var(--bg-alt,#eef7f0);color:var(--brand);display:grid;place-items:center;margin:0 auto 22px">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="width:40px;height:40px"><path d="M20 6L9 17l-5-5"/></svg>
          </span>
          <h1 style="font-size:clamp(1.6rem,3.6vw,2.1rem)">{{ __('Order Placed Successfully!') }}</h1>
          <p class="muted mt-1" style="font-size:1rem">{{ __('Order') }} <strong>#{{ $order_number }}</strong></p>
          <p class="muted" style="margin-top:10px">{{ __('Send your order to the store on WhatsApp so they can confirm it quickly.') }}</p>

          <div style="display:grid;gap:12px;margin-top:26px">
            @if (!empty($order_whatsapp_url))
              <a href="{{ $order_whatsapp_url }}" target="_blank" rel="noopener" class="btn btn-primary btn-lg btn-block">
                <svg viewBox="0 0 24 24" fill="currentColor" style="width:20px;height:20px"><path d="M12 2a10 10 0 00-8.6 15L2 22l5.2-1.4A10 10 0 1012 2zm5.5 12.4c-.3-.2-1.7-.9-2-1s-.5-.1-.7.1-.7.9-.9 1.1-.4.2-.7.1a8.2 8.2 0 01-2.4-1.5 9 9 0 01-1.7-2.1c-.2-.3 0-.5.1-.6l.5-.6.3-.5v-.5l-.9-2.2c-.3-.6-.5-.5-.7-.5h-.6a1.2 1.2 0 00-.8.4A3.4 3.4 0 005 8.8a5.9 5.9 0 001.3 3.2 13.5 13.5 0 005.2 4.6 17 17 0 001.7.6 4.2 4.2 0 001.9.1 3.1 3.1 0 002-1.4 2.5 2.5 0 00.2-1.4c-.1-.1-.3-.2-.6-.3z"/></svg>
                {{ __('Send order on WhatsApp') }}
              </a>
            @endif
            <a href="{{ $ocsTrack }}" target="_blank" class="btn btn-dark btn-lg btn-block">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4z"/></svg>
              {{ __('Track Order') }}
            </a>
            <a href="{{ URL::to($tSlug . '/categories') }}" class="link-arrow" style="justify-content:center;margin-top:6px">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px"><path d="M19 12H5M11 6l-6 6 6 6"/></svg>
              {{ __('Continue to shop') }}
            </a>
          </div>
        </div>
      </div>
    </section>
@endsection
