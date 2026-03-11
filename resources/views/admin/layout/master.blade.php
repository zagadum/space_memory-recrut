<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="csrf-token" content="{{ csrf_token() }}">

	{{-- TODO translatable suffix --}}
    <title>@yield('title', 'Memory') - {{ trans('admin.page_title_suffix') }}</title>

    @if (\App\Helpers\SiteHelper::GetLang()=='pl')
        <link href="/css/fonts_pl.css" rel="stylesheet"/>
    @else
        <link href="/css/fonts.css" rel="stylesheet"/>
    @endif
    <link href="{{ mix('/css/admin.css') }}" rel="stylesheet">

    {{-- favicons --}}

    <link rel="apple-touch-icon" sizes="180x180" href="{{asset('images/favicons/favicon-180x180.png')}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{asset('images/favicons/favicon-32x32.png')}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/favicons/favicon-16x16.png')}}">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="{{asset('images/favicons/safari-pinned-tab.svg')}}" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#b91d47">

    @yield('styles')

</head>

<body class=" auth-page app header-fixed sidebar-fixed sidebar-lg-show">
    @yield('header')

    @yield('content')

    @yield('footer')


    @yield('bottom-scripts')
</body>

</html>
