<div class="mobile-nav" id="mobileNav">
  <div class="mobile-nav__scrim" data-close></div>
  <div class="mobile-nav__panel">
    <div class="mobile-nav__top">
      <span class="brand">
        @if (!empty($tLogo))
          <span class="brand-mark" style="overflow:hidden"><img src="{{ helper::image_path($tLogo) }}" alt="{{ $tName }}" style="width:100%;height:100%;object-fit:cover"></span>
        @else
          <span class="brand-mark"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 8h16l-1.6 11.2a2 2 0 01-2 1.8H7.6a2 2 0 01-2-1.8z"/><path d="M9 8V6a3 3 0 016 0v2"/></svg></span>
        @endif
        @if (!empty($tName))<span class="brand-name">{{ $tName }}</span>@endif
      </span>
      <button class="modal__close" data-close aria-label="Close"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
    </div>
    <a href="{{ $tBase }}" class="mlink {{ $tActive == 'home' ? 'active' : '' }}">{{ __('Home') }}</a>
    <a href="{{ URL::to($tSlug . '/categories') }}" class="mlink {{ $tActive == 'menu' ? 'active' : '' }}">{{ __('Shop') }}</a>
    <a href="{{ URL::to($tSlug . '/aboutus') }}" class="mlink {{ $tActive == 'about' ? 'active' : '' }}">{{ __('About') }}</a>
    <a href="{{ URL::to($tSlug . '/contact') }}" class="mlink {{ $tActive == 'contact' ? 'active' : '' }}">{{ __('Contact') }}</a>
    <a href="{{ URL::to($tSlug . '/faqshow') }}" class="mlink {{ $tActive == 'faqs' ? 'active' : '' }}">{{ __('Help') }}</a>
    <a href="{{ URL::to($tSlug . '/cart') }}" class="mlink {{ $tActive == 'cart' ? 'active' : '' }}">{{ __('My basket') }}</a>
    <div class="mobile-nav__foot">
      <a href="{{ URL::to($tSlug . '/categories') }}" class="btn btn-primary btn-block">{{ __('Shop the offers') }}</a>
      @if (!empty($tWa))<a href="https://wa.me/{{ $tWa }}" class="btn btn-ghost btn-block">{{ __('Order on WhatsApp') }}</a>@endif
    </div>
  </div>
</div>
