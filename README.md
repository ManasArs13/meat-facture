# meat-facture

Тестовое задание для бэкенд-разработчика

REST API для управления заказами в приложении
«Мясофактура».

## Доступ к документации Swagger

Документация доступна по пути 'api/documentation'

## Возможности

-   Регистрация и авторизация пользователей (JWT токен)
    'api/login';
    'api/register
-   Просмотр ассортимента продуктов и категории с фильтрацией, просмотр отдельного экземпляра
    'api/categories';
    'api/products;
-   Просмотр и оформление заказов (доступно только авторизированным пользователем)
    'api/orders';
    Доступно ограничение кол-ва товаров в заказе в константе MAX_PRODUCTS_PER_ORDER модели Order
-   Доступны функциональные тесты (PHPUnit)

## Технологии

-   **Backend**: Laravel 12
-   **Тесты**: PHPUnit
-   **База данных**: PostgreSQL
-   **Авторизация**: JWT
-   **Документация**: Swagger

## Установка

1. Клонировать репозиторий:

```bash
git clone https://github.com/ManasArs13/meat-facture.git && cd shortlink
```

2. Установите зависимости:

```bash
composer install && npm install && npm run build
```

3. Настройте:

```bash
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

4. Запустить миграции:

Для удобства тестирования созданы тестовые данные, использую Factories и Seeders

```bash
php artisan migrate --seed
```

## Структура базы данных

-   users - зарегистрированные пользователи
-   orders - заказы
-   categories - категории товаров
-   products - ассортимент товаров

## Тестирование

```bash
php artisan test
```

-   orders (4 теста)
     ✓ guest cannot access orders  
     ✓ authenticated user can access orders  
     ✓ authenticated user can show single order  
     ✓ it creates order with products

-   categories (4 теста)
    ✓ it can list categories 0.31s
    ✓ it can filter categories 0.02s
    ✓ it can sort categories 0.01s
    ✓ it can show single category

-   products (4 теста)
    ✓ it can list products 0.03s
    ✓ it can filter products 0.01s
    ✓ it can sort products 0.02s
    ✓ it can show single products
