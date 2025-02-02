<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/catalog', [ProductController::class, 'index'])->name('catalog'); // Список товаров
Route::post('/create-order', [OrderController::class, 'create'])->name('create.order'); // Создание заказа
Route::post('/approve-order', [OrderController::class, 'approve'])->name('approve.order'); // Одобрение заказа
