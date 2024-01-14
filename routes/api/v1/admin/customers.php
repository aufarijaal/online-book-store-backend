<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('customers')->group(function () {
  Route::get('/', [\App\Http\Controllers\API\v1\Admin\CustomerController::class, 'index'])->name('customers.index');
  Route::get('/{id}/edit', [\App\Http\Controllers\API\v1\Admin\CustomerController::class, 'edit'])->name('customers.edit');
  Route::post('/', [\App\Http\Controllers\API\v1\Admin\CustomerController::class, 'store'])->name('customers.store');
  Route::match(['put', 'patch'], '/{id}', [\App\Http\Controllers\API\v1\Admin\CustomerController::class, 'update'])->name('customers.update');
  Route::match(['put', 'patch'], '/{id}/change-password', [\App\Http\Controllers\API\v1\Admin\CustomerController::class, 'changePassword'])->name('customers.change_password');
  Route::delete('/', [\App\Http\Controllers\API\v1\Admin\CustomerController::class, 'destroy'])->name('customers.destroy');
});
