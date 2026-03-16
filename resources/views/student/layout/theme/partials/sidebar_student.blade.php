{{-- Desktop Sidebar --}}
<aside class="sidebar-desktop d-none d-lg-flex flex-column">
    <div class="logo-area mb-5">
        <div class="logo-box text-center">
        <a href="{{ url('student') }}" class="navbar-brand">
                <img src="{{asset('images/logo.svg')}}" alt="Space Memory">
        </a>
        </div>
        @if (config('app.site_closed'))
            <div class="alert alert-warning mt-2 text-center small">
                SITE CLOSE - TEST MODE
            </div>
        @endif
    </div>

    <nav class="nav flex-column mb-auto">
        @php
            $isPaid = false;
            if(Auth::guard('recruting_student')->check()) {
                $isPaid = Auth::guard('recruting_student')->user()->hasPaid();
            }
        @endphp

        <a class="nav-link btn btn-outline-primary mb-3 {{ !$isPaid ? 'nav-link--locked' : '' }}" href="/father">
           <div class="nav-icon icon-home"></div>
            <span>{{ trans('student.main_menu.main') }}</span>
        </a>

        <a class="nav-link btn btn-outline-primary mb-3 {{ Request::is('student/hometask*') ? 'active' : '' }} {{ !$isPaid ? 'nav-link--locked' : '' }}" href="#">
           <div class="nav-icon icon-homework"></div>
            <span>{{ trans('student.main_menu.hometask') }}</span>
        </a>

        <a class="nav-link btn btn-outline-primary mb-3 {{ Request::is('student/traning*') ? 'active' : '' }} {{ !$isPaid ? 'nav-link--locked' : '' }}" href="#">
           <div class="nav-icon icon-training"></div>
            <span>{{ trans('student.main_menu.traning') }}</span>
        </a>

        <a class="nav-link btn btn-outline-primary mb-3 {{ Request::is('student/olympiad*') ? 'active' : '' }} {{ !$isPaid ? 'nav-link--locked' : '' }}" href="#">
           <div class="nav-icon icon-olympiad"></div>
            <span>{{ trans('student.main_menu.olympiad') }}</span>
        </a>

        <a class="nav-link btn btn-outline-primary mb-3 {{ Request::is('father*') || Request::is('student*') ? 'active' : '' }}" href="{{ route('father.portal') }}">
           <div class="nav-icon icon-parent"></div>
            <span>Strefa rodzica</span>
        </a>




        <a href="#" class="sidebar-myspace-btn {{ !$isPaid ? 'nav-link--locked' : '' }}">
            <span class="fold"></span>

            <div class="points_wrapper">
                <i class="point"></i>
                <i class="point"></i>
                <i class="point"></i>
                <i class="point"></i>
                <i class="point"></i>
                <i class="point"></i>
                <i class="point"></i>
                <i class="point"></i>
                <i class="point"></i>
                <i class="point"></i>
            </div>

            <span class="inner">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5">
                    <polyline points="13.18 1.37 13.18 9.64 21.45 9.64 10.82 22.63 10.82 14.36 2.55 14.36 13.18 1.37"></polyline>
                </svg>
                <span>{{ trans('student.main_menu.games') }}</span>
            </span>
        </a>
    </nav>



    <div class="mt-4">
        @if(Auth::guard('recruting_student')->check())
            <a href="{{ route('father.logout') }}" class="nav-link btn btn-outline-secondary btn-block">
                <div class="nav-icon icon-exit"></div>
                <span>{{ trans('student.main_menu.exit') }}</span>
            </a>
        @else
            <a href="{{ url('/logout') }}" class="nav-link btn btn-outline-secondary btn-block">
                <div class="nav-icon icon-exit"></div>
                <span>{{ trans('student.main_menu.exit') }}</span>
            </a>
        @endif
    </div>
</aside>
