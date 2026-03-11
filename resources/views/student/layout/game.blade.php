@extends('student.layout.game-platform')



@section('content')
    <?php
    $addMainClass='hideMenu';
        if (!empty($hideLeftMenu)){
            $addMainClass='hideMenu';
        }
    ?>
    <div class="app-body" style="margin-top: 0px;">
        @if(empty($hideLeftMenu))
            <div class="sidebar">
                <nav class="sidebar-nav">
                    <ul class="nav">
                        <li class="nav-item"><a class="nav-link" href="#">{{ trans('game.main_menu.inventar') }}</a></li>
                        <li class="nav-item"><a class="nav-link"  href="#">Солнечная система</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Задания</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ url('student/shop') }}">{{ trans('student.main_menu.shop') }}</a></li>

                    </ul>
                </nav>
            </div>
        @endif

        <main class="main {{$addMainClass}}">
            <div class="container-fluid training_container" id="app">


                    <notifications position="bottom right" :duration="1000" ></notifications>


                @yield('body')
            </div>
        </main>
    </div>


@endsection

@section('bottom-scripts')
    @parent
@endsection
