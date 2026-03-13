<!DOCTYPE html>
<html lang="pl">
<head><meta charset="UTF-8"></head>
<body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">

    <div style="text-align: center; margin-bottom: 24px;">
        <h1 style="color: #1a1a1a; font-size: 22px;">Global Leaders Skills</h1>
    </div>

    <p>{{ __('recruiting.email.greeting', ['name' => $name]) }}</p>

    <p>{!! __('recruiting.email.invitation_text', ['subject' => $subject ?? 'Space Memory']) !!}</p>

    <p>{{ __('recruiting.email.action_text') }}</p>

    <div style="text-align: center; margin: 32px 0;">
        <a href="{{ $registerUrl }}"
           style="display: inline-block; padding: 14px 32px; background: #4f6ef7; color: #fff; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 16px;">
            {{ __('recruiting.email.action_button') }}
        </a>
    </div>

    <p style="font-size: 12px; color: #888;">
        {{ __('recruiting.email.footer_address') }}
    </p>

</body>
</html>
