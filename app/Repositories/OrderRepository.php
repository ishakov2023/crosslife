<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository
{
    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        // Здесь создается новый заказ с указанными параметрами
        $order = Order::create([
            'order_number' => $data['order_number'],
            'status' => $data['status'],
            'order_date' => $data['order_date'],
            'customer_id' => $data['customer_id'],
            'total_price' => $data['total_price'],
        ]);

        // Создание деталей заказа (товары и их количество)
        foreach ($data['items'] as $item) {
            $order->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        return $order;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        // Поиск заказа по его идентификатору:
        // Если заказ не найден, будет выброшено исключение `ModelNotFoundException`,
        // которое автоматически преобразуется в HTTP-ответ 404 Not Found.
        return Order::findOrFail($id);
    }
}