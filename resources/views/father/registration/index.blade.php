<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="register-form-token" content="{{ $registerFormToken }}">
    <title>Space Memory - Rejestracja</title>
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

        .lang-btn:hover {
            color: var(--primary-accent);
        }

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
            max-width: 650px;
            /* Шире для формы регистрации */
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
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }
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
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.6);
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
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 15px;
        }

        .section-label {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
            text-transform: uppercase;
            margin-bottom: 10px;
            letter-spacing: 1px;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 15px;
            position: relative;
        }

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

        .input-field.is-invalid {
            border-color: var(--error-color) !important;
            box-shadow: 0 0 10px rgba(255, 75, 75, 0.2) !important;
        }

        .phone-wrapper.is-invalid {
            border-color: var(--error-color) !important;
            box-shadow: 0 0 10px rgba(255, 75, 75, 0.2) !important;
        }

        /* Checkbox error */
        input[type="checkbox"].is-invalid {
            outline: 2px solid var(--error-color) !important;
            outline-offset: 2px;
        }

        /* Чекбоксы */
        .checkbox-group {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 10px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.8);
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

        .login-link:hover {
            text-decoration: underline;
        }

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

        .footer-links a:hover {
            color: var(--primary-accent);
        }

        @media (max-width: 600px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }

            .logo {
                width: 100px;
            }

            .main-container {
                padding-top: 80px;
            }

            .glass-card {
                padding: 30px 20px;
            }
        }

        /* Звёздочка обязательного поля */
        .required-star {
            color: #ff4b4b;
            margin-left: 3px;
            font-size: 14px;
            line-height: 1;
        }

        /* Ссылка-триггер модалки */
        .doc-link {
            color: var(--primary-accent);
            text-decoration: underline;
            text-decoration-style: dotted;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
            font-size: inherit;
            font-family: inherit;
            transition: color 0.2s, text-shadow 0.2s;
        }

        .doc-link:hover {
            color: #fff;
            text-shadow: 0 0 8px rgba(65, 225, 232, 0.5);
        }

        /* Оверлей */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(6px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-overlay.active {
            display: flex;
        }

        /* Окно */
        .modal-window {
            background: #0c1f2c;
            border: 1px solid rgba(65, 225, 232, 0.3);
            border-radius: 20px;
            width: 100%;
            max-width: 580px;
            max-height: 80vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.8), 0 0 40px rgba(65, 225, 232, 0.06);
            animation: modalIn 0.25s cubic-bezier(0.22, 1, 0.36, 1);
        }

        @keyframes modalIn {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.97);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 24px 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
            flex-shrink: 0;
        }

        .modal-title {
            font-family: 'Nunito', sans-serif;
            font-size: 15px;
            font-weight: 800;
            color: var(--primary-accent);
            letter-spacing: 0.3px;
        }

        .modal-close {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.6);
            width: 30px;
            height: 30px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .modal-close:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
        }

        .modal-body {
            padding: 20px 24px 24px;
            overflow-y: auto;
            font-size: 13px;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.72);
        }

        .modal-body h3 {
            color: var(--primary-accent);
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 18px 0 8px;
        }

        .modal-body h3:first-child {
            margin-top: 0;
        }

        .modal-body p {
            margin-bottom: 10px;
        }

        .modal-body ul {
            padding-left: 18px;
            margin-bottom: 10px;
        }

        .modal-body ul li {
            margin-bottom: 4px;
        }

        .modal-body::-webkit-scrollbar {
            width: 5px;
        }

        .modal-body::-webkit-scrollbar-track {
            background: transparent;
        }

        .modal-body::-webkit-scrollbar-thumb {
            background: rgba(65, 225, 232, 0.25);
            border-radius: 10px;
        }

        /* ─── PHONE PREFIX WRAPPER ───────────────────── */
        .phone-wrapper {
            display: flex;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-bottom: 1px solid rgba(65, 225, 232, 0.15);
            overflow: hidden;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .phone-wrapper:focus-within {
            border-color: rgba(65, 225, 232, 0.3);
            border-bottom-color: var(--primary-accent);
            box-shadow: 0 4px 20px rgba(65, 225, 232, 0.08);
        }

        .dial-select {
            background: rgba(0, 0, 0, 0.7);
            border: none;
            border-right: 1px solid rgba(255, 255, 255, 0.07);
            color: #fff;
            font-size: 13px;
            font-family: 'Nunito', sans-serif;
            padding: 12px 8px 12px 12px;
            outline: none;
            cursor: pointer;
            flex-shrink: 0;
            appearance: none;
            -webkit-appearance: none;
            width: 100px;
            letter-spacing: 0.3px;
        }

        .dial-select option {
            background: #0c1e2c;
            color: #dff0f5;
        }

        .phone-input-field {
            flex: 1;
            padding: 12px 14px;
            background: rgba(0, 0, 0, 0.55);
            border: none;
            color: #fff;
            font-size: 14px;
            font-family: 'Nunito', sans-serif;
            outline: none;
            min-width: 0;
        }

        .phone-input-field::placeholder {
            color: rgba(255, 255, 255, 0.25);
            font-size: 13px;
        }

        /* ─── STYLED SELECT ──────────────────────────── */
        select.input-field {
            appearance: none;
            -webkit-appearance: none;
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='7' viewBox='0 0 11 7'%3E%3Cpath d='M1 1l4.5 4.5L10 1' stroke='rgba(65,225,232,0.45)' stroke-width='1.5' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 36px;
        }

        select.input-field option {
            background: #0c1e2c;
            color: #dff0f5;
        }

        select.input-field:disabled {
            opacity: 0.38;
            cursor: not-allowed;
        }

        select.input-field option[value=""] {
            color: rgba(223, 240, 245, 0.35);
        }
    </style>
</head>

<body>

    <canvas id="starfield"></canvas>

    <div class="lang-switcher">
        <button class="lang-btn" data-lang="en">EN</button>
        <button class="lang-btn active" data-lang="pl">PL</button>
        <button class="lang-btn" data-lang="ua">UA</button>
        <button class="lang-btn" data-lang="ru">RU</button>
    </div>

    <div class="main-container">
        <img src="http://indigomental-sklep.pl/wp-content/uploads/2026/02/logo_space-memory.png" alt="Logo"
            class="logo">

        <div class="glass-card">
            <h2 class="form-title" id="formTitle">Rejestracja Space Memory</h2>
            <div class="promo-text" id="promoText">-</div>

            <!-- Error Message Block -->
            <div id="error-block" style="display: none; background: rgba(255, 75, 75, 0.1); border: 1px solid #ff4b4b; color: #ff4b4b; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; text-align: center;">
            </div>

            <form id="regForm">
                <!-- СЕКЦИЯ: АККАУНТ -->
                <div class="section-label" id="secAccount">Konto</div>
                <div class="form-group">
                    <input type="email" id="email" class="input-field" placeholder="Email (do logowania)">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <input type="password" id="password" class="input-field" placeholder="Hasło (np. Space2026)">
                    </div>
                    <div class="form-group">
                        <input type="password" id="passwordRep" class="input-field" placeholder="Powtórz hasło">
                    </div>
                </div>

                <!-- СЕКЦИЯ: РОДИТЕЛЬ -->
                <div class="form-section">
                    <div class="section-label" id="secParent">Rodzic / Opiekun</div>
                    <div class="form-row">
                        <div class="form-group">
                            <input type="text" id="pName" class="input-field" placeholder="Imię (Rodzica)">
                        </div>
                        <div class="form-group">
                            <input type="text" id="pSurname" class="input-field" placeholder="Nazwisko (Rodzica)">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <div class="phone-wrapper">
                                <select id="dialCode" class="dial-select"></select>
                                <input type="tel" id="phone" class="phone-input-field" placeholder="Numer telefonu">
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="text" id="passport" class="input-field" placeholder="PESEL / Numer dowodu">
                        </div>
                    </div>
                </div>

                <!-- СЕКЦИЯ: РЕБЕНОК -->
                <div class="form-section">
                    <div class="section-label" id="secChild">Dziecko (Uczeń)</div>
                    <div class="form-row">
                        <div class="form-group">
                            <input type="text" id="cName" class="input-field" placeholder="Imię (Dziecka)">
                        </div>
                        <div class="form-group">
                            <input type="text" id="cSurname" class="input-field" placeholder="Nazwisko (Dziecka)">
                        </div>
                    </div>
                    <div class="form-group">
                        <label
                            style="font-size:12px; color:rgba(255,255,255,0.5); display:block; margin-bottom:5px; padding-left:5px;"
                            id="dobLabel">Data urodzenia</label>
                        <input type="date" id="cDob" class="input-field">
                    </div>
                </div>

                <!-- СЕКЦИЯ: АДРЕС -->
                <div class="form-section">
                    <div class="section-label" id="secAddr">Adres zamieszkania</div>
                    <div class="form-row">
                        <div class="form-group">
                            <select id="country" class="input-field">
                                <option value="">— Kraj —</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <select id="city" class="input-field" disabled>
                                <option value="">— najpierw wybierz kraj —</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group" style="flex: 2;">
                            <input type="text" id="address" class="input-field"
                                placeholder="Ulica, numer domu/mieszkania">
                        </div>
                        <div class="form-group">
                            <input type="text" id="zip" class="input-field" placeholder="Kod pocztowy">
                        </div>
                    </div>
                </div>

                <!-- СОГЛАСИЯ -->
                <div class="form-section">
                    <div class="checkbox-group">
                        <input type="checkbox" id="checkTermsPriv" required >
                        <label for="checkTermsPriv" id="lblTermsPriv">
                            <span id="txtTerms1">Akceptuję </span><button type="button" class="doc-link"
                                onclick="openModal('modalTerms')">Regulamin</button><span id="txtTerms2"> i
                            </span><button type="button" class="doc-link" onclick="openModal('modalPriv')">Politykę
                                Prywatności</button><span class="required-star">*</span>
                        </label>
                    </div>
                    <div class="checkbox-group">
                        <input type="checkbox" id="checkDataProcess" required >
                        <label for="checkDataProcess">
                            <span id="lblDataProcess">Zgadzam się na przetwarzanie danych w celu realizacji
                                usług</span><span class="required-star">*</span>
                        </label>
                    </div>
                    <div class="checkbox-group">
                        <input type="checkbox" id="checkUrgent" required >
                        <label for="checkUrgent">
                            <span id="lblUrgent">Żądam rozpoczęcia usług przed upływem 14 dni</span><span
                                class="required-star">*</span>
                        </label>
                    </div>
                    <div class="checkbox-group" style="margin-top: 8px;">
                        <input type="checkbox" id="checkImage" >
                        <label for="checkImage">
                            <span id="lblImage1">Zgadzam się na publikację wizerunku dziecka</span>
                        </label>
                    </div>
                    <div class="checkbox-group">
                        <input type="checkbox" id="checkRecord" >
                        <label for="checkRecord">
                            <span id="lblRecord">Zgadzam się na nagrywanie zajęć</span>
                        </label>
                    </div>
                    <div class="checkbox-group">
                        <input type="checkbox" id="checkMarketing" >
                        <label for="checkMarketing">
                            <span id="lblMarketing">Chcę otrzymywać informacje marketingowe e-mailem</span>
                        </label>
                    </div>
                </div>

                <!-- КОММЕНТАРИЙ -->
                <div class="form-group">
                    <textarea id="comment" class="input-field"
                        placeholder="Komentarz dla nauczyciela (zainteresowania)..."></textarea>
                </div>

                <button type="submit" class="submit-btn" id="regBtn">ZAREJESTRUJ SIĘ</button>
            </form>

            <a href="/login" class="login-link" id="haveAccLink">Masz już konto? Zaloguj się</a>
        </div>
    </div>

    <footer class="site-footer">
        <div class="footer-company" id="footerCompany">
            Global Leaders Skills Sp. z o.o. | NIP: 5252970924 | Biuro obsługi klienta: Al. Jerozolimskie 123A, 02-017
            Warszawa | Dane rejestrowe: ul. Kabacki Dukt 1, 02-798 Warszawa | KRS: 0001055763, REGON: 526267569
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
                address: "Улица, номер дома/квартиры",
                zip: "Почтовый код",

                lblTermsPriv: "Принимаю| и ",
                lblDataProcess: "Согласен на обработку данных для оказания услуг",
                lblUrgent: "Требую начать услуги до истечения 14 дней",
                lblImage: "Согласен на публикацию ",
                lblRecord: "Согласен на запись занятий",
                lblMarketing: "Хочу получать маркетинговые сообщения на email",

                comment: "Комментарий для учителя (интересы, нюансы)...",
                btn: "ЗАРЕГИСТРИРОВАТЬСЯ",
                haveAcc: "Уже есть аккаунт? Войти",

                footerCompany: "Global Leaders Skills Sp. z o.o. | NIP: 5252970924 | Biuro obsługi klienta: Al. Jerozolimskie 123A, 02-017 Warszawa | Dane rejestrowe: ul. Kabacki Dukt 1, 02-798 Warszawa | KRS: 0001055763, REGON: 526267569"
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
                address: "Street, house/apartment number",
                zip: "Zip Code",

                lblTermsPriv: "I accept the| and ",
                lblDataProcess: "I consent to processing of data for service delivery",
                lblUrgent: "I request services to begin before 14 days have passed",
                lblImage: "I consent to publication of ",
                lblRecord: "I consent to recording of classes",
                lblMarketing: "I want to receive marketing emails",

                comment: "Comment for the teacher (interests, notes)...",
                btn: "REGISTER NOW",
                haveAcc: "Already have an account? Login",

                footerCompany: "Global Leaders Skills Sp. z o.o. | NIP: 5252970924 | Customer service: Al. Jerozolimskie 123A, 02-017 Warsaw | Registered address: ul. Kabacki Dukt 1, 02-798 Warsaw | KRS: 0001055763, REGON: 526267569"
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
                address: "Ulica, numer domu/mieszkania",
                zip: "Kod pocztowy",

                lblTermsPriv: "Akceptuję| i ",
                lblDataProcess: "Zgadzam się na przetwarzanie danych w celu realizacji usług",
                lblUrgent: "Żądam rozpoczęcia usług przed upływem 14 dni",
                lblImage: "Zgadzam się na ",
                lblRecord: "Zgadzam się na nagrywanie zajęć",
                lblMarketing: "Chcę otrzymywać informacje marketingowe e-mailem",

                comment: "Komentarz dla nauczyciela (zainteresowania)...",
                btn: "ZAREJESTRUJ SIĘ",
                haveAcc: "Masz już konto? Zaloguj się",

                footerCompany: "Global Leaders Skills Sp. z o.o. | NIP: 5252970924 | Biuro obsługi klienta: Al. Jerozolimskie 123A, 02-017 Warszawa | Dane rejestrowe: ul. Kabacki Dukt 1, 02-798 Warszawa | KRS: 0001055763, REGON: 526267569"
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
                address: "Вулиця, номер будинку/квартири",
                zip: "Поштовий код",

                lblTermsPriv: "Приймаю| і ",
                lblDataProcess: "Згоден на обробку даних для надання послуг",
                lblUrgent: "Вимагаю розпочати послуги до закінчення 14 днів",
                lblImage: "Згоден на публікацію ",
                lblRecord: "Згоден на запис занять",
                lblMarketing: "Хочу отримувати маркетингові повідомлення на email",

                comment: "Коментар для вчителя (інтереси, нюанси)...",
                btn: "ЗАРЕЄСТРУВАТИСЯ",
                haveAcc: "Вже є акаунт? Увійти",

                footerCompany: "Global Leaders Skills Sp. z o.o. | NIP: 5252970924 | Biuro obsługi klienta: Al. Jerozolimskie 123A, 02-017 Warszawa | Dane rejestrowe: ul. Kabacki Dukt 1, 02-798 Warszawa | KRS: 0001055763, REGON: 526267569"
            }
        };

        // --- ЛОГИКА АНИМАЦИИ (ЗВЕЗДЫ И ПЛАНЕТЫ) ---
        const canvas = document.getElementById('starfield');
        const ctx = canvas.getContext('2d');
        let width, height, stars = [];

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

        function initObjects() {
            stars = [];
            for (let i = 0; i < 300; i++) stars.push(new Star());
        }

        function animate() {
            const bgGrad = ctx.createRadialGradient(width / 2, height / 2, 0, width / 2, height / 2, width);
            bgGrad.addColorStop(0, '#0f2a36');
            bgGrad.addColorStop(1, '#050f14');
            ctx.fillStyle = bgGrad;
            ctx.fillRect(0, 0, width, height);

            stars.forEach(s => { s.update(); s.draw(); });
            requestAnimationFrame(animate);
        }

        window.addEventListener('resize', resize);
        resize();
        animate();

        // ═══════════════════════════════════════════════════════════
        // СТРАНЫ, ГОРОДА И КОД ТЕЛЕФОНА
        // ═══════════════════════════════════════════════════════════
        const COUNTRIES = [
            { name: 'Polska', dial: '+48', flag: '🇵🇱', cities: ['Warszawa', 'Kraków', 'Wrocław', 'Poznań', 'Gdańsk', 'Szczecin', 'Łódź', 'Katowice', 'Lublin', 'Bydgoszcz', 'Białystok', 'Rzeszów', 'Toruń', 'Kielce', 'Radom', 'Gliwice', 'Olsztyn', 'Zabrze', 'Bielsko-Biała', 'Bytom'] },
            { name: 'Ukraina', dial: '+380', flag: '🇺🇦', cities: ['Kijów', 'Lwów', 'Odessa', 'Charków', 'Dniepr', 'Zaporoże', 'Winnica', 'Iwano-Frankiwsk', 'Tarnopol', 'Czerniowce', 'Równe', 'Żytomierz', 'Połtawa', 'Cherson', 'Mikołajów', 'Sumy', 'Chmielnicki'] },
            { name: 'Niemcy', dial: '+49', flag: '🇩🇪', cities: ['Berlin', 'Hamburg', 'Monachium', 'Kolonia', 'Frankfurt', 'Stuttgart', 'Düsseldorf', 'Dortmund', 'Leipzig', 'Norymberga', 'Bremen', 'Dresden', 'Hanower', 'Duisburg', 'Bochum'] },
            { name: 'Wielka Brytania', dial: '+44', flag: '🇬🇧', cities: ['Londyn', 'Manchester', 'Birmingham', 'Glasgow', 'Leeds', 'Liverpool', 'Bristol', 'Edinburgh', 'Sheffield', 'Belfast', 'Leicester', 'Coventry', 'Bradford'] },
            { name: 'Białoruś', dial: '+375', flag: '🇧🇾', cities: ['Mińsk', 'Grodno', 'Brześć', 'Witebsk', 'Mohylew', 'Bobrujsk', 'Baranowicze', 'Pińsk', 'Orsha', 'Mozyr'] },
            { name: 'Czechy', dial: '+420', flag: '🇨🇿', cities: ['Praga', 'Brno', 'Ostrawa', 'Pilzno', 'Liberec', 'Ołomuniec', 'Hradec Králové', 'České Budějovice', 'Pardubice'] },
            { name: 'Litwa', dial: '+370', flag: '🇱🇹', cities: ['Wilno', 'Kowno', 'Kłajpeda', 'Szawle', 'Poniewież', 'Alytus', 'Mariampol'] },
            { name: 'Łotwa', dial: '+371', flag: '🇱🇻', cities: ['Ryga', 'Daugavpils', 'Lipawa', 'Jełgawa', 'Jūrmała', 'Ventspils', 'Rēzekne'] },
            { name: 'Estonia', dial: '+372', flag: '🇪🇪', cities: ['Tallin', 'Tartu', 'Narwa', 'Pärnu', 'Kohtla-Järve', 'Viljandi'] },
            { name: 'Francja', dial: '+33', flag: '🇫🇷', cities: ['Paryż', 'Lyon', 'Marsylia', 'Tuluza', 'Bordeaux', 'Nantes', 'Strasburg', 'Lille', 'Nicea', 'Reims', 'Montpellier', 'Grenoble'] },
            { name: 'Włochy', dial: '+39', flag: '🇮🇹', cities: ['Rzym', 'Mediolan', 'Neapol', 'Turyn', 'Palermo', 'Genua', 'Bolonia', 'Florencja', 'Bari', 'Katania'] },
            { name: 'Hiszpania', dial: '+34', flag: '🇪🇸', cities: ['Madryt', 'Barcelona', 'Walencja', 'Sewilla', 'Zaragoza', 'Málaga', 'Murcia', 'Palma', 'Las Palmas', 'Bilbao'] },
            { name: 'Holandia', dial: '+31', flag: '🇳🇱', cities: ['Amsterdam', 'Rotterdam', 'Haga', 'Utrecht', 'Eindhoven', 'Tilburg', 'Groningen', 'Almere', 'Breda'] },
            { name: 'Belgia', dial: '+32', flag: '🇧🇪', cities: ['Bruksela', 'Antwerpia', 'Gandawa', 'Brugia', 'Liège', 'Charleroi', 'Namur'] },
            { name: 'Austria', dial: '+43', flag: '🇦🇹', cities: ['Wiedeń', 'Graz', 'Linz', 'Salzburg', 'Innsbruck', 'Klagenfurt', 'Villach'] },
            { name: 'Szwajcaria', dial: '+41', flag: '🇨🇭', cities: ['Zurych', 'Genewa', 'Bazylea', 'Berno', 'Lozanna', 'Lucerna', 'Sankt Gallen'] },
            { name: 'Mołdawia', dial: '+373', flag: '🇲🇩', cities: ['Kiszyniów', 'Tyraspol', 'Bielce', 'Bendery', 'Rybnița', 'Cahul'] },
            { name: 'Rumunia', dial: '+40', flag: '🇷🇴', cities: ['Bukareszt', 'Cluj-Napoca', 'Timișoara', 'Iași', 'Constanța', 'Craiova', 'Brașov', 'Galați'] },
            { name: 'Słowacja', dial: '+421', flag: '🇸🇰', cities: ['Bratysława', 'Koszyce', 'Preszów', 'Żylina', 'Bańska Bystrzyca', 'Trnava', 'Nitra'] },
            { name: 'Węgry', dial: '+36', flag: '🇭🇺', cities: ['Budapeszt', 'Debreczyn', 'Miszkolc', 'Pecz', 'Győr', 'Nyíregyháza', 'Kecskemét', 'Székesfehérvár'] },
            { name: 'USA', dial: '+1', flag: '🇺🇸', cities: ['Nowy Jork', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix', 'Philadelphia', 'San Antonio', 'San Diego', 'Dallas', 'San Jose', 'Austin', 'Miami', 'Atlanta', 'Seattle', 'Boston'] },
            { name: 'Kanada', dial: '+1', flag: '🇨🇦', cities: ['Toronto', 'Montreal', 'Vancouver', 'Calgary', 'Ottawa', 'Edmonton', 'Winnipeg', 'Quebec City', 'Hamilton'] },
            { name: 'Inne', dial: '+', flag: '🌍', cities: [] },
        ];

        // Populate dial-code select
        function initDialSelect() {
            const sel = document.getElementById('dialCode');
            sel.innerHTML = '';
            COUNTRIES.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.dial;
                opt.textContent = c.flag + ' ' + c.dial;
                if (c.name === 'Polska') opt.selected = true;
                sel.appendChild(opt);
            });
        }

        // Populate country select
        function initCountrySelect() {
            const sel = document.getElementById('country');
            sel.innerHTML = '<option value="">— Kraj —</option>';
            COUNTRIES.forEach(c => {
                if (c.name === 'Inne') return;
                const opt = document.createElement('option');
                opt.value = c.name;
                opt.dataset.dial = c.dial;
                opt.textContent = c.flag + '  ' + c.name;
                sel.appendChild(opt);
            });
        }

        // Populate city select based on selected country
        function populateCities(countryName) {
            const cityEl = document.getElementById('city');
            const country = COUNTRIES.find(c => c.name === countryName);
            cityEl.innerHTML = '';
            if (!country || !country.cities.length) {
                cityEl.innerHTML = '<option value="">— brak miast —</option>';
                cityEl.disabled = true;
                return;
            }
            cityEl.disabled = false;
            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = '— Miasto —';
            cityEl.appendChild(placeholder);
            country.cities.forEach(city => {
                const opt = document.createElement('option');
                opt.value = city;
                opt.textContent = city;
                cityEl.appendChild(opt);
            });
        }

        // When country changes → update cities + auto-sync dial code
        document.getElementById('country').addEventListener('change', function () {
            populateCities(this.value);
        });

        initDialSelect();
        initCountrySelect();

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
                document.getElementById('txtTerms1').textContent = t.lblTermsPriv.split('|')[0];
                document.getElementById('txtTerms2').textContent = t.lblTermsPriv.split('|')[1] || ' i ';
                document.getElementById('lblDataProcess').textContent = t.lblDataProcess;
                document.getElementById('lblUrgent').textContent = t.lblUrgent;
                document.getElementById('lblImage1').textContent = t.lblImage;
                document.getElementById('lblRecord').textContent = t.lblRecord;
                document.getElementById('lblMarketing').textContent = t.lblMarketing;
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
                    if (t[key]) a.textContent = t[key];
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
                parent_phone: document.getElementById('dialCode').value + document.getElementById('phone').value,
                parent_passport: document.getElementById('passport').value,
                name: document.getElementById('cName').value,
                surname: document.getElementById('cSurname').value,
                dob: document.getElementById('cDob').value,
                country: document.getElementById('country').value,
                city: document.getElementById('city').value,
                address: document.getElementById('address').value,
                zip: document.getElementById('zip').value,
                photo_consent: document.getElementById('checkImage').checked ? 1 : 0,
                terms_accepted: document.getElementById('checkTermsPriv').checked ? 1 : 0,
                privacy_accepted: document.getElementById('checkTermsPriv').checked ? 1 : 0,
                data_processing: document.getElementById('checkDataProcess').checked ? 1 : 0,
                urgent_start: document.getElementById('checkUrgent').checked ? 1 : 0,
                recording_consent: document.getElementById('checkRecord').checked ? 1 : 0,
                marketing_consent: document.getElementById('checkMarketing').checked ? 1 : 0,
                reg_comment: document.getElementById('comment').value,
                locale: document.querySelector('.lang-btn.active').dataset.lang,
            };

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const registerFormToken = document.querySelector('meta[name="register-form-token"]').getAttribute('content');

                const response = await fetch('/api/v1/register', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Register-Form-Token': registerFormToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(payload)
                });
                const data = await response.json();
                
                const errorBlock = document.getElementById('error-block');
                errorBlock.style.display = 'none';
                errorBlock.textContent = '';

                // Clear previous validation styles
                document.querySelectorAll('.input-field, .phone-wrapper').forEach(el => el.classList.remove('is-invalid'));

                if (data.success) {
                    localStorage.setItem('student_email', payload.email);
                    window.location.href = '/verify';
                } else {
                    let errorMsg = '';
                    if (data.errors) {
                        const fieldMapping = {
                            'email': 'email',
                            'password': 'password',
                            'password_confirmation': 'passwordRep',
                            'parent_name': 'pName',
                            'parent_surname': 'pSurname',
                            'parent_phone': 'phone',
                            'parent_passport': 'passport',
                            'name': 'cName',
                            'surname': 'cSurname',
                            'dob': 'cDob',
                            'country': 'country',
                            'city': 'city',
                            'address': 'address',
                            'zip': 'zip',
                            'terms_accepted': 'checkTermsPriv',
                            'data_processing': 'checkDataProcess'
                        };

                        for (const key in data.errors) {
                            errorMsg += data.errors[key].join('\n') + '\n';
                            
                            const fieldId = fieldMapping[key];
                            if (fieldId) {
                                const input = document.getElementById(fieldId);
                                if (input) {
                                    input.classList.add('is-invalid');
                                    // Special case for phone wrapper
                                    if (key === 'parent_phone') {
                                        const wrapper = input.closest('.phone-wrapper');
                                        if (wrapper) wrapper.classList.add('is-invalid');
                                    }
                                }
                            }
                        }
                    } else {
                        errorMsg = data.message || 'Ошибка регистрации';
                    }
                    
                    // Show error in block and scroll
                    errorBlock.textContent = errorMsg;
                    errorBlock.style.display = 'block';
                    errorBlock.scrollIntoView({ behavior: 'smooth', block: 'center' });

                    btn.disabled = false;
                    btn.textContent = document.querySelector('.lang-btn.active').dataset.lang === 'en' ? 'REGISTER NOW' :
                        document.querySelector('.lang-btn.active').dataset.lang === 'pl' ? 'ZAREJESTRUJ SIĘ' :
                            document.querySelector('.lang-btn.active').dataset.lang === 'ua' ? 'ЗАРЕЄСТРУВАТИСЯ' :
                                'ЗАРЕГИСТРИРОВАТЬСЯ';
                }
            } catch (err) {
                console.error(err);
                const errorBlock = document.getElementById('error-block');
                errorBlock.textContent = 'Ошибка соединения с сервером';
                errorBlock.style.display = 'block';
                
                btn.disabled = false;

                btn.textContent = document.querySelector('.lang-btn.active').dataset.lang === 'en' ? 'REGISTER NOW' :
                    document.querySelector('.lang-btn.active').dataset.lang === 'pl' ? 'ZAREJESTRUJ SIĘ' :
                        document.querySelector('.lang-btn.active').dataset.lang === 'ua' ? 'ЗАРЕЄСТРУВАТИСЯ' :
                            'ЗАРЕГИСТРИРОВАТЬСЯ';
            }
        });
    </script>
    <!-- ══ MODAL: REGULAMIN ══════════════════════════════════════ -->
    <div class="modal-overlay" id="modalTerms">
        <div class="modal-window">
            <div class="modal-header">
                <span class="modal-title">Regulamin Portalu Rodzica</span>
                <button class="modal-close" onclick="closeModal('modalTerms')">✕</button>
            </div>
            <div class="modal-body">
                <p style="color:rgba(255,255,255,0.35); font-size:11px; margin-bottom:16px;">Wersja obowiązująca od dnia
                    04.03.2026 r. · recruitment-edugls.com</p>

                <h3>§1. Postanowienia ogólne</h3>
                <p>1.1. Niniejszy Regulamin określa ogólne warunki, zasady oraz sposób świadczenia usług drogą
                    elektroniczną za pośrednictwem platformy rekrutacyjno-edukacyjnej dostępnej pod adresem
                    <strong>recruitment-edugls.com</strong> (zwanej dalej Portalem Rodzica).
                </p>
                <p
                    style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08); border-radius:10px; padding:12px; font-size:12px;">
                    <strong>Dane Usługodawcy:</strong><br>
                    GLOBAL LEADERS SKILLS sp. z o.o.<br>
                    ul. Kabacki Dukt 1, lok. U1 i U2, 02-798 Warszawa<br>
                    KRS: 0001055763 | NIP: 5252970924<br>
                    E-mail: <a href="/cdn-cgi/l/email-protection" class="__cf_email__"
                        data-cfemail="680903090c0d0501091b18090b0d050d05071a11280f05090104460b0705">[email&#160;protected]</a><br>
                    Telefon: +48 730 536 091
                </p>

                <h3>§2. Definicje</h3>
                <ul>
                    <li><strong>Portal Rodzica (Serwis)</strong> – platforma internetowa pod adresem
                        recruitment-edugls.com, służąca do zarządzania edukacją dziecka, rekrutacją oraz komunikacją.
                    </li>
                    <li><strong>Rodzic / Opiekun prawny (Użytkownik)</strong> – osoba fizyczna zakładająca Konto w
                        Serwisie, działająca w imieniu własnym oraz reprezentująca małoletnie Dziecko.</li>
                    <li><strong>Konto</strong> – indywidualny panel Użytkownika w Serwisie, zabezpieczony loginem i
                        hasłem.</li>
                    <li><strong>Przedsiębiorca na prawach konsumenta</strong> – osoba fizyczna zawierająca umowę
                        bezpośrednio związaną z jej działalnością gospodarczą, gdy z treści tej umowy wynika, że nie
                        posiada ona dla tej osoby charakteru zawodowego.</li>
                </ul>

                <h3>§4. Rejestracja, Dane i Zgody Użytkownika</h3>
                <p><strong>4.1. Zakres zbieranych danych:</strong> Aby w pełni korzystać z Portalu Rodzica, konieczne
                    jest założenie Konta. Podczas rejestracji wymagane są: imię i nazwisko Rodzica/Opiekuna, dane
                    logowania (e-mail, hasło), adres zamieszkania, dane kontaktowe, a także imię, nazwisko i data
                    urodzenia Dziecka.</p>
                <p><strong>4.2. Oświadczenia i zgody składane podczas rejestracji:</strong></p>
                <ul>
                    <li><span style="color:#ff4b4b;">*</span> <strong>Akceptacja Regulaminu i Polityki Prywatności
                            (obowiązkowe)</strong> – warunek konieczny do utworzenia Konta i zawarcia umowy.</li>
                    <li><span style="color:#ff4b4b;">*</span> <strong>Zgoda na przetwarzanie danych
                            (obowiązkowe)</strong> – w celu prawidłowej realizacji usług edukacyjnych.</li>
                    <li><span style="color:#ff4b4b;">*</span> <strong>Żądanie rozpoczęcia świadczenia usług przed
                            upływem 14 dni (obowiązkowe)</strong> – skutki opisano w §6.</li>
                    <li><span style="color:#41e1e8;">✓</span> <strong>Wizerunek i nagrywanie (dobrowolne)</strong> –
                        zgoda na publikację wizerunku dziecka oraz nagrywanie zajęć.</li>
                    <li><span style="color:#41e1e8;">✓</span> <strong>Informacje marketingowe (dobrowolne)</strong> –
                        zgoda na przesyłanie ofert i promocji e-mailem.</li>
                </ul>

                <h3>§6. Odstąpienie od umowy</h3>
                <p><strong>6.1.</strong> Konsumentowi przysługuje prawo odstąpienia od umowy w terminie <strong>14
                        dni</strong> bez podania przyczyny.</p>
                <p
                    style="background:rgba(245,158,11,0.08); border:1px solid rgba(245,158,11,0.2); border-radius:10px; padding:12px;">
                    <strong>6.2. Skutki żądania rozpoczęcia usług przed upływem 14 dni:</strong><br>
                    Z uwagi na złożone podczas rejestracji oświadczenie, Usługodawca natychmiastowo przystępuje do
                    świadczenia usług. W przypadku odstąpienia od umowy <strong>Użytkownik ma obowiązek zapłaty za
                        świadczenia spełnione do chwili odstąpienia</strong>, proporcjonalnie do zakresu wykonanego
                    świadczenia.
                </p>
                <p><strong>6.3.</strong> Aby skorzystać z prawa odstąpienia, należy przesłać oświadczenie na: <strong><a
                            href="/cdn-cgi/l/email-protection" class="__cf_email__"
                            data-cfemail="c0a1aba1a4a5ada9a1b3b0a1a3a5ada5adafb2b980a7ada1a9aceea3afad">[email&#160;protected]</a></strong>.
                </p>

                <h3>§7. Procedura Reklamacyjna</h3>
                <ul>
                    <li><strong>7.1.</strong> Usługodawca ponosi odpowiedzialność za brak zgodności usług z umową wobec
                        Konsumentów i Przedsiębiorców na prawach konsumenta.</li>
                    <li><strong>7.2.</strong> Reklamacje należy zgłaszać na: <strong><a
                                href="/cdn-cgi/l/email-protection" class="__cf_email__"
                                data-cfemail="ec8d878d888981858d9f9c8d8f89818981839e95ac8b818d8580c28f8381">[email&#160;protected]</a></strong>,
                        podając dane Konta, opis problemu i żądanie.</li>
                    <li><strong>7.3.</strong> Reklamacje rozpatrywane są w terminie <strong>14 dni</strong> od
                        otrzymania.</li>
                </ul>

                <h3>§8. Postanowienia końcowe</h3>
                <p>8.1. W sprawach nieuregulowanych mają zastosowanie przepisy prawa polskiego.</p>
                <p>8.2. Zasady przetwarzania danych szczegółowo opisuje Polityka Prywatności Serwisu.</p>
            </div>
        </div>
    </div>

    <!-- ══ MODAL: POLITYKA PRYWATNOŚCI ══════════════════════════ -->
    <div class="modal-overlay" id="modalPriv">
        <div class="modal-window">
            <div class="modal-header">
                <span class="modal-title">Polityka Prywatności</span>
                <button class="modal-close" onclick="closeModal('modalPriv')">✕</button>
            </div>
            <div class="modal-body">
                <p style="color:rgba(255,255,255,0.35); font-size:11px; margin-bottom:16px;">Wersja obowiązująca od dnia
                    04.03.2026 r. · recruitment-edugls.com</p>

                <p style="font-style:italic; margin-bottom:16px;">Niniejsza Polityka Prywatności określa zasady
                    przetwarzania i ochrony danych osobowych przekazanych przez Użytkowników w związku z rejestracją i
                    korzystaniem z Portalu Rodzica w domenie <strong>recruitment-edugls.com</strong>.</p>

                <h3>I. Administrator Danych Osobowych</h3>
                <p>Administratorem danych osobowych Użytkowników (Rodziców/Opiekunów oraz Dzieci) jest:</p>
                <p
                    style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08); border-radius:10px; padding:12px; font-size:12px;">
                    <strong>GLOBAL LEADERS SKILLS Sp. z o.o.</strong><br>
                    Siedziba: ul. Kabacki Dukt 1 lok U1 i U2, 02-798 Warszawa<br>
                    NIP: 5252970924 | KRS: 0001055763<br>
                    E-mail: <a href="/cdn-cgi/l/email-protection" class="__cf_email__"
                        data-cfemail="8dece6ece9e8e0e4ecfefdeceee8e0e8e0e2fff4cdeae0ece4e1a3eee2e0">[email&#160;protected]</a><br>
                    Telefon: +48 730 536 091
                </p>

                <h3>II. Zakres i Cele Przetwarzania Danych</h3>
                <p>Administrator za pośrednictwem formularza rejestracyjnego zbiera i przetwarza następujące dane
                    osobowe:</p>
                <ul>
                    <li><strong>Dane Rodzica/Opiekuna:</strong> imię i nazwisko, adres e-mail, numer telefonu, adres
                        zamieszkania, hasło do konta.</li>
                    <li><strong>Dane Dziecka (Ucznia):</strong> imię i nazwisko, data urodzenia.</li>
                </ul>
                <p><strong>Cele i podstawy prawne przetwarzania:</strong></p>
                <ul>
                    <li><strong>1. Realizacja usług edukacyjnych i prowadzenie Konta</strong> – niezbędne do wykonania
                        umowy i organizacji zajęć (art. 6 ust. 1 lit. b RODO).</li>
                    <li><strong>2. Wypełnienie obowiązków prawnych</strong> – w zakresie wymaganym przez prawo (art. 6
                        ust. 1 lit. c RODO).</li>
                    <li><strong>3. Marketing bezpośredni</strong> – przesyłanie informacji o kursach i ofertach
                        e-mailem, wyłącznie na podstawie dobrowolnej zgody (art. 6 ust. 1 lit. a RODO).</li>
                    <li><strong>4. Obowiązki prawne i księgowe</strong> – rozliczenia finansowe i dokumentacja
                        rachunkowa (art. 6 ust. 1 lit. c RODO).</li>
                </ul>

                <h3>III. Prawa Użytkownika</h3>
                <p>Zgodnie z przepisami RODO, każdemu Użytkownikowi przysługuje prawo do:</p>
                <ul>
                    <li><strong>Dostępu</strong> do treści swoich danych oraz ich kopii.</li>
                    <li><strong>Sprostowania</strong> (poprawiania) błędnych danych.</li>
                    <li><strong>Usunięcia</strong> danych (prawo do bycia zapomnianym).</li>
                    <li><strong>Cofnięcia zgody</strong> (np. na marketing lub wizerunek) w dowolnym momencie.</li>
                    <li><strong>Wniesienia sprzeciwu</strong> wobec przetwarzania danych.</li>
                    <li><strong>Przenoszenia</strong> danych do innego administratora.</li>
                    <li><strong>Wniesienia skargi</strong> do Prezesa Urzędu Ochrony Danych Osobowych (PUODO).</li>
                </ul>

                <h3>IV. Bezpieczeństwo Danych</h3>
                <p>Administrator stosuje odpowiednie środki techniczne i organizacyjne zapewniające ochronę
                    przetwarzanych danych osobowych, ze szczególnym uwzględnieniem ochrony danych małoletnich.</p>
                <p
                    style="background:rgba(65,225,232,0.06); border:1px solid rgba(65,225,232,0.15); border-radius:10px; padding:12px; font-size:12px;">
                    🔒 Komunikacja między komputerem Użytkownika a serwerem jest szyfrowana przy użyciu protokołu
                    <strong>SSL</strong>. Hasła Użytkowników są przechowywane w formie zaszyfrowanej (hashowanej).
                </p>

                <h3>V. Pliki Cookies</h3>
                <ul>
                    <li>Portal korzysta z plików cookies przechowywanych w urządzeniu końcowym Użytkownika.</li>
                    <li>Cookies wykorzystywane są do: utrzymania sesji logowania, dostosowania zawartości strony oraz
                        tworzenia anonimowych statystyk (np. Google Analytics).</li>
                    <li>Użytkownik może w każdej chwili zablokować cookies w ustawieniach przeglądarki.</li>
                </ul>

                <h3>VI. Profilowanie</h3>
                <p><strong>Strona internetowa nie korzysta z profilowania danych osobowych.</strong> Zebrane dane nie są
                    używane do zautomatyzowanego podejmowania decyzji ani automatycznej oceny zachowań Użytkownika
                    wywołujących skutki prawne.</p>

                <h3>VII. Kontakt</h3>
                <p>W przypadku pytań dotyczących przetwarzania danych lub chęci wycofania zgód:</p>
                <p
                    style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08); border-radius:10px; padding:12px; font-size:12px;">
                    E-mail: <strong><a href="/cdn-cgi/l/email-protection" class="__cf_email__"
                            data-cfemail="7d1c161c191810141c0e0d1c1e18101810120f043d1a101c1411531e1210">[email&#160;protected]</a></strong><br>
                    Adres: GLOBAL LEADERS SKILLS Sp. z o.o., ul. Kabacki Dukt 1 lok U1 i U2, 02-798 Warszawa
                </p>
            </div>
        </div>
    </div>

    <!-- ══ MODAL: ZGODA NA ZDJĘCIA ══════════════════════════════ -->
    <div class="modal-overlay" id="modalPhoto">
        <div class="modal-window">
            <div class="modal-header">
                <span class="modal-title">Zgoda na wizerunek</span>
                <button class="modal-close" onclick="closeModal('modalPhoto')">✕</button>
            </div>
            <div class="modal-body">
                <h3>Zakres zgody</h3>
                <p>Wyrażam zgodę na nieodpłatne utrwalanie i wykorzystanie wizerunku mojego dziecka w postaci zdjęć i
                    materiałów wideo wykonanych podczas zajęć organizowanych przez SPACE MEMORY ASSOCIATION Sp. z o.o.
                </p>

                <h3>Cel wykorzystania</h3>
                <ul>
                    <li>Dokumentacja przebiegu zajęć i postępów uczniów.</li>
                    <li>Materiały promocyjne publikowane na stronie internetowej i w mediach społecznościowych szkoły.
                    </li>
                    <li>Wewnętrzne materiały szkoleniowe dla kadry pedagogicznej.</li>
                </ul>

                <h3>Czas trwania zgody</h3>
                <p>Zgoda jest udzielana na czas nieokreślony i może zostać odwołana w dowolnym momencie poprzez pisemne
                    poinformowanie administratora. Odwołanie zgody nie wpływa na zgodność z prawem przetwarzania
                    dokonanego przed jej odwołaniem.</p>

                <h3>Uwaga</h3>
                <p>Zaznaczenie tej opcji jest dobrowolne i nie jest warunkiem uczestnictwa w zajęciach.</p>
            </div>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
            document.body.style.overflow = '';
        }
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function (e) {
                if (e.target === this) closeModal(this.id);
            });
        });
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay.active').forEach(m => closeModal(m.id));
            }
        });
    </script>

</body>

</html>