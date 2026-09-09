@php $tActive = 'home'; @endphp
@extends('front.template-9.layout')

@section('content')
    @php
        $docs = collect($doctors ?? []);
        $svcs = collect($services ?? []);
        $heroImg = helper::food_image($tName . ' medical clinic', $storeinfo->id, 'retail');
        $depts = $docs->map(fn($d) => trim((string) $d->specialty))->filter()->unique()->values();
        $reviews = collect($storereview ?? [])->take(6);
        $bookUrl = URL::to($tSlug . '/booking');
    @endphp

    <!-- HERO -->
    <section class="hero">
      <div class="container">
        <div class="hero-grid">
          <div class="hero-body">
            @if (!empty($tApp->tag_line))<span class="eyebrow">{{ $tApp->tag_line }}</span>@endif
            <h1>{{ $tName }}</h1>
            <p>{{ !empty($tDesc) ? \Illuminate\Support\Str::limit(strip_tags($tDesc), 200) : __('Book an appointment with our doctors in a few taps. Choose a doctor, pick a time, and we\'ll confirm your visit.') }}</p>
            <div class="hero-cta" style="display:flex;gap:12px;flex-wrap:wrap;margin-top:16px">
              <a href="{{ $bookUrl }}" class="btn btn-primary btn-lg">{{ __('Book an appointment') }}</a>
              @if (!empty($tWa))<a href="https://wa.me/{{ $tWa }}" class="btn btn-ghost btn-lg">{{ __('Message on WhatsApp') }}</a>@endif
            </div>
            <div class="hero-trust" style="display:flex;gap:18px;flex-wrap:wrap;margin-top:20px;font-size:.9rem;color:var(--text-2)">
              <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;vertical-align:-2px;color:var(--brand)"><path d="M20 6L9 17l-5-5"/></svg> {{ __('Qualified doctors') }}</span>
              <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;vertical-align:-2px;color:var(--brand)"><path d="M20 6L9 17l-5-5"/></svg> {{ __('Easy booking') }}</span>
              <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;vertical-align:-2px;color:var(--brand)"><path d="M20 6L9 17l-5-5"/></svg> {{ __('WhatsApp confirmation') }}</span>
            </div>
          </div>
          <div class="hero-visual">
            <div class="hero-media"><img src="{{ $heroImg }}" alt="{{ $tName }}"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- TRUST -->
    <section class="section-tight" style="padding-top:0">
      <div class="container">
        <div class="trust-row">
          <div class="trust"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l8 3v6c0 5-3.4 8.4-8 9.6C7.4 20.4 4 17 4 12V6z"/><path d="M9 12l2 2 4-4"/></svg></span><div><strong>{{ __('Trusted care') }}</strong><span>{{ __('Qualified doctors') }}</span></div></div>
          <div class="trust"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span><div><strong>{{ __('Quick booking') }}</strong><span>{{ __('Pick a time that suits') }}</span></div></div>
          <div class="trust"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="2" y="5" width="20" height="14" rx="3"/><path d="M2 10h20"/></svg></span><div><strong>{{ __('Flexible payment') }}</strong><span>{{ __('Pay at reception or online') }}</span></div></div>
          <div class="trust"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3 19.5 19.5 0 01-6-6 19.8 19.8 0 01-3-8.7A2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/></svg></span><div><strong>{{ __('Real people') }}</strong><span>{{ __('Call or WhatsApp us') }}</span></div></div>
        </div>
      </div>
    </section>

    <!-- DEPARTMENTS -->
    @if ($depts->count())
    <section class="section-tight">
      <div class="container">
        <div class="head-row reveal"><div class="section-head" style="margin-bottom:0"><span class="eyebrow">{{ __('Our departments') }}</span><h2>{{ __('Find the right specialty') }}</h2></div></div>
        <div class="dept-grid reveal" style="margin-top:24px">
          @foreach ($depts->take(12) as $dep)
            <a href="{{ $bookUrl }}" class="dept">
              <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l8 3v6c0 5-3.4 8.4-8 9.6C7.4 20.4 4 17 4 12V6z"/><path d="M12 8v6M9 11h6"/></svg></span>
              <strong>{{ $dep }}</strong><span>{{ $docs->where('specialty', $dep)->count() }} {{ __('doctors') }}</span>
            </a>
          @endforeach
        </div>
      </div>
    </section>
    @endif

    <!-- DOCTORS -->
    @if ($docs->count())
    <section class="section-tight">
      <div class="container">
        <div class="head-row reveal">
          <div class="section-head" style="margin-bottom:0"><span class="eyebrow">{{ __('Our team') }}</span><h2>{{ __('Doctors available') }}</h2></div>
          <a href="{{ $bookUrl }}" class="link-arrow">{{ __('Book now') }} <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
        </div>
        <div class="doc-list reveal" style="display:grid;gap:16px;margin-top:24px">
          @foreach ($docs->take(8) as $d)
            @php $dimg = !empty($d->image) ? helper::image_path($d->image) : helper::food_image($d->name . ' doctor', $d->id, 'retail'); @endphp
            <article class="doc">
              <div class="doc__photo">
                <img class="doc__img" src="{{ $dimg }}" alt="{{ $d->name }}" loading="lazy">
                <span class="doc__online"><i></i> {{ __('Available') }}</span>
              </div>
              <div>
                <h4>{{ $d->name }}</h4>
                @if ($d->specialty)<div class="doc__spec">{{ $d->specialty }}</div>@endif
                @if ($d->qualification)<div class="doc__qual">{{ $d->qualification }}</div>@endif
                <div class="doc__meta">
                  @if ($d->experience)<span class="tag"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3v5a4 4 0 008 0V3"/><path d="M6 3H4M14 3h2M10 12v3a5 5 0 0010 0v-2"/><circle cx="20" cy="11" r="2"/></svg> {{ $d->experience }}</span>@endif
                  @if ($d->languages)<span class="tag ok">{{ $d->languages }}</span>@endif
                </div>
                @if (!empty($d->about))<p class="small muted mt-2">{{ \Illuminate\Support\Str::limit(strip_tags($d->about), 90) }}</p>@endif
              </div>
              <div class="doc__right">
                @if ($d->fee > 0)<span class="doc__fee">{{ helper::currency_formate($d->fee, $storeinfo->id) }}<small>{{ __('consultation') }}</small></span>@endif
                <a href="{{ $bookUrl }}?doctor={{ $d->id }}" class="btn btn-primary btn-sm">{{ __('Book') }}</a>
              </div>
            </article>
          @endforeach
        </div>
        <div class="text-center mt-4"><a href="{{ $bookUrl }}" class="btn btn-ghost btn-lg">{{ __('See all doctors') }}</a></div>
      </div>
    </section>
    @endif

    <!-- HOW IT WORKS -->
    <section class="section bg-alt">
      <div class="container">
        <div class="section-head center reveal"><span class="eyebrow">{{ __('How it works') }}</span><h2>{{ __('Booked in four simple steps') }}</h2></div>
        <div class="steps reveal">
          <div class="step"><span class="n">1</span><h4>{{ __('Choose a doctor') }}</h4><p>{{ __('Browse our team and pick the right specialty.') }}</p></div>
          <div class="step"><span class="n">2</span><h4>{{ __('Pick a date & time') }}</h4><p>{{ __('Select when you\'d like your appointment.') }}</p></div>
          <div class="step"><span class="n">3</span><h4>{{ __('Add your details') }}</h4><p>{{ __('Tell us who you are and the reason for your visit.') }}</p></div>
          <div class="step"><span class="n">4</span><h4>{{ __('Confirm & done') }}</h4><p>{{ __('Confirm online or on WhatsApp — we\'ll see you soon.') }}</p></div>
        </div>
      </div>
    </section>

    <!-- STATS -->
    <section class="section-tight">
      <div class="container">
        <div class="stat-row reveal">
          <div class="stat"><b><span data-count="{{ max($docs->count(), 1) }}">0</span></b><span>{{ __('Doctors') }}</span></div>
          <div class="stat"><b><span data-count="{{ max($depts->count(), 1) }}">0</span></b><span>{{ __('Departments') }}</span></div>
          <div class="stat"><b><span data-count="24" data-suffix="/7">0</span></b><span>{{ __('Book anytime') }}</span></div>
          <div class="stat"><b><span data-count="4.9">0</span></b><span>{{ __('Patient rating') }}</span></div>
        </div>
      </div>
    </section>

    @if ($reviews->count())
    <section class="section-tight">
      <div class="container">
        <div class="head-row"><div class="section-head" style="margin-bottom:0"><span class="eyebrow">{{ __('Patient stories') }}</span><h2>{{ __('What patients say') }}</h2></div></div>
        <div class="tst-scroller reveal">
          @foreach ($reviews as $rv)
            <article class="testimonial"><span class="stars">@for ($s = 0; $s < (int) ($rv->star ?? 5); $s++)<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.9 21l1.2-6.8-5-4.9 6.9-1z"/></svg>@endfor</span><p>"{{ \Illuminate\Support\Str::limit($rv->description, 180) }}"</p><div class="who">@if (!empty($rv->image))<img src="{{ helper::image_path($rv->image) }}" alt="">@endif<div><strong>{{ $rv->name }}</strong><span>{{ __('Verified patient') }}</span></div></div></article>
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
              <span class="eyebrow">{{ __('Feel better sooner') }}</span>
              <h2 class="mt-2">{{ __('Book your appointment today.') }}</h2>
              <p>{{ __('Choose a doctor and a time — or message us on WhatsApp.') }}</p>
            </div>
            <div class="flex gap-1 wrap">
              <a href="{{ $bookUrl }}" class="btn btn-primary btn-lg">{{ __('Book an appointment') }}</a>
              @if (!empty($tWa))<a href="https://wa.me/{{ $tWa }}" class="btn btn-ghost btn-lg" style="color:#fff;border-color:rgba(255,255,255,.28)">{{ __('Message on WhatsApp') }}</a>@endif
            </div>
          </div>
        </div>
      </div>
    </section>
@endsection
