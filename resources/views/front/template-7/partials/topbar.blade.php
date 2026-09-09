<div class="topbar">
  <div class="container">
    <span class="topbar-left"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 16V8a2 2 0 012-2h9v10H3zM14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/></svg> {{ __('Fast delivery to your door') }}</span>
    <span class="topbar-right">
      @include('front.partials.lang_switch')
      <a href="{{ URL::to($tSlug . '/categories') }}">{{ __("This week's offers") }}</a>
      <a href="{{ URL::to($tSlug . '/contact') }}">{{ __('Track order') }}</a>
      @if (!empty($tPhone))<a href="tel:{{ $tPhone }}">{{ $tPhone }}</a>@endif
    </span>
  </div>
</div>
