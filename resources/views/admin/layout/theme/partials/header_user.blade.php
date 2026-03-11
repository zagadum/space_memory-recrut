<header class="app-header navbar admin-header">
    @if(View::exists('admin.layout.logo'))
        @include('admin.layout.logo_header_user')
    @endif
    <button class="navbar-toggler sidebar-toggler d-lg-none" type="button" data-toggle="sidebar-show">
        <span class="navbar-toggler-icon"></span>
    </button>
</header>