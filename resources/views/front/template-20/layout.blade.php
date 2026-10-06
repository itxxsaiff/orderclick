@php
    // AI design engine. Branding ($tName, $tSlug, …) and the store's design plan ($tDesign,
    // $tFlow) come from the AppServiceProvider view composer, so they are ready for both this
    // layout and the eagerly captured @section content.
    $tActive = $tActive ?? 'home';
    $tCss = env('ASSETSPATHURL') . 'web-assets/templates/restaurant/';
    $tDesign = $tDesign ?? \App\Services\StoreDesign::for($storeinfo->id ?? 0);
    $tFlow = $tFlow ?? $tDesign['flow'];
    $ocRtl = session()->get('direction') == 2;
@endphp
<!DOCTYPE html>
<html lang="{{ session()->get('locale', app()->getLocale()) }}" data-theme="{{ $tDesign['mode'] }}"
    dir="{{ $ocRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    @include('partials.google_tag')
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags(\App\Services\StoreDesign::text($tDesign, 'hero_text', (string) $tDesc)), 160) }}">
    <meta name="theme-color" content="{{ $tDesign['palette']['brand'] }}">
    <title>{{ $tName ?: \Illuminate\Support\Str::title(str_replace('-', ' ', $tSlug)) }}</title>
    <link rel="icon" href="{{ helper::image_path($tApp->favicon) }}" type="image" sizes="16x16">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="{{ \App\Services\StoreDesign::fontUrl($tDesign) }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url($tCss . 'css/style.css') }}">
    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'web-assets/engine/engine.css') }}?v=1">
    {{-- The design plan: these tokens restyle every page of the engine. --}}
    <style>html[data-theme], :root { {!! \App\Services\StoreDesign::cssVars($tDesign) !!} }</style>
    @include('front.partials.responsive_fix')
    @yield('styles')
</head>
<body class="{{ \App\Services\StoreDesign::bodyClasses($tDesign) }}">
    @if (\App\Services\StoreDesign::previewing($storeinfo->id))
        <div class="oce-preview-bar">{{ trans('labels.design_preview_bar') }}</div>
    @endif
    @include('front.template-20.partials.header')
    @include('front.template-20.partials.mobile_nav')

    <main>
        @yield('content')
    </main>

    @include('front.template-20.partials.footer')
    @include('front.template-20.partials.search_modal')
    @if ($tFlow === 'orders')
        @include('front.template-20.partials.cart_drawer')
    @endif

    <div class="toast-wrap"></div>

    <script>
        window.OC = {
            base: "{{ $tBase }}",
            vendor: "{{ $storeinfo->id }}",
            token: "{{ csrf_token() }}",
            addUrl: "{{ URL::to('add-to-cart') }}",
            qtyUrl: "{{ URL::to('cart/qtyupdate') }}",
            delUrl: "{{ URL::to('cart/deletecartitem') }}",
            fragUrl: "{{ URL::to($tSlug . '/cart-fragment') }}",
            checkoutUrl: "{{ URL::to($tSlug . '/checkout') }}"
        };
    </script>
    <script src="{{ url($tCss . 'js/main.js') }}"></script>
    {{-- The design decides light/dark; ignore a preference saved by another store. --}}
    <script>document.documentElement.setAttribute('data-theme', @json($tDesign['mode']));</script>
    @if ($tFlow === 'orders')
        <script src="{{ url($tCss . 'js/oc-cart.js') }}"></script>
    @endif
    @yield('scripts')
    @include('front.partials.ai_assistant')
</body>
</html>
