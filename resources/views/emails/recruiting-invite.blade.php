<!DOCTYPE html>
<html lang="pl">
<head><meta charset="UTF-8"></head>
<body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">

    <div style="text-align: center; margin-bottom: 24px;">
        <h1 style="color: #1a1a1a; font-size: 22px;">Global Leaders Skills</h1>
    </div>

    <p>Szanowna/y {{ $name }},</p>

    <p>Zapraszamy do rejestracji w programie <strong>{{ $subject ?? 'Space Memory' }}</strong>!</p>

    <p>Kliknij poniższy przycisk, aby rozpocząć rejestrację:</p>

    <div style="text-align: center; margin: 32px 0;">
        <a href="{{ $registerUrl }}"
           style="display: inline-block; padding: 14px 32px; background: #4f6ef7; color: #fff; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 16px;">
            Zarejestruj się
        </a>
    </div>

    <p style="font-size: 12px; color: #888;">
        Global Leaders Skills Sp. z o.o. · Al. Jerozolimskie 123A, 02-017 Warszawa
    </p>

</body>
</html>
