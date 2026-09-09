<div class="mobile-nav" id="mobileNav">
  <div class="mobile-nav__scrim" data-close></div>
  <div class="mobile-nav__panel">
    <div class="mobile-nav__top">
      <span class="brand">
        @if (!empty($tLogo))<span class="brand-mark" style="overflow:hidden"><img src="{{ helper::image_path($tLogo) }}" alt="{{ $tName }}" style="width:100%;height:100%;object-fit:cover"></span>
        @else<span class="brand-mark"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21V9l9-6 9 6v12"/><path d="M9 21v-6h6v6"/><path d="M3 13h18"/></svg></span>@endif
        @if (!empty($tName))<span class="brand-name">{{ $tName }}</span>@endif
      </span>
      <button class="modal__close" data-close aria-label="Close"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
    </div>
    <a href="{{ $tBase }}" class="mlink {{ $tActive == 'home' ? 'active' : '' }}">{{ __('Home') }}</a>
    <a href="{{ URL::to($tSlug . '/booking') }}" class="mlink {{ $tActive == 'menu' ? 'active' : '' }}">{{ __('Book appointment') }}</a>
    <a href="{{ URL::to($tSlug . '/aboutus') }}" class="mlink {{ $tActive == 'about' ? 'active' : '' }}">{{ __('About') }}</a>
    <a href="{{ URL::to($tSlug . '/contact') }}" class="mlink {{ $tActive == 'contact' ? 'active' : '' }}">{{ __('Contact') }}</a>
    <a href="{{ URL::to($tSlug . '/faqshow') }}" class="mlink {{ $tActive == 'faqs' ? 'active' : '' }}">{{ __('Help') }}</a>
    <div class="mobile-nav__foot">
      <a href="{{ URL::to($tSlug . '/booking') }}" class="btn btn-primary btn-block">{{ __('Book appointment') }}</a>
      @if (!empty($tWa))<a href="https://wa.me/{{ $tWa }}" class="btn btn-ghost btn-block">{{ __('Message on WhatsApp') }}</a>@endif
    </div>
  </div>
</div>
