# GLS Recruiting & Student Cabinet (`space_memory-recrut`)

Backend-сервис и публичный портал для системы **Global Leaders Skills (GLS)**. 
Отвечает за регистрацию, кабинет ученика и воронку рекрутинга.

---

## 🚀 Стек технологий

- **Core:** Laravel 12 (PHP 8.4)
- **Database:** MySQL 8.0 (DB: `memory_recruiting`)
- **Frontend:** Laravel Blade + Vanilla SCSS (BEM). 
- **Auth:** JWT (tymon/jwt-auth) + Middleware `verify.jwt`.
- **Environment:** Mac, PHP 8.4 (Herd), MySQL 8.0.

---

## 🧠 Архитектурные правила (CRITICAL)

### 1. Структура Laravel 12
- **Bootstrap vs Config:** Вся логика роутинга (`->withRouting`), мидлваров (`->withMiddleware`) и исключений должна находиться **только** в `bootstrap/app.php`. 
- **Config:** Файлы в `config/*.php` должны содержать только массивы настроек. ЗАПРЕЩЕНО вызывать `Application::configure()` внутри конфигов.

### 2. Frontend ограничения
- **No Tailwind/Vue:** Для новых страниц кабинета и регистрации используем **только чистый Blade и SCSS**. Это обеспечивает мгновенную загрузку (Instant Load).
- **Стили:** Находятся в `resources/sass/student/`. Используем БЭМ-нотацию.
- **Никаких CDN**: Все зависимости (JS/CSS) должны быть локальными.

### 3. База данных
- **Уникальные индексы:** При создании уникальных индексов на несколько полей (например, в таблицах оплат), всегда задавайте короткое имя вручную, чтобы не превысить лимит MySQL (64 символа).
  ```php
  $table->unique(['field1', 'field2', ...], 'short_idx_name');
  ```

---

## 🗄️ Схема данных

### Основные таблицы
- **`recruting_student`**: Лиды и неактивные ученики. 
  - `status`: lead, new_student, active, expelled.
  - `enabled`: 0 = ждет активации, 1 = активен.
- **`gls_projects`**: Список проектов (1: Space Memory, 2: Indigo).
- **`gls_salary_calculations`**: Система расчетов зарплат учителей.
- **`gls_payment_transactions`**: Лог транзакций учеников.

---

## 🛠 Локальная установка

1. **Клонировать и установить:**
   ```bash
   composer install
   npm install
   cp .env.example .env
   ```
2. **Настройка БД:**
   - Создайте базу `memory_recruiting`.
   - Выполните миграции: `php artisan migrate`.
   - Запустите сидер проектов: `php artisan db:seed --class=GlsProjectsSeeder`.
3. **Запуск:**
   ```bash
   php artisan serve --port=8000
   npm run dev
   ```

---

## 🌐 API & Маршруты

Все API эндпоинты защищены или структурированы под префиксом `/api/v1/`.
- `POST /api/v1/register` — Регистрация нового ученика.
- `GET /api/v1/recruitment/new-students` — Список для менеджеров (требует JWT).

Полный список: `php artisan route:list | grep recruitment`

---

## 📦 Команда проекта

- **PM:** Артём
- **Архитектор:** Claude (AI)
- **Repo:** [zagadum/space_memory-recrut](https://github.com/zagadum/space_memory-recrut)
