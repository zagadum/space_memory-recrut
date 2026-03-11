<?php
$prefix = Route::getCurrentRoute()->getPrefix();
?>
<div class="sidebar sidebar-menu" >
    <div class="sidebar-logo">
        @if(View::exists('admin.layout.logo'))
            @include('admin.layout.logo')
        @endif
    </div>

    <nav class="sidebar-nav">
        <ul class="nav">
            <!--- ЭТО  Можно править --->
            @if (session('role')=='admin')
             <li class="nav-item"><a class="nav-link" href="{{ url('admin/franchisees') }}"><img src="{{asset('images/fi_user.svg')}}">{{ trans('admin.menu.franchisee.title') }}</a><a class="btn btn-create-item btn-spinner" href="{{ url('admin/franchisees/create') }}" role="button"><img src="{{asset('images/btn_plus.svg')}}"></a></li>
            @endif
            @if (session('role')=='franchisee' || session('role')=='admin')
                <li class="nav-item"><a class="nav-link" href="{{ url('admin/managers') }}"><img src="{{asset('images/fi_user.svg')}}">{{ trans('admin.menu.managers.title') }}</a><a class="btn btn-create-item btn-spinner" href="{{ url('admin/managers/create') }}" role="button"><img src="{{asset('images/btn_plus.svg')}}"></a></li>

           @endif
            @if (in_array(session('role'),['franchisee','admin','manager']))
                <li class="nav-item"><a class="nav-link" href="{{ url('admin/teachers') }}"><img src="{{asset('images/fi_user-check.svg')}}"> {{ trans('admin.menu.teacher.title')   }}</a><a class="btn btn-create-item btn-spinner" href="{{ url('admin/teachers/create') }}" role="button"><img src="{{asset('images/btn_plus.svg')}}"></a></li>
            @endif
            <li class="nav-item"><a class="nav-link" href="{{ url('admin/teacher-groups') }}"><img src="{{asset('images/organizations.svg')}}"> {{  trans('admin.menu.groups.title') }}</a><a class="btn btn-create-item btn-spinner" href="{{ url('admin/teacher-groups/create') }}" role="button"><img src="{{asset('images/btn_plus.svg')}}"></a></li>
           <li class="nav-item"><a class="nav-link" href="{{ url('admin/students') }}"><img src="{{asset('images/fi_users.svg')}}"> {{ trans('admin.menu.student.title')  }}</a><a class="btn btn-create-item btn-spinner" href="{{ url('admin/students/create') }}" role="button"><img src="{{asset('images/btn_plus.svg')}}"></a></li>
            @if (session('role')=='admin' || session('role')=='franchisee')
                <li class="nav-item"><a class="nav-link" href="{{ url('admin/restore') }}"><img src="{{asset('images/RestTime.svg')}}">{{ trans('admin.menu.restore.title')  }}</a></li>
            <li class="nav-hr"></li>
            @endif
           <li class="nav-item"><a class="nav-link"  href="/admin/calendar"><img src="{{asset('images/calendar.svg')}}">{{ trans('admin.menu.calendar.title') }}</a></li>
           <li class="nav-hr"></li>

            <!--- OLYMPIAD MENU -->
            @if (session('role')=='admin' or session('role')=='franchisee' )
                <li class="nav-item"><a class="nav-link" href="{{ url('admin/olympiad_main') }}"><img src="{{asset('images/fi_olympiad.svg')}}"> {{ trans('olympiad.menu.title') }}</a><a class="btn btn-create-item btn-spinner" href="{{ url('admin/olympiad_main/create') }}" role="button"><img src="{{asset('images/btn_plus.svg')}}"></a></li>
            @endif
                @if ($prefix=='/admin/olympiad_main')

{{--                    <li class="nav-item" style="padding-left:10pt"><a class="nav-link" href="{{ url('admin/olympiad_main/practicians') }}"><img src="{{asset('images/fi_olympiad.svg')}}">{{ trans('olympiad.menu.practicians') }}</a></li>--}}
                     <li class="nav-item" style="padding-left:10pt"><a class="nav-link" href="{{ url('admin/olympiad_main/payments') }}"><img src="{{asset('images/fi_olympiad.svg')}}">{{ trans('olympiad.menu.payments') }}</a></li>
                @endif


            <li class="nav-hr"></li>
            <!--- OLYMPIAD END -->

           <li class="nav-item"><a class="nav-link" href="/student/traning/create"><img src="{{asset('images/fi_edit-3.svg')}}">{{ trans('admin.menu.training.title') }}</a></li>
           <li class="nav-item"><a class="nav-link" href="/student/traning/present"><img src="{{asset('images/fi_edit-3.svg')}}">{{ trans('admin.menu.training.present') }}</a></li>
           <li class="nav-item"><a class="nav-link" href="/student/olympiad/create"><img src="{{asset('images/fi_edit-3.svg')}}">{{ trans('admin.menu.training.olympiad') }}</a></li>

            @if (session('role')!='manager')
             <li class="nav-item"><a class="nav-link" href="/admin/learning"><img src="{{asset('images/fi_book-open.svg')}}">{{ trans('admin.menu.video_material.title') }}</a></li>
            @endif
            @if (session('role')=='admin')
            <li class="nav-item"><a class="nav-link" href="/admin/ads"><img src="{{asset('images/fi_book-open.svg')}}">ADS</a></li>
            @endif

            @if (session('role')!='manager')
                <li class="nav-hr"></li>
               <li class="nav-item dropdown"><a class="nav-link" href="{{ url('admin/shop/') }}" ><img src="{{asset('images/fi_grid.svg')}}">{{ trans('admin.menu.shop.title') }}</a></li>
               <li class="nav-item"><a class="nav-link" href="{{ url('admin/shop/orders') }}" ><img src="{{asset('images/fi_union.svg')}}">{{ trans('admin.menu.order.title') }}</a></li>
               <li class="nav-hr"></li>
               <li class="nav-item"><a class="nav-link" href="{{ url('admin/finance') }}"><img src="{{asset('images/integration.svg')}}">{{ trans('admin.menu.finance.title') }}</a></li>
               <li class="nav-item"><a class="nav-link" href="{{ url('admin/student-payments') }}"><img src="{{asset('images/fi_settings.svg')}}"> {{ trans('admin.menu.student-payment.title') }}</a></li>
               <li class="nav-hr"></li>
               <li class="nav-item"><a class="nav-link" href="{{ url('admin/faq') }}"   ><img src="{{asset('images/fi_help-circle.svg')}}">{{ trans('admin.menu.faq.title') }}</a></li>
            @endif
            @if ((Auth::guard('franchisee')->check() || Auth::guard('admin')->check()) &&  session('role')!='manager')
            <li class="nav-item"><a class="nav-link" href="{{ url('admin/feedback') }}"   ><img src="{{asset('images/fi_message-square.svg')}}">{{ trans('admin.menu.feedback.title') }}</a></li>
           @endif

         @if (session('role')!='manager')
           <li class="nav-hr"></li>
           <li class="nav-item"><a class="nav-link" href="{{ url('admin/training-contract') }}"><img src="{{asset('images/fi_file-text.svg')}}">{{ trans('admin.menu.training-contract.title') }}</a></li>
           <li class="nav-item"><a class="nav-link" href="{{ url('admin/about') }}"><img src="{{asset('images/fi_file-text.svg')}}">{{ trans('admin.menu.about.title') }}</a></li>
           <li class="nav-item"><a class="nav-link" href="{{ url('admin/privacy-policy') }}"><img src="{{asset('images/fi_file-text.svg')}}">{{ trans('admin.menu.privacy-policy.title') }}</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ url('admin/term-use') }}"   ><img src="{{asset('images/fi_file-text.svg')}}">{{ trans('admin.menu.term-use.title') }}</a></li>
            @endif
        </ul>
    </nav>
    <a href="{{ url('admin/logout') }}" class="sidebar-back_btn">{{ trans('admin.btn.logout') }}</a> <!--sidebar-minimizer brand-minimizer-->
</div>
