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
        .hidden {
            display: none;
        }
        /* Restricted Access Styles */
        .nav-link--locked {
            opacity: 0.6 !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
            filter: grayscale(1) !important;
            position: relative;
        }
        .nav-link--locked::after {
            content: '🔒';
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 14px;
            opacity: 0.8;
            z-index: 5;
        }
        /* Mobile specific lock tweak */
        .fixed-bottom-menu .nav-link--locked::after {
            right: 50%;
            top: -5px;
            transform: translateX(50%);
            font-size: 12px;
        }
    </style>
</head>

<body class="page-{{ str_replace('/', '-', trim(request()->path(), '/')) ?: 'home' }} {{ !empty($hideLeftMenu) ? 'no-sidebar' : '' }}">
<div class="container-fluid h-100 ">
    <div class="row h-100 flex-nowrap">
        {{-- Desktop Sidebar --}}
        @if (empty($hideLeftMenu))
        @include('student.layout.theme.partials.sidebar_student')
        @endif
        {{-- Main Content --}}
        <main class="col p-0 d-flex flex-column h-100">
            {{-- Mobile Header --}}

            @include('student.layout.theme.partials.header_student')

            {{-- Content Area --}}
            <div class="content-area flex-grow-1">
@yield('content')

        </div>
        </main>
        </div>
</div>
@if (empty($hideLeftMenu))
{{-- Fixed Bottom Menu (Mobile only) --}}
@include('student.layout.theme.partials.bottom_menu')

{{-- Full Screen Mobile Menu Overlay --}}
@include('student.layout.theme.partials.mobile_menu')
@endif
{{-- Confirm Dialog --}}
@include('student.components.confirm-dialog')

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ mix('/js/admin.js') }}"></script>
{{--<script src="/js/confirm-dialog.js"></script>--}}
{{-- Mobile Menu Toggle Script --}}
<script>
    $(document).ready(function () {
        $('#menu-toggle').click(function () {
            $(this).addClass('active');
            $('#mobile-menu-overlay').removeClass('d-none');
            $('body').addClass('overflow-hidden');
        });

        $('#menu-close').click(function () {
            $('#menu-toggle').removeClass('active');
            $('#mobile-menu-overlay').addClass('d-none');
            $('body').removeClass('overflow-hidden');
        });




        });


    function confirmDialog(message, onConfirm,subject='') {
        if (message!=''){
            $('#confirmMessage').text(message);
        }
        if (subject!=''){
            $('#confirmSubject').text(subject);
        }else{
            $('#confirmSubject').hide();
        }

        $('#overlay, #confirmDialog').fadeIn();

        // очищаємо старі обробники, щоб не дублювались
        $('#confirmYes').off('click').on('click', function () {
            $('#overlay, #confirmDialog').fadeOut();
            onConfirm(1);
        });

    }


    $('.btn-close').off('click').on('click', function () {
        $('#overlay, #confirmDialog').fadeOut();

    });
    $('#confirmNo').off('click').on('click', function () {
        $('#overlay, #confirmDialog').fadeOut();

    });
</script>
@yield('bottom-scripts')
</body>
</html>
