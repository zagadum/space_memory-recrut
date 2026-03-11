<div class="sidebar">
    <div class="sidebar-logo">
        @if(View::exists('admin.layout.logo'))
            @include('admin.layout.logo')
        @endif
    </div>
 <nav class="sidebar-nav">

<!--- ЭТО НЕ ТРОГАТЬ ЭТО АДМИН МЕНЮ --->
        [{{@Auth::user()->language}}]
<ul class="nav">
    @if (Auth::guard('admin')->check())
           <li class="nav-item"><a class="nav-link" href="{{ url('admin/training-statistics') }}"><i class="nav-icon icon-star"></i> {{ trans('admin.training-statistic.title') }}</a></li>
           <li class="nav-item"><a class="nav-link" href="{{ url('admin/currencies') }}"><i class="nav-icon icon-user"></i> {{ __('Currency') }}</a></li>
           <li class="nav-item"><a class="nav-link" href="{{ url('admin/countries') }}"><i class="nav-icon icon-user"></i> {{ __('Countries') }}</a></li>
           <li class="nav-item"><a class="nav-link" href="{{ url('admin/regions') }}"><i class="nav-icon icon-user"></i> {{ __('Regions') }}</a></li>
           <li class="nav-item"><a class="nav-link" href="{{ url('admin/cities') }}"><i class="nav-icon icon-user"></i> {{ __('Cities') }}</a></li>

            <li class="nav-title">{{ trans('admin.sidebar.settings') }}</li>
            <li class="nav-item"><a class="nav-link" href="{{ url('admin/admin-users') }}"><i class="nav-icon icon-user"></i> {{ __('Manage access') }}</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ url('admin/translations') }}"><i class="nav-icon icon-location-pin"></i> {{ __('Translations') }}</a></li>
    @endif
            <li class="nav-title">{{ __('admin.menu.cabinet.title') }}</li>
            <li class="nav-item"><a class="nav-link" href="{{ url('admin/') }}"><i class="nav-icon icon-user"></i> {{ __('admin.menu.admin_home.title') }}</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ url('admin/training-images-tasks') }}"><i class="nav-icon icon-puzzle"></i> {{ __('admin.menu.training-images-task.title') }}</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ url('admin/training-words-tasks') }}"><i class="nav-icon icon-magnet"></i> {{ __('admin.menu.training-words-task.title') }}</a></li>
             <!-- demo menu -->




            <li class="nav-title">{{ trans('admin.sidebar.settings') }}</li>
            {{-- Do not delete me :) I'm also used for auto-generation menu items --}}
            {{--<li class="nav-item"><a class="nav-link" href="{{ url('admin/configuration') }}"><i class="nav-icon icon-settings"></i> {{ __('Configuration') }}</a></li>--}}

</ul>
            <a href="{{ url('admin/logout') }}" class="sidebar-back_btn">{{ trans('brackets/admin-auth::admin.profile_dropdown.logout') }}</a> <!--sidebar-minimizer brand-minimizer-->
</nav>
</div>


