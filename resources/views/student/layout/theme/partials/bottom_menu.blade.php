{{-- Fixed Bottom Menu (Mobile only) --}}
<div class="fixed-bottom-menu fixed-bottom d-lg-none py-2">
    <div class="row text-center align-items-center">
        <div class="col">
            <a href="{{ url('student') }}"
               class="text-secondary d-flex justify-content-center mt-4 {{ Request::is('student') || Request::is('student/dashboard') ? 'text-primary' : '' }}">
                <div class="nav-icon icon-home"></div>
            </a>
        </div>
        <div class="col">
            <a href="{{ url('student/hometask') }}"
               class="text-secondary  d-flex justify-content-center mt-4 {{ Request::is('student/hometask*') ? 'text-primary' : '' }}">
                <div class="nav-icon icon-homework"></div>
            </a>
        </div>
        <div class="col pt-3">
            <a href="{{ url('/games/platform') }}">
                <div class="cta-circle d-flex align-items-center justify-content-center mx-auto">
                    <img src="{{ asset('images/cta-circle.png') }}" alt="My Space">
                </div>
            </a>
        </div>
        <div class="col">
            <a href="{{ url('student/traning/create') }}"
               class="text-secondary  d-flex justify-content-center mt-4 {{ Request::is('student/traning*') ? 'text-primary' : '' }}">
                <div class="nav-icon icon-training"></div>
            </a>
        </div>
        <div class="col">
            <a href="{{ url('student/olympiad/create') }}"
               class="text-secondary  d-flex justify-content-center mt-4 {{ Request::is('student/olympiad*') ? 'text-primary' : '' }}">
                <div class="nav-icon icon-olympiad"></div>
            </a>
        </div>
    </div>
</div>
