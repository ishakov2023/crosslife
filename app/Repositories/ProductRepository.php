<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository
{
    /**
     * @return Collection
     */
    public function getAll()
    {
        // Используется метод all() для получения всех записей из таблицы products.
        return Product::all();
    }

    /**
     * @param $products
     * @return void
     * @throws \Exception
     */
    public function reserveProducts($products)
    {
        // Проходим по каждому товару в массиве $products
        foreach ($products as $product) {
            // Находим товар по его идентификатору. Если товар не найден, будет выброшено исключение.
            $prod = Product::findOrFail($product['id']);

            // Проверяем, достаточно ли товара на складе для резервирования
            if ($prod->stock < $product['quantity']) {
                // Если товара недостаточно, выбрасываем исключение с сообщением об ошибке
                throw new \Exception("Not enough stock for product: " . $prod->name);
            }

            // Уменьшаем количество товара на складе на указанное количество
            $prod->stock -= $product['quantity'];

            // Сохраняем изменения в базе данных
            $prod->save();
        }
    }
}