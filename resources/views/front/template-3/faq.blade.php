@php $tActive = 'faqs'; @endphp
@extends('front.template-3.layout')

@section('content')
    @php $faqs = collect($allfaqs ?? []); @endphp

    <section class="page-head">
      <div class="container">
        <div class="crumb">
          <a href="{{ $tBase }}">{{ __('Home') }}</a>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M9 6l6 6-6 6"/></svg>
          <span class="now">{{ __('FAQs') }}</span>
        </div>
        <div class="section-head">
          <span class="eyebrow">{{ __('Help centre') }}</span>
          <h1>{{ __('Questions we get asked a lot') }}</h1>
          <p>{{ __('Ordering, delivery, payment and more. If yours isn\'t here, message us and we\'ll answer it.') }}</p>
        </div>
      </div>
    </section>

    <section style="padding-bottom:clamp(40px,6vw,72px)">
      <div class="container container-sm">
        @if ($faqs->count())
          <div class="acc">
            @foreach ($faqs as $i => $f)
              <div class="acc-item {{ $i === 0 ? 'open' : '' }}">
                <button class="acc-head">{{ $f->question ?? $f->title ?? '' }} <span class="pm"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg></span></button>
                <div class="acc-body"><div><p>{!! nl2br(e($f->answer ?? $f->description ?? '')) !!}</p></div></div>
              </div>
            @endforeach
          </div>
        @else
          <div class="text-center muted" style="padding:40px 0">
            <p class="lead">{{ __('No FAQs have been added yet.') }}</p>
          </div>
        @endif

        @if (!empty($tWa))
        <div class="cta-band mt-4">
          <div class="cta-band__inner">
            <div>
              <h2>{{ __('Still stuck?') }}</h2>
              <p>{{ __('A real person answers our WhatsApp during every service.') }}</p>
            </div>
            <div class="flex gap-2 wrap">
              <a href="https://wa.me/{{ $tWa }}" class="btn btn-primary btn-lg">{{ __('Message us') }}</a>
              <a href="{{ URL::to($tSlug . '/contact') }}" class="btn btn-ghost btn-lg" style="color:#fff;border-color:rgba(255,255,255,.28)">{{ __('Contact form') }}</a>
            </div>
          </div>
        </div>
        @endif
      </div>
    </section>
@endsection
