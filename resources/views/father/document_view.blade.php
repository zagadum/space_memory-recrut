@extends('student.layout.theme.master_student')

@section('styles')
<style>
:root {
    --teal:      #26F9FF;
    --teal-dim:  rgba(38,249,255,0.10);
    --teal-glow: rgba(38,249,255,0.22);
    --green:     #4ade80;
    --green-dim: rgba(74,222,128,0.10);
    --bg:        #04151d;
    --surface-1: #0d2535;
    --border:    rgba(38,249,255,0.12);
    --text:      #f2f2f2;
    --muted:     rgba(242,242,242,0.45);
    --r-lg:      20px;
}

body { background: var(--bg) !important; }
.content-area { background: var(--bg) !important; padding: 0 !important; }
header.d-lg-none { background: var(--bg) !important; border-bottom: 1px solid var(--border); }

/* ── PAGE WRAP ── */
.dv-wrap {
    min-height: 100vh;
    padding: 36px 48px 80px;
    background: var(--bg);
    color: var(--text);
    font-family: 'Roboto', sans-serif;
    position: relative;
}
.dv-wrap::before {
    content: '';
    position: fixed; top: -120px; right: -80px;
    width: 500px; height: 500px;
    background: radial-gradient(circle, rgba(38,249,255,0.05) 0%, transparent 65%);
    pointer-events: none; z-index: 0;
}
.dv-wrap > * { position: relative; z-index: 1; }

/* ── TOP BAR ── */
.dv-topbar {
    display: flex; align-items: center; gap: 14px;
    margin-bottom: 28px;
}
.dv-back {
    width: 38px; height: 38px;
    border-radius: 10px;
    border: 1px solid var(--border);
    background: transparent; color: var(--muted);
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; cursor: pointer; text-decoration: none;
    transition: background .2s, color .2s, border-color .2s;
    flex-shrink: 0;
}
.dv-back:hover {
    background: var(--teal-dim);
    border-color: var(--teal-glow);
    color: var(--teal); text-decoration: none;
}
.dv-topbar__title { flex: 1; }
.dv-topbar__title h1 { font-size: 20px; font-weight: 700; color: #fff; margin: 0; }
.dv-topbar__title p  { font-size: 12px; color: var(--muted); margin: 3px 0 0; }

/* ── LAYOUT ── */
.dv-layout {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 24px;
    align-items: start;
}

/* ══════════════════════════════════════
   PAPER DOCUMENT
══════════════════════════════════════ */
.dv-paper-wrap {
    border-radius: var(--r-lg);
    overflow: hidden;
    box-shadow: 0 8px 40px rgba(0,0,0,0.5);
}

.dv-paper-toolbar {
    display: flex; align-items: center; justify-content: space-between;
    padding: 12px 20px;
    background: var(--surface-1);
    border: 1px solid var(--border);
    border-bottom: none;
    border-radius: var(--r-lg) var(--r-lg) 0 0;
}
.dv-paper-toolbar__left {
    display: flex; align-items: center; gap: 10px;
    font-size: 13px; color: var(--muted);
}
.dv-paper-toolbar__left i { color: var(--teal); }
.dv-paper-toolbar__right { display: flex; align-items: center; gap: 6px; }
.dv-tool-btn {
    width: 30px; height: 30px;
    border-radius: 7px;
    border: 1px solid var(--border);
    background: transparent; color: var(--muted);
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; cursor: pointer;
    transition: background .2s, color .2s;
}
.dv-tool-btn:hover { background: var(--teal-dim); color: var(--teal); }

.dv-progress {
    height: 3px; background: rgba(255,255,255,0.06);
    border-left: 1px solid var(--border);
    border-right: 1px solid var(--border);
}
.dv-progress__fill {
    height: 100%;
    background: linear-gradient(90deg, #26F9FF, #4ade80);
    width: 0%; transition: width .1s linear;
}

.dv-scroll {
    height: 700px;
    overflow-y: auto;
    background: #c8c8c8;
    padding: 32px 28px;
    border: 1px solid var(--border);
    border-top: none;
    border-radius: 0 0 var(--r-lg) var(--r-lg);
}
.dv-scroll::-webkit-scrollbar { width: 6px; }
.dv-scroll::-webkit-scrollbar-track { background: transparent; }
.dv-scroll::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.25); border-radius: 10px; }

/* ── THE WHITE PAPER ── */
.dv-paper {
    background: #ffffff;
    color: #1a1a1a;
    width: 100%;
    max-width: 760px;
    margin: 0 auto;
    padding: 60px 70px;
    border-radius: 3px;
    box-shadow: 0 2px 16px rgba(0,0,0,0.18);
    font-family: 'Georgia', 'Times New Roman', serif;
    font-size: 13.5px;
    line-height: 1.8;
}

.doc-header {
    text-align: center;
    margin-bottom: 44px;
    padding-bottom: 28px;
    border-bottom: 2px solid #1a1a1a;
}
.doc-header__company {
    font-size: 11px;
    font-family: 'Roboto', sans-serif;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: #555;
    margin-bottom: 12px;
}
.doc-header__title {
    font-size: 18px; font-weight: 700;
    color: #111; margin: 0 0 6px; line-height: 1.4;
}
.doc-header__subtitle { font-size: 13px; color: #444; margin: 0 0 4px; }
.doc-header__meta {
    font-size: 11.5px; color: #888;
    font-family: 'Roboto', sans-serif; margin-top: 10px;
}

.doc-parties {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 0; margin-bottom: 36px;
    border: 1px solid #ccc; border-radius: 2px; overflow: hidden;
}
.doc-party { padding: 16px 20px; background: #f8f8f8; }
.doc-party + .doc-party { border-left: 1px solid #ccc; background: #fff; }
.doc-party__label {
    font-size: 9px; font-family: 'Roboto', sans-serif;
    font-weight: 700; text-transform: uppercase; letter-spacing: 1.2px;
    color: #888; margin-bottom: 10px;
}
.doc-party__row { margin-bottom: 5px; }
.doc-party__row:last-child { margin-bottom: 0; }
.doc-party__key {
    font-size: 10px; font-family: 'Roboto', sans-serif;
    color: #999; text-transform: uppercase; letter-spacing: 0.5px;
}
.doc-party__val {
    font-size: 13px; font-family: 'Roboto', sans-serif;
    font-weight: 600; color: #111;
}
.doc-party__val--filled { color: #0b6e8a; }

.doc-section { margin-bottom: 28px; }
.doc-section-title {
    font-size: 13px; font-family: 'Roboto', sans-serif;
    font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;
    color: #111; margin: 0 0 12px;
    padding-bottom: 6px; border-bottom: 1px solid #ddd;
}
.doc-clause {
    display: flex; gap: 0; margin-bottom: 8px;
    font-size: 13px; color: #222; text-align: justify;
}
.doc-clause-num { font-weight: 700; min-width: 44px; flex-shrink: 0; color: #111; }
.doc-note {
    background: #f5f5f5; border-left: 3px solid #aaa;
    padding: 10px 14px; margin: 12px 0;
    font-size: 12.5px; color: #444; font-family: 'Roboto', sans-serif;
}
.doc-note--warn { background: #fffbf0; border-left-color: #d97706; color: #6b4a00; }
.doc-footer {
    margin-top: 48px; padding-top: 20px; border-top: 1px solid #ccc;
    font-size: 11px; font-family: 'Roboto', sans-serif;
    color: #aaa; text-align: center; line-height: 1.6;
}

/* ══════════════════════════════════════
   SIDEBAR
══════════════════════════════════════ */
.dv-sidebar { display: flex; flex-direction: column; gap: 16px; }

.dv-sign-card {
    background: var(--surface-1);
    border: 1px solid var(--border);
    border-radius: var(--r-lg);
    padding: 24px;
}
.dv-sign-card__title {
    font-size: 12px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.8px;
    color: var(--muted); margin: 0 0 18px;
}

.dv-consent {
    display: flex; gap: 12px; align-items: flex-start; margin-bottom: 20px;
}
.dv-cb {
    width: 20px; height: 20px; border-radius: 5px;
    border: 1.5px solid rgba(38,249,255,0.3);
    background: transparent; cursor: pointer; flex-shrink: 0; margin-top: 1px;
    appearance: none; -webkit-appearance: none;
    display: flex; align-items: center; justify-content: center;
    transition: background .2s, border-color .2s; position: relative;
}
.dv-cb:checked { background: var(--teal); border-color: var(--teal); }
.dv-cb:checked::after {
    content: '✓'; color: #04151d;
    font-size: 12px; font-weight: 900; position: absolute;
}
.dv-consent-label { font-size: 12.5px; color: var(--muted); line-height: 1.55; cursor: pointer; }

.dv-btn-sign {
    display: flex; align-items: center; justify-content: center; gap: 9px;
    width: 100%; padding: 14px;
    background: linear-gradient(135deg, #26F9FF, #179599);
    border: none; border-radius: 11px;
    color: #04151d; font-size: 14px; font-weight: 800; cursor: pointer;
    box-shadow: 0 4px 18px rgba(38,249,255,0.2);
    transition: opacity .2s, transform .15s, box-shadow .2s;
}
.dv-btn-sign:disabled { opacity: 0.35; cursor: not-allowed; transform: none !important; box-shadow: none; }
.dv-btn-sign:not(:disabled):hover {
    opacity: .88; transform: translateY(-2px);
    box-shadow: 0 8px 26px rgba(38,249,255,0.35);
}

.dv-signed {
    display: none; background: var(--green-dim);
    border: 1px solid rgba(74,222,128,0.3);
    border-radius: 11px; padding: 16px;
    align-items: center; gap: 12px;
}
.dv-signed.show { display: flex; }
.dv-signed i { color: var(--green); font-size: 22px; flex-shrink: 0; }
.dv-signed__text { font-size: 13px; color: var(--green); font-weight: 700; }
.dv-signed__sub  { font-size: 11px; color: rgba(74,222,128,0.6); margin-top: 2px; }

.dv-info-pill {
    background: rgba(255,255,255,0.03);
    border: 1px solid var(--border);
    border-radius: 10px; padding: 14px 16px;
    font-size: 11.5px; color: var(--muted); line-height: 1.55;
}
.dv-info-pill i { color: var(--teal); margin-right: 5px; }

/* ── RESPONSIVE ── */
@media (max-width: 1050px) { .dv-layout { grid-template-columns: 1fr 270px; } }
@media (max-width: 900px) {
    .dv-wrap { padding: 20px 16px 100px; }
    .dv-layout { grid-template-columns: 1fr; }
    .dv-sidebar { order: -1; }
    .dv-scroll { height: 480px; padding: 20px 12px; }
    .dv-paper { padding: 36px 28px; }
    .doc-parties { grid-template-columns: 1fr; }
    .doc-party + .doc-party { border-left: none; border-top: 1px solid #ccc; }
}
@media (max-width: 480px) {
    .dv-paper { padding: 28px 18px; font-size: 12.5px; }
}
</style>
@endsection

@section('content')
@php
    $normalizedDocStatus = strtolower(trim((string) ($document->doc_status ?? '')));
    $isSigned = in_array($normalizedDocStatus, ['sign', 'signed'], true) || !empty($document->sign_date);
@endphp
<div class="dv-wrap">

    {{-- TOP BAR --}}
    <div class="dv-topbar">
        <a href="{{ route('father.documents') }}" class="dv-back" title="Назад к документам">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="dv-topbar__title">
            <h1>{{ $document->title ?? ($document->doc_no ? ('Документ № ' . $document->doc_no) : 'Договор 2026 Групповые занятия') }}</h1>
            <p>Ознакомьтесь с документом и подпишите его</p>
        </div>
    </div>

    <div class="dv-layout">

        {{-- ══════════════════════ LEFT — PAPER ══════════════════════ --}}
        <div class="dv-paper-wrap">

            <div class="dv-paper-toolbar">
                <div class="dv-paper-toolbar__left">
                    <i class="fas fa-file-alt"></i>
                    Regulamin GLS · вступил в силу 03.03.2026
                </div>
                <div class="dv-paper-toolbar__right">
                    <button class="dv-tool-btn" id="btnDownload" title="Скачать PDF">
                        <i class="fas fa-download" id="btnDownloadIcon"></i>
                    </button>
                    <button class="dv-tool-btn" id="btnTop" title="Наверх">
                        <i class="fas fa-angle-up"></i>
                    </button>
                </div>
            </div>

            <div class="dv-progress">
                <div class="dv-progress__fill" id="progressFill"></div>
            </div>

            <div class="dv-scroll" id="docScroll">
            <div class="dv-paper">

                <div class="doc-header">
                    <div class="doc-header__company">Global Leaders Skills Sp. z o.o.</div>
                    <div class="doc-header__title">
                        Regulamin świadczenia usług,<br>
                        w tym świadczenia usług drogą elektroniczną
                    </div>
                    <div class="doc-header__subtitle">przez spółkę GLOBAL LEADERS SKILLS sp. z o.o. z siedzibą w Warszawie</div>
                    <div class="doc-header__meta">wersja obowiązująca od dnia 03.03.2026 r.</div>
                </div>

                {{-- PARTIES --}}
                <div class="doc-section">
                    <div class="doc-section-title">Стороны договора</div>
                    <div class="doc-parties">
                        <div class="doc-party">
                            <div class="doc-party__label">Исполнитель</div>
                            <div class="doc-party__row">
                                <div class="doc-party__key">Название</div>
                                <div class="doc-party__val">GLOBAL LEADERS SKILLS Sp. z o.o.</div>
                            </div>
                            <div class="doc-party__row">
                                <div class="doc-party__key">Адрес</div>
                                <div class="doc-party__val">ul. Kabacki Dukt 1, lok. U1 i U2, 02-798 Warszawa</div>
                            </div>
                            <div class="doc-party__row">
                                <div class="doc-party__key">KRS / NIP</div>
                                <div class="doc-party__val">0001055763 / 5252970924</div>
                            </div>
                        </div>
                        <div class="doc-party">
                            <div class="doc-party__label">Клиент (Rodzic)</div>
                            <div class="doc-party__row">
                                <div class="doc-party__key">Имя и фамилия</div>
                                <div class="doc-party__val doc-party__val--filled">{{ $parent->full_name ?? '—' }}</div>
                            </div>
                            <div class="doc-party__row">
                                <div class="doc-party__key">Email</div>
                                <div class="doc-party__val doc-party__val--filled">{{ $parent->email ?? '—' }}</div>
                            </div>
                            <div class="doc-party__row">
                                <div class="doc-party__key">Ученик (Kursant)</div>
                                <div class="doc-party__val doc-party__val--filled">{{ $student->full_name ?: '—' }}</div>
                            </div>
                            <div class="doc-party__row">
                                <div class="doc-party__key">Группа</div>
                                <div class="doc-party__val doc-party__val--filled">{{ $student->group?->name ?? '—' }}</div>
                            </div>
                            <div class="doc-party__row">
                                <div class="doc-party__key">Абонемент / мес.</div>
                                <div class="doc-party__val doc-party__val--filled">{{ number_format($contract->subscription_amount ?? 0, 2) }} zł</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 1. DEFINICJE --}}
                <div class="doc-section">
                    <div class="doc-section-title">1. Definicje</div>
                    <div class="doc-clause"><span class="doc-clause-num">1.1.</span><span><strong>Abonament</strong> – opłata za miesięczny pakiet Zajęć Edukacyjnych i zapewnienie dostępu do Platformy Edukacyjnej uprawniająca do uczestniczenia w Zajęciach Edukacyjnych w danym miesiącu kalendarzowym.</span></div>
                    <div class="doc-clause"><span class="doc-clause-num">1.2.</span><span><strong>Godzina lekcyjna</strong> – podstawowa jednostka czasu zajęć edukacyjnych; jej długość wynosi <strong>30 minut</strong> dla Grup Młodszych i <strong>45 minut</strong> dla Grup Starszych.</span></div>
                    <div class="doc-clause"><span class="doc-clause-num">1.3.</span><span><strong>Grupa Młodsza</strong> – grupa zajęciowa, do której uczęszczają dzieci w wieku 5–7 lat.</span></div>
                    <div class="doc-clause"><span class="doc-clause-num">1.4.</span><span><strong>Grupa Starsza</strong> – grupa zajęciowa, do której uczęszczają dzieci w wieku 8–14 lat.</span></div>
                    <div class="doc-clause"><span class="doc-clause-num">1.5.</span><span><strong>Grupa Zajęciowa</strong> – grupa Kursantów, dla której prowadzone są zajęcia grupowe.</span></div>
                    <div class="doc-clause"><span class="doc-clause-num">1.6.</span><span><strong>Konsument</strong> – osoba fizyczna dokonująca z przedsiębiorcą czynności prawnej niezwiązanej bezpośrednio z jej działalnością gospodarczą lub zawodową.</span></div>
                    <div class="doc-clause"><span class="doc-clause-num">1.7.</span><span><strong>Kursant</strong> – dziecko uczęszczające na zajęcia edukacyjne organizowane przez Spółkę.</span></div>
                    <div class="doc-clause"><span class="doc-clause-num">1.8.</span><span><strong>Platforma Edukacyjna</strong> – platforma edukacyjna dostępna pod adresem indigomental.com.</span></div>
                    <div class="doc-clause"><span class="doc-clause-num">1.10.</span><span><strong>Regulamin</strong> – niniejszy regulamin dostępny pod adresem: indigomental.pl.</span></div>
                    <div class="doc-clause"><span class="doc-clause-num">1.11.</span><span><strong>Rodzic</strong> – rodzic lub opiekun prawny Kursanta lub inna osoba, która zawarła umowę o świadczenie Zajęć Edukacyjnych dla Kursanta.</span></div>
                    <div class="doc-clause"><span class="doc-clause-num">1.12.</span><span><strong>Spółka</strong> – GLOBAL LEADERS SKILLS Sp. z o.o. z siedzibą w Warszawie, ul. Kabacki Dukt 1, lok. U1 i U2, 02-798 Warszawa; KRS: 0001055763; NIP: 5252970924; REGON: 526267569; kapitał zakładowy: 5 000,00 zł.</span></div>
                    <div class="doc-clause"><span class="doc-clause-num">1.13.</span><span><strong>Trener</strong> – osoba współpracująca ze Spółką, która prowadzi dla Kursantów Zajęcia Edukacyjne.</span></div>
                    <div class="doc-clause"><span class="doc-clause-num">1.14.</span><span><strong>Umowa</strong> – umowa zawarta pomiędzy Spółką a Rodzicem Kursanta; Regulamin stanowi jej integralną i nierozerwalną część.</span></div>
                    <div class="doc-clause"><span class="doc-clause-num">1.18.</span><span><strong>Zajęcia on-line</strong> – zajęcia edukacyjne prowadzone na odległość za pośrednictwem sieci Internet przy wykorzystaniu komunikatorów internetowych umożliwiających połączenie wideo oraz audio.</span></div>
                    <div class="doc-clause"><span class="doc-clause-num">1.19.</span><span><strong>Zajęcia stacjonarne</strong> – zajęcia edukacyjne prowadzone przy jednoczesnej obecności Trenera i Kursantów.</span></div>
                </div>

                {{-- 2. POSTANOWIENIA --}}
                <div class="doc-section">
                    <div class="doc-section-title">2. Postanowienia ogólne</div>
                    <div class="doc-clause"><span class="doc-clause-num">2.1.</span><span>Niniejszy dokument reguluje zasady świadczenia usług przez Spółkę, której głównym przedmiotem działalności jest prowadzenie zajęć edukacyjnych dla dzieci z zakresu <strong>arytmetyki mentalnej, szybkiego czytania i technik szybkiego zapamiętywania</strong>, w tym także świadczenie takich usług drogą elektroniczną.</span></div>
                    <div class="doc-clause"><span class="doc-clause-num">2.2.</span><span>Niniejszy dokument stanowi regulamin usług świadczonych drogą elektroniczną w rozumieniu ustawy z dnia 18 lipca 2002 r. o świadczeniu usług drogą elektroniczną.</span></div>
                    <div class="doc-clause"><span class="doc-clause-num">2.3.</span><span>Korzystanie z usług opisanych w niniejszym Regulaminie jest możliwe po jego zaakceptowaniu i zapoznaniu się z Polityką Prywatności Spółki.</span></div>
                </div>

                {{-- 3. USŁUGI --}}
                <div class="doc-section">
                    <div class="doc-section-title">3. Rodzaj i zakres świadczonych usług</div>
                    <div class="doc-clause"><span class="doc-clause-num">3.1.</span><span>Na podstawie Regulaminu Spółka świadczy na rzecz Usługobiorców następujące usługi drogą elektroniczną: dostęp do Strony internetowej, Zajęcia Edukacyjne.</span></div>
                    <div class="doc-clause"><span class="doc-clause-num">3.4.</span><span>Usługa Zajęcia Edukacyjne świadczona jest odpłatnie i przez czas nieoznaczony. Zawarcie umowy następuje poprzez akceptację niniejszego Regulaminu i późniejsze podpisanie Umowy.</span></div>
                    <div class="doc-clause"><span class="doc-clause-num">3.5.</span><span>W ramach odpłatności za usługę Zajęcia Edukacyjne Spółka zapewnia także dostęp do treści cyfrowych udostępnianych w ramach Platformy Edukacyjnej, a cena za ten dostęp wliczona jest w Abonament i wynosi <strong>244,50 zł</strong>.</span></div>
                </div>

                {{-- 5. ZAJĘCIA --}}
                <div class="doc-section">
                    <div class="doc-section-title">5. Podstawowe postanowienia o świadczeniu Zajęć Edukacyjnych</div>
                    <div class="doc-clause"><span class="doc-clause-num">5.1.</span><span>Zajęcia Edukacyjne służą przeprowadzeniu Kursantów przez kurs obejmujący arytmetykę mentalną, szybkie czytanie i techniki szybkiego zapamiętywania.</span></div>
                    <div class="doc-clause"><span class="doc-clause-num">5.2.</span><span>Podstawowy czas trwania kursu to <strong>24 miesiące</strong>, jednak jego długość może ulec wydłużeniu ze względu na postępy Kursanta w nauce, formę realizacji zajęć lub przerwy w Zajęciach Edukacyjnych.</span></div>
                    <div class="doc-clause"><span class="doc-clause-num">5.5.</span><span>Zajęcia Edukacyjne odbywają się <strong>raz w tygodniu</strong> zgodnie z harmonogramem ustalonym przez Spółkę dla Grupy Zajęciowej, do której przypisany został Kursant.</span></div>
                    <div class="doc-clause"><span class="doc-clause-num">5.7.</span><span>W celu zarezerwowania miejsca w Grupie Zajęciowej Rodzic da Spółce zadatek w kwocie <strong>200 zł</strong>, który powinien być przekazany w terminie 2 dni od dnia zawarcia Umowy.</span></div>
                    <div class="doc-clause"><span class="doc-clause-num">5.9.</span><span>Zajęcia Edukacyjne odbywają się co do zasady w Grupach Zajęciowych liczących nie więcej niż <strong>10 Kursantów</strong>.</span></div>
                    <div class="doc-clause"><span class="doc-clause-num">5.10.</span><span>Na wniosek Rodzica udział Kursanta w zajęciach może zostać zawieszony na czas wakacji letnich, pod warunkiem że Rodzic dokonał zapłaty Abonamentu z góry za przynajmniej trzy miesięczne pakiety zajęć.</span></div>
                </div>

                {{-- 7. OBOWIĄZKI --}}
                <div class="doc-section">
                    <div class="doc-section-title">7. Obowiązki Kursantów i Rodziców</div>
                    <div class="doc-clause"><span class="doc-clause-num">7.1.</span><span>Rodzice są odpowiedzialni za dołożenie wszelkich starań do tego, aby zapewnić poprawne i prawidłowe uczestnictwo Kursantów w Zajęciach Edukacyjnych.</span></div>
                    <div class="doc-clause"><span class="doc-clause-num">7.3.</span><span>Kursant powinien uczestniczyć w sposób czynny w zajęciach, zachowywać się zgodnie z zasadami kultury osobistej, odrabiać zadane prace domowe i przygotowywać się do zajęć.</span></div>
                    <div class="doc-clause"><span class="doc-clause-num">7.5.</span><span>Rodzic nie ma prawa uczestniczenia wraz z Kursantem w zajęciach ze względu na to, że obecność Rodzica wpływa na sposób zachowania Kursanta.</span></div>
                </div>

                {{-- 9. ODPŁATNOŚĆ --}}
                <div class="doc-section">
                    <div class="doc-section-title">9. Odpłatność za Zajęcia Edukacyjne</div>
                    <div class="doc-note">
                        Kwota Abonamentu ustalona jest w Umowie. Abonament za dany miesięczny pakiet zajęć płatny jest z góry, do końca poprzedniego miesiąca, za pośrednictwem systemu płatności elektronicznych <strong>PayU</strong>.
                    </div>
                    <div class="doc-clause"><span class="doc-clause-num">9.3.</span><span>Za moment zapłaty uznaje się chwilę zaksięgowania wpłaty przez Spółkę.</span></div>
                    <div class="doc-clause"><span class="doc-clause-num">9.6.</span><span>Jeśli dany Rodzic zapisuje na Zajęcia Edukacyjne więcej niż jednego Kursanta, kwota Abonamentu na drugiego i kolejnego Kursanta ulega obniżeniu o <strong>10%</strong>.</span></div>
                    <div class="doc-clause"><span class="doc-clause-num">9.8.</span><span>Zajęcia Edukacyjne odbywają się raz w tygodniu, w wymiarze <strong>60 minut dla Grup Młodszych</strong> i <strong>90 minut dla Grup Starszych</strong>; w każdym miesiącu Zajęcia powinny odbyć się czterokrotnie.</span></div>
                    <div class="doc-clause"><span class="doc-clause-num">9.11.</span><span>Nieobecność Kursanta na poszczególnych zajęciach pozostaje bez wpływu na wysokość Abonamentu.</span></div>
                </div>

                {{-- 12. WYPOWIEDZENIE --}}
                <div class="doc-section">
                    <div class="doc-section-title">12. Wypowiedzenie umowy</div>
                    <div class="doc-clause"><span class="doc-clause-num">12.1.</span><span>Rodzic ma prawo wypowiedzieć Umowę ze skutkiem na koniec miesiąca kalendarzowego, w którym złożył wypowiedzenie. Wypowiedzenie dokonane w pierwszym miesiącu obowiązywania Umowy będzie skuteczne dopiero na koniec następnego miesiąca.</span></div>
                    <div class="doc-note doc-note--warn">
                        ⚠ Wypowiedzenie musi być dokonane w formie <strong>pisemnej lub e-mail</strong> pod rygorem nieważności. Oświadczenia złożone Trenerowi nie są skuteczne wobec Spółki.
                    </div>
                    <div class="doc-clause"><span class="doc-clause-num">12.3.</span><span>Spółka ma prawo wypowiedzieć Umowę ze skutkiem natychmiastowym w przypadku braku terminowej zapłaty Abonamentu lub niewłaściwego zachowania Kursanta zakłócającego prowadzenie zajęć.</span></div>
                </div>

                {{-- 14. ODSTĄPIENIE --}}
                <div class="doc-section">
                    <div class="doc-section-title">14. Prawo odstąpienia od Umowy</div>
                    <div class="doc-clause"><span class="doc-clause-num">14.1.</span><span>Usługobiorca będący Konsumentem ma prawo odstąpić od umowy bez podania jakiejkolwiek przyczyny w terminie <strong>14 dni</strong> od zawarcia Umowy.</span></div>
                    <div class="doc-clause"><span class="doc-clause-num">14.3.</span><span>Aby zachować termin do odstąpienia, wystarczy wysłać do Spółki na adres pocztowy lub e-mail informację dotyczącą odstąpienia przed upływem terminu.</span></div>
                    <div class="doc-clause"><span class="doc-clause-num">14.4.</span><span>Spółka zwróci Usługobiorcy wszystkie otrzymane płatności niezwłocznie, jednak nie później niż 14 dni od dnia, w którym Spółka została poinformowana o odstąpieniu od umowy.</span></div>
                </div>

                {{-- 15. REKLAMACJE --}}
                <div class="doc-section">
                    <div class="doc-section-title">15. Rozpatrywanie reklamacji</div>
                    <div class="doc-note">
                        📧 Reklamacje: <strong>dzialjakosci.indigo@gmail.com</strong> lub Al. Jerozolimskie 123A, p.19, 02-017 Warszawa. Termin rozpatrzenia: <strong>14 dni</strong>.
                    </div>
                </div>

                {{-- 17. DANE OSOBOWE --}}
                <div class="doc-section">
                    <div class="doc-section-title">17. Dane osobowe</div>
                    <div class="doc-clause"><span class="doc-clause-num">17.1.</span><span>Administratorem danych osobowych jest Spółka. Dane będą przetwarzane zgodnie z polityką prywatności dostępną pod adresem: indigomental.pl.</span></div>
                </div>

                <div class="doc-footer">
                    Niniejszy Regulamin ma zastosowanie do umów zawartych od dnia <strong>3 marca 2026 r.</strong><br>
                    © GLOBAL LEADERS SKILLS Sp. z o.o. · ul. Kabacki Dukt 1, 02-798 Warszawa
                </div>

            </div>{{-- /dv-paper --}}
            </div>{{-- /dv-scroll --}}
        </div>{{-- /dv-paper-wrap --}}


        {{-- ══════════════════════ RIGHT — SIDEBAR ══════════════════════ --}}
        <div class="dv-sidebar">

            <div class="dv-sign-card">
                <div class="dv-sign-card__title">
                    <i class="fas fa-pen-nib" style="color:var(--teal);margin-right:6px"></i>
                    Подписание документа
                </div>

                {{-- Checkbox — скрыт если документ уже подписан --}}
                @if(!$isSigned)
                <div class="dv-consent" id="consentBlock">
                    <input type="checkbox" class="dv-cb" id="cbRead">
                    <label class="dv-consent-label" for="cbRead">
                        Я ознакомился(-ась) с документом и принимаю все его условия
                    </label>
                </div>
                @endif

                {{-- Signed banner (hidden) --}}
                <div class="dv-signed {{ $isSigned ? 'show' : '' }}" id="signedBanner">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <div class="dv-signed__text">Документ подписан!</div>
                        <div class="dv-signed__sub" id="signedDate">{{ optional($document->sign_date)->format('d.m.Y H:i') }}</div>
                    </div>
                </div>

                {{-- Sign button --}}
                <div id="signBlock" style="{{ $isSigned ? 'display:none;' : '' }}">
                    <button class="dv-btn-sign" id="btnSign" disabled>
                        <i class="fas fa-pen-nib"></i>
                        Подписать документ
                    </button>
                </div>
            </div>

            <div class="dv-info-pill">
                <i class="fas fa-shield-alt"></i>
                Факт ознакомления и принятия документа фиксируется в системе с привязкой к вашему аккаунту и времени подписания.
            </div>

        </div>

    </div>
</div>
@endsection

@section('bottom-scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const docScroll    = document.getElementById('docScroll');
    const progressFill = document.getElementById('progressFill');
    const cbRead       = document.getElementById('cbRead');       // null когда подписан
    const btnSign      = document.getElementById('btnSign');      // null когда подписан
    const isSigned     = {{ $isSigned ? 'true' : 'false' }};

    /* ── Progress bar ── */
    function updateProgress() {
        const scrollable = docScroll.scrollHeight - docScroll.clientHeight;
        progressFill.style.width = scrollable <= 0
            ? '100%'
            : Math.min((docScroll.scrollTop / scrollable) * 100, 100) + '%';
    }
    docScroll.addEventListener('scroll', updateProgress);
    updateProgress();

    /* ── Scroll to top ── */
    document.getElementById('btnTop').addEventListener('click', () =>
        docScroll.scrollTo({ top: 0, behavior: 'smooth' }));

    /* ── Скачать PDF ── */
    document.getElementById('btnDownload').addEventListener('click', function () {
        const btn  = this;
        const icon = document.getElementById('btnDownloadIcon');

        btn.disabled = true;
        icon.className = 'fas fa-spinner fa-spin';

        fetch('{{ route("father.document.download", $document->id) }}', {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(async (r) => {
            if (!r.ok) {
                const text = await r.text().catch(() => '');
                throw new Error(text || 'Ошибка формирования PDF');
            }
            return r.blob();
        })
        .then((blob) => {
            const url  = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href  = url;
            link.download = 'Document-{{ $document->doc_no ?? $document->id }}.pdf';
            document.body.appendChild(link);
            link.click();
            link.remove();
            URL.revokeObjectURL(url);
        })
        .catch((err) => {
            alert(err.message || 'Не удалось скачать документ');
        })
        .finally(() => {
            btn.disabled = false;
            icon.className = 'fas fa-download';
        });
    });

    /* ── Checkbox → кнопка подписи ── */
    if (!isSigned && cbRead && btnSign) {
        cbRead.addEventListener('change', function () {
            btnSign.disabled = !this.checked;
        });
    }

    /* ── Подписать ── */
    if (!isSigned && btnSign) {
        btnSign.addEventListener('click', function () {
            btnSign.disabled = true;
            btnSign.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Сохраняем…';

            fetch('{{ route("father.documents.sign") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    document_id: {{ $document->id ?? 'null' }},
                    student_id:  {{ $student->id  ?? 'null' }}
                })
            })
            .then(async (r) => {
                const data = await r.json();
                if (!r.ok || !data.success) {
                    throw new Error(data.message || 'Ошибка подписи');
                }
                onSigned(data.signed_at);
            })
            .catch((error) => {
                btnSign.disabled = false;
                btnSign.innerHTML = '<i class="fas fa-pen-nib"></i> Подписать документ';
                alert(error.message || 'Не удалось подписать документ');
            });
        });
    }

    /* ── Показать состояние «подписан» ── */
    function onSigned(signedAt) {
        // скрыть кнопку
        const signBlock = document.getElementById('signBlock');
        if (signBlock) signBlock.style.display = 'none';

        // скрыть чекбокс
        const consentBlock = document.getElementById('consentBlock');
        if (consentBlock) consentBlock.style.display = 'none';

        // дата подписания
        if (signedAt) {
            const dt = new Date(String(signedAt).replace(' ', 'T'));
            const el = document.getElementById('signedDate');
            if (el) el.textContent =
                dt.toLocaleDateString('ru-RU') + ' · ' +
                dt.toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
        }

        // баннер
        document.getElementById('signedBanner').classList.add('show');
    }

});
</script>
@endsection
