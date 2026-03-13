<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta Http-Equiv="Cache-Control" Content="no-cache, max-age=0, must-revalidate, no-store">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Memory')</title>

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">

    @if (app()->getLocale() == 'pl')
        <link href="/css/fonts_pl.css" rel="stylesheet"/>
    @else
        <link href="/css/fonts.css" rel="stylesheet"/>
    @endif

    {{-- Bootstrap 4 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    {{-- FontAwesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    {{-- Student CSS --}}
    <link href="{{ mix('/css/student.css') }}" rel="stylesheet"/>

    <link rel="apple-touch-icon" sizes="180x180" href="{{asset('images/favicons/favicon-180x180.png')}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{asset('images/favicons/favicon-32x32.png')}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/favicons/favicon-16x16.png')}}">

    <link rel="mask-icon" href="{{asset('images/favicons/safari-pinned-tab.svg')}}" color="#5bbad5">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"/>
    <meta name="msapplication-TileColor" content="#b91d47">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css">

    @yield('styles')
    <style>
        body {
            background-color: #04151d !important;
            margin: 0;
            padding: 0;
            color: #fff;
        }
        .hidden {
            display: none;
        }
    </style>
</head>

<body>
    @yield('content')

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ mix('/js/admin.js') }}"></script>
    @yield('bottom-scripts')
</body>
</html>
