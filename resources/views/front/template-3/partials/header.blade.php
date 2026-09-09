<header class="site-header">
  <div class="container header-inner">
    <a href="{{ $tBase }}" class="brand">
      <span class="brand-mark">
        @if (!empty($tLogo))
          <img src="{{ helper::image_path($tLogo) }}" alt="{{ $tName }}" style="width:100%;height:100%;object-fit:cover;border-radius:inherit">
        @else
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 2v7c0 1.7 1.3 3 3 3s3-1.3 3-3V2M6 12v10M18 2c-2 0-3 2.5-3 5.5S16 13 18 13s3-2 3-5.5S20 2 18 2zM18 13v9"/></svg>
        @endif
      </span>
      @if (!empty($tName))<span>{{ $tName }}@if (!empty($tApp->tag_line))<small>{{ $tApp->tag_line }}</small>@endif</span>@endif
    </a>

    <nav class="nav">
      <a href="{{ $tBase }}" class="{{ $tActive == 'home' ? 'active' : '' }}">{{ __('Home') }}</a>
      <a href="{{ URL::to($tSlug . '/categories') }}" class="{{ $tActive == 'menu' ? 'active' : '' }}">{{ __('Menu') }}</a>
      <a href="{{ URL::to($tSlug . '/aboutus') }}" class="{{ $tActive == 'about' ? 'active' : '' }}">{{ __('About us') }}</a>
      <a href="{{ URL::to($tSlug . '/contact') }}" class="{{ $tActive == 'contact' ? 'active' : '' }}">{{ __('Contact') }}</a>
      <a href="{{ URL::to($tSlug . '/faqshow') }}" class="{{ $tActive == 'faqs' ? 'active' : '' }}">{{ __('FAQs') }}</a>
    </nav>

    <div class="header-actions">
      <span class="icon-btn" style="width:auto;padding:0 10px;gap:6px;">@include('front.partials.lang_switch')</span>
      <button class="icon-btn" data-open="searchModal" aria-label="Search menu">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
      </button>
      <button class="icon-btn cart-btn" data-open="cartDrawer" aria-label="Open cart">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 4h2l2.4 11.2a2 2 0 002 1.6h7.7a2 2 0 002-1.6L21 8H6"/><circle cx="10" cy="20" r="1.4"/><circle cx="18" cy="20" r="1.4"/></svg>
        <span class="cart-count" @if (!$tCount) style="display:none" @endif>{{ $tCount }}</span>
      </button>
      <button class="icon-btn" data-theme-toggle aria-label="Toggle dark mode">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 3a9 9 0 100 18 9 9 0 000-18z"/><path d="M12 3v18" fill="currentColor"/></svg>
      </button>
      <a href="{{ URL::to($tSlug . '/categories') }}" class="btn btn-primary btn-sm hide-sm">{{ __('Order now') }}</a>
      <button class="icon-btn burger" data-open="mobileNav" aria-label="Open menu">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h10"/></svg>
      </button>
    </div>
  </div>
</header>
