<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('father.login.title') }} — GLS</title>
    <link rel="stylesheet" href="{{ asset('css/father.css') }}">
</head>
<body class="father-portal father-portal--centered">
    <div class="father-login">
        <div class="father-login__logo">Global Leaders Skills</div>
        <h1 class="father-login__title">{{ __('father.login.title') }}</h1>
        <p class="father-login__subtitle">{{ __('father.login.subtitle') }}</p>

        <form method="POST" action="{{ route('father.login.submit') }}">
            @csrf

            <div class="father-form-group">
                <label class="father-label">{{ __('father.login.email') }}</label>
                <input type="email" name="email" value="{{ old('email', $email) }}" 
                       required autofocus class="father-input @error('email') father-input--invalid @enderror" placeholder="jan@example.pl">
                @error('email')
                    <div class="father-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="father-form-group">
                <label class="father-label">{{ __('father.login.password') }}</label>
                <input type="password" name="password" required 
                       class="father-input @error('password') father-input--invalid @enderror" placeholder="••••••••">
                @error('password')
                    <div class="father-error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="father-btn father-btn--primary father-btn--full">
                {{ __('father.login.submit') }}
            </button>
        </form>

        <div class="father-login__footer">
            <a href="#">{{ __('father.login.forgot_password') }}</a>
        </div>
    </div>
</body>
</html>
