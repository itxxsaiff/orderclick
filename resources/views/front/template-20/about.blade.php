@php $tActive = 'about'; @endphp
@extends('front.template-20.layout')

@section('content')
    @php
        $aboutContent = optional($aboutus)->about_content;
        $aboutImg = !empty(optional($tApp)->cover_image) ? helper::image_path($tApp->cover_image)
            : \App\Services\StoreDesign::stockImage($tName . (($tFlow ?? 'orders') === 'orders' ? ' restaurant interior' : ''), $storeinfo->id, $storeinfo->id ?? ($vdata ?? null));
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
              {{-- Main action follows the business: menu for stores, booking / service request otherwise. --}}
              @if (($tFlow ?? 'orders') === 'booking')
                <a href="{{ URL::to($tSlug . '/booking') }}" class="btn btn-dark btn-lg">{{ trans('labels.book_now') }}</a>
              @elseif (($tFlow ?? 'orders') === 'service')
                <a href="{{ URL::to($tSlug . '/service') }}" class="btn btn-dark btn-lg">{{ trans('labels.request_a_service') }}</a>
              @else
                <a href="{{ URL::to($tSlug . '/categories') }}" class="btn btn-dark btn-lg">{{ __('See the menu') }}</a>
              @endif
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

    {{-- Same closing call-to-action as the home page: it follows the business (order / book / request). --}}
    @include('front.template-20.sections.cta')
@endsection
