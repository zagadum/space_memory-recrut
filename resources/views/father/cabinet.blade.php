<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Space Memory - Личный кабинет</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0b1d26;
            --primary-accent: #41e1e8;
            --secondary-accent: #eb8b11;
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
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .main-container {
            width: 100%;
            max-width: 600px;
            padding: 20px;
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 30px;
            padding: 50px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.6);
            text-align: center;
        }

        h1 {
            color: var(--primary-accent);
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        p {
            font-size: 18px;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 40px;
        }

        .logout-btn {
            padding: 12px 30px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: var(--secondary-accent);
            color: var(--secondary-accent);
        }
    </style>
</head>
<body>

    <div class="main-container">
        <div class="glass-card">
            <h1>Личный кабинет</h1>
            <p>Добро пожаловать в Space Memory! Ваш аккаунт успешно подтвержден. <br> Здесь скоро появится информация о ваших занятиях.</p>
            
            <button class="logout-btn" onclick="logout()">ВЫЙТИ</button>
        </div>
    </div>

    <script>
        window.location.href = '/test-parent-portal';
        function logout() {
            localStorage.clear();
            window.location.href = '/register';
        }
    </script>
</body>
</html>
