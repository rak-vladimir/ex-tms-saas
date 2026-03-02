# Mini TMS — Multi‑Tenant Order Management Service

---

## Стек

* PHP 8.4
* Laravel 12
* PostgreSQL
* Docker + Docker Compose
* Nginx + PHP‑FPM

---

## Запуск проекта

```bash
docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan migrate
docker compose exec app php artisan seed:tenants-couriers
```

Команда `seed:tenants-couriers` создаёт только tenants и couriers для тестирования API. Выводит ключи и данные созданных записей.

---

## Настройка окружения

Скопируйте файл окружения:

```bash
cp .env.example .env
```

Отредактируйте переменные (при необходимости):

```env
APP_KEY=base64:GENERATED_KEY

DB_CONNECTION=pgsql
DB_HOST=database
DB_PORT=5432
DB_DATABASE=tms
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

> Redis не используется — для простоты и надёжности выбраны стандартные драйверы file и database.


Сгенерируйте ключ приложения:

```bash
docker compose exec app php artisan key:generate
```

---

## Список маршрутов

Пагинация включена.

### Couriers

- `GET /api/couriers` — список курьеров текущего tenant-а (фильтры опциональны `status`, `delivery_date`)
- `POST /api/couriers` — создание курьера
- `GET /api/couriers/{courier}` — просмотр информации о курьере
- `DELETE /api/couriers/{courier}` — удаление курьера

---

### Orders

- `GET /api/orders` — список заказов текущего tenant-а
- `POST /api/orders` — создание заказа
- `GET /api/orders/{order}` — просмотр информации о заказе
- `POST /api/orders/{order}/assign` — назначение курьера
- `POST /api/orders/{order}/status` — смена статуса заказа

---

## Основные сущности

* Tenants (компании)
* Orders (заказы доставки)
* Couriers (курьеры)
* Assignments (назначения курьеров)
* Order Status History (история статусов)

---

## Примеры запросов

### Создание заказа

```http request
POST http://localhost/api/orders
Content-Type: application/json
X-API-KEY: <tenant_api_key>

{
  "external_id": "ORD-1001",
  "customer_name": "Иван Петров",
  "phone": "+77010000001",
  "pickup_address": "г. Лисаков, ул. Центральная 1",
  "delivery_address": "г. Лисаков, ул. Южная 5",
  "delivery_date": "2026-03-03"
}
```

### Назначение курьера

```http request
POST http://localhost/api/orders/{order_id}/assign
Content-Type: application/json
X-API-KEY: <tenant_api_key>

{
  "courier_id": 1
}
```

### Смена статуса заказа

```http request
POST http://localhost/api/orders/{order_id}/status
Content-Type: application/json
X-API-KEY: <tenant_api_key>

{
  "status": "delivered"
}
```

---

## Архитектурные решения

- **Multi-tenant архитектура**
  <br>
  Каждый запрос идентифицируется по заголовку `X-API-KEY`. Middleware (`TenantMiddleware`) определяет текущего tenant-а и сохраняет его в `CurrentTenant`.
  Все модели фильтруются через глобальный `TenantScope`.
- **Идемпотентность заказов**
  <br>
  Реализована в `OrderService::create`:
    - если указан external_id → заказ ищется по `(tenant_id, external_id)` и обновляется;
    - если не указан → создаётся новый заказ.
      Это гарантирует идемпотентность по `external_id`.
- **История статусов**
  <br>
  Каждое изменение статуса фиксируется в таблице `status_histories`.
  Это позволяет отслеживать полный жизненный цикл заказа.
- **Supervisor**
  <br>
  Управляет процессами внутри контейнера:
    - `php-fpm` — основной процесс для обработки запросов;
    - `laravel-queue` — обработка фоновых задач;
    - `laravel-schedule` — планировщик.
- **Почему не используем FormRequest**
  <br>
  Валидация выполняется прямо в контроллере через `$request->validate()`.
  <br>
  Причины:
    - Правила проверки (например, обязательные поля заказа или курьера), которые легко описываются прямо в контроллере.
    - FormRequest добавил бы дополнительный уровень абстракции, но не дал бы значимых преимуществ, так как валидация у нас минимальная и не зависит от контекста.
    - Валидация не зависит от бизнес‑логики или сложных условий, поэтому нет необходимости выносить её в отдельные классы.
    - Такой подход облегчает тестовое задание: меньше файлов, проще навигация, быстрее чтение кода.
      В реальном проекте FormRequest можно использовать для более сложных сценариев, но здесь выбран прагматичный вариант.
- **Фоновая задача**
  <br>
  Если заказ находится в статусе `new` более 30 минут — он автоматически отменяется.
- **Enum** для статусов заказов
- **Транзакции** для критичных операций
- **Индексы** и **уникальные ограничения** в БД

---

### Почему не использовались Factory и стандартные Seeder

В данном проекте не было необходимости создавать отдельные фабрики и сидеры:

- **Factory** обычно применяются для генерации тестовых данных в больших объёмах. Здесь достаточно минимального набора сущностей (tenants и couriers), поэтому фабрики избыточны.
- **Seeder** в классическом виде также не нужен, так как для демонстрации предусмотрена отдельная artisan‑команда `seed:tenants-couriers`. Она создаёт ровно то, что требуется по
  ТЗ — два tenant-а и по два курьера для каждого.
- Такой подход делает код проще и прозрачнее: нет лишних файлов, а демо‑данные создаются строго контролируемым образом.

Таким образом, фабрики и стандартные сидеры были сознательно опущены, чтобы не усложнять тестовое задание и сосредоточиться на архитектуре и бизнес‑логике.\

---

Проект реализовывался полностью в рамках тестового задания и демонстрирует:

* работу с мульти‑тенант архитектурой
* проектирование API
* обработку доменной логики доставки
* работу с очередями и планировщиком задач
