@extends('student.layout.default')

@section('body')
<?php
$zoom['zoom_url']=$zoom['zoom_url']??'';
$zoom['zoom_text']=$zoom['zoom_text']?? trans('student.index.online_now');

// Get language first
$lang=\App\Helpers\SiteHelper::GetLang();

if (!empty($zoom['zoom_url'])){
    $scheme= parse_url($zoom['zoom_url'],PHP_URL_SCHEME);
    if (!isset($scheme)) {
        $zoom['zoom_url'] = '//' . $zoom['zoom_url'];
    }
}
// Set news URL to Instagram based on language
$url_news = 'https://www.instagram.com/indigomental.pl/';
if ($lang == 'uk') {
    $url_news = 'https://www.instagram.com/spacememory.ukraine';
}


if (empty($zoom['zoom_img'])) {
    $zoom['zoom_img'] = asset('images/ava_online.png');
}
$ads['url']=$ads['url']??'#';
?>

<div class="content-grid">
    {{-- Item 1: Profile --}}
    <div class="grid-item item1 dashboard-profile">
        <div class="profile-avatar">
            <img id="avatar-img" class="{{ empty($userInfo['photo']) ? 'default-avatar' : '' }}" src="{{$userInfo['photo'] ?? asset('images/default_avatar.png')}}" alt="avatar" onerror="this.onerror=null,this.src='{{asset('images/default_avatar.png')}}',this.classList.add('default-avatar')">
            <div class="avatar-edit-overlay" onclick="document.getElementById('avatar-upload').click();">
                <img src="{{asset('images/avatar-edit.png')}}" alt="edit">
            </div>
            <input type="file" name="photo" id="avatar-upload" style="display: none;" accept="image/*">
        </div>
        <div class="profile-name">{{@$userInfo['surname']}} {{@$userInfo['lastname']}}</div>
    </div>

    {{-- Item 2: Trainer --}}
    <div class="grid-item item2 dashboard-block dashboard-trainer">
        <div class="trainer-icon ">
            <img src="{{@$teacher['avatar'] ?: asset('images/default_avatar.png')}}" alt="{{@$teacher['name']}}" class="{{ empty($teacher['avatar']) ? 'default-avatar' : '' }}" onerror="this.onerror=null,this.src='{{asset('images/default_avatar.png')}}',this.classList.add('default-avatar')">
        </div>
        <div class="trainer-info">
            <div class="trainer-name">{{@$teacher['name']}}</div>
            <div class="trainer-title">{{ trans('student.index.trainer_title') }}</div>
                    </div>
                        </div>

    {{-- Item 3: Trainer --}}
    <div class="grid-item item3 dashboard-block dashboard-group">
        <div class="group-info">
            <div class="group-name">{{@$userInfo['groupName']}}</div>
            <div class="group-label">{{ trans('student.index.group') }}</div>
        </div>
    </div>

    {{-- Item 4: Stats --}}
    <div class="grid-item item4 dashboard-block dashboard-coin">
            <img src="{{asset('images/space_coins.png')}}" alt="coins" class="coin-icon">
            <div class="stat-content">
                <div class="stat-value">{{@$userInfo['balance']}}</div>
                <div class="stat-label">{{ trans('game.modal_coins.coins') }}</div>
            </div>
    </div>
    {{-- Item 5: Stats --}}
    <div class="grid-item item5 dashboard-block dashboard-diamond">
        <img src="{{asset('images/space_diamond.webp')}}" alt="trainings" class="diamond-icon">
        <div class="stat-content">
            <div class="stat-value">{{@$userInfo['diams']}}</div>
            <div class="stat-label">{{ trans('game.modal_coins.diams') }}</div>
        </div>
    </div>
    {{-- Item 6: Stats --}}
    <div class="grid-item item6 dashboard-block dashboard-first-step">
        <img src="{{$userInfo['rank_img']}}" alt="trainings" class="first-step-icon">
        <div class="stat-content">
            <div class="stat-value">{{$userInfo['rank_name']}}</div>
                    </div>
                </div>

    {{-- Item 7: Homework Action --}}
    <div class="grid-item item7 dashboard-block dashboard-actions">
        <a href="{{ url('student/hometask') }}" class="action-card h-100 d-flex flex-column">
            <div class="action-icon mb-0">
                <img src="{{asset('images/home_work.png')}}" alt="homework" class="action-icon-img">
            </div>
            <div class="action-label mt-auto">
                {{ trans('student.index.homework') }}
{{--                <img src="{{asset('images/arrow_next.svg')}}" alt="arrow" class="action-arrow">--}}
        </div>
                </a>
            </div>

    {{-- Item 8: Zoom Action --}}
    <div class="grid-item item8 dashboard-block dashboard-actions">
        @if(!empty($zoom['zoom_url']))
        <a href="{{$zoom['zoom_url']}}" target="_blank" class="action-card h-100 d-flex flex-column">
            <div class="action-icon mb-0">
                <img src="{{asset('images/zoom.png')}}" alt="zoom" class="action-icon-img">
            </div>
            <div class="action-label mt-auto">
                {{ trans('student.index.online_now') }}
{{--                <img src="{{asset('images/arrow_next.svg')}}" alt="arrow" class="action-arrow">--}}
            </div>
        </a>
        @else
        <div class="action-card action-card-disabled h-100 d-flex flex-column">
            <div class="action-icon mb-0">
                <img src="{{asset('images/zoom.png')}}" alt="zoom" class="action-icon-img">
            </div>
            <div class="action-label action-label-disabled mt-auto">
                {{ trans('student.index.no_zoom_link') }}
            </div>
        </div>
        @endif
    </div>

    {{-- Item 9: Space Loot (Last Transaction) --}}
    <div class="grid-item item9 dashboard-block dashboard-time">
        <div class="time-label">{{ trans('student.index.space_loot') }}</div>
        @if(!empty($lastBalanceLog))
            <div class="time-value dashboard-loot-accrual">
                @if($lastBalanceLog->coins != 0)
                    <div class="loot-item">
                        <img src="{{asset('images/space_coins.webp')}}" alt="coins" class="loot-icon">
                        <span class="loot-value {{ $lastBalanceLog->coins > 0 ? 'positive' : 'negative' }}">
                            {{ $lastBalanceLog->coins > 0 ? '+' : '' }}{{ $lastBalanceLog->coins }}
                        </span>
                    </div>
                @endif
                @if($lastBalanceLog->diams != 0)
                    <div class="loot-item">
                        <img src="{{asset('images/space_diamond.webp')}}" alt="diamonds" class="loot-icon">
                        <span class="loot-value {{ $lastBalanceLog->diams > 0 ? 'positive' : 'negative' }}">
                            {{ $lastBalanceLog->diams > 0 ? '+' : '' }}{{ $lastBalanceLog->diams }}
                        </span>
                    </div>
                @endif
            </div>
        @else
            <div class="time-value dashboard-loot-accrual">
                <div class="loot-item">
                    <img src="{{asset('images/space_coins.webp')}}" alt="coins" class="loot-icon">
                    <span class="loot-value">0</span>
                </div>
            </div>
        @endif
        <div class="time-chart">
            <img src="{{asset('images/stat_line.svg')}}" alt="statistics" class="time-chart-img">
        </div>
        <a href="{{ url('student/bonus-history') }}" class="time-link">
            {{ trans('student.index.statistic') }}
{{--            <img src="{{asset('images/arrow_next.svg')}}" alt="arrow" class="time-link-arrow">--}}
        </a>
        </div>

    {{-- Item 10: Rating --}}
    <div class="grid-item item10 dashboard-block dashboard-rating d-flex flex-column">
{{--        Автоматично брати рейтинг з бази даних і показувати + лінка на сторінку --}}
{{--        <div class="rating-list w-100">--}}
{{--            @for($i = 0; $i < 3; $i++)--}}
{{--                @php--}}
{{--                    $student = $ranking[$i] ?? null;--}}
{{--                @endphp--}}
{{--                <div class="rating-item d-flex flex-row w-100 mb-2">--}}
{{--                    <img src="{{asset('images/rating' . ($i + 1) . '.png')}}" alt="user" class="rating-image">--}}
{{--                    @if($student)--}}
{{--                        <span class="rating-name mr-auto ml-2 my-auto">{{ $student['name'] }}</span>--}}
{{--                        <div class="rating-value my-auto">{{ $student['balance'] }}</div>--}}
{{--                    @else--}}
{{--                        <span class="rating-name mr-auto ml-2 my-auto">{{ trans('student.index.no_data') }}</span>--}}
{{--                        <div class="rating-value my-auto">0</div>--}}
{{--                    @endif--}}
{{--                </div>--}}
{{--            @endfor--}}
{{--        </div>--}}
{{--        <a href="{{ url('/student/ranking') }}" class="rating-more w-100">--}}
{{--            {{ trans('student.index.rating') }}--}}
{{--        </a>--}}

        <div class="rating-list w-100">
            <div class="rating-item d-flex flex-row w-100 mb-2">
                <img src="{{asset('images/rating1.png')}}" alt="user" class="rating-image">
                <span class="rating-name mr-auto ml-2 my-auto">Mario</span>
                <div class="rating-value my-auto">302</div>
            </div>
            <div class="rating-item d-flex flex-row w-100 mb-2">
                <img src="{{asset('images/rating2.png')}}" alt="user" class="rating-image">
                <span class="rating-name mr-auto ml-2 my-auto">Alexander</span>
                <div class="rating-value my-auto">273</div>
            </div>
            <div class="rating-item d-flex flex-row w-100 mb-2">
                <img src="{{asset('images/rating3.png')}}" alt="user" class="rating-image">
                <span class="rating-name mr-auto ml-2 my-auto">Przemysław</span>
                <div class="rating-value my-auto">240</div>
            </div>
        </div>
        <div class="rating-more w-100">
            {{ trans('student.index.rating') }}
        </div>
    </div>

    {{-- Item 11: News - Full block is clickable --}}
    <a href="{{$url_news}}" target="_blank" class="grid-item item11 dashboard-block dashboard-news dashboard-news-link">
        <div class="content-news">
            <span class="news-link">
                {{ trans('student.index.news') }}
            </span>
        </div>
    </a>

    {{-- Item 12: Character (Desktop only) --}}
    <div class="grid-item item12 dashboard-character">
        <a href="{{ url('/games/platform') }}" class="character-avatar w-100 d-flex" style="cursor: pointer;">
{{--            <img src="{{asset('images/character-avatar.png')}}" alt="character" class="w-100 mb-auto">--}}
            <img src="/images/games/cloth/pers/{{ @$userInfo['model'] }}" alt="character" class="character-avatar-img">
        </a>
    </div>
</div>

@if (!empty($bonusAll))
<gamebonus :data="{{ json_encode($bonusAll) }}" inline-template ref="gamebonus">
    @include('games.modal.coins-help')
</gamebonus>
@endif

@endsection
@section('bottom-scripts')


<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modalBtns = document.querySelectorAll('.modal__btn-js');
        const modals = document.querySelectorAll('.game__modal');

        function toggleModalDisplay(modalId, display) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = display;
            }
        }

        function closeModal(modal) {
            if (modal) {
                modal.style.display = 'none';
                modal.setAttribute('aria-hidden', 'true');

                const modalId = modal.getAttribute('id');
                if (modalId === 'specialModalHelpCoins') {
                    toggleModalDisplay('specialModal5', 'flex');
                } else if (modalId === 'specialModal8') {
                    toggleModalDisplay('specialModal6', 'flex');
                }

                const openerButtonId = modal.getAttribute('data-opener');
                if (openerButtonId) {
                    const openerButton = document.getElementById(openerButtonId);
                    openerButton?.focus();
                }
            }
        }

        modalBtns.forEach((btn) => {
            btn.addEventListener('click', () => {
                const modalId = btn.getAttribute('data-modal');
                openModal(modalId);
                btn.setAttribute('data-opener', modalId);
            });
        });

        modals.forEach((modal) => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    closeModal(modal);
                }
            });

            const closeBtn = modal.querySelector('.close-modal');
            closeBtn?.addEventListener('click', () => closeModal(modal));
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                modals.forEach((modal) => {
                    if (modal.style.display === 'flex' && modal.getAttribute('aria-hidden') === 'false') {
                        closeModal(modal);
                    }
                });
            }
        });
    });

    $(document).ready(function () {
        $('#specialModalHelpCoins').css('display', 'flex');

        $('#avatar-upload').on('change', function() {
            let formData = new FormData();
            let file = this.files[0];
            if (file) {
                formData.append('photo', file);

                $.ajax({
                    url: "{{ route('student/upload-photo') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.photoUrl) {
                            $('#avatar-img').attr('src', response.photoUrl + '?' + new Date().getTime());
                        }
                    },
                    error: function(xhr) {
                        let message = 'Error uploading photo';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            message = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        alert(message);
                        console.error(xhr);
                    }
                });
            }
        });
    });

    function confirmDialog(message, onConfirm,subject='') {
        if (message!=''){
            $('#confirmMessage').text(message);
        }
        if (subject!=''){
            $('#confirmSubject').text(subject);
        }else{
            $('#confirmSubject').hide();
        }

        $('#overlay, #confirmDialog').fadeIn();

        // очищаємо старі обробники, щоб не дублювались
        $('#confirmYes').off('click').on('click', function () {
            $('#overlay, #confirmDialog').fadeOut();
            onConfirm(1);
        });
        $('.btn-close').off('click').on('click', function () {
            $('#overlay, #confirmDialog').fadeOut();

        });
        $('#confirmNo').off('click').on('click', function () {
            $('#overlay, #confirmDialog').fadeOut();

        });
    }
</script>
@endsection
