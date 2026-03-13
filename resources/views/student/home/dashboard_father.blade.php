@extends('student.layout.theme.master_student')

@section('styles')
<style>
/* =====================================================
   RESET & BASE — перебиваем то что мешает из master
   ===================================================== */
:root {
    --accent:       #26F9FF;
    --bg-deep:      #04151d;
    --card-from:    #193c4d;
    --card-to:      #082536;
    --card-shadow:  -1px -1px 1px 0px #657e8a;
    --card-radius:  20px;
    --card-pad:     20px 24px;
}

body {
    background-color: var(--bg-deep) !important;
}

/* Тёмный мобильный хедер */
header.d-lg-none {
    background: var(--bg-deep) !important;
    border-bottom: 1px solid rgba(38, 249, 255, 0.1);
}

/* Убираем белый фон content-area */
.content-area {
    background: var(--bg-deep) !important;
    padding: 0 !important;
}

/* =====================================================
   DASHBOARD LAYOUT
   ===================================================== */
.dash-page {
    position: relative;
    display: grid;
    /* xl: контент + mascot */
    grid-template-columns: 1fr 300px;
    gap: 32px;
    min-height: 100vh;
    padding: 40px 48px 40px 40px;
    background: var(--bg-deep);
    color: #fff;
    font-family: 'Roboto', sans-serif;
    overflow: hidden; /* clip glow */
}

/* Декоративный cyan-glow в правом верхнем углу */
.dash-page::before {
    content: '';
    position: absolute;
    top: -60px;
    right: 280px; /* правее mascot */
    width: 600px;
    height: 280px;
    background: url('/images/student-cabinet_bg.png') right top / cover no-repeat;
    transform: scaleX(-1);
    opacity: 0.45;
    pointer-events: none;
    z-index: 0;
}

.dash-content {
    min-width: 0;
    position: relative;
    z-index: 1;
}

/* =====================================================
   ПРОФИЛЬ
   ===================================================== */
.dash-profile {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 32px;
}

.dash-profile__avatar-wrap {
    position: relative;
    width: 80px;
    height: 80px;
    flex-shrink: 0;
}

.dash-profile__avatar {
    width: 100%;
    height: 100%;
    border-radius: 20px;
    border: 2px solid var(--accent);
    object-fit: cover;
    display: block;
}

.dash-profile__edit {
    position: absolute;
    bottom: -6px;
    right: -6px;
    width: 26px;
    height: 26px;
    background: rgba(38, 249, 255, 0.65);
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    transition: background 0.2s;
}

.dash-profile__edit:hover {
    background: var(--accent);
}

.dash-profile__name {
    font-size: 30px;
    font-weight: 700;
    margin: 0;
    color: #f2f2f2;
    line-height: 1.2;
}

/* =====================================================
   БАЗОВАЯ КАРТОЧКА
   ===================================================== */
.dash-card {
    background: linear-gradient(to top, var(--card-to), var(--card-from));
    border-radius: var(--card-radius);
    box-shadow: var(--card-shadow);
    padding: var(--card-pad);
    color: #f2f2f2;
    display: flex;
    align-items: center;
    gap: 16px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.dash-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--card-shadow), 0 6px 20px rgba(38, 249, 255, 0.07);
}

.dash-card--col {
    flex-direction: column;
    align-items: flex-start;
}

.dash-card__icon {
    width: 52px;
    height: 52px;
    flex-shrink: 0;
    object-fit: contain;
}

.dash-card__icon--small {
    width: 44px;
    height: 44px;
}

.dash-card__icon--round {
    border-radius: 10px;
    border: 1px solid rgba(38, 249, 255, 0.35);
    object-fit: cover;
}

.dash-card__title {
    font-size: 16px;
    font-weight: 600;
    line-height: 1.3;
}

.dash-card__sub {
    font-size: 13px;
    font-weight: 300;
    color: rgba(242, 242, 242, 0.5);
    margin-top: 3px;
}

.dash-card__value {
    font-size: 22px;
    font-weight: 700;
    color: #fafafa;
    line-height: 1.2;
}

.dash-card__label {
    font-size: 13px;
    font-weight: 300;
    color: rgba(242, 242, 242, 0.5);
    margin-top: 3px;
}

/* Ссылка «→» в карточке */
.dash-card__link {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 16px;
    font-weight: 500;
    color: #f2f2f2;
    text-decoration: none;
    margin-top: auto;
    transition: color 0.2s;
}

.dash-card__link:hover {
    color: var(--accent);
    text-decoration: none;
}

.dash-card__link i {
    color: var(--accent);
    font-size: 14px;
}

/* Spacer для flex */
.dash-card__spacer {
    margin-left: auto;
}

/* Accent badge icon (иконка справа в info-картах) */
.dash-card__badge {
    margin-left: auto;
    font-size: 18px;
    color: var(--accent);
    opacity: 0.45;
}

/* =====================================================
   СЕКЦИИ — грид-лэйауты
   ===================================================== */

/* Ряд 1: учитель + группа */
.dash-row--info {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 16px;
}

/* Ряд 2: монеты + алмазы + уровень */
.dash-row--currency {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 16px;
}

/* Ряд 3: домашнее + zoom + статистика */
.dash-row--actions {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 16px;
}

/* Action карточка — вертикальная */
.dash-card--action {
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 24px 20px;
    text-decoration: none;
    color: #fff;
}

.dash-card--action:hover {
    color: #fff;
}

.dash-card--action .dash-card__icon {
    width: 68px;
    height: 68px;
    margin-bottom: 4px;
}

/* Статистика — особая карточка */
.dash-card--stats {
    flex-direction: column;
    align-items: flex-start;
    padding: 20px 24px;
}

.dash-card--stats .stat-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--accent);
    margin: 4px 0 8px;
}

.dash-card--stats .stat-chart {
    width: 100%;
    margin: 4px 0 8px;
}

/* Ряд 4: рейтинг + новости */
.dash-row--bottom {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

/* =====================================================
   РЕЙТИНГ
   ===================================================== */
.dash-card--ranking {
    flex-direction: column;
    align-items: flex-start;
    padding: 20px 24px;
    gap: 12px;
}

.rank-list {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.rank-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    border-radius: 12px;
    background: linear-gradient(to top, #082536, #193c4d);
    box-shadow: var(--card-shadow);
    font-size: 15px;
}

/* Первое место — светлее */
.rank-item:first-child {
    background: linear-gradient(to top, #17303d, #1e4558);
}

.rank-item__medal {
    width: 24px;
    height: 24px;
    object-fit: contain;
    flex-shrink: 0;
}

.rank-item__name {
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.rank-item__score {
    font-weight: 600;
    color: rgba(242, 242, 242, 0.8);
    flex-shrink: 0;
}

/* =====================================================
   NEWS
   ===================================================== */
.dash-card--news {
    position: relative;
    overflow: hidden;
    min-height: 200px;
    padding: 0;
    align-items: stretch;
}

.dash-card--news .news-bg {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.6;
    border-radius: var(--card-radius);
    transition: opacity 0.3s;
}

.dash-card--news:hover .news-bg {
    opacity: 0.75;
}

/* Градиент снизу */
.dash-card--news::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 90px;
    background: linear-gradient(to top, rgba(8,37,54,0.96) 15%, transparent);
    border-radius: 0 0 var(--card-radius) var(--card-radius);
    pointer-events: none;
}

.dash-card--news .news-content {
    position: relative;
    z-index: 2;
    width: 100%;
    min-height: 200px;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 20px 24px;
}

/* =====================================================
   MASCOT
   ===================================================== */
.dash-mascot {
    position: sticky;
    top: 32px;
    height: calc(100vh - 80px);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-end;
    padding-bottom: 16px;
    z-index: 1;
}

.dash-mascot__glow {
    position: absolute;
    bottom: 8px;
    left: 50%;
    transform: translateX(-50%);
    width: 320px;
    height: 100px;
    background: radial-gradient(ellipse at center,
        rgba(38,249,255,0.5) 0%,
        rgba(38,249,255,0.2) 35%,
        transparent 70%
    );
    border-radius: 50%;
    pointer-events: none;
}

.dash-mascot__img {
    position: relative;
    max-width: 100%;
    max-height: 76vh;
    z-index: 2;
    filter: drop-shadow(0 0 28px rgba(38,249,255,0.3));
    display: block;
}

/* =====================================================
   АДАПТИВ — ПЛАНШЕТ (992–1199px)
   Mascot убирается, контент на всю ширину
   ===================================================== */
@media (max-width: 1199px) {
    .dash-page {
        grid-template-columns: 1fr; /* только контент */
        padding: 32px 32px 32px 28px;
    }

    .dash-page::before {
        right: 0;
        width: 60%;
        opacity: 0.3;
    }

    .dash-mascot {
        display: none; /* прячем персонажа */
    }
}

/* =====================================================
   АДАПТИВ — МОБИЛЬНЫЙ (<992px)
   Сайдбар скрыт, нижнее меню видно
   ===================================================== */
@media (max-width: 991px) {
    .dash-page {
        grid-template-columns: 1fr;
        padding: 16px 16px 110px; /* 110px = bottom nav 96px + отступ */
    }

    .dash-page::before {
        display: none;
    }

    /* Профиль */
    .dash-profile__name {
        font-size: 22px;
    }

    .dash-profile__avatar-wrap {
        width: 60px;
        height: 60px;
    }

    /* Ряды карточек */
    .dash-row--info {
        grid-template-columns: 1fr 1fr;
    }

    .dash-row--currency {
        grid-template-columns: repeat(3, 1fr);
    }

    .dash-row--actions {
        grid-template-columns: 1fr 1fr;
    }

    .dash-row--bottom {
        grid-template-columns: 1fr;
    }

    /* Меньше padding у карточек */
    .dash-card {
        padding: 14px 16px;
    }

    .dash-card--action {
        padding: 18px 14px;
    }

    .dash-card--action .dash-card__icon {
        width: 52px;
        height: 52px;
    }

    .dash-card--news {
        min-height: 160px;
    }

    .dash-card--news .news-content {
        min-height: 160px;
    }

    /* Иконки чуть меньше */
    .dash-card__icon {
        width: 40px;
        height: 40px;
    }

    .dash-card__value {
        font-size: 18px;
    }
}

/* =====================================================
   АДАПТИВ — МАЛЕНЬКИЙ МОБИЛЬНЫЙ (<576px)
   ===================================================== */
@media (max-width: 575px) {
    .dash-row--currency {
        grid-template-columns: 1fr 1fr; /* 2 колонки, уровень под ними */
    }

    .dash-row--actions {
        grid-template-columns: 1fr; /* в столбик */
    }

    .dash-card--action {
        flex-direction: row;
        text-align: left;
        padding: 16px;
    }

    .dash-card--action .dash-card__icon {
        width: 44px;
        height: 44px;
        margin-bottom: 0;
        margin-right: 4px;
    }
}
</style>
@endsection

@section('content')

<div class="dash-page">

    {{-- ==================== ЛЕВАЯ КОЛОНКА: КОНТЕНТ ==================== --}}
    <div class="dash-content">

        {{-- Профиль --}}
        <div class="dash-profile">
            <div class="dash-profile__avatar-wrap">
                <img src="{{ $student->avatar_url ?? asset('images/ava.png') }}"
                     alt="Avatar"
                     class="dash-profile__avatar">
                <button class="dash-profile__edit" title="Редактировать фото">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                        <path d="M7 16l3-1 7-7-2-2-7 7-1 3m10-10l2 2"
                              stroke="#04151d" stroke-width="2.5"
                              stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
            <h1 class="dash-profile__name">
                {{ $student->name ?? 'Ученик' }} {{ $student->surname ?? '' }}
            </h1>
        </div>

        {{-- Ряд 1: Тренер + Группа --}}
        <div class="dash-row--info">
            {{-- Тренер --}}
            <div class="dash-card">
                <img class="dash-card__icon dash-card__icon--small dash-card__icon--round"
                     src="{{ $teacher->avatar_url ?? asset('images/space_trainer.png') }}"
                     alt="Teacher">
                <div>
                    <div class="dash-card__title">
                        {{ $teacher?->name ?? 'Nie wyznaczono' }} {{ $teacher?->surname ?? '' }}
                    </div>
                    <div class="dash-card__sub">Trener SpaceM</div>
                </div>
                <span class="dash-card__badge">
                    <i class="fas fa-comment-dots"></i>
                </span>
            </div>

            {{-- Группа --}}
            <div class="dash-card">
                <div>
                    <div class="dash-card__title">
                        {{ $group?->name ?? 'Grupa w trakcie tworzenia' }}
                    </div>
                    <div class="dash-card__sub">Grupa</div>
                </div>
                <span class="dash-card__badge">
                    <i class="fas fa-users"></i>
                </span>
            </div>
        </div>

        {{-- Ряд 2: Монеты + Алмазы + Уровень --}}
        <div class="dash-row--currency">
            <div class="dash-card">
                <img class="dash-card__icon" src="{{ asset('images/space_coins.png') }}" alt="coins">
                <div>
                    <div class="dash-card__value">{{ $stats['coins'] }}</div>
                    <div class="dash-card__label">Space Coins</div>
                </div>
            </div>

            <div class="dash-card">
                <img class="dash-card__icon" src="{{ asset('images/space_diamond.png') }}" alt="diamonds">
                <div>
                    <div class="dash-card__value">{{ $stats['diamonds'] }}</div>
                    <div class="dash-card__label">Diamenty</div>
                </div>
            </div>

            <div class="dash-card">
                <img class="dash-card__icon" src="{{ asset('images/space_first_steps.png') }}" alt="level">
                <div>
                    <div class="dash-card__value">{{ $stats['level'] }}</div>
                    <div class="dash-card__label">First Step</div>
                </div>
            </div>
        </div>

        {{-- Ряд 3: Домашнее + Zoom + Статистика --}}
        <div class="dash-row--actions">
            {{-- Домашнее задание --}}
            <a href="#" class="dash-card dash-card--action">
                <img class="dash-card__icon" src="{{ asset('images/robot.png') }}" alt="homework">
                <div class="dash-card__link" style="margin-top: 8px;">
                    <span>Praca domowa</span>
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>

            {{-- Zoom --}}
            <a href="#" class="dash-card dash-card--action">
                <img class="dash-card__icon" src="{{ asset('images/zoom.png') }}" alt="zoom">
                <div class="dash-card__link" style="margin-top: 8px;">
                    <span>Dołącz do lekcji</span>
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>

            {{-- Статистика --}}
            <div class="dash-card dash-card--stats">
                <div class="dash-card__label">Średni czas treningu</div>
                <div class="stat-value">12 minut</div>
                <svg class="stat-chart" height="32" viewBox="0 0 156 31">
                    <path d="M0 20L20 15L40 18L60 12L80 16L100 10L120 14L140 8L156 12"
                          stroke="url(#sg)" stroke-width="3" fill="none"
                          stroke-linecap="round" stroke-linejoin="round"/>
                    <defs>
                        <linearGradient id="sg" x1="0" y1="0" x2="156" y2="0" gradientUnits="userSpaceOnUse">
                            <stop offset="0%"   stop-color="#123344"/>
                            <stop offset="50%"  stop-color="#8ED7DE"/>
                            <stop offset="100%" stop-color="#26F9FF"/>
                        </linearGradient>
                    </defs>
                </svg>
                <a href="#" class="dash-card__link">
                    <span>Statystyka</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
        </div>

        {{-- Ряд 4: Рейтинг + Новости --}}
        <div class="dash-row--bottom">
            {{-- Рейтинг --}}
            <div class="dash-card dash-card--ranking">
                <div class="rank-list">
                    @foreach($ranking ?? [] as $index => $rankStudent)
                        <div class="rank-item">
                            <img class="rank-item__medal"
                                 src="{{ asset('images/rating' . ($index + 1) . '.png') }}"
                                 alt="{{ $index + 1 }}">
                            <span class="rank-item__name">{{ $rankStudent->name }}</span>
                            <span class="rank-item__score">{{ $rankStudent->score }}</span>
                        </div>
                    @endforeach
                </div>
                <a href="#" class="dash-card__link">
                    <span>Ranking</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>

            {{-- Новости --}}
            <div class="dash-card dash-card--news">
                <img class="news-bg"
                     src="{{ asset('images/news_bg.jpg') }}"
                     alt="News">
                <div class="news-content">
                    <a href="#" class="dash-card__link">
                        <span>Nowiny</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>{{-- /.dash-content --}}

    {{-- ==================== ПРАВАЯ КОЛОНКА: ПЕРСОНАЖ ==================== --}}
    <div class="dash-mascot">
        <div class="dash-mascot__glow"></div>
        <img src="{{ asset('images/heroes/big-main-hero.png') }}"
             alt="Mascot"
             class="dash-mascot__img">
    </div>

</div>{{-- /.dash-page --}}

@endsection
