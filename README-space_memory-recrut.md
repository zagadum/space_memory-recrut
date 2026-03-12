# space_memory-recrut

Backend-сервис для регистрации и профиля ученика в системе Space Memory. Предоставляет API для публичной части (регистрация, работа с лидами и новыми студентами).

## Что это

`space_memory-recrut` — это Laravel-приложение, отвечающее за публичную часть системы управления учениками:
- Регистрация новых учеников через форму
- Управление лидами (потенциальными учениками)
- Работа с новыми студентами до их активации
- История изменений по каждому студенту

## Место в архитектуре

Система состоит из **двух микросервисов**:

1. **`space_memory-recrut`** (этот репозиторий) — публичная часть для регистрации:
   - Ученик регистрируется через публичную форму
   - Создаётся запись с `enabled=0` (неактивирован)
   - Данные доступны для менеджеров через API

2. **`space_memory_adm`** — админ CRM отдела продаж:
   - Менеджеры видят новых студентов
   - Распределяют их по группам и учителям
   - Активируют аккаунт через "Старт группы" (`enabled` → 1)

**Связь между сервисами:**
- Ученик **взаимодействует только** с `space_memory-recrut`
- Активация аккаунта происходит **из `space_memory_adm`** через действие "Старт группы"
- Оба сервиса работают с одной БД, но разными таблицами/функциями

## Стек

- **Backend:** Laravel 11 (PHP 8.x)
- **База данных:** MySQL 8.0.29+
- **Аутентификация:** JWT (tymon/jwt-auth)
- **Email:** SMTP (Gmail)
- **Дополнительно:**
  - Laravel Events для логирования истории
  - Debug Bar для разработки

## Структура БД

### Таблица `recruting_student`

Основная таблица для хранения всех регистраций (лидов и новых студентов).

| Поле | Тип | Описание |
|------|-----|----------|
| `id` | BIGINT | ID записи |
| `franchisee_id` | BIGINT NULL | ID франшизы |
| `group_id` | BIGINT NULL | ID группы (назначается менеджером) |
| `teacher_id` | BIGINT NULL | ID учителя (назначается менеджером) |
| `email` | VARCHAR UNIQUE | Email ученика (обязательно при регистрации) |
| `password` | VARCHAR | Хеш пароля |
| `name` | VARCHAR NULL | Имя ученика |
| `surname` | VARCHAR NULL | Фамилия |
| `lastname` | VARCHAR NULL | Отчество |
| `patronymic` | VARCHAR NULL | Отчество (deprecated) |
| **Данные родителей** | | |
| `parent_name` | VARCHAR NULL | Имя родителя |
| `parent_surname` | VARCHAR NULL | Фамилия родителя |
| `parent_phone` | VARCHAR NULL | Телефон родителя |
| `parent_passport` | VARCHAR NULL | Паспорт родителя |
| **Адрес и доп. информация** | | |
| `dob` | DATE NULL | Дата рождения |
| `country` | VARCHAR NULL | Страна |
| `city` | VARCHAR NULL | Город |
| `address` | VARCHAR NULL | Адрес |
| `zip` | VARCHAR NULL | Индекс |
| `apartment` | VARCHAR NULL | Квартира |
| **Согласия и статус** | | |
| `photo_consent` | BOOLEAN | Согласие на фото (default: 0) |
| `terms_accepted` | BOOLEAN | Принятие условий (default: 0) |
| `privacy_accepted` | BOOLEAN | Принятие политики (default: 0) |
| `reg_comment` | TEXT NULL | Комментарий при регистрации |
| **Системные флаги** | | |
| `enabled` | BOOLEAN | **0** = неактивирован, **1** = активирован (default: 1) |
| `blocked` | BOOLEAN | Заблокирован (default: 0) |
| `deleted` | BOOLEAN | Удалён/архивирован (default: 0) |
| `api_token` | VARCHAR NULL | Токен API |
| `sess_id` | VARCHAR NULL | ID сессии |
| `last_login_at` | TIMESTAMP NULL | Последний вход |
| `created_at` | TIMESTAMP | Дата создания |
| `updated_at` | TIMESTAMP | Дата обновления |

### Таблица `recruting_student_history`

История изменений по каждому студенту.

| Поле | Тип | Описание |
|------|-----|----------|
| `id` | BIGINT | ID записи |
| `student_id` | BIGINT | FK на `recruting_student.id` |
| `event` | VARCHAR(100) | Тип события (created, updated, archived) |
| `detail` | VARCHAR(500) NULL | Детали события |
| `changed_by` | VARCHAR(100) NULL | Кто внёс изменение |
| `meta` | JSON NULL | Дополнительные данные |
| `created_at` | TIMESTAMP | Время события |

> ⚠️ логика частично реализована.
> События `StudentCreatedEvent`, `StudentUpdatedEvent`, `StudentArchivedEvent` — есть в `NewStudentsController`.
> `LeadsController` события не пишет. Endpoint `GET .../history` — не реализован в `routes/api.php`.

## API Endpoints

Все роуты доступны **без аутентификации**.

| Метод | Путь | Описание | Контроллер |
|-------|------|----------|------------|
| **Регистрация** | | | |
| `POST` | `/api/v1/register` | Публичная регистрация ученика (устанавливает `enabled=0`) | `NewStudentsController@register` |
| **Новые студенты** | | | |
| `GET` | `/api/v1/recruitment/new-students` | Список новых студентов (`enabled=0`, `deleted=0`) | `NewStudentsController@index` |
| `GET` | `/api/v1/recruitment/new-students/{id}` | Получить данные студента по ID | `NewStudentsController@show` |
| `POST` | `/api/v1/recruitment/new-students` | Создать студента вручную (устанавливает `enabled=1`) | `NewStudentsController@store` |
| `POST` | `/api/v1/recruitment/new-students/{id}/archive` | Архивировать студента (`deleted=1`) | `NewStudentsController@archive` |
| `GET` | `/api/v1/recruitment/new-students/{id}/history` | История изменений студента | `NewStudentsController@history` |
| **Лиды** | | | |
| `GET` | `/api/v1/recruitment/leads` | Список лидов (`enabled=0`, `deleted=0`) | `LeadsController@index` |
| `POST` | `/api/v1/recruitment/leads` | Создать лида вручную (устанавливает `enabled=0`) | `LeadsController@store` |
| `PATCH` | `/api/v1/recruitment/leads/{id}` | Обновить данные лида | `LeadsController@update` |

### Примеры запросов

**Регистрация нового ученика:**
```bash
POST /api/v1/register
Content-Type: application/json

{
  "email": "student@example.com",
  "password": "password123",
  "name": "Иван",
  "surname": "Иванов",
  "parent_name": "Петр",
  "parent_phone": "+375291234567",
  "dob": "2015-05-15",
  "city": "Минск",
  "photo_consent": 1,
  "terms_accepted": 1,
  "privacy_accepted": 1
}
```

**Создание лида вручную:**
```bash
POST /api/v1/recruitment/leads
Content-Type: application/json

{
  "email": "lead@example.com",
  "name": "Мария",
  "surname": "Петрова"
}
```

## Локальный запуск

### Требования

- PHP 8.0+
- MySQL 8.0.29+
- Composer
- Node.js 12.22+ / npm 8.19+
- Git
- SMTP доступ (для отправки писем)

### Установка

1. **Клонировать репозиторий:**
   ```bash
   git clone <repository-url>
   cd space_memory-recrut
   ```

2. **Установить зависимости:**
   ```bash
   composer install
   npm install --legacy-peer-deps
   ```

3. **Создать `.env` файл:**
   ```bash
   cp .env.example .env
   ```

4. **Настроить переменные окружения** (см. раздел ниже)

5. **Создать структуру директорий:**
   ```bash
   mkdir -p storage/logs
   mkdir -p storage/framework/{sessions,views,cache}
   mkdir -p storage/debugbar
   chmod -R 775 storage
   chmod -R 775 bootstrap/cache
   ```

6. **Запустить миграции:**
   ```bash
   php artisan migrate
   ```

7. **Сгенерировать ключ приложения:**
   ```bash
   php artisan key:generate
   ```

8. **Запустить локальный сервер:**
   ```bash
   php artisan serve --host=127.0.0.1 --port=8000
   ```

9. **API будет доступен по адресу:** `http://127.0.0.1:8000/api/v1/`

## Переменные окружения (.env)

### Основные настройки

```env
APP_NAME=memory
APP_ENV=local
APP_DEBUG=true
APP_URL=https://space.loc
```

### База данных

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=memory
DB_USERNAME=memory
DB_PASSWORD=memory
DB_CHARSET=utf8
DB_COLLATION=utf8_unicode_ci
```

### JWT аутентификация

```env
JWT_SECRET=<your-secret-key>
```

Сгенерировать можно командой:
```bash
php artisan jwt:secret
```

### Email (SMTP)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Дополнительные БД (опционально)

```env
DB_OLYMPIAD_HOST=localhost
DB_OLYMPIAD_PORT=3306
DB_OLYMPIAD_DATABASE=memory_olympiad
DB_OLYMPIAD_USERNAME=olympiad
DB_OLYMPIAD_PASSWORD=olympiad
```

### Debug

```env
DEBUGBAR_ENABLED=true
LOG_CHANNEL=daily
LOG_LEVEL=error
```

## Известные проблемы и TODO

### Критичные проблемы

1. **Несоответствие флага `enabled` между методами:**
   - `index()` в `NewStudentsController` и `LeadsController` ищет записи с `enabled=0` (неактивированные)
   - `store()` в `NewStudentsController` создаёт записи с `enabled=1` (активированные)
   - `register()` (публичная регистрация) создаёт с `enabled=0` (корректно)
   - **Проблема:** студенты, созданные через `store()`, не попадают в список новых студентов
   - **Решение:** унифицировать логику или добавить явный параметр `enabled` в запрос

2. **Отсутствует аутентификация на API роутах:**
   - Все эндпоинты доступны публично
   - **Риск:** любой может создавать/читать/архивировать студентов
   - **Решение:** добавить middleware `auth:api` или JWT-аутентификацию

3. **Endpoint `/expelled-students` не реализован:**
   - Фронт вызывает этот маршрут (из `space_memory_adm`)
   - В `routes/api.php` его нет
   - **Решение:** создать `ExpelledStudentsController` или добавить метод в существующий контроллер

### Проблемы архитектуры

4. **LeadsController и NewStudentsController работают с одной таблицей:**
   - Оба контроллера используют `recruting_student` с фильтром `enabled=0`
   - Нет разделения между лидом и новым студентом
   - **Решение:** добавить поле `status` (enum: 'lead', 'new_student', 'active', 'expelled')

5. **Отсутствует воронка статусов:**
   - Нет поля для отслеживания перехода: лид → новый студент → принят
   - Сложно анализировать конверсию
   - **Решение:** добавить миграцию для поля `status` + логику смены статуса

6. **Нет полей `phone` и `subject` для лидов:**
   - При создании лида нельзя указать телефон или предмет обучения
   - Эти данные часто собираются на первом этапе
   - **Решение:** добавить миграцию с полями `phone`, `subject`, `source` (откуда пришёл лид)

### Отсутствующие функции

7. **Миграция для таблицы `new_groups` отсутствует:**
   - Есть модель `NewGroups`, но миграции нет
   - Контроллер `NewGroupsController` есть, но роутов нет
   - **Решение:** создать миграцию или удалить неиспользуемый код

8. **Нет валидации уникальности email при обновлении:**
   - В `LeadsController@update` валидация `unique` учитывает текущий ID
   - В `NewStudentsController` нет метода `update`
   - **Решение:** добавить PATCH-методы для обновления

9. **Отсутствует событие для `LeadsController`:**
   - `NewStudentsController` триггерит события (`StudentCreatedEvent`, `StudentUpdatedEvent`, `StudentArchivedEvent`)
   - `LeadsController` не пишет в историю
   - **Решение:** добавить события для трекинга изменений лидов

### Технический долг

10. **События используются напрямую в контроллерах:**
    - `event(new StudentCreatedEvent(...))` смешивает бизнес-логику и контроллер
    - **Лучше:** вынести в Service Layer или использовать Eloquent Events

11. **Использование Query Builder вместо Eloquent:**
    - Вся работа с БД через `DB::table('recruting_student')`
    - Теряются преимущества ORM (relations, events, scopes)
    - **Решение:** рефакторинг на использование моделей

12. **Нет пагинации в `index()` методах:**
    - `->get()` возвращает все записи сразу
    - При большом количестве записей может быть проблема производительности
    - **Решение:** добавить `paginate(20)`

---

## Лицензия

Проприетарное ПО. Все права защищены.

## Контакты

Для вопросов и предложений обращаться к команде разработки Space Memory.
