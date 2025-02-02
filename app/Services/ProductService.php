<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Возвращает список всех товаров.
     *
     * @return Collection Коллекция всех товаров из базы данных
     */
    public function getAllProducts()
    {
        return $this->productRepository->getAll();
    }

    /**
     * Резервирует товары, уменьшая их количество на складе.
     *
     * @param array $products Массив товаров с указанием их идентификаторов и количества
     * @throws \Exception Если недостаточно товара на складе
     */
    public function reserveProducts($products)
    {
        // Вызываем метод reserveProducts() репозитория для резервирования товаров
        // Этот метод проверяет наличие достаточного количества товара и обновляет его на складе
        $this->productRepository->reserveProducts($products);
    }
}