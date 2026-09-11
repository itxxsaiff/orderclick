{{--
    Order Click platform Google Analytics (GA4). The Measurement ID is the platform's
    settings.tracking_id (vendor 1). Included immediately after <head> on every public page:
    landing, marketplace, blogs, registration, login and the storefronts.
    Nothing is output unless the ID looks like a GA4 ID, so an empty or odd value is harmless.
--}}
@php $ocGaId = strtoupper(trim((string) (helper::appdata(1)->tracking_id ?? ''))); @endphp
@if (preg_match('/^G-[A-Z0-9]{6,}$/', $ocGaId))
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ocGaId }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', '{{ $ocGaId }}');
    </script>
@endif
