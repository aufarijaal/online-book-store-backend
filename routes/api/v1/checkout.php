<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('checkout')->group(function () {
  Route::middleware(['auth:sanctum'])->get('/', [\App\Http\Controllers\API\v1\PaymentController::class, 'checkout'])->name('checkout.index');
  Route::middleware(['auth:sanctum'])->get('/reorder', [\App\Http\Controllers\API\v1\PaymentController::class, 'reorder'])->name('checkout.reorder');
  Route::post('/midtrans-callback', [\App\Http\Controllers\API\v1\PaymentController::class, 'callback'])->name('checkout.callback');
});
