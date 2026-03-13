<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('father.payments.redirect_title') }}</title>
    <link rel="stylesheet" href="{{ asset('css/father.css') }}">
</head>
<body class="father-portal father-portal--centered">
    <div class="father-login">
        <div class="father-login__logo">Global Leaders Skills</div>
        <h1 class="father-login__title">{{ __('father.payments.redirecting') }}</h1>
        <p class="father-login__subtitle">{{ __('father.payments.redirect_text') }}</p>

        <div class="father-spinner"></div>

        <form id="imoje-form" method="POST" action="{{ $payUrl }}" style="display:none;">
            @foreach($fields as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
        </form>

        <noscript>
            <p>{{ __('father.payments.js_disabled') }}</p>
            <form method="POST" action="{{ $payUrl }}">
                @foreach($fields as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <button type="submit" class="father-btn father-btn--primary">
                    {{ __('father.payments.pay_now') }}
                </button>
            </form>
        </noscript>
    </div>

    <script>
        document.getElementById('imoje-form').submit();
    </script>
</body>
</html>
