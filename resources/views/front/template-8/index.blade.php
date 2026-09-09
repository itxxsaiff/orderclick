@php $tActive = 'home'; @endphp
@extends('front.template-8.layout')

@section('content')
    @php
        $svcs = collect($services ?? []);
        $cats = collect($categories ?? []);
        $heroImg = helper::food_image($tName . ' booking venue', $storeinfo->id, 'retail');
        $picks = $svcs->take(8);
        $reviews = collect($storereview ?? [])->take(6);
    @endphp

    <!-- HERO -->
    <section class="hero">
      <div class="hero-media">
        <img src="{{ $heroImg }}" alt="{{ $tName }}">
        <div class="container">
          <div class="hero-body">
            @if (!empty($tApp->tag_line))<span class="eyebrow">{{ $tApp->tag_line }}</span>@endif
            <h1>{{ $tName }}</h1>
            <p>{{ !empty($tDesc) ? \Illuminate\Support\Str::limit(strip_tags($tDesc), 200) : __('Book with us in a few taps. Pick a service, choose a time, and confirm — we\'ll take care of the rest.') }}</p>
            <div class="hero-badges">
              <div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg> {{ __('Easy booking') }}</div>
              <div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg> {{ __('Quick confirmation') }}</div>
              <div><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 00-8.6 15L2 22l5.2-1.4A10 10 0 1012 2z"/></svg> {{ __('Book on WhatsApp') }}</div>
            </div>
            <div class="hero-cta" style="display:flex;gap:12px;flex-wrap:wrap;margin-top:18px">
              <a href="{{ URL::to($tSlug . '/booking') }}" class="btn btn-primary btn-lg">{{ __('Book now') }}</a>
              @if (!empty($tWa))<a href="https://wa.me/{{ $tWa }}" class="btn btn-ghost btn-lg" style="color:#fff;border-color:rgba(255,255,255,.4)">{{ __('Book on WhatsApp') }}</a>@endif
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- USPS -->
    <section class="section-tight">
      <div class="container">
        <div class="usp-row">
          <div class="usp"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l8 3v6c0 5-3.4 8.4-8 9.6C7.4 20.4 4 17 4 12V6z"/><path d="M9 12l2 2 4-4"/></svg></span><div><strong>{{ __('Trusted service') }}</strong><span>{{ __('Quality you can rely on') }}</span></div></div>
          <div class="usp"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span><div><strong>{{ __('Simple booking') }}</strong><span>{{ __('A few taps and you\'re done') }}</span></div></div>
          <div class="usp"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="2" y="5" width="20" height="14" rx="3"/><path d="M2 10h20"/></svg></span><div><strong>{{ __('Flexible payment') }}</strong><span>{{ __('Pay now or on the day') }}</span></div></div>
          <div class="usp"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3 19.5 19.5 0 01-6-6 19.8 19.8 0 01-3-8.7A2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/></svg></span><div><strong>{{ __('Real people') }}</strong><span>{{ __('Call or WhatsApp us') }}</span></div></div>
        </div>
      </div>
    </section>

    <!-- SERVICES -->
    @if ($picks->count())
    <section class="section-tight">
      <div class="container">
        <div class="head-row reveal">
          <div class="section-head" style="margin-bottom:0"><span class="eyebrow">{{ __('What we offer') }}</span><h2>{{ __('Popular bookings') }}</h2></div>
          <a href="{{ URL::to($tSlug . '/booking') }}" class="link-arrow">{{ __('See all') }} <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
        </div>
        <div class="grid g-4 reveal" style="margin-top:26px">
          @foreach ($picks as $s)
            @php $img = !empty($s->image) ? helper::image_path($s->image) : helper::food_image($s->name . ' ' . $s->category, $s->id, 'retail'); @endphp
            <article class="listing">
              <div class="listing__media">
                <a href="{{ URL::to($tSlug . '/booking') }}?service={{ $s->id }}"><img src="{{ $img }}" alt="{{ $s->name }}" loading="lazy"></a>
              </div>
              <div class="listing__body">
                @if ($s->category)<span class="listing__loc">{{ $s->category }}</span>@endif
                <h4><a href="{{ URL::to($tSlug . '/booking') }}?service={{ $s->id }}">{{ $s->name }}</a></h4>
                @if (!empty($s->description))<p class="small muted">{{ \Illuminate\Support\Str::limit(strip_tags($s->description), 70) }}</p>@endif
                <div class="listing__foot">
                  <span class="price">{{ helper::currency_formate($s->price, $storeinfo->id) }}@if (!empty($s->duration))<small> / {{ $s->duration }}</small>@endif</span>
                  <a href="{{ URL::to($tSlug . '/booking') }}?service={{ $s->id }}" class="btn btn-primary btn-sm">{{ __('Book') }}</a>
                </div>
              </div>
            </article>
          @endforeach
        </div>
        <div class="text-center mt-4"><a href="{{ URL::to($tSlug . '/booking') }}" class="btn btn-ghost btn-lg">{{ __('View all services') }}</a></div>
      </div>
    </section>
    @endif

    <!-- HOW IT WORKS -->
    <section class="section bg-alt">
      <div class="container">
        <div class="section-head center reveal"><span class="eyebrow">{{ __('How it works') }}</span><h2>{{ __('Booked in four simple steps') }}</h2></div>
        <div class="steps reveal">
          <div class="step"><span class="n">1</span><h4>{{ __('Choose a service') }}</h4><p>{{ __('Browse what we offer and pick the one that suits you.') }}</p></div>
          <div class="step"><span class="n">2</span><h4>{{ __('Pick a date & time') }}</h4><p>{{ __('Select when you\'d like it — we\'ll confirm availability.') }}</p></div>
          <div class="step"><span class="n">3</span><h4>{{ __('Add your details') }}</h4><p>{{ __('Tell us who you are and any special requests.') }}</p></div>
          <div class="step"><span class="n">4</span><h4>{{ __('Confirm & done') }}</h4><p>{{ __('Confirm online or send it to WhatsApp — we take it from there.') }}</p></div>
        </div>
      </div>
    </section>

    <!-- STATS -->
    <section class="section-tight">
      <div class="container">
        <div class="stat-row reveal">
          <div class="stat"><b><span data-count="{{ max($svcs->count(), 1) }}">0</span></b><span>{{ __('Services offered') }}</span></div>
          <div class="stat"><b><span data-count="{{ max($cats->count(), 1) }}">0</span></b><span>{{ __('Categories') }}</span></div>
          <div class="stat"><b><span data-count="24" data-suffix="/7">0</span></b><span>{{ __('Book anytime') }}</span></div>
          <div class="stat"><b><span data-count="4.9">0</span></b><span>{{ __('Average rating') }}</span></div>
        </div>
      </div>
    </section>

    @if ($reviews->count())
    <section class="section-tight">
      <div class="container">
        <div class="head-row"><div class="section-head" style="margin-bottom:0"><span class="eyebrow">{{ __('Loved by guests') }}</span><h2>{{ __('What people say') }}</h2></div></div>
        <div class="tst-scroller reveal">
          @foreach ($reviews as $rv)
            <article class="testimonial"><span class="stars">@for ($s = 0; $s < (int) ($rv->star ?? 5); $s++)<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.9 21l1.2-6.8-5-4.9 6.9-1z"/></svg>@endfor</span><p>"{{ \Illuminate\Support\Str::limit($rv->description, 180) }}"</p><div class="who">@if (!empty($rv->image))<img src="{{ helper::image_path($rv->image) }}" alt="">@endif<div><strong>{{ $rv->name }}</strong><span>{{ __('Verified guest') }}</span></div></div></article>
          @endforeach
        </div>
      </div>
    </section>
    @endif

    <!-- CTA -->
    <section class="section-tight">
      <div class="container">
        <div class="cta-band reveal">
          <div class="cta-band__inner">
            <div>
              <span class="eyebrow">{{ __('Ready when you are') }}</span>
              <h2 class="mt-2">{{ __('Book your spot in minutes.') }}</h2>
              <p>{{ __('Pick a service and a time — or send us a message on WhatsApp.') }}</p>
            </div>
            <div class="flex gap-1 wrap">
              <a href="{{ URL::to($tSlug . '/booking') }}" class="btn btn-primary btn-lg">{{ __('Book now') }}</a>
              @if (!empty($tWa))<a href="https://wa.me/{{ $tWa }}" class="btn btn-ghost btn-lg" style="color:#fff;border-color:rgba(255,255,255,.28)">{{ __('Book on WhatsApp') }}</a>@endif
            </div>
          </div>
        </div>
      </div>
    </section>
@endsection
