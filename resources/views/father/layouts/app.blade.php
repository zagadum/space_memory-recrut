<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'GLS — Portal Rodzica')</title>
    <link rel="stylesheet" href="{{ asset('css/father.css') }}">
</head>
<body class="father-portal">
    <nav class="father-nav">
        <div class="father-nav__logo">Global Leaders Skills</div>
        <div class="father-nav__links">
            <a href="{{ route('father.document') }}"
               class="father-nav__link @if(request()->routeIs('father.document*')) father-nav__link--active @endif">
                {{ __('father.nav.documents') }}
            </a>
            <a href="{{ route('father.payment') }}"
               class="father-nav__link @if(request()->routeIs('father.payment*')) father-nav__link--active @endif">
                {{ __('father.nav.payments') }}
            </a>
            <a href="{{ route('father.learn') }}"
               class="father-nav__link @if(request()->routeIs('father.learn')) father-nav__link--active @endif">
                {{ __('father.nav.learn') }}
            </a>
        </div>
    </nav>

    <main class="father-main">
        @yield('content')
    </main>
</body>
</html>
