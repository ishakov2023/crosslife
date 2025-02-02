<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    protected $orderService;
    protected $productService;

    /**
     * @param OrderService $orderService
     * @param ProductService $productService
     */
    public function __construct(OrderService $orderService, ProductService $productService)
    {
        $this->orderService = $orderService;
        $this->productService = $productService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'products' => 'required|array',
        ]);

        try {
            // Резервирование товаров:
            // Этот блок вызывает метод `reserveProducts` из сервиса `productService`.
            // Он проверяет наличие достаточного количества товара на складе для каждого продукта
            // и уменьшает количество на складе, если всё в порядке.
            // Если товара недостаточно, будет выброшено исключение.
            $this->productService->reserveProducts($data['products']);

            // Создание заказа:
            // Этот блок вызывает метод `createOrder` из сервиса `orderService`.
            // Метод создает новый заказ, генерирует уникальный номер заказа,
            // рассчитывает общую стоимость заказа и сохраняет его в базу данных.
            $order = $this->orderService->createOrder($data['customer_id'], $data['products']);

            return response()->json($order, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function approve(Request $request): JsonResponse
    {
        $data = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'customer_id' => 'required|exists:customers,id',
        ]);

        try {
            // Одобрение заказа:
            // Этот блок вызывает метод `approveOrder` из сервиса `orderService`.
            // Метод проверяет, достаточно ли средств на балансе покупателя,
            // списывает сумму заказа с баланса, меняет статус заказа на "одобрен"
            // и сохраняет изменения в базе данных.
            $order = $this->orderService->approveOrder($data['order_id'], $data['customer_id']);

            return response()->json($order, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
