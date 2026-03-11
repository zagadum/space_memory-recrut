
    <ul class="admin_card-header_nav nav navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a role="button" class="dropdown-toggle nav-link">
                        <span>
<?php
                            $roleGet=session('role');
                            $roleText='';
                           $Auth=Auth::guard($roleGet)->user();
                            if (session('role')=='admin'){
                                $roleText='('. trans('admin.role.admin') .')';
                            }
                            if (session('role')=='teacher'){
                                $roleText='('. trans('admin.role.teacher') .')';

                            }
                            if (session('role')=='franchisee'){
                                $roleText='('. trans('admin.role.franchisee') .')';

                            }
                            ?>

                            @if(Auth::check() && $Auth->avatar_thumb_url)
                                <img src="{{ $Auth->avatar_thumb_url }}" class="avatar-photo">
                            @elseif(isset($Auth->id) && $Auth->surname && $Auth->first_name)
                                <span class="avatar-initials"><img src="{{asset('/images/fi_user.png')}}"></span>
    @else
                                <span class="avatar-initials"><i class="fa fa-user"></i></span>
                            @endif

                        @if (!empty($Auth->full_name))
                       <span class="hidden-md-down">{{$roleText}} {{  $Auth->full_name  }}</span>
                        @endif

                        </span>
                <span class="caret"></span>
            </a>
            @if(View::exists('admin.layout.profile-dropdown'))
                @include('admin.layout.profile-dropdown')
            @endif
        </li>
    </ul>

