@php
    // Shared storefront language switch. Two languages: a one-tap toggle. More (English, Arabic,
    // German, French…): a small menu. Inherits the surrounding text colour so it fits every
    // theme's header / topbar; the open menu has its own light panel.
    $ocLoc = app()->getLocale();
    $ocNative = ['en' => 'English', 'ar' => 'العربية', 'de' => 'Deutsch', 'fr' => 'Français'];
    $ocLangs = \Illuminate\Support\Facades\Cache::remember('oc_site_languages', 600, function () {
        return \App\Models\Languages::where('is_available', 1)->orderBy('id')->get(['code', 'name'])
            ->map(fn($l) => ['code' => $l->code, 'name' => $l->name])->all();
    });
    $ocLangs = collect($ocLangs)->map(fn($l) => $l + ['label' => $ocNative[$l['code']] ?? $l['name']]);
    if ($ocLangs->count() < 2) {
        $ocLangs = collect([['code' => 'en', 'label' => 'English'], ['code' => 'ar', 'label' => 'العربية']]);
    }
    $ocCurrent = $ocLangs->firstWhere('code', $ocLoc) ?? $ocLangs->first();
    $ocGlobe = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18"/><path d="M12 3c2.5 2.7 3.8 6 3.8 9s-1.3 6.3-3.8 9c-2.5-2.7-3.8-6-3.8-9S9.5 5.7 12 3z"/></svg>';
@endphp
@if ($ocLangs->count() === 2)
    @php $ocNext = $ocLangs->firstWhere('code', '!=', $ocCurrent['code']) ?? $ocLangs->last(); @endphp
    <a href="{{ url('lang/change?lang=' . $ocNext['code']) }}" class="oc-lang" title="{{ $ocNext['label'] }}" aria-label="{{ $ocNext['label'] }}"
        style="display:inline-flex;align-items:center;gap:6px;color:inherit;text-decoration:none;font-weight:600;font-size:13px;white-space:nowrap;line-height:1;">
        {!! $ocGlobe !!}<span>{{ $ocNext['label'] }}</span>
    </a>
@else
    <style>
        .oc-langmenu { position: relative; display: inline-flex; }
        .oc-langmenu > summary { list-style: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; color: inherit;
            font-weight: 600; font-size: 13px; white-space: nowrap; line-height: 1; }
        .oc-langmenu > summary::-webkit-details-marker { display: none; }
        .oc-langmenu > summary::after { content: ""; width: 6px; height: 6px; border: solid currentColor; border-width: 0 1.6px 1.6px 0;
            transform: rotate(45deg) translateY(-2px); opacity: .7; }
        .oc-langmenu__list { position: absolute; top: calc(100% + 12px); inset-inline-end: -10px; z-index: 300; min-width: 150px;
            background: #fff; color: #14181b; border-radius: 12px; padding: 6px; box-shadow: 0 14px 34px -12px rgba(0,0,0,.35); border: 1px solid rgba(0,0,0,.06); }
        .oc-langmenu__list a { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 9px 12px;
            border-radius: 8px; color: #14181b !important; text-decoration: none; font-size: 14px; font-weight: 600; }
        .oc-langmenu__list a:hover { background: #f2f4f5; }
        .oc-langmenu__list a.is-on { color: var(--brand, #1f9d55) !important; }
    </style>
    <details class="oc-lang oc-langmenu">
        <summary aria-label="{{ __('Language') }}">{!! $ocGlobe !!}<span>{{ $ocCurrent['label'] }}</span></summary>
        <div class="oc-langmenu__list">
            @foreach ($ocLangs as $l)
                <a href="{{ url('lang/change?lang=' . $l['code']) }}" class="{{ $l['code'] === $ocCurrent['code'] ? 'is-on' : '' }}" lang="{{ $l['code'] }}">
                    <span>{{ $l['label'] }}</span>@if ($l['code'] === $ocCurrent['code'])<span>✓</span>@endif
                </a>
            @endforeach
        </div>
    </details>
    <script>
        // Close the language menu when tapping anywhere else.
        document.addEventListener('click', function (e) {
            document.querySelectorAll('details.oc-langmenu[open]').forEach(function (d) { if (!d.contains(e.target)) d.removeAttribute('open'); });
        });
    </script>
@endif
