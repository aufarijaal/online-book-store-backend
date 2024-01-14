<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('orders')->group(function () {
  Route::get('/', [\App\Http\Controllers\API\v1\OrderController::class, 'index'])->name('orders.index');
  Route::post('/', [\App\Http\Controllers\API\v1\OrderController::class, 'store'])->name('orders.store');

  Route::post('/pay', [\App\Http\Controllers\API\v1\PaymentController::class, 'pay'])->name('orders.pay');
});
