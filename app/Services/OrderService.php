<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Product;
use App\Repositories\OrderRepository;

class OrderService
{
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     *  Создает новый заказ.
     *
     * @param $customerId
     * @param $products
     * @return mixed
     */
    public function createOrder($customerId, $products)
    {
        $totalPrice = 0; // Инициализируем общую стоимость заказа
        $items = []; // Массив для хранения деталей заказа (товары и их количество)

        // Проходим по каждому товару из массива $products
        foreach ($products as $productData) {
            // Находим товар по его идентификатору. Если товар не найден, будет выброшено исключение.
            $product = Product::findOrFail($productData['id']);

            // Рассчитываем общую стоимость заказа, умножая цену товара на его количество
            $totalPrice += $product->price * $productData['quantity'];

            // Добавляем детали товара в массив $items
            $items[] = [
                'product_id' => $product->id, // Идентификатор товара
                'quantity' => $productData['quantity'], // Количество товара
                'price' => $product->price, // Цена товара на момент создания заказа
            ];
        }

        // Создаем заказ через репозиторий
        $order = $this->orderRepository->create([
            'order_number' => uniqid('order_'), // Генерируем уникальный номер заказа
            'status' => 'pending', // Устанавливаем статус заказа как "в ожидании"
            'order_date' => now(), // Устанавливаем текущую дату как дату создания заказа
            'customer_id' => $customerId, // Указываем идентификатор покупателя
            'total_price' => $totalPrice, // Указываем общую стоимость заказа
            'items' => $items, // Передаем детали заказа
        ]);

        // Возвращаем созданный заказ
        return $order;
    }

    /**
     *  Одобряет существующий заказ и списывает сумму с баланса покупателя.
     *
     * @param $orderId
     * @param $customerId
     * @return mixed
     * @throws \Exception
     */
    public function approveOrder($orderId, $customerId)
    {
        // Находим заказ по его идентификатору. Если заказ не найден, будет выброшено исключение.
        $order = $this->orderRepository->find($orderId);

        // Находим покупателя по его идентификатору. Если покупатель не найден, будет выброшено исключение.
        $customer = Customer::findOrFail($customerId);

        // Проверяем, достаточно ли средств на балансе покупателя для оплаты заказа
        if ($customer->balance < $order->total_price) {
            // Если средств недостаточно, выбрасываем исключение
            throw new \Exception('Insufficient balance');
        }

        // Списание суммы заказа с баланса покупателя
        $customer->balance -= $order->total_price;
        $customer->save(); // Сохраняем изменения баланса покупателя

        // Обновляем статус заказа на "одобрен"
        $order->status = 'approved';
        $order->save(); // Сохраняем изменения статуса заказа

        // Возвращаем обновленный заказ

        return $order;
    }

}