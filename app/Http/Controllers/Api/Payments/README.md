# API GLS Payment

## Что добавлено

- Контроллер: `app/Http/Controllers/Api/Payments/PaymentController.php`
- Репозитории:
  - `app/Repositories/Payment/PaymentReadRepository.php`
  - `app/Repositories/Payment/PaymentDocumentRepository.php`
  - `app/Repositories/Payment/StudentProgramEventRepository.php`
- Модели:
  - `app/Models/PaymentDocument.php`
  - `app/Models/StudentProgramEvent.php`
- Миграции:
  - `database/migrations/2026_03_06_020000_create_payment_documents_table.php`
  - `database/migrations/2026_03_06_020100_create_student_program_events_table.php`

## Новые таблицы

### payment_documents
Хранит финансовые документы по программе (invoice/refund/correction/extra).

### student_program_events
Хранит события жизненного цикла программы (tariff/pause/discount/unlock/split/archive/resume).

## Как применить

```bash
php artisan migrate
```

## Проверка маршрутов

```bash
php artisan route:list --path=api/v1/payments
```


