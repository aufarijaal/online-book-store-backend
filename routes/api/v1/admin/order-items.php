<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('order-items')->group(function () {
  Route::get('/', [\App\Http\Controllers\API\v1\OrderItemController::class, 'index'])->name('order_items.index');
  Route::get('/{id}', [\App\Http\Controllers\API\v1\OrderItemController::class, 'show'])->name('order_items.show');
  Route::post('/', [\App\Http\Controllers\API\v1\OrderItemController::class, 'store'])->name('order_items.store');
  Route::match(['put', 'patch'], '/{id}', [\App\Http\Controllers\API\v1\OrderItemController::class, 'update'])->name('order_items.update');
  Route::delete('/{id}', [\App\Http\Controllers\API\v1\OrderItemController::class, 'destroy'])->name('order_items.destroy');
});
