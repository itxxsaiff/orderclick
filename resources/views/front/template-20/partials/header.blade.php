{{-- Layout variant (classic / centered / minimal / bold) is switched by the body class from the design plan. --}}
<header class="site-header">
  <div class="container header-inner">
    <a href="{{ $tBase }}" class="brand">
      <span class="brand-mark">
        @if (!empty($tLogo))
          <img src="{{ helper::image_path($tLogo) }}" alt="{{ $tName }}" style="width:100%;height:100%;object-fit:cover;border-radius:inherit">
        @else
          {{ mb_strtoupper(mb_substr($tName ?: 'S', 0, 1)) }}
        @endif
      </span>
      @if (!empty($tName))<span>{{ $tName }}@if (!empty($tApp->tag_line))<small>{{ $tApp->tag_line }}</small>@endif</span>@endif
    </a>

    <nav class="nav">
      @foreach ($tNav as $link)
        <a href="{{ $link['url'] }}" class="{{ $tActive == $link['key'] ? 'active' : '' }}">{{ $link['label'] }}</a>
      @endforeach
    </nav>

    <div class="header-actions">
      <span class="icon-btn" style="width:auto;padding:0 10px;gap:6px;">@include('front.partials.lang_switch')</span>
      @if ($tFlow === 'orders')
        <button class="icon-btn" data-open="searchModal" aria-label="{{ __('Search') }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
        </button>
        <button class="icon-btn cart-btn" data-open="cartDrawer" aria-label="{{ __('My cart') }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 4h2l2.4 11.2a2 2 0 002 1.6h7.7a2 2 0 002-1.6L21 8H6"/><circle cx="10" cy="20" r="1.4"/><circle cx="18" cy="20" r="1.4"/></svg>
          <span class="cart-count" @if (!$tCount) style="display:none" @endif>{{ $tCount }}</span>
        </button>
      @endif
      <a href="{{ $tCta['url'] }}" class="btn btn-primary btn-sm hide-sm">{{ $tCta['label'] }}</a>
      <button class="icon-btn burger" data-open="mobileNav" aria-label="{{ __('Menu') }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h10"/></svg>
      </button>
    </div>
  </div>
</header>
