@php
    // Shared storefront language toggle — flips between Arabic and English (and RTL/LTR).
    // Inherits the surrounding text colour so it fits every theme's header/topbar.
    $ocLoc = app()->getLocale();
    $ocNext = $ocLoc === 'ar' ? 'en' : 'ar';
    $ocNextLabel = $ocLoc === 'ar' ? 'English' : 'العربية';
@endphp
<a href="{{ url('lang/change?lang=' . $ocNext) }}" class="oc-lang" title="{{ $ocNextLabel }}" aria-label="{{ $ocNextLabel }}"
    style="display:inline-flex;align-items:center;gap:6px;color:inherit;text-decoration:none;font-weight:600;font-size:13px;white-space:nowrap;line-height:1;">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <circle cx="12" cy="12" r="9"/><path d="M3 12h18"/><path d="M12 3c2.5 2.7 3.8 6 3.8 9s-1.3 6.3-3.8 9c-2.5-2.7-3.8-6-3.8-9S9.5 5.7 12 3z"/>
    </svg>
    <span>{{ $ocNextLabel }}</span>
</a>
