<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Space Memory - {{ __('recruiting.registration.complete_title') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0b1d26;
            --primary-accent: #41e1e8;
            --secondary-accent: #eb8b11;
            --success-color: #41e8a3;
            --error-color: #ff4d4d;
            --text-main: #ffffff;
            --glass-bg: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(65, 225, 232, 0.8);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Nunito', sans-serif;
        }

        body {
            background: radial-gradient(circle at center, #0f2a36 0%, #050f14 100%);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 20px;
        }

        .main-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 500px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .logo {
            width: 120px;
            margin-bottom: 30px;
            filter: drop-shadow(0 0 15px rgba(65, 225, 232, 0.4));
        }

        .glass-card {
            width: 100%;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 30px;
            padding: 40px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.6);
        }

        h1 {
            font-size: 24px;
            font-weight: 800;
            color: var(--primary-accent);
            margin-bottom: 25px;
            text-align: center;
            text-transform: uppercase;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.7);
        }

        input[type="password"] {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(65, 225, 232, 0.3);
            border-radius: 12px;
            padding: 12px 15px;
            color: white;
            outline: none;
            transition: 0.3s;
        }

        input[type="password"]:focus {
            border-color: var(--primary-accent);
            box-shadow: 0 0 15px rgba(65, 225, 232, 0.2);
        }

        .consent-group {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 15px;
            cursor: pointer;
        }

        .consent-group input {
            margin-top: 4px;
            accent-color: var(--primary-accent);
        }

        .consent-text {
            font-size: 13px;
            line-height: 1.4;
            color: rgba(255, 255, 255, 0.8);
        }

        .error-list {
            background: rgba(255, 77, 77, 0.1);
            border: 1px solid var(--error-color);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
            list-style: none;
        }

        .error-list li {
            color: var(--error-color);
            font-size: 13px;
        }

        .submit-btn {
            width: 100%;
            padding: 16px;
            border-radius: 15px;
            background: linear-gradient(135deg, var(--primary-accent) 0%, #29a8ad 100%);
            border: none;
            color: #062330;
            font-size: 18px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            margin-top: 10px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(65, 225, 232, 0.5);
        }

        .student-info {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .student-name {
            font-size: 18px;
            font-weight: 600;
        }

        .student-email {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body>

    <div class="main-container">
        <img src="http://indigomental-sklep.pl/wp-content/uploads/2026/02/logo_space-memory.png" alt="Logo" class="logo">

        <div class="glass-card">
            <h1>{{ __('recruiting.registration.complete_title') }}</h1>

            <div class="student-info">
                <div class="student-name">{{ $import->name }} {{ $import->surname }}</div>
                <div class="student-email">{{ $import->email }}</div>
            </div>

            @if ($errors->any())
                <ul class="error-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <form action="{{ route('registration.complete.store', $token) }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>{{ __('recruiting.registration.password') }}</label>
                    <input type="password" name="password" required>
                </div>

                <div class="form-group">
                    <label>{{ __('recruiting.registration.password_confirmation') }}</label>
                    <input type="password" name="password_confirmation" required>
                </div>

                <label class="consent-group">
                    <input type="checkbox" name="consent_data" value="1" required>
                    <span class="consent-text">{{ __('recruiting.registration.consent_data') }}</span>
                </label>

                <label class="consent-group">
                    <input type="checkbox" name="consent_policy" value="1" required>
                    <span class="consent-text">{{ __('recruiting.registration.consent_policy') }}</span>
                </label>

                <label class="consent-group">
                    <input type="checkbox" name="consent_photo" value="1" required>
                    <span class="consent-text">{{ __('recruiting.registration.consent_photo') }}</span>
                </label>

                <button type="submit" class="submit-btn">{{ __('recruiting.registration.submit') }}</button>
            </form>
        </div>
    </div>

</body>
</html>
