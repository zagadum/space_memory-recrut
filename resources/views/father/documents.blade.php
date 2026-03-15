@extends('student.layout.theme.master_student')

@section('styles')
<style>
:root {
    --teal:       #26F9FF;
    --teal-dim:   rgba(38,249,255,0.10);
    --green:      #4ade80;
    --green-dim:  rgba(74,222,128,0.10);
    --bg:         #04151d;
    --surface-1:  #0d2535;
    --surface-2:  #112d40;
    --border:     rgba(38,249,255,0.12);
    --text:       #f2f2f2;
    --muted:      rgba(242,242,242,0.45);
    --r-lg:       20px;
    --r-md:       12px;
}

body { background: var(--bg) !important; }
.content-area { background: var(--bg) !important; padding: 0 !important; }
header.d-lg-none { background: var(--bg) !important; border-bottom: 1px solid var(--border); }

/* ── PAGE ── */
.dp-wrap {
    min-height: 100vh;
    padding: 44px 52px 60px;
    background: var(--bg);
    color: var(--text);
    font-family: 'Roboto', sans-serif;
    position: relative;
    overflow: hidden;
}
.dp-wrap::before {
    content: '';
    position: absolute;
    top: -100px; right: -60px;
    width: 480px; height: 480px;
    background: radial-gradient(circle, rgba(38,249,255,0.06) 0%, transparent 65%);
    pointer-events: none; z-index: 0;
}
.dp-wrap > * { position: relative; z-index: 1; }

/* ── PAGE HEADER ── */
.dp-head {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 40px;
}
.dp-head__icon {
    width: 52px; height: 52px;
    border-radius: 14px;
    background: var(--teal-dim);
    border: 1px solid rgba(38,249,255,0.22);
    display: flex; align-items: center; justify-content: center;
    color: var(--teal); font-size: 20px; flex-shrink: 0;
}
.dp-head h1 { font-size: 28px; font-weight: 700; margin: 0; color: #fff; letter-spacing: -0.3px; }
.dp-head p  { font-size: 13px; color: var(--muted); margin: 3px 0 0; }

/* ── DOC LIST ── */
.dp-list { display: flex; flex-direction: column; gap: 14px; }

/* ── DOC CARD ── */
.dp-card {
    background: var(--surface-1);
    border: 1px solid var(--border);
    border-radius: var(--r-lg);
    padding: 24px 28px;
    display: flex;
    align-items: center;
    gap: 20px;
    position: relative;
    overflow: hidden;
    transition: border-color .2s, box-shadow .2s;
}
.dp-card:hover {
    border-color: rgba(38,249,255,0.28);
    box-shadow: 0 6px 28px rgba(0,0,0,0.3);
}

/* Левая цветная полоска — статус */
.dp-card::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 4px;
    border-radius: 0;
    transition: background .3s;
}
.dp-card--unsigned::before {
    background: var(--teal);
    box-shadow: 0 0 12px rgba(38,249,255,0.35);
}
.dp-card--signed::before {
    background: var(--green);
    box-shadow: 0 0 12px rgba(74,222,128,0.35);
}

/* Иконка файла */
.dp-card__file-icon {
    width: 44px; height: 54px;
    flex-shrink: 0;
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    color: var(--teal); font-size: 18px;
    position: relative;
}
/* Загнутый уголок */
.dp-card__file-icon::after {
    content: '';
    position: absolute;
    top: 0; right: 0;
    width: 10px; height: 10px;
    background: var(--bg);
    border-left: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
    border-radius: 0 0 0 4px;
}

/* Инфо */
.dp-card__info { flex: 1; min-width: 0; }
.dp-card__name {
    font-size: 17px; font-weight: 600; color: #fff;
    margin: 0 0 8px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.dp-card__meta {
    display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
}
.dp-card__date { font-size: 12px; color: var(--muted); }

/* Бейдж статуса */
.dp-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px; font-weight: 600;
}
.dp-badge--unsigned {
    background: rgba(38,249,255,0.08);
    border: 1px solid rgba(38,249,255,0.2);
    color: var(--teal);
}
.dp-badge--signed {
    background: var(--green-dim);
    border: 1px solid rgba(74,222,128,0.25);
    color: var(--green);
}
.dp-badge__dot {
    width: 6px; height: 6px;
    border-radius: 50%; flex-shrink: 0;
}
.dp-badge--unsigned .dp-badge__dot {
    background: var(--teal);
    animation: blink 2s infinite;
}
.dp-badge--signed .dp-badge__dot {
    background: var(--green);
}
@keyframes blink {
    0%,100% { opacity: 1; }
    50%      { opacity: 0.25; }
}

/* Кнопки */
.dp-card__actions {
    display: flex; align-items: center; gap: 10px; flex-shrink: 0;
}

/* Просмотр */
.dp-btn-view {
    width: 40px; height: 40px;
    border-radius: 10px;
    border: 1px solid var(--border);
    background: transparent;
    color: var(--muted);
    display: flex; align-items: center; justify-content: center;
    font-size: 15px; cursor: pointer;
    text-decoration: none;
    transition: background .2s, color .2s, border-color .2s;
    flex-shrink: 0;
}
.dp-btn-view:hover {
    background: var(--teal-dim);
    border-color: rgba(38,249,255,0.3);
    color: var(--teal);
    text-decoration: none;
}

/* Подписать */
.dp-btn-sign {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 22px;
    background: linear-gradient(135deg, #26F9FF, #179599);
    border: none; border-radius: 10px;
    color: #04151d; font-size: 14px; font-weight: 700;
    cursor: pointer; text-decoration: none; white-space: nowrap;
    box-shadow: 0 4px 16px rgba(38,249,255,0.2);
    transition: opacity .2s, transform .15s, box-shadow .2s;
}
.dp-btn-sign:hover {
    opacity: .88; transform: translateY(-2px);
    box-shadow: 0 6px 22px rgba(38,249,255,0.38);
    color: #04151d; text-decoration: none;
}

/* Скачать фактуру */
.dp-btn-download {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 22px;
    background: transparent;
    border: 1.5px solid rgba(74,222,128,0.35);
    border-radius: 10px;
    color: var(--green); font-size: 14px; font-weight: 600;
    cursor: pointer; text-decoration: none; white-space: nowrap;
    transition: background .2s, border-color .2s, transform .15s;
}
.dp-btn-download:hover {
    background: var(--green-dim);
    border-color: rgba(74,222,128,0.6);
    transform: translateY(-2px);
    color: var(--green); text-decoration: none;
}

/* ── АДАПТИВ ── */
@media (max-width: 991px) {
    .dp-wrap { padding: 24px 20px 110px; }
    .dp-head h1 { font-size: 22px; }
    .dp-card { flex-wrap: wrap; padding: 18px 20px; gap: 14px; }
    .dp-card__actions {
        width: 100%;
        border-top: 1px solid var(--border);
        padding-top: 14px;
        justify-content: flex-end;
    }
}
@media (max-width: 480px) {
    .dp-card__actions { justify-content: stretch; }
    .dp-btn-sign, .dp-btn-download { flex: 1; justify-content: center; }
}
</style>
@endsection

@section('content')
<div class="dp-wrap">

    {{-- HEADER --}}
    <div class="dp-head">
        <div class="dp-head__icon">
            <i class="fas fa-file-contract"></i>
        </div>
        <div>
            <h1>Документы</h1>
            <p>
                Ваши договоры и соглашения
                @if(!empty($student))
                    · {{ trim(($student->name ?? '') . ' ' . ($student->surname ?? '')) ?: ($student->email ?? '') }}
                @endif
            </p>
        </div>
    </div>

    {{-- СПИСОК --}}
    <div class="dp-list">
        @forelse ($documents as $doc)
        @php
            $isSigned = ($doc->doc_status ?? null) === 'sign';
        @endphp
        <div class="dp-card {{ $isSigned ? 'dp-card--signed' : 'dp-card--unsigned' }}">
            <div class="dp-card__file-icon" {!! $isSigned ? 'style="color: var(--green);"' : '' !!}>
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="dp-card__info">
                <p class="dp-card__name">
                    {{ $doc->title ?: ($doc->doc_no ? ('Документ № ' . $doc->doc_no) : ('Документ #' . $doc->id)) }}
                </p>
                <div class="dp-card__meta">
                    <span class="dp-card__date">
                        <i class="fas {{ $isSigned ? 'fa-calendar-check' : 'fa-calendar-alt' }}" style="margin-right:4px;opacity:.5;"></i>
                        {{ $isSigned ? 'Документ подписан! ' . optional($doc->sign_date)->format('d.m.Y H:i') : 'Ожидает подписи' }}
                    </span>
                    <span class="dp-badge {{ $isSigned ? 'dp-badge--signed' : 'dp-badge--unsigned' }}">
                        <span class="dp-badge__dot"></span>
                        {{ $isSigned ? 'Подписан' : 'Ожидает подписи' }}
                    </span>
                </div>
            </div>
            <div class="dp-card__actions">
                <a href="{{ route('father.document.view', $doc->id) }}" class="dp-btn-view" title="Просмотреть">
                    <i class="fas fa-eye"></i>
                </a>
                @if(!$isSigned)
                    <a href="{{ route('father.document.view', $doc->id) }}" class="dp-btn-sign">
                        <i class="fas fa-pen-nib"></i>
                        Подписать
                    </a>
                @else
                    <a href="{{ route('father.document.view', $doc->id) }}" class="dp-btn-download">
                        <i class="fas fa-download"></i>
                        Открыть
                    </a>
                @endif
            </div>
        </div>
        @empty
        <div class="dp-empty" style="text-align: center; padding: 40px; color: var(--muted);">
            <i class="fas fa-folder-open" style="font-size: 40px; margin-bottom: 15px; display: block;"></i>
            У вас пока нет доступных документов
        </div>
        @endforelse
    </div>

</div>
@endsection
