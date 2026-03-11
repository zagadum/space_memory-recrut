{{-- Mobile Header --}}
<header class="d-lg-none p-3 d-flex justify-content-between align-items-center">
    <div class="logo-box">
        <a href="{{ url('student') }}" class="navbar-brand p-0 m-0">
            <img src="{{asset('images/logo.svg')}}" alt="Logo" style="max-height: 64px;">
    </a>
    </div>
    <button class="btn btn-link text-dark p-0 menu-toggle-btn" id="menu-toggle">
        <span class="menu-icon">
            <span class="menu-line"></span>
            <span class="menu-line"></span>
        </span>
    </button>
</header>
