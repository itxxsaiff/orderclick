@php $tActive = 'about'; @endphp
@extends('front.template-5.layout')

@section('content')
    @php
        $aboutContent = optional($aboutus)->about_content;
        $aboutImg = helper::food_image($tName . ' store interior', $storeinfo->id, 'food');
    @endphp

    <section class="page-head">
      <div class="container">
        <div class="crumb">
          <a href="{{ $tBase }}">{{ __('Home') }}</a>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M9 6l6 6-6 6"/></svg>
          <span class="now">{{ __('About us') }}</span>
        </div>
        <div class="split" style="align-items:center">
          <div>
            <span class="eyebrow">{{ __('Our story') }}</span>
            <h1 class="mt-1">{{ $tName }}</h1>
            @if (!empty($tDesc))<p class="lead mt-2">{{ \Illuminate\Support\Str::limit(strip_tags($tDesc), 240) }}</p>@endif
            <div class="flex gap-2 wrap mt-3">
              <a href="{{ URL::to($tSlug . '/categories') }}" class="btn btn-dark btn-lg">{{ __('Shop the collection') }}</a>
              <a href="{{ URL::to($tSlug . '/contact') }}" class="btn btn-ghost btn-lg">{{ __('Visit us') }}</a>
            </div>
          </div>
          <div class="media-frame">
            <img src="{{ $aboutImg }}" alt="{{ $tName }}">
          </div>
        </div>
      </div>
    </section>

    @if (!empty(trim(strip_tags((string) $aboutContent))))
    <section class="section">
      <div class="container container-sm">
        <div class="prose">
          {!! $aboutContent !!}
        </div>
      </div>
    </section>
    @endif

    <section class="section-tight">
      <div class="container">
        <div class="cta-band">
          <div class="cta-band__inner">
            <div>
              <h2>{{ __('Come and see the collection.') }}</h2>
              <p>{{ __('Browse the collection and order in a few taps — or send it straight to WhatsApp.') }}</p>
            </div>
            <div class="flex gap-2 wrap">
              <a href="{{ URL::to($tSlug . '/categories') }}" class="btn btn-primary btn-lg">{{ __('Order now') }}</a>
              @if (!empty($tWa))
                <a href="https://wa.me/{{ $tWa }}" class="btn btn-ghost btn-lg" style="color:#fff;border-color:rgba(255,255,255,.28)">{{ __('WhatsApp') }}</a>
              @endif
            </div>
          </div>
        </div>
      </div>
    </section>
@endsection
