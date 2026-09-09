<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <span class="brand">
          <span class="brand-mark">
            @if (!empty($tLogo))
              <img src="{{ helper::image_path($tLogo) }}" alt="{{ $tName }}" style="width:100%;height:100%;object-fit:cover;border-radius:inherit">
            @else
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 2v7c0 1.7 1.3 3 3 3s3-1.3 3-3V2M6 12v10M18 2c-2 0-3 2.5-3 5.5S16 13 18 13s3-2 3-5.5S20 2 18 2zM18 13v9"/></svg>
            @endif
          </span>
          @if (!empty($tName))<span>{{ $tName }}@if (!empty($tApp->tag_line))<small>{{ $tApp->tag_line }}</small>@endif</span>@endif
        </span>
        @if (!empty($tDesc))<p>{{ \Illuminate\Support\Str::limit(strip_tags($tDesc), 160) }}</p>@endif
        <div class="socials">
          @if (!empty($tApp->instagram))<a href="{{ $tApp->instagram }}" target="_blank" rel="noopener" aria-label="Instagram"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/></svg></a>@endif
          @if (!empty($tApp->facebook))<a href="{{ $tApp->facebook }}" target="_blank" rel="noopener" aria-label="Facebook"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M14 9h3V6h-3c-2.2 0-4 1.8-4 4v2H8v3h2v7h3v-7h3l1-3h-4v-2c0-.6.4-1 1-1z"/></svg></a>@endif
          @if (!empty($tApp->tiktok))<a href="{{ $tApp->tiktok }}" target="_blank" rel="noopener" aria-label="TikTok"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 3c.4 2.3 1.9 3.9 4 4.2v3c-1.5.1-2.9-.3-4.2-1.1v6.3a5.9 5.9 0 11-5.9-5.9c.3 0 .6 0 .9.1v3.1a2.9 2.9 0 102 2.8V3z"/></svg></a>@endif
          @if (!empty($tWa))<a href="https://wa.me/{{ $tWa }}" target="_blank" rel="noopener" aria-label="WhatsApp"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 00-8.6 15L2 22l5.2-1.4A10 10 0 1012 2zm5.5 12.4c-.3-.2-1.7-.9-2-1s-.5-.1-.7.1-.7.9-.9 1.1-.4.2-.7.1a8.2 8.2 0 01-2.4-1.5 9 9 0 01-1.7-2.1c-.2-.3 0-.5.1-.6l.5-.6.3-.5v-.5l-.9-2.2c-.3-.6-.5-.5-.7-.5h-.6a1.2 1.2 0 00-.8.4A3.4 3.4 0 005 8.8a5.9 5.9 0 001.3 3.2 13.5 13.5 0 005.2 4.6 17 17 0 001.7.6 4.2 4.2 0 001.9.1 3.1 3.1 0 002-1.4 2.5 2.5 0 00.2-1.4c-.1-.1-.3-.2-.6-.3z"/></svg></a>@endif
        </div>
      </div>

      <div>
        <h5>{{ __('Explore') }}</h5>
        <ul class="footer-links">
          <li><a href="{{ $tBase }}">{{ __('Home') }}</a></li>
          <li><a href="{{ URL::to($tSlug . '/categories') }}">{{ __('Full menu') }}</a></li>
          <li><a href="{{ URL::to($tSlug . '/aboutus') }}">{{ __('About us') }}</a></li>
          <li><a href="{{ URL::to($tSlug . '/contact') }}">{{ __('Contact') }}</a></li>
          <li><a href="{{ URL::to($tSlug . '/cart') }}">{{ __('My cart') }}</a></li>
        </ul>
      </div>

      <div>
        <h5>{{ __('Support') }}</h5>
        <ul class="footer-links">
          <li><a href="{{ URL::to($tSlug . '/faqshow') }}">{{ __('FAQs') }}</a></li>
          <li><a href="{{ URL::to($tSlug . '/terms_condition') }}">{{ __('Terms & Conditions') }}</a></li>
          <li><a href="{{ URL::to($tSlug . '/privacypolicy') }}">{{ __('Privacy Policy') }}</a></li>
          <li><a href="{{ URL::to($tSlug . '/contact') }}">{{ __('Track my order') }}</a></li>
        </ul>
      </div>

      <div>
        <h5>{{ __('Get in touch') }}</h5>
        <ul class="footer-contact">
          @if (!empty($tAddr))<li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s7-5.6 7-11a7 7 0 10-14 0c0 5.4 7 11 7 11z"/><circle cx="12" cy="10" r="2.6"/></svg><span>{{ $tAddr }}</span></li>@endif
          @if (!empty($tPhone))<li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3 19.5 19.5 0 01-6-6 19.8 19.8 0 01-3-8.7A2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/></svg><a href="tel:{{ $tPhone }}">{{ $tPhone }}</a></li>@endif
          @if (!empty($tEmail))<li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2.5" y="4.5" width="19" height="15" rx="2.5"/><path d="M3 7l9 6 9-6"/></svg><a href="mailto:{{ $tEmail }}">{{ $tEmail }}</a></li>@endif
        </ul>
      </div>
    </div>

    <div class="footer-bottom">
      <span>© {{ date('Y') }} {{ $tName }}. {{ __('Powered by Order Click') }}.</span>
      <nav>
        <a href="{{ URL::to($tSlug . '/terms_condition') }}">{{ __('Terms') }}</a>
        <a href="{{ URL::to($tSlug . '/privacypolicy') }}">{{ __('Privacy') }}</a>
      </nav>
    </div>
  </div>
</footer>
