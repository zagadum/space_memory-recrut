@extends('student.layout.theme.master_student')

@section('styles')
<style>
/* ============================================================
   PARENT PORTAL — PORTAL RODZICA
   Space Memory dark theme, designed from scratch
   ============================================================ */

:root {
    --teal:       #26F9FF;
    --teal-dim:   rgba(38, 249, 255, 0.12);
    --teal-glow:  rgba(38, 249, 255, 0.25);
    --gold:       #F5BE30;
    --bg:         #04151d;
    --surface-1:  #0d2535;
    --surface-2:  #112d40;
    --border:     rgba(38, 249, 255, 0.12);
    --text:       #f2f2f2;
    --text-muted: rgba(242, 242, 242, 0.5);
    --radius-lg:  20px;
    --radius-md:  14px;
    --shadow:     0 4px 24px rgba(0, 0, 0, 0.35);
}

body { background: var(--bg) !important; }
.content-area { background: var(--bg) !important; padding: 0 !important; }
header.d-lg-none {
    background: var(--bg) !important;
    border-bottom: 1px solid var(--border);
}

/* ============================================================
   PAGE WRAPPER
   ============================================================ */
.parent-portal {
    min-height: 100vh;
    padding: 44px 52px 60px;
    background: var(--bg);
    color: var(--text);
    font-family: 'Roboto', sans-serif;
    position: relative;
    overflow: hidden;
}

/* Фоновый декор — две glowing-точки */
.parent-portal::before,
.parent-portal::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
}
.parent-portal::before {
    top: -120px;
    right: -80px;
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(38,249,255,0.07) 0%, transparent 65%);
}
.parent-portal::after {
    bottom: 60px;
    left: -100px;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(38,249,255,0.04) 0%, transparent 65%);
}

.parent-portal > * { position: relative; z-index: 1; }

/* ============================================================
   PROFILE HEADER
   ============================================================ */
.pp-header {
    display: flex;
    align-items: center;
    gap: 24px;
    margin-bottom: 48px;
}

.pp-header__avatar-wrap {
    position: relative;
    flex-shrink: 0;
}

.pp-header__avatar {
    width: 88px;
    height: 88px;
    border-radius: 22px;
    border: 2.5px solid var(--teal);
    object-fit: cover;
    display: block;
    box-shadow: 0 0 20px rgba(38,249,255,0.2);
}

.pp-header__edit-btn {
    position: absolute;
    bottom: -6px;
    right: -6px;
    width: 28px;
    height: 28px;
    background: var(--teal);
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    transition: transform 0.15s, box-shadow 0.15s;
    box-shadow: 0 0 10px rgba(38,249,255,0.4);
}
.pp-header__edit-btn:hover {
    transform: scale(1.15);
    box-shadow: 0 0 16px rgba(38,249,255,0.7);
}

.pp-header__info {}

.pp-header__label {
    font-size: 11px;
    font-weight: 500;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: var(--teal);
    opacity: 0.75;
    margin-bottom: 4px;
}

.pp-header__name {
    font-size: 34px;
    font-weight: 700;
    margin: 0;
    color: #fff;
    line-height: 1.1;
    letter-spacing: -0.5px;
}

/* Горизонтальная cyan-линия под именем */
.pp-header__line {
    display: block;
    width: 48px;
    height: 3px;
    background: linear-gradient(to right, var(--teal), transparent);
    border-radius: 2px;
    margin-top: 10px;
}

/* ============================================================
   ACTION CARDS — 3 штуки
   ============================================================ */
.pp-actions {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 32px;
}

.pp-action-card {
    position: relative;
    background: var(--surface-1);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 32px 28px 28px;
    text-decoration: none;
    color: var(--text);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transition: border-color 0.25s, transform 0.25s, box-shadow 0.25s;
    cursor: pointer;
}

/* Мягкий teal градиент при hover */
.pp-action-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(38,249,255,0.06) 0%, transparent 60%);
    opacity: 0;
    transition: opacity 0.3s;
    border-radius: var(--radius-lg);
}

.pp-action-card:hover {
    border-color: rgba(38,249,255,0.45);
    transform: translateY(-5px);
    box-shadow: 0 12px 36px rgba(0,0,0,0.4), 0 0 0 1px rgba(38,249,255,0.15);
    color: var(--text);
    text-decoration: none;
}
.pp-action-card:hover::before { opacity: 1; }

/* Иконка */
.pp-action-card__icon {
    width: 72px;
    height: 72px;
    object-fit: contain;
    margin-bottom: 24px;
    flex-shrink: 0;
    transition: transform 0.3s;
}
.pp-action-card:hover .pp-action-card__icon {
    transform: scale(1.08) translateY(-2px);
}

/* Нижняя строка: название + стрелка */
.pp-action-card__footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: auto;
}

.pp-action-card__title {
    font-size: 18px;
    font-weight: 700;
    color: #fff;
    letter-spacing: -0.2px;
}

.pp-action-card__arrow {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 1.5px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--teal);
    font-size: 13px;
    flex-shrink: 0;
    transition: background 0.2s, border-color 0.2s;
}
.pp-action-card:hover .pp-action-card__arrow {
    background: var(--teal-dim);
    border-color: rgba(38,249,255,0.4);
}

/* Декоративный угловой акцент */
.pp-action-card::after {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 60px;
    height: 60px;
    background: radial-gradient(circle at top right, rgba(38,249,255,0.08), transparent 70%);
    border-radius: 0 var(--radius-lg) 0 0;
    pointer-events: none;
}

/* ============================================================
   WELCOME CARD
   ============================================================ */
.pp-welcome {
    background: var(--surface-1);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 40px 48px;
    box-shadow: var(--shadow);
}

/* Заголовок карточки */
.pp-welcome__head {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 10px;
}

.pp-welcome__emoji {
    font-size: 32px;
    line-height: 1;
    flex-shrink: 0;
}

.pp-welcome__title {
    font-size: 24px;
    font-weight: 700;
    color: #fff;
    margin: 0;
    letter-spacing: -0.3px;
}

.pp-welcome__subtitle {
    font-size: 15px;
    color: var(--text-muted);
    margin: 0 0 40px 46px; /* align с заголовком */
    line-height: 1.5;
}

/* ============================================================
   STEPS TIMELINE
   ============================================================ */
.pp-steps {
    display: flex;
    flex-direction: column;
    gap: 0;
}

.pp-step {
    display: grid;
    grid-template-columns: 48px 1fr;
    gap: 0 24px;
    position: relative;
}

/* Левая колонка: номер + вертикальная линия */
.pp-step__aside {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.pp-step__num {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0f2d3e, #1a4055);
    border: 1.5px solid rgba(38,249,255,0.35);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 700;
    color: var(--teal);
    flex-shrink: 0;
    box-shadow: 0 0 14px rgba(38,249,255,0.15);
    position: relative;
    z-index: 1;
    transition: box-shadow 0.2s, border-color 0.2s;
}

.pp-step:hover .pp-step__num {
    border-color: rgba(38,249,255,0.7);
    box-shadow: 0 0 22px rgba(38,249,255,0.3);
}

/* Вертикальная линия между шагами */
.pp-step__line {
    width: 1.5px;
    flex: 1;
    background: linear-gradient(to bottom, rgba(38,249,255,0.3), rgba(38,249,255,0.05));
    margin: 4px 0;
    min-height: 24px;
}

/* Последний шаг — без линии */
.pp-step:last-child .pp-step__line {
    display: none;
}

/* Правая колонка: контент */
.pp-step__body {
    padding-bottom: 36px;
}

.pp-step:last-child .pp-step__body {
    padding-bottom: 0;
}

.pp-step__title {
    font-size: 17px;
    font-weight: 700;
    color: #fff;
    margin: 0 0 10px;
    line-height: 48px; /* выравниваем с кружком */
}

.pp-step__text {
    font-size: 14px;
    color: var(--text-muted);
    line-height: 1.65;
    margin: 0;
}

/* Тег телефона */
.pp-step__phone {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-top: 10px;
    background: var(--teal-dim);
    border: 1px solid rgba(38,249,255,0.2);
    border-radius: 8px;
    padding: 7px 14px;
    font-size: 14px;
    font-weight: 600;
    color: var(--teal);
    text-decoration: none;
    transition: background 0.2s;
}
.pp-step__phone:hover {
    background: rgba(38,249,255,0.18);
    color: var(--teal);
    text-decoration: none;
}

/* Список вариантов */
.pp-step__list {
    list-style: none;
    padding: 0;
    margin: 10px 0 0;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.pp-step__list li {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    color: var(--text-muted);
}

.pp-step__list li::before {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--teal);
    opacity: 0.6;
    flex-shrink: 0;
}

/* Теги способов оплаты */
.pp-step__tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
}

.pp-step__tag {
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 6px 14px;
    font-size: 13px;
    color: rgba(242,242,242,0.7);
}

/* Заметка курсивом */
.pp-step__note {
    font-size: 12px;
    color: rgba(242,242,242,0.35);
    font-style: italic;
    margin-top: 8px;
    line-height: 1.5;
}

/* ============================================================
   CONTRACT BANNER
   ============================================================ */
.pp-contract-banner {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 20px 28px;
    border-radius: var(--radius-md);
    border: 1px solid;
    margin-bottom: 28px;
    position: relative;
    overflow: hidden;
}
.pp-contract-banner--warn {
    background: rgba(245, 190, 48, 0.07);
    border-color: rgba(245, 190, 48, 0.35);
}
.pp-contract-banner--ok {
    background: rgba(74, 222, 128, 0.06);
    border-color: rgba(74, 222, 128, 0.25);
}
.pp-contract-banner__icon {
    font-size: 26px;
    flex-shrink: 0;
    line-height: 1;
}
.pp-contract-banner__body { flex: 1; }
.pp-contract-banner__title {
    font-size: 15px;
    font-weight: 700;
    color: #fff;
    margin: 0 0 4px;
}
.pp-contract-banner--warn .pp-contract-banner__title { color: var(--gold); }
.pp-contract-banner--ok  .pp-contract-banner__title { color: #4ade80; }
.pp-contract-banner__text {
    font-size: 13px;
    color: var(--text-muted);
    margin: 0;
    line-height: 1.5;
}
.pp-contract-banner__btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 22px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    white-space: nowrap;
    flex-shrink: 0;
    transition: opacity .2s, transform .15s;
}
.pp-contract-banner__btn:hover {
    opacity: .85;
    transform: translateY(-2px);
    text-decoration: none;
}
.pp-contract-banner__btn--sign {
    background: linear-gradient(135deg, var(--gold), #e09c18);
    color: #1a0f00;
}
.pp-contract-banner__btn--view {
    background: rgba(74, 222, 128, 0.12);
    border: 1px solid rgba(74, 222, 128, 0.3);
    color: #4ade80;
}
@media (max-width: 575px) {
    .pp-contract-banner { flex-wrap: wrap; }
    .pp-contract-banner__btn { width: 100%; justify-content: center; }
}

/* ============================================================
   АДАПТИВ
   ============================================================ */
@media (max-width: 991px) {
    .parent-portal {
        padding: 24px 20px 110px;
    }

    .pp-header { margin-bottom: 32px; }
    .pp-header__name { font-size: 26px; }
    .pp-header__avatar { width: 68px; height: 68px; border-radius: 16px; }

    .pp-actions {
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .pp-welcome { padding: 28px 24px; }
    .pp-welcome__subtitle { margin-left: 0; }
}

@media (max-width: 575px) {
    .pp-actions {
        grid-template-columns: 1fr;
    }

    .pp-action-card {
        flex-direction: row;
        align-items: center;
        padding: 20px 22px;
        gap: 20px;
    }

    .pp-action-card__icon {
        width: 52px;
        height: 52px;
        margin-bottom: 0;
        flex-shrink: 0;
    }

    .pp-action-card__footer {
        flex: 1;
    }

    .pp-action-card__title { font-size: 16px; }
}
</style>
@endsection

@section('content')

<div class="parent-portal">

    {{-- ======================== PROFILE HEADER ======================== --}}
    <header class="pp-header">
        <div class="pp-header__avatar-wrap">
            <img src="{{ $student->avatar_url ?? asset('images/ava.png') }}"
                 alt="Avatar"
                 class="pp-header__avatar">
            <button class="pp-header__edit-btn" title="Изменить фото">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
                    <path d="M7 16l3-1 7-7-2-2-7 7-1 3m10-10l2 2"
                          stroke="#04151d" stroke-width="2.5"
                          stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
        <div class="pp-header__info">
            <p class="pp-header__label">Strefa rodzica</p>
            <h1 class="pp-header__name">
                {{ $student->name ?? '' }} {{ $student->surname ?? '' }}
            </h1>
            <p style="color: rgba(255,255,255,0.6); font-size: 14px; margin-top: 5px;">
                {{ $student->group?->name ?? 'Не назначена' }}
            </p>
            <span class="pp-header__line"></span>
        </div>
    </header>

    {{-- ======================== CONTRACT BANNER ======================== --}}
    @if(!$hasSignedContract)
        <div class="pp-contract-banner pp-contract-banner--warn">
            <div class="pp-contract-banner__icon">📋</div>
            <div class="pp-contract-banner__body">
                <p class="pp-contract-banner__title">Подпишите договор</p>
                <p class="pp-contract-banner__text">
                    Для доступа к занятиям и оплатам необходимо ознакомиться с договором и поставить электронную подпись.
                </p>
            </div>
            @if($contractDoc)
                <a href="{{ route('father.document.view', $contractDoc->id) }}" class="pp-contract-banner__btn pp-contract-banner__btn--sign">
                    <i class="fas fa-pen-nib"></i>
                    Подписать
                </a>
            @else
                <a href="{{ route('father.documents') }}" class="pp-contract-banner__btn pp-contract-banner__btn--sign">
                    <i class="fas fa-file-contract"></i>
                    К документам
                </a>
            @endif
        </div>
    @else
        <div class="pp-contract-banner pp-contract-banner--ok">
            <div class="pp-contract-banner__icon">✅</div>
            <div class="pp-contract-banner__body">
                <p class="pp-contract-banner__title">Договор подписан</p>
                <p class="pp-contract-banner__text">
                    Вы подписали договор
                    @if($contractDoc && $contractDoc->sign_date)
                        {{ optional($contractDoc->sign_date)->format('d.m.Y') }}
                    @endif
                    — можно переходить к оплате.
                </p>
            </div>
            @if($contractDoc)
                <a href="{{ route('father.document.view', $contractDoc->id) }}" class="pp-contract-banner__btn pp-contract-banner__btn--view">
                    <i class="fas fa-eye"></i>
                    Просмотреть
                </a>
            @endif
        </div>
    @endif

    {{-- ======================== ACTION CARDS ======================== --}}
    <div class="pp-actions">

        <a href="{{ route('father.documents') }}" class="pp-action-card">
            <div class="pp-action-card__icon" style="font-size: 48px; display: flex; align-items: center; justify-content: center; background: none !important; border: none !important;">
                📄
            </div>
            <div class="pp-action-card__footer">
                <span class="pp-action-card__title">Документы</span>
                <span class="pp-action-card__arrow">
                    <i class="fas fa-arrow-right"></i>
                </span>
            </div>
        </a>

        <a href="{{ route('father.payment') }}" class="pp-action-card">
            <div class="pp-action-card__icon" style="font-size: 48px; display: flex; align-items: center; justify-content: center; background: none !important; border: none !important;">
                💳
            </div>
            <div class="pp-action-card__footer">
                <span class="pp-action-card__title">Оплаты</span>
                <span class="pp-action-card__arrow">
                    <i class="fas fa-arrow-right"></i>
                </span>
            </div>
        </a>

        <a href="/father/learn" class="pp-action-card">
            <div class="pp-action-card__icon" style="font-size: 48px; display: flex; align-items: center; justify-content: center; background: none !important; border: none !important;">
                🎓
            </div>
            <div class="pp-action-card__footer">
                <span class="pp-action-card__title">Обучение для родителей</span>
                <span class="pp-action-card__arrow">
                    <i class="fas fa-arrow-right"></i>
                </span>
            </div>
        </a>

    </div>

    {{-- ======================== WELCOME CARD ======================== --}}
    <div class="pp-welcome">

        <div class="pp-welcome__head">
            <span class="pp-welcome__emoji">👋</span>
            <h2 class="pp-welcome__title">Добро пожаловать в Портал родителя</h2>
        </div>
        <p class="pp-welcome__subtitle">
            Чтобы начать обучение, пожалуйста, выполните несколько простых шагов:
        </p>

        <div class="pp-steps">

            {{-- ШАГ 1 --}}
            <div class="pp-step">
                <div class="pp-step__aside">
                    <div class="pp-step__num" @if($hasSignedContract) style="background: linear-gradient(135deg,#14532d,#166534);border-color:rgba(74,222,128,0.5);color:#4ade80;" @endif>
                        @if($hasSignedContract)<i class="fas fa-check" style="font-size:14px;"></i>@else 1 @endif
                    </div>
                    <div class="pp-step__line"></div>
                </div>
                <div class="pp-step__body">
                    <h3 class="pp-step__title">Ознакомьтесь с договором</h3>
                    <p class="pp-step__text">
                        Перейдите во вкладку «Документы» и внимательно прочитайте договор.<br>
                        Если вы заметите неточности в данных или информации, пожалуйста, свяжитесь с нами:
                    </p>
                    <a href="tel:+48730536091" class="pp-step__phone">
                        <i class="fas fa-phone-alt"></i>
                        +48 730 536 091
                    </a>
                </div>
            </div>

            {{-- ШАГ 2 --}}
            <div class="pp-step">
                <div class="pp-step__aside">
                    <div class="pp-step__num" @if($hasSignedContract) style="background: linear-gradient(135deg,#14532d,#166534);border-color:rgba(74,222,128,0.5);color:#4ade80;" @endif>
                        @if($hasSignedContract)<i class="fas fa-check" style="font-size:14px;"></i>@else 2 @endif
                    </div>
                    <div class="pp-step__line"></div>
                </div>
                <div class="pp-step__body">
                    <h3 class="pp-step__title">Подпишите договор онлайн</h3>
                    <p class="pp-step__text">
                        Нажмите кнопку «Podpisz umowę», чтобы подтвердить договор в электронном виде.
                    </p>
                    <p class="pp-step__note">
                        В момент подписания система автоматически сохраняет IP-адрес устройства
                        в целях юридического подтверждения согласия.
                    </p>
                </div>
            </div>

            {{-- ШАГ 3 --}}
            <div class="pp-step">
                <div class="pp-step__aside">
                    <div class="pp-step__num">3</div>
                    <div class="pp-step__line"></div>
                </div>
                <div class="pp-step__body">
                    <h3 class="pp-step__title">Оплатите занятия</h3>
                    <p class="pp-step__text">
                        Перейдите во вкладку «Оплаты» и выберите удобный период обучения:
                    </p>
                    <ul class="pp-step__list">
                        <li>1 месяц (4 занятия)</li>
                        <li>3 месяца (12 занятий)</li>
                        <li>6 месяцев (24 занятия)</li>
                    </ul>
                </div>
            </div>

            {{-- ШАГ 4 --}}
            <div class="pp-step">
                <div class="pp-step__aside">
                    <div class="pp-step__num">4</div>
                    <div class="pp-step__line"></div>
                </div>
                <div class="pp-step__body">
                    <h3 class="pp-step__title">Выберите способ оплаты</h3>
                    <div class="pp-step__tags">
                        <span class="pp-step__tag">
                            <i class="fas fa-credit-card" style="margin-right:6px; color: var(--teal); opacity:.7;"></i>
                            Imoje — карта, Apple Pay, Google Pay
                        </span>
                        <span class="pp-step__tag">
                            <i class="fas fa-sync-alt" style="margin-right:6px; color: var(--teal); opacity:.7;"></i>
                            Подписка — ежемесячное списание
                        </span>
                    </div>
                </div>
            </div>

        </div>{{-- /.pp-steps --}}

    </div>{{-- /.pp-welcome --}}

</div>{{-- /.parent-portal --}}

@endsection
