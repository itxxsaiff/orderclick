<!DOCTYPE html>
<html lang="{{ session()->get('locale', app()->getLocale()) }}" dir="{{ session()->get('direction') == 2 ? 'rtl' : 'ltr' }}" class="light">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta property="og:title" content="{{ @helper::appdata('')->meta_title }}" />
    <meta property="og:description" content="{{ @helper::appdata('')->meta_description }}" />
    <meta property="og:image" content="{{ @helper::image_path(@helper::appdata('')->og_image) }}" />

     <script>
        const theme = localStorage.getItem('theme');
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.add('light');
        }
    </script>
    
    {{-- <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">  --}}

    <link rel="icon" href="{{ @helper::image_path(@helper::appdata('')->favicon) }}" type="image" sizes="16x16">
    <title>{{ @helper::appdata('')->website_title }}</title>
    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'admin-assets/css/bootstrap/bootstrap.min.css') }}">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'admin-assets/css/fontawesome/all.min.css') }}">
    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'admin-assets/css/toastr/toastr.min.css') }}">
    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'admin-assets/css/style.css') }}"><!-- Custom CSS -->
    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'admin-assets/css/responsive.css') }}">
    <!-- Responsive CSS -->
    <!-- IF VERSION 2  -->
    @if (@helper::appdata('')->recaptcha_version == 'v2')
    <script src='https://www.google.com/recaptcha/api.js'></script>
    @endif
    <!-- IF VERSION 3  -->
    @if (@helper::appdata('')->recaptcha_version == 'v3')
    {!! RecaptchaV3::initJs() !!}
    @endif

   
</head>

<body>
    <main>
        @yield('content')
    </main>
    <script src="{{ url(env('ASSETSPATHURL') . 'admin-assets/js/jquery/jquery.min.js') }}"></script><!-- jQuery JS -->
    <script src="{{ url(env('ASSETSPATHURL') . 'admin-assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script><!-- Bootstrap JS -->
    <script src="{{ url(env('ASSETSPATHURL') . 'admin-assets/js/toastr/toastr.min.js') }}"></script><!-- Toastr JS -->
    <script>
        function setLightMode() {
            document.documentElement.classList.remove('dark');
            document.documentElement.classList.add('light');
            localStorage.setItem('theme', 'light');
        }

        function setDarkMode() {
            document.documentElement.classList.remove('light');
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        }
    </script>
    <script>
        toastr.options = {
            "closeButton": true,
            "positionClass": "toast-bottom-center",
        }
        @if(Session::has('success'))
        toastr.success("{{ session('success') }}");
        @endif
        @if(Session::has('error'))
        toastr.error("{{ session('error') }}");
        @endif
        $(window).on("load", function() {
            "use strict";
            $('#preloader').fadeOut('slow')
        });

        function myFunction() {
            "use strict";
            toastr.error("This operation was not performed due to demo mode");
            return false;
        }

        $('#name').on('blur', function() {
            "use strict";
            $('#slug').val($('#name').val().split(" ").join("-").toLowerCase());
        });
    </script>
    @yield('scripts')
</body>

</html>
