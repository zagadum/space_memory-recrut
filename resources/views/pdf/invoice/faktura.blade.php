<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Faktura {{ $invoice->documentNumber }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 10pt;
            color: #1a1a1a;
        }
        body {
            padding: 30px 40px;
        }
        h1 {
            font-size: 16pt;
            margin-bottom: 6px;
        }
        .header-table {
            width: 100%;
            margin-bottom: 24px;
        }
        .header-table td {
            vertical-align: top;
            padding: 4px 0;
        }
        .party-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .party-table td {
            vertical-align: top;
            width: 50%;
            padding: 8px;
        }
        .party-label {
            font-size: 8pt;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .party-name {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 2px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            padding: 6px 8px;
            text-align: left;
            font-size: 8pt;
            text-transform: uppercase;
        }
        .items-table td {
            border: 1px solid #ccc;
            padding: 6px 8px;
        }
        .num {
            text-align: right;
        }
        .summary-table {
            width: 40%;
            margin-left: auto;
            margin-bottom: 30px;
        }
        .summary-table td {
            padding: 4px 8px;
        }
        .total {
            font-weight: bold;
            font-size: 12pt;
        }
        .exempt-note {
            font-size: 8pt;
            color: #555;
            margin-bottom: 16px;
        }
        .footer {
            font-size: 8pt;
            color: #888;
            margin-top: 40px;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }
        .meta-row td {
            font-size: 9pt;
            padding: 2px 0;
        }
    </style>
</head>
<body>

    <h1>Faktura VAT {{ $invoice->documentNumber }}</h1>

    <table class="header-table">
        <tr class="meta-row">
            <td><strong>Data wystawienia:</strong> {{ $invoice->issueDate }}</td>
            <td><strong>Data sprzedaży:</strong> {{ $invoice->saleDate }}</td>
        </tr>
        @if($invoice->paymentDueDate)
        <tr class="meta-row">
            <td><strong>Termin płatności:</strong> {{ $invoice->paymentDueDate }}</td>
            <td><strong>Metoda płatności:</strong> {{ $invoice->paymentMethod ?? 'przelew' }}</td>
        </tr>
        @endif
    </table>

    <table class="party-table">
        <tr>
            <td>
                <div class="party-label">Sprzedawca</div>
                <div class="party-name">Global Leaders Skills Sp. z o.o.</div>
                <div>NIP: 5252970924</div>
                <div>ul. Kabacki Dukt 1, 02-798 Warszawa</div>
                <div>KRS: 0001055763 &middot; REGON: 526267569</div>
                @if($invoice->bankAccount)
                    <div style="margin-top:6px;">Nr konta: {{ $invoice->bankAccount }}</div>
                @endif
            </td>
            <td>
                <div class="party-label">Nabywca</div>
                <div class="party-name">{{ $invoice->buyerName }}</div>
                @if($invoice->buyerNip)
                    <div>NIP: {{ $invoice->buyerNip }}</div>
                @endif
                <div>{{ $invoice->buyerAddress }}</div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width:30px;">Lp.</th>
                <th>Nazwa</th>
                <th style="width:40px;">Ilość</th>
                <th style="width:35px;">j.m.</th>
                <th style="width:85px;">Cena jedn. netto</th>
                <th style="width:60px;">Stawka VAT</th>
                <th style="width:70px;">Kwota VAT</th>
                <th style="width:85px;">Wartość brutto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td class="num">{{ $item->lp }}</td>
                <td>{{ $item->nazwa }}</td>
                <td class="num">{{ number_format($item->quantity, 0) }}</td>
                <td>{{ $item->unit }}</td>
                <td class="num">{{ number_format($item->unitPrice, 2, ',', ' ') }}</td>
                <td class="num">{{ $item->vatRate }}</td>
                <td class="num">{{ number_format($item->vatAmount, 2, ',', ' ') }}</td>
                <td class="num">{{ number_format($item->totalGross, 2, ',', ' ') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @php
        $totalNet   = collect($invoice->items)->sum(fn($i) => $i->totalNet);
        $totalVat   = collect($invoice->items)->sum(fn($i) => $i->vatAmount);
        $totalGross = collect($invoice->items)->sum(fn($i) => $i->totalGross);
    @endphp

    <table class="summary-table">
        <tr>
            <td>Razem netto:</td>
            <td class="num">{{ number_format($totalNet, 2, ',', ' ') }} {{ $invoice->currency }}</td>
        </tr>
        <tr>
            <td>Razem VAT:</td>
            <td class="num">{{ number_format($totalVat, 2, ',', ' ') }} {{ $invoice->currency }}</td>
        </tr>
        <tr>
            <td class="total">Do zapłaty:</td>
            <td class="num total">{{ number_format($totalGross, 2, ',', ' ') }} {{ $invoice->currency }}</td>
        </tr>
    </table>

    @if(collect($invoice->items)->contains(fn($i) => $i->vatRate === 'zw.'))
        <div class="exempt-note">
            Zwolnienie z VAT на podstawie art. 43 ust. 1 pkt 26 lit. a ustawy o VAT
            (usługi edukacyjne).
        </div>
    @endif

    <div class="footer">
        Global Leaders Skills Sp. z o.o. &middot; Biuro obsługi klienta: Al. Jerozolimskie 123A, 02-017 Warszawa
    </div>

</body>
</html>
