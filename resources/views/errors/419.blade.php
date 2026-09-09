<!DOCTYPE html>
<html lang="{{ session()->get('locale', app()->getLocale()) }}" dir="{{ session()->get('direction') == 2 ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:title" content="{{ helper::adminappdata('')->meta_title }}" />
    <meta property="og:description" content="{{ helper::adminappdata('')->meta_description }}" />
    <meta property="og:image" content='{{ helper::image_path(helper::adminappdata('')->og_image) }}' />
    <link rel="icon" href="{{ helper::image_path(helper::adminappdata('')->favicon) }}" type="image"
        sizes="16x16">
    <title> {{ helper::adminappdata('')->website_title }} </title>
    <!-- Font Family Poppins -->

    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'admin-assets/css/poppins.css') }}">

    <!-- Error Css -->

    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'admin-assets/css/error.css') }}">

    <!-- Error Responsive -->

    <link rel="stylesheet" href="{{ url(env('ASSETSPATHURL') . 'admin-assets/css/error-responsive.css') }}">

</head>

<body>
    <div class="errorpage">
        <div>
            <h1>Oops!</h1>
            <b>419 - Your session has expired</b>
            <p class="subtitle">Go back and try again.</p>
            <a href="{{ URL::to(@request()->vendor . '/') }}" class="btn btn-primary">Go To Homepage</a>
        </div>
    </div>
</body>

</html>
