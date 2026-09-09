@php $tActive = 'contact'; @endphp
@extends('front.template-5.layout')

@section('content')
    @php
        $tms = collect($timings ?? []);
        $mapLink = optional($tApp)->map_link;
    @endphp

    <section class="page-head">
      <div class="container">
        <div class="crumb">
          <a href="{{ $tBase }}">{{ __('Home') }}</a>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M9 6l6 6-6 6"/></svg>
          <span class="now">{{ __('Contact us') }}</span>
        </div>
        <div class="section-head">
          <span class="eyebrow">{{ __('We answer fast') }}</span>
          <h1>{{ __('Get in touch') }}</h1>
          <p>{{ __('A question about your order, allergens, or a big booking — someone from the team reads every message.') }}</p>
        </div>
        <div class="grid g-4">
          @if (!empty($tPhone))
          <div class="contact-card">
            <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3 19.5 19.5 0 01-6-6 19.8 19.8 0 01-3-8.7A2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/></svg></span>
            <div><strong>{{ __('Call or WhatsApp') }}</strong><a href="tel:{{ $tPhone }}">{{ $tPhone }}</a></div>
          </div>
          @endif
          @if (!empty($tEmail))
          <div class="contact-card">
            <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2.5" y="4.5" width="19" height="15" rx="2.5"/><path d="M3 7l9 6 9-6"/></svg></span>
            <div><strong>{{ __('Email us') }}</strong><a href="mailto:{{ $tEmail }}">{{ $tEmail }}</a></div>
          </div>
          @endif
          @if (!empty($tAddr))
          <div class="contact-card">
            <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s7-5.6 7-11a7 7 0 10-14 0c0 5.4 7 11 7 11z"/><circle cx="12" cy="10" r="2.6"/></svg></span>
            <div><strong>{{ __('Find us') }}</strong><p>{{ $tAddr }}</p></div>
          </div>
          @endif
          @if (!empty($tWa))
          <div class="contact-card">
            <span class="ic"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 00-8.6 15L2 22l5.2-1.4A10 10 0 1012 2z"/></svg></span>
            <div><strong>{{ __('Chat on WhatsApp') }}</strong><a href="https://wa.me/{{ $tWa }}" target="_blank" rel="noopener">{{ __('Start a chat') }}</a></div>
          </div>
          @endif
        </div>
      </div>
    </section>

    <section style="padding-bottom:clamp(40px,6vw,72px)">
      <div class="container contact-layout">

        <div>
          @if ($tms->count())
          <div class="panel">
            <div class="panel__head">
              <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span>
              <h3>{{ __('Opening hours') }}</h3>
            </div>
            <div class="panel__body">
              <ul class="hours-list">
                @foreach ($tms as $t)
                  <li>
                    <span>{{ ucfirst($t->day) }}</span>
                    <strong>{{ $t->is_always_close == 1 ? __('Closed') : (\Illuminate\Support\Str::of($t->open_time)->limit(5, '') . ' – ' . \Illuminate\Support\Str::of($t->close_time)->limit(5, '')) }}</strong>
                  </li>
                @endforeach
              </ul>
            </div>
          </div>
          @endif

          @if (!empty($mapLink))
          <div class="map-frame mt-2">
            <iframe title="{{ __('Our location') }}" loading="lazy" src="{{ $mapLink }}"></iframe>
          </div>
          @endif

          @if (!empty($tWa))
          <div class="panel mt-2">
            <div class="panel__body flex items-center gap-2">
              <span class="brand-mark" style="width:44px;height:44px"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 00-8.6 15L2 22l5.2-1.4A10 10 0 1012 2z"/></svg></span>
              <div>
                <strong style="font-size:.95rem;letter-spacing:-.02em">{{ __('Fastest way to reach us') }}</strong>
                <p class="small">{{ __('WhatsApp is answered within minutes during service.') }}</p>
              </div>
              <a href="https://wa.me/{{ $tWa }}" class="btn btn-primary btn-sm" style="margin-left:auto">{{ __('Chat') }}</a>
            </div>
          </div>
          @endif
        </div>

        <div class="panel">
          <div class="panel__head">
            <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2.5" y="4.5" width="19" height="15" rx="2.5"/><path d="M3 7l9 6 9-6"/></svg></span>
            <h3>{{ __('Send us a message') }}</h3>
          </div>
          <div class="panel__body">
            @if (session('success'))<div class="note mb-2" style="border-color:var(--brand)"><p>{{ session('success') }}</p></div>@endif
            @if (session('error'))<div class="note mb-2" style="border-color:#e5484d"><p>{{ session('error') }}</p></div>@endif
            <form action="{{ URL::to($tSlug . '/submit') }}" method="POST">
              @csrf
              <input type="hidden" name="vendor_id" value="{{ $storeinfo->id }}">
              <input type="hidden" name="last_name" value="">
              <div class="form-grid mb-2">
                <div class="field">
                  <label for="cname">{{ __('Your name') }} <span class="req">*</span></label>
                  <input class="input" id="cname" name="first_name" placeholder="{{ __('Full name') }}" required>
                </div>
                <div class="field">
                  <label for="cphone">{{ __('Mobile') }} <span class="req">*</span></label>
                  <input class="input" id="cphone" name="mobile" type="tel" placeholder="+___ ____ ____" required>
                </div>
              </div>
              <div class="field mb-2">
                <label for="cemail">{{ __('Email') }}</label>
                <input class="input" id="cemail" name="email" type="email" placeholder="you@example.com">
              </div>
              <div class="field">
                <label for="cmsg">{{ __('Message') }} <span class="req">*</span></label>
                <textarea class="textarea" id="cmsg" name="message" placeholder="{{ __('Tell us what you need…') }}" required></textarea>
              </div>
              <button class="btn btn-primary btn-block btn-lg mt-3" type="submit">{{ __('Send message') }}</button>
              <p class="hint text-center mt-2">{{ __('We reply to everything. Usually the same day.') }}</p>
            </form>
          </div>
        </div>
      </div>
    </section>
@endsection
