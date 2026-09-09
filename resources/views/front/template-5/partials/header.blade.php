<header class="site-header">
  <div class="container header-inner">
    <button class="icon-btn burger" data-open="mobileNav" aria-label="Open menu"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h10"/></svg></button>
    <a href="{{ $tBase }}" class="brand">
      @if (!empty($tLogo))
        <span class="brand-mark" style="overflow:hidden"><img src="{{ helper::image_path($tLogo) }}" alt="{{ $tName }}" style="width:100%;height:100%;object-fit:cover"></span>
      @else
        <span class="brand-mark"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 8h16l-1.6 11.2a2 2 0 01-2 1.8H7.6a2 2 0 01-2-1.8z"/><path d="M9 8V6a3 3 0 016 0v2"/><path d="M9.5 13.5h5M12 11v5"/></svg></span>
      @endif
      @if (!empty($tName))<span class="brand-name">{{ $tName }}@if (!empty($tApp->tag_line))<small>{{ $tApp->tag_line }}</small>@endif</span>@endif
    </a>
    <div class="header-search">
      <svg class="s-ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
      <input type="search" placeholder="{{ __('Search products…') }}" data-open="searchModal" readonly>
      <button class="s-btn" data-open="searchModal" aria-label="Search"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg></button>
    </div>
    <div class="header-actions">
      <button class="icon-btn" data-theme-toggle aria-label="Toggle dark mode"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 3a9 9 0 100 18 9 9 0 000-18z"/><path d="M12 3v18" fill="currentColor"/></svg></button>
      <button class="icon-btn cart-btn" data-open="cartDrawer" aria-label="Open basket"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9h18l-1.7 10.2a2 2 0 01-2 1.8H6.7a2 2 0 01-2-1.8z"/><path d="M8 9l3-6M16 9l-3-6"/></svg><span class="cart-count" @if (!$tCount) style="display:none" @endif>{{ $tCount }}</span></button>
    </div>
  </div>
</header>
