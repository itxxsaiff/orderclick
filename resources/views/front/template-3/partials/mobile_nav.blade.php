<div class="mobile-nav" id="mobileNav">
  <div class="mobile-nav__scrim" data-close></div>
  <div class="mobile-nav__panel">
    <div class="mobile-nav__top">
      <span class="brand">
        @if (!empty($tLogo))
          <span class="brand-mark"><img src="{{ helper::image_path($tLogo) }}" alt="{{ $tName }}" style="width:100%;height:100%;object-fit:cover;border-radius:inherit"></span>
        @elseif (!empty($tName))
          <span class="brand-mark">{{ strtoupper(substr($tName, 0, 1)) }}</span>
        @endif
        @if (!empty($tName)) {{ $tName }} @endif
      </span>
      <button class="modal__close" data-close aria-label="Close">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
      </button>
    </div>
    <a href="{{ $tBase }}" class="mlink {{ $tActive == 'home' ? 'active' : '' }}">{{ __('Home') }} <span>01</span></a>
    <a href="{{ URL::to($tSlug . '/categories') }}" class="mlink {{ $tActive == 'menu' ? 'active' : '' }}">{{ __('Menu') }} <span>02</span></a>
    <a href="{{ URL::to($tSlug . '/aboutus') }}" class="mlink {{ $tActive == 'about' ? 'active' : '' }}">{{ __('About us') }} <span>03</span></a>
    <a href="{{ URL::to($tSlug . '/contact') }}" class="mlink {{ $tActive == 'contact' ? 'active' : '' }}">{{ __('Contact') }} <span>04</span></a>
    <a href="{{ URL::to($tSlug . '/faqshow') }}" class="mlink {{ $tActive == 'faqs' ? 'active' : '' }}">{{ __('FAQs') }} <span>05</span></a>
    <a href="{{ URL::to($tSlug . '/cart') }}" class="mlink {{ $tActive == 'cart' ? 'active' : '' }}">{{ __('My cart') }} <span>06</span></a>
    <div class="mobile-nav__foot">
      <a href="{{ URL::to($tSlug . '/categories') }}" class="btn btn-primary btn-block">{{ __('Order now') }}</a>
      @if (!empty($tPhone))
        <a href="tel:{{ $tPhone }}" class="btn btn-ghost btn-block">{{ $tPhone }}</a>
      @endif
    </div>
  </div>
</div>
