@php $tActive = 'home'; @endphp
@extends('front.template-10.layout')

@section('content')
    @php
        $docs = collect($doctors ?? []);
        $svcs = collect($services ?? []);
        $reviews = collect($storereview ?? [])->take(6);
        $cats = $docs->map(fn($d) => trim((string) $d->specialty))->filter()->unique()->values();
        $heroImg = helper::food_image($tName . ' beauty salon studio', $storeinfo->id, 'retail');
        $bookUrl = URL::to($tSlug . '/booking');
        $clock = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>';
    @endphp

    <!-- HERO -->
    <section class="hero">
      <div class="container hero-grid">
        <div>
          @if (!empty($tApp->tag_line))<span class="eyebrow">{{ $tApp->tag_line }}</span>@endif
          <h1>{{ $tName }}</h1>
          <p class="lead">{{ !empty($tDesc) ? \Illuminate\Support\Str::limit(strip_tags($tDesc), 190) : __('Hair, skin, nails and wellness — pick a treatment, choose who you\'d like, take a slot that suits. Confirmed on WhatsApp in about a minute.') }}</p>
          <div class="hero-cta">
            <a href="{{ $bookUrl }}" class="btn btn-primary btn-lg">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M8 3v4M16 3v4M3 10h18"/></svg>
              {{ __('Book an appointment') }}
            </a>
            <a href="{{ $bookUrl }}" class="btn btn-ghost btn-lg">{{ __('See services & prices') }}</a>
          </div>
          <ul class="hero-points">
            <li><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span> {{ __('No account, no password — just your name and number') }}</li>
            <li><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span> {{ __('Confirmation and reminder straight to WhatsApp') }}</li>
            <li><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span> {{ __('Free to change or cancel before your slot') }}</li>
          </ul>
        </div>
        <div class="hero-visual">
          <div class="hero-media"><img src="{{ $heroImg }}" alt="{{ $tName }}"></div>
          <div class="float-card float-card--next">
            <span class="brand-mark" style="width:38px;height:38px">{!! $clock !!}</span>
            <div><strong>{{ __('Book anytime') }}</strong><span>{{ __('24/7 online') }}</span></div>
          </div>
        </div>
      </div>
    </section>

    <!-- CATEGORIES (from specialities) -->
    @if ($cats->count())
    <section class="section-tight">
      <div class="container">
        <div class="head-row reveal">
          <div class="section-head" style="margin-bottom:0"><span class="eyebrow">{{ __('What we do') }}</span><h2>{{ __('Pick a treatment') }}</h2></div>
          <a href="{{ $bookUrl }}" class="link-arrow">{{ __('Full price list') }} <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
        </div>
        <div class="cat-row reveal">
          @foreach ($cats->take(6) as $cat)
            <a href="{{ $bookUrl }}" class="cat">
              <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.8 4.9L19 9.6l-5.2 1.8L12 16.3l-1.8-4.9L5 9.6l5.2-1.7z"/></svg></span>
              <strong>{{ $cat }}</strong><span>{{ $docs->where('specialty', $cat)->count() }} {{ __('specialists') }}</span>
            </a>
          @endforeach
        </div>
      </div>
    </section>
    @endif

    <!-- POPULAR SERVICES -->
    @if ($svcs->count())
    <section class="section">
      <div class="container">
        <div class="head-row reveal">
          <div class="section-head" style="margin-bottom:0"><span class="eyebrow">{{ __('Most booked') }}</span><h2>{{ __('What people come in for') }}</h2></div>
          <a href="{{ $bookUrl }}" class="link-arrow">{{ __('Book now') }} <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
        </div>
        <div class="grid g-4 services reveal">
          @foreach ($svcs->take(8) as $i => $s)
            @php $simg = !empty($s->image) ? helper::image_path($s->image) : helper::food_image($s->name . ' salon treatment', $s->id, 'retail'); @endphp
            <article class="svc-card">
              <div class="svc-card__media">
                @if ($i === 0)<span class="badge brand">{{ __('Most booked') }}</span>@endif
                <a href="{{ $bookUrl }}"><img src="{{ $simg }}" alt="{{ $s->name }}" loading="lazy"></a>
              </div>
              <div class="svc-card__body">
                <h4><a href="{{ $bookUrl }}">{{ $s->name }}</a></h4>
                @if (!empty($s->description))<p>{{ \Illuminate\Support\Str::limit(strip_tags($s->description), 92) }}</p>@endif
                <div class="svc-card__foot">
                  <a href="{{ $bookUrl }}" class="meta-chip brand">{{ __('Book') }} <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
                  @if ($s->price > 0)<span class="price">{{ helper::currency_formate($s->price, $storeinfo->id) }}</span>@endif
                </div>
              </div>
            </article>
          @endforeach
        </div>
        <div class="text-center mt-4"><a href="{{ $bookUrl }}" class="btn btn-ghost btn-lg">{{ __('See all services') }}</a></div>
      </div>
    </section>
    @endif

    <!-- HOW IT WORKS -->
    <section class="section bg-alt">
      <div class="container">
        <div class="section-head center reveal">
          <span class="eyebrow">{{ __('Booking') }}</span>
          <h2>{{ __('Four taps and it\'s yours') }}</h2>
          <p>{{ __('No account to create, no password to forget. Your confirmation arrives on WhatsApp with everything you need.') }}</p>
        </div>
        <div class="flow reveal">
          <div class="flow-step"><span class="n">1</span><h4>{{ __('Choose a treatment') }}</h4><p>{{ __('Every service shows the real price — no surprises at the chair.') }}</p></div>
          <div class="flow-step"><span class="n">2</span><h4>{{ __('Pick who you\'d like') }}</h4><p>{{ __('Choose a specific specialist, or leave it to us and we\'ll match you.') }}</p></div>
          <div class="flow-step"><span class="n">3</span><h4>{{ __('Take a slot') }}</h4><p>{{ __('Choose the day and time that works best for you.') }}</p></div>
          <div class="flow-step"><span class="n">4</span><h4>{{ __('Confirm & done') }}</h4><p>{{ __('Name and number, that\'s it. Confirmation on WhatsApp instantly.') }}</p></div>
        </div>
        <div class="text-center mt-4"><a href="{{ $bookUrl }}" class="btn btn-primary btn-lg">{{ __('Start booking') }}</a></div>
      </div>
    </section>

    <!-- TEAM -->
    @if ($docs->count())
    <section class="section">
      <div class="container">
        <div class="head-row reveal">
          <div class="section-head" style="margin-bottom:0"><span class="eyebrow">{{ __('The team') }}</span><h2>{{ __('Book with someone specific') }}</h2><p class="mt-1">{{ __('Everyone here has a speciality. If you\'re not sure who fits, we\'ll match you.') }}</p></div>
          <a href="{{ $bookUrl }}" class="link-arrow">{{ __('Meet everyone') }} <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
        </div>
        <div class="grid g-4 reveal">
          @foreach ($docs->take(8) as $d)
            @php $dimg = !empty($d->image) ? helper::image_path($d->image) : helper::food_image($d->name . ' stylist', $d->id, 'retail'); @endphp
            <a href="{{ $bookUrl }}?doctor={{ $d->id }}" class="staff">
              <div class="staff__img">
                <img src="{{ $dimg }}" alt="{{ $d->name }}" loading="lazy">
                @if ($d->fee > 0)<span class="staff__next">{{ helper::currency_formate($d->fee, $storeinfo->id) }}</span>@endif
              </div>
              <strong>{{ $d->name }}</strong>
              <span>{{ $d->specialty ?: __('Specialist') }}@if ($d->experience) · {{ $d->experience }}@endif</span>
              @if ($d->languages)<div class="staff__tags"><span class="meta-chip">{{ $d->languages }}</span></div>@endif
            </a>
          @endforeach
        </div>
      </div>
    </section>
    @endif

    <!-- STATS -->
    <section class="section-tight">
      <div class="container">
        <div class="stat-row reveal">
          <div class="stat"><b><span data-count="{{ max($docs->count(), 1) }}">0</span></b><span>{{ __('Specialists') }}</span></div>
          <div class="stat"><b><span data-count="{{ max($svcs->count(), 1) }}">0</span></b><span>{{ __('Services') }}</span></div>
          <div class="stat"><b><span data-count="24" data-suffix="/7">0</span></b><span>{{ __('Book anytime') }}</span></div>
          <div class="stat"><b><span data-count="4.9">0</span></b><span>{{ __('Client rating') }}</span></div>
        </div>
      </div>
    </section>

    @if ($reviews->count())
    <section class="section-tight">
      <div class="container">
        <div class="head-row"><div class="section-head" style="margin-bottom:0"><span class="eyebrow">{{ __('Kind words') }}</span><h2>{{ __('What clients say') }}</h2></div></div>
        <div class="grid g-3 reveal">
          @foreach ($reviews as $rv)
            <article class="panel" style="padding:24px">
              <span class="stars">@for ($s = 0; $s < (int) ($rv->star ?? 5); $s++)<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.9 21l1.2-6.8-5-4.9 6.9-1z"/></svg>@endfor</span>
              <p class="mt-2">"{{ \Illuminate\Support\Str::limit($rv->description, 170) }}"</p>
              <div class="who" style="display:flex;align-items:center;gap:10px;margin-top:12px">@if (!empty($rv->image))<img src="{{ helper::image_path($rv->image) }}" alt="" style="width:40px;height:40px;border-radius:50%;object-fit:cover">@endif<div><strong>{{ $rv->name }}</strong><span class="muted small d-block">{{ __('Verified client') }}</span></div></div>
            </article>
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
              <span class="eyebrow">{{ __('Look and feel your best') }}</span>
              <h2 class="mt-2">{{ __('Book your appointment today.') }}</h2>
              <p>{{ __('Choose a treatment and a time — or message us on WhatsApp.') }}</p>
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
