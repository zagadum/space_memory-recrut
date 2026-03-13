<?php

return [
    'email' => [
        'subject' => 'Zaproszenie do rejestracji w programie :subject',
        'greeting' => 'Szanowna/y :name,',
        'invitation_text' => 'Zapraszamy do rejestracji w programie **:subject**!',
        'action_text' => 'Kliknij poniższy przycisk, aby rozpocząć rejestrację:',
        'action_button' => 'Zarejestruj się',
        'footer_address' => 'Global Leaders Skills Sp. z o.o. · Al. Jerozolimskie 123A, 02-017 Warszawa',
    ],
    'registration' => [
        'complete_title' => 'Dokończ rejestrację',
        'password' => 'Hasło',
        'password_confirmation' => 'Powtórz hasło',
        'consent_data' => 'Wyrażam zgodę na przetwarzanie moich danych osobowych w celu rejestracji.',
        'consent_policy' => 'Akceptuję politykę prywatności.',
        'consent_photo' => 'Wyrażam zgodę na wykorzystanie wizerunku (foto/wideo).',
        'submit' => 'Zarejestruj się',
        'invalid_token' => 'Nieprawidłowy lub wygasły token.',
        'success' => 'Rejestracja zakończona sukcesem!',
    ],
    'campaign' => [
        'already_in_progress' => 'Kampania jest już w trakcie realizacji',
        'started' => 'Kampania została uruchomiona',
        'import_new_only' => 'Użyj POST /api/v1/recruiting/campaigns aby zaimportować nową kampanię',
    ],
];
