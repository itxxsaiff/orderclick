{{-- Shared legal/content page shell. Expects: $legalTitle, $legalContent --}}
<section class="page-head">
  <div class="container">
    <div class="crumb">
      <a href="{{ $tBase }}">{{ __('Home') }}</a>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M9 6l6 6-6 6"/></svg>
      <span class="now">{{ $legalTitle }}</span>
    </div>
    <div class="section-head">
      <span class="eyebrow">{{ $tName }}</span>
      <h1>{{ $legalTitle }}</h1>
    </div>
  </div>
</section>

<section style="padding-bottom:clamp(40px,6vw,72px)">
  <div class="container container-sm">
    @if (!empty(trim(strip_tags((string) $legalContent))))
      <div class="prose">{!! $legalContent !!}</div>
    @else
      <div class="text-center muted" style="padding:40px 0"><p class="lead">{{ __('This page has not been set up yet.') }}</p></div>
    @endif
  </div>
</section>
