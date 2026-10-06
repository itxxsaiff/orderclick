<div class="mobile-nav" id="mobileNav">
  <div class="mobile-nav__scrim" data-close></div>
  <div class="mobile-nav__panel">
    <div class="mobile-nav__top">
      <span class="brand">
        <span class="brand-mark">
          @if (!empty($tLogo))
            <img src="{{ helper::image_path($tLogo) }}" alt="{{ $tName }}" style="width:100%;height:100%;object-fit:cover;border-radius:inherit">
          @else
            {{ mb_strtoupper(mb_substr($tName ?: 'S', 0, 1)) }}
          @endif
        </span>
        @if (!empty($tName)) {{ $tName }} @endif
      </span>
      <button class="modal__close" data-close aria-label="{{ __('Close') }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
      </button>
    </div>
    @foreach ($tNav as $i => $link)
      <a href="{{ $link['url'] }}" class="mlink {{ $tActive == $link['key'] ? 'active' : '' }}">{{ $link['label'] }} <span>{{ sprintf('%02d', $i + 1) }}</span></a>
    @endforeach
    @if ($tFlow === 'orders')
      <a href="{{ URL::to($tSlug . '/cart') }}" class="mlink {{ $tActive == 'cart' ? 'active' : '' }}">{{ __('My cart') }} <span>{{ sprintf('%02d', count($tNav) + 1) }}</span></a>
    @endif
    <div class="mobile-nav__foot">
      <a href="{{ $tCta['url'] }}" class="btn btn-primary btn-block">{{ $tCta['label'] }}</a>
      @if (!empty($tPhone))
        <a href="tel:{{ $tPhone }}" class="btn btn-ghost btn-block">{{ $tPhone }}</a>
      @endif
    </div>
  </div>
</div>
