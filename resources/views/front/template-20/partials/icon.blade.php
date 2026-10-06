{{-- One of StoreDesign::ICONS, drawn as an inline SVG. --}}
@php
    $ocIcons = [
        'bolt'     => '<path d="M13 2L4.1 13.3a.6.6 0 00.5 1H11l-1 7.7 8.9-11.3a.6.6 0 00-.5-1H12z"/>',
        'lock'     => '<rect x="4" y="10" width="16" height="11" rx="2.5"/><path d="M8 10V7a4 4 0 018 0v3"/>',
        'whatsapp' => '<path d="M21 12a9 9 0 01-13.2 8L3 21l1.1-4.6A9 9 0 1121 12z"/><path d="M9 9.5c.5 2 2 3.6 4 4.3l1.2-1.1 2 .9-.5 1.6c-3.5.2-7.2-3.4-7-7l1.6-.5.9 2z"/>',
        'star'     => '<path d="M12 2l2.6 5.6 6.1.8-4.5 4.2 1.2 6-5.4-3-5.4 3 1.2-6L3.3 8.4l6.1-.8z"/>',
        'leaf'     => '<path d="M5 21c0-9 5-15 15-16-1 10-7 15-16 15"/><path d="M5 21l7-7"/>',
        'truck'    => '<path d="M3 16V8a2 2 0 012-2h9v10H3zM14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/>',
        'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'shield'   => '<path d="M12 3l8 3v6c0 5-3.4 8.4-8 9.6C7.4 20.4 4 17 4 12V6z"/><path d="M9 12l2 2 4-4"/>',
        'heart'    => '<path d="M20.8 8.6a5 5 0 00-8.8-3.2 5 5 0 00-8.8 3.2c0 5.4 8.8 11 8.8 11s8.8-5.6 8.8-11z"/>',
        'gift'     => '<rect x="3" y="8" width="18" height="13" rx="2"/><path d="M12 8v13M3 12h18M12 8S10 3 7.5 4.5 9 8 12 8zm0 0s2-5 4.5-3.5S15 8 12 8z"/>',
        'tag'      => '<path d="M20.6 13.4l-7.2 7.2a2 2 0 01-2.8 0L3 13V3h10l7.6 7.6a2 2 0 010 2.8z"/><circle cx="7.5" cy="7.5" r="1.5"/>',
        'phone'    => '<path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3 19.5 19.5 0 01-6-6 19.8 19.8 0 01-3-8.7A2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/>',
        'check'    => '<path d="M20 6L9 17l-5-5"/>',
        'sparkle'  => '<path d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8z"/><path d="M19 15l.8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8z"/>',
        'map'      => '<path d="M12 21s7-5.6 7-11a7 7 0 10-14 0c0 5.4 7 11 7 11z"/><circle cx="12" cy="10" r="2.6"/>',
        'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2.5"/><path d="M3 10h18M8 3v4M16 3v4"/>',
    ];
@endphp
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $ocIcons[$name] ?? $ocIcons['check'] !!}</svg>
