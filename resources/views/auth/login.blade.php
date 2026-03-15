@extends('admin.layout.master')

@section('title', trans('admin.auth.login.title'))

@section('content')
<?php $user_agent = $_SERVER['HTTP_USER_AGENT']??'';
$stop=0;
if (stripos($user_agent,'Trident')>0){
    $stop=1;
}
if (stripos($user_agent,'MSIE')>0){
    $stop=1;
}
$lang=\App\Helpers\SiteHelper::GetLang();
$server_name = $_SERVER['SERVER_NAME'];

$main_domain = preg_replace('/^[^.]+\./', '', $server_name);


?>
@if ($stop)
<div style='border: 1px solid #F7941D; background: #FEEFDA; text-align: center; clear: both; height: 120px; position: relative;'>
    <div style='position: absolute; right: 3px; top: 3px; font-family: courier new; font-weight: bold;'>
        <a href='#' onclick='javascript:this.parentNode.parentNode.style.display="none"; return false;'>
            <img src='https://www.ie6nomore.com/files/theme/ie6nomore-cornerx.jpg' style='border: none;' alt='Close this notice' />
        </a>
    </div>
    <div style='width: 640px; margin: 0 auto; text-align: left; padding: 0; overflow: hidden; color: black;'>
        <div style='width: 75px; float: left;'>
            <img src='https://www.ie6nomore.com/files/theme/ie6nomore-warning.jpg' alt='Warning!' />
        </div>
        <div style='width: 275px; float: left; font-family: Arial, sans-serif;'>

            <div style='font-size: 12px; margin-top: 6px; line-height: 12px;'>Функція Internet Explorer застаріла перейдіть у браузер Chrome</div>
        </div>
        <div style='float: left;'>
            <a href='http://www.google.com/chrome' target='_blank'>
                <img src='https://www.ie6nomore.com/files/theme/ie6nomore-chrome.jpg' style='border: none;' alt='Get Google Chrome' />
            </a>
        </div>


    </div>
</div>
@endif
<?php
$showNewYear = false;
try {
    $today = new DateTime('now');
    $start = new DateTime('2026-01-01');
    $end = new DateTime('2026-01-08');
    $end->setTime(23, 59, 59); // включительно до конца дня
    if ($today >= $start && $today <= $end) {
        $showNewYear = true;
    }
} catch (Exception $e) {
    // при ошибке оставляем false
}
if (!empty($_REQUEST['test_ny'])) {
    $showNewYear = true;
}
$showNewYear=false;

?>
@if ($showNewYear)
@include('admin.auth.new_year')
@endif
    <div class="container" id="app">
        <div class="row align-items-center justify-content-center auth">

            @if (!empty($showSetLocationModal))
                {{-- Add ModalWIndows begin --}}
                    @include('admin.auth.includes.modal-show', [
                        'ipService' => $ipService,
                        'showSetLocationModal' => $showSetLocationModal
                    ])
                {{-- Add ModalWIndows end --}}
            @endif
            <div class="auth-logo"><img src="{{asset('/images/auth-logo.png')}}"></div>
            <div class="auth-form_container">
                <auth-form :action="/father/login" :data="{}" inline-template>
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/admin/login') }}" novalidate>
                        {{ csrf_field() }}
                        <div class="auth-body">
                            @include('admin.auth.includes.messages')

                            {{-- Spacy Character Section --}}
                            <div class="spacy-character-container">
                                <img id="spacy-character" src="{{asset('/images/spacy_login/1_static_email_start/spacy-14.webp')}}" alt="Spacy">
                            </div>

                            <div class="form-group"
                                 :class="{'has-danger': errors.has('email'), 'has-success': fields.email && fields.email.valid }">
                                <div class="input-group">
                                    <input type="text" v-model="form.email" v-validate="'required|email'"
                                           class="form-control"
                                           :class="{'form-control-danger': errors.has('email'), 'form-control-success': fields.email && fields.email.valid}"
                                           id="email" name="email"
                                           placeholder="{{ trans('admin.auth.email') }}">
                                </div>
                                <div v-if="errors.has('email')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('email') }}</div>
                            </div>

                            <div class="form-group"
                                 :class="{'has-danger': errors.has('password'), 'has-success': fields.password && fields.password.valid }">
                                <div class="input-group">
                                    <input type="password" v-model="form.password" class="form-control" :class="{'form-control-danger': errors.has('password'), 'form-control-success': fields.password && fields.password.valid}"
                                          id="password" name="password"
                                           placeholder="{{ trans('admin.auth.password') }}">
                                           <img id="type-switch" src="{{asset('/images/eye-regular.svg')}}" style="position: absolute; width: 24px; height: 24px; right: 10px; top: 10px; z-index: 10; cursor: pointer;">
                                </div>
                                <div v-if="errors.has('password')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('password') }}
                                </div>
                            </div>

                            <div class="form-group">
                                <input type="hidden" name="remember" value="1">
                                <button type="submit" class="btn auth-btn btn-spinner"><i class="fa"></i> {{ trans('admin.auth.login.button') }}</button>
                            </div>

{{--                            <div class="form-group text-center">--}}
{{--                                <a href="{{ url('/admin/password-reset') }}" class="auth-ghost-link">{{ trans('admin.auth.login.forgot_password') }}</a>--}}
{{--                            </div>--}}
                        <div class="d-flex align-items-center justify-content-center">

                            <div class="form-group d-flex align-items-center justify-content-center auth-lang" :class="{'has-danger': errors.has('language'), 'has-success': fields.language && fields.language.valid }">
                                <label for="language" class="col-form-label text-md-left">{{ trans('admin.franchisee.columns.language') }}</label>
                                <select id="language" name="language">
                                    <option value="ua" {{ $lang == 'ua' ? 'selected="selected"' : '' }}>ua</option>
                                    <option value="pl" {{ $lang == 'pl' ? 'selected="selected"' : '' }}>pl</option>
                                    <option value="en" {{ $lang == 'en' ? 'selected="selected"' : '' }}>en</option>
                                </select>
                            </div>
                        </div>
                        </div>
                    </form>
                </auth-form>
            </div>
        </div>


    </div>
<div class="cookiealert" role="alert">
    <div class="cookiealert-content">
        <div class="cookiealert-icon">&#x1F36A;</div>
        <div class="cookiealert-text">
            @if($lang == 'pl')
                Używamy plików cookie, aby zapewnić najlepsze wrażenia na naszej stronie.
                <a href="https://space-memory.pl/#popup:Polityka_prywatnosci_space_memory" target="_blank">Polityka prywatności</a>
            @elseif($lang == 'ua')
                Ми використовуємо cookies для забезпечення найкращого досвіду на нашому сайті.
                <a href="https://space-memory.pl/#popup:Polityka_prywatnosci_space_memory" target="_blank">Політика конфіденційності</a>
            @else
                We use cookies to ensure the best experience on our website.
                <a href="https://space-memory.pl/#popup:Polityka_prywatnosci_space_memory" target="_blank">Privacy Policy</a>
            @endif
        </div>
        <button type="button" class="cookiealert-accept acceptcookies">
            @if($lang == 'pl')
                Zgoda
            @elseif($lang == 'ua')
                Добре
            @else
                Accept
            @endif
        </button>
    </div>
</div>
@endsection


@section('bottom-scripts')

    <script src="{{ asset('js/cookiealert.js') }}"></script>

    <script type="text/javascript">

        var main_domain = '{{$main_domain}}';

        // fix chrome password autofill
        // https://github.com/vuejs/vue/issues/1331
        document.getElementById('password').dispatchEvent(new Event('input'));

        // SPACY ANIMATION - ПРОСТА ЛОГІКА
        (function() {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const spacyImg = document.getElementById('spacy-character');
            const toggleBtn = document.getElementById('type-switch');

            // Завантаження картинок
            const SPACY = {};
            for (let i = 1; i <= 8; i++) SPACY[i] = "{{asset('/images/spacy_login/5_h_password_s_password/spacy-')}}" + i + ".webp";
            for (let i = 8; i <= 14; i++) SPACY[i] = "{{asset('/images/spacy_login/4_static_password/spacy-')}}" + i + ".webp";
            for (let i = 14; i <= 21; i++) SPACY[i] = "{{asset('/images/spacy_login/1_static_email_start/spacy-')}}" + i + ".webp";
            for (let i = 22; i <= 41; i++) SPACY[i] = "{{asset('/images/spacy_login/2_email_typing_progress/spacy-')}}" + i + ".webp";
            for (let i = 41; i <= 51; i++) SPACY[i] = "{{asset('/images/spacy_login/3_email_end_static/spacy-')}}" + i + ".webp";

            // Стан
            let activeField = null;
            let isPasswordVisible = false;
            let blinkTimer = null;

            // Preload
            for (let num in SPACY) { const img = new Image(); img.src = SPACY[num]; }

            // Показати картинку
            function show(num) {
                spacyImg.src = SPACY[num];
            }

            // Анімація
            function animate(frames, delay, callback) {
                let i = 0;
                function next() {
                    if (i >= frames.length) { if (callback) callback(); return; }
                    show(frames[i++]);
                    setTimeout(next, delay);
                }
                next();
            }

            // Позиція каретки → номер картинки (25-36)
            function getCaretImage() {
                if (emailInput.value.length === 0) return 21;

                const caret = emailInput.selectionStart;
                const text = emailInput.value.substring(0, caret);

                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                const style = window.getComputedStyle(emailInput);
                ctx.font = style.fontSize + ' ' + style.fontFamily;
                const textWidth = ctx.measureText(text).width;

                const fieldWidth = Math.min(415 - 48, emailInput.offsetWidth - 48) || 367;
                const progress = Math.min(1, textWidth / fieldWidth);

                return Math.min(36, Math.max(25, 25 + Math.floor(progress * 11)));
            }

            // ============ EMAIL ============

            // Функція для запуску моргання кожні 3 секунди
            function startBlinkLoop() {
                clearTimeout(blinkTimer);
                blinkTimer = setTimeout(function() {
                    if (activeField === 'email' && emailInput.value.length === 0) {
                        animate([23, 21], 100, function() {
                            // Після моргання - запустити наступне через 3 секунди
                            startBlinkLoop();
                        });
                    }
                }, 3000);
            }

            // Email focus
            emailInput.addEventListener('focus', function() {
                activeField = 'email';
                clearTimeout(blinkTimer);

                // Якщо порожнє → анімація 15-21, потім blink кожні 3 сек
                if (emailInput.value.length === 0) {
                    animate([15, 16, 17, 18, 19, 20, 21], 50, function() {
                        if (activeField === 'email' && emailInput.value.length === 0) {
                            startBlinkLoop();
                        }
                    });
                } else {
                    // Якщо є текст → показати позицію каретки
                    show(getCaretImage());
                }
            });

            // Email input
            emailInput.addEventListener('input', function() {
                if (activeField !== 'email') return;
                clearTimeout(blinkTimer);

                // Якщо поле стало порожнім → показати 21 і запустити цикл моргання
                if (emailInput.value.length === 0) {
                    show(21);
                    startBlinkLoop();
                } else {
                    show(getCaretImage());
                }
            });

            // Email caret move
            function updateCaret() {
                if (activeField === 'email' && emailInput.value.length > 0) show(getCaretImage());
            }
            emailInput.addEventListener('click', updateCaret);
            emailInput.addEventListener('keyup', function(e) {
                if ([37, 38, 39, 40, 35, 36].indexOf(e.keyCode) !== -1) updateCaret();
            });
            emailInput.addEventListener('mouseup', updateCaret);

            // Email blur
            emailInput.addEventListener('blur', function() {
                activeField = null;
                clearTimeout(blinkTimer);
                setTimeout(function() { if (!activeField) show(14); }, 100);
            });

            // ============ PASSWORD ============

            // Password focus
            passwordInput.addEventListener('focus', function() {
                activeField = 'password';

                // Якщо показано → просто показати відкриті очі (peek анімація тільки при кліку toggle)
                if (isPasswordVisible) {
                    show(1);
                } else {
                    // Якщо приховано → закрити 13-8
                    animate([13, 12, 11, 10, 9, 8], 80);
                }
            });

            // Password blur
            passwordInput.addEventListener('blur', function() {
                activeField = null;

                // Якщо було приховано → відкрити 8-13
                if (!isPasswordVisible) {
                    animate([8, 9, 10, 11, 12, 13], 80, function() {
                        setTimeout(function() { if (!activeField) show(14); }, 100);
                    });
                } else {
                    setTimeout(function() { if (!activeField) show(14); }, 100);
                }
            });

            // ============ TOGGLE ============

            toggleBtn.addEventListener('mouseenter', function() { activeField = 'toggle'; });
            toggleBtn.addEventListener('mouseleave', function() { if (activeField === 'toggle') activeField = null; });

            toggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const field = document.getElementById('password');
                const willShow = field.type === 'password';

                if (willShow) {
                    // Показати пароль → peek анімація 8-4-1
                    isPasswordVisible = true;
                    field.type = 'text';
                    animate([8, 4, 1], 120, function() {
                        // Після завершення анімації - повернути фокус
                        passwordInput.focus();
                        const len = passwordInput.value.length;
                        passwordInput.setSelectionRange(len, len);
                    });
                } else {
                    // Сховати пароль → зворотна peek 1-7-8
                    isPasswordVisible = false;
                    field.type = 'password';
                    animate([1, 7, 8], 120, function() {
                        // Після завершення анімації - повернути фокус
                        passwordInput.focus();
                        const len = passwordInput.value.length;
                        passwordInput.setSelectionRange(len, len);
                    });
                }
            });

            // Ініціалізація
            show(14);
        })();

        document.getElementById('language').addEventListener('change', function() {
            var lang = this.value;


            if (main_domain=='firm.kiev.ua'){
                @if ($main_domain=='firm.kiev.ua')
                if (lang=='en'){
                    window.location.href =  'https://memory-en.firm.kiev.ua';
                }
                if (lang=='ua'){
                    window.location.href =  'https://memory.firm.kiev.ua';
                }
                if (lang=='pl'){
                    window.location.href =  'https://memory-pl.firm.kiev.ua';
                }
                @endif
            }else{
                window.location.href =  'https://'+lang+'.'+main_domain;
            }

        });


    </script>
@endsection
