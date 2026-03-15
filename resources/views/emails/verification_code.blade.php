<x-mail::message>
# Twój kod weryfikacyjny

Dziękujemy za rejestrację w Space Memory. Użyj poniższego kodu, чтобы подтвердить свой email:

<x-mail::panel>
# {{ $code }}
</x-mail::panel>

Jeśli nie rejestrowałeś się w нашем сервисе, просто игнорируйте это письмо.

Dziękujemy,<br>
Zespół {{ config('app.name') }}
</x-mail::message>
