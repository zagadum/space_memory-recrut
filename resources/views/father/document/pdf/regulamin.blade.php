<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>{{ $document->title ?? 'Regulamin' }}</title>
    <style>
        * { margin: 0; padding: 0; font-family: "DejaVu Sans", sans-serif; font-size: 10pt; color: #1a1a1a; }
        body { padding: 30px 40px; }

        .doc-header { text-align: center; border-bottom: 2px solid #1a1a1a; padding-bottom: 18px; margin-bottom: 24px; }
        .doc-header h1 { font-size: 14pt; font-weight: bold; margin-bottom: 4px; }
        .doc-header .subtitle { font-size: 10pt; color: #444; margin-bottom: 2px; }
        .doc-header .meta { font-size: 8.5pt; color: #888; margin-top: 6px; }

        .parties-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .parties-table td { width: 50%; vertical-align: top; padding: 8px; border: 1px solid #ccc; }
        .party-label { font-size: 7.5pt; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; font-weight: bold; }
        .party-row { margin-bottom: 3px; }
        .party-key { font-size: 8pt; color: #999; text-transform: uppercase; }
        .party-val { font-size: 10pt; font-weight: bold; color: #0b6e8a; }

        .section { margin-bottom: 16px; }
        .section-title { font-size: 9pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.6px;
            border-bottom: 1px solid #ddd; padding-bottom: 4px; margin-bottom: 8px; }
        .clause { display: flex; margin-bottom: 5px; font-size: 9.5pt; color: #222; text-align: justify; }
        .clause-num { font-weight: bold; min-width: 36px; flex-shrink: 0; }
        .note { background: #f5f5f5; border-left: 3px solid #aaa; padding: 7px 10px; margin: 8px 0;
            font-size: 9pt; color: #444; }
        .note-warn { background: #fffbf0; border-left-color: #d97706; color: #6b4a00; }

        .sign-block { margin-top: 30px; border-top: 1px solid #ccc; padding-top: 16px; }
        .sign-title { font-size: 9pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;
            color: #555; margin-bottom: 10px; }
        .sign-table { width: 100%; }
        .sign-table td { width: 50%; vertical-align: top; padding: 6px 0; }
        .sign-label { font-size: 8pt; color: #888; margin-bottom: 4px; }
        .sign-line { border-top: 1px solid #aaa; margin-top: 30px; padding-top: 4px; font-size: 8pt; color: #aaa; }
        .sign-status { font-size: 10pt; font-weight: bold; }
        .sign-status.signed { color: #16a34a; }
        .sign-status.pending { color: #b45309; }

        .doc-footer { margin-top: 24px; border-top: 1px solid #ccc; padding-top: 10px;
            font-size: 8pt; color: #aaa; text-align: center; line-height: 1.6; }
    </style>
</head>
<body>

{{-- NAGŁÓWEK --}}
<div class="doc-header">
    <div style="font-size:8pt;color:#555;text-transform:uppercase;letter-spacing:1px;margin-bottom:8px;">Global Leaders Skills Sp. z o.o.</div>
    <h1>Regulamin świadczenia usług,<br>w tym świadczenia usług drogą elektroniczną</h1>
    <div class="subtitle">przez spółkę GLOBAL LEADERS SKILLS sp. z o.o. z siedzibą w Warszawie</div>
    <div class="meta">wersja obowiązująca od dnia 03.03.2026 r.</div>
    @if($document->doc_no)
        <div class="meta" style="margin-top:4px;">Nr dokumentu: <strong>{{ $document->doc_no }}</strong></div>
    @endif
</div>

{{-- STRONY UMOWY --}}
<div class="section">
    <div class="section-title">Strony umowy</div>
    <table class="parties-table">
        <tr>
            <td>
                <div class="party-label">Wykonawca</div>
                <div class="party-row"><div class="party-key">Nazwa</div><div class="party-val" style="color:#1a1a1a;">GLOBAL LEADERS SKILLS Sp. z o.o.</div></div>
                <div class="party-row"><div class="party-key">Adres</div><div style="font-size:9.5pt;">ul. Kabacki Dukt 1, lok. U1 i U2, 02-798 Warszawa</div></div>
                <div class="party-row"><div class="party-key">KRS / NIP</div><div style="font-size:9.5pt;">0001055763 / 5252970924</div></div>
            </td>
            <td>
                <div class="party-label">Klient (Rodzic)</div>
                <div class="party-row"><div class="party-key">Imię i nazwisko</div><div class="party-val">{{ $parentName }}</div></div>
                <div class="party-row"><div class="party-key">Email</div><div style="font-size:9.5pt;">{{ $student->email }}</div></div>
                <div class="party-row"><div class="party-key">Kursant</div><div class="party-val">{{ $student->full_name ?: '—' }}</div></div>
                @if($student->city)<div class="party-row"><div class="party-key">Miejscowość</div><div style="font-size:9.5pt;">{{ $student->city }}</div></div>@endif
            </td>
        </tr>
    </table>
</div>

{{-- 1. DEFINICJE --}}
<div class="section">
    <div class="section-title">1. Definicje</div>
    <div class="clause"><span class="clause-num">1.1.</span><span><strong>Abonament</strong> – opłata za miesięczny pakiet Zajęć Edukacyjnych i zapewnienie dostępu do Platformy Edukacyjnej.</span></div>
    <div class="clause"><span class="clause-num">1.2.</span><span><strong>Godzina lekcyjna</strong> – 30 minut dla Grup Młodszych i 45 minut dla Grup Starszych.</span></div>
    <div class="clause"><span class="clause-num">1.7.</span><span><strong>Kursant</strong> – dziecko uczęszczające na zajęcia edukacyjne organizowane przez Spółkę.</span></div>
    <div class="clause"><span class="clause-num">1.11.</span><span><strong>Rodzic</strong> – rodzic lub opiekun prawny Kursanta lub inna osoba, która zawarła umowę.</span></div>
    <div class="clause"><span class="clause-num">1.12.</span><span><strong>Spółka</strong> – GLOBAL LEADERS SKILLS Sp. z o.o., ul. Kabacki Dukt 1, 02-798 Warszawa; KRS: 0001055763; NIP: 5252970924.</span></div>
    <div class="clause"><span class="clause-num">1.14.</span><span><strong>Umowa</strong> – umowa zawarta pomiędzy Spółką a Rodzicem Kursanta; Regulamin stanowi jej integralną część.</span></div>
</div>

{{-- 2. POSTANOWIENIA --}}
<div class="section">
    <div class="section-title">2. Postanowienia ogólne</div>
    <div class="clause"><span class="clause-num">2.1.</span><span>Dokument reguluje zasady świadczenia usług w zakresie arytmetyki mentalnej, szybkiego czytania i technik szybkiego zapamiętywania.</span></div>
    <div class="clause"><span class="clause-num">2.3.</span><span>Korzystanie z usług możliwe jest po akceptacji niniejszego Regulaminu i zapoznaniu się z Polityką Prywatności.</span></div>
</div>

{{-- 5. ZAJĘCIA --}}
<div class="section">
    <div class="section-title">5. Podstawowe postanowienia o świadczeniu Zajęć Edukacyjnych</div>
    <div class="clause"><span class="clause-num">5.2.</span><span>Podstawowy czas trwania kursu to <strong>24 miesiące</strong>.</span></div>
    <div class="clause"><span class="clause-num">5.5.</span><span>Zajęcia odbywają się <strong>raz w tygodniu</strong> zgodnie z harmonogramem Grupy Zajęciowej.</span></div>
    <div class="clause"><span class="clause-num">5.7.</span><span>Rodzic zobowiązany do uiszczenia zadatku w kwocie <strong>200 zł</strong> w terminie 2 dni od zawarcia Umowy.</span></div>
</div>

{{-- 9. ODPŁATNOŚĆ --}}
<div class="section">
    <div class="section-title">9. Odpłatność</div>
    <div class="note">Kwota Abonamentu ustalona jest w Umowie. Płatność miesięczna z góry, do końca poprzedniego miesiąca, przez system <strong>PayU</strong>.</div>
    <div class="clause"><span class="clause-num">9.6.</span><span>Przy zapisie więcej niż jednego Kursanta – zniżka <strong>10%</strong> na każdego kolejnego.</span></div>
</div>

{{-- 12. WYPOWIEDZENIE --}}
<div class="section">
    <div class="section-title">12. Wypowiedzenie umowy</div>
    <div class="clause"><span class="clause-num">12.1.</span><span>Rodzic może wypowiedzieć Umowę ze skutkiem na koniec miesiąca, w którym złożył wypowiedzenie.</span></div>
    <div class="note note-warn">⚠ Wypowiedzenie wyłącznie w formie <strong>pisemnej lub e-mail</strong>. Oświadczenia złożone Trenerowi nie są skuteczne.</div>
</div>

{{-- PODPIS --}}
<div class="sign-block">
    <div class="sign-title">Status podpisania dokumentu</div>
    <table class="sign-table">
        <tr>
            <td>
                <div class="sign-label">Klient (Rodzic)</div>
                @if($isSigned)
                    <div class="sign-status signed">✓ Dokument podpisany</div>
                    <div style="font-size:9pt;color:#555;margin-top:3px;">Data: {{ optional($document->sign_date)->format('d.m.Y H:i') }}</div>
                @else
                    <div class="sign-status pending">Oczekuje na podpis</div>
                @endif
                <div class="sign-line">{{ $parentName }}</div>
            </td>
            <td>
                <div class="sign-label">W imieniu GLOBAL LEADERS SKILLS Sp. z o.o.</div>
                <div class="sign-line">Podpis i pieczęć</div>
            </td>
        </tr>
    </table>
</div>

<div class="doc-footer">
    Niniejszy Regulamin ma zastosowanie do umów zawartych od dnia <strong>3 marca 2026 r.</strong><br>
    © GLOBAL LEADERS SKILLS Sp. z o.o. · ul. Kabacki Dukt 1, 02-798 Warszawa<br>
    Wygenerowano: {{ now()->format('d.m.Y H:i') }}
</div>

</body>
</html>

