@php
    $tActive = $tActive ?? 'home';
    $tCss = $tCss ?? env('ASSETSPATHURL') . 'web-assets/templates/booking/';
@endphp
<!DOCTYPE html>
<html lang="{{ session()->get('locale', app()->getLocale()) }}" data-theme="light" dir="{{ session()->get('direction') == 2 ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    @include('partials.google_tag')
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $tDesc }}">
    <title>{{ $tName ?: \Illuminate\Support\Str::title(str_replace('-', ' ', $tSlug)) }}</title>
    <link rel="icon" href="{{ helper::image_path($tApp->favicon) }}" type="image" sizes="16x16">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;1,9..144,600&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ url($tCss . 'css/style.css') }}">
    @include('front.partials.responsive_fix')
    <style>
      .modal__dialog { overflow: hidden; display: flex; flex-direction: column; }
      .modal__body { overflow-y: auto; min-height: 0; overscroll-behavior: contain; }
    </style>
    @yield('styles')
</head>
<body>
    @include('front.template-8.partials.topbar')
    @include('front.template-8.partials.header')
    @include('front.template-8.partials.mobile_nav')
    <main>@yield('content')</main>
    @include('front.template-8.partials.footer')
    @include('front.template-8.partials.search_modal')
    <div class="toast-wrap"></div>
    <script src="{{ url($tCss . 'js/main.js') }}"></script>
    @yield('scripts')
    @include('front.partials.ai_assistant')
</body>
</html>
