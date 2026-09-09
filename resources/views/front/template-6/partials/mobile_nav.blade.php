<div class="mobile-nav" id="mobileNav">
  <div class="mobile-nav__scrim" data-close></div>
  <div class="mobile-nav__panel">
    <div class="mobile-nav__top">
      <span class="brand">
        @if (!empty($tLogo))
          <span class="brand-mark" style="overflow:hidden"><img src="{{ helper::image_path($tLogo) }}" alt="{{ $tName }}" style="width:100%;height:100%;object-fit:cover"></span>
        @else
          <span class="brand-mark">{{ strtoupper(mb_substr($tName ?: 'S', 0, 1)) }}</span>
        @endif
        @if (!empty($tName))<span class="brand-name">{{ $tName }}</span>@endif
      </span>
      <button class="modal__close" data-close aria-label="Close"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
    </div>
    <a href="{{ $tBase }}" class="mlink {{ $tActive == 'home' ? 'active' : '' }}">{{ __('Home') }} <span>01</span></a>
    <a href="{{ URL::to($tSlug . '/categories') }}" class="mlink {{ $tActive == 'menu' ? 'active' : '' }}">{{ __('Shop') }} <span>02</span></a>
    <a href="{{ URL::to($tSlug . '/aboutus') }}" class="mlink {{ $tActive == 'about' ? 'active' : '' }}">{{ __('About') }} <span>03</span></a>
    <a href="{{ URL::to($tSlug . '/contact') }}" class="mlink {{ $tActive == 'contact' ? 'active' : '' }}">{{ __('Contact') }} <span>04</span></a>
    <a href="{{ URL::to($tSlug . '/faqshow') }}" class="mlink {{ $tActive == 'faqs' ? 'active' : '' }}">{{ __('FAQs') }} <span>05</span></a>
    <a href="{{ URL::to($tSlug . '/cart') }}" class="mlink {{ $tActive == 'cart' ? 'active' : '' }}">{{ __('Shopping bag') }} <span>06</span></a>
    <div class="mobile-nav__foot">
      <a href="{{ URL::to($tSlug . '/categories') }}" class="btn btn-primary btn-block">{{ __('Shop new arrivals') }}</a>
      @if (!empty($tWa))
        <a href="https://wa.me/{{ $tWa }}" class="btn btn-ghost btn-block">{{ __('Order on WhatsApp') }}</a>
      @endif
    </div>
  </div>
</div>
