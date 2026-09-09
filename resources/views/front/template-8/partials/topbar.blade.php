<div class="topbar">
  <div class="container">
    <span class="topbar-left"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l8 3v6c0 5-3.4 8.4-8 9.6C7.4 20.4 4 17 4 12V6z"/><path d="M9 12l2 2 4-4"/></svg> {{ __('Easy booking · Trusted service') }}</span>
    <span class="topbar-right">
      @include('front.partials.lang_switch')
      @if (!empty($tWa))<a href="https://wa.me/{{ $tWa }}">{{ __('Book on WhatsApp') }}</a>@endif
      <a href="{{ URL::to($tSlug . '/contact') }}">{{ __('Help centre') }}</a>
      @if (!empty($tPhone))<a href="tel:{{ $tPhone }}">{{ $tPhone }}</a>@endif
    </span>
  </div>
</div>
