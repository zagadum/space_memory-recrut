@extends('admin.layout.theme.master_user')

@section('header')
    @include('admin.layout.theme.partials.header_user')
@endsection


@section('content')

    <div class="app-body">

        @if(View::exists('admin.layout.sidebar_user'))
            @include('admin.layout.sidebar_user')
        @endif

        <main class="main">

            <div class="container-fluid" id="app" :class="{'loading': loading}">
                <div class="modals">
                    <v-dialog/>
                </div>

                <div>
                    <notifications position="bottom right" :duration="2000" />
                </div>

                @yield('body')

                @include('admin.layout.theme.partials.modal-delete')
            </div>
        </main>
    </div>


@endsection




@section('bottom-scripts')
    @parent
@endsection
