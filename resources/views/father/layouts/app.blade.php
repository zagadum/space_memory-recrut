<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'GLS — Portal Rodzica')</title>
    <link rel="stylesheet" href="{{ asset('css/father.css') }}">
    @yield('styles')
    <style>
        .father-nav__link--locked {
            opacity: 0.6;
            cursor: not-allowed !important;
            pointer-events: none;
            filter: grayscale(1);
            position: relative;
        }
        .father-nav__link--locked::after {
            content: '🔒';
            font-size: 11px;
            margin-left: 6px;
            opacity: 0.8;
            vertical-align: middle;
        }
    </style>
</head>
<body class="father-portal">
    <nav class="father-nav">
        <div class="father-nav__logo">Global Leaders Skills</div>
        <div class="father-nav__links">
            <a href="/father/document"
               class="father-nav__link @if(request()->routeIs('father.document*')) father-nav__link--active @endif">
                {{ __('father.nav.documents') }}
            </a>
            <a href="{{ route('father.payment') }}"
               class="father-nav__link @if(request()->routeIs('father.payment*')) father-nav__link--active @endif">
                {{ __('father.nav.payments') }}
            </a>
            @php
                $isPaid = false;
                if(Auth::guard('recruting_student')->check()) {
                    $isPaid = Auth::guard('recruting_student')->user()->hasPaid();
                }
            @endphp
            <a href="{{ route('father.learn') }}"
               class="father-nav__link @if(request()->routeIs('father.learn')) father-nav__link--active @endif {{ !$isPaid ? 'father-nav__link--locked' : '' }}">
                {{ __('father.nav.learn') }}
            </a>
            <form method="POST" action="{{ route('father.logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="father-nav__link" style="background:none; border:none; cursor:pointer;">
                    {{ __('father.nav.logout') }}
                </button>
        </div>
    </nav>

    <main class="father-main">
        @yield('content')
    </main>

    @yield('scripts')
</body>
</html>
