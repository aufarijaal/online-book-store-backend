<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('carts')->group(function () {
  Route::get('/', [\App\Http\Controllers\API\v1\CartController::class, 'index'])->name('carts.index');

  Route::get('/count', [\App\Http\Controllers\API\v1\CartController::class, 'count'])->name('carts.count');

  Route::post('/', [\App\Http\Controllers\API\v1\CartController::class, 'store'])->name('carts.store');

  Route::delete('/{id}', [\App\Http\Controllers\API\v1\CartController::class, 'destroy'])->name('carts.destroy');
});
