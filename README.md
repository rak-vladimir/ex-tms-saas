# Mini TMS — Multi‑Tenant Order Management Service

Тестовое задание: **Middle PHP Backend Developer (TMS SaaS)**

Мини‑сервис управления заказами доставки с поддержкой мульти‑тенант архитектуры.

---

## Стек

* PHP 8.4
* Laravel 12
* PostgreSQL
* Docker + Docker Compose
* Nginx + PHP‑FPM

---

## Запуск проекта

### 1. Клонирование

```bash
git clone <repo_url>
cd ex-multi-tenant-order-management
```

### 2. Настройка окружения

Скопируйте файл окружения:

```bash
cp .env.example .env
```

Отредактируйте переменные (при необходимости):

```env
APP_NAME=TMS
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=pgsql
DB_HOST=database
DB_PORT=5432
DB_DATABASE=tms
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

Сгенерируйте ключ приложения:

```bash
docker compose run --rm app php artisan key:generate
```

---

### 3. Сборка и запуск контейнеров

```bash
docker compose up -d --build
```

Проверка статуса:

```bash
docker compose ps
```

---

### 4. Установка зависимостей

```bash
docker compose exec app composer install
```

---

### 5. Миграции базы данных

```bash
docker compose exec app php artisan migrate
```

---

## Мульти‑тенант доступ

Каждый API‑запрос должен содержать заголовок:

```
X-API-Key: tenant_api_key
```

По ключу определяется компания (tenant). Доступ к данным других компаний запрещён.

Реализация:

* Middleware `IdentifyTenant`
* TenantContext (хранение текущего tenant)
* Глобальные скоупы моделей по `tenant_id`

---

## Основные сущности

* Tenants (компании)
* Orders (заказы доставки)
* Couriers (курьеры)
* Assignments (назначения курьеров)
* Order Status History (история статусов)

---

## API (планируется по ТЗ)

### Создание заказа

```
POST /orders
```

Особенности:

* Валидация
* Идемпотентность по `external_id`
* Начальный статус `new`
* Запись в историю статусов

---

### Список заказов

```
GET /orders
```

Фильтры:

* status
* delivery_date

Пагинация включена.

---

### Назначение курьера

```
POST /orders/{id}/assign
```

---

### Изменение статуса доставки

```
POST /orders/{id}/status
```

---

### Получение заказа

```
GET /orders/{id}
```

Возвращает:

* данные заказа
* назначенного курьера
* историю статусов

---

## Фоновая задача

Если заказ находится в статусе `new` более 30 минут — он автоматически отменяется.

Реализация:

* Scheduler
* Queue
* Job отмены устаревших заказов
* Запись события в историю

---

## Архитектурные решения

* Стандартная структура Laravel
* Service Layer для бизнес‑логики
* Form Requests для валидации
* Enum для статусов заказов
* Транзакции для критичных операций
* Индексы и уникальные ограничения в БД

---

## Тестирование

(Будет добавлено)

---

## Примечания

Проект реализуется в рамках тестового задания и демонстрирует:

* работу с мульти‑тенант архитектурой
* проектирование API
* обработку доменной логики доставки
* работу с очередями и планировщиком задач
