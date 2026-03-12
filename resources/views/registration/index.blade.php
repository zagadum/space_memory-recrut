<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Space Memory - Регистрация</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0b1d26;
            --primary-accent: #41e1e8;
            --secondary-accent: #eb8b11;
            --error-color: #ff4b4b;
            --text-main: #ffffff;
            --glass-bg: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(65, 225, 232, 0.8);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }

        body {
            background: radial-gradient(circle at center, #0f2a36 0%, #050f14 100%);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
        }

        #starfield {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        /* Языки */
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

        .lang-btn:hover { color: var(--primary-accent); }
        .lang-btn.active {
            color: #050f14;
            background: var(--primary-accent);
            box-shadow: 0 0 15px var(--primary-accent);
        }

        /* Контейнер */
        .main-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 650px; /* Шире для формы регистрации */
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 40px;
            margin-bottom: 60px;
        }

        .logo {
            width: 120px;
            margin-bottom: 20px;
            filter: drop-shadow(0 0 15px rgba(65, 225, 232, 0.4));
            animation: float 4s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        .glass-card {
            width: 100%;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 30px;
            padding: 40px 30px 30px;
            position: relative;
            box-shadow: 0 25px 60px rgba(0,0,0,0.6);
        }

        h2.form-title {
            text-align: center;
            color: var(--primary-accent);
            font-family: 'Nunito', sans-serif;
            margin-bottom: 5px;
            font-size: 22px;
            text-shadow: 0 0 10px rgba(65, 225, 232, 0.3);
        }
        
        .promo-text {
            text-align: center;
            color: var(--secondary-accent);
            font-size: 14px;
            margin-bottom: 25px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .form-section {
            margin-bottom: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 15px;
        }

        .section-label {
            font-size: 12px;
            color: rgba(255,255,255,0.5);
            text-transform: uppercase;
            margin-bottom: 10px;
            letter-spacing: 1px;
            font-weight: bold;
        }

        .form-group { margin-bottom: 15px; position: relative; }

        .form-row {
            display: flex;
            gap: 15px;
        }
        
        .form-row .form-group {
            flex: 1;
        }

        .input-field {
            width: 100%;
            padding: 12px 18px;
            border-radius: 12px;
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            font-size: 15px;
            outline: none;
            transition: all 0.3s ease;
        }
        
        /* Стили для даты */
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }
        
        textarea.input-field {
            resize: vertical;
            min-height: 80px;
        }

        .input-field:focus {
            border-color: var(--primary-accent);
            box-shadow: 0 0 15px rgba(65, 225, 232, 0.2);
        }

        /* Чекбоксы */
        .checkbox-group {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 10px;
            font-size: 13px;
            color: rgba(255,255,255,0.8);
        }

        .checkbox-group input[type="checkbox"] {
            accent-color: var(--secondary-accent);
            width: 16px;
            height: 16px;
            margin-top: 2px;
            cursor: pointer;
        }

        .checkbox-group label {
            cursor: pointer;
            line-height: 1.4;
        }

        .submit-btn {
            width: 100%;
            padding: 15px;
            border-radius: 15px;
            background: linear-gradient(135deg, var(--primary-accent) 0%, #29a8ad 100%);
            border: none;
            color: #062330;
            font-size: 18px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 20px;
            box-shadow: 0 4px 15px rgba(65, 225, 232, 0.3);
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(65, 225, 232, 0.5);
        }

        .login-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--secondary-accent);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }
        
        .login-link:hover { text-decoration: underline; }

        /* Футер */
        .site-footer {
            width: 100%;
            padding: 20px;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(5px);
            margin-top: auto;
            position: relative;
            z-index: 10;
        }

        .footer-company {
            text-align: center;
            color: rgba(255, 255, 255, 0.3);
            font-size: 11px;
            margin-bottom: 8px;
        }

        .footer-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.4);
            text-decoration: none;
            font-size: 10px;
            text-transform: uppercase;
            transition: color 0.3s;
        }
        
        .footer-links a:hover { color: var(--primary-accent); }

        @media (max-width: 600px) {
            .form-row { flex-direction: column; gap: 0; }
            .logo { width: 100px; }
            .main-container { padding-top: 80px; }
            .glass-card { padding: 30px 20px; }
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

        <div class="glass-card">
            <h2 class="form-title" id="formTitle">Регистрация Space Memory</h2>
            <div class="promo-text" id="promoText">Скидка 5% на групповые занятия!</div>
            
            <form id="regForm">
                <!-- СЕКЦИЯ: АККАУНТ -->
                <div class="section-label" id="secAccount">Аккаунт</div>
                <div class="form-group">
                    <input type="email" id="email" class="input-field" placeholder="Email (для входа)">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <input type="password" id="password" class="input-field" placeholder="Пароль (Space2026)">
                    </div>
                    <div class="form-group">
                        <input type="password" id="passwordRep" class="input-field" placeholder="Повтори пароль">
                    </div>
                </div>

                <!-- СЕКЦИЯ: РОДИТЕЛЬ -->
                <div class="form-section">
                    <div class="section-label" id="secParent">Родитель</div>
                    <div class="form-row">
                        <div class="form-group">
                            <input type="text" id="pName" class="input-field" placeholder="Имя">
                        </div>
                        <div class="form-group">
                            <input type="text" id="pSurname" class="input-field" placeholder="Фамилия">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <input type="tel" id="phone" class="input-field" placeholder="Номер телефона">
                        </div>
                        <div class="form-group">
                            <input type="text" id="passport" class="input-field" placeholder="ID / Номер паспорта">
                        </div>
                    </div>
                </div>

                <!-- СЕКЦИЯ: РЕБЕНОК -->
                <div class="form-section">
                    <div class="section-label" id="secChild">Ребёнок (Ученик)</div>
                    <div class="form-row">
                        <div class="form-group">
                            <input type="text" id="cName" class="input-field" placeholder="Имя ребёнка">
                        </div>
                        <div class="form-group">
                            <input type="text" id="cSurname" class="input-field" placeholder="Фамилия ребёнка">
                        </div>
                    </div>
                    <div class="form-group">
                        <label style="font-size:12px; color:rgba(255,255,255,0.5); display:block; margin-bottom:5px; padding-left:5px;" id="dobLabel">Дата рождения</label>
                        <input type="date" id="cDob" class="input-field">
                    </div>
                </div>

                <!-- СЕКЦИЯ: АДРЕС -->
                <div class="form-section">
                    <div class="section-label" id="secAddr">Адрес</div>
                    <div class="form-row">
                        <div class="form-group">
                            <input type="text" id="country" class="input-field" placeholder="Страна">
                        </div>
                        <div class="form-group">
                            <input type="text" id="city" class="input-field" placeholder="Город">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group" style="flex: 2;">
                            <input type="text" id="address" class="input-field" placeholder="Улица и номер дома">
                        </div>
                        <div class="form-group">
                            <input type="text" id="zip" class="input-field" placeholder="Почтовый код">
                        </div>
                    </div>
                </div>

                <!-- СОГЛАСИЯ -->
                <div class="form-section">
                    <div class="checkbox-group">
                        <input type="checkbox" id="checkTerms" required>
                        <label for="checkTerms" id="lblTerms">Подтверждаю условия и правила</label>
                    </div>
                    <div class="checkbox-group">
                        <input type="checkbox" id="checkPriv" required>
                        <label for="checkPriv" id="lblPriv">Согласен с Политикой приватности</label>
                    </div>
                    <div class="checkbox-group">
                        <input type="checkbox" id="checkPhoto">
                        <label for="checkPhoto" id="lblPhoto">Разрешаю фото и видео с занятий</label>
                    </div>
                </div>

                <!-- КОММЕНТАРИЙ -->
                <div class="form-group">
                    <textarea id="comment" class="input-field" placeholder="Комментарий для учителя (интересы, на что обратить внимание)..."></textarea>
                </div>

                <button type="submit" class="submit-btn" id="regBtn">ЗАРЕГИСТРИРОВАТЬСЯ</button>
            </form>
            
            <a href="/admin/login" class="login-link" id="haveAccLink">Уже есть аккаунт? Войти</a>
        </div>
    </div>

    <footer class="site-footer">
        <div class="footer-company" id="footerCompany">
            SPACE MEMORY ASSOCIATION Sp. z o.o. | Adres: Al. Jerozolimskie 123A, 02-017, Warszawa, Polska.
        </div>
        <div class="footer-links" id="footerLinks">
            <a href="#" data-key="link1">Niezbędnik Space Memory</a>
            <a href="#" data-key="link2">Standardy Ochrony</a>
            <a href="#" data-key="link4">Polityka prywatności</a>
            <a href="#" data-key="link6">Regulamin</a>
        </div>
    </footer>

    <script>
        // Тексты и переводы для всех полей
        const translations = {
            ru: { 
                title: "Регистрация Space Memory",
                promo: "Скидка 5% на групповые занятия!",
                
                secAccount: "Аккаунт",
                email: "Email (для входа)",
                pass: "Пароль (Space2026)",
                passRep: "Повтори пароль",
                
                secParent: "Родитель",
                pName: "Имя (Родителя)",
                pSurname: "Фамилия (Родителя)",
                phone: "Номер телефона",
                passport: "Идентификационный номер / Паспорт",
                
                secChild: "Ребёнок (Ученик)",
                cName: "Имя (Ребёнка)",
                cSurname: "Фамилия (Ребёнка)",
                dobLabel: "Дата рождения",

                secAddr: "Адрес проживания",
                country: "Страна",
                city: "Город",
                address: "Улица и номер дома",
                zip: "Почтовый код",

                lblTerms: "Подтверждаю условия и правила",
                lblPriv: "Согласен с Политикой приватности",
                lblPhoto: "Разрешаю фото/видео с занятий",
                
                comment: "Комментарий для учителя (интересы, нюансы)...",
                btn: "ЗАРЕГИСТРИРОВАТЬСЯ",
                haveAcc: "Уже есть аккаунт? Войти",
                
                footerCompany: "SPACE MEMORY ASSOCIATION Sp. z o.o. | Адрес: Al. Jerozolimskie 123A, 02-017, Варшава, Польша.",
                link1: "Инструментарий", link2: "Защита детей", link4: "Приватность", link6: "Регламент"
            },
            en: { 
                title: "Space Memory Registration",
                promo: "5% Discount for Group Classes!",
                
                secAccount: "Account Details",
                email: "Email (for login)",
                pass: "Password (e.g., Space2026)",
                passRep: "Repeat Password",
                
                secParent: "Parent / Guardian",
                pName: "First Name (Parent)",
                pSurname: "Last Name (Parent)",
                phone: "Phone Number",
                passport: "ID Number / Passport",
                
                secChild: "Child (Student)",
                cName: "Child's First Name",
                cSurname: "Child's Last Name",
                dobLabel: "Date of Birth",

                secAddr: "Address",
                country: "Country",
                city: "City",
                address: "Street and House Number",
                zip: "Zip Code",

                lblTerms: "I accept the Terms & Conditions",
                lblPriv: "I agree to the Privacy Policy",
                lblPhoto: "I allow photo/video from classes",
                
                comment: "Comment for the teacher (interests, notes)...",
                btn: "REGISTER NOW",
                haveAcc: "Already have an account? Login",

                footerCompany: "SPACE MEMORY ASSOCIATION Sp. z o.o. | Address: Al. Jerozolimskie 123A, 02-017, Warsaw, Poland.",
                link1: "Toolkit", link2: "Child Protection", link4: "Privacy", link6: "Terms"
            },
            pl: { 
                title: "Rejestracja Space Memory",
                promo: "Zniżka 5% na zajęcia grupowe!",
                
                secAccount: "Konto",
                email: "Email (do logowania)",
                pass: "Hasło (np. Space2026)",
                passRep: "Powtórz hasło",
                
                secParent: "Rodzic / Opiekun",
                pName: "Imię (Rodzica)",
                pSurname: "Nazwisko (Rodzica)",
                phone: "Numer telefonu",
                passport: "PESEL / Numer dowodu",
                
                secChild: "Dziecko (Uczeń)",
                cName: "Imię (Dziecka)",
                cSurname: "Nazwisko (Dziecka)",
                dobLabel: "Data urodzenia",

                secAddr: "Adres zamieszkania",
                country: "Kraj",
                city: "Miasto",
                address: "Ulica i numer domu",
                zip: "Kod pocztowy",

                lblTerms: "Akceptuję regulamin",
                lblPriv: "Zgadzam się z Polityką Prywatności",
                lblPhoto: "Zezwalam na zdjęcia/wideo z zajęć",
                
                comment: "Komentarz dla nauczyciela (zainteresowania)...",
                btn: "ZAREJESTRUJ SIĘ",
                haveAcc: "Masz już konto? Zaloguj się",

                footerCompany: "SPACE MEMORY ASSOCIATION Sp. z o.o. | Adres: Al. Jerozolimskie 123A, 02-017, Warszawa, Polska.",
                link1: "Niezbędnik", link2: "Standardy Ochrony", link4: "Polityka prywatności", link6: "Regulamin"
            },
            ua: { 
                title: "Реєстрація Space Memory",
                promo: "Знижка 5% на групові заняття!",
                
                secAccount: "Акаунт",
                email: "Email (для входу)",
                pass: "Пароль (Space2026)",
                passRep: "Повтори пароль",
                
                secParent: "Батьки",
                pName: "Ім'я (Батьків)",
                pSurname: "Прізвище (Батьків)",
                phone: "Номер телефону",
                passport: "Ідентифікаційний номер / Паспорт",
                
                secChild: "Дитина (Учень)",
                cName: "Ім'я (Дитини)",
                cSurname: "Прізвище (Дитини)",
                dobLabel: "Дата народження",

                secAddr: "Адреса",
                country: "Країна",
                city: "Місто",
                address: "Вулиця та номер будинку",
                zip: "Поштовий код",

                lblTerms: "Підтверджую умови та правила",
                lblPriv: "Згоден з Політикою приватності",
                lblPhoto: "Дозволяю фото/відео із занять",
                
                comment: "Коментар для вчителя (інтереси, нюанси)...",
                btn: "ЗАРЕЄСТРУВАТИСЯ",
                haveAcc: "Вже є акаунт? Увійти",

                footerCompany: "SPACE MEMORY ASSOCIATION Sp. z o.o. | Адреса: Al. Jerozolimskie 123A, 02-017, Варшава, Польща.",
                link1: "Інструментарій", link2: "Захист дітей", link4: "Приватність", link6: "Регламент"
            }
        };

        // --- ЛОГИКА АНИМАЦИИ (ЗВЕЗДЫ И ПЛАНЕТЫ) ---
        const canvas = document.getElementById('starfield');
        const ctx = canvas.getContext('2d');
        let width, height, stars = [], planets = [];

        function resize() {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
            initObjects();
        }

        class Star {
            constructor() {
                this.x = Math.random() * width;
                this.y = Math.random() * height;
                this.r = Math.random() * 1.5;
                this.alpha = Math.random();
                this.alphaChange = (Math.random() * 0.01 + 0.002) * (Math.random() < 0.5 ? 1 : -1);
            }
            update() {
                this.alpha += this.alphaChange;
                if (this.alpha <= 0.1 || this.alpha >= 1) this.alphaChange *= -1;
            }
            draw() {
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.r, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(255, 255, 255, ${this.alpha})`;
                ctx.fill();
            }
        }

        class Planet {
            constructor(radius, distance, speed, color, hasRings = false) {
                this.radius = radius;
                this.distance = distance;
                this.speed = speed;
                this.angle = Math.random() * Math.PI * 2;
                this.color = color;
                this.hasRings = hasRings;
            }
            update() { this.angle += this.speed; }
            draw() {
                const cx = width / 2;
                const cy = height / 2;
                
                ctx.beginPath();
                ctx.arc(cx, cy, this.distance, 0, Math.PI * 2);
                ctx.strokeStyle = 'rgba(65, 225, 232, 0.08)';
                ctx.lineWidth = 1;
                ctx.stroke();

                const x = cx + Math.cos(this.angle) * this.distance;
                const y = cy + Math.sin(this.angle) * this.distance;

                if (this.hasRings) {
                    ctx.beginPath();
                    ctx.ellipse(x, y, this.radius * 2.2, this.radius * 0.8, this.angle, 0, Math.PI * 2);
                    ctx.strokeStyle = 'rgba(194, 158, 115, 0.4)';
                    ctx.lineWidth = 2;
                    ctx.stroke();
                }

                ctx.beginPath();
                ctx.arc(x, y, this.radius, 0, Math.PI * 2);
                ctx.fillStyle = this.color;
                ctx.shadowBlur = 10;
                ctx.shadowColor = this.color;
                ctx.fill();
                ctx.shadowBlur = 0;
            }
        }

        function initObjects() {
            stars = []; planets = [];
            for (let i = 0; i < 300; i++) stars.push(new Star());
            const baseDist = Math.min(width, height) * 0.15;
            const distStep = 45;
            planets.push(new Planet(6, baseDist, 0.008, '#2271B3')); // Earth
            planets.push(new Planet(4.5, baseDist + distStep, 0.006, '#E27B58')); // Mars
            planets.push(new Planet(14, baseDist + distStep * 2.5, 0.003, '#D39C7E')); // Jupiter
            planets.push(new Planet(12, baseDist + distStep * 4.5, 0.002, '#C29E73', true)); // Saturn
        }

        function animate() {
            const bgGrad = ctx.createRadialGradient(width/2, height/2, 0, width/2, height/2, width);
            bgGrad.addColorStop(0, '#0f2a36');
            bgGrad.addColorStop(1, '#050f14');
            ctx.fillStyle = bgGrad;
            ctx.fillRect(0, 0, width, height);
            
            // Солнце
            const cx = width / 2;
            const cy = height / 2;
            const sunGrad = ctx.createRadialGradient(cx, cy, 5, cx, cy, 50);
            sunGrad.addColorStop(0, '#FFF5E1');
            sunGrad.addColorStop(0.4, '#FFD200');
            sunGrad.addColorStop(1, 'rgba(255, 100, 0, 0)');
            ctx.fillStyle = sunGrad;
            ctx.beginPath();
            ctx.arc(cx, cy, 50, 0, Math.PI * 2);
            ctx.fill();

            stars.forEach(s => { s.update(); s.draw(); });
            planets.forEach(p => { p.update(); p.draw(); });
            requestAnimationFrame(animate);
        }

        window.addEventListener('resize', resize);
        resize();
        animate();

        // --- ЛОГИКА ПЕРЕКЛЮЧЕНИЯ ЯЗЫКОВ ---
        document.querySelectorAll('.lang-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.lang-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const lang = btn.dataset.lang;
                const t = translations[lang];

                // Обновление текстов (TextContent)
                document.getElementById('formTitle').textContent = t.title;
                document.getElementById('promoText').textContent = t.promo;
                document.getElementById('secAccount').textContent = t.secAccount;
                document.getElementById('secParent').textContent = t.secParent;
                document.getElementById('secChild').textContent = t.secChild;
                document.getElementById('secAddr').textContent = t.secAddr;
                document.getElementById('dobLabel').textContent = t.dobLabel;
                document.getElementById('lblTerms').textContent = t.lblTerms;
                document.getElementById('lblPriv').textContent = t.lblPriv;
                document.getElementById('lblPhoto').textContent = t.lblPhoto;
                document.getElementById('regBtn').textContent = t.btn;
                document.getElementById('haveAccLink').textContent = t.haveAcc;
                document.getElementById('footerCompany').textContent = t.footerCompany;

                // Обновление плейсхолдеров
                document.getElementById('email').placeholder = t.email;
                document.getElementById('password').placeholder = t.pass;
                document.getElementById('passwordRep').placeholder = t.passRep;
                document.getElementById('pName').placeholder = t.pName;
                document.getElementById('pSurname').placeholder = t.pSurname;
                document.getElementById('phone').placeholder = t.phone;
                document.getElementById('passport').placeholder = t.passport;
                document.getElementById('cName').placeholder = t.cName;
                document.getElementById('cSurname').placeholder = t.cSurname;
                document.getElementById('country').placeholder = t.country;
                document.getElementById('city').placeholder = t.city;
                document.getElementById('address').placeholder = t.address;
                document.getElementById('zip').placeholder = t.zip;
                document.getElementById('comment').placeholder = t.comment;

                // Ссылки в футере
                document.querySelectorAll('#footerLinks a').forEach(a => {
                    const key = a.dataset.key;
                    if(t[key]) a.textContent = t[key];
                });
            });
        });

        // Отправка формы (Laravel API)
        document.getElementById('regForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('regBtn');
            btn.disabled = true;
            btn.textContent = '...';
            
            const payload = {
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
                password_confirmation: document.getElementById('passwordRep').value,
                parent_name: document.getElementById('pName').value,
                parent_surname: document.getElementById('pSurname').value,
                parent_phone: document.getElementById('phone').value,
                parent_passport: document.getElementById('passport').value,
                name: document.getElementById('cName').value,
                surname: document.getElementById('cSurname').value,
                dob: document.getElementById('cDob').value,
                country: document.getElementById('country').value,
                city: document.getElementById('city').value,
                address: document.getElementById('address').value,
                zip: document.getElementById('zip').value,
                photo_consent: document.getElementById('checkPhoto').checked ? 1 : 0,
                terms_accepted: document.getElementById('checkTerms').checked ? 1 : 0,
                privacy_accepted: document.getElementById('checkPriv').checked ? 1 : 0,
                reg_comment: document.getElementById('comment').value,
            };

            try {
                const response = await fetch('/api/v1/register', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'Accept': 'application/json' 
                    },
                    body: JSON.stringify(payload)
                });
                const data = await response.json();
                if (data.success) {
                    localStorage.setItem('student_email', payload.email);
                    window.location.href = '/verify';
                } else {
                    alert(data.message || 'Ошибка регистрации');
                    btn.disabled = false;
                    btn.textContent = document.querySelector('.lang-btn.active').dataset.lang === 'en' ? 'REGISTER NOW' : 
                                      document.querySelector('.lang-btn.active').dataset.lang === 'pl' ? 'ZAREJESTRUJ SIĘ' :
                                      document.querySelector('.lang-btn.active').dataset.lang === 'ua' ? 'ЗАРЕЄСТРУВАТИСЯ' :
                                      'ЗАРЕГИСТРИРОВАТЬСЯ';
                }
            } catch (err) {
                alert('Ошибка соединения с сервером');
                btn.disabled = false;
                btn.textContent = document.querySelector('.lang-btn.active').dataset.lang === 'en' ? 'REGISTER NOW' : 
                                  document.querySelector('.lang-btn.active').dataset.lang === 'pl' ? 'ZAREJESTRUJ SIĘ' :
                                  document.querySelector('.lang-btn.active').dataset.lang === 'ua' ? 'ЗАРЕЄСТРУВАТИСЯ' :
                                  'ЗАРЕГИСТРИРОВАТЬСЯ';
            }
        });
    </script>
</body>
</html>
