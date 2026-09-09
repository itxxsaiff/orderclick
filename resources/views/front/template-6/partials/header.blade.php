@php $megaCats = collect($tCats ?? [])->take(10); @endphp
<header class="site-header">
  <div class="container header-inner">
    <a href="{{ $tBase }}" class="brand">
      @if (!empty($tLogo))
        <span class="brand-mark" style="overflow:hidden"><img src="{{ helper::image_path($tLogo) }}" alt="{{ $tName }}" style="width:100%;height:100%;object-fit:cover"></span>
      @else
        <span class="brand-mark">{{ strtoupper(mb_substr($tName ?: 'S', 0, 1)) }}</span>
      @endif
      @if (!empty($tName))<span class="brand-name">{{ $tName }}@if (!empty($tApp->tag_line))<small>{{ $tApp->tag_line }}</small>@endif</span>@endif
    </a>
    <nav class="nav">
      <a href="{{ $tBase }}" class="{{ $tActive == 'home' ? 'active' : '' }}">{{ __('Home') }}</a>
      @if ($megaCats->count())
        <span class="has-mega">
          <a href="{{ URL::to($tSlug . '/categories') }}" class="{{ $tActive == 'menu' ? 'active' : '' }}">{{ __('Shop') }} <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M6 9l6 6 6-6"/></svg></a>
          <div class="mega">
            <div>
              <h6>{{ __('Categories') }}</h6>
              <ul>
                @foreach ($megaCats->take(5) as $c)
                  <li><a href="{{ URL::to($tSlug . '/categories') }}#cat-{{ $c->id }}">{{ $c->name }}</a></li>
                @endforeach
              </ul>
            </div>
            @if ($megaCats->count() > 5)
            <div>
              <h6>{{ __('More') }}</h6>
              <ul>
                @foreach ($megaCats->slice(5, 5) as $c)
                  <li><a href="{{ URL::to($tSlug . '/categories') }}#cat-{{ $c->id }}">{{ $c->name }}</a></li>
                @endforeach
              </ul>
            </div>
            @endif
            <a href="{{ URL::to($tSlug . '/categories') }}" class="mega-card">
              <img src="{{ !empty($megaCats->first()->image) ? helper::image_path($megaCats->first()->image) : helper::food_image($megaCats->first()->name, $megaCats->first()->id, 'retail') }}" alt="{{ $megaCats->first()->name }}">
              <span>{{ __('Shop everything') }} →</span>
            </a>
          </div>
        </span>
      @else
        <a href="{{ URL::to($tSlug . '/categories') }}" class="{{ $tActive == 'menu' ? 'active' : '' }}">{{ __('Shop') }}</a>
      @endif
      <a href="{{ URL::to($tSlug . '/aboutus') }}" class="{{ $tActive == 'about' ? 'active' : '' }}">{{ __('About') }}</a>
      <a href="{{ URL::to($tSlug . '/contact') }}" class="{{ $tActive == 'contact' ? 'active' : '' }}">{{ __('Contact') }}</a>
      <a href="{{ URL::to($tSlug . '/faqshow') }}" class="{{ $tActive == 'faqs' ? 'active' : '' }}">{{ __('FAQs') }}</a>
    </nav>
    <div class="header-actions">
      <span class="icon-btn" style="width:auto;padding:0 10px;gap:6px;">@include('front.partials.lang_switch')</span>
      <button class="icon-btn" data-open="searchModal" aria-label="Search"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg></button>
      <button class="icon-btn cart-btn" data-open="cartDrawer" aria-label="Open bag"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 8h14l1 12H4z"/><path d="M9 8V6a3 3 0 016 0v2"/></svg><span class="cart-count" @if (!$tCount) style="display:none" @endif>{{ $tCount }}</span></button>
      <button class="icon-btn" data-theme-toggle aria-label="Toggle dark mode"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3a9 9 0 100 18 9 9 0 000-18z"/><path d="M12 3v18" fill="currentColor"/></svg></button>
      <button class="icon-btn burger" data-open="mobileNav" aria-label="Open menu"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h10"/></svg></button>
    </div>
  </div>
</header>
