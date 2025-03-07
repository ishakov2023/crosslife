Для создания контейнеров через docker введите docker-compose up --build -d.
Далее введите docker exec -it  my-project-laravel.test-1 bash в появившемся терминале введите php artisan migrate это создаст нужные таблицы.
Чтобы заполнить таблицы через seeder введите php artisan db:seed теперь у нас есть заполненные таблицы Customer и Products.

API Документация
1. Получение списка товаров
Пример запроса curl -X GET http://localhost:80/api/catalog
Эндпоинт: GET /catalog
Описание
Получение списка доступных товаров с их деталями.
Метод: GET
Параметры: Нет параметров.
Ответ
Успешный ответ (200 OK):
[
    {
        "id": 1,
        "name": "Product 1",
        "description": "Description of Product 1",
        "price": "100.00",
        "stock": 50,
        "created_at": "2023-10-01T12:00:00.000000Z",
        "updated_at": "2023-10-01T12:00:00.000000Z"
    },
    {
        "id": 2,
        "name": "Product 2",
        "description": "Description of Product 2",
        "price": "200.00",
        "stock": 30,
        "created_at": "2023-10-01T12:00:00.000000Z",
        "updated_at": "2023-10-01T12:00:00.000000Z"
    }
]
Структура данных:
id (integer): Уникальный идентификатор товара.
name (string): Название товара.
description (string): Описание товара.
price (decimal): Цена товара.
stock (integer): Количество товара на складе.
created_at (datetime): Дата создания записи.
updated_at (datetime): Дата последнего обновления записи.

2. Создание заказа
Пример запроса: 
curl -X POST http://localhost:80/api/create-order \
  -H "Content-Type: application/json" \
  -d '{
    "customer_id": 1,
    "products": [
      {"id": 1, "quantity": 2},
      {"id": 2, "quantity": 1}
    ]
  }'
Эндпоинт: POST /create-order
Описание: Создание нового заказа с резервированием выбранных товаров.
Метод:POST
Параметры
Тело запроса (JSON):
{
    "customer_id": 1,
    "products": [
        {
            "id": 1,
            "quantity": 2
        },
        {
            "id": 2,
            "quantity": 1
        }
    ]
}
Обязательные поля:
customer_id (integer): Идентификатор покупателя.
products (array): Массив товаров с указанием их количества.
id (integer): Идентификатор товара.
quantity (integer): Количество товара.

Успешный ответ (201 Created):
{
    "id": 1,
    "order_number": "order_64e9f8a7b1cde",
    "status": "pending",
    "order_date": "2023-10-01T13:00:00.000000Z",
    "customer_id": 1,
    "total_price": "400.00",
    "created_at": "2023-10-01T13:00:00.000000Z",
    "updated_at": "2023-10-01T13:00:00.000000Z"
}

Структура данных:
id (integer): Уникальный идентификатор заказа.
order_number (string): Уникальный номер заказа.
status (string): Статус заказа (pending — ожидает одобрения).
order_date (datetime): Дата создания заказа.
customer_id (integer): Идентификатор покупателя.
total_price (decimal): Общая стоимость заказа.
created_at (datetime): Дата создания записи.
updated_at (datetime): Дата последнего обновления записи.

400 Bad Request: 
{
    "error": "Not enough stock for product: Product 1"
}

3. Одобрение заказа
Пример запроса:
curl -X POST http://localhost:80/api/approve-order \
  -H "Content-Type: application/json" \
  -d '{"order_id": 1, "customer_id": 1}'
Эндпоинт: POST /approve-order
Описание: Одобрение заказа с списанием средств с баланса покупателя.
Метод: POST
Тело запроса (JSON):
{
    "order_id": 1,
    "customer_id": 1
}
Обязательные поля:
order_id (integer): Идентификатор заказа.
customer_id (integer): Идентификатор покупателя.

Успешный ответ (200 OK):
{
    "id": 1,
    "order_number": "order_64e9f8a7b1cde",
    "status": "approved",
    "order_date": "2023-10-01T13:00:00.000000Z",
    "customer_id": 1,
    "total_price": "400.00",
    "created_at": "2023-10-01T13:00:00.000000Z",
    "updated_at": "2023-10-01T13:10:00.000000Z"
}
Структура данных:
id (integer): Уникальный идентификатор заказа.
order_number (string): Уникальный номер заказа.
status (string): Статус заказа (approved — одобрен).
order_date (datetime): Дата создания заказа.
customer_id (integer): Идентификатор покупателя.
total_price (decimal): Общая стоимость заказа.
created_at (datetime): Дата создания записи.
updated_at (datetime): Дата последнего обновления записи.

400 Bad Request:
{
    "error": "Insufficient balance"
}
