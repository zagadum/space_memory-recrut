<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">

<head>

    <meta charset="utf-8">
    <meta Http-Equiv="Cache-Control" Content="no-cache, max-age=0, must-revalidate, no-store">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="csrf-token" content="{{ csrf_token() }}">

	{{-- TODO translatable suffix --}}
    <title>@yield('title', 'Memory') - {{ trans('admin.page_title_suffix') }}</title>


    <link href="{{ mix('/css/admin.css') }}" rel="stylesheet"/>
    @if (\App\Helpers\SiteHelper::GetLang()=='pl' || \App\Helpers\SiteHelper::GetLang()=='en')
        <link href="/css/fonts_pl.css" rel="stylesheet"/>
    @else
        <link href="/css/fonts.css" rel="stylesheet"/>
    @endif
    @yield('styles')

</head>

<body class="app">
@yield('header')

    @yield('content')



    @include('admin.partials.wysiwyg-svgs')
<script src="/js/polyfill.min.js"></script>
    <script src="{{ mix('/js/admin.js') }}"></script>


    @yield('bottom-scripts')
</body>

</html>
