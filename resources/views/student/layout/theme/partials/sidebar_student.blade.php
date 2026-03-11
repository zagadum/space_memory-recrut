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
        <a class="nav-link btn btn-outline-primary mb-3 text-left {{ Request::is('student') || Request::is('student/dashboard') ? 'active' : '' }}" href="{{ url('student') }}">
           <div class="nav-icon icon-home"></div>
            <span>{{ trans('student.main_menu.main') }}</span>
        </a>

        <a class="nav-link btn btn-outline-primary mb-3 text-left {{ Request::is('student/hometask*') ? 'active' : '' }}" href="#">
           <div class="nav-icon icon-homework"></div>
            <span>{{ trans('student.main_menu.hometask') }}</span>
        </a>

        <a class="nav-link btn btn-outline-primary mb-3 text-left {{ Request::is('student/traning*') ? 'active' : '' }}" href="#">
           <div class="nav-icon icon-training"></div>
            <span>{{ trans('student.main_menu.traning') }}</span>
        </a>

        <a class="nav-link btn btn-outline-primary mb-3 text-left {{ Request::is('student/olympiad*') ? 'active' : '' }}" href="#">
           <div class="nav-icon icon-olympiad"></div>
            <span>{{ trans('student.main_menu.olympiad') }}</span>
        </a>



        <a href="#" class="btn btn-primary btn-block mt-5 sidebar-myspace-btn">
            <img src="{{ asset('images/cta-circle.png') }}" alt="" class="sidebar-myspace-icon">
            <span>{{ trans('student.main_menu.games') }}</span>
        </a>
    </nav>

    <div class="mt-4">
        <a href="{{ url('/logout') }}" class="nav-link btn btn-outline-secondary btn-block">
            <div class="nav-icon icon-exit"></div>
            <span>{{ trans('student.main_menu.exit') }}</span>
        </a>
    </div>
</aside>
