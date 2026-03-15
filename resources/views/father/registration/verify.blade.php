<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="verify-form-token" content="{{ $verifyFormToken }}">
    <title>Space Memory - Подтверждение регистрации</title>
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
            overflow: hidden;
            position: relative;
        }

        #starfield {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        .lang-switcher {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 20;
            display: flex;
            gap: 10px;
            background: var(--glass-bg);
            padding: 8px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
        }

        .lang-btn {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.6);
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            padding: 4px 8px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .lang-btn.active {
            color: #050f14;
            background: var(--primary-accent);
            box-shadow: 0 0 15px var(--primary-accent);
        }

        .main-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 420px;
            padding: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: -50px;
        }

        .logo {
            width: 150px;
            margin-bottom: 40px;
            filter: drop-shadow(0 0 15px rgba(65, 225, 232, 0.4));
            animation: float 4s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .glass-card {
            width: 100%;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 30px;
            padding: 60px 30px 40px;
            position: relative;
            box-shadow: 0 25px 60px rgba(0,0,0,0.6);
            text-align: center;
            transition: all 0.5s ease;
        }

        .icon-container {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(5, 15, 20, 0.9);
            position: absolute;
            top: -50px;
            left: 50%;
            transform: translateX(-50%);
            border: 2px solid var(--primary-accent);
            box-shadow: 0 0 30px rgba(65, 225, 232, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
            transition: all 0.5s ease;
        }

        .icon-container svg {
            width: 50px;
            height: 50px;
            fill: var(--primary-accent);
        }

        .icon-container.success {
            border-color: var(--success-color);
            box-shadow: 0 0 30px rgba(65, 232, 163, 0.5);
        }

        .icon-container.success svg {
            fill: var(--success-color);
        }

        .success-title {
            font-size: 24px;
            font-weight: 800;
            color: var(--primary-accent);
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .success-message {
            font-size: 15px;
            line-height: 1.5;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 25px;
        }

        /* Код подтверждения */
        .code-input-container {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-bottom: 20px;
        }

        .code-digit {
            width: 50px;
            height: 60px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(65, 225, 232, 0.3);
            border-radius: 12px;
            font-size: 28px;
            color: white;
            text-align: center;
            line-height: 60px;
            outline: none;
            transition: all 0.3s ease;
        }

        .code-digit:focus {
            border-color: var(--primary-accent);
            background: rgba(65, 225, 232, 0.1);
            box-shadow: 0 0 15px rgba(65, 225, 232, 0.2);
        }

        .error-msg {
            color: var(--error-color);
            font-size: 14px;
            margin-top: -10px;
            margin-bottom: 15px;
            display: none;
            animation: shake 0.4s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .action-btn {
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
        }

        .action-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(65, 225, 232, 0.5);
        }

        .action-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .resend-link {
            display: block;
            margin-top: 20px;
            color: var(--primary-accent);
            text-decoration: none;
            font-size: 13px;
            opacity: 0.8;
            transition: 0.3s;
            background: none;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        .resend-link:hover:not(:disabled) { opacity: 1; text-decoration: underline; }
        .resend-link:disabled { color: gray; cursor: default; }

        /* Футер */
        .site-footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 12px;
            z-index: 10;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(5px);
        }

        .footer-company {
            text-align: center;
            color: rgba(255, 255, 255, 0.3);
            font-size: 10px;
            margin-bottom: 5px;
        }

        .footer-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.4);
            text-decoration: none;
            font-size: 9px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    <canvas id="starfield"></canvas>

    <div class="lang-switcher">
        <button class="lang-btn" data-lang="en">EN</button>
        <button class="lang-btn" data-lang="pl">PL</button>
        <button class="lang-btn" data-lang="ua">UA</button>
        <button class="lang-btn active" data-lang="ru">RU</button>
    </div>

    <div class="main-container">
        <img src="http://indigomental-sklep.pl/wp-content/uploads/2026/02/logo_space-memory.png" alt="Logo" class="logo">

        <div class="glass-card" id="card">
            <div class="icon-container" id="mainIcon">
                <svg viewBox="0 0 24 24" id="svgIcon">
                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                </svg>
            </div>

            <h2 class="success-title" id="title">Введите код</h2>
            <p class="success-message" id="message">
                Мы отправили 4-значный код подтверждения на ваш имейл.
            </p>
            
            <div class="father-form-group" style="margin-bottom: 20px;">
                <label class="father-label" style="display: block; margin-bottom: 8px; color: var(--primary-accent); font-weight: 600;">{{ __('father.verify.email_label') ?? 'Adres email' }}</label>
                <input type="email" name="email" id="verify-email" required 
                       placeholder="jan@example.pl"
                       class="father-input" 
                       style="width: 100%; padding: 12px; border-radius: 12px; border: 1px solid var(--glass-border); background: rgba(255,255,255,0.05); color: white;"
                       value="">
            </div>

            <div class="code-input-container" id="inputSection">
                <input type="text" maxlength="1" class="code-digit" autofocus>
                <input type="text" maxlength="1" class="code-digit">
                <input type="text" maxlength="1" class="code-digit">
                <input type="text" maxlength="1" class="code-digit">
                <input type="text" maxlength="1" class="code-digit">
                <input type="text" maxlength="1" class="code-digit">
            </div>

            <div class="error-msg" id="error">Неверный код. Попробуйте еще раз.</div>

            <button class="action-btn" id="confirmBtn">Подтвердить</button>
            
            <button class="resend-link" id="resendBtn" disabled>Отправить код еще раз</button>
        </div>
    </div>

    <footer class="site-footer">
        <div class="footer-company" id="footerCompany">
            SPACE MEMORY ASSOCIATION Sp. z o.o. | Adres: Al. Jerozolimskie 123A, 02-017, Warszawa, Polska.
        </div>
        <div class="footer-links" id="footerLinks">
            <a href="#">Niezbędnik Space Memory</a>
            <a href="#">Standardy Ochrony Małoletnich</a>
            <a href="#">Oświadczenie o odstąpieniu</a>
            <a href="#">Polityka prywatności</a>
            <a href="#">Klauzula RODO</a>
            <a href="#">Regulamin</a>
        </div>
    </footer>

    <script>
        const translations = {
            en: {
                title: "Enter Code",
                message: "We sent a 6-digit confirmation code to your email.",
                emailLabel: "Email Address",
                confirm: "Confirm",
                resend: "Resend code",
                wait: "Resend in ",
                sec: "s",
                error: "Invalid code. Try again.",
                emailError: "Please provide a valid email",
                codeError: "Enter 6-digit code",
                successTitle: "Success!",
                successMsg: "Your email is verified. Welcome to Space Memory!",
                startBtn: "Start working"
            },
            pl: {
                title: "Wprowadź kod",
                message: "Wysłaliśmy 6-cyfrowy kod potwierdzający na Twój e-mail.",
                emailLabel: "Adres email",
                confirm: "Potwierdź",
                resend: "Wyślij kod ponownie",
                wait: "Wyślij ponownie za ",
                sec: "s",
                error: "Nieprawidłowy kod. Spróbuj ponownie.",
                emailError: "Proszę podać poprawny adres email",
                codeError: "Wprowadź 6-cyfrowy kod",
                successTitle: "Sukces!",
                successMsg: "Twój e-mail został zweryfikowany. Witamy w Space Memory!",
                startBtn: "Rozpocznij pracę"
            },
            ua: {
                title: "Введіть код",
                message: "Ми надіслали 6-значний код підтвердження на вашу електронну пошту.",
                emailLabel: "Електронна пошта",
                confirm: "Підтвердити",
                resend: "Надіслати код ще раз",
                wait: "Надіслати ще раз через ",
                sec: "с",
                error: "Невірний код. Спробуйте ще раз.",
                emailError: "Будь ласка, введіть коректну електронну пошту",
                codeError: "Введіть 6-значний код",
                successTitle: "Успішно!",
                successMsg: "Вашу пошту підтверджено. Ласкаво просимо до Space Memory!",
                startBtn: "Почати роботу"
            },
            ru: {
                title: "Введите код",
                message: "Мы отправили 6-значный код подтверждения на ваш имейл.",
                emailLabel: "Электронная почта",
                confirm: "Подтвердить",
                resend: "Отправить код еще раз",
                wait: "Отправить заново через ",
                sec: "с",
                error: "Неверный код. Попробуйте еще раз.",
                emailError: "Пожалуйста, введите корректный имейл",
                codeError: "Введите 6-значный код",
                successTitle: "Успешно!",
                successMsg: "Ваша почта подтверждена. Добро пожаловать в Space Memory!",
                startBtn: "Начать работу"
            }
        };

        let currentLang = 'pl'; // Default or from URL

        document.querySelectorAll('.lang-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.lang-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                currentLang = btn.dataset.lang;
                applyTranslations(currentLang);
            });
        });

        function applyTranslations(lang) {
            const t = translations[lang];
            document.getElementById('title').textContent = t.title;
            document.getElementById('message').textContent = t.message;
            document.getElementById('confirmBtn').textContent = t.confirm;
            document.getElementById('resendBtn').textContent = t.resend;
            document.getElementById('title').textContent = t.title;
            document.querySelector('.father-label').textContent = t.emailLabel;
        }

        // Auto-detect lang if needed
        const urlLang = new URLSearchParams(window.location.search).get('lang');
        if (urlLang && translations[urlLang]) {
            currentLang = urlLang;
            document.querySelectorAll('.lang-btn').forEach(b => {
                b.classList.toggle('active', b.dataset.lang === currentLang);
            });
            applyTranslations(currentLang);
        }
        const inputs = document.querySelectorAll('.code-digit');
        const confirmBtn = document.getElementById('confirmBtn');
        const errorMsg = document.getElementById('error');
        const title = document.getElementById('title');
        const message = document.getElementById('message');
        const mainIcon = document.getElementById('mainIcon');
        const inputSection = document.getElementById('inputSection');
        const resendBtn = document.getElementById('resendBtn');
        const svgIcon = document.getElementById('svgIcon');
        const emailInput = document.getElementById('verify-email');
        
        // Try to get email from localStorage or URL
        const userEmail = localStorage.getItem('student_email') || new URLSearchParams(window.location.search).get('email');
        if (userEmail) emailInput.value = userEmail;

        // Auto-paste logic
        document.addEventListener('paste', (e) => {
            const pasteData = (e.clipboardData || window.clipboardData).getData('text').trim();
            if (pasteData.length === 6 && /^\d+$/.test(pasteData)) {
                e.preventDefault();
                pasteData.split('').forEach((char, i) => {
                    if (inputs[i]) inputs[i].value = char;
                });
                inputs[5].focus();
                verifyCode(pasteData);
            }
        });

        // Логика ввода кода
        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                const val = e.target.value;
                if (val && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
                
                // Авто-отправка если все поля заполнены
                let code = "";
                inputs.forEach(i => code += i.value);
                if (code.length === 6) {
                    verifyCode(code);
                }
            });
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });

        confirmBtn.addEventListener('click', () => {
            let code = "";
            inputs.forEach(input => code += input.value);
            if (code.length === 6) {
                verifyCode(code);
            } else {
                showError("Wprowadź 6-cyfrowy kod");
            }
        });

        async function verifyCode(code) {
            const email = emailInput.value.trim();
            if (!email || !email.includes('@')) {
                showError("Proszę podać poprawny adres email");
                emailInput.focus();
                return;
            }

            confirmBtn.disabled = true;
            confirmBtn.textContent = "...";
            errorMsg.style.display = 'none';

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const formToken = document.querySelector('meta[name="verify-form-token"]').getAttribute('content');

            try {
                const response = await fetch('/recruitment/verify-code', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Form-Token': formToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        email: email,
                        code: code,
                        locale: currentLang
                    })
                });
                const data = await response.json();
                
                if (data.success) {
                    localStorage.setItem('api_token', data.api_token);
                    localStorage.setItem('student_email', email);
                    showSuccess();
                } else {
                    showError(data.message || "Неверный код. Попробуйте еще раз.");
                }
            } catch (err) {
                showError("Ошибка соединения с сервером");
            } finally {
                if (inputSection.style.display !== 'none') {
                     confirmBtn.disabled = false;
                     confirmBtn.textContent = "Подтвердить";
                }
            }
        }

        function showError(text) {
            errorMsg.textContent = text;
            errorMsg.style.display = 'block';
            inputs.forEach(input => {
                input.style.borderColor = 'var(--error-color)';
                input.value = "";
            });
            inputs[0].focus();
        }

        function showSuccess() {
            errorMsg.style.display = 'none';
            inputSection.style.display = 'none';
            resendBtn.style.display = 'none';
            emailInput.parentElement.style.display = 'none';
            confirmBtn.disabled = false;
            confirmBtn.textContent = "Начать работу";
            
            title.textContent = "Успешно!";
            title.style.color = "var(--success-color)";
            message.textContent = "Ваша почта подтверждена. Добро пожаловать в Space Memory!";
            
            mainIcon.classList.add('success');
            svgIcon.innerHTML = `
                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" fill="var(--success-color)"/>
            `;

            confirmBtn.onclick = () => {
                window.location.href = '/father/parent-portal';
            };
        }

        // Таймер для повторной отправки
        let timer = 60;
        let timerInterval;

        resendBtn.addEventListener('click', async () => {
            if(timer > 0) return;
            const email = emailInput.value.trim();
            if (!email) {
                alert("Wprowadź email");
                return;
            }
            
            try {
                const resendCsrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const resendFormToken = document.querySelector('meta[name="verify-form-token"]').getAttribute('content');

                const response = await fetch('/recruitment/resend-code', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': resendCsrf,
                        'X-Form-Token': resendFormToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ 
                        email: email,
                        locale: currentLang
                    })
                });
                const data = await response.json();
                if (data.success) {
                    timer = 60;
                    startTimer();
                    alert("Kod został wysłany ponownie");
                } else {
                    alert(data.message || "Ошибка при отправке");
                }
            } catch (err) {
                alert("Ошибка соединения с сервером");
            }
        });

        function startTimer() {
            resendBtn.disabled = true;
            if (timerInterval) clearInterval(timerInterval);
            timerInterval = setInterval(() => {
                resendBtn.textContent = `Отправить заново через ${timer}с`;
                timer--;
                if (timer < 0) {
                    clearInterval(timerInterval);
                    resendBtn.disabled = false;
                    resendBtn.textContent = "Отправить код еще раз";
                }
            }, 1000);
        }
        
        startTimer();

        // --- Starfield Animation ---
        const canvas = document.getElementById('starfield');
        const ctx = canvas.getContext('2d');
        let width, height, stars = [];

        function resize() {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
            stars = [];
            for (let i = 0; i < 200; i++) {
                stars.push({
                    x: Math.random() * width,
                    y: Math.random() * height,
                    r: Math.random() * 1.5,
                    alpha: Math.random(),
                    change: (Math.random() * 0.02 + 0.005)
                });
            }
        }

        function animate() {
            ctx.fillStyle = '#050f14';
            ctx.fillRect(0, 0, width, height);
            stars.forEach(s => {
                s.alpha += s.change;
                if(s.alpha > 1 || s.alpha < 0) s.change *= -1;
                ctx.beginPath();
                ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(255, 255, 255, ${s.alpha <= 0 ? 0 : s.alpha})`;
                ctx.fill();
            });
            requestAnimationFrame(animate);
        }

        window.addEventListener('resize', resize);
        resize();
        animate();
    </script>
</body>
</html>
