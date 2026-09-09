@php
    // Branding ($tName, $tSlug, $tCss, …) is supplied by the AppServiceProvider view
    // composer for template-3/4 so it is available to both the layout and the eagerly
    // captured @section content. Fallbacks here keep the layout renderable in isolation.
    $tActive = $tActive ?? 'home';
    $tCss = $tCss ?? env('ASSETSPATHURL') . 'web-assets/templates/restaurant/';
@endphp
<!DOCTYPE html>
<html lang="{{ session()->get('locale', app()->getLocale()) }}" data-theme="light"
    dir="{{ session()->get('direction') == 2 ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $tDesc }}">
    <title>{{ $tName ?: \Illuminate\Support\Str::title(str_replace('-', ' ', $tSlug)) }}</title>
    <link rel="icon" href="{{ helper::image_path($tApp->favicon) }}" type="image" sizes="16x16">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ url($tCss . 'css/style.css') }}">
    @include('front.partials.responsive_fix')
    <style>
      /* Keep the modal scrollbar inside the rounded corners (was poking out at the top). */
      .modal__dialog { overflow: hidden; display: flex; flex-direction: column; }
      .modal__body { overflow-y: auto; min-height: 0; overscroll-behavior: contain; }
    </style>
    @yield('styles')
</head>
<body>
    @include('front.template-3.partials.header')
    @include('front.template-3.partials.mobile_nav')

    <main>
        @yield('content')
    </main>

    @include('front.template-3.partials.footer')
    @include('front.template-3.partials.search_modal')
    @include('front.template-3.partials.cart_drawer')

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
    <script src="{{ url(env('ASSETSPATHURL') . 'web-assets/templates/restaurant/js/oc-cart.js') }}"></script>
    @yield('scripts')
</body>
</html>
