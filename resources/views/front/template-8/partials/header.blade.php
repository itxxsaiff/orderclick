<header class="site-header">
  <div class="container header-inner">
    <a href="{{ $tBase }}" class="brand">
      @if (!empty($tLogo))
        <span class="brand-mark" style="overflow:hidden"><img src="{{ helper::image_path($tLogo) }}" alt="{{ $tName }}" style="width:100%;height:100%;object-fit:cover"></span>
      @else
        <span class="brand-mark"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21V9l9-6 9 6v12"/><path d="M9 21v-6h6v6"/><path d="M3 13h18"/></svg></span>
      @endif
      @if (!empty($tName))<span class="brand-name">{{ $tName }}@if (!empty($tApp->tag_line))<small>{{ $tApp->tag_line }}</small>@endif</span>@endif
    </a>
    <nav class="nav">
      <a href="{{ $tBase }}" class="{{ $tActive == 'home' ? 'active' : '' }}">{{ __('Home') }}</a>
      <a href="{{ URL::to($tSlug . '/booking') }}" class="{{ $tActive == 'menu' ? 'active' : '' }}">{{ __('Book now') }}</a>
      <a href="{{ URL::to($tSlug . '/aboutus') }}" class="{{ $tActive == 'about' ? 'active' : '' }}">{{ __('About') }}</a>
      <a href="{{ URL::to($tSlug . '/contact') }}" class="{{ $tActive == 'contact' ? 'active' : '' }}">{{ __('Contact') }}</a>
      <a href="{{ URL::to($tSlug . '/faqshow') }}" class="{{ $tActive == 'faqs' ? 'active' : '' }}">{{ __('Help') }}</a>
    </nav>
    <div class="header-actions">
      <button class="icon-btn" data-open="searchModal" aria-label="Search"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg></button>
      <button class="icon-btn" data-theme-toggle aria-label="Toggle dark mode"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 3a9 9 0 100 18 9 9 0 000-18z"/><path d="M12 3v18" fill="currentColor"/></svg></button>
      @if (!empty($tWa))<a href="https://wa.me/{{ $tWa }}" class="btn btn-primary btn-sm hide-sm"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 00-8.6 15L2 22l5.2-1.4A10 10 0 1012 2z"/></svg> {{ __('Book on WhatsApp') }}</a>@endif
      <button class="icon-btn burger" data-open="mobileNav" aria-label="Open menu"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h10"/></svg></button>
    </div>
  </div>
</header>
