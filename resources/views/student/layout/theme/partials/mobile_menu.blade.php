{{-- Full Screen Mobile Menu Overlay --}}
<div id="mobile-menu-overlay" class="d-none">
    <div class="d-flex flex-column h-100 p-3">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div class="logo-box">
                <a href="{{ url('student') }}" class="navbar-brand p-0 m-0">
                    <img src="{{asset('images/logo.svg')}}" alt="Logo" style="max-height: 64px;">
                </a>
            </div>
            <button class="btn btn-link p-0 menu-close-btn" id="menu-close">
                <span class="close-icon">
                    <span class="close-line"></span>
                    <span class="close-line"></span>
                </span>
            </button>
        </div>

        {{-- Menu Items --}}
        <nav class="nav flex-column mb-auto px-4">
            <a class="nav-link btn btn-outline-primary mb-3 {{ Request::is('student') || Request::is('student/dashboard') ? 'active' : '' }}" 
               href="{{ url('student') }}">
                <div class="nav-icon icon-home"></div>
                {{ trans('student.main_menu.main') }}
            </a>
            
            <a class="nav-link btn btn-outline-primary mb-3 {{ Request::is('student/hometask*') ? 'active' : '' }}" 
               href="{{ url('student/hometask') }}">
                <div class="nav-icon icon-homework"></div>
                {{ trans('student.main_menu.hometask') }}
            </a>
            
            <a class="nav-link btn btn-outline-primary mb-3 {{ Request::is('student/traning*') ? 'active' : '' }}" 
               href="{{ url('student/traning/create') }}">
                <div class="nav-icon icon-training"></div>
                {{ trans('student.main_menu.traning') }}
            </a>
            
            <a class="nav-link btn btn-outline-primary mb-3 {{ Request::is('student/olympiad*') && !Request::is('student/olympiad-portal*') ? 'active' : '' }}"
               href="{{ url('student/olympiad/create') }}">
                <div class="nav-icon icon-olympiad"></div>
                {{ trans('student.main_menu.olympiad') }}
            </a>

            <a class="nav-link btn btn-outline-primary mb-3 {{ Request::is('student/olympiad-portal*') ? 'active' : '' }}"
               href="{{ url('student/olympiad-portal') }}">
                <div class="nav-icon icon-olympiad"></div>
                {{ trans('student.main_menu.olympiad-main') }}
            </a>

            <a class="nav-link btn btn-outline-primary mb-3 {{ Request::is('student/bugtracker*') ? 'active' : '' }}"
               href="/student/bugtracker/create">
                <div class="nav-icon icon-olympiad"></div>
                {{ trans('student.main_menu.bugtracker') }}
            </a>

            <a class="nav-link btn btn-outline-primary mb-3 {{ Request::is('student/bonus-history*') ? 'active' : '' }}"
               href="{{ url('student/bonus-history') }}">
                <div class="nav-icon icon-olympiad"></div>
                {{ trans('student.main_menu.bonus-history') }}
            </a>

            <a href="{{ url('/games/platform') }}" class="btn btn-primary btn-block mb-4">
                {{ trans('student.main_menu.games') }}
            </a>
        </nav>

        {{-- Bottom Actions --}}
        <div class="px-4 pb-5">
            <a href="{{ url('/logout') }}" class="nav-link btn btn-outline-secondary btn-block">
                <div class="nav-icon icon-exit"></div>
                {{ trans('student.main_menu.exit') }}
            </a>
        </div>
    </div>
</div>
