<!DOCTYPE html>
<html lang="pl">
<head><meta charset="UTF-8"></head>
<body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; color: #1a1a1a;">

    <div style="text-align: center; margin-bottom: 24px;">
        <h1 style="font-size: 20px; color: #1a1a1a; margin: 0;">Global Leaders Skills</h1>
        <p style="font-size: 12px; color: #888; margin: 4px 0 0;">Sp. z o.o.</p>
    </div>

    <p>{{ __('invoice_email.greeting', ['name' => $studentName]) }}</p>

    <p>{{ __('invoice_email.body', ['number' => $invoiceNumber, 'service' => $serviceName]) }}</p>

    <table style="width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 14px;">
        <tr style="background: #f7f8fc;">
            <td style="padding: 10px 14px; border: 1px solid #eee; font-weight: 600;">
                {{ __('invoice_email.number') }}
            </td>
            <td style="padding: 10px 14px; border: 1px solid #eee;">
                {{ $invoiceNumber }}
            </td>
        </tr>
        <tr>
            <td style="padding: 10px 14px; border: 1px solid #eee; font-weight: 600;">
                {{ __('invoice_email.issue_date') }}
            </td>
            <td style="padding: 10px 14px; border: 1px solid #eee;">
                {{ $issueDate }}
            </td>
        </tr>
        <tr style="background: #f7f8fc;">
            <td style="padding: 10px 14px; border: 1px solid #eee; font-weight: 600;">
                {{ __('invoice_email.amount') }}
            </td>
            <td style="padding: 10px 14px; border: 1px solid #eee; font-weight: 700;">
                {{ $amount }} {{ $currency }}
            </td>
        </tr>
    </table>

    <p>{{ __('invoice_email.attachment_note') }}</p>

    <p style="margin-top: 24px;">{{ __('invoice_email.closing') }}</p>

    <div style="margin-top: 32px; padding-top: 16px; border-top: 1px solid #eee; font-size: 11px; color: #888;">
        Global Leaders Skills Sp. z o.o. · Al. Jerozolimskie 123A, 02-017 Warszawa
        <br>NIP: 5252970924 · KRS: 0001055763
    </div>

</body>
</html>
