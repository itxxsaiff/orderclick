@php
    $T = fn($k, $f = '') => \App\Services\StoreDesign::text($tDesign, $k, $f);
    $ocAbout = $T('about_text', \Illuminate\Support\Str::limit(strip_tags((string) $tDesc), 600));
    $ocImg = !empty(optional($tApp)->cover_image) ? helper::image_path($tApp->cover_image) : null;
@endphp
@if ($ocAbout !== '')
<section class="section oce-about">
  <div class="container oce-about-grid {{ $ocImg ? '' : 'oce-about-solo' }}">
    <div class="section-head reveal" style="margin-bottom:0">
      <h2>{{ $T('about_title', trans('labels.eng_about_title')) }}</h2>
      <p>{{ $ocAbout }}</p>
      <a href="{{ URL::to($tSlug . '/aboutus') }}" class="link-arrow mt-2">{{ __('Read more') }}
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
      </a>
    </div>
    @if ($ocImg)<div class="hero-media oce-about-media reveal"><img src="{{ $ocImg }}" alt="{{ $tName }}" loading="lazy"></div>@endif
  </div>
</section>
@endif
