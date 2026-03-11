@extends('student.layout.theme.master_student')

@section('header')
    @include('student.layout.theme.partials.header_student')
@endsection

@section('content')
    <?php
    $addMainClass='hideMenu';
        if (!empty($hideLeftMenu)){
            $addMainClass='hideMenu';
        }
    ?>

    <div class="app-body">
      <main class="main {{$addMainClass}}">
            <div class="container-fluid training_container" id="app">
                <div>
                    <notifications position="bottom right" :duration="2000">
                </div>
                @yield('body')
            </div>
        </main>
    </div>
@endsection

@section('bottom-scripts')
    @parent
@endsection
